<?php
/**
 * Class minecraftAUTH
 *
 * @description Verifies a users Minecraft login information.
 * @author Jonathan Barrow (halolink44@gmail.com)
 * @package MCSkin
 * @version 1.0
 * @copyright 2016 Jonathan Barrow
 * @link https://github.com/ReduxRedstone/MCSkin
 */
class MCSkin {

	public function change($skinURL, $uuid, $token) {
		
		$payload = "model=\"\"&url=$skinURL";

		$cURLOpts = array("Authorization: Bearer $token","Content-type: application/json",);

		$cURL = curl_init("https://api.mojang.com/user/profile/$uuid/skin");
		curl_setopt($cURL, CURLOPT_HEADER, false);
		curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($cURL, CURLOPT_HTTPHEADER, $cURLOpts);
		curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($cURL, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($cURL, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($cURL, CURLOPT_POST, true);
		curl_setopt($cURL, CURLOPT_POSTFIELDS, $payload);
		$data = curl_exec($cURL);
		//curl_close($cURL);

		$response = json_decode($data, true);
		return $response;
	}
}