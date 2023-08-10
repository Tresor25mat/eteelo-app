<?php
session_start();
require_once ("connexion.php");
if($_SESSION['user_courrier']['User']==''){
    $token = "dGVzdDE6V2F2ZXNjb20=";
}else{
  $token = base64_encode(stripslashes($_SESSION['user_courrier']['User']).':'.stripslashes($_SESSION['user_courrier']['Pw']));
}
$content = $_POST['message'];
$numbers = htmlentities($_POST['destinataire']);
$sender = Securite::bdd($_POST['expediteur']);
$val1_conversation=rand();
$val2_conversation=rand();
$ID_Conversation=sha1($val1_conversation.$val2_conversation);
$val1_Message=rand();
$val2_Message=rand();
$ID_Message=sha1($val1_Message.$val2_Message);
$val1_Participer=rand();
$val2_Participer=rand();
$ID_Participer=sha1($val1_Participer.$val2_Participer);
$val1_Telephone=rand();
$val2_Telephone=rand();
$ID_Telephone=sha1($val1_Telephone.$val2_Telephone);
$sender = strtr($sender,
	'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ',
	'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
$sender = preg_replace('/([^.a-z0-9]+)/i', '-', $sender);
// $content = iconv("UTF-8", "ISO-8859-15", $content);
$postdata = array(
    'from' => $sender,
    'to' => $numbers,
    'text' => $content
    // 'date' => "2020-01-20 15:45:00"
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://197.242.157.206:8086/sms/single");
curl_setopt($ch, CURLOPT_HEADER, FALSE );
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json', 'Authorization: Basic ' . $token));
$response = curl_exec($ch);
// var_dump($response);
 $err = curl_error($ch);
 curl_close($ch);

 if ($err) {
 	echo "cURL Error #:" . $err;
 } else {
 	$content=Securite::bdd($content);
 	$sel_tel=$pdo->query("SELECT * FROM telephone WHERE Num_Telephone='".$numbers."'");
    $pos = strpos($response, 'smsCount');
    $mystring = substr($response, $pos+10, 1);
    $pos_desc = strpos($response, 'description');
    $mydesc= substr($response, $pos_desc+14, 8);
    if($mydesc=='ESME_ROK'){
        $statut="Delivered";
    }else{
        $statut="Undelivered";
    }
 	if($tel=$sel_tel->fetch()){
 		$insert=$pdo->query("INSERT INTO conversation (ID_Conversation, Date_Envoie, ID_Utilisateur) VALUES ('".$ID_Conversation."', Now(), ".$_SESSION['user_courrier']['ID_Utilisateur'].")");
 		// $insert=$pdo->query("INSERT INTO conversation (ID_Conversation, ID_Utilisateur) VALUES ('".$ID_Conversation."', ".$_SESSION['user_courrier']['ID_Utilisateur'].")");
  		$insert_message=$pdo->query("INSERT INTO message (ID_Message, ID_Conversation, Emetteur, Texte, ID_Utilisateur) VALUES ('".$ID_Message."', '".$ID_Conversation."', '".$sender."', '".$content."', ".$_SESSION['user_courrier']['ID_Utilisateur'].")");

  		$insert_participer=$pdo->query("INSERT INTO participer (ID_Participer, ID_Conversation, ID_Telephone, Reponse, smsCount, Command, ID_Utilisateur, Statut) VALUES ('".$ID_Participer."', '".$ID_Conversation."', '".$tel['ID_Telephone']."', '".$response."',".$mystring.", 1, ".$_SESSION['user_courrier']['ID_Utilisateur'].",'".$statut."')");
 	}else{
  		$insert_telephone=$pdo->query("INSERT INTO telephone (ID_Telephone, Num_Telephone, ID_Utilisateur) VALUES ('".$ID_Telephone."', '".$numbers."', ".$_SESSION['user_courrier']['ID_Utilisateur'].")");
 		$insert=$pdo->query("INSERT INTO conversation (ID_Conversation, Date_Envoie, ID_Utilisateur) VALUES ('".$ID_Conversation."', Now(), ".$_SESSION['user_courrier']['ID_Utilisateur'].")");

  		$insert_message=$pdo->query("INSERT INTO message (ID_Message, ID_Conversation, Emetteur, Texte, ID_Utilisateur) VALUES ('".$ID_Message."', '".$ID_Conversation."', '".$sender."', '".$content."', ".$_SESSION['user_courrier']['ID_Utilisateur'].")");
  		
  		$insert_participer=$pdo->query("INSERT INTO participer (ID_Participer, ID_Conversation, ID_Telephone, Reponse, smsCount, Command, ID_Utilisateur, Statut) VALUES ('".$ID_Participer."', '".$ID_Conversation."', '".$ID_Telephone."', '".$response."',".$mystring.", 1, ".$_SESSION['user_courrier']['ID_Utilisateur'].",'".$statut."')");
 	}
	echo "1";
   // echo $response;
 }

 ?>