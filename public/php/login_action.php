<?php
include_once 'general_function.php';

$flag = 0;
$username=$_POST['username'];
$password=hash("sha256",$_POST['password']);

$username = test_input($username);
if (!preg_match("/^[a-zA-Z0-9]*$/",$username)) {
    $flag = 1;
}

session_start();

if($flag==0){
    $query="select * from utente where username=? and password=?";
    $stmt=executeQuery($query,array(&$username,&$password),array("ss"));

    $host  = $_SERVER['HTTP_HOST'];
    $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $extra = 'esplora.php';

    if($stmt!=null){   
        $result=$stmt->get_result();
        $stmt->close();
        if($result->num_rows==1){
            //Inizializzo la sessione
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $_SESSION['user_id']=$row['id'];
            $_SESSION['user_username']=$row['username'];
            $_SESSION['user_tipo']=$row['tipo'];
            
            /* Redirect to a different page in the current directory that was requested */
            header("Location: http://$host$uri/$extra");
            return;
        }
    }
}

$_SESSION['errore_login']="Execute failed";

header("Location: http://$host$uri/login.php");

?>