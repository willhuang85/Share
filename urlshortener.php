<?php

/* Credit goes to http://www.phpriot.com/articles/google-url-shorening-api
 * By Quentin Zervaas
 */
class ShortenURL {
	
	private $response;
	
	function __construct($longURL) {
		$ch = curl_init(
            sprintf('%s/url?key=%s', 'AIzaSyChIyp1nuI0_ZBF_KgmzQRrg4ECEYnweTE', 'https://www.googleapis.com/urlshortener/v1')
        );
		
		$requestData = array('longUrl' => $longUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
		$result = curl_exec($ch);
        curl_close($ch);
 
        $this->response = json_decode($result, true);
	}
	
	function getShortenURL() {
		return $this->response;
	}
}
?>