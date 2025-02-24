<?php
session_start();
//var_dump($_SESSION);
include 'models/config.php';
include 'classes/User.php';

if(get('action') == 'login') {

  $params = array(
    'client_id' => OAUTH2_CLIENT_ID,
    'redirect_uri' => 'http://spiritus-tech.mon.world/login.php',
    'response_type' => 'code',
    'scope' => 'identify email guilds'
  );

  // Redirect the user to Discord's authorization page
  header('Location: https://discordapp.com/api/oauth2/authorize' . '?' . http_build_query($params));
  die();
}

if(get('code')) {

  // Exchange the auth code for a token
  $token = apiRequest($tokenURL, array(
    "grant_type" => "authorization_code",
    'client_id' => OAUTH2_CLIENT_ID,
    'client_secret' => OAUTH2_CLIENT_SECRET,
    'redirect_uri' => 'http://spiritus-tech.mon.world/login.php',
    'code' => get('code')
  ));
  $logout_token = $token->access_token;
  $_SESSION['access_token'] = $token->access_token;


  header('Location: ' . $_SERVER['PHP_SELF']);
}

if(session('access_token')) {
    $user = apiRequest($apiURLBase);

    $User = new User();
    $User->setLogin($user->username);
    $User->setDiscord_id($user->id);
    $User->setMail($user->email);

    if($User->verifyDiscord()) {
        error_log("verify OK", 0);
        if($User->discordSubscribe()) {

            $_SESSION["user_name"] = $user->username;
            $_SESSION["user_logged"] = true;
            $_SESSION["user"] = $user->id;
            $_SESSION["avatar"] = "https://cdn.discordapp.com/avatars/".$user->id."/".$user->avatar.".png?size=64";
            
        }
    } else {
        
        $_SESSION["user_name"] = $user->username;
        $_SESSION["user_logged"] = true;
        $_SESSION["user"] = $user->id;
       $_SESSION["avatar"] = "https://cdn.discordapp.com/avatars/".$user->id."/".$user->avatar.".png?size=64";
        header('Location: http://spiritus-tech.mon.world/');
    }

    header('Location: http://spiritus-tech.mon.world/');

}

if(isset($_SESSION["admin"])) {
    header("Location: http://spiritus-tech.mon.world/admin.php");
}

?>


<!doctype html>
<html lang="en">
    <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Spiritus</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"/>
        <link href="https://fonts.googleapis.com/css?family=VT323&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="public/css/style.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="public/css/mediaQueries.css" type="text/css" media="screen" />
        <meta name="google-site-verification" content="DD1z6A8wvXxJ7T6FTogi28NYD76suAaQeK15MDx-UqE" />

    </head>
    
    <body>
                <?php include "includes/nav.php"?>
        
        <section class="center" style="margin-top:80px">
        <h2>Une erreur s'est produite lors de la connection</h2>

        <h3>Vous pouvez réessayer en cliquant <a href="http://spiritus-tech.mon.world/login.php?action=login"> ici </a> ou rejoindre le <a href="https://discord.gg/TC7Qjfs">serveur support.</a></h3>
        </section>
        
        <script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    </body>
</html>