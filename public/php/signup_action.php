<?php
include_once 'general_function.php';

$username=$_GET['username'];
$password=hash("sha256",$_GET['password']);
$email=$_GET['email'];
$nome=$_GET['nome'];
$cognome=$_GET['cognome'];
$datanascita=$_GET['datanascita'];
$flag = 0;
if (!(preg_match("/[0-9]{4}-[0-9]{2}-[0-9]{2}/",$datanascita) || preg_match("/[0-9]{2}-[0-9]{2}-[0-9]{4}/",$datanascita))) {
    $flag = 6;
}


$cognome = test_input($cognome);
if (!preg_match("/^[a-zA-Z0-9]*$/",$cognome)) {
    $flag = 5;
}

$nome = test_input($nome);
if (!preg_match("/^[a-zA-Z0-9]*$/",$nome)) {
    $flag = 4;
}

$email = test_input($email);
if (!preg_match("/[a-zA-Z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/",$email)) {
    $flag = 3;
}

$password = test_input($password);
if (!preg_match("/^[a-zA-Z0-9]*$/",$password)) {
    $flag = 2;
}

$username = test_input($username);
if (!preg_match("/^[a-zA-Z0-9]*$/",$username)) {
    $flag = 1;
}

session_start();
$datanascita= date("Y-m-d",strtotime($datanascita));

echo " ".$datanascita;
if($flag==0){
    $query="insert into utente (username,password,email,nome,cognome,data_nascita) values (?,?,?,?,?,?)";
    $stmt=executeQuery($query,array(&$username,&$password,&$email,&$nome,&$cognome,&$datanascita),array("ssssss"));
    if($stmt->errno===0){     
        $stmt->close();
        $query="select id,tipo from utente where username=?";
        echo $username;
        $stmt=executeQuery($query,array(&$username),array("s"));
        $result=$stmt->get_result();
        $stmt->close();
        if($result->num_rows==1){
            //Inizializzo la sessione
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $_SESSION['user_id']=$row['id'];
            $_SESSION['user_username']=$username;
            $_SESSION['user_tipo']=$row['tipo'];
            
        }      
        $host  = $_SERVER['HTTP_HOST'];
        $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        $extra = 'esplora.php';
        header("Location: http://$host$uri/$extra");
        }else{
            $_SESSION['errore_signup']="Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            $host  = $_SERVER['HTTP_HOST'];
            $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
            $extra = 'signup.php';
            
            header("Location: http://$host$uri/$extra");
        }
}
else{

    $host  = $_SERVER['HTTP_HOST'];
    $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $extra = 'signup.php';

    switch($flag){
        case '1':{
            $_SESSION['errore_signup']='1';
            header("Location: http://$host$uri/$extra");
        }
        break;

        case '2':{
            $_SESSION['errore_signup']='2';
            header("Location: http://$host$uri/$extra");
        }
        break;

        case '3':{
            $_SESSION['errore_signup']='3';
            header("Location: http://$host$uri/$extra");
        }
        break;

        case '4':{
            $_SESSION['errore_signup']='4';
            header("Location: http://$host$uri/$extra");
        }
        break;

        case '5':{
            $_SESSION['errore_signup']='5';
            header("Location: http://$host$uri/$extra");
        }
        break;

        case '6':{
            $_SESSION['errore_signup']='6';
            header("Location: http://$host$uri/$extra");
        }
        break;

        default:
        break;
    }
} 

?>