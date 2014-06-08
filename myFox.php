<?php

// Cyril E      http://www.ituilerie.com
// Pour connaitre l'etat de l'alarme   http://xxxxx/myFox.php
// Pour changer l'etat de l'alarme    http://xxxxx/myFox.php?levelrequest=armed          partial   ou   disarmed


$password='xxxxxx';    // Mot de passe du compte
$username='xxxxxx';      // Non d'utilisateur (mail)


$client_id = 'xxxxx';         // Client ID, s'incrire a l'API
$client_secret = 'xxxxxx';   // Client secret

$siteid = 0 ;  // Site ID si vous l'avez deja, sinon laisser 0

if (isset($_GET['levelrequest']))            // Niveau de securité demandé   armed   partial   disarmed
{
    $levelrequest=$_GET['levelrequest'];
}


// Authentification
$curl = curl_init( 'https://api.myfox.me/oauth2/token' );
curl_setopt( $curl, CURLOPT_POST, true );
curl_setopt( $curl, CURLOPT_POSTFIELDS, array(
    		'grant_type' => 'password',
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'username' => $username,
    		'password' => $password
) );
curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1);
$auth = curl_exec( $curl );
$secret = json_decode($auth);
$token = $secret->access_token;


// Obtention du site ID
if ($siteid==0){
$api_url = "https://api.myfox.me:443/v2/client/site/items?access_token=" . $token;
$requete = @file_get_contents($api_url);
$json_result = json_decode($requete,true);
$siteid = $json_result["payload"]["items"][0]["siteId"];    // On prend en compte le premier site de la liste, si plusieur Sites remplacer la valeur 0 par 1 2 3.....(non testé), le site ID peut etre trouvé dans votre compte, sa valeur ne change pas
}


// Obtenir Etat de l'alarme
$api_url2 = "https://api.myfox.me:443/v2/site/" .$siteid. "/security?access_token=" . $token;
$requete2 = @file_get_contents($api_url2);
$json_result2 = json_decode($requete2,true);
$statusvalue = $json_result2["payload"]["status"];
$statuslabel = $json_result2["payload"]["statusLabel"];



// Lecture de la sonde de temperature

//Obtention de l'ID de l'appareil
$api_url4 = "https://api.myfox.me:443/v2/site/" .$siteid. "/device/data/temperature/items?access_token=" . $token;
$requete4 = @file_get_contents($api_url4);
$json_result4 = json_decode($requete4,true);
$deviceId = $json_result4["payload"]["items"][0]["deviceId"];    // si vous avez plusieurs thermometre il faudra remplace le 0 par la valeur
$deviceName = $json_result4["payload"]["items"][0]["label"];   // permet d'obtenir le non de l'appareil 

//Obtention de la derniere valeur

date_default_timezone_set('GMT');
$date = date("Y-m-d");
$timestamp = time();
$TimeEnd = date("H:i:s", $timestamp);
$TimeStart = date("H:i:s", $timestamp-3600);

$explosion = explode(':', $TimeEnd);
$HeureEnd = $explosion[0];
$MinutesEnd = $explosion[1];

$explosion = explode(':', $TimeStart);
$HeureStart = $explosion[0];
$MinutesStart = $explosion[1];

$api_url5 = "https://api.myfox.me:443/v2/site/" .$siteid. "/device/" .$deviceId. "/data/temperature?dateFrom=" .$date. "T" .$HeureStart."%3A".$MinutesStart."%3A00Z&dateTo=" .$date. "T" .$HeureEnd. "%3A".$MinutesEnd."%3A00Z&access_token=" . $token;
$requete5 = @file_get_contents($api_url5);
$json_result5 = json_decode($requete5,true);
$tempValue = $json_result5["payload"]["items"][0]["celsius"];


echo '<?xml version="1.0" encoding="utf8" ?>';
echo "<myfox>";
echo "<statusvalue>" . $statusvalue  . "</statusvalue>";
echo "<statuslabel>" . $statuslabel  . "</statuslabel>";
echo "<temperature>" . $tempValue  . "</temperature>";
echo "</myfox>";


// Changer l'etat de l'alarme
if (isset($_GET['levelrequest']))
{
$api_url3 = "https://api.myfox.me:443/v2/site/" .$siteid. "/security/set/".$levelrequest."?access_token=" . $token;
$curl2 = curl_init( $api_url3 );
curl_setopt( $curl2, CURLOPT_POST, true );
curl_setopt( $curl2, CURLOPT_RETURNTRANSFER, 1);
$return = curl_exec( $curl2 );
}
?>