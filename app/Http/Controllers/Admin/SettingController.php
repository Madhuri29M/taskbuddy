<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use App\Models\Helpers\CommonHelper;

class SettingController extends Controller
{   
    // use CommonHelper;

    public function __construct()
    {
      $this->middleware('auth');
      // $this->middleware('permission:setting-list', ['only' => ['index','show']]);
      // $this->middleware('permission:setting-create', ['only' => ['create','store']]);
      // $this->middleware('permission:setting-edit', ['only' => ['edit','update']]);
      // $this->middleware('permission:setting-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $page_title = trans('setting.settings');
        $settings = Setting::pluck('value','name')->all();
        return view('admin.setting.index',compact(['settings','page_title']));
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {   
        $settings = $request->all();
        $vreq = [];
        foreach ($settings as $sk => $sv) {
            if($sk == 'app_name'){
                $validation = 'required|max:99';
            }
            else if($sk == 'google_map_api_key'){
                $validation = 'required|max:50';
            }else{
                $validation = 'required';
            }
            $vreq[$sk] = $validation;
        }
        $request->validate($vreq);

        foreach ($settings as $key => $value) {
        Setting::where('name', $key)->update(['value'=> $value]);
        }   

        return redirect()->route('setting.index')->with('success','Settings updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
   
}
