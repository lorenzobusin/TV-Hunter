<?php
include_once 'general_function.php';

global $connection;
session_start();
$serie_id = $_GET['serie_id'];
$id_utente = $_SESSION["user_id"];

$query="insert into preferiti (id_serie, id_utente) values (?,?)";
$stmt=executeQuery($query,array(&$serie_id,&$id_utente),array("ss"));

$host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$extra = 'serie.php';
header("Location: http://$host$uri/$extra?serie_id=".$serie_id);

    

?>