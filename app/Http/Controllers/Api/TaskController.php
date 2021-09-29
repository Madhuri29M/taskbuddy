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
                'title'         => 'required|string',
                'description'   => 'nullable|string|max:500',
                'attachments.*' => 'mimes:png,jpg,jpeg,pdf,doc,docx|max:10000',
                'date'          => 'required|date|date_format:Y-m-d',
                'time'          => 'required|date_format:H:i:s',
                'assign_to'     => 'required|exists:users,id',
            ]);
            if($validator->fails()){
                return $this->sendValidationError('', $validator->errors()->first());       
            }
            \Log::info("attachments:");
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
            $task->created_at = date('Y-m-m H:i:00');
            if(Auth::guard('api')->user()->id == $request->assign_to)
            {
                $task->status = 'accepted';
            }
            if($task->save()){

                //upload attachments
                // echo "<pre>";print_r($attachments);exit;
                if($request->attachments)
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
                            if($attachment_type = "application/octet-stream")
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
                if($request->assigned_to != Auth::guard('api')->user()->id)
                {
                    //notify buddy for new task
                    $user     = User::where(['id' => $request->assign_to])->first();
                    $title    = trans('notify.task_request_title');
                    $body     = trans('notify.task_request_body',['buddy_name' => Auth::guard('api')->user()->first_name]);
                    $slug     = 'task_request';
                    $buddy_id = Auth::guard('api')->user()->id;

                    $this->sendNotification($user,$title,$body,$slug,$buddy_id,$task->id);
                }
                
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
                                    ->orderBy('due_time','asc')
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

                if($task->assigned_to != $task->assigned_by)
                {
                    // Move to trash Notification (From Buddy To Task Owner)
                    $user     = User::where(['id' => $task->assigned_by])->first();
                    $title    = trans('notify.task_trashed_title');
                    $body     = trans('notify.task_trashed_body',['user'=> $task->assignedBy->first_name,'buddy_name' => $task->assignedTo->first_name,'task' => $task->title]);
                    $slug     = 'task_trashed';
                    $buddy_id = $task->assigned_to;

                    $this->sendNotification($user,$title,$body,$slug,$buddy_id,$task->id);
                }

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

                if($task->assigned_to != $task->assigned_by)
                {
                    // Task Completed Notification (From Buddy To Task Owner)
                    $user     = User::where(['id' => $task->assigned_by])->first();
                    $title    = trans('notify.task_completed_title');
                    $body     = trans('notify.task_completed_body',['user'=> $task->assignedBy->first_name,'buddy_name' => $task->assignedTo->first_name,'task' => $task->title]);
                    $slug     = 'task_completed';
                    $buddy_id = $task->assigned_to;

                    $this->sendNotification($user,$title,$body,$slug,$buddy_id,$task->id);
                }

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

                if($task->assigned_to != $task->assigned_by)
                    // Task Reschedule Notification (From Buddy To Task Owner)
                    $user     = User::where(['id' => $task->assigned_by])->first();
                    $title    = trans('notify.task_rescheduled_title');
                    $body     = trans('notify.task_rescheduled_body',['user'=> $task->assignedBy->first_name,'buddy_name' => $task->assignedTo->first_name,'task' => $task->title]);
                    $slug     = 'task_rescheduled';
                    $buddy_id = $task->assigned_to;

                    $this->sendNotification($user,$title,$body,$slug,$buddy_id,$task->id);
                }

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
              ->where('assigned_to',Auth::guard('api')->user()->id)
              ->orderBy('due_date','asc')
              ->get()->pluck('due_date')->toArray();

            $response['task_dates'] = array_values(array_unique($task_dates)); 
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
                $assigned_to_me = $assigned_to_me->where(function($q) use($search){
                                    $q->where('title','like','%'.$search.'%')
                                    ->orWhere('description','like','%'.$search.'%');
                                });
                                    
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
                    $assigned_to_me = $assigned_to_me->whereDate('due_date','>=', $from_date);
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
            $assigned_to_me = $assigned_to_me->orderBy('due_date','desc')->paginate();
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
            $search          = @$request->search;
            $status          = @$request->status;
            $from_date       = @$request->from_date;
            $to_date         = @$request->to_date;
            $attachment_type = @$request->attachment_type;

            $assigned_to_buddy = Task::where('assigned_by',Auth::guard('api')->user()->id)
                                ->where('assigned_to','!=',Auth::guard('api')->user()->id);

            if($search && $search != '') {
                $assigned_to_buddy = $assigned_to_buddy->where(function($q) use($search){
                                    $q->where('title','like','%'.$search.'%')
                                    ->orWhere('description','like','%'.$search.'%');
                                });
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
                    $assigned_to_buddy = $assigned_to_buddy->whereDate('due_date','>=', $from_date);
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

            $assigned_to_buddy = $assigned_to_buddy->orderBy('due_date','desc')->paginate();
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
                $assigned_to_me = $assigned_to_me->where(function($q) use($search){
                                    $q->where('title','like','%'.$search.'%')
                                    ->orWhere('description','like','%'.$search.'%');
                                });
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
                    $assigned_to_me = $assigned_to_me->whereDate('due_date','>=', $from_date);
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
            $assigned_to_me = $assigned_to_me->orderBy('due_date','desc')->paginate();
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
                $assigned_to_buddy = $assigned_to_buddy->where(function($q) use($search){
                                    $q->where('title','like','%'.$search.'%')
                                    ->orWhere('description','like','%'.$search.'%');
                                });
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
                    $assigned_to_buddy = $assigned_to_buddy->whereDate('due_date','>=', $from_date);
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

            $assigned_to_buddy = $assigned_to_buddy->orderBy('due_date','desc')->paginate();
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
                $pending_requests = $pending_requests->where(function($q) use($search){
                                    $q->where('title','like','%'.$search.'%')
                                    ->orWhere('description','like','%'.$search.'%');
                                });
            }

            if($from_date && $to_date)
            {
                $pending_requests = $pending_requests->whereBetween('due_date', [$from_date, $to_date]);
            }
            else
            {
                if($from_date)
                {
                    $pending_requests = $pending_requests->whereDate('due_date','>=', $from_date);
                }
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

            $pending_requests = $pending_requests->orderBy('due_date','desc')->paginate();
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
                $history = $history->where(function($q) use($search){
                                    $q->where('title','like','%'.$search.'%')
                                    ->orWhere('description','like','%'.$search.'%');
                                });
            }

            if($from_date && $to_date)
            {
                $history = $history->whereBetween('due_date', [$from_date, $to_date]);
            }
            else
            {
                if($from_date)
                {
                    $history = $history->whereDate('due_date','>=', $from_date);
                }
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

            $history = $history->orderBy('due_date','desc')->paginate();
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

            //check if task is already accepted
            if($task->status == 'accepted')
            {
                return $this->sendResponse(new TaskResource($task), trans('task.task_accepted_already'));
            }

            $task->status = 'accepted';

            if($task->save())
            {   
                $status = 'Accepted';
                $this->update_task_history($task->id,$status,Auth::guard('api')->user()->id);

                if($task->assigned_to != $task->assigned_by)
                {
                    //Task Accepted Notification (From Buddy To Task Owner)
                    $user     = User::where(['id' => $task->assigned_by])->first();
                    $title    = trans('notify.task_accepted_title');
                    $body     = trans('notify.task_accepted_body',['user'=> $task->assignedBy->first_name,'buddy_name' => $task->assignedTo->first_name,'task' => $task->title]);
                    $slug     = 'task_accepted';
                    $buddy_id = $task->assigned_to;

                    $this->sendNotification($user,$title,$body,$slug,$buddy_id,$task->id);
                }

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

            //check if task is already rejected
            if($task->status == 'rejected')
            {
                return $this->sendResponse(new TaskResource($task), trans('task.task_rejected_already'));
            }

            $task->status = 'rejected';

            if($task->save())
            {   
                $status = 'Rejected';
                $this->update_task_history($task->id,$status,Auth::guard('api')->user()->id);

                if($task->assigned_to != $task->assigned_by)
                {
                    // Task Rejected Notification (From Buddy To Task Owner)
                    $user     = User::where(['id' => $task->assigned_by])->first();
                    $title    = trans('notify.task_rejected_title');
                    $body     = trans('notify.task_rejected_body',['user'=> $task->assignedBy->first_name,'buddy_name' => $task->assignedTo->first_name,'task' => $task->title]);
                    $slug     = 'task_rejected';
                    $buddy_id = $task->assigned_to;

                    $this->sendNotification($user,$title,$body,$slug,$buddy_id,$task->id);
                }

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

    /**
     * Task Details.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function task_details(Request $request) {
        try{
            //VALIDATION ..
            $validator=  Validator::make($request->all(),[
                'task_id'         => 'required|numeric|exists:tasks,id',
            ]);

            if($validator->fails()) {
                return $this->sendValidationError('', $validator->errors()->first());
            }

            $task = Task::find($request->task_id);

            if($task)
            {
                return $this->sendResponse(new TaskResource($task), trans('task.task_details'));
            }

        }catch(\Exception $e){
            return $this->sendError('',$e->getMessage());           
        }
    }

    /**
     *  Delayed Task by me.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function delayed_task_by_me(Request $request) {
        try{
            
            $search          = @$request->search;
            $status          = @$request->status;
            $from_date       = @$request->from_date;
            $to_date         = @$request->to_date;
            $attachment_type = @$request->attachment_type;
            $buddy_ids       = @$request->buddy_ids;

            $delayed_by_me = Task::where('assigned_to',Auth::guard('api')->user()->id)
                                ->where(DB::raw("CONCAT(due_date,' ',due_time)"),'<=',date('Y-m-d H:i:s'))
                                ->where('status','accepted');

            if($search && $search != '') {
                $delayed_by_me = $delayed_by_me->where(function($q) use($search){
                                    $q->where('title','like','%'.$search.'%')
                                    ->orWhere('description','like','%'.$search.'%');
                                });
                                    
            }

            if($status && $status != '') {
                $status = explode(',', $status);
                $delayed_by_me = $delayed_by_me->whereIn('status',$status);
            }

            if($from_date && $to_date)
            {
                $delayed_by_me = $delayed_by_me->whereBetween('due_date', [$from_date, $to_date]);
            }
            else
            {
                if($from_date)
                {
                    $delayed_by_me = $delayed_by_me->whereDate('due_date','>=', $from_date);
                }
            }

            if($attachment_type && $attachment_type != '')
            {
                $attachment_type = explode(',', $attachment_type);
                $delayed_by_me = $delayed_by_me->whereHas('attachments',function($que) use($attachment_type){
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
                $delayed_by_me = $delayed_by_me->whereIn('assigned_to',$buddy_ids);
            }

            $delayed_by_me = $delayed_by_me->orderBy('due_date','desc')->paginate();
            if(count($delayed_by_me))
            {
                return $this->sendPaginateResponse(TaskResource::collection($delayed_by_me), trans('task.task_delayed_by_me'));
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
     *  Delayed Task by buddy.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function delayed_task_by_buddy(Request $request) {
        try{
            
            $search          = @$request->search;
            $status          = @$request->status;
            $from_date       = @$request->from_date;
            $to_date         = @$request->to_date;
            $attachment_type = @$request->attachment_type;
            $buddy_ids       = @$request->buddy_ids;

            $delayed_by_buddy = Task::where('assigned_by',Auth::guard('api')->user()->id)
                                ->where('assigned_to','!=',Auth::guard('api')->user()->id)
                                ->where(DB::raw("CONCAT(due_date,' ',due_time)"),'<=',date('Y-m-d H:i:s'))
                                ->where('status','accepted');

            if($search && $search != '') {
                $delayed_by_buddy = $delayed_by_buddy->where(function($q) use($search){
                                    $q->where('title','like','%'.$search.'%')
                                    ->orWhere('description','like','%'.$search.'%');
                                });
                                    
            }

            if($status && $status != '') {
                $status = explode(',', $status);
                $delayed_by_buddy = $delayed_by_buddy->whereIn('status',$status);
            }

            if($from_date && $to_date)
            {
                $delayed_by_buddy = $delayed_by_buddy->whereBetween('due_date', [$from_date, $to_date]);
            }
            else
            {
                if($from_date)
                {
                    $delayed_by_buddy = $delayed_by_buddy->whereDate('due_date','>=', $from_date);
                }
            }

            if($attachment_type && $attachment_type != '')
            {
                $attachment_type = explode(',', $attachment_type);
                $delayed_by_buddy = $delayed_by_buddy->whereHas('attachments',function($que) use($attachment_type){
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
                $delayed_by_buddy = $delayed_by_buddy->whereIn('assigned_to',$buddy_ids);
            }

            $delayed_by_buddy = $delayed_by_buddy->orderBy('due_date','desc')->paginate();
            if(count($delayed_by_buddy))
            {
                return $this->sendPaginateResponse(TaskResource::collection($delayed_by_buddy), trans('task.task_delayed_by_buddy'));
            }
            else
            {
                return $this->sendResponse([],trans('task.tasks_not_found'));
            }
        }catch(\Exception $e){
            return $this->sendError('',$e->getMessage());           
        }
    }

    public function notify_buddy_for_delayed_task(Request $request) {
        try{
            $validator=  Validator::make($request->all(),[
                'task_id'         => 'required|numeric|exists:tasks,id',
            ]);
            if($validator->fails()) {
                return $this->sendValidationError('', $validator->errors()->first());
            }

            $task = Task::find($request->task_id);
            if($task->assigned_to != $task->assigned_by)
            {
                // Task Delayed Notification (From Task Owner To Buddy)
                $user     = User::where(['id' => $task->assigned_to])->first();
                $title    = trans('notify.task_delayed_by_buddy_title');
                $body     = trans('notify.task_delayed_by_buddy_body',['user'=> $task->assignedTo->first_name,'buddy_name' => $task->assignedby->first_name,'task' => $task->title]);
                $slug     = 'task_delayed_by_buddy';
                $buddy_id = $task->assigned_by;

                $this->sendNotification($user,$title,$body,$slug,$buddy_id,$task->id);
            }

            return $this->sendResponse(new TaskResource($task), trans('task.notification_sent'));

        }catch(\Exception $e){
            return $this->sendError('',$e->getMessage());           
        }
    }
    
}
