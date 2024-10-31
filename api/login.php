<?php
	require "steam/apikey.php";
	require "steam/openid.php";

	$OpenID = new LightOpenID("127.0.0.1");
if (!function_exists('is_session_started')) {
    function is_session_started()
    {
        if (php_sapi_name() !== 'cli') {
            if (version_compare(phpversion(), '5.4.0', '>=')) {
                return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
            } else {
                return session_id() === '' ? FALSE : TRUE;
            }
        }
        return FALSE;
    }
}
if ( is_session_started() === FALSE ) session_start();
	if(!$OpenID->mode){
		
		if(isset($_GET['login'])){
			$OpenID->identity = "http://steamcommunity.com/openid";
			header("Location: ".$OpenID->authUrl()."");
		}
		
		if(!isset($_SESSION['T2SteamAuth'])){
			$login = "<div id=\"login\">Welcome Guest. <br /><br /><br /><a href=\"api/login.php?login\"><img src=\"http://steamcommunity-a.akamaihd.net/public/images/signinthroughsteam/sits_small.png\"/></a></div>";
		}
	}
	elseif($OpenID->mode == "cancel"){
		echo "User has canceled Authentication";
		header("Location: ../index.html");
	}
	else
	{
		if(!isset($_SESSION['T2SteamAuth'])){
            $_SESSION['T2SteamAuth'] = $OpenID->validate() ? $OpenID->identity : null;
			$_SESSION['T2SteamID64'] = str_replace("http://steamcommunity.com/openid/id/", "", $_SESSION['T2SteamAuth']);
			
			if($_SESSION['T2SteamAuth'] != null)
			{
				$Steam64 = $_SESSION['T2SteamID64'];
				$profile = file_get_contents("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key={$api}&steamids={$Steam64}");
				$buffer = fopen("../cache/{$Steam64}.json", "w+");
				fwrite($buffer, $profile);
				fclose($buffer);
			}
			
		}
		header("Location: ../index.html");
	}
	
	if(isset($_GET['logout'])){
		unset($_SESSION['T2SteamAuth']);
		unset($_SESSION['T2SteamID64']);
		unset($_SESSION['steam']);
		header("Location: ../index.html");
	}
	
	if(isset($_SESSION['T2SteamAuth'])){
		$login = "<div id$\"login\"><a href=\"?logout\">Logout</a></div>";
	}

	try {
		if(isset($_SESSION['T2SteamID64'])) {
		$content = file_get_contents("./cache/{$_SESSION['T2SteamID64']}.json");
            $_SESSION['steam'] = json_decode($content, FILE_USE_INCLUDE_PATH);
        }
		else {
            $steam = null;
        }
	}catch (Exception $e) {
		echo 'Exception reçue : '.  $e->getMessage(). "\n";
	}
	
	echo $login;
	
	if(isset($_SESSION['steam']) && $_SESSION['steam'] != null){
       /* foreach($_SESSION['steam']['response']['players'][0] as $child) {
            print_r($_SESSION['steam']['response']['players'][0]);
        }*/
    }

?>