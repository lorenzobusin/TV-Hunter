<?php
include_once 'general_function.php';

global $connection;
session_start();

$host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

if(!array_key_exists('user_id',$_SESSION) && empty($_SESSION['user_id'])){
    header("Location: http://$host$uri/login.php");
    return;
}
$id_utente = $_SESSION["user_id"];

$query="update messaggi set risposta=?, admin_id=$id_utente where id=?";
$stmt=executeQuery($query,array(&$_GET["messaggio"],&$_GET["messaggio_id"]),array("ss"));


$extra = 'amministrazione.php';

header("Location: http://$host$uri/$extra");

?>