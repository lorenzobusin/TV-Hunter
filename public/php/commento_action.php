<?php
include_once 'general_function.php';

session_start();
$host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

if(!array_key_exists('user_id',$_SESSION) && empty($_SESSION['user_id'])) {
    header("Location: http://$host$uri/login.php");
    return ;
}
    
$query="INSERT INTO post (id_serie, id_utente, testo)VALUES (".$_SESSION["serie_id"].", ".$_SESSION['user_id'].", ?);";
$stmt=executeQuery($query,array(&$_GET["post"]),array("s"));

$extra = 'serie.php?serie_id='.$_SESSION["serie_id"];



if($stmt!=null){   
    $stmt->close();
    header("Location: http://$host$uri/$extra");
        return;
    }


header("Location: http://$host$uri/$extra");

?>