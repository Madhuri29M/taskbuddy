<?php
namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB,Auth;
use App\Models\BlockedBranch;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
         $this->middleware('permission:permission-list|permission-create|permission-delete', ['only' => ['index','store']]);
         $this->middleware('permission:permission-create', ['only' => ['create','store']]);
         $this->middleware('permission:permission-delete', ['only' => ['destroy']]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {    
        $page_title = trans('title.permission_index');
        $permissions = Permission::orderBy('id','DESC')->get();
        return view('admin.permissions.index',compact(['permissions','page_title']));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   
        $page_title = trans('title.permission_create');
        $roles = Role::pluck('name','id');
        return view('admin.permissions.create',compact(['roles','page_title']));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:permissions,name',
            'role_id' => 'required'
        ]);
        $permission = Permission::create(['name' => $request->input('name')]);

        foreach ($request->input('role_id') as $key => $value) {
            # code...
            $role = Role::find($value);
            $role->givePermissionTo($request->input('name'));
        }
        return redirect()->route('permissions.index')
                        ->with('success', trans('permission.permission_created_success'));
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $permission = Permission::find($id);
        $permission->delete();
        return redirect()->route('permissions.index')
                        ->with('success', trans('permission.permission_delete_success'));
    }

    public function unauthorized(){
        $message = "Sorry, You Do Not Have Permission To Access This Feature. Please Contact Admin for More Details";
        if(Auth::user()->hasRole('admin') && Auth::user()->status == 'blocked'){
            $branch = Auth::user()->branch;
            $blocked_branch = BlockedBranch::where('branch_id',$branch->id)->first();
            if($blocked_branch){
                $message = $blocked_branch->reason_for_blocking;
            } else {
                $message = "Sorry, Your Account has been blocked. Please Contact Admin for More Details";
            }
        }
        return view('admin.permissions.unauthorized',compact('message'));
    }
}