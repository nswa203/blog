<?php

namespace App\Http\Controllers;

class PagesController extends Controller {
	
	public function getIndex() {
		return view('pages.welcome');
	} 
	
	public function getAbout() {
		$first=	'Nick';
		$last=	'Svonja';
		$email=	'nswa203@btinternet.com';
		$data=[];
		$data['fullname']=$first.' '.$last;
		$data['email']=$email;
		
		return view('pages.about')->with('data',$data);
	} 

	public function getContact() {
		return view('pages.contact');
	} 

	public function postContact() {
		
	} 
	
	
}
