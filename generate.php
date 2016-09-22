<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Cannot 'GET' page");
}

if (!(isset($_FILES["file"]) OR isset($_POST["username"]) OR isset($_POST["password"]))) {
	echo "Invalid form values!";
	die();
}

ini_set('max_execution_time', 3000);

$img = $_FILES['file']['tmp_name'];
if (file_exists($img)) {
    $dims = getimagesize($img);
    if ($dims === FALSE) {
    	echo "Not an image";
        die();
    } else {
    	list($width, $height) = $dims;
    	if ($width !== 64 OR $height !== 64) {
    		echo "<pre>Not a valid skin file!</pre>";
    		die();
    	}

    	$randName = time();
		mkdir("skins/$randName", 0777, true);

		$extension = explode(".", $_FILES["file"]["name"]);
		$extension = end($extension);

		$newfilename = $randName."-ORIG.".$extension;

		$filename = $_FILES["file"]["name"];
		$filetmp  = $_FILES["file"]["tmp_name"];
		$filetype = $_FILES["file"]["type"];
		$filesize = $_FILES["file"]["size"];

    	move_uploaded_file($filetmp,"skins/$randName/".$newfilename);

    	for ($i=0; $i < 26; $i++) {
			$source = imagecreatefrompng("skins/$randName/".$newfilename);
			$source_width = imagesx($source);
			$source_height = imagesy($source);
			$fn = @sprintf("$randName-%s.png", $i);
			
			imagepng($source, $fn);
			imagedestroy($source);
			rename($fn, "skins/$randName/$fn");
		}
    }
} else {
	echo "Not a file";
    die();
}

$coords = array("~3.90625 ~-2.175 ~-.125","~4.15625 ~-3.175 ~-.125","~3.90625 ~-3.925 ~-.125","~4.15625 ~-4.925 ~-.125","~3.90625 ~-5.675 ~-.125","~4.15625 ~-6.675 ~-.125","~3.65625 ~-7.425 ~-.125","~3.90625 ~-8.425 ~-.125","~4.15625 ~-9.425 ~-.125","~4.40625 ~-10.425 ~-.125","~3.65625 ~-11.175 ~-.125","~3.90625 ~-12.175 ~-.125","~4.15625 ~-13.175 ~-.125","~4.40625 ~-14.175 ~-.125","~3.65625 ~-14.925 ~-.125","~3.90625 ~-15.925 ~-.125","~4.15625 ~-16.925 ~-.125","~4.40625 ~-17.925 ~-.125","~3.90625 ~-18.675 ~","~4.15625 ~-19.675 ~","~3.90625 ~-20.675 ~-.25","~4.15625 ~-21.675 ~-.25","~3.90625 ~-22.425 ~","~4.15625 ~-23.425 ~","~3.90625 ~-24.425 ~-.25","~4.15625 ~-25.425 ~-.25");

$comm = "summon FallingSand ~ ~1 ~ {Time:1,Block:redstone_block,Passengers:[{id:FallingSand,Time:1,Block:activator_rail,Passengers:[";

include 'classes/imgur.class.php';
include 'classes/mclogin.class.php';
include 'classes/skinupload.class.php';

$imgurClientKey = "";
$imgurSecretKey = "";

$imgur = new Imgur($imgurClientKey, $imgurSecretKey);
$skin = new MCSkin();
$auth = new minecraftAUTH();

$client = $auth->login($_POST["username"], $_POST["password"]);
if (!isset($client["userData"]["uuid"])) {
	echo "<pre>Invalid Login</pre>";
	rmdir("skins/$randName");
	die();
}
$uuid = $client["userData"]["uuid"];
$uuid_full = $client["userData"]["uuid_full"];
$accessToken = $client["userData"]["accessToken"];

for ($i=0; $i < 26; $i++) {
	if ($i == 0) {
		$comm_block = "command_block";
	} else {
		$comm_block = "chain_command_block";
	}

	/*
	for ($rc=0; $rc < 26; $rc++) {
		$image = imagecreatetruecolor(64, 64);
		for($h = 1; $h <= 64; $h++) {
		    for($w = 1; $w <= 64; $w++) {
		        $r = mt_rand(0,255);
		        $g = mt_rand(0,255);
		        $b = mt_rand(0,255);
		        $c = imagecolorallocate ($image, $r , $g, $b);
		        imagesetpixel($image,$w - 1 , $h - 1, $c);
		    }
		}

		imagealphablending($image, true);
		imagesavealpha($image, true);

		imagefill($image,0,0,0x7fff0000);

		imagepng($image-$rc, "img.png");
		imagedestroy($image);
		rename("img-$rc.png", "skins/$randName/img-$rc.png");
	}

	$image = $imgur->uploadImage("skins/$randName/img-$i.png");
	#^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^#
	#    Use this to generate random skins    #
	#^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^#

	*/

	###########################################
	#    NEED TO ADD FUNCTIONS TO CUT SKIN    #
	###########################################
	
	$image = $imgur->uploadImage("skins/$randName/$randName-$i.png");
	$image = json_decode($image, true);

	$skinURL = $image["data"]["link"];
	$changed = $skin->change($skinURL, $uuid, $accessToken);

	if ($changed) {
		echo "<pre>".print_r($changed, true)."</pre>";
		rmdir("skins/$randName");
		die();
	}

	$cURLOpts = array("Content-type: application/json");

	$cURL = curl_init("https://sessionserver.mojang.com/session/minecraft/profile/$uuid");
	curl_setopt($cURL, CURLOPT_HEADER, false);
	curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($cURL, CURLOPT_HTTPHEADER, $cURLOpts);
	curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($cURL, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($cURL, CURLOPT_FOLLOWLOCATION, 1);
	$data = curl_exec($cURL);
	curl_close($cURL);

	$session = json_decode($data, true);

	if (isset($session["error"])) {
		echo "<pre>".$session["errorMessage"]."</pre>";
		rmdir("skins/$randName");
		die();
	}

	$texture = $session["properties"][0]["value"];

	$comm .= "{id:MinecartCommandBlock,Command:setblock ~2 ~$i ~ $comm_block 1 replace {auto:1,Command:/summon ArmorStand ".$coords[$i]." {Pose:{RightArm:[-45f,45f,0f]},HandItems:[{id:skull,Count:1,Damage:3,tag:{SkullOwner:{Id:$uuid_full,Properties:{textures:[{Value:$texture}]}}}}],NoGravity:1,Invisible:1}}},";

	#Can only get player sessions once per minute. If multiple accounts are integrated into the upload, then upload could be divvied up the lower the time needed#
	sleep(60);
}

$comm .= "{id:MinecartCommandBlock,Command:setblock ~ ~-2 ~ command_block 0 replace {auto:1,Command:fill ~ ~ ~ ~ ~3 ~ air}},{id:MinecartCommandBlock,Command:kill @e[r=0]}]}]}";

echo $comm;
