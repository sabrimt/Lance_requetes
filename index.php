<?php
require_once ('inc/init.inc.php');
include ('inc/function.inc.php');

// Pour l'affichage des BDD
$bdd = $mysqli->query("SHOW DATABASES");

$tables = execute_requete("SHOW TABLES");

// 7.1 Lancement d'une requete à la BDD

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
  header("location:lance_requetes.php");

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

    <style>
      .historique{
        max-height: 100px;
        display: block;
        overflow-y: auto;
        
      }


    </style>
    
  </head>
  <body>
    <h1 style="text-align: center; background-color: lightskyblue;">Lance requêtes: Sabri MTIR</h1>
    <div class="container col-sm-10 col-sm-offset-1">
      <div class="row">
        <div class="col-sm-8 col-sm-offset-2">
          <form method="POST" action="">
            <div class="form-group">

              <!-- Selecteur BDD -->
              <label for="db">Base de données :</label>
              <select name="db" class="form-control" style="width: 20% ; display: inline-block; margin-left: 20px;">
              <?php

              // Boucle de récupération des BDD
              while($base=$bdd->fetch_assoc())
              {
                if($base['Database'] != 'mysql' && $base['Database'] != 'information_schema' && $base['Database'] != 'test' && $base['Database'] != 'performance_schema' && $base['Database'] != 'phpmyadmin' && $base['Database'] != 'sys')
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
              }
              
              ?>
              </select><hr />

              <!-- Champ requete -->
              <label for="requete">Lance requêtes :</label>
              <textarea class="form-control" rows="3" id="requete" name="requete" placeholder="Ici, votre requête..."><?php 
              if(isset($_GET['action']) && $_GET['action'] == 'reprendre')
              {
                echo $_GET['req'];

              }elseif(isset($_POST['requete']))
                {
                  echo $_POST['requete'];
                }
              ?></textarea>

              <input type="submit" class="form-control btn btn-info" id="envoi" name="envoi" value="Envoyer" /><hr />

            </div>
          </form>
        </div>
      </div> <!-- Row -->

      <div class="row"><!-- Espace historique -->
        <div class="col-sm-8 col-sm-offset-2">
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
                <div class="col-sm-4 col-sm-offset-8">

                  <a href="lance_requetes.php?action=supprimer_hist" onclick="return(confirm('Etes vous sur ?'))" class="btn btn-danger" style="margin-left: 0 auto; display: block;">Supprimer l'historique</a>

                </div>
              </div>
            </div>
          </div>
        </div>
      </div><!-- Fin historique -->


<!-- 7.2 Affichage du résultat -->
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
                echo '<div style="background-color:lightcoral; width:100%; text-align: center;">erreur sur la requete: ' . $_POST['requete'] . '<br />Message: ' . $mysqli->error . '</div><hr/>';
                if(empty($_POST['requete']))
                {
                  echo '<div style="background-color:lightcoral; width:100%; text-align: center;">Vous devez entrer une requete</div><hr/>';
                }

              }else{

                echo '<div style="background-color: darkseagreen; width: 100%; text-align: center;">Voici le résultat de votre requête:</div>';
                echo '<br /><p>Requête: ' . $_POST['requete'] . '</p></h3>';
                echo '<br /><p>Base de données: ' . $_POST['db'] . '</p>';
                echo '<p>Lignes concernées: ' . $requete_entree->num_rows . '</p>';
                echo '<p>Pour information, voici les tables de cette base de données:<br/>';

                // Envoi historiique sur un fichier txt
                if(file_exists('historique.txt'))
                {
                  fwrite($f, '<a href="?action=reprendre&db=' . $_POST['db'] . '&req=' . $_POST['requete'] . '">' . $_POST['db'] . ' => ' . $_POST['requete'] . '</a><br />');
                }


                
                // Affichage des tables
                while($table=$tables->fetch_assoc())
                {
                  
                    echo $table["Tables_in_" . "$_POST[db]"];echo '<br/> ';
                    /*echo '<pre>';echo print_r($table);echo '</pre>';*/
                  
                }

              }
              ?>

              </h3>
            </div>
            <div class="panel-body">
              <table class="table table-hover" border="1" style="border-collapse: collapse; width: 100%;">
                <tr>
                <?php

                if(isset($_POST['requete']) && (isset($_POST['db'])) && !empty($_POST['requete']) && $requete_entree)
                {
                  while($colonne = $requete_entree->fetch_field()) // recup du nom des colonnes
                  {
                    echo '<th style="text-align: center; background-color: seagreen;">' . $colonne->name . '</th>';
                  } 
  

                  echo '</tr>';
                  $c = 0; // compteur modulo
                  while($employe = $requete_entree->fetch_assoc()) // recup des ligne de valeurs
                  {
                    // affichage des lignes en couleur differentes
                    if($c % 2 == 0){ 

                      echo '<tr style="background-color: beige;">';
                    }else{
                      echo '<tr>';
                    }
                    $c++;

                    
                    foreach($employe AS /*$indice =>*/ $valeur) // affichage des valeurs
                    {
                      
                      echo '<td style="padding: 4px; text-align: center;">' . $valeur . '</td>';
                      
                    }
                      
                  }
                }
                ?>


              </table>
            </div>
          </div>
        </div>
      </div>
      <?php } 
      

      ?>

    </div><!-- Fin container-->






    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    
  </body>
</html>