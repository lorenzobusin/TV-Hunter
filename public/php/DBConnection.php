<?php
/*
  blocco dei parametri di connessione
*/
global $connection;
function connettiDB() {
    // nome di host
    $host = "localhost";
    // nome del database
    $db = "my_tvhunter";
    // username dell'utente in connessione
    $username = "root";
    // password dell'utente
    $password = "";
    global $connection;
    $connection=new mysqli($host,$username,$password,$db);
    
    if($connection->connect_errno){
        $host  = $_SERVER['HTTP_HOST'];
        $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        header("Location: http://$host$uri/404.php");
        return;
    }
    
}
?>
