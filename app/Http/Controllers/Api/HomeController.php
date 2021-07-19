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

class HomeController extends BaseController
{
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
        foreach ($day_wise_task_count as $day_count) {
            $my_days[$day_count['day']] = $day_count['count'];
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

        $day_wise_task_per['Monday']['my_task'] = (($my_days['Monday'] + $buddy_days['Monday']) == 0) ? 0 : ($my_days['Monday'] * 100) / ($my_days['Monday'] + $buddy_days['Monday']);
        $day_wise_task_per['Tuesday']['my_task'] = (($my_days['Tuesday'] + $buddy_days['Tuesday']) == 0) ? 0 : ($my_days['Tuesday'] * 100) / ($my_days['Tuesday'] + $buddy_days['Tuesday']);
        $day_wise_task_per['Wednesday']['my_task'] = (($my_days['Wednesday'] + $buddy_days['Wednesday']) == 0) ? 0 : ($my_days['Wednesday'] * 100) / ($my_days['Wednesday'] + $buddy_days['Wednesday']);
        $day_wise_task_per['Thursday']['my_task'] = (($my_days['Thursday'] + $buddy_days['Thursday']) == 0) ? 0 : ($my_days['Thursday'] * 100) / ($my_days['Thursday'] + $buddy_days['Thursday']);
        $day_wise_task_per['Friday']['my_task'] = (($my_days['Friday'] + $buddy_days['Friday']) == 0) ? 0 : ($my_days['Friday'] * 100) / ($my_days['Friday'] + $buddy_days['Friday']);
        $day_wise_task_per['Saturday']['my_task'] = (($my_days['Saturday'] + $buddy_days['Saturday']) == 0) ? 0 : ($my_days['Saturday'] * 100) / ($my_days['Saturday'] + $buddy_days['Saturday']);
        $day_wise_task_per['Sunday']['my_task'] = (($my_days['Sunday'] + $buddy_days['Sunday']) == 0) ? 0 : ($my_days['Sunday'] * 100) / ($my_days['Sunday'] + $buddy_days['Sunday']);

        $day_wise_task_per['Monday']['buddy_task'] = (($my_days['Monday'] + $buddy_days['Monday']) == 0) ? 0 : ($buddy_days['Monday'] * 100) / ($my_days['Monday'] + $buddy_days['Monday']);
        $day_wise_task_per['Tuesday']['buddy_task'] = (($my_days['Tuesday'] + $buddy_days['Tuesday']) == 0) ? 0 : ($buddy_days['Tuesday'] * 100) / ($my_days['Tuesday'] + $buddy_days['Tuesday']);
        $day_wise_task_per['Wednesday']['buddy_task'] = (($my_days['Wednesday'] + $buddy_days['Wednesday']) == 0) ? 0 : ($buddy_days['Wednesday'] * 100) / ($my_days['Wednesday'] + $buddy_days['Wednesday']);
        $day_wise_task_per['Thursday']['buddy_task'] = (($my_days['Thursday'] + $buddy_days['Thursday']) == 0) ? 0 : ($buddy_days['Thursday'] * 100) / ($my_days['Thursday'] + $buddy_days['Thursday']);
        $day_wise_task_per['Friday']['buddy_task'] = (($my_days['Friday'] + $buddy_days['Friday']) == 0) ? 0 : ($buddy_days['Friday'] * 100) / ($my_days['Friday'] + $buddy_days['Friday']);
        $day_wise_task_per['Saturday']['buddy_task'] = (($my_days['Saturday'] + $buddy_days['Saturday']) == 0) ? 0 : ($buddy_days['Saturday'] * 100) / ($my_days['Saturday'] + $buddy_days['Saturday']);
        $day_wise_task_per['Sunday']['buddy_task'] = (($my_days['Sunday'] + $buddy_days['Sunday']) == 0) ? 0 : ($buddy_days['Sunday'] * 100) / ($my_days['Sunday'] + $buddy_days['Sunday']);

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

        $dashboard_response = [];
        $dashboard_response['my_task_week_chart'] = $my_days;
        $dashboard_response['my_task_buddy_task_week_wise'] = $day_wise_task_per;
        $dashboard_response['completed_task_per'] = $completed_task_per;
        $dashboard_response['pending_task_per'] = $pending_task_per;
        $dashboard_response['trashed_task_per'] = $trashed_task_per;
        $dashboard_response['this_week_assigned_by_buddy_count'] = $this_week_assigned_by_buddy_count;
        $dashboard_response['this_week_assigned_to_buddy_count'] = $this_week_assigned_to_buddy_count;
        $dashboard_response['assigned_by_buddy_all_task_count'] = $assigned_by_buddy_all_task_count;
        $dashboard_response['assigned_to_buddy_all_task_count'] = $assigned_to_buddy_all_task_count;
        $dashboard_response['delayed_task_by_me'] = $delayed_task_by_me;
        $dashboard_response['delayed_task_by_buddy'] = $delayed_task_by_buddy;
        
        return $this->sendResponse($dashboard_response,trans('common.dashboard_data'));
        
    }

}
