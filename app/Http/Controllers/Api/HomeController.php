<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Models\User;
use App\Models\Setting;
use App\Models\Task;
use App\Models\DeviceDetail;
use Validator;
use Auth,DB;
use App\Models\Helpers\CommonHelper;

class HomeController extends BaseController
{
    use CommonHelper;
    /**
     * Update device Token.
     *
     * @return \Illuminate\Http\Response
     */
    public function update_device_token(Request $request) {

        $validator = Validator::make($request->all(), [
            'device_token' => 'required',
            'device_type'  => 'required',
        ]);

        if($validator->fails()) {
            return $this->sendError('', $validator->errors()->first());       
        }

        $data = [
            'device_token' => $request->device_token,
            'device_type'  => $request->device_type,
        ];
        $createArray = array();
        
        foreach ($data as $key => $value) {
            $createArray[$key] = $value;
        }
        $device_detail = DeviceDetail::where('user_id',Auth::user()->id)->first();
        if($device_detail){
            $res = $device_detail->update($createArray);
        } else {
            $createArray['user_id'] = Auth::user()->id;
            $res = DeviceDetail::create($createArray);
        }
        if($res)
        {
            return $this->sendResponse('',trans('common.token_updated'));
        }
        else
        {
            return $this->sendError('',trans('common.token_can_not_be_updated'));
        }
    }

    /**
     * Dashboard
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard(Request $request) {

        $day_wise_task_count = Task::select([
                  DB::raw('count(id) as `count`'), 
                  DB::raw('DAYNAME(due_date) as day')
                ])
                ->where('assigned_to',Auth::guard('api')->user()->id)
                ->groupBy('day')->get();

        $day_wise_buddy_task_count = Task::select([
                  DB::raw('count(id) as `count`'), 
                  DB::raw('DAYNAME(due_date) as day')
                ])
                ->where('assigned_by',Auth::guard('api')->user()->id)
                ->where('assigned_to','!=',Auth::guard('api')->user()->id)
                ->groupBy('day')->get();
        $my_days['Monday'] = 0;
        $my_days['Tuesday'] = 0;
        $my_days['Wednesday'] = 0;
        $my_days['Thursday'] = 0;
        $my_days['Friday'] = 0;
        $my_days['Saturday'] = 0;
        $my_days['Sunday'] = 0;
        $my_days_res = [];
        foreach ($day_wise_task_count as $day_count) {
            $my_days[$day_count['day']] = $day_count['count'];
        }

        foreach ($my_days as $day => $day_per) {
            
        }
        $buddy_days['Monday'] = 0;
        $buddy_days['Tuesday'] = 0;
        $buddy_days['Wednesday'] = 0;
        $buddy_days['Thursday'] = 0;
        $buddy_days['Friday'] = 0;
        $buddy_days['Saturday'] = 0;
        $buddy_days['Sunday'] = 0;
        foreach ($day_wise_buddy_task_count as $day_count) {
            $buddy_days[$day_count['day']] = $day_count['count'];
        }

        $day_wise_task_per = [];
        foreach ($my_days as $day => $day_per) {

            //my_task_week_chart
            $my_days_res_arr['day'] = $day;
            $my_days_res_arr['count'] = (string)$day_per;
            $my_days_res[] = $my_days_res_arr;

            //my_task_buddy_task_week_wise
            $day_wise_task_per_arr['day'] = $day;
            $day_wise_task_per_arr['my_task'] = (($my_days[$day] + $buddy_days[$day]) == 0) ? (string)0 : (string)(($my_days[$day] * 100) / ($my_days[$day] + $buddy_days[$day]));
            $day_wise_task_per_arr['buddy_task'] = (($my_days[$day] + $buddy_days[$day]) == 0) ? (string)0 : (string)(($buddy_days[$day] * 100) / ($my_days[$day] + $buddy_days[$day]));
            $day_wise_task_per[] = $day_wise_task_per_arr;
        }

        $monday = strtotime("last monday");
        $monday = date('w', $monday)==date('w') ? $monday+7*86400 : $monday;
        $sunday = strtotime(date("Y-m-d",$monday)." +6 days");
        $this_week_from = date("Y-m-d",$monday);
        $this_week_to = date("Y-m-d",$sunday);

        $this_week_assigned_by_buddy_count = Task::where('assigned_to',Auth::guard('api')->user()->id)
            ->whereBetween('due_date', [$this_week_from, $this_week_to])
            ->get()
            ->count();

        $this_week_assigned_to_buddy_count = Task::where('assigned_by',Auth::guard('api')->user()->id)
                ->where('assigned_to','!=',Auth::guard('api')->user()->id)
                ->whereBetween('due_date', [$this_week_from, $this_week_to])
                ->get()
                ->count();

        $assigned_by_buddy_all_task_count = Task::where('assigned_to',Auth::guard('api')->user()->id)
            ->get()
            ->count();

        $assigned_to_buddy_all_task_count = Task::where('assigned_by',Auth::guard('api')->user()->id)
                ->where('assigned_to','!=',Auth::guard('api')->user()->id)
                ->get()
                ->count();

        $delayed_task_by_me = Task::where('assigned_to',Auth::guard('api')->user()->id)
            ->where(DB::raw("CONCAT(due_date,' ',due_time)"),'<=',date('Y-m-d H:i:s'))
            ->where('status','accepted')
            ->get()
            ->count();
        $delayed_task_by_buddy = Task::where('assigned_by',Auth::guard('api')->user()->id)
            ->where('assigned_to','!=',Auth::guard('api')->user()->id)
            ->where(DB::raw("CONCAT(due_date,' ',due_time)"),'<=',date('Y-m-d H:i:s'))
            ->where('status','accepted')
            ->get()
            ->count();

        $completed_task = Task::where('assigned_to',Auth::guard('api')->user()->id)->where('status','completed')->get()->count();
        $pending_task = Task::where('assigned_to',Auth::guard('api')->user()->id)->where('status','pending')->get()->count();
        $trashed_task = Task::where('assigned_to',Auth::guard('api')->user()->id)->where('status','trashed')->get()->count();

        $total_task_counts = $completed_task + $pending_task + $trashed_task;

        $completed_task_per = $total_task_counts == 0 ? 0 : $completed_task * 100 / $total_task_counts;
        $pending_task_per   = $total_task_counts == 0 ? 0 : $pending_task * 100 / $total_task_counts;
        $trashed_task_per   = $total_task_counts == 0 ? 0 : $trashed_task * 100 / $total_task_counts;

        $pending_task_request_count = Task::where('assigned_to',Auth::guard('api')->user()->id)->where('status','pending')->get()->count();
        $dashboard_response = [];
        $dashboard_response['my_task_week_chart'] = $my_days_res;
        $dashboard_response['my_task_buddy_task_week_wise'] = $day_wise_task_per;
        $dashboard_response['completed_task_per'] = (string)$completed_task_per;
        $dashboard_response['pending_task_per'] = (string)$pending_task_per;
        $dashboard_response['trashed_task_per'] = (string)$trashed_task_per;
        $dashboard_response['this_week_assigned_by_buddy_count'] = (string)$this_week_assigned_by_buddy_count;
        $dashboard_response['this_week_assigned_to_buddy_count'] = (string)$this_week_assigned_to_buddy_count;
        $dashboard_response['assigned_by_buddy_all_task_count'] = (string)$assigned_by_buddy_all_task_count;
        $dashboard_response['assigned_to_buddy_all_task_count'] = (string)$assigned_to_buddy_all_task_count;
        $dashboard_response['delayed_task_by_me'] = (string)$delayed_task_by_me;
        $dashboard_response['delayed_task_by_buddy'] = (string)$delayed_task_by_buddy;
        $dashboard_response['pending_task_request_count'] = (string)$pending_task_request_count;
        
        return $this->sendResponse($dashboard_response,trans('common.dashboard_data'));
        
    }

    public function task_notifications(){
        \Log::info('Sending Task reminder notifications');

        // Task Due Notification (10 Minutes Before)
        $now = time();
        $ten_minutes = $now + (10 * 60);
        $tenMinsDue = date('H:i:00', $ten_minutes);
        $dueTasks = Task::where('due_date',date('Y-m-d'))->where('due_time',$tenMinsDue)->whereIn('status',['accepted','pending'])->get();
        foreach ($dueTasks as $task) {
            $user     = User::where(['id' => $task->assigned_to])->first();
            $title    = trans('notify.task_reminder_title');
            $body     = trans('notify.task_reminder_body',['task' => $task->title]);
            $slug     = 'task_reminder';
            $buddy_id = $task->assigned_by;

            $this->sendNotification($user,$title,$body,$slug,$buddy_id,$task->id);
            \Log::info($body);
        }

        // Task Didn't Accept Notification (From Buddy To Task Owner - After 2 Hours, 6 Hours, 24 Hours, Assigning to Buddy)

        $two_hrs = date("Y-m-d H:i:00", strtotime('-2 hours'));
        $six_hrs = date("Y-m-d H:i:00", strtotime('-6 hours'));
        $twenty_four_hrs = date("Y-m-d H:i:00", strtotime('-24 hours'));
        $taskNotAccept2Hrs = Task::where(function($q) use($two_hrs,$six_hrs,$twenty_four_hrs){
            $q->where('created_at',$two_hrs)
            ->orWhere('created_at',$six_hrs)
            ->orWhere('created_at',$twenty_four_hrs);
        })
        ->where('status','pending')->get();

        foreach ($taskNotAccept2Hrs as $task) {
            $user     = User::where(['id' => $task->assigned_by])->first();
            $title    = trans('notify.task_not_accepted_title');
            $body     = trans('notify.task_not_accepted_body',['user'=>$user->first_name,'buddy_name'=>$task->assignedTo->first_name, 'task' => $task->title]);
            $slug     = 'task_not_accepted';
            $buddy_id = $task->assigned_to;
            $this->sendNotification($user,$title,$body,$slug,$buddy_id,$task->id);
            \Log::info($body);
        }

        // Task Didn't Complete Notification (From Buddy To Task Owner - After 4 hours of task time) 

        $four_hrs_delay = date("Y-m-d H:i:00", strtotime('-4 hours'));
        $taskNotCompleted = Task::where('due_date',date('Y-m-d'))->where('due_time',$four_hrs_delay)->where('status','accepted')->whereNull('completed_date')->get();

        foreach ($taskNotCompleted as $task) {
            $user     = User::where(['id' => $task->assigned_by])->first();
            $title    = trans('notify.task_not_completed_title');
            $body     = trans('notify.task_not_completed_body',['user'=>$user->first_name,'buddy_name'=>$task->assignedTo->first_name, 'task' => $task->title]);
            $slug     = 'task_not_completed';
            $buddy_id = $task->assigned_to;
            $this->sendNotification($user,$title,$body,$slug,$buddy_id,$task->id);
            \Log::info($body);
        }

        //send notification for delay task list only if It's 8 PM
        if(date('H:00') == '20:00')
        {
            // All Delayed Tasks
            $delayedTasks = Task::where(DB::raw("CONCAT(due_date,' ',due_time)"),'<=',date('Y-m-d H:i:s'))->where('status','accepted')->get();

            $delayByBuddiesSentIds = [];
            $delayByMeSentIds = [];
            foreach ($delayedTasks as $task) {

                if(!in_array($task->assigned_by, $delayByBuddiesSentIds))
                {
                    // Task Delayed by Buddies Notification (When tasks are accepted but not completed within the same day, System will send the notification around 08:00 PM)
                    $user     = User::where(['id' => $task->assigned_by])->first();
                    $title    = trans('notify.task_delayed_by_buddies_title');
                    $body     = trans('notify.task_delayed_by_buddies_body',['user'=>$user->first_name]);
                    $slug     = 'task_delayed_by_buddies';
                    $buddy_id = $task->assigned_to;
                    $this->sendNotification($user,$title,$body,$slug,$buddy_id,$task->id);
                    \Log::info($body);

                    $delayByBuddiesSentIds[] = $task->assigned_by;
                }

                if(!in_array($task->assigned_to, $delayByMeSentIds))
                {
                    // Task Delayed By Me notification (When Task is accepted but not completed)
                    $user     = User::where(['id' => $task->assigned_to])->first();
                    $title    = trans('notify.task_delayed_by_me_title');
                    $body     = trans('notify.task_delayed_by_me_body',['buddy_name'=>$task->assignedTo->first_name]);
                    $slug     = 'task_delayed_by_me';
                    $buddy_id = $task->assigned_by;
                    $this->sendNotification($user,$title,$body,$slug,$buddy_id,$task->id);
                    \Log::info($body);

                    $delayByMeSentIds[] = $task->assigned_to;
                }
                
            }
        }

    }

}
