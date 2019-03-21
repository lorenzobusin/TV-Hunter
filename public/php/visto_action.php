<?php
include_once 'general_function.php';

global $connection;
session_start();
$id_utente = $_SESSION["user_id"];
$id_episodio = $_GET["id_episodio"];
$serie_id = $_GET['serie_id'];


$query="insert into visto (id_episodio, id_utente) values (?,?)";
$stmt=executeQuery($query,array(&$id_episodio,&$id_utente),array("ss"));

$host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$extra = 'serie.php';
header("Location: http://$host$uri/$extra?serie_id=".$serie_id);   

?>