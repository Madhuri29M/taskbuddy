<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Http\Resources\NotificationResource;
use App\Models\Notifications;
use Auth,DB;
use Carbon\Carbon;

class NotificationController extends BaseController
{
    public function index(){
        $user = Auth::user();
        if($user == null){
            return $this->sendError($this->array,trans('notifications.user_not_found'));
        }
        //update all notifications mark as read
        Notifications::where(['user_id' => $user->id,'is_sent' => '1'])->update(['is_read'=>'1']);

        $notifications = Notifications::where(['user_id' => $user->id,'is_sent' => '1'])
        ->orderBy('id','DESC')
        ->paginate();

        if(count($notifications)) {
          return $this->sendPaginateResponse(NotificationResource::collection($notifications),trans('notifications.notification_data_found'));
        } else {
          return $this->sendResponse('',trans('common.no_data')); 
        }
      
    }
    public function clear_all_notifications()
    {
      $notification = Notifications::where('user_id', Auth::user()->id)->delete();
      if($notification) {
          return $this->sendResponse([],trans('notifications.notifications_cleared'));
      } else {
          return $this->sendResponse('',trans('notifications.notifications_already_cleared'));
      }
    }

    /**
     * Notification mark as read
     */
    public function markAsRead(Request $request)
    {
        $notifications = Notifications::where('id',$request->notification_id)->update(['is_read' => '1']);
        return $this->sendResponse('', trans('notifications.notification_status_updated'));         
    }
    
    /**
     * Count for unread notifications
     */
    public function unreadNotifCount(Request $request)
    {
        $count = Notifications::where('is_read','0')->where('user_id',Auth::user()->id)->count();
        $data['count'] = (string)$count;
        return $this->sendResponse($data, trans('notifications.unread_notification_count'));
    }

    /**
     * Delete Single Notification
     */
    public function deleteNotification(Request $request)
    {
        $notifications = Notifications::where('id',$request->notification_id)->delete();
        return $this->sendResponse('', trans('notifications.notification_deleted'));         
    }
}

