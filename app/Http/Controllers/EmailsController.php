<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailsController extends Controller
{
    //
    public function sendWelcomeMail()
    {
        // $user = User::find(1);
        // Mail::to($user->email)->send(new WelcomeMail($user));
        Mail::to('recipent@example.com')->send(new WelcomeMail());
        return 'Email sent successfully';
    }
}
