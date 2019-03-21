<?php 
include_once 'DBConnection.php';
    /*Utilizzato per le query con output esterni*/
    function executeQuery($query, $parameters, $type ){
        
        global $connection;
        if(empty($connection))
            connettiDB();
            
        if ($stmt = $connection->prepare($query)) {
    
            $params=array_merge($type, $parameters);
            // Bind a variable to the parameter as a string.
            call_user_func_array(array($stmt, "bind_param"), $params);            
            // Execute the statement.
            $stmt->execute();
             
            if($stmt) return $stmt;
            }
            return null;
        }
                   
    /*Ritorna il risultato di una query come tabella con la prima riga = all'intestazione delle colonne*/
    function resultQueryToTable($result){
        
        $table = array();
                
        while ($row = mysqli_fetch_assoc($result)) {
            $table[] = $row;
        }
        
        return $table;
    }

    function test_input($data) {
      $data = trim($data);
      $data = stripslashes($data);
      $data = htmlspecialchars($data);
      return $data;
    }   


    function printNavbar($currentPage){  
        $nav = implode("",file("../txt/nav.txt"));
        
        if(array_key_exists('user_tipo',$_SESSION) && !empty($_SESSION['user_tipo']) && $_SESSION['user_tipo']=="admin"){
            $nav = preg_replace("/<!-- Amministrazione -->/i", '<li class="nav__impostazioni refAmministrazione"><a href="amministrazione.php" title="Amministrazione">Amministrazione</a></li>', $nav);
        }
            
        
        switch ($currentPage) {
            case "esplora":
                $nav = preg_replace("/refEsplora/i", "selezionato", $nav);
                break;

            case "profilo":
                $nav = preg_replace("/refProfilo/i", "selezionato", $nav);
                break;

            case "preferiti":
                $nav = preg_replace("/refPreferiti/i", "selezionato", $nav);
                break;
                
            case "faq":
                $nav = preg_replace("/refFaq/i", "selezionato", $nav);
                break;

            case "supporto":
                $nav = preg_replace("/refSupporto/i", "selezionato", $nav);
                break;

            case "privacy":
                $nav = preg_replace("/refPrivacy/i", "selezionato", $nav);
                break;

            case "about":
                $nav = preg_replace("/refAbout/i", "selezionato", $nav);
                break;

            case "home":
                $nav = preg_replace("/refHome/i", "#", $nav);
                break;
            
            case "amministrazione":
                $nav = preg_replace("/refAmministrazione/i", "selezionato", $nav);
                break;
                
            default:
                break;
        }

        $nav = preg_replace("/refEsplora/i", "", $nav);
        $nav = preg_replace("/refProfilo/i", "", $nav);
        $nav = preg_replace("/refPreferiti/i", "", $nav);
        $nav = preg_replace("/refImpostazioni/i", "", $nav);
        $nav = preg_replace("/refFaq/i", "", $nav);
        $nav = preg_replace("/refSupporto/i", "", $nav);
        $nav = preg_replace("/refPrivacy/i", "", $nav);
        $nav = preg_replace("/refAbout/i", "", $nav);
        $nav = preg_replace("/refHome/i", "home.php", $nav);

        return $nav;
    }


    function printPageHome($output){
        global $connection;
        
        if(empty($connection))
            connettiDB();
        
        $host  = $_SERVER['HTTP_HOST'];
        $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        $extra = 'serie.php?serie_id=';

        $head_page = implode("", file("../txt/pagehead.txt"));
        $home = implode("", file("../txt/home.txt"));
        $show = implode("", file("../txt/show_home.txt"));

        $output = preg_replace("/<!-- Nome_Pagina -->/i", "home", $output );
        $output = preg_replace("/<!-- Page_Head -->/i", $head_page, $output );


        $query="select id,titolo,miniatura,descrizione,consigliato,non_consigliato, (consigliato)/(consigliato+non_consigliato)*100 AS perc_consigliato from serie order by perc_consigliato DESC LIMIT 3";
            //Seleziono la lista delle serie tv del momento
            $shows=resultQueryToTable($connection->query($query));
            
            //Scrivo le serie tv
            $show_collect ="<!-- Successiva -->";
            $first = 1;

            foreach ($shows as $shows) {
                $show_collect=preg_replace("/<!-- Successiva -->/i",$show." <!-- Successiva -->" , $show_collect );
                $show_collect=preg_replace("/<!-- Immagine -->/i",$shows["miniatura"] , $show_collect );
                $show_collect=preg_replace("/<!-- Titolo -->/i",$shows["titolo"] , $show_collect );
                $show_collect=preg_replace("/<!-- Descrizione -->/i",$shows["descrizione"] , $show_collect );
                $show_collect=preg_replace("/<!-- Consigliato -->/i", (int)$shows["perc_consigliato"] , $show_collect );
                $show_collect=preg_replace("/<!-- Url_Show -->/i","http://$host$uri/$extra".$shows["id"] , $show_collect );  
                if($first == 1){
                    $show_collect=preg_replace("/<!-- Div_Consigliati -->/i", "<div class=\"consigliati__show\" id=\"first-show-anchor\">" , 
                        $show_collect );
                    $first = 0;
                }
                else{
                    $show_collect=preg_replace("/<!-- Div_Consigliati -->/i", "<div class=\"consigliati__show\">" , 
                        $show_collect );
                }
            }
            $show=preg_replace("/<!-- Successiva -->/i","" , $show );
            $output = preg_replace("/<!-- Contenuto_Effettivo -->/i", $home , $output );
            $output = preg_replace("/<!-- Titoli_Momento -->/i" , $show_collect , $output);
            
            if(array_key_exists('user_username',$_SESSION) && !empty($_SESSION['user_username'])){
                $output = preg_replace("/<!-- Registrati -->/i", "" , $output );
            }
            else{
                $Registrati =  
                "<div class=\"iscriviti\">
                    <p>
                        Per interagire con la community, per votare le tue serie TV preferite e molto altro <a href=\"signup.php\" title=\"registrati\">REGISTRATI</a> oppure <a href=\"login.php\" title=\"accedi\">EFFETTUA IL <span lang=\"EN\">LOGIN</span></a>
                    </p>
                </div>";
                $output = preg_replace("/<!-- Registrati -->/i", $Registrati , $output );
            }

        return $output;
    }
    
    function printPageEsplora($output){
        global $connection;
        
        if(empty($connection))
            connettiDB();
            
        
        $host  = $_SERVER['HTTP_HOST'];
        $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        $extra = 'serie.php?serie_id=';

        $head_page = implode("",file("../txt/pagehead.txt"));
        $genere_page = implode("",file("../txt/genere.txt"));
        $show_page = implode("",file("../txt/show.txt"));
        $query="select distinct id, nome from genere a join serie_genere b on a.id=b.id_genere";
        //Seleziono la lista dei generi già in una tabella
        $generi=resultQueryToTable($connection->query($query));
        $genere_show_collect="<!-- Successivo -->";
        /*if (count($generi)>1) {
         $genere_show_collect=$genere_page;
         }*/
        for ($i = 0; $i < count($generi); $i++) {
            
            $genere_id=$generi[$i]["id"];
            $genere_nome=$generi[$i]["nome"];
            
            $query="select id,miniatura,titolo, consigliato from serie a join serie_genere b on a.id=b.id_serie where b.id_genere=$genere_id";
            //Seleziono la lista delle serie tv di un determinato genere già in una tabella
            $series=resultQueryToTable($connection->query($query));
            
            //Scrivo le serie tv
            $show="<!-- Successiva -->";
            
            /*if (count($series)>1) {
             $show=$show_page;
             }*/
            for ($j = 0; $j < count($series); $j++) {
                
                $show=preg_replace("/<!-- Successiva -->/i",$show_page." <!-- Successiva -->" , $show );
                $show=preg_replace("/<!-- Immagine -->/i",$series[$j]["miniatura"] , $show );
                $show=preg_replace("/<!-- Id -->/i",$series[$j]["id"] , $show );
                $show=preg_replace("/<!-- Titolo -->/i",$series[$j]["titolo"] , $show );
                $show=preg_replace("/<!-- Consigliato -->/i",$series[$j]["consigliato"] , $show );
                $show=preg_replace("/<!-- Url_Show -->/i","http://$host$uri/$extra".$series[$j]["id"] , $show );
                
                
            }
            $show=preg_replace("/<!-- Successiva -->/i","" , $show );
            
            //Inserisco le serie nel div del genere e completo il div con le informazioni mancanti
            $genere_show_collect=preg_replace("/<!-- Successivo -->/i",$genere_page." <!-- Successivo -->" , $genere_show_collect );
            $genere_show_collect=preg_replace("/<!-- Genere -->/i",$genere_nome , $genere_show_collect );
            $genere_show_collect=preg_replace("/<!-- Mostra_Tutto_Genere -->/i",'<a href="http://'.$host.$uri.'/genere.php?genere_id='.$genere_id.'"><h3>mostra tutto</h3></a>' , $genere_show_collect );
            $genere_show_collect=preg_replace("/<!-- Genere_Titolo -->/i",$genere_nome , $genere_show_collect );
            $genere_show_collect=preg_replace("/<!-- Show -->/i",$show , $genere_show_collect );
            
        }
        $genere_show_collect=preg_replace("/<!-- Successivo -->/i","" , $genere_show_collect );
        
        $output = preg_replace("/<!-- Nome_Pagina -->/i", "esplora", $output );
        $output = preg_replace("/<!-- Page_Head -->/i", $head_page, $output );
        $output = preg_replace("/<!-- Contenuto_Effettivo -->/i", $genere_show_collect, $output );
        
        return $output;
        
    }
    
    function printPageSerie($output){
        
        global $connection;
        if(empty($connection))
            connettiDB();
            
        $host  = $_SERVER['HTTP_HOST'];
        $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        $extra = 'esplora.php';
        $extra2 = 'preferiti_action.php?serie_id=';
        $extra3 = 'rimuoviPref_action.php?serie_id=';
        $extra4 = 'consigliati_action.php?serie_id=';
        $extra5 = 'rimuoviCons_action.php?serie_id=';
        $extra6 = 'modificaCons_action.php?consigliato=';
        $extra7 = 'vota_action.php?serie_id=';
        $extra8 = 'cambiaVoto_action.php?serie_id=';
        $extra9 = 'cancellaVoto_action.php?serie_id=';

        
        
        if(!array_key_exists('serie_id',$_GET) && empty($_GET['serie_id'])) { 
            /* Redirect to a different page in the current directory that was requested */
            header("Location: http://$host$uri/$extra");
        }
        
        $_SESSION['serie_id']=$_GET['serie_id'];
        
        $side_block = implode("",file("../txt/side_bar.txt"));
        $side_serie_block = implode("",file("../txt/serie/side_bar_serie.txt"));
        $attore_block = implode("",file("../txt/serie/attore.txt"));
        $post_block = implode("",file("../txt/serie/post.txt"));
        $serie_block = implode("",file("../txt/serie/serie.txt")); 
        $title = implode("",file("../txt/serie/pagehead_serie.txt"));
        

        //Imposta immagine di background personalizzata

        $query = "select background from serie where id=?";
        
        $stmt=executeQuery($query,array(&$_GET["serie_id"]),array("i"));
        $result=resultQueryToTable($stmt->get_result());
        $stmt->close();
                
        if(count($result)<=0){
            $output = preg_replace("/<!-- Nome_Pagina -->/i", "genere", $output );
            $output = preg_replace("/<!-- Page_Head -->/i", $title, $output );
            $error_block = implode("",file("../txt/errore.txt"));
            $error_block = preg_replace("/<!-- Messaggio_Errore -->/i", "Serie non disponibile", $error_block );            
            $output = preg_replace("/<!-- Contenuto_Effettivo -->/i", $error_block, $output );
            
            return $output;
        }
        
        $title = preg_replace("/<--! Url_Back -->/i", $result[0]["background"], $title);
        $output = preg_replace("/<!-- Page_Head -->/i", $title, $output );
        
        //Parte side-bar        
        
        $query="select a.id, a.nome, a.cognome, a.miniatura from attore a join serie_attore b on a.id=b.id_attore where id_serie=?";

        $stmt=executeQuery($query,array(&$_GET["serie_id"]),array("i"));
        $attori=resultQueryToTable($stmt->get_result());
        $stmt->close();
        
        $attore_collect="<!-- Successivo -->";
        
        
        foreach ($attori as $attore) {
            $attore_collect=preg_replace("/<!-- Successivo -->/i",$attore_block." <!-- Successivo -->" , $attore_collect );
            $attore_collect=preg_replace("/<!-- Immagine -->/i",$attore["miniatura"] , $attore_collect );
            $attore_collect=preg_replace("/<!-- Nome_Cognome_Attore -->/i",$attore["nome"]." ".$attore["cognome"] , $attore_collect );      
        }
        $attore_collect=preg_replace("/<!-- Successivo -->/i","" , $attore_collect );



        if(array_key_exists('user_username',$_SESSION) && !empty($_SESSION['user_username'])){
            $id_utente = $_SESSION["user_id"];
            $query = "select * from preferiti where id_serie=? and id_utente=".$id_utente;
            $stmt=executeQuery($query,array(&$_GET["serie_id"]),array("i"));
            $isPreferito=resultQueryToTable($stmt->get_result());
            $stmt->close();
            
            if(empty($isPreferito)){
                $side_serie_block=preg_replace("/<!-- Add-Rem -->/i", "Aggiungi ai preferiti" , $side_serie_block );
                $side_serie_block=preg_replace("/<!-- Add_Preferitio -->/i", "http://$host$uri/$extra2".$_GET["serie_id"] , 
                $side_serie_block );
            }
            else{
                $side_serie_block=preg_replace("/<!-- Add-Rem -->/i", "Rimuovi dai preferiti" , $side_serie_block );
                $side_serie_block=preg_replace("/<!-- Add_Preferitio -->/i", "http://$host$uri/$extra3".$_GET["serie_id"] , 
                $side_serie_block );
            }


            $query = "select * from consiglio where id_serie=? and id_utente=".$id_utente;
            $stmt=executeQuery($query,array(&$_GET["serie_id"]),array("i"));
            $isPresent=resultQueryToTable($stmt->get_result());
            $stmt->close();
            
            
            if(empty($isPresent)){
                $side_serie_block=preg_replace("/<!-- Consigliato -->/i", "http://$host$uri/$extra4".$_GET["serie_id"]."&consigliato=1" , $side_serie_block );
                $side_serie_block=preg_replace("/<!-- Sconsigliato -->/i", "http://$host$uri/$extra4".$_GET["serie_id"]."&consigliato=0" , $side_serie_block );
            }
            else{
                $query = "select * from consiglio where id_serie=? and id_utente=".$id_utente." and consigliato=1";
                $stmt=executeQuery($query,array(&$_GET["serie_id"]),array("i"));
                $isConsigliato=resultQueryToTable($stmt->get_result());
                $stmt->close();
                
                if(empty($isConsigliato)){
                    $side_serie_block=preg_replace("/Scons_Color/i", " selected" , $side_serie_block );
                    $side_serie_block=preg_replace("/Cons_Color/i", "" , $side_serie_block );
                    $side_serie_block=preg_replace("/<!-- Consigliato -->/i", "http://$host$uri/$extra6"."1"."&serie_id=".$_GET["serie_id"] , $side_serie_block );
                    $side_serie_block=preg_replace("/<!-- Sconsigliato -->/i", "http://$host$uri/$extra5".$_GET["serie_id"] , 
                        $side_serie_block );
                }
                else{
                    $side_serie_block=preg_replace("/Scons_Color/i", "" , $side_serie_block );
                    $side_serie_block=preg_replace("/Cons_Color/i", " selected" , $side_serie_block );
                    $side_serie_block=preg_replace("/<!-- Consigliato -->/i", "http://$host$uri/$extra5".$_GET["serie_id"] , 
                        $side_serie_block );
                    $side_serie_block=preg_replace("/<!-- Sconsigliato -->/i", "http://$host$uri/$extra6"."0"."&serie_id=".
                        $_GET["serie_id"] , $side_serie_block );
                }
            }

            $query = "select * from voto where id_serie=? and id_utente=".$id_utente;
            $stmt=executeQuery($query,array(&$_GET["serie_id"]),array("i"));
            $isVotato=resultQueryToTable($stmt->get_result());
            $stmt->close();
            
            
            if(empty($isVotato)){
                $side_serie_block=preg_replace("/<!-- Voto1 -->/i", "http://$host$uri/$extra7".$_GET["serie_id"]."&voto=1" , 
                        $side_serie_block );
                $side_serie_block=preg_replace("/<!-- Voto2 -->/i", "http://$host$uri/$extra7".$_GET["serie_id"]."&voto=2" , 
                        $side_serie_block );
                $side_serie_block=preg_replace("/<!-- Voto3 -->/i", "http://$host$uri/$extra7".$_GET["serie_id"]."&voto=3" , 
                        $side_serie_block );
                $side_serie_block=preg_replace("/<!-- Voto4 -->/i", "http://$host$uri/$extra7".$_GET["serie_id"]."&voto=4" , 
                        $side_serie_block );
                $side_serie_block=preg_replace("/<!-- Voto5 -->/i", "http://$host$uri/$extra7".$_GET["serie_id"]."&voto=5" , 
                        $side_serie_block );
            }
            else{
                switch ($isVotato[0]["valutazione"]) {
                    case '1':{
                        $side_serie_block=preg_replace("/<!-- Voto1 -->/i", "http://$host$uri/$extra9".$_GET["serie_id"] , 
                        $side_serie_block );
                        $side_serie_block=preg_replace("/<!-- Voto2 -->/i", "http://$host$uri/$extra8".$_GET["serie_id"]."&voto=2" , 
                        $side_serie_block );
                        $side_serie_block=preg_replace("/<!-- Voto3 -->/i", "http://$host$uri/$extra8".$_GET["serie_id"]."&voto=3" , 
                        $side_serie_block );
                        $side_serie_block=preg_replace("/<!-- Voto4 -->/i", "http://$host$uri/$extra8".$_GET["serie_id"]."&voto=4" , 
                        $side_serie_block );
                        $side_serie_block=preg_replace("/<!-- Voto5 -->/i", "http://$host$uri/$extra8".$_GET["serie_id"]."&voto=5" , 
                        $side_serie_block );
                        $side_serie_block=preg_replace("/voto1_Color/i", "class=\"selected\"" , $side_serie_block );
                    }
                        break;

                    case '2':{
                        $side_serie_block=preg_replace("/<!-- Voto1 -->/i", "http://$host$uri/$extra8".$_GET["serie_id"]."&voto=1" , 
                        $side_serie_block );
                        $side_serie_block=preg_replace("/<!-- Voto2 -->/i", "http://$host$uri/$extra9".$_GET["serie_id"] , 
                        $side_serie_block );
                        $side_serie_block=preg_replace("/<!-- Voto3 -->/i", "http://$host$uri/$extra8".$_GET["serie_id"]."&voto=3" , 
                        $side_serie_block );
                        $side_serie_block=preg_replace("/<!-- Voto4 -->/i", "http://$host$uri/$extra8".$_GET["serie_id"]."&voto=4" , 
                        $side_serie_block );
                        $side_serie_block=preg_replace("/<!-- Voto5 -->/i", "http://$host$uri/$extra8".$_GET["serie_id"]."&voto=5" , 
                        $side_serie_block );
                        $side_serie_block=preg_replace("/voto2_Color/i", "class=\"selected\"" , $side_serie_block );
                    }
                        break;

                    case '3':{
                        $side_serie_block=preg_replace("/<!-- Voto1 -->/i", "http://$host$uri/$extra8".$_GET["serie_id"]."&voto=1" , 
                        $side_serie_block );
                        $side_serie_block=preg_replace("/<!-- Voto2 -->/i", "http://$host$uri/$extra8".$_GET["serie_id"]."&voto=2", 
                        $side_serie_block );
                        $side_serie_block=preg_replace("/<!-- Voto3 -->/i", "http://$host$uri/$extra9".$_GET["serie_id"] , 
                        $side_serie_block );
                        $side_serie_block=preg_replace("/<!-- Voto4 -->/i", "http://$host$uri/$extra8".$_GET["serie_id"]."&voto=4" , 
                        $side_serie_block );
                        $side_serie_block=preg_replace("/<!-- Voto5 -->/i", "http://$host$uri/$extra8".$_GET["serie_id"]."&voto=5" , 
                        $side_serie_block );
                        $side_serie_block=preg_replace("/voto3_Color/i", "class=\"selected\"" , $side_serie_block );
                    }
                        break;

                    case '4':{
                        $side_serie_block=preg_replace("/<!-- Voto1 -->/i", "http://$host$uri/$extra8".$_GET["serie_id"]."&voto=1" , 
                        $side_serie_block );
                        $side_serie_block=preg_replace("/<!-- Voto2 -->/i", "http://$host$uri/$extra8".$_GET["serie_id"]."&voto=2", 
                        $side_serie_block );
                        $side_serie_block=preg_replace("/<!-- Voto3 -->/i", "http://$host$uri/$extra8".$_GET["serie_id"]."&voto=3" , 
                        $side_serie_block );
                        $side_serie_block=preg_replace("/<!-- Voto4 -->/i", "http://$host$uri/$extra9".$_GET["serie_id"] , 
                        $side_serie_block );
                        $side_serie_block=preg_replace("/<!-- Voto5 -->/i", "http://$host$uri/$extra8".$_GET["serie_id"]."&voto=5" , 
                        $side_serie_block );
                        $side_serie_block=preg_replace("/voto4_Color/i", "class=\"selected\"" , $side_serie_block );
                    }
                        break;

                    case '5':{
                        $side_serie_block=preg_replace("/<!-- Voto1 -->/i", "http://$host$uri/$extra8".$_GET["serie_id"]."&voto=1" , 
                        $side_serie_block );
                        $side_serie_block=preg_replace("/<!-- Voto2 -->/i", "http://$host$uri/$extra8".$_GET["serie_id"]."&voto=2", 
                        $side_serie_block );
                        $side_serie_block=preg_replace("/<!-- Voto3 -->/i", "http://$host$uri/$extra8".$_GET["serie_id"]."&voto=3" , 
                        $side_serie_block );
                        $side_serie_block=preg_replace("/<!-- Voto4 -->/i", "http://$host$uri/$extra8".$_GET["serie_id"]."&voto=4"  , 
                        $side_serie_block );
                        $side_serie_block=preg_replace("/<!-- Voto5 -->/i", "http://$host$uri/$extra9".$_GET["serie_id"], 
                        $side_serie_block );
                        $side_serie_block=preg_replace("/voto5_Color/i", "class=\"selected\"" , $side_serie_block );
                    }
                        break;

                    default:
                        break;
                }
            }

        }
        else{

            $side_serie_block=preg_replace("/<!-- Voto1 -->/i", "http://$host$uri/login.php", $side_serie_block );
            $side_serie_block=preg_replace("/<!-- Voto2 -->/i", "http://$host$uri/login.php", $side_serie_block );
            $side_serie_block=preg_replace("/<!-- Voto3 -->/i", "http://$host$uri/login.php", $side_serie_block );
            $side_serie_block=preg_replace("/<!-- Voto4 -->/i", "http://$host$uri/login.php", $side_serie_block );
            $side_serie_block=preg_replace("/<!-- Voto5 -->/i", "http://$host$uri/login.php", $side_serie_block );

            $side_serie_block=preg_replace("/<!-- Add-Rem -->/i", "Aggiungi ai preferiti" , $side_serie_block );
            $side_serie_block=preg_replace("/<!-- Add_Preferitio -->/i", "http://$host$uri/login.php", $side_serie_block );

            $side_serie_block=preg_replace("/<!-- Consigliato -->/i", "http://$host$uri/login.php" , $side_serie_block );
            $side_serie_block=preg_replace("/<!-- Sconsigliato -->/i", "http://$host$uri/login.php" , $side_serie_block );
        }

        $side_serie_block=preg_replace("/Scons_Color/i", "" , $side_serie_block );
        $side_serie_block=preg_replace("/Cons_Color/i", "" , $side_serie_block );

        $side_serie_block=preg_replace("/voto1_Color/i", "" , $side_serie_block );
        $side_serie_block=preg_replace("/voto2_Color/i", "" , $side_serie_block );
        $side_serie_block=preg_replace("/voto3_Color/i", "" , $side_serie_block );
        $side_serie_block=preg_replace("/voto4_Color/i", "" , $side_serie_block );
        $side_serie_block=preg_replace("/voto5_Color/i", "" , $side_serie_block );

        $side_serie_block=preg_replace("/<!-- Attore -->/i",$attore_collect , $side_serie_block );
        $side_block = preg_replace("/<!-- Side_Bar_Contnent -->/i", $side_serie_block, $side_block );
        $output = preg_replace("/<!-- Side_Bar -->/i", $side_block, $output );
        
        //Titolo, voto e consiglio

        $output = preg_replace("/<!-- Page_Head -->/i", $title, $output );
        $query = "select titolo,voto,consigliato,non_consigliato,preferiti,(consigliato)/(consigliato+non_consigliato)*100 AS perc_consigliato from serie where id=?";
        $stmt=executeQuery($query,array(&$_GET["serie_id"]),array("i"));
        $result=resultQueryToTable($stmt->get_result());
        $stmt->close();
        
        if(($result[0]["voto"] - (int)$result[0]["voto"]) == 0){
            $voto = (int)$result[0]["voto"];
        }
        else
            $voto = $result[0]["voto"];

        $output = preg_replace("/<!-- Titolo -->/i",$result[0]["titolo"] , $output );
        $output = preg_replace("/<!-- Voto -->/i",$voto , $output );

        if ($result[0]["perc_consigliato"] > 0)
            $output = preg_replace("/<!-- Percentuale_Consigliati -->/i",(int)$result[0]["perc_consigliato"] , $output );
        else
            $output = preg_replace("/<!-- Percentuale_Consigliati -->/i", '0' , $output );

        

        //Parte centro stagioni ed episodi 
        $query="select miniatura,titolo,distribuzione,descrizione,creatore,consigliato,non_consigliato,voto from serie where id=?";
        $stmt=executeQuery($query,array(&$_GET["serie_id"]),array("i"));
        $serie=resultQueryToTable($stmt->get_result());
        $stmt->close();
                
        $serie_block=preg_replace("/<!-- Descrizione -->/i",$serie[0]["descrizione"] , $serie_block );
        
        $numero_stagioni=mysqli_fetch_assoc(
            $connection->query(
                "select count(*) as totale_stagioni from (select distinct b.stagione from serie a join episodio b on a.id=b.id_serie
                 where a.id=".$_GET["serie_id"].") as stagioni"
                )
            )["totale_stagioni"];
        
        $stagione_collect="<!-- Successivo -->";
        
        $extra="stagione.php?serie_id="; 
        for ($i = 1; $i <= $numero_stagioni; $i++) {
                
            $link=(string)"http://".$host.$uri."/".$extra.$_GET["serie_id"]."&stagione_numero=".($i);
            $stagione_collect=preg_replace("/<!-- Successivo -->/i","<a href=\"javascript:episodi('$link')\" >Stagione".($i)."</a>"." <!-- Successivo -->" , $stagione_collect );
            $stagione_collect=preg_replace("/<!-- SuccessivoNS -->/i","<a href='$link' >Stagione".($i)."</a>"." <!-- SuccessivoNS -->" , $stagione_collect );           
            }
        $stagione_collect=preg_replace("/<!-- Successivo -->/i","" , $stagione_collect );
            
        $serie_block=preg_replace("/<!-- Stagione -->/i",$stagione_collect , $serie_block );
        
        $stagione_collect="<!-- Successivo -->";
        
        $extra="serie.php?serie_id=";
        for ($i = 1; $i <= $numero_stagioni; $i++) {
            
            $link=(string)"http://".$host.$uri."/".$extra.$_GET["serie_id"]."&stagione_numero=".($i);
            $stagione_collect=preg_replace("/<!-- Successivo -->/i","<a href='$link' >Stagione".($i)."</a>"." <!-- Successivo -->" , $stagione_collect );
        }
        $stagione_collect=preg_replace("/<!-- Successivo -->/i","" , $stagione_collect );
        
        $serie_block=preg_replace("/<!-- StagioneNS -->/i",$stagione_collect , $serie_block );
        
        if(array_key_exists('stagione_numero',$_GET) && !empty($_GET['stagione_numero'])){
            $query="select b.id, b.titolo,b.descrizione, b.numero,b.data,b.visualizzato from serie a join episodio b on a.id=b.id_serie where b.stagione=?  and a.id=?";
            $stmt=executeQuery($query,array(&$_GET['stagione_numero'],&$_GET['serie_id']),array("ss"));
            
        }else{
            $query="select b.id, b.titolo,b.descrizione, b.numero,b.data,b.visualizzato from serie a join episodio b on a.id=b.id_serie where b.stagione=1  and a.id=?";
            $stmt=executeQuery($query,array(&$_GET['serie_id']),array("s"));
            
        }
        $episodi=resultQueryToTable($stmt->get_result());
        $stmt->close();
        $episodio_collect="<!-- Successivo -->";
        
        if(array_key_exists('user_username',$_SESSION) && !empty($_SESSION['user_username'])){
            foreach ($episodi as $episodio) {

                $query="select visto.id_utente from visto join episodio join utente where visto.id_episodio=".$episodio["id"]." and visto.id_utente=".$_SESSION['user_id']." group by visto.id_utente";
                $isVisto=resultQueryToTable($connection->query($query));

                if(empty($isVisto)){
                    $isVisto="NO";
                    $visto_nonVisto = "si";
                    $link="http://$host$uri/visto_action.php?id_episodio=".$episodio["id"]."&serie_id=".$_GET["serie_id"];
                }
                else{
                    $isVisto="SI";
                    $visto_nonVisto = "no";
                    $link="http://$host$uri/nonVisto_action.php?id_episodio=".$episodio["id"]."&serie_id=".$_GET["serie_id"];
                }

                $episodio_collect=preg_replace("/<!-- Successivo -->/i", 
                '<tr> '
                .'<td>'.$episodio["numero"].'</td>'
                    .'<td>'.$episodio["titolo"].'</td>' //TODO: aggiungere href episodio
                .'<td>'.date("d-m-Y", strtotime($episodio["data"])).'</td>'
                .'<td>'.'<a href="'.$link.'" title="visto '. $visto_nonVisto.'">'.$isVisto.'</a></td>'
                .'</tr>'
                .'<!-- Successivo -->'
                    , $episodio_collect );
            }
        }
        else{
            foreach ($episodi as $episodio) {
                $episodio_collect=preg_replace("/<!-- Successivo -->/i", 
                '<tr> '
                .'<td>'.$episodio["numero"].'</td>'
                    .'<td>'.$episodio["titolo"].'</td>' //TODO: aggiungere href episodio
                .'<td>'.date("d-m-Y", strtotime($episodio["data"])).'</td>'
                .'<td>'.'<a href="http://'.$host.$uri.'/login.php" title="visto no">NO</a></td>'
                .'</tr>'
                .'<!-- Successivo -->'
                    , $episodio_collect );
            }
        }


        $episodio_collect=preg_replace("/<!-- Successivo -->/i","" , $episodio_collect );
        $serie_block=preg_replace("/<!-- Episodio -->/i",$episodio_collect, $serie_block );
        
        //parte centro post per ogni serie
        
        
        if(!array_key_exists('user_id',$_SESSION) && empty($_SESSION['user_id'])){
            
            $query="select b.id, b.testo, c.username from (serie a join post b on a.id=b.id_serie) join utente c on b.id_utente=c.id where b.cancellato=0 and a.id=? and b.id not in 
            (select id_ref from segnalazione where tipo=1)";
        }else{
            $query="select b.id, b.testo, c.username from (serie a join post b on a.id=b.id_serie) join utente c on b.id_utente=c.id where b.cancellato=0 and a.id=? and b.id not in
            (select id_ref from segnalazione where tipo=1 and id_utente=".$_SESSION["user_id"].")";            
        }
        
        $stmt=executeQuery($query,array(&$_GET["serie_id"]),array("i"));
        $posts=resultQueryToTable($stmt->get_result());
        $stmt->close();
                
        $post_collect="<!-- Successivo -->";
        
        foreach ($posts as $post) {
            $post_collect=preg_replace("/<!-- Successivo -->/i",$post_block."<!-- Successivo -->", $post_collect );
            $post_collect=preg_replace("/<!-- Username_Autore -->/i",$post["username"], $post_collect );
            $post_collect=preg_replace("/<!-- Testo -->/i",$post["testo"], $post_collect );
            $post_collect=preg_replace("/<!-- Segnala -->/i","http://$host$uri/segnala_action.php?post_id=".$post["id"], $post_collect );
            
            
        }
        $post_collect=preg_replace("/<!-- Successivo -->/i","" , $post_collect );
        $serie_block=preg_replace("/<!-- Post -->/i",$post_collect, $serie_block );
        
        
        $output = preg_replace("/<!-- Nome_Pagina -->/i", "show-page", $output );
        $output = preg_replace("/<!-- Contenuto_Effettivo -->/i", $serie_block, $output );
        
        
        return $output;
        
    }

    function printPagePreferiti($output){

        global $connection;

        if(empty($connection))
            connettiDB();
        
        $host  = $_SERVER['HTTP_HOST'];
        $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        $extra = 'serie.php?serie_id=';

        $head_page = implode("", file("../txt/pagehead.txt"));
        $output = preg_replace("/<!-- Nome_Pagina -->/i", "preferiti", $output );
        $output = preg_replace("/<!-- Page_Head -->/i", $head_page, $output );
        $preferiti = implode("", file("../txt/preferiti.txt"));
        
        if(array_key_exists('user_username',$_SESSION) && !empty($_SESSION['user_username'])){
            $title_pref="<h1 class=\"titolo-genere\">
                            Preferiti
                        </h1>
                        <ul class=\"list-inline list_no-style elenco-serie\">";
            $preferiti =preg_replace("/<!-- Titolo_Preferiti -->/i", $title_pref, $preferiti);
            $show = implode("", file("../txt/show_preferiti.txt"));

            $id_utente = $_SESSION["user_id"];

            $query="select distinct serie.id,serie.titolo,serie.miniatura from (serie JOIN preferiti ON serie.id = preferiti.id_serie) JOIN utente ON utente.id = preferiti.id_utente and utente.id=".$id_utente;

            $shows=resultQueryToTable($connection->query($query));

            $show_collect ="<!-- Successiva -->";

            foreach ($shows as $shows) {
                $show_collect=preg_replace("/<!-- Successiva -->/i",$show." <!-- Successiva -->" , $show_collect );
                $show_collect=preg_replace("/<!-- Immagine -->/i",$shows["miniatura"] , $show_collect );
                $show_collect=preg_replace("/<!-- Titolo -->/i",$shows["titolo"] , $show_collect );
                $show_collect=preg_replace("/<!-- Url_Show -->/i","http://$host$uri/$extra".$shows["id"] , $show_collect );  
            }

            $show=preg_replace("/<!-- Successiva -->/i","</ul>" , $show );
            $output = preg_replace("/<!-- Contenuto_Effettivo -->/i", $preferiti , $output );
            $output = preg_replace("/<!-- Registrati -->/i", "" , $output );
            $output = preg_replace("/<!-- Preferiti_Show -->/i" , $show_collect , $output);

        }

        else{
                $Registrati =  
                "<div class=\"iscriviti\">
                    <p>
                        Per vedere l'elenco delle tue serie TV preferite <a href=\"signup.php\" title=\"registrati\">REGISTRATI</a> oppure <a href=\"login.php\" title=\"accedi\">EFFETTUA IL <span lang=\"EN\">LOGIN</span></a>
                    </p>
                </div>";

            $preferiti = preg_replace("/<!-- Registrati -->/i", $Registrati , $preferiti );
            $output = preg_replace("/<!-- Contenuto_Effettivo -->/i", $preferiti , $output );
        }

        return $output;  
    }

    function printPageProfilo($output){

        global $connection;
        if(empty($connection))
            connettiDB();
            
        $host  = $_SERVER['HTTP_HOST'];

        $head_page = implode("", file("../txt/pagehead.txt"));
        $output = preg_replace("/<!-- Nome_Pagina -->/i", "profilo", $output );
        $output = preg_replace("/<!-- Page_Head -->/i", $head_page, $output );
        $profilo = implode("", file("../txt/profilo.txt"));
        
        if(array_key_exists('user_username',$_SESSION) && !empty($_SESSION['user_username'])){

            $id_utente = $_SESSION["user_id"];

            $query="select foto_profilo, nome, cognome, data_nascita, email, username from utente WHERE id = ".$id_utente."";

            $info=resultQueryToTable($connection->query($query));

            $profilo=preg_replace("/<!-- Foto -->/i",$info[0]["foto_profilo"] , $profilo );
            $profilo=preg_replace("/<!-- Nome -->/i", $info[0]["nome"] , $profilo );
            $profilo=preg_replace("/<!-- Cognome -->/i", $info[0]["cognome"] , $profilo );
            $datanascita= date("d-m-Y",strtotime($info[0]["data_nascita"]));
            $profilo=preg_replace("/<!-- Data -->/i", $datanascita , $profilo );
            $profilo=preg_replace("/<!-- User -->/i", $info[0]["username"] , $profilo );
            $profilo=preg_replace("/<!-- Email -->/i", $info[0]["email"] , $profilo );


            $output = preg_replace("/<!-- Registrati -->/i", "" , $output );
            $output = preg_replace("/<!-- Contenuto_Effettivo -->/i", $profilo , $output );
        }

        else{
                $Registrati =  
                "<div class=\"iscriviti\">
                    <p>
                        Per accedere alla tua area personale <a href=\"signup.php\" title=\"registrati\">REGISTRATI</a> oppure <a href=\"login.php\" title=\"accedi\">EFFETTUA IL <span lang=\"EN\">LOGIN</span></a>
                    </p>
                </div>";

            $profilo = preg_replace("/<!-- Registrati -->/i", $profilo , $Registrati );
            $output = preg_replace("/<!-- Contenuto_Effettivo -->/i", $profilo , $output );
        }

        return $output;  
    }


    function printPagePrivacy($output){
        
        $host  = $_SERVER['HTTP_HOST'];
        $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

        $head_page = implode("", file("../txt/pagehead.txt"));
        $privacy = implode("", file("../txt/privacy.txt"));

        $output = preg_replace("/<!-- Nome_Pagina -->/i", "privacy", $output );
        $output = preg_replace("/<!-- Page_Head -->/i", $head_page, $output );
        $output = preg_replace("/<!-- Contenuto_Effettivo -->/i", $privacy , $output );

        return $output;
    }

    function printPageAbout($output){
        
        $host  = $_SERVER['HTTP_HOST'];
        $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

        $head_page = implode("", file("../txt/pagehead.txt"));
        $about = implode("", file("../txt/about.txt"));

        $output = preg_replace("/<!-- Nome_Pagina -->/i", "about", $output );
        $output = preg_replace("/<!-- Page_Head -->/i", $head_page, $output );
        $output = preg_replace("/<!-- Contenuto_Effettivo -->/i", $about , $output );

        return $output;
    }

    function printPageLogin($output){
        
        $host  = $_SERVER['HTTP_HOST'];
        $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        
        $head_page = implode("", file("../txt/pagehead.txt"));
        $login = implode("", file("../txt/login.txt"));
        
        $output = preg_replace("/<!-- Nome_Pagina -->/i", "login", $output );
        $output = preg_replace("/<!-- Page_Head -->/i", $head_page, $output );
        if(array_key_exists('errore_login',$_SESSION) && !empty($_SESSION['errore_login'])){
            $login = preg_replace("/<!-- Errore -->/i", "Errore login, reinserisci i dati. ", $login );
            unset($_SESSION['errore_login']);
        }
        
        
        $output = preg_replace("/<!-- Contenuto_Effettivo -->/i", $login , $output );
        
        return $output;
    }
    
    function printPageSignUp($output){
            
        $host  = $_SERVER['HTTP_HOST'];
        $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        
        $head_page = implode("", file("../txt/pagehead.txt"));
        $signup = implode("", file("../txt/signup.txt"));
        
        $output = preg_replace("/<!-- Nome_Pagina -->/i", "signup", $output );
        $output = preg_replace("/<!-- Page_Head -->/i", $head_page, $output );

        
        if(array_key_exists('errore_signup',$_SESSION) && !empty($_SESSION['errore_signup'])){
            switch($_SESSION['errore_signup']){
                case '1':
                    $signup = preg_replace("/<!-- Errore -->/i", "Errore formato username. ", $signup );
                break;

                case '2':
                    $signup = preg_replace("/<!-- Errore -->/i", "Errore formato password. ", $signup );
                break;

                case '3':
                    $signup = preg_replace("/<!-- Errore -->/i", "Errore formato email. ", $signup );
                break;

                case '4':
                    $signup = preg_replace("/<!-- Errore -->/i", "Errore formato nome. ", $signup );
                break;

                case '5':
                    $signup = preg_replace("/<!-- Errore -->/i", "Errore formato cognome. ", $signup );
                break;

                case '6':
                    $signup = preg_replace("/<!-- Errore -->/i", "Errore formato data. ", $signup );
                break;

                default: 
                    $signup = preg_replace("/<!-- Errore -->/i", "Username gi&agrave; in uso, cambiare username. ", $signup );
                break;
            }
            
        }
        unset($_SESSION['errore_signup']);
        $output = preg_replace("/<!-- Contenuto_Effettivo -->/i", $signup , $output );
        
        return $output;
    }
    
    function printPageGenere($output){
        global $connection;
        if(empty($connection))
            connettiDB();
            
        $host  = $_SERVER['HTTP_HOST'];
        $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        $extra = 'serie.php?serie_id=';
        
        $head_page = implode("", file("../txt/pagehead.txt"));
        $genere_page = implode("",file("../txt/genere.txt"));
        $show_page = implode("",file("../txt/show.txt"));
        
        $query="select distinct id, nome from genere where id=?";
        //Seleziono la lista dei generi già in una tabella
        $stmt=executeQuery($query,array(&$_GET["genere_id"]),array("i"));
        $generi=resultQueryToTable($stmt->get_result());
        $stmt->close();
        
        if(count($generi)<=0){
            $output = preg_replace("/<!-- Nome_Pagina -->/i", "genere", $output );
            $output = preg_replace("/<!-- Page_Head -->/i", $head_page, $output );
            $error_block = implode("",file("../txt/errore.txt"));
            $error_block = preg_replace("/<!-- Messaggio_Errore -->/i", "Genere non disponibile, perch&egrave; non  provi a cercarne un altro? :)", $error_block );
            $output = preg_replace("/<!-- Contenuto_Effettivo -->/i", $error_block, $output );
                        
            return $output;
        }
        $genere_show_collect="<!-- Successivo -->";
        
            $genere_id=$generi[0]["id"];
            $genere_nome=$generi[0]["nome"];
            
            $query="select id,miniatura,titolo, consigliato from serie a join serie_genere b on a.id=b.id_serie where b.id_genere=$genere_id";
            //Seleziono la lista delle serie tv di un determinato genere già in una tabella
            $series=resultQueryToTable($connection->query($query));
            
            //Scrivo le serie tv
            $show="<!-- Successiva -->";
            
            for ($j = 0; $j < count($series); $j++) {
                
                $show=preg_replace("/<!-- Successiva -->/i",$show_page." <!-- Successiva -->" , $show );
                $show=preg_replace("/<!-- Immagine -->/i",$series[$j]["miniatura"] , $show );
                $show=preg_replace("/<!-- Id -->/i",$series[$j]["id"] , $show );
                $show=preg_replace("/<!-- Titolo -->/i",$series[$j]["titolo"] , $show );
                $show=preg_replace("/<!-- Consigliato -->/i",$series[$j]["consigliato"] , $show );
                $show=preg_replace("/<!-- Url_Show -->/i","http://$host$uri/$extra".$series[$j]["id"] , $show );
                
                
            }
            $show=preg_replace("/<!-- Successiva -->/i","" , $show );
            
            //Inserisco le serie nel div del genere e completo il div con le informazioni mancanti
            $genere_show_collect=preg_replace("/<!-- Successivo -->/i",$genere_page." <!-- Successivo -->" , $genere_show_collect );
            $genere_show_collect=preg_replace("/<!-- Genere -->/i",$genere_nome , $genere_show_collect );
            $genere_show_collect=preg_replace("/<!-- Genere_Titolo -->/i",$genere_nome , $genere_show_collect );
            $genere_show_collect=preg_replace("/<!-- Show -->/i",$show , $genere_show_collect );
            
        $genere_show_collect=preg_replace("/<!-- Successivo -->/i","" , $genere_show_collect );
        
        $output = preg_replace("/<!-- Nome_Pagina -->/i", "genere", $output );
        $output = preg_replace("/<!-- Page_Head -->/i", $head_page, $output );
        $output = preg_replace("/<!-- Contenuto_Effettivo -->/i", $genere_show_collect, $output );
        
        return $output;
        }

        function printPageRicerca($output){
            global $connection;
            if(empty($connection))
                connettiDB();
                
                $host  = $_SERVER['HTTP_HOST'];
                $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
                $extra = 'serie.php?serie_id=';
                
                $head_page = implode("", file("../txt/pagehead.txt"));
                $genere_page = implode("",file("../txt/genere.txt"));
                $show_page = implode("",file("../txt/show.txt"));
                
                $genere_show_collect="<!-- Successivo -->";
                
                $genere_nome="Risultati ricerca";
                
                $query="select id,miniatura,titolo, consigliato from serie where titolo like concat('%',?,'%')";
                //Seleziono la lista dei generi già in una tabella
                $stmt=executeQuery($query,array(&$_GET["spazio_ricerca"]),array("s"));
                $series=resultQueryToTable($stmt->get_result());
                $stmt->close();
                
                //Scrivo le serie tv
                $show="<!-- Successiva -->";
                
                if(count($series)<=0){
                    $output = preg_replace("/<!-- Nome_Pagina -->/i", "genere", $output );
                    $output = preg_replace("/<!-- Page_Head -->/i", $head_page, $output );
                    $error_block = implode("",file("../txt/errore.txt"));
                    $error_block = preg_replace("/<!-- Messaggio_Errore -->/i", "Nessuna serie rispetta i parametri di ricerca, perch&egrave; non  provi a cercarne un altro? :)", $error_block );
                    $output = preg_replace("/<!-- Contenuto_Effettivo -->/i", $error_block, $output );
                    
                    return $output;
                }
                
                for ($j = 0; $j < count($series); $j++) {
                    
                    $show=preg_replace("/<!-- Successiva -->/i",$show_page." <!-- Successiva -->" , $show );
                    $show=preg_replace("/<!-- Immagine -->/i",$series[$j]["miniatura"] , $show );
                    $show=preg_replace("/<!-- Id -->/i",$series[$j]["id"] , $show );
                    $show=preg_replace("/<!-- Titolo -->/i",$series[$j]["titolo"] , $show );
                    $show=preg_replace("/<!-- Consigliato -->/i",$series[$j]["consigliato"] , $show );
                    $show=preg_replace("/<!-- Url_Show -->/i","http://$host$uri/$extra".$series[$j]["id"] , $show );
                    
                    
                }
                $show=preg_replace("/<!-- Successiva -->/i","" , $show );
                
                //Inserisco le serie nel div del genere e completo il div con le informazioni mancanti
                $genere_show_collect=preg_replace("/<!-- Successivo -->/i",$genere_page." <!-- Successivo -->" , $genere_show_collect );
                $genere_show_collect=preg_replace("/<!-- Genere -->/i",$genere_nome , $genere_show_collect );
                $genere_show_collect=preg_replace("/<!-- Genere_Titolo -->/i",$genere_nome , $genere_show_collect );
                $genere_show_collect=preg_replace("/<!-- Show -->/i",$show , $genere_show_collect );
                
                $genere_show_collect=preg_replace("/<!-- Successivo -->/i","" , $genere_show_collect );
                
                $output = preg_replace("/<!-- Nome_Pagina -->/i", "genere", $output );
                $output = preg_replace("/<!-- Page_Head -->/i", $head_page, $output );
                $output = preg_replace("/<!-- Contenuto_Effettivo -->/i", $genere_show_collect, $output );
                
                return $output;
        }
        
   function printPage404($output){        
        $host  = $_SERVER['HTTP_HOST'];
        $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

        $head_page = implode("", file("../txt/pagehead.txt"));
        $error = implode("", file("../txt/404.txt"));

        $output = preg_replace("/<!-- Nome_Pagina -->/i", "404", $output );
        $output = preg_replace("/<!-- Page_Head -->/i", $head_page, $output );
        $output = preg_replace("/<!-- Contenuto_Effettivo -->/i", $error , $output );

        return $output;
    }
    
    function printPageAmministrazione($output){
        global $connection;
        if(empty($connection))
            connettiDB();
            
        $host  = $_SERVER['HTTP_HOST'];
        $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        $extra ="login.php";
        
        if(!array_key_exists('user_id',$_SESSION) && empty($_SESSION['user_id'])){
            header("Location: http://$host$uri/$extra");            
        }
        
        
        $head_page = implode("", file("../txt/pagehead.txt"));
        
        if(array_key_exists('user_tipo',$_SESSION) && !empty($_SESSION['user_tipo']) && $_SESSION['user_tipo']=="admin"){           
            $amministratore = implode("", file("../txt/amministrazione.txt"));
        }else{
            $amministratore="Solo gli admin possono accedere a questa pagina";
        }
        
        $output = preg_replace("/<!-- Nome_Pagina -->/i", "amministazione", $output );
        $output = preg_replace("/<!-- Page_Head -->/i", $head_page, $output );
        
        $query="select a.id, a.id_ref, c.username, a.tipo, b.testo from (segnalazione a join post b on a.id_ref=b.id) join utente c on a.id_utente=c.id where a.checked=0";
        
        //Seleziono la lista delle segnalazioni
        $segnalazioni=resultQueryToTable($connection->query($query));
        
        $segnalazioni_collect="<!-- Successivo -->";
        
        $ammAct="amministrazione_action.php?";
        foreach ($segnalazioni as $segnalazione) {
            $link=(string)"http://".$host.$uri."/".$ammAct."post_id=".$segnalazione["id_ref"]."&segnalazione_tipo=".$segnalazione["tipo"]."&elimina=";
            
            $segnalazioni_collect=preg_replace("/<!-- Successivo -->/i",
                '<tr> '
                .'<td class="nome-utente">'.$segnalazione["username"].'</td>'
                .'<td class="tipo-segnalazione">'.$segnalazione["testo"].'</td>'
                .'<td class="spazio-eliminazione">'
                    .'<a href="'.$link.'true">'
                    .'<img src="../img/admin/trash.png" class= "elimina-commento" alt="immagine cestino" title="elimina commento '.$segnalazione["id_ref"].
                '"></img>'
                    .' </a>'
                .'</td>'
                .'<td class="spazio-spunta" title="spunta commento '.$segnalazione["id_ref"].
                '">'
                .'<a href="'.$link.'false" >'
                .'&#10004;'
                .' </a>'
                .'</td>'
                
                .'</tr>'
                .'<!-- Successivo -->'
                , $segnalazioni_collect );
        }
        
        $segnalazioni_collect=preg_replace("/<!-- Successivo -->/i","" , $segnalazioni_collect );
        $amministratore=preg_replace("/<!-- Segnalazioni -->/i",$segnalazioni_collect, $amministratore );
        
        //parte messaggi
        $query="select a.id,a.messaggio,b.username from messaggi a join utente b on a.user_id=b.id where admin_id is null";
        
        //Seleziono la lista delle segnalazioni
        $messaggi=resultQueryToTable($connection->query($query));
        
        $messaggio_collect="<!-- Successivo -->";
        
        $messAct="messaggio_action.php?";
        
        foreach ($messaggi as $messaggio) {
            $link=(string)"http://".$host.$uri."/".$messAct."messaggio_id=".$messaggio["id"]."&elimina=";
            
            $messaggio_collect=preg_replace("/<!-- Successivo -->/i",
                '<tr> '
                .'<td class="nome-utente">'.$messaggio["username"].'</td>'
                .'<td class="messaggio-inviato">'.$messaggio["messaggio"].'</td>'
                .'<td>'
                .'<form class="post-form post-form-supporto" action="messaggio_amministratore_action.php" method="get">'
                .'<div class="post-holder">'
                .'<div class="textarea-wrap">'
                .'<input class="jsonly" name="messaggio_id" title="risposta a messaggio '.$messaggio["id"].'" value="'.$messaggio["id"].'"/>'
                .'<textarea placeholder="inserisci il tuo messaggio" title="scrivi risposta a messaggio '.$messaggio["id"].'" name="messaggio"></textarea>'
                .'</div>'
                .'</div>'
                .'<div class="submit-post">'
                .'<input class="submit-post-btn" type="submit" value="Invia" title="invia risposta a messaggio '.$messaggio["id"].'"/>'
                .'</div>'
                .'</form>'
                .'</td>'
                .'<td class="spazio-eliminazione">'
                .'<a href="'.$link.'true">'
                .'<img src="../img/admin/trash.png" class= "elimina-commento" alt="immagine cestino" title="elimina messaggio '.$messaggio["id"].
                '"></img>'
                .' </a>'
                .'</td>'
                .'</tr>'
                .'<!-- Successivo -->'
                , $messaggio_collect );
        }
                
        
        $messaggio_collect=preg_replace("/<!-- Successivo -->/i","" , $messaggio_collect );
        $amministratore=preg_replace("/<!-- Messaggio -->/i",$messaggio_collect, $amministratore );
        
        
        $output = preg_replace("/<!-- Contenuto_Effettivo -->/i", $amministratore , $output );
        
        return $output;
    }
    
    function printPageFAQ($output){
            
        $host  = $_SERVER['HTTP_HOST'];
        $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        
        $head_page = implode("", file("../txt/pagehead.txt"));
        $faq = implode("", file("../txt/faq.txt"));
        
        $output = preg_replace("/<!-- Nome_Pagina -->/i", "FAQ", $output );
        $output = preg_replace("/<!-- Page_Head -->/i", $head_page, $output );
        $output = preg_replace("/<!-- Contenuto_Effettivo -->/i", $faq , $output );
        
        return $output;
    }
    
    function printPageSupporto($output){
        
        global $connection;
        if(empty($connection))
            connettiDB();
            
        $host  = $_SERVER['HTTP_HOST'];
        
        $head_page = implode("", file("../txt/pagehead.txt"));
        $output = preg_replace("/<!-- Nome_Pagina -->/i", "supporto", $output );
        $output = preg_replace("/<!-- Page_Head -->/i", $head_page, $output );
        $supporto = implode("", file("../txt/supporto.txt"));
        
        if(!array_key_exists('user_username',$_SESSION) && empty($_SESSION['user_username'])){
            $Registrati =
            "<div class=\"iscriviti\">
                    <p>
                        Per accedere alla tua area personale <a href=\"signup.php\" title=\"registrati\">REGISTRATI</a> oppure <a href=\"login.php\" title=\"accedi\">EFFETTUA IL <span lang=\"EN\">LOGIN</span></a>
                    </p>
                </div>";
            
            $output = preg_replace("/<!-- Contenuto_Effettivo -->/i", $Registrati , $output );
            return $output;            
        }            
        
        //parte messaggi
        $query="SELECT messaggio,risposta FROM messaggi where user_id=".$_SESSION['user_id'];
        
        //Seleziono la lista delle segnalazioni
        $messaggi=resultQueryToTable($connection->query($query));
        
        $messaggio_collect="<!-- Successivo -->";
        
        foreach ($messaggi as $messaggio) {
            
            $messaggio_collect=preg_replace("/<!-- Successivo -->/i",
                '<tr> '
                .'<td class="messaggio-inviato">'.$messaggio["messaggio"].'</td>'
                .'<td class="messaggio-inviato">'.$messaggio["risposta"].'</td>'
                .'</tr>'
                .'<!-- Successivo -->'
                , $messaggio_collect );
        }
        
        $messaggio_collect=preg_replace("/<!-- Successivo -->/i","" , $messaggio_collect );
        $supporto=preg_replace("/<!-- Messaggio -->/i",$messaggio_collect, $supporto );
        
        //parte segnalazioni
        
        $query="select a.checked,b.cancellato, b.testo, c.titolo from (segnalazione a join post b on a.id_ref=b.id) join serie c on b.id_serie=c.id where a.id_utente=".$_SESSION['user_id'];
        
        //Seleziono la lista delle segnalazioni
        $segnalazioni=resultQueryToTable($connection->query($query));
        
        $segnalazione_collect="<!-- Successivo -->";
        
        foreach ($segnalazioni as $segnalazione) {
            $azione=$segnalazione["checked"]==$segnalazione["cancellato"]&&$segnalazione["checked"]=="1"?"Cancellato":"Cancellato solo per te";
            $segnalazione_collect=preg_replace("/<!-- Successivo -->/i",
                '<tr> '
                .'<td class="serie">'.$segnalazione["titolo"].'</td>'
                .'<td class="segnalazione">'.$segnalazione["testo"].'</td>'
                .'<td class="azione">'.$azione.'</td>'
                .'</tr>'
                .'<!-- Successivo -->'
                , $segnalazione_collect );
        }
                
        $segnalazione_collect=preg_replace("/<!-- Successivo -->/i","" , $segnalazione_collect );
        $supporto=preg_replace("/<!-- Segnalazione -->/i",$segnalazione_collect, $supporto );
        
        
        $output = preg_replace("/<!-- Contenuto_Effettivo -->/i", $supporto , $output );       
        
        return $output;
    }
    
    
    ?>