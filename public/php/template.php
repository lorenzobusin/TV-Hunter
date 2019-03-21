<?php 
    include_once 'general_function.php';

    //Parte standard per tutte le pagine
    session_start();
    function createPage($namePage){
        readfile('../txt/head.txt');
        echo printNavbar($namePage);

        
        
        $file_content = implode("",file("../txt/pagecenter.txt"));
        $host  = $_SERVER['HTTP_HOST'];
        $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        $footer = implode("",file("../txt/footer.txt"));
        
        if(array_key_exists('user_username',$_SESSION) && !empty($_SESSION['user_username'])) {
            $extra="logout_action.php";
            $output = preg_replace("/<!-- Nome -->/i",'<a href="#">'.$_SESSION["user_username"].'</a>', $file_content);//TODO: aggiungere il link al profilo            
            $output = preg_replace("/<!-- Log -->/i", "Logout", $output );
            $output = preg_replace("/<!-- Url_Log -->/i", "http://$host$uri/$extra", $output );
        }else{
            $extra="login.php";
            $output = preg_replace("/<!-- Nome -->/i","Accesso non effettuato.", $file_content);            
            $output = preg_replace("/<!-- Log -->/i", "Login", $output );
            $output = preg_replace("/<!-- Url_Log -->/i", "http://$host$uri/$extra", $output );
            
        }

        if(array_key_exists('user_tipo',$_SESSION) && !empty($_SESSION['user_tipo']) && $_SESSION['user_tipo']=="admin"){
            $footer = preg_replace("/<!-- Amministrazione -->/i", '<li refAdmin><a href="amministrazione.php" title="Amministrazione">Amministrazione</a></li>', $footer);
        }

        //Parte personalizzata per tutte le pagine
        $script="";

        switch ($namePage) {
            case "esplora":
                $output = preg_replace("/<!-- Link_Navigazione -->/i",'<a class="path" href="home.php">Home</a><span>&#x27AA;</span><a href="#" class="selezionato path">Esplora</a>', $output);
                $footer = preg_replace("/refEsplora/i", "class=\"selezionato\"", $footer );
                echo printPageEsplora($output);
            break;
            
            case "serie":
                $output = preg_replace("/<!-- Link_Navigazione -->/i",'<a class="path" href="home.php">Home</a><span>&#x27AA;</span><a href="esplora.php" class="path">Esplora</a><span>&#x27AA;</span><a href="serie.php" class="path">Serie</a>', $output);
                echo printPageSerie($output);
                $script = implode("",file("../javascript/serie.js"));                
                break;
                
            case "about":
                $output = preg_replace("/<!-- Link_Navigazione -->/i",'<a class="path" href="home.php">Home</a><span>&#x27AA;</span><a href="#" class="path">Esplora</a>', $output);                
                $footer = preg_replace("/refAbout/i", "class=\"selezionato\"", $footer );
                echo printPageAbout($output);
                break;

            case "home":
                $output = preg_replace("/<!-- Link_Navigazione -->/i",'<a class="path" href="#">Home</a>', $output);                
                echo printPageHome($output);
                break;

            case "privacy":
                $output = preg_replace("/<!-- Link_Navigazione -->/i",'<a class="path" href="home.php">Home</a><span>&#x27AA;</span><a href="#" class="path">Privacy</a>', $output);
                $footer = preg_replace("/refPrivacy/i", "class=\"selezionato\"", $footer );
                echo printPagePrivacy($output);
                break;

            case "login":
                $output = preg_replace("/<!-- Link_Navigazione -->/i",'<a class="path" href="home.php">Home</a><span>&#x27AA;</span><a href="#" class="path">Login</a>', $output);                
                echo printPageLogin($output);
                break;

            case "signup":
                $output = preg_replace("/<!-- Link_Navigazione -->/i",'<a class="path" href="home.php">Home</a><span>&#x27AA;</span><a href="#" class="path">Signup</a>', $output);                
                echo printPageSignUp($output);
                $script = implode("",file("../javascript/valida_form_signup.js"));                
                break;

            case "preferiti":
                $output = preg_replace("/<!-- Link_Navigazione -->/i",'<a class="path" href="home.php">Home</a><span>&#x27AA;</span><a href="#" class="path">Preferiti</a>', $output);                
                $footer = preg_replace("/refPreferiti/i", "class=\"selezionato\"", $footer );
                echo printPagePreferiti($output);
                break;

             case "profilo":
                 $output = preg_replace("/<!-- Link_Navigazione -->/i",'<a class="path" href="home.php">Home</a><span>&#x27AA;</span><a href="#" class="path">Profilo</a>', $output);
                $footer = preg_replace("/refProfilo/i", "class=\"selezionato\"", $footer );
                echo printPageProfilo($output);
                break;

             case "genere":
                 $output = preg_replace("/<!-- Link_Navigazione -->/i",'<a class="path" href="home.php">Home</a><span>&#x27AA;</span><a href="esplora.php" class="path">Esplora</a><span>&#x27AA;</span><a href="#" class="path">Genere</a>', $output);
                 echo printPageGenere($output);
                 break;
             
             case "ricerca":
                 $output = preg_replace("/<!-- Link_Navigazione -->/i",'<a class="path" href="home.php">Home</a><span>&#x27AA;</span><a href="esplora.php" class="path">Esplora</a><span>&#x27AA;</span><a href="#" class="path">Genere</a>', $output);
                 echo printPageRicerca($output);
                 break;  
                 
             case "amministrazione":
                 $output = preg_replace("/<!-- Link_Navigazione -->/i",'<a class="path" href="home.php">Home</a><span>&#x27AA;</span><a href="#" class="path">Amministrazione</a>', $output);                 
                 $footer = preg_replace("/refAdmin/i", "class=\"selezionato\"", $footer );
                 echo printPageAmministrazione($output);
                 break;
             case "faq":
                 $output = preg_replace("/<!-- Link_Navigazione -->/i",'<a class="path" href="home.php">Home</a><span>&#x27AA;</span><a href="#" class="path">FAQ</a>', $output);                 
                 $footer = preg_replace("/refFaq/i", "class=\"selezionato\"", $footer );
                 echo printPageFAQ($output);
                 break;
                 
            case "404":
                 echo printPage404($output);
                 break;
                 
            case "supporto":
                $output = preg_replace("/<!-- Link_Navigazione -->/i",'<a class="path" href="home.php">Home</a><span>&#x27AA;</span><a href="#" class="path">Supporto</a>', $output);                
                $footer = preg_replace("/refSupporto/i", "class=\"selezionato\"", $footer );
                echo printPageSupporto($output);
                break;
                

            default:
                
            break;
        }
        
        
        //Parte standard per tutte le pagine
        $footer = preg_replace("/refAbout/i", "", $footer );
        $footer = preg_replace("/refPrivacy/i", "", $footer );
        $footer = preg_replace("/refFaq/i", "", $footer );
        $footer = preg_replace("/refSupporto/i", "", $footer );
        $footer = preg_replace("/refEsplora/i", "", $footer );
        $footer = preg_replace("/refProfilo/i", "", $footer );
        $footer = preg_replace("/refPreferiti/i", "", $footer );
        $footer = preg_replace("/refAdmin/i", "", $footer );       
        $footer = preg_replace("/<!-- Script -->/i", $script, $footer );
        
        echo $footer;
    }
?>