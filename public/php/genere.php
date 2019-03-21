<?php 
    include_once 'template.php';

    if(array_key_exists('spazio_ricerca',$_GET) && !empty($_GET['spazio_ricerca'])){        
         createPage("ricerca");
    }else{
        createPage("genere");
    }
?>