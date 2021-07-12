<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Notifications;
use App\Http\Resources\Master\NotificationResource;
use Auth;
use App;


class NotificationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       $this->middleware(['auth','profile_updated','admin_approval','user_inactive']);
    }

    public function index() {
      $page_title = trans('notifications.heading');
      //update all notifications mark as read
      $user  =  Auth::user();
      $notification = Notifications::where(['user_id' => $user->id,'is_sent' => '1'])->update(['is_read'=>'1']);
      return view ('admin.notifications.index',compact('page_title','user')); 
    }

    public function index_ajax(Request $request) {
        $request         =    $request->all();
        $draw            =    $request['draw'];
        $row             =    $request['start'];
        // $rowperpage      =    $request['length']; // Rows display per page
        $columnIndex     =    $request['order'][0]['column']; // Column index
        $columnName      =    $request['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder =    $request['order'][0]['dir']; // asc or desc
        $searchValue     =    $request['search']['value']; // Search value

        $user  =  Auth::user();
        if($user->user_type == 'admin') {
         // $query = Notifications::where('user_id',$user->id)->orwhere('user_type','admin');
         $query = Notifications::where('user_type','admin');
      }else{
       $query = Notifications::where('user_id',$user->id);
     }
        ## Total number of records without filtering
        $total = $query->count();
        $totalRecords = $total;

        $length = ($request['length'] == -1) ? $totalRecords : $request['length'];
        $rowperpage      =    $length; // Rows display per page

        ## Total number of record with filtering
        $filter = $query; 

        if($searchValue != ''){
        $filter = $filter->where(function($q)use ($searchValue) {
                            $q->where('title','like','%'.$searchValue.'%')
                            ->orWhere('id','like','%'.$searchValue.'%')
                            ->orWhere('content','like','%'.$searchValue.'%');
                            // ->orWhere('status','like','%'.$searchValue.'%');
                     });
        }
        
        $filter_count = $filter->count();
        $totalRecordwithFilter = $filter_count;

        ## Fetch records
        $empQuery = $filter;
        $empQuery = $empQuery->orderBy($columnName, $columnSortOrder)->offset($row)->limit($rowperpage)->get();
        $data = array();
        foreach ($empQuery as $emp) {
        ## Set dynamic route for action buttons
            // $emp['edit']= route("country.edit",$emp["id"]);
            $emp['show']= route("notifications.show",$emp["id"]);
            $emp['delete'] = route("notifications.destroy",$emp["id"]);

            if($emp["slug"] == 'new_customer_registered'){
              if($emp["data_id"]){
                $redirect_link = route('customers.show',$emp["data_id"]);
              } else {
                $redirect_link = route('customers.index');
              }
            }
            if($emp["slug"] == 'new_ticket_raised_by_customer' || $emp["slug"] == 'new_ticket_raised_by_vendor' || $emp["slug"] == 'new_ticket_raised_by_individual' || $emp["slug"] == 'new_ticket_raised_by_employee'){
                $redirect_link = route('contact_us.index');
            }
            $emp['redirect_link'] = @$redirect_link;
            
          $data[]=$emp;
        }

        ## Response
        $response = array(
          "draw" => intval($draw),
          "iTotalRecords" => $totalRecords,
          "iTotalDisplayRecords" => $totalRecordwithFilter,
          "aaData" => $data
        );

        echo json_encode($response);
    }

    public function destroy($id)
    {
      try {

        $reason = Notifications::find($id);

        if(empty($reason) && $reason->count() == 0){
          return redirect()->route('notifications.index')->with('error', trans('notifications.error'));
        }

        if($reason->delete()){
            return redirect()->route('notifications.index')->with('success',trans('notifications.deleted'));
        }else{
            return redirect()->route('notifications.index')->with('error',trans('notifications.error'));
        }

      } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->withError($e->getMessage());
      }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function get_notification()
    {
      $user = Auth::guard('web')->user();

      $notifications = Notifications::where(['user_id' => $user->id,'is_read' => '0'])->get();
      $notifications_data = json_decode(json_encode(NotificationResource::collection($notifications)));

      $notif_data = [];
      $url = route("bookings",0);
      
      if($user->user_type == 'admin') {
          $url = route('notifications.index'); 
      }

      foreach ($notifications_data as $nfd) {
        array_push($notif_data, [
            "title"  => $nfd->title,
            "message"  => $nfd->content,
            "icon"  => asset('customer/img/noti_icon.png'),
            "url" => $url
        ]);
        $notif = Notifications::where('id',$nfd->id)->first();
        $notif->is_read = '1';
        $notif->save();
      }

      $notification_array = [
        "notif" => $notif_data,
        "count" => count($notif_data),
        "result" => true
      ];
      /*$notification_array = [
        "notif" => [
            [
              "title"  => "New Booking Request! only 2",
              "message"  => "You Have New Booking Request! Hurry Accept The Request Now.",
              "icon"  => "http://localhost:66/blusher-web-application/public/media/artist1_1612352276.jpg",
              "url" => route("bookings","pending")
            ]
        ],
        "count" => 2,
        "result" => true
      ];
      $notification_array = [
        "notif" => [],
        "count" => 0,
        "result" => false
      ];*/
      return response()->json($notification_array);
      /*$data = 2;
      if($data > 0) {
        return '{"notif":[{"title":"New Booking Request!1","message":"You Have New Booking Request! Hurry Accept The Request Now.","icon":"http://localhost:66/blusher-web-application/public/media/artist1_1612352276.jpg","url":"http://localhost/blusher-web-application/public/admin/bookings/pending"}, {"title":"New Booking Request!2","message":"You Have New Booking Request! Hurry Accept The Request Now.","icon":"http://localhost:66/blusher-web-application/public/media/artist1_1612352276.jpg","url":"http://localhost/blusher-web-application/public/admin/bookings/pending"}, {"title":"New Booking Request!3","message":"You Have New Booking Request! Hurry Accept The Request Now.","icon":"http://localhost:66/blusher-web-application/public/media/artist1_1612352276.jpg","url":"http://localhost/blusher-web-application/public/admin/bookings/pending"}],"count":3,"result":true}';
      } else {
        return '{"notif":[],"count":0,"result":true}';
      }*/
    }
}
