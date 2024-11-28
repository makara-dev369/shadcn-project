<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function login()
    {
        return Inertia::render('Auth/Login');
    }


    public function storeLogin(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];
        
        // Login valided
        if(Auth::attempt($credentials)){
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'Provided credentials do not match.',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function signup()
    {
        return Inertia::render('Auth/Signup');
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeSignup(Request $request)
    {
        $validator  = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            
        ]);

        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name'=> $request->name,
            'email'=> $request->email,
            'password'=> $request->password
        ]);

         return redirect()->route('login');
    }

    public function logout(Request $request){

        Auth::logout();
 
        $request->session()->invalidate();
     
        return redirect()->route('login');
    }

   
}
