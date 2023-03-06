<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use \App\Models\User;

class AuthorLoginForm extends Component
{

    public $login_id, $password;

    public function LoginHandler()
    {

        $fieldType = filter_var($this->login_id, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        if ($fieldType == 'email') {
            $this->validate([
                'login_id' => 'required|email|exists:users,email',
                'password' => 'required|min:5',
            ], [
                'login_id.required' => 'Email or Username is required',
                'login_id.email' => 'Invalid Email Address',
                'login_id.exists' => 'Email not registered',
                'password.required' => 'Password required',
            ]);
        } else {
            $this->validate([
                'login_id' => 'required|exists:users,username',
                'password' => 'required|min:5',
            ], [
                'login_id.required' => 'Email or Username is required',
                'login_id.exists' => 'Username not registered',
                'password.required' => 'Password required',
            ]);
        }

        $creds = array($fieldType => $this->login_id, 'password' => $this->password);
        if (Auth::guard('web')->attempt($creds)) {
            $checkUser = User::where($fieldType, $this->login_id)->first();
            if ($checkUser->blocked == 1) {
                Auth::guard()->logout();
                return redirect()->route('author.login')->with('fail', 'You Account had Blocked');
            } else {
                return redirect()->route('author.home');
            }
        } else {
            session()->flash('fail', 'Incorrect Email/Username or Password');
        }


        // $this->validate([
        //     'email' => 'required|email|exists:users,email',
        //     'password' => 'required|min:5'
        // ], [
        //     'email.required' => 'Enter Your Email Addres',
        //     'email.email' => 'Invalid Email Addres',
        //     'email.exists' => 'This Email not registered in database',
        //     'password.required' => 'Enter your Password',
        // ]);
        // $creds = array('email' => $this->email, 'password' => $this->password);

        // if (Auth::guard('web')->attempt($creds)) {
        //     $checkUser = User::where('email', $this->email)->first();
        //     if ($checkUser->blocked == 1) {
        //         Auth::guard('web')->logout();
        //         return redirect()->route('author.login')->with('fail', 'you account had blocked');
        //     } else {
        //         return redirect()->route('author.home');
        //     }
        // } else {
        //     session()->flash('fail', 'incorrect email or password');
        // }
    }
    public function render()
    {
        return view('livewire.author-login-form');
    }
}
