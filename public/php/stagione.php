<?php

include_once 'general_function.php';
session_start();
$host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
//get the q parameter from URL
$serie_id=$_GET["serie_id"];
$stagione_numero=$_GET["stagione_numero"];
//lookup all links from the xml file if length of q>0


$hint="";

$query="select b.id, b.titolo,b.descrizione, b.numero,b.data,b.visualizzato from serie a join episodio b on a.id=b.id_serie where b.stagione=?  and a.id=?";

$stmt=executeQuery($query,array(&$stagione_numero,&$serie_id),array("ii"));
$episodi=resultQueryToTable($stmt->get_result());
$stmt->close();

$episodio_collect="<tbody>
                        <tr>
							<th>Numero Episodio</th>
							<th>Nome Episodio</th>
							<th>Data Rilascio</th>
							<th>Visto</th>
						</tr>"."<!-- Successivo -->";

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
$episodio_collect=$episodio_collect."</tbody>";


//output the response
echo $episodio_collect;
?>