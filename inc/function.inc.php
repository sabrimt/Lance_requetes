<?php
require_once 'init.inc.php';
//Fonction d'exécution de requêtes 
function execute_requete($req)
{
  global $mysqli;
  //global $bdd;

  if(isset($_POST['db']))
  {
  $mysqli->select_db($_POST['db']);
  }

  $resultat = $mysqli->query($req);
  
  return $resultat; // on retourne le résultat de la requete
}