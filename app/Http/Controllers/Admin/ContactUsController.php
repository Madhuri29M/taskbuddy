<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactUs;
use App\Models\User;
use App\Models\Helpers\CommonHelper;
use Illuminate\Support\Facades\Mail;
use Validator,Auth,DB;

class ContactUsController extends Controller
{
  use CommonHelper;

    public function __construct()
    {
      $this->middleware('auth');
      $this->middleware('permission:ticket-list', ['only' => ['index','show']]);
      $this->middleware('permission:ticket-create', ['only' => ['create','store']]);
      $this->middleware('permission:ticket-edit', ['only' => ['edit','update']]);
      $this->middleware('permission:ticket-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {  
      $page_title = trans('contact_us.heading');
      $contactus = ContactUs::all();
      return view('admin.contact_us.index',compact('contactus','page_title'));
    }


    public function index_ajax(Request $request){
        // print_r($request);
        $from_date       =    $this->ArToEn($request->from_date);
        $to_date         =    $this->ArToEn($request->to_date);
        $status_filter   =    $request->status;
        $type_filter     =    $request->user_type;
        $request         =    $request->all();
        $draw            =    $request['draw'];
        $row             =    $request['start'];
        $rowperpage      =    $request['length']; // Rows display per page
        $columnIndex     =    $request['order'][0]['column']; // Column index
        $columnName      =    $request['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder =    $request['order'][0]['dir']; // asc or desc
        $searchValue     =    $request['search']['value']; // Search value

        $query = ContactUs::query();
        $query = $query->whereBetween('created_at',[$from_date,$to_date]);

        if($status_filter != null){
          $query = $query->where('status',$status_filter);
        }

        if($type_filter != null){
          $query = $query->where('user_type',$type_filter);
        }
    
        ## Total number of records without filtering
        $totalRecords = $query->count();

        ## Total number of record with filtering
        if($searchValue != ''){
            $filter =   $query->where(function($q) use ($searchValue) {
                             $q->where('user_type','like','%'.$searchValue.'%')
                              ->orWhere('description','like','%'.$searchValue.'%')
                              ->orWhere('id','like','%'.$searchValue.'%');
                        });
        }
        $filter = $query;
        $totalRecordwithFilter = $filter->count();

        ## Fetch records
        $empQuery = $filter->orderBy($columnName, $columnSortOrder)->offset($row)->limit($rowperpage)->get();
        $data = array();
        $i = 1;
        foreach ($empQuery as $emp) {
          $user_type = $emp->user->user_type;
          if($emp->user->user_type == 'vendor'){
            $user_type = trans('vendors.partner');
          }
          if($emp->user->user_type == 'individual'){
            $user_type = trans('vendors.individual');
          }
          if($emp->user->user_type == 'customer'){
            $user_type = trans('payout.customer');
          }

        ## Set dynamic route for action buttons
          // $emp['number']            = $row + $i;
          $emp['name']              = ($emp->user->first_name) ? $emp->user->first_name.' '.$emp->user->last_name : @$emp->user->profile->first_name.' '.@$emp->user->profile->last_name;
          $emp['user_type']         = ucfirst($user_type);
          $emp['date'] = $emp->created_at->toDateString();

          $data[]      = $emp;
          $i++;
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

    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {   
        $page_title = trans('title.contact_detail');
        $contactus = ContactUs::find($id);
        return view('admin.contact_us.show',compact('contactus','page_title'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $contactus = ContactUs::find($id);

        if($contactus->delete()){
            return redirect()->route('contact_us.index')->with('success',trans('contact_us.deleted'));
        }else{
            return redirect()->route('contact_us.index')->with('error',trans("contact_us.error"));
        }
    }

     public function status(Request $request)
    {   
        $contact = ContactUs::find($request->id);
        if($contact->status == 'resolved') {
          if($request->status == 'new_ticket' || $request->status == 'acknowledged'){
            return response()->json(['error' => trans("contact_us.can't_update_resolved"),'type'=>'error']);
          }
        }elseif ($contact->status == 'acknowledged') {
            if($request->status == 'new_ticket'){
             return response()->json(['error' => trans("contact_us.sorry_you_not_able_to_change_status_to_new"),'type'=>'error']); 
            }
        } 

        if($request->status == 'new_ticket'){
          $request->status = 'pending';
        }
    
        $contact= ContactUs::where('id',$request->id)
               ->update(['status'=>$request->status]);
        $contact = ContactUs::find($request->id);  
        
        // print_r($contact);die;      
      if($contact){
        if($contact->status != 'resolved'){
          //Notify admin for new ticket raised by vendor
            $user     = User::where(['id' => $contact->user_id])->first();
            $title    = trans('notify.ticket_status_updated_to_'.$contact->status);
            $body     = trans('notify.ticket_status_updated_body_to_'.$contact->status);
            $slug     = 'ticket_status_updated';
            $this->sendNotification($user,$title,$body,$slug,null,null,$contact->id);
          
        }


        return response()->json(['success' =>trans('contact_us.status_updated'),'type'=>'success']);
       
      }else{

        return response()->json(['error' => trans('contact_us.error'),'type'=>'error']);
       }
    }

    public function r_status(Request $request){
      $validator = Validator::make($request->all(),[

            'id'           => 'required|exists:contact_us,id',
            'comment'         => 'required|max:500'

      ]);

      if($validator->fails()){
            return response()->json(['message' => $validator->errors()->first(),'type'=>'error']);
        }
        $contact_us   =  ContactUs::find($request->id);
        $data['comment']    = $request->comment;
        $data['status']    ='resolved';
        $contact_us->update($data);

        $contact = ContactUs::find($request->id);  
        if($contact){

          //Notify admin for new ticket raised by vendor
          $user     = User::where(['id' => $contact->user_id])->first();
          $title    = trans('notify.ticket_status_updated_to_'.$contact->status);
          $body     = trans('notify.ticket_status_updated_body_to_'.$contact->status);
          $slug     = 'ticket_status_updated';
          $this->sendNotification($user,$title,$body,$slug,null,null,$contact->id);

          
          return response()->json(['message' => trans('contact_us.status_updated'),'type'=>'success']);

        }else{

          return response()->json(['error' => trans('contact_us.error'),'type'=>'error']);
       }
    }
}