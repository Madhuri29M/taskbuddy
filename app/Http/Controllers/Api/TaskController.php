<?php


namespace App\Http\Controllers\Api;
use App\Http\Controllers\Api\BaseController;
use App\Models\Buddy;
use App\Models\User;
use App\Models\Task;
use App\Models\TaskHistory;
use App\Models\TaskAttachemnt;
use Illuminate\Http\Request;
use DB,Validator,Auth;
use App\Http\Resources\UserResource;
use App\Http\Resources\BuddyResource;
use App\Http\Resources\TaskResource;
use App\Models\Helpers\CommonHelper;

class TaskController extends BaseController
{
    use CommonHelper;

    /**
     * Create Task.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function create_task(Request $request) {
        DB::beginTransaction();
        try{
            $validator = Validator::make($request->all(), [
                'title'         => 'required|string|min:3|max:50',
                'description'   => 'required|string|max:500',
                'attachments.*' => 'mimes:png,jpg,jpeg,svg,pdf,doc,docx|max:10000',
                'date'          => 'required|date|date_format:Y-m-d',
                'time'          => 'required|date_format:H:i:s',
                'assign_to'     => 'required|exists:users,id',
            ]);
            if($validator->fails()){
                return $this->sendValidationError('', $validator->errors()->first());       
            }
            \Log::info($request->attachments);
            //check if date is not past
            if(strtotime(date('Y-m-d H:i:s')) > strtotime($request->date.' '.$request->time)) 
            {
                return $this->sendError('',trans('task.its_old_date'));
            }

            $task = new Task;
            $task->assigned_to = $request->assign_to;
            $task->assigned_by = Auth::guard('api')->user()->id;
            $task->title = $request->title;
            $task->description = $request->description;
            $task->due_date = $request->date;
            $task->due_time = $request->time;
            if($task->save()){

                //upload attachments
                // echo "<pre>";print_r($attachments);exit;
                if($request->hasFile('attachments'))
                {
                    // echo "<pre>";print_r("this");exit;
                    $attachments = $request->attachments;
                    if(count($attachments))
                    {
                        foreach($attachments as $attachment){
                            $attachment_type = $attachment->getClientMimeType();
                            $attachment_type_arr = explode("/", $attachment_type, 2);
                            if($attachment_type_arr[0] == 'image')
                            {
                                $attachment_type = 'image';
                            }
                            $task_attachment = new TaskAttachemnt();
                            $task_attachment->task_id = $task->id;
                            $task_attachment->attachment = $this->saveMedia($attachment);
                            $task_attachment->attachment_type = $attachment_type;
                            $task_attachment->save();
                        }  
                    }
                }
                

                //update task history
                $status = 'Created';
                $this->update_task_history($task->id,$status,Auth::guard('api')->user()->id);
                //notify buddy for new task
                $user     = User::where(['id' => $request->assign_to])->first();
                $title    = trans('notify.task_request_title');
                $body     = trans('notify.task_request_body',['buddy_name' => Auth::guard('api')->user()->first_name]);
                $slug     = 'task_request';
                $buddy_id = Auth::guard('api')->user()->id;

                $this->sendNotification($user,$title,$body,$slug,$buddy_id);
                DB::commit();
                return $this->sendResponse('', trans('task.task_created'));
            }else{
                DB::rollback();
                return $this->sendError('',trans('task.task_created_error'));
            }

        }catch(\Exception $e){
            DB::rollback();
            return $this->sendError('',$e->getMessage());           
        }
    }

    /**
     * Create Task.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function todays_tasks(Request $request) {
        try{
            $todays_tasks = Task::where('assigned_to',Auth::guard('api')->user()->id)
                                    ->where('due_date',date('Y-m-d'))
                                    ->where('status','accepted')
                                    ->paginate();


            if(count($todays_tasks))
            {
                return $this->sendPaginateResponse(TaskResource::collection($todays_tasks), trans('task.todays_tasks'));
            }
            else
            {
                return $this->sendResponse([],trans('task.tasks_not_found'));
            }
        }catch(\Exception $e){
            return $this->sendError('',$e->getMessage());           
        }
    }

    /**
     * Move to Trash.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function move_to_trash(Request $request) {
        try{
            //VALIDATION ..
            $validator=  Validator::make($request->all(),[
                'task_id'         => 'required|numeric|exists:tasks,id',
            ]);

            if($validator->fails()) {
                return $this->sendValidationError('', $validator->errors()->first());
            }

            $task = Task::find($request->task_id);
            $task->status = 'trashed';

            if($task->save())
            {   
                $status = 'Trashed';
                $this->update_task_history($task->id,$status,Auth::guard('api')->user()->id);
                return $this->sendResponse(new TaskResource($task), trans('task.task_moved_to_trash'));
            }
            else
            {
                return $this->sendResponse([],trans('task.tasks_not_found'));
            }
        }catch(\Exception $e){
            return $this->sendError('',$e->getMessage());           
        }
    }

    /**
     * Mark as done.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function mark_as_done(Request $request) {
        try{
            //VALIDATION ..
            $validator=  Validator::make($request->all(),[
                'task_id'         => 'required|numeric|exists:tasks,id',
            ]);

            if($validator->fails()) {
                return $this->sendValidationError('', $validator->errors()->first());
            }

            $task = Task::find($request->task_id);
            $task->status = 'completed';
            $task->completed_date = date('Y-m-d');
            $task->completed_time = date('H:i:s');

            if($task->save())
            {   
                $status = 'Completed';
                $this->update_task_history($task->id,$status,Auth::guard('api')->user()->id);
                return $this->sendResponse(new TaskResource($task), trans('task.marked_as_done'));
            }
            else
            {
                return $this->sendResponse([],trans('task.marked_as_done_error'));
            }
        }catch(\Exception $e){
            return $this->sendError('',$e->getMessage());           
        }
    }
    
    /**
     * Reschedule Task.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function reschedule_task(Request $request) {
        try{
            //VALIDATION ..
            $validator=  Validator::make($request->all(),[
                'task_id' => 'required|numeric|exists:tasks,id',
                'date'    => 'required|date|date_format:Y-m-d',
                'time'    => 'required|date_format:H:i:s',
            ]);

            if($validator->fails()) {
                return $this->sendValidationError('', $validator->errors()->first());
            }

            //check if date is not past
            if(strtotime(date('Y-m-d H:i:s')) > strtotime($request->date.' '.$request->time)) 
            {
                return $this->sendError('',trans('task.its_old_date'));
            }
            $task = Task::find($request->task_id);
            $task->rescheduled_at = date('Y-m-d H:i:s');
            $task->due_date = $request->date;
            $task->due_time = $request->time;

            if($task->save())
            {   
                $status = 'Rescheduled';
                $this->update_task_history($task->id,$status,Auth::guard('api')->user()->id);
                return $this->sendResponse(new TaskResource($task), trans('task.task_rescheduled'));
            }
            else
            {
                return $this->sendResponse([],trans('task.task_reschedule_error'));
            }
        }catch(\Exception $e){
            return $this->sendError('',$e->getMessage());           
        }
    }

    /**
     * Task assigned to me by buddies.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function task_dates(Request $request) {
        try{
            $validator=  Validator::make($request->all(),[
                'year'         => 'required|numeric|digits:4',
                'month'         => 'required|numeric|digits:2',
            ]);

            if($validator->fails()) {
                return $this->sendValidationError('', $validator->errors()->first());
            }

            $task_dates = Task::whereYear('due_date', '=', $request->year)
              ->whereMonth('due_date', '=', $request->month)
              ->get()->pluck('due_date')->toArray();

            $response['task_dates'] = $task_dates; 
            if(count($task_dates))
            {
                return $this->sendResponse($response, trans('task.task_dates'));
            }
            else
            {
                return $this->sendResponse([],trans('task.task_dates_not_found'));
            }
        }catch(\Exception $e){
            return $this->sendError('',$e->getMessage());           
        }
    }
    /**
     * Task assigned to me by buddies.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function assigned_to_me(Request $request) {
        try{
            
            $search          = @$request->search;
            $status          = @$request->status;
            $from_date       = @$request->from_date;
            $to_date         = @$request->to_date;
            $attachment_type = @$request->attachment_type;

            $assigned_to_me = Task::where('assigned_to',Auth::guard('api')->user()->id);

            if($search && $search != '') {
                $assigned_to_me = $assigned_to_me->where('title','like','%'.$search.'%');
            }

            if($status && $status != '') {
                $status = explode(',', $status);
                $assigned_to_me = $assigned_to_me->whereIn('status',$status);
            }

            if($from_date && $to_date)
            {
                $assigned_to_me = $assigned_to_me->whereBetween('due_date', [$from_date, $to_date]);
            }
            else
            {
                if($from_date)
                {
                    $assigned_to_me = $assigned_to_me->whereDate('due_date', $from_date);
                }
            }

            if($attachment_type && $attachment_type != '')
            {
                $attachment_type = explode(',', $attachment_type);
                $assigned_to_me = $assigned_to_me->whereHas('attachments',function($que) use($attachment_type){
                    $type = [];
                    if(in_array('image', $attachment_type))
                    {
                        $type[] = 'image';   
                    }
                    if(in_array('pdf', $attachment_type))
                    {
                        $type[] = 'application/pdf';
                    }
                    if(in_array('document', $attachment_type))
                    {
                        $type[] = 'text/plain';
                    }
                    $que->whereIn('attachment_type',$type);
                });
            }
            $assigned_to_me = $assigned_to_me->paginate();
            if(count($assigned_to_me))
            {
                return $this->sendPaginateResponse(TaskResource::collection($assigned_to_me), trans('task.task_assigned_to_me'));
            }
            else
            {
                return $this->sendResponse([],trans('task.tasks_not_found'));
            }
        }catch(\Exception $e){
            return $this->sendError('',$e->getMessage());           
        }
    }

    /**
     * Task assigned to buddies by me.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function assigned_to_buddy(Request $request) {
        try{

            $validator=  Validator::make($request->all(),[
                'user_id'         => 'required|numeric|exists:users,id',
            ]);

            if($validator->fails()) {
                return $this->sendValidationError('', $validator->errors()->first());
            }

            $search          = @$request->search;
            $status          = @$request->status;
            $from_date       = @$request->from_date;
            $to_date         = @$request->to_date;
            $attachment_type = @$request->attachment_type;

            $assigned_to_buddy = Task::where('assigned_by',Auth::guard('api')->user()->id)
                                ->where('assigned_to','!=',Auth::guard('api')->user()->id);

            if($search && $search != '') {
                $assigned_to_buddy = $assigned_to_buddy->where('title','like','%'.$search.'%');
            }

            if($status && $status != '') {
                $status = explode(',', $status);
                $assigned_to_buddy = $assigned_to_buddy->whereIn('status',$status);
            }

            if($from_date && $to_date)
            {
                $assigned_to_buddy = $assigned_to_buddy->whereBetween('due_date', [$from_date, $to_date]);
            }
            else
            {
                if($from_date)
                {
                    $assigned_to_me = $assigned_to_me->whereDate('due_date', $from_date);
                }
            }

            if($attachment_type && $attachment_type != '')
            {
                $attachment_type = explode(',', $attachment_type);
                $assigned_to_buddy = $assigned_to_buddy->whereHas('attachments',function($que) use($attachment_type){
                    $type = [];
                    if(in_array('image', $attachment_type))
                    {
                        $type[] = 'image';   
                    }
                    if(in_array('pdf', $attachment_type))
                    {
                        $type[] = 'application/pdf';
                    }
                    if(in_array('document', $attachment_type))
                    {
                        $type[] = 'text/plain';
                    }
                    $que->whereIn('attachment_type',$type);
                });
            }

            $assigned_to_buddy = $assigned_to_buddy->paginate();
            if(count($assigned_to_buddy))
            {
                return $this->sendPaginateResponse(TaskResource::collection($assigned_to_buddy), trans('task.task_assigned_by_me'));
            }
            else
            {
                return $this->sendResponse([],trans('task.tasks_not_found'));
            }
        }catch(\Exception $e){
            return $this->sendError('',$e->getMessage());           
        }
    }

    /**
     * Task assigned to me by particular buddy.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function buddy_assigned_to_me(Request $request) {
        try{

            $validator=  Validator::make($request->all(),[
                'buddy_id'         => 'required|numeric|exists:users,id',
            ]);

            if($validator->fails()) {
                return $this->sendValidationError('', $validator->errors()->first());
            }

            $search          = @$request->search;
            $status          = @$request->status;
            $from_date       = @$request->from_date;
            $to_date         = @$request->to_date;
            $attachment_type = @$request->attachment_type;

            $assigned_to_me = Task::where('assigned_to',Auth::guard('api')->user()->id)->where('assigned_by',$request->buddy_id);

            if($search && $search != '') {
                $assigned_to_me = $assigned_to_me->where('title','like','%'.$search.'%');
            }

            if($status && $status != '') {
                $status = explode(',', $status);
                $assigned_to_me = $assigned_to_me->whereIn('status',$status);
            }

            if($from_date && $to_date)
            {
                $assigned_to_me = $assigned_to_me->whereBetween('due_date', [$from_date, $to_date]);
            }
            else
            {
                if($from_date)
                {
                    $assigned_to_me = $assigned_to_me->whereDate('due_date', $from_date);
                }
            }

            if($attachment_type && $attachment_type != '')
            {
                $attachment_type = explode(',', $attachment_type);
                $assigned_to_me = $assigned_to_me->whereHas('attachments',function($que) use($attachment_type){
                    $type = [];
                    if(in_array('image', $attachment_type))
                    {
                        $type[] = 'image';   
                    }
                    if(in_array('pdf', $attachment_type))
                    {
                        $type[] = 'application/pdf';
                    }
                    if(in_array('document', $attachment_type))
                    {
                        $type[] = 'text/plain';
                    }
                    $que->whereIn('attachment_type',$type);
                });
            }
            $assigned_to_me = $assigned_to_me->paginate();
            if(count($assigned_to_me))
            {
                return $this->sendPaginateResponse(TaskResource::collection($assigned_to_me), trans('task.task_assigned_to_me'));
            }
            else
            {
                return $this->sendResponse([],trans('task.tasks_not_found'));
            }
        }catch(\Exception $e){
            return $this->sendError('',$e->getMessage());           
        }
    }

    /**
     * Task assigned to particular buddy by me.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function me_assigned_to_buddy(Request $request) {
        try{

            $validator=  Validator::make($request->all(),[
                'buddy_id'         => 'required|numeric|exists:users,id',
            ]);

            if($validator->fails()) {
                return $this->sendValidationError('', $validator->errors()->first());
            }

            $search          = @$request->search;
            $status          = @$request->status;
            $from_date       = @$request->from_date;
            $to_date         = @$request->to_date;
            $attachment_type = @$request->attachment_type;

            $assigned_to_buddy = Task::where('assigned_by',Auth::guard('api')->user()->id)
                                ->where('assigned_to','=',$request->buddy_id);

            if($search && $search != '') {
                $assigned_to_buddy = $assigned_to_buddy->where('title','like','%'.$search.'%');
            }

            if($status && $status != '') {
                $status = explode(',', $status);
                $assigned_to_buddy = $assigned_to_buddy->whereIn('status',$status);
            }

            if($from_date && $to_date)
            {
                $assigned_to_buddy = $assigned_to_buddy->whereBetween('due_date', [$from_date, $to_date]);
            }
            else
            {
                if($from_date)
                {
                    $assigned_to_me = $assigned_to_me->whereDate('due_date', $from_date);
                }
            }

            if($attachment_type && $attachment_type != '')
            {
                $attachment_type = explode(',', $attachment_type);
                $assigned_to_buddy = $assigned_to_buddy->whereHas('attachments',function($que) use($attachment_type){
                    $type = [];
                    if(in_array('image', $attachment_type))
                    {
                        $type[] = 'image';   
                    }
                    if(in_array('pdf', $attachment_type))
                    {
                        $type[] = 'application/pdf';
                    }
                    if(in_array('document', $attachment_type))
                    {
                        $type[] = 'text/plain';
                    }
                    $que->whereIn('attachment_type',$type);
                });
            }

            $assigned_to_buddy = $assigned_to_buddy->paginate();
            if(count($assigned_to_buddy))
            {
                return $this->sendPaginateResponse(TaskResource::collection($assigned_to_buddy), trans('task.task_assigned_by_me'));
            }
            else
            {
                return $this->sendResponse([],trans('task.tasks_not_found'));
            }
        }catch(\Exception $e){
            return $this->sendError('',$e->getMessage());           
        }
    }

    /**
     * Pending Requests.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function pending_requests(Request $request) {
        try{
            $search          = @$request->search;
            $from_date       = @$request->from_date;
            $to_date         = @$request->to_date;
            $attachment_type = @$request->attachment_type;
            $buddy_ids       = @$request->buddy_ids;

            $pending_requests = Task::where('assigned_to',Auth::guard('api')->user()->id)
                                    ->where('status','pending');

            if($search && $search != '') {
                $pending_requests = $pending_requests->where('title','like','%'.$search.'%');
            }

            if($from_date && $to_date)
            {
                $pending_requests = $pending_requests->whereBetween('due_date', [$from_date, $to_date]);
            }

            if($attachment_type && $attachment_type != '')
            {
                $attachment_type = explode(',', $attachment_type);
                $pending_requests = $pending_requests->whereHas('attachments',function($que) use($attachment_type){
                    $type = [];
                    if(in_array('image', $attachment_type))
                    {
                        $type[] = 'image';   
                    }
                    if(in_array('pdf', $attachment_type))
                    {
                        $type[] = 'application/pdf';
                    }
                    if(in_array('document', $attachment_type))
                    {
                        $type[] = 'text/plain';
                    }
                    $que->whereIn('attachment_type',$type);
                });
            }

            if($buddy_ids)
            {
                $buddy_ids = explode(',', $buddy_ids);
                $pending_requests = $pending_requests->whereIn('assigned_by',$buddy_ids);
            }

            $pending_requests = $pending_requests->paginate();
            if(count($pending_requests))
            {
                return $this->sendPaginateResponse(TaskResource::collection($pending_requests), trans('task.pending_requests'));
            }
            else
            {
                return $this->sendResponse([],trans('task.tasks_not_found'));
            }
        }catch(\Exception $e){
            return $this->sendError('',$e->getMessage());           
        }
    }

    /**
     * History.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function history(Request $request) {
        try{
            $search          = @$request->search;
            $from_date       = @$request->from_date;
            $to_date         = @$request->to_date;
            $attachment_type = @$request->attachment_type;
            $buddy_ids       = @$request->buddy_ids;
            $status          = @$request->status;

            $history = Task::where('assigned_to',Auth::guard('api')->user()->id)
                                    ->where(DB::raw("CONCAT(due_date,' ',due_time)"),'<=',date('Y-m-d H:i:s'))
                                    ->whereIn('status',['completed','rejected','trashed']);

            if($search && $search != '') {
                $history = $history->where('title','like','%'.$search.'%');
            }

            if($from_date && $to_date)
            {
                $history = $history->whereBetween('due_date', [$from_date, $to_date]);
            }

            if($attachment_type && $attachment_type != '')
            {
                $attachment_type = explode(',', $attachment_type);
                $history = $history->whereHas('attachments',function($que) use($attachment_type){
                    $type = [];
                    if(in_array('image', $attachment_type))
                    {
                        $type[] = 'image';   
                    }
                    if(in_array('pdf', $attachment_type))
                    {
                        $type[] = 'application/pdf';
                    }
                    if(in_array('document', $attachment_type))
                    {
                        $type[] = 'text/plain';
                    }
                    $que->whereIn('attachment_type',$type);
                });
            }

            if($buddy_ids)
            {
                $buddy_ids = explode(',', $buddy_ids);
                $history = $history->whereIn('assigned_by',$buddy_ids);
            }

            if($status && $status != '') {
                $status = explode(',', $status);
                $history = $history->whereIn('status',$status);
            }

            $history = $history->paginate();
            if(count($history))
            {
                return $this->sendPaginateResponse(TaskResource::collection($history), trans('task.history'));
            }
            else
            {
                return $this->sendResponse([],trans('task.tasks_not_found'));
            }
        }catch(\Exception $e){
            return $this->sendError('',$e->getMessage());           
        }
    }

    /**
     * Accept Task.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function accept_task(Request $request) {
        try{
            //VALIDATION ..
            $validator=  Validator::make($request->all(),[
                'task_id'         => 'required|numeric|exists:tasks,id',
            ]);

            if($validator->fails()) {
                return $this->sendValidationError('', $validator->errors()->first());
            }

            $task = Task::find($request->task_id);
            $task->status = 'accepted';

            if($task->save())
            {   
                $status = 'Accepted';
                $this->update_task_history($task->id,$status,Auth::guard('api')->user()->id);
                return $this->sendResponse(new TaskResource($task), trans('task.task_accepted'));
            }
            else
            {
                return $this->sendResponse([],trans('task.task_update_error'));
            }
        }catch(\Exception $e){
            return $this->sendError('',$e->getMessage());           
        }
    }

    /**
     * Reject Task.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function reject_task(Request $request) {
        try{
            //VALIDATION ..
            $validator=  Validator::make($request->all(),[
                'task_id'         => 'required|numeric|exists:tasks,id',
            ]);

            if($validator->fails()) {
                return $this->sendValidationError('', $validator->errors()->first());
            }

            $task = Task::find($request->task_id);
            $task->status = 'rejected';

            if($task->save())
            {   
                $status = 'Rejected';
                $this->update_task_history($task->id,$status,Auth::guard('api')->user()->id);
                return $this->sendResponse(new TaskResource($task), trans('task.task_rejected'));
            }
            else
            {
                return $this->sendResponse([],trans('task.task_update_error'));
            }
        }catch(\Exception $e){
            return $this->sendError('',$e->getMessage());           
        }
    }
    
}
