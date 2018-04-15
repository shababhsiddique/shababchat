<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use View;
use App\User;
use Session;
use Redirect;

class HomeController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Show open threads
     * @return type
     */
    public function index() {

        $userId = \Auth::user()->id;
        View::share('userId', $userId);

        $users = User::where('id','!=', $userId)->get();
        
        //return view
        return view('messenger.messages')->with("users",$users);
    }

    /**
     * Get into a thread , view messages in thread and write (like chat)
     * @param type $code
     * @param type $sender
     * @param type $receiver
     * @return type
     */
    public function viewMessage($code, $sender, $receiver) {

        $userId = \Auth::user()->id;
        $users = User::where('id','!=', $userId)->get();

        $loggedId = \Auth::user()->id;

        if ($sender == $loggedId) {
            $other = $receiver;
        } else {
            $other = $sender;
        }

        $otherPartyUser = User::find($other);




        $party1 = min($sender, $receiver);
        $party2 = max($sender, $receiver);

        $generatedCode = md5("$party1###$party2");

        if ($generatedCode != $code) {
            //This is not the right url
            Session::put('message', array(
                'title' => 'Error',
                'body' => 'Something wrong with the link you tried to access',
                'type' => 'danger'
            ));

            return Redirect::to('/home');
        }


        View::share("user_logged", $loggedId);
        View::share("user_logged_name", \Auth::user()->name);

        view::share("user_other", $other);
        view::share("user_other_name", $otherPartyUser->name);
        View::share('thread', $code);


        //return view
        return view('messenger.writemessage')->with("users",$users);
    }

   

}
