<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    protected function rules()
    {
        // If you have further fields and rules you can add in following array.
        return [
            'token' => 'required',
            'email' => 'required|email|max:99',
            'password' => 'required|confirmed|digits_between:8,14',
        ];
    }

    // protected function validationErrorMessages()
    // {
    //     return [
    //     'password.regex'  => 'Password Must Contain Upper-case, Lower-case, Number and Special characters Like (~!@#$%^&*()_+=-?.',
    //     ];
    // }

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;
}
