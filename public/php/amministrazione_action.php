<?php
include_once 'general_function.php';

session_start();
$host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$extra = 'amministrazione.php';

if(!array_key_exists('user_tipo',$_SESSION) && empty($_SESSION['user_tipo']) && $_SESSION['user_tipo']!="admin"){
    header("Location: http://$host$uri/login.php");
    return ;
}

if(!array_key_exists('post_id',$_GET) && empty($_GET['post_id']) && !array_key_exists('segnalazione_tipo',$_GET) && empty($_GET['segnalazione_tipo'])&& !array_key_exists('elimina',$_GET) && empty($_GET['elimina'])){
    header("Location: http://$host$uri/$extra");
    return;
}
$query=" UPDATE segnalazione SET checked=1 where id_ref=? and tipo=?";
$stmt=executeQuery($query,array(&$_GET["post_id"],&$_GET["segnalazione_tipo"]),array("ss"));

if($stmt!=null && $_GET["elimina"]=="true"){
    if($stmt->affected_rows>0){
        $stmt->close();
        $tabella="";
        switch ($_GET["segnalazione_tipo"]) {
            case "post":
                $tabella="post";
                break;
            case "commento":
                $tabella="commento";
                break;
            case "risposta":
                $tabella="risposta";
                break;
              }
        $query=" UPDATE $tabella SET cancellato=1 where id=?";
        $stmt=executeQuery($query,array(&$_GET["post_id"]),array("s"));
        
    }
}

header("Location: http://$host$uri/$extra");

?>