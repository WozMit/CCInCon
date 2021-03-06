<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App;

class CoordinadorLoginController extends Controller
{
  public function __construct()
  {
    $this->middleware('guest:coordinador', ['except' => ['logout']]);
  }
  public function showLoginForm()
  {
    return view('auth.coordinador-login');
  }

  public function login(Request $request)
  {
  // Validate the form data
    $this->validate($request, [
      'email'   => 'required|email',
      'password' => 'required|min:3'
    ]);

    // Attempt to log the user in
    if (Auth::guard('coordinador')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
      // if successful, then redirect to their intended location
      return redirect()->intended(route('coordinadores.index'));
    }

    // if unsuccessful, then redirect back to the login with the form data
    return redirect()->back()->withInput($request->only('email', 'remember'));
  }
}
