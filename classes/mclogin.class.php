<?php
/**
 * Class minecraftAUTH
 *
 * @description Verifies a users Minecraft login information.
 * @author Jonathan Barrow (halolink44@gmail.com)
 * @package minecraftAUTH
 * @version 1.0
 * @copyright 2016 Jonathan Barrow
 * @link https://github.com/ReduxRedstone/minecraftAUTH-PHP
 */
class minecraftAUTH {

	const auth     = "https://authserver.mojang.com/authenticate";
	const hasPaid  = "https://api.mojang.com/profiles/minecraft";
	const authCode = "fd070c0f27474121bc467ed93f47db9e";

	public function login($username="", $password="") {
		$username = trim($username);
		$password = trim($password);
		if (empty($username) OR empty($password)) {
			return 1; /*Blank username or password*/
		} else {
			$payload = array(
				"agent"=>array(
						"name"=>"Minecraft",
						"version"=>1
					),
				"username"=>$username,
				"password"=>$password,
				"clientToken"=>self::authCode
			);
			$payload = json_encode($payload);

			$cURLOpts = array("Content-type: application/json");

			$cURL = curl_init(self::auth);
			curl_setopt($cURL, CURLOPT_HEADER, false);
			curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($cURL, CURLOPT_HTTPHEADER, $cURLOpts);
			curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($cURL, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($cURL, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($cURL, CURLOPT_POST, true);
			curl_setopt($cURL, CURLOPT_POSTFIELDS, $payload);
			$data = curl_exec($cURL);
			curl_close($cURL);

			$response = json_decode($data, true);
			if (isset($response["error"])) {
				return 2; /*Unexpected error*/
				/*return $response["errorMessage"]; //USE THIS TO CAPTURE THE REAL ERROR*/
			} else {
				$accessToken = $response["accessToken"];
				$clientToken = $response["clientToken"];
				$playerName  = $response["selectedProfile"]["name"];

				$payload = json_encode(array($playerName));

				$cURLOpts = array("Content-type: application/json");

				$cURL = curl_init(self::hasPaid);
				curl_setopt($cURL, CURLOPT_HEADER, false);
				curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($cURL, CURLOPT_HTTPHEADER, $cURLOpts);
				curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($cURL, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($cURL, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($cURL, CURLOPT_POST, true);
				curl_setopt($cURL, CURLOPT_POSTFIELDS, $payload);
				$data = curl_exec($cURL);
				curl_close($cURL);

				$response = json_decode($data, true);

				if (isset($response["demo"])) {
					return 3; /*Account is demo, has not paid*/
				} else {
					if (isset($response[0]["id"], $response[0]["name"])) {
						$uuid      = $response[0]["id"];
		    			$uuid_full = substr_replace(substr_replace(substr_replace(substr_replace($uuid, '-', 8, 0), '-', 13, 0), '-', 18, 0), '-', 23, 0);
		    			$user      = $response[0]["name"];

		    			return array("loggedIn"=>1,"userData"=>array("username"=>$user,"uuid"=>$uuid,"uuid_full"=>$uuid_full,"accessToken"=>$accessToken,"clientToken"=>$clientToken));
					} else {
						return 4; /*Unknown error. Not even sure if this will ever be an issue, but its here anyway!*/
					}
				}
			}
		}
	}
}