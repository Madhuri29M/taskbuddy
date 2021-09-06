<?php

namespace App\Models\Helpers;

use Illuminate\Support\Facades\Storage;
use DB;
use Redirect;
use App\Models\User;
use App\Models\Setting;
use App\Models\DeviceDetail;
use App\Models\TaskHistory;
use App\Models\Country;
use App\Models\Notifications;
use App\Models\SmsVerification;
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

    // $code = mt_rand(1000,9999);
    $code = 1234;
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
      $fileNameToStore = uniqid().'_'.time().'.'.$extension;

      $save =  $media->move('media',$fileNameToStore);
      $path =  $this->image_path.$fileNameToStore;
      
      return $path;

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
  public function sendNotification($user,$title,$body,$slug,$buddy_id=NULL,$task_id=NULL){
      
      if($user == null){
        return true;
      }

      //Save notification in DB
      $notify           = new Notifications();
      $notify->user_id  = $user->id;
      $notify->title    = $title;
      $notify->content  = $body;
      $notify->slug     = $slug;
      $notify->buddy_id = $buddy_id;
      $notify->task_id  = $task_id;
      $notify->save();

      //Check for user's device details
      $device = DeviceDetail::where('user_id',$user->id)->orderBy('created_at','DESC')->first();
      //Check if Device details available
      if(!empty($device)){

          $fcm_server_key = 'AAAA9_G8WpA:APA91bERPSHejFW9j3G0oDUmQnhEyDegmQLbf8kPS8qsxT_yN7UEIDtuzRk7_gCyiORBz-BjcBOMH8M3RsIaXbKyaf8CUHkb8wwRUuxIh98rQtD7wDpHnl48XprGC60Zy2cdmBvca7iM';

          $message = [ 
            "to" => $device->device_token,
            "notification" => [
                "body" => $body,
                "title" => $title,
                "sound" => "notification_tone.wav"
            ],
            "data" => [
                'buddy_id' => $buddy_id,
                'task_id' => $task_id,
                'slug' => $slug,
                "click_action" => "FLUTTER_NOTIFICATION_CLICK"
            ]
          ];
          
          /*$push = new PushNotification('fcm');
          $push->setMessage($message);
          $push = $push->setApiKey($fcm_server_key);
          

          $push  =  $push->setDevicesToken($device->device_token)
                         ->send()
                         ->getFeedback();*/

          $push_notification_key = $fcm_server_key;    
          $url = "https://fcm.googleapis.com/fcm/send";            
          $header = array("authorization: key=" . $push_notification_key . "",
              "content-type: application/json"
          );    

          $postdata = json_encode($message);

          $ch = curl_init();
          $timeout = 120;
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
          curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
          curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
          curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

          // Get URL content
          $result = curl_exec($ch);    
          // close handle to release resources
          curl_close($ch);

          return true;

      }

      return true;
  }
    //Update Task History
    public function update_task_history($task_id,$status,$user_id){
        $task_history = new TaskHistory;
        $task_history->task_id = $task_id;
        $task_history->status = $status;
        $task_history->updated_by = $user_id;
        $res = $task_history->save();
        if($res)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    //Send OTP
    public function sendOtp($country_code,$mobile_number,$otp,$sms=''){
        DB::beginTransaction();
        try{
            //Delete Old OTP Entries to prevent DB table data flooding
            $exists = SmsVerification::where('mobile_number',$mobile_number)->where('status','!=','pending')->get();
            if($exists->count() > 0){
                foreach ($exists as $exist) {
                    if($sms){
                        if($exist->id != $sms->id){
                            $exist->delete();
                        }
                    }
                }
            }
            //Add Data into table
            if($sms == ''){
              $sms                = new SmsVerification();
              $sms->mobile_number = $mobile_number;
            }
            $sms->code       = $otp;
            $sms->status     = 'pending';
            $sms->created_at = Carbon::now();
            $sms->save();

            //By-pass SMS Verification
            // DB::commit();
            // return true;

            //temp remove once testing done
            // $otp = mt_rand(1000,9999);
            //send sms api
            DB::commit();
            $response = 1;
            if($response == 1){
              return true;
            } else {
              return false;
            }
        } catch (\Exception|\GuzzleException $e){ 
            DB::rollback();
            return false;
        }
    }

    /**
     * Save images from external URL
     *
     * @param  file  $image
     *
     * @return image model
    */
    public function saveImageFromUrl($url, $featured = null)
    {
        // Get file info and validate
        $file_headers = get_headers($url, TRUE);
        $pathinfo = pathinfo($url);
        // $size = getimagesize($url);

        if ($file_headers === false) return; // when server not found

        $extension = 'jpg';

        // Get the original file
        // echo "<pre>";print_r($url);exit;
        $file_content = file_get_contents($url);
        // Make path and upload
        if (!is_dir('uploads/profile_images/')) // Make the directory if not exist
        {
            mkdir('uploads/profile_images/', 0777, true);
        }
        $path = 'uploads/profile_images/' . uniqid() . '.' . $extension;
        // echo "<pre>";print_r($path);exit;
        $res = file_put_contents(public_path($path), $file_content);
        return $path;
    }
  
  

}
