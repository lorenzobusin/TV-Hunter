<?php 
    include_once 'template.php';
    
    if(array_key_exists('user_id',$_SESSION) && !empty($_SESSION['user_id'])){
        createPage("esplora");
    }else{
        createPage("login");
    }
    
    
?>