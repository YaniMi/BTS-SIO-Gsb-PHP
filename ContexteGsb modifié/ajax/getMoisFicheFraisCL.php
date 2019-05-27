<?php
/**
 * Ajax : renvoit les mois des fiches d'un visiteur
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */

include '../includes/class.pdogsb.inc.php';

$pdo = PdoGsb::getPdoGsb();

$idVisiteur = filter_input(INPUT_GET, 'idVisiteur', FILTER_SANITIZE_STRING);

$lesMois = $pdo->getLesMoisDisponibles($idVisiteur);

$lesMoisCL = array(); //les mois des fiches cloturées

foreach($lesMois as $leMois){
    $mois = $leMois['mois'];
    $ficheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $mois);
    $idEtat = $ficheFrais['idEtat'];
    if($idEtat == 'CL') {
        $lesMoisCL[] = $leMois;
    }
}

if(!empty($lesMoisCL)){
    echo json_encode($lesMoisCL);
} else {
    echo 'Aucun fiche en cours de saisi';
}
