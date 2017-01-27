<?php
require_once ('inc/init.inc.php');
include ('inc/function.inc.php');

// Pour l'affichage des BDD
$bdd = $mysqli->query("SHOW DATABASES");

$tables = execute_requete("SHOW TABLES");

// Lancement d'une requete à la BDD

$requete_entree ="";
if(isset($_POST['requete']) && (isset($_POST['db'])) && !empty($_POST['requete']))
{
    $requete_entree = execute_requete($_POST['requete']);// execution de requete

    $_POST['requete'] = htmlentities($_POST['requete'], ENT_QUOTES);
    $_POST['db'] = htmlentities($_POST['db'], ENT_QUOTES);


    //$employes = $requete_entree->fetch_assoc();

    //echo $employes;
}
//$resultat = $mysqli->query("SELECT * FROM employes WHERE id_employes=350");


/*GESTION FICHIER HISTORIQUE*/

if(isset($_POST['requete']) && isset($_POST['db']) && ($requete_entree))
{
  // Création d'un fichier historique.txt
  $f = fopen("historique.txt", "a");
}

// Suppression historique
if(isset($_GET['action']) && ($_GET['action'] == 'supprimer_hist'))
{
  unlink("historique.txt");
  header("location:index.php");

}

/*RECUPERATION DES REQUETES DS L'HISTORIQUE*/
if(isset($_GET['action']) && $_GET['action'] == 'reprendre')
{
    $bdd_recup = $_GET['db'];// recuperation de base de données depuis historique
    $req_recup = $_GET['req'];// recuperation de requête depuis historique
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Lance requetes</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <script src="https://code.jquery.com/jquery-3.1.1.js" integrity="sha256-16cdPddA6VdVInumRGo6IbivbERE8p7CQR3HzTBuELA=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/style.css"/>
</head>
<body>
    <header>
        <h1><span class="glyphicon glyphicon-th-large"></span>Lance requêtes</h1>
    </header>
    <div class="container col-sm-10 col-sm-offset-1">
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1">
                <div class="row">
                    <form method="POST" action="index.php">
                        <div class="form-group">
                            <div class="db-query-block col-sm-7">
                                <!-- Selecteur BDD -->
                                <div class="db-block">
                                    <label for="db">Base de données :</label>
                                    <select name="db" class="db-form form-control">
                                        <?php
                                        // Boucle de récupération des BDD
                                        while($base=$bdd->fetch_assoc())
                                        {
                                            echo '<option';
                                            if(isset($_GET['action']) && $_GET['action'] == 'reprendre' && $_GET['db'] == $base['Database'])
                                            {
                                                echo ' selected';
                                                
                                            }elseif(isset($_POST['db']) && ($_POST['db'])== $base['Database'])
                                            {
                                             echo ' selected';
                                            }
                                            echo '>' . $base['Database'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div><!-- End BDD -->

                                <!-- Champ requete -->
                                <div class="query-block">
                                    <label for="requete">Lance requêtes :</label>
                                    <textarea class="form-control" rows="5" id="requete" name="requete" placeholder="Ici, votre requête..."><?php
                                    if(isset($_GET['action']) && $_GET['action'] == 'reprendre')
                                    {
                                    echo $_GET['req'];
                                    }elseif(isset($_POST['requete']))
                                    {
                                        echo $_POST['requete'];
                                    }
                                    ?></textarea>
                                    <input type="submit" class="form-control btn btn-info" id="envoi" name="envoi" value="Envoyer" />
                                </div><!-- End requete -->
                            </div>
                            <div class="historique-block col-sm-5">
                                <h5>Historique :</h5>
                                <div class="panel panel-default">
                                    <div class="panel-body historique">
                                    <?php
                                    // inclusion du contenu du fichier historique
                                    if(file_exists('historique.txt'))
                                    {
                                      include('historique.txt');

                                    }else{

                                      echo '<p>Aucune requête mémorisée...</p>';
                                    }
                                    ?>
                                    </div>
                                    <div class="panel-footer">
                                        <div class="row">
                                            <div class="col-sm-9 col-sm-offset-3">
                                                <a href="index.php?action=supprimer_hist" onclick="return(confirm('Etes vous sur ?'))" class="btn btn-danger">Vider l'historique</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div><!-- Row -->
            </div>
        </div> <!-- Row -->

<!-- Affichage du résultat -->
        <?php
        if(isset($_POST['requete']))
        { ?>
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                        <?php
                        if(!$requete_entree)
                        {
                            if(empty($_POST['requete']))
                            {
                                echo '<div class="error">Vous devez entrer une requete</div>';
                            } else {
                                echo '<div class="error">erreur sur la requete: ' . $_POST['requete'] . '<br />Message: ' . $mysqli->error . '</div>';
                            }
                        }else{
                            echo '<div class="success">Résultat de la requête</div>';
                            if (($requete_entree !== FALSE) && ($mysqli->affected_rows>0))
                            {
                                echo '<div class="col-sm-12"><h5 style="color: #4BB5C1;">Votre requête a bien été enregistrée</h5></div>';
                            } else
                            {
                                echo '<div class="col-sm-12"><h5 style="color: #E89393;">Aucune ligne affectée par la requête ou l\'entrée que vous tentez de supprimer n\'existe pas</h5></div>';
                            }
                            echo '<div class="header-req col-sm-6">';
                            echo '<b>Requête: </b><p>' . $_POST['requete'] . '</p>';
                            echo '<b>Base de données: </b><p>' . $_POST['db'] . '</p>';
                            echo '<b>Lignes concernées: </b><p>' . $mysqli->affected_rows . '</p>';// ou $requete_entree->num_rows qui ne fonctionnera pas pour un booléen(ex: retour résultat de insert, delete, update...)   
                            
                            echo '</div>';
                            echo '<div class="header-tables col-sm-6"><b>Pour information, voici les tables de cette base de données:</b><div class="tables-block">';

                            // Envoi historiique sur un fichier txt
                            if(file_exists('historique.txt'))
                            {
                                fwrite($f, '<p>¤¤ <a href="?action=reprendre&db=' . $_POST['db'] . '&req=' . $_POST['requete'] . '">' . $_POST['db'] . ' => ' . $_POST['requete'] . '</a> ¤¤</p>');
                            }

                            // Affichage des tables
                            while($table=$tables->fetch_assoc())
                            {        
                             echo '<p><span class="glyphicon glyphicon-chevron-right"></span>' . $table["Tables_in_" . "$_POST[db]"];echo '</p>';
                            /*echo '<pre>';echo print_r($table);echo '</pre>';*/
                            }
                        }
                        ?>
                                </div>
                            </div>
                        </h3>
                    </div>
                    </div>
                    <?php
                    if (is_object($requete_entree))
                    {
                    ?>
                    <div class="row">
                    <div class="panel-body col-sm-12">
                        <table class="table table-hover" border="1">
                            <tr>
                            <?php

                            if(isset($_POST['requete']) && (isset($_POST['db'])) && !empty($_POST['requete']) && $requete_entree)
                            {
                                while($colonne = $requete_entree->fetch_field()) // recup du nom des colonnes
                                {
                                    echo '<th>' . $colonne->name . '</th>';
                                } 
                                echo '</tr>';
                                $c = 0; // compteur modulo
                                while($result = $requete_entree->fetch_assoc()) // recup des ligne de valeurs
                                {
                                    // affichage des lignes en couleurs differentes
                                    if($c % 2 == 0){ 

                                        echo '<tr style="background-color: #F5F5F5;">';
                                    }else{
                                        echo '<tr>';
                                    }
                                    $c++;

                                    foreach($result AS /*$indice =>*/ $valeur) // affichage des valeurs
                                    {
                      
                                        echo '<td style="padding: 4px; text-align: center;">' . $valeur . '</td>';
                      
                                    }
                                }
                            }
                            ?>
                        </table>
                    </div>
                </div>
                <?php
                }
                ?>
            </div>
        </div>
        <?php }
        ?>

    </div><!-- Fin container-->






    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    
    </body>
</html>