<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use GuzzleHttp\Client;
use App\Post;
use Auth;
use Mail;
use Session;
use Purifier;

class PagesController extends Controller
 {

	public function __construct(Request $request) {
        $this->middleware(function ($request, $next) {
            session(['zone' => 'Blog']);
            return $next($request);
        });
	}

	public function getIndex() {
		$posts = Post::orderBy('created_at', 'desc')->where('status', '>=', '4')->paginate(4);
		if ($posts) {

		} else {
			Session::flash('failure', 'No blog Posts were found.');
		}
		return view('pages.welcome', ['posts' => $posts]);
	}

	public function getAbout() {
		$data = [];
		$data['name']  = env('APP_OWNER');
		$data['email'] = env('APP_EMAIL');

		return view('pages.about')->with('data', $data);
	}

	public function getContact() {
		$data = [];
		$data['name']  = env('APP_OWNER');
		$data['email'] = env('APP_EMAIL');		
		return view('pages.contact')->with('data', $data);
	}

	public function postContact(Request $request) {
		$this->validate($request, [
			'name' 		=> 'required|min:3|max:191',
			'email' 	=> 'required|email|min:5|max:191',
			'subject'	=> 'required|min:3|max:191',
			'message' 	=> 'required|min:8|max:2048',
		]);

        // We protect this public Form with a Captcha which protects us from Bots etc.
        // Google recaptcha account credentials were stored as ENV values. 
        $token = $request->input('g-recaptcha-response');
        if ($token) {
            $client = new Client();
            $response = $client->post(env('CAPTCHA_SERVER'), [
                'form_params' => [
                    'secret' => env('CAPTCHA_SECRET'),
                    'response' => $token
                ]
            ]);
            $results = json_decode($response->getBody()->getContents());
            $myrc = $results->success;
        } else { $myrc = false; }
        if (!$myrc) {
            Session::flash('failure', "You're probably not human!");
            return Redirect::back()->withInput();
        }

		$data = [
			'name' 			=> $request->name,
			'email' 		=> $request->email,
			'subject'		=> $request->subject,
			'bodyMessage'	=> Purifier::clean($request->message),
		];

		$myrc = Mail::send('emails.contact', $data, function($message) use ($data) {
			$message->from($data['email']);
			$message->to('nswa002@btinternet.com');
			$message->subject($data['subject']);
		});

		if (!$myrc) {
			Session::flash('success', 'Your eMail was successfully sent.');
			return redirect()->route('home');
		} else {
			Session::flash('failure', 'The eMail was NOT sent.');
            return Redirect::back()->withInput();
		}
	}

}
