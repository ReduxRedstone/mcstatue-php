<?php
/**
 * Class Imgur
 *
 * @description Verifies a users Minecraft login information.
 * @author Jonathan Barrow (halolink44@gmail.com)
 * @package Imgur
 * @version 1.0
 * @copyright 2016 Jonathan Barrow
 * @link https://github.com/ReduxRedstone/Imgur-PHP
 */
class Imgur {

	private $keyClient;
	private $keySecret;
	private $cURL;

	public function __construct($keyClient, $keySecret) {
		$this->keyClient = $keyClient;
		$this->keySecret = $keySecret;
		$this->cURL = curl_init();
	}

	public function return() {
		return $this->keyClient." ".$this->keySecret;
	}

	public function uploadImage($imageURL) {
		$imageData = file_get_contents($imageURL);
		curl_setopt($this->cURL, CURLOPT_HEADER, false);
		curl_setopt($this->cURL, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->cURL, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($this->cURL, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($this->cURL, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($this->cURL, CURLOPT_POST, true);
		curl_setopt($this->cURL, CURLOPT_URL, 'https://api.imgur.com/3/image.json');
		curl_setopt($this->cURL, CURLOPT_HTTPHEADER, array('Authorization: Client-ID ' . $this->keyClient));
		curl_setopt($this->cURL, CURLOPT_POSTFIELDS, array('image' => base64_encode($imageData)));
		$response = curl_exec($this->cURL);
		//curl_close($this->cURL);
		return $response;
	}

}