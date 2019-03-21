<?php
include_once 'general_function.php';

global $connection;
session_start();
$serie_id = $_GET['serie_id'];
$id_utente = $_SESSION["user_id"];
$consigliato = $_GET["consigliato"];


$query="update consiglio set consigliato=? where id_serie=? and id_utente=?";
$stmt=executeQuery($query,array(&$consigliato,&$serie_id,&$id_utente),array("sss"));


$host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$extra = 'serie.php';
header("Location: http://$host$uri/$extra?serie_id=".$serie_id);  
    

?>