<?php
/**
* Index du projet GSB
*
* PHP Version 7
*
* @category  PPE
* @package   GSB
* @author    Réseau CERTA <contact@reseaucerta.org>
* @author    José GIL <jgil@ac-nice.fr>
* @copyright 2017 Réseau CERTA
* @license   Réseau CERTA
* @version   GIT: <0>
* @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
*/

require_once 'includes/fct.inc.php';
require_once 'includes/class.pdogsb.inc.php';

session_start();

$pdo = PdoGsb::getPdoGsb();
$estConnecte = estConnecte();

if(estConnecte()){
    $pageUserList = $pdo->getPageUserList($_SESSION['idTypeUser']);
}

require 'vues/v_entete.php';

//uc est la page qui sera présentée
$uc = filter_input(INPUT_GET, 'uc', FILTER_SANITIZE_STRING);

if (!$estConnecte) { //si l'utilisateur n'est pas connecté
    $uc = 'connexion';
} elseif (empty($uc)) {
    $uc = 'accueil';
}

switch ($uc) {
//GENERAL
case 'connexion':
    include 'controleurs/c_connexion.php';
    break;
case 'deconnexion':
    include 'controleurs/c_deconnexion.php';
    break;
case 'accueil':
    include 'controleurs/c_accueil.php';
    break;
case 'help':
    include 'vues/v_help.php';
    break;
default:
    $idTypeUser = $_SESSION['idTypeUser'];
    
    $isPageAcessible = $pdo->isPageAccessibleForUserType($idTypeUser, $uc);
    
    if($isPageAcessible){
        //si la page est une page spécifique au type de l'utilisateur
        $include = 'controleurs/c_' . $uc . '.php';
        if(file_exists($include)){
            include $include;
        } else {
            ajouterErreur('Cette page n\'est pas encore disponible');
            include 'vues/v_erreurs.php';
        }
    } else {
        ajouterErreur('Cette page n\'est pas disponible');
        include 'vues/v_erreurs.php';
    }
}

require 'vues/v_pied.php';
