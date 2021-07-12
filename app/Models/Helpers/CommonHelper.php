<?php

namespace App\Models\Helpers;

use Illuminate\Support\Facades\Storage;
use DB;
use Redirect;
use App\Models\User;
use App\Models\Setting;
use App\Models\DeviceDetail;
use App\Models\Country;
use App\Models\Notifications;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Carbon\Carbon;
use App\Notifications\SendVerificationEmail;
use Edujugon\PushNotification\PushNotification;
// use Notification;
use DateTime;
use Auth, App;



trait CommonHelper
{

  public $image_path = 'media/';


  //Genrate OTP
  public function genrateOtp(){

    $code = mt_rand(1000,9999);
    // $code = 1234;
    return $code;
  }
  


  /**
   * Save different type of media into different folders
   */
  public function saveMedia($file,$type = '')
  {   
      //Laravel Image Saving 
      $media           = $file;
      $filenameWithExt =  $media->getClientOriginalName();
      $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
      $extension       =  $media->getClientOriginalExtension();
      $fileNameToStore = str_replace(' ','_',$filename).'_'.time().'.'.$extension;

      if($type == 'advertisement_image' || $type == 'advertisement_video')
      {
        $fileNameToStore = 'advertisement_'.time().'.'.$extension;
      }

      $save =  $media->move('media',$fileNameToStore);
      $path =  $this->image_path.$fileNameToStore;
      
      return $path;

      //S3 Bucket Image Saving
      // DB::beginTransaction();
      // try{
      //     $path = Storage::disk('s3')->put('images/originals', $file,'public');
      //     DB::commit();
      //     return $path;
      // }catch(\Exception $e){
      //     DB::rollback();
      //     $path = '';
      //     return $path;
      // }   
  }

  /**
   * Delete Media
   */
  public function deleteMedia($file)
  {   
    if(file_exists($file)){

      unlink($file);
      return true;

    }else{

      return true;
    }
  }

  /**
  * Send Notification
  */
  /**
  * Send Notification
  */
  public function sendNotification($user,$title,$body,$slug){
      
      if($user == null){
        return true;
      }

      //Save notification in DB
      $notify           = new Notifications();
      $notify->user_id  = $user->id;
      $notify->title    = $title;
      $notify->content  = $body;
      $notify->slug     = $slug;
     
    
      $notify->save();

      //Check for user's device details
      $device = DeviceDetail::where('user_id',$user->id)->orderBy('created_at','DESC')->first();
      //Check if Device details available
      if(!empty($device)){

          $fcm_server_key = '';

          $message = [ "notification" => [
                "body" => $body,
                "title" => $title
            ],
            "data" => [
                "click_action" => "FLUTTER_NOTIFICATION_CLICK"
            ]
          ];
          
          $push = new PushNotification('fcm');
          $push->setMessage($message);
          $push = $push->setApiKey($fcm_server_key);
          

          $push  =  $push->setDevicesToken($device->device_token)
                         ->send()
                         ->getFeedback();

          return true;

      }

      return true;
  }

}
