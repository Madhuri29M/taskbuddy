<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Helpers\CommonHelper;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Validator;
use DB;

class CustomerMessageController extends Controller
{
  use CommonHelper;
  
    public function __construct()
    {
      $this->middleware('auth');
      $this->middleware('permission:customer_message-list', ['only' => ['index','show']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = trans('customers.user_list');
        
        $users = User::where('user_type','customer')->get();

        return view('admin.customer_message.index',compact('users','page_title'));
    }

    public function index_ajax(Request $request){

        $request         =    $request->all();
        $draw            =    $request['draw'];
        $row             =    $request['start'];
        $columnIndex     =    $request['order'][0]['column']; // Column index
        $columnName      =    $request['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder =    $request['order'][0]['dir']; // asc or desc
        $searchValue     =    $request['search']['value']; // Search value

        $query        = User::where(['user_type'=>'customer','status'=>'active']);   
    
        ## Total number of records without filtering
        $total        = $query->count();
        $totalRecords = $total;
        $length = ($request['length'] == -1) ? $totalRecords : $request['length'];
        $rowperpage      =    $length; // Rows display per page

        ## Total number of record with filtering
        $filter= $query;

        if($searchValue != ''){
            $filter =   $filter->where(function($q)use ($searchValue) {

                            $q->where('first_name','like','%'.$searchValue.'%')
                            ->orWhere('email','like','%'.$searchValue.'%')
                            ->orWhere('mobile_number','like','%'.$searchValue.'%')
                            ->orWhere('created_at','like','%'.$searchValue.'%');

                        })->where(['user_type'=>'customer','status'=>'active']);
        }

        $filter_data           = $filter->count();
        $totalRecordwithFilter = $filter_data;

        ## Fetch records
        $empQuery   = $filter;
        $empQuery   = $empQuery->orderBy($columnName, $columnSortOrder)->offset($row)->limit($rowperpage)->get();

        //Assign Dynamic Values
        $data       = array();

        $i = 1;
        foreach ($empQuery as $emp) {
        ## Set dynamic route for action buttons
            $emp['number']              =  $row + $i;
            $emp['profile_picture']     =  asset($emp["profile_image"]);
          
            
            $data[] = $emp;

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
    * Ajax for index page status dropdown.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function send(Request $request)
    {   
        $validator = $request->validate([

            'title'      => 'required|min:5|max:30',
            'body'       => 'required|min:5|max:100',
        ]);

       $data = $request->all();
       // print_r($data);die;
     
       if(!isset($data['id']) || $data['id'] == null){
         return redirect()->route('c_message.index')->with('warning',trans('customer_messages.select_user'));
       }

       foreach ($data['id'] as $key => $value) {

         $user = User::find($value);

            if($user != null){

                if($data['send_type'] == 'push'){

                    $type     = 'broadcast';
                    $title    = $data['title'];
                    $body     = $data['body'];
                    $slug     = 'admin_broadcast';
                    $order_id = '';

                $this->sendNotification($user,$title,$body,$slug);


                }else{

                    if($user->notify == '1'){

                       $title = $data['title'];
                       $body  = $data['body'];
     
                       $this->sendEmail($user,$title,$body);

                    }
                }
            }
       }

       return redirect()->route('c_message.index')->with('success',trans('customer_messages.message_sent'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
    }

   
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
       
    }

     /**
     * Update the selected user password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function change_password(Request $request)
    {

       
    }

  

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
    }

    
    


   
        

}
