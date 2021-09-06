<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'error' => 'Something went wrong',
    'failed' => 'These credentials do not match our records.',
    'password' => 'Password',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',

    'forgot_password'   =>  'Forgot Password',
    'sign_in'          =>  'Sign In',
    //register
    'address_en'  => 'Address in English',
    'address_ar'  => 'Address in Arabic',
    'r-password' => 'Password',
    'employee'  => 'employee',
    'individual' => 'Freelancer',
    'email' => 'E-Mail Address',
    'confirm_password' => 'Confirm Password',
    'full_name' => 'Full Name',
    'full_name_ar' => 'Full Name In Arabic',
    'full_name_en' => 'Full Name In English',
    'address' => 'Address',
    'contact' => 'Contact Number',
    'register' => 'Register',
    'registered_successfully' => 'Congratulations! You have been registered successfully.',
    'something_went_wrong' => 'Something went wrong. Please try again.',
    'input_error' => 'There were some problems with your input.',
    'next_step'    =>  'Next Step',
    'instagram_id'  =>  'Instagram',
    'profile_image'    =>  'Profile Image',
    
    'tags'               =>  'Tags',
    'register_success' => 'Registration Successful',
    'profile_save_error' => "Something went wrong,coudn't save your profile",
    'user_save_error' => "Something went wrong,coudn't create your profile",
    'login_success'  => 'Logged in successfully',
    'not_verified' => 'Your Profile is not verified',
    'inactive' => 'Your account is inactive',
    'customer' => 'Please login using customer application',
    'logout' => 'Logged out successfully',
    'unauthenticated' => 'Unauthenticated',
    'device_token_updated' => 'Device token updated',
    'password_reset' => 'Please check your email for password reset link',
    'user_not_found' => 'User not found',
    
    'about_us_en'            =>  'About Us Content in English (200 Character Max)',
    'about_us_ar'            =>  'About Us Content in Arabic (200 Character Max)',
    

    // message
    'welcome'  => 'Welcome',
    
    'add'                      =>  'Add',
    'hours'                    =>  'Hours',


    //API
    'login_error'      => 'Invalid user, Please enter valid credentials',
    'login_password'   => 'Please change your account password first to login',
    'login_status'     => 'Account deactivated, Please contact us for further support',
    'reset_password_user'  => 'User not found, Please try to login again',
    'reset_password_success'  => 'Password reset successfully',
    'reset_password_error'  => "Something went wrong,coudn't reset password",
    'forgot_password_user'  => "No account is associated with given phone number",
    'forgot_password_success'  => "Success! Your forgot password link has been sent to your registered mobile number",
    'forgot_password_error'  => "Something went wrong, coudn't reset password",
    'number_is_invalid'  => "Phone number is invalid",
    'old_password_not_matched' => 'Your Old Password not matched',
    'no_forgot_request_found' => 'No any request found for forgot password.',

    //new
    'device_type_error' => 'Device type should either android or iphone',
    'email_already_exists' => 'This email is already registered',
    'you_can_login' => 'You are already registered, Please login with your credentials',
    'registered_verify_otp' => 'You are registered, Please verify your account using OTP sent to your phone number',
    'user_inactive' => 'Sorry!, You can not login',
    'old_password_wrong' => 'Your old password is incorrect',
    'pasword_same_as_current' => 'Please enter a password which is not similar then current password',
    'password_success' => 'Password updated successfully',
    'logout_success' => 'You have logged-out successfully',
    'invalid_otp' => 'Invalid phone number or OTP, Please try again',
    'otp_expired_use_new' => 'OTP code expired, use new OTP, Just sent to your phone number',
    'otp_expired' => 'OTP code expired, You can resend it',
    // 'otp_sent' => 'OTP Sent successfully',
    'user_not_exists' => 'User does not exists with this Phone number',
    'forgot_password_otp_sent' => 'Forgot Password OTP Sent successfully',
    'password_reset_success' => 'Password reset successfully',
    'user_updated' => 'User details updated successfully',
    'language_updated'  =>'your preferred language has been updated',
    'language_not_found'  =>  'Language not found',
    'step_2_success'    =>  'Step 2 Register Successfully',
    'register_successfully'  =>  'Register Successfully',
    'please_update_all_certificates'   =>  'Please Update All Certificates',
    'please_add_atleast_one_working_day'   =>  'Please Add Atleast One Working Day',

    //login page
    'forgot_password' => 'Forgot passsword ?',
    'sign_in_to_start' => 'Sign in to start your session',
    'reset_password' => 'Reset Password',
    'send_reset_pass_link' => 'Send Password Reset Link',

    //register
    'first_name' => 'First Name',
    'last_name' => 'Last Name',
    'error' => 'Error!',
    'input_error' => 'There were some problems with your input.',
    'your_account_is_not_approved' => 'Your account has not been approved by admin.',
    'tags'  =>  'Tags',
    'select_tags'   =>  'Select Tags',

    //api
    'normal_login' => 'Please try to Regular Login with same email/mobile',
    'social_login' => 'Please try to Social Login',
    'mobile_exists' => 'This mobile number already exists. Please try to Login',
    'error_in_otp_short' => 'Error in OTP', 
    'error_in_otp_long' => 'Error in Sending OTP',
    'otp_invalid_long' => 'OTP invalid, please try again',
    'otp_expired_short' => 'OTP Expired',
    'otp_expired_long' => 'OTP has been expired, please resend otp code',
    'otp_not_genrated'  => 'Something went wrong, Otp not generated.Please try again later',
    'number_not_found'  => 'Something went wrong, Invalid Mobile Number Or OTP',
    'otp_wrong_number' => 'The given mobile number is not associated with your account',
    'mobile_verified' => 'Your Mobile number verified successfully',
    'number_active' => 'This mobile number is already verified',
    'otp_sent' => 'OTP sent to your mobile number :number',
    'mobile_unauthenticated' => 'Your mobile is not  verified. Please verify your account using OTP sent to your phone number.',
    'failed_error' => 'Something went wrong,Invalid Credentials',
    'failed_social' => 'You are not having social login',
    'account_blocked' => 'Your account is inactive, please contact :contact for further support',
    'your_account_is_not_active'   => 'your account is not active',
    'logged_in' => 'You have successfully logged in.',
    'mobile_changed' => 'Your mobile number has been updated successfully',
    'otp_sent_email' => 'OTP sent to your email :email',
    'otp_wrong_email' => 'Invalid OTP',
    'email_not_found'  => 'Something went wrong, Invalid Email Or OTP',
    'email_updated' => 'Your email has been updated successfully',
    'otp_sent_error' => 'Could not send OTP. Please Try Again.',
    'social_email_taken' => 'Please login with the registered mobile number using this social signup.',
    'user_not_valid' => 'This user is not valid.',
    'phone_not_verified' => 'Your mobile number is not verified.',    'account_exist_mobile_required' => 'Account already exist. Mobile Number is required.',
    'account_not_exist_mobile_required' => 'Account does not exist. Mobile Number is required.',
    'mobile_number'    =>  'Mobile Number',
    'your_verification_code' => 'Your Verification Code is: :code',
    'agree_terms_conditions' => 'By clicking on Sign up you agree to',
    'firebase_token_not_valid' => 'Firebase token is not valid.'
    

];
