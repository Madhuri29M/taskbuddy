<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cms;
use App\Models\Translations\CmsTranslation;


class CmsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:cms-list', ['only' => ['index','show']]);
        $this->middleware('permission:cms-create', ['only' => ['create','store']]);
        $this->middleware('permission:cms-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:cms-delete', ['only' => ['destroy']]);
    }


    public function index()
    {
        $page_title = trans('cms.cms_index');
        $cms = CmsTranslation::all();
        return view('admin.cms.index',compact('cms','page_title'));
    
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        $page_title = trans('title.cms_create');
        return view('admin.cms.create',compact('page_title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = $request->validate([
            'page_name:ar'  => 'required|max:50',
            'page_name:en'  => 'required|max:50',
            'content:ar'    => 'required',
            'content:en'    => 'required',
            'slug'          => 'required|max:50',
            // 'display_order' => 'required|integer',
        ]);

        $data = $request->all();
        $data['display_order'] = '1';
    
        if(Cms::create($data)) {
            return redirect()->route('cms.index')->with('success',trans('cms.cms_saved_successfully'));
        } else {
            return redirect()->route('cms.index')->with('error',trans('cms.cms_saved_unsuccessfully'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page_title = trans('cms.show');
        $cms = Cms::find($id);
        return view('admin.cms.show',compact('cms','page_title'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page_title = trans('cms.update');
        $cms = CmsTranslation::find($id);
        return view('admin.cms.edit',compact('cms','page_title'));
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

        $data = $request->all();
        $cms = CmsTranslation::find($id);
        
        if(empty($cms)){
            return redirect()->route('cms.index')->with('error',trans('cms.something_went_wrong'));
        }

        $validator = $request->validate([
            'page_name'     => 'required|max:50',
            'content'       => 'required',
            // 'display_order' => 'required|integer|max:255',
        ]);

        if($cms->update($data)){
            return redirect()->route('cms.index')->with('success',trans('cms.cms_update_success'));
        } else {
            return redirect()->route('cms.index')->with('error',trans('cms.cms_update_unsuccess'));
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
        // $cms = Cms::find($id);

        // if($cms->delete()){
        //     return redirect()->route('cms.index')->with('success',trans('cms.cms_deleted_Successfully'));
        // }else{
        //     return redirect()->route('cms.index')->with('error',trans('cms.cms_deleted_UnSuccessfully'));
        // }
    }

    public function status(Request $request)
    {
       //  $cms= Cms::where('id',$request->id)
       //         ->update(['status'=>$request->status]);
    
       // if($cms){
       //  return response()->json(['success' => trans('cms.cms_status_update_sucess')]);
       // }else{
       //  return response()->json(['error' => trans('cms.cms_status_update_unsucess')]);
       // }
    }

     public function payment_success(Request $request)
    {
       return view('admin.cms.payment_success');
    }

     public function payment_error(Request $request)
    {
       return view('admin.cms.payment_error');
    }
}
   