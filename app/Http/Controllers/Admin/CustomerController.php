<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Validator,Auth,DB;
use App\Models\Helpers\CommonHelper;
use Illuminate\Support\Facades\Mail;
use App\Mail\CommonMail;

class CustomerController extends Controller
{
	use CommonHelper;

    public function __construct()
    {
      $this->middleware('auth');
      $this->middleware('permission:customer-list', ['only' => ['index','show']]);
      $this->middleware('permission:customer-create', ['only' => ['create','store']]);
      $this->middleware('permission:customer-edit', ['only' => ['edit','update']]);
      $this->middleware('permission:customer-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index() {
      $page_title = trans('customers.heading');
      $customers = User::where('user_type','Customer')->get();
      return view ('admin.customers.index',compact('customers','page_title')); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index_ajax(Request $request) {

        $query = User::where('user_type','customer');
        $totalRecords = $query->count();
        $request         =    $request->all();
        $draw            =    $request['draw'];
        $row             =    $request['start'];
        $length = ($request['length'] == -1) ? $totalRecords : $request['length']; 
        $rowperpage      =    $length; // Rows display per page
        $columnIndex     =    $request['order'][0]['column']; // Column index
        $columnName      =    $request['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder =    $request['order'][0]['dir']; // asc or desc
        $searchValue     =    $request['search']['value']; // Search value

      //   $user  =  Auth::user();
      //   if($user->user_type == 'customer') {
      //   $query = User::where('user_type','customer');
      // }
     
    
        ## Total number of records without filtering
        $total = $query->count();
        $totalRecords = $total;

        ## Total number of record with filtering
        $filter = $query;

        if($searchValue != ''){
        $filter = $filter->where(function($q)use ($searchValue) {
                            $q->where('first_name','like','%'.$searchValue.'%')
                            ->orWhere('last_name','like','%'.$searchValue.'%')
                            ->orWhere('email','like','%'.$searchValue.'%')
                            ->orWhere('mobile_number','like','%'.$searchValue.'%')
                            ->orWhere('status','like','%'.$searchValue.'%')
                            ->orWhere('id','like','%'.$searchValue.'%');
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
            $emp['name']= $emp["first_name"].' '.$emp["last_name"];
            $emp['email']= ($emp["email"]) ? $emp["email"] : '';
            $emp['show']= route("customers.show",$emp["id"]);
            $emp['delete'] = route("customers.destroy",$emp["id"]);
            
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

    public function create() {
      
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $page_title = trans('customers.show');
        $customers = User::find($id);
        if($customers){
            return view('admin.customers.show',compact(['customers','page_title']));
        }else{
            return redirect()->route('customers.index')->with('error', trans('customers.admin_error'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }

    public function destroy($id) {
        $customers = User::find($id);
        if(!$customers){
          return redirect()->route('customers.index')->with('error',trans('common.no_data'));
        }
        if($customers->delete()){
            return redirect()->route('customers.index')->with('success',trans('customers.deleted'));
        }else{
            return redirect()->route('customers.index')->with('error',trans('customers.error'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function status(Request $request) {
       // print_r($request->all()); die;
        $customer= User::find($request->id);
        $customer->status = $request->status;
       if($customer->save()){

        //EMAIL NOTIFY
            if($customer->status == 'active') {
                $subject = trans('notify.customer_profile_activated_email_subject');
                $content = trans('notify.customer_profile_activated_email_content');
            }
            if($customer->status == 'inactive') {
                $subject = trans('notify.customer_profile_suspended_email_subject');
                $content = trans('notify.customer_profile_suspended_email_content');
            }
            if($customer->email){
                $email =  $customer->email;
                $user = ($customer->first_name) ? $customer->first_name." ".$customer->last_name : 'User';
                $data = [
                    'subject'   => $subject,
                    'user'   => $user,
                    'content'   => $content,
                    'template'  => 'mail.common',
                    'url'   => ''
                ];
                Mail::to($email)->send(new CommonMail($data));
            }
        //EMAIL NOTIFY

        return response()->json(['success' => trans('customers.status_updated')]);
       } else {
        return response()->json(['error' => trans('customers.error')]);
       }
    }

     public function send_notification(Request $request)
    {

        $validator = Validator::make($request->all(),[

            'id'           => 'required|exists:users,id',
            'title'        => 'required|max:50',
            'body'         => 'required|max:300'

        ]);
      
        if($validator->fails()){
            return response()->json(['message' => $validator->errors()->first(),'type'=>'error']);
        }

        $user = User::where(['id'=>$request->id])->first();

        if($user == null){
          return response()->json(['message' => 'User Not Found','type'=>'error']);
        }

        $type     = 'broadcast';
        $title    = $request->title;
        $body     = $request->body;
        $slug     = 'admin_message';
        $this->sendNotification($user,$title,$body,$slug);
    
        return response()->json(['message' => 'Notification Sent','type'=>'success']);
    }

  }
