<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Helpers\CommonHelper;
use Illuminate\Support\Facades\Hash;
use App\Notifications\ForgotPasswordRequest;
use App\Models\User;
use App\Models\Country;
use App\Models\Order;
use Validator;


class UserController extends Controller
{
    use CommonHelper;

    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = trans('title.user_list');
        $users = User::where('user_type','customer')->get();

        return view('admin.user.index',compact('users','page_title'));
    }

    public function index_ajax(Request $request){

        $request         =    $request->all();
        $draw            =    $request['draw'];
        $row             =    $request['start'];
        $rowperpage      =    $request['length']; // Rows display per page
        $columnIndex     =    $request['order'][0]['column']; // Column index
        $columnName      =    $request['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder =    $request['order'][0]['dir']; // asc or desc
        $searchValue     =    $request['search']['value']; // Search value

        $query        = User::where('user_type','customer');   
    
        ## Total number of records without filtering
        $total        = $query->count();
        $totalRecords = $total;

        ## Total number of record with filtering
        $filter= $query;

        if($searchValue != ''){
            $filter =   $filter->where(function($q)use ($searchValue) {

                            $q->where('name','like','%'.$searchValue.'%')
                            ->orWhere('email','like','%'.$searchValue.'%')
                            ->orWhere('phone_number','like','%'.$searchValue.'%')
                            ->orWhere('id','like','%'.$searchValue.'%')
                            ->orWhere('created_at','like','%'.$searchValue.'%');

                        });
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
            $emp['number']     =  $row + $i;
            $emp['edit']       =  route("user.edit",$emp["id"]);
            $emp['show']       =  route("user.show",$emp["id"]);
            
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page_title = trans('title.user_detail');
        $user = User::find($id);
        return view('admin.user.show',compact('user','page_title'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // $user      = User::find($id);
        // $countries = Country::where('status','active')->get();
        // return view('admin.user.edit',compact('user','countries'));
    }

    /**
     * Show the form for editing the admin profile.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function admin_edit($id)
    {
        $admin     = User::where(['id'=>$id,'user_type'=>'admin'])->first();
        $countries = Country::where('status','active')->get(); 
        return view('admin.user.admin_edit',compact('admin','countries'));
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function admin_update(Request $request, $id)
    {
        $data  = $request->all();
        $admin = User::where(['id' => $id,'user_type'=>'admin'])->first();
      

        if(empty($admin)){
          return redirect()->route('home')->with('error','Something went wrong');  
        }

        $validator = $request->validate([
            'name'                 => 'required|max:50',
            'email'                => ['required','email','max:255',Rule::unique('users','email')->ignore($admin->id)],
            'profile_image'      => 'sometimes|nullable|image|max:10000',
            'old_password'         => 'sometimes|nullable|min:6|max:15',
            'new_password'         => 'sometimes|nullable|min:6|max:15',
            'confirm_password'     => 'sometimes|nullable|min:6|max:15',
            
        ]);

        $data = $request->all();

        if(isset($data['old_password']) && $data['old_password'] != null){

            $match = Hash::check($data['old_password'],$admin->password);

            if(!$match){
                return redirect()->back()->withInput($data)->with('error','Invalid old password');
            }

            if($data['new_password'] == null){
                return redirect()->back()->withInput($data)->with('error','Please enter a new password');
            }

            if($data['confirm_password'] == null){
                return redirect()->back()->withInput($data)->with('error','Please confirm a new password');
            }

            if($data['new_password'] != $data['confirm_password']){
                return redirect()->back()->withInput($data)->with('error','The confirmed password must be same as new password');
            }

            $password = Hash::make($data['new_password']);
            $data['password'] = $password;
        }

        if(isset($data['profile_image']) && $data['profile_image'] != null){
            $data['profile_image'] = $this->saveMedia($data['profile_image']);
        }
        $data['first_name']  = $request->name;
        if($admin->update($data)){
            return redirect()->route('admin_edit_profile',[$admin])->with('success','Admin Details Updated');
        }else{
            return redirect()->route('home')->with('error','Something went wrong');
        }
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

     /**
     * Ajax for index page status dropdown.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request)
    {   
        if($request->status == 'inactive'){

         $order = Order::where('customer_id',$request->id)->where('status','!=','delivered')->where('status','!=','cancelled')->where('status','!=','pending')->first();

         if($order != null){
            return response()->json(['error' => 'The customer has a booked order','type'=>'error']);
         }
       }
        //print_r($request->all()); die;
        $user= User::where('id',$request->id)
               ->update(['status'=>$request->status]);
    
       if($user){
         $user = User::find($request->id);
        if($user->status =='inactive'){

            //Remove Old Login Access Tokens If Exists
                $tokens = $user->tokens;
        
                if($tokens->count() > 0){
                   foreach($tokens as $token) {
                     $token->revoke();   
                    }
                }
        }
        return response()->json(['success' => 'User Status Updated','type'=>'success']);
       }else{
        return response()->json(['error' => 'User Status Not Updated','type'=>'error']);
       }
    }

     /**
     * Send Push Notification
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
        $order_id = '';
        $this->sendNotification($type,$user,$title,$body,$slug,$order_id);;
    
        return response()->json(['message' => 'Notification Sent','type'=>'success']);


    }

    public function export(Request $request)
    {
       $fileName = 'customers.csv';
       $tasks = User::where(['user_type'=>'customer'])->get();

            $headers = array(
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            );

            $columns = array('Name', 'Mobile Number', 'Email', 'Registration Date', 'Status');

            $callback = function() use($tasks, $columns) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);

                foreach ($tasks as $task) {
                    $row['Name']  = $task->name;
                    $row['Mobile Number']    = $task->phone_number;
                    $row['Email']    = $task->email;
                    $row['Registration Date']  = $task->created_at;
                    $row['Status']  = $task->status;

                    fputcsv($file, array($row['Name'], $row['Mobile Number'], $row['Email'], $row['Registration Date'], $row['Status']));
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
    }

}
