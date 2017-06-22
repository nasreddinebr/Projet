<?php
namespace BlogEcrivain\bin;

class StopReturn {
	
	public function stopRepetitiveReturn() {
		
		/**
		* Stop the repetitive return of a form
		*/
			
		// Recuperate the current URL
		$curentURL = $_SERVER['REQUEST_URI'] ;
			
		// Browser redirection to url retrieved in variable $curentURL
		header('Location: ' . $curentURL);
		exit; //Finished the current script
	}
}

