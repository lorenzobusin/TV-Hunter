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
    
$post_id = $_GET['post_id'];
$id_utente = $_SESSION["user_id"];

$query="insert into segnalazione (id_ref, id_utente,tipo) values (?,$id_utente,1)";
$stmt=executeQuery($query,array(&$post_id),array("s"));

$extra = 'serie.php';
header("Location: http://$host$uri/$extra?serie_id=".$_SESSION["serie_id"]);



?>