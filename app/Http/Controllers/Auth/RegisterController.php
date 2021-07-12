<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\Profile;
use App\Models\VendorType;
use App\Models\VendorProfile;
use App\Models\Category;
use App\Models\Country;
use App\Models\BankDetail;
use App\Models\ProfileSpeciality;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use DB;
use Spatie\Permission\Models\Role;
use Auth;
use App\Models\Helpers\CommonHelper;

class RegisterController extends Controller
{
  use CommonHelper;
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality   without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        $tags_categories = Category::where(['status' => 'active' , 'added_by' => 'admin'])->get();
        $country = Country::where('country_code','+966')->first();
        return view('auth.register',compact('tags_categories','country'));
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
      $mobile_number = $this->ArToEn($request->mobile_number);
      $request->merge([
          'mobile_number' => $mobile_number,
      ]);
      if($request->register_as == 'individual'){
          $validator= $request->validate([
              // 'first_name'     => 'required|string|min:3|max:100',
              'register_as'       => 'required',
              'full_name:ar'      => 'required|string|min:3|max:100',
              'full_name:en'      => 'required|string|min:3|max:100',
              // 'tags'           => 'required',
              'address:ar'        => 'required|string|min:3|max:1000',
              'address:en'        => 'required|string|min:3|max:1000',
              // 'mobile_number'     => 'required|numeric|digits:9|unique:users,mobile_number',
              'mobile_number'     => 'required|numeric|unique:users,mobile_number,NULL,id,user_type,individual|digits:9',
              'email'             => 'required|string|email|max:255|unique:users',
              'is_terms'          =>  'required|in:1',
              'instagram_id'      =>  'nullable|string|max:99',
              // 'instagram_id'      =>  ['nullable' ,'max:99', "regex:/^(?:(?:http|https):\/\/)?(?:www.)?(?:instagram.com|instagr.am)\/([A-Za-z0-9-_]+)/im"],
              'profile_image'     => 'required|nullable|image|max:10000',
              'lattitude'         => 'required',
              'longitude'         => 'required',
              'password'          => 'required|string|min:8|max:14',    
          ],
          [
              'password.regex'    => 'Password Must Contain Upper-case, Lower-case, Number and Special characters Like (~!@#$%^&*()_+=-?.',
          ]);
        }else{
          $validator= $request->validate([
              // 'first_name'     => 'required|string|min:3|max:100',
              'register_as'    => 'required',
              'full_name'      => 'required|string|min:3|max:100',
              // 'tags'           => 'required',
              // 'store_name:ar'  => 'required|string|min:3|max:100',
              // 'store_name:en'  => 'required|string|min:3|max:100',
              // 'address:ar'     => 'required|string|min:3|max:1000',
              // 'address:en'     => 'required|string|min:3|max:1000',
              // 'mobile_number'  => 'required|numeric|digits:9|unique:users,mobile_number',
              'mobile_number'     => 'required|numeric|unique:users,mobile_number,NULL,id,user_type,vendor|digits:9',
              'email'          => 'required|string|email|max:255|unique:users',
              'is_terms'       => 'required|in:1',
              'password'       => 'required|confirmed|string|min:8|max:14',    
          ],
          [
              'password.regex' => 'Password Must Contain Upper-case, Lower-case, Number and Special characters Like (~!@#$%^&*()_+=-?.',
          ]);
        }  

        DB::beginTransaction();
        if(isset($request['full_name:en'])){
          $first_name = $request['full_name:en'];
        }
        if(isset($request->full_name)){
          $first_name = $request->full_name;
        }
        $userArray = [
            'first_name'     => $first_name,
            'email'          => $request->email,
            'mobile_number'  => $request->mobile_number,
            'registered_on'  => 'web',
            'status'         => 'active',
            'user_type'      => $request->register_as,
            'password'       => Hash::make($request->password),
            'country_id'     => Country::where('country_code','+966')->first()->id,
            'profile_image'  => $request->profile_image ? $request->profile_image :'', 
        ];
        if(isset($userArray['profile_image']) && $userArray['profile_image'] != null){
            $userArray['profile_image'] = $this->saveMedia($userArray['profile_image']);
        }
        $user = User::create($userArray);
        // print_r($user);die;
        $role = Role::where('name',$request->register_as)->first();
        $user->assignRole([$role->id]);

        $vendorArray = [
            'user_id'          => $user->id,
            'user_type'        => $request->register_as,
            'first_name:en'    => $request['full_name:en'] ? $request['full_name:en'] :'' ,
            'first_name:ar'    => $request['full_name:ar'] ? $request['full_name:ar'] :'',
            // 'store_name:en'    => $request['store_name:en'] ?$request['store_name:en'] :'' ,
            // 'store_name:ar'    => $request['store_name:ar'] ? $request['store_name:ar'] :'',
            'store_address:en' => $request['address:en'],
            'store_address:ar' => $request['address:ar'],
            'instagram_id'     => $request->instagram_id,
            'lattitude'        => $request->lattitude,
            'longitude'        => $request->longitude,

        ];
        // print_r($vendorArray); die;
        $vendor = Profile::create($vendorArray);

        if($request->tags){
          //speciality Deleted
          ProfileSpeciality::where('profile_id' , $vendor->id)->delete();
          $specializations   =  $request->tags;
          foreach ($specializations as $tag) {
            $specializations = ProfileSpeciality::create([
              'profile_id' =>   $vendor->id ,  
              'category_id' =>  $tag,
            ]);
          }
        }

        if($vendor) {
           $bank_detail = BankDetail::Create([
          'vendor_id'   => $user->id,
          'profile_id'  => $vendor->id,
          'bank_name'   => '',  
          'branch_name'   => '',  
          'branch_city'   => '',  
          'account_name'   => '',  
          'account_number'   => '',  
        ]);
          // verification
          $user->sendEmailVerificationNotification();
          DB::commit();
          Auth::login($user);
          return redirect()->route('verification.notice');
          // end verification
          // return redirect()->route('login')->with('message',trans('auth.registered_successfully'));
        }else{
          DB::rollback();
          return redirect()->route('register')->with('error',trans('auth.something_went_wrong'));
        }
        
    }
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'min:14', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function index2()
    {
        $tags_categories = Category::where(['status' => 'active' , 'added_by' => 'admin'])->get();
        $country = Country::where('country_code','+966')->first();
        return view('auth.register2',compact('tags_categories','country'));
    }

    public function store_register2(Request $request)
    {
        $store_contact = $this->ArToEn($request->store_contact);
        $request->merge([
            'store_contact' => $store_contact,
        ]);
        // print_r($request->all());die;
        $validator= $request->validate([
              'store_name:ar'        => 'required|string|min:3|max:100',
              'store_name:en'        => 'required|string|min:3|max:100',
              'store_email'          => 'required|email|max:255|unique:profiles,store_email',
              'store_address:ar'     => 'required|string|min:3|max:1000',
              'store_address:en'     => 'required|string|min:3|max:1000',
              'store_contact'        => 'required|numeric|digits:9|unique:profiles,store_contact',
              // 'instagram_id'         =>  ['nullable' ,'max:99', "regex:/^(?:(?:http|https):\/\/)?(?:www.)?(?:instagram.com|instagr.am)\/([A-Za-z0-9-_]+)/im"],
              // 'tags'           => 'required',
              'store_banner_image'   => 'required|image|mimes:png,jpg,jpeg,svg|max:10000',
              'store_logo_image'   => 'required|image|mimes:png,jpg,jpeg,svg|max:10000',
        ]);
         DB::beginTransaction();
        $data = $request->all();
        $data['user_type']  = 'vendor';
        $data['user_id']  = auth()->user()->id;
        //create store banner image
        if($request->store_banner_image){
            if($data['store_banner_image']) {
                $path = $this->saveMedia($request->store_banner_image,'store_banner_image');
                $data['store_banner_image'] = $path; 
            }
        }

        //create store logo image
        if($request->store_logo_image){
            if($data['store_logo_image']) {
                $path = $this->saveMedia($request->store_logo_image,'store_logo_image');
                $data['store_logo_image'] = $path; 
            }
        }        
        if(Profile::create($data)) {
            DB::commit();
            return redirect()->route('register_step3')->with('success',trans('auth.registered_step_2'));
        } else {
            return redirect()->route('register_step2')->with('error',trans('auth.error'));
        }
    }

    public function index3()
    {
        $page_title  =  '';
        return view('auth.register3',compact('page_title'));
    }

    public function store_register3(Request $request)
    {
      $certificate  =  $request->all();
      $user_id = Auth::user()->id;
      $profile = Profile::where('user_id',$user_id)->first();
      if(array_key_exists("registration_certificate",$certificate)){
        $this->validate($request,[
          'registration_certificate.*'  => 'sometimes|required|image|mimes:png,jpg,jpeg|max:10000',
        ]);
        $image_type = 'registration_certificate';
        $certificate_images = $request->file('registration_certificate');
      }
      // COMPANY CERTIFICATE
      if(array_key_exists("company_certificate",$certificate)){
        $this->validate($request,[
          'company_certificate.*'       =>  'sometimes|required|image|mimes:png,jpg,jpeg|max:10000',
        ]);
        $image_type = 'company_certificate';
        $certificate_images = $request->file('company_certificate');
      }
      // CITIZEN CERTIFICATE
      if(array_key_exists("citizen_certificate",$certificate)){
        $this->validate($request,[
          'citizen_certificate.*'       =>  'sometimes|required|image|mimes:png,jpg,jpeg|max:10000',
        ]);
        $image_type = 'citizen_certificate';
        $certificate_images = $request->file('citizen_certificate');
      }
      // ID CERTIFICATE
      if(array_key_exists("id_certificate",$certificate)){
        $this->validate($request,[
          'id_certificate.*'       =>  'sometimes|required|image|mimes:png,jpg,jpeg|max:10000',
        ]);
        $image_type = 'id_certificate';
        $certificate_images = $request->file('id_certificate');
      }
      DB::beginTransaction();
      //Save Certificate Images 
      if(!empty($certificate_images)){
        $image  =  $certificate_images;
        foreach ($image as $certificate) { 
          $path = $this->saveMedia($certificate,'certificate');
          $certificate  = ProfileImage::create([
            'profile_id' => $profile->id, 
            'image_type' => $image_type, 
            'image' => $path
          ]);
        }
        if($certificate){
              DB::commit();
              return redirect()->route('home')->with('success', trans('certificates.added'));
        } else {
              DB::rollback();
              return redirect()->route('register_step3')->with('error', trans('certificates.error'));
        }
      } 
    }
}
