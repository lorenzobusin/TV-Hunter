
<?php
include_once 'general_function.php';

//get the q parameter from URL
$q=$_GET["q"];

//lookup all links from the xml file if length of q>0
$hint="";

if (strlen($q)>0) {    
    
    $host  = $_SERVER['HTTP_HOST'];
    $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $extra = 'serie.php?serie_id=';
    
    $query="select id,miniatura,titolo, consigliato from serie where titolo like concat('%',?,'%')";
    //Seleziono la lista dei generi già in una tabella
    $stmt=executeQuery($query,array(&$q),array("s"));
    $series=resultQueryToTable($stmt->get_result());
    $stmt->close();
    
    $genere_page = implode("",file("../txt/genere.txt"));
    $show_page = implode("",file("../txt/show.txt"));
    
    $genere_show_collect="<!-- Successivo -->";
            
    //Scrivo le serie tv
    $show="<!-- Successiva -->";
    
    /*if (count($series)>1) {
     $show=$show_page;
     }*/
    
    foreach ($series as $serie) {
        $hint="1";
        $show=preg_replace("/<!-- Successiva -->/i",$show_page." <!-- Successiva -->" , $show );
        $show=preg_replace("/<!-- Immagine -->/i",$serie["miniatura"] , $show );
        $show=preg_replace("/<!-- Id -->/i",$serie["id"] , $show );
        $show=preg_replace("/<!-- Titolo -->/i",$serie["titolo"] , $show );
        $show=preg_replace("/<!-- Consigliato -->/i",$serie["consigliato"] , $show );
        $show=preg_replace("/<!-- Url_Show -->/i","http://$host$uri/$extra".$serie["id"] , $show );
    }
    
    $show=preg_replace("/<!-- Successiva -->/i","" , $show );
    
    //Inserisco le serie nel div del genere e completo il div con le informazioni mancanti
    $genere_show_collect=preg_replace("/<!-- Successivo -->/i",$genere_page , $genere_show_collect );
    $genere_show_collect=preg_replace("/<!-- Genere -->/i","Risultati ricerca" , $genere_show_collect );
    $genere_show_collect=preg_replace("/<!-- Genere_Titolo -->/i","Risultati ricerca" , $genere_show_collect );
    $genere_show_collect=preg_replace("/<!-- Show -->/i",$show , $genere_show_collect );
    
}
    


// Set output to "no suggestion" if no hint was found
// or to the correct values
if ($hint=="") {
  $response="Nessuna serie rispetta i parametri di ricerca";
} else {
    $response=$genere_show_collect;
}

//output the response
echo $response;
?> 