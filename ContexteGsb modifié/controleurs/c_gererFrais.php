<?php
/**
* Gestion des frais
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

$idVisiteur = $_SESSION['idUser'];

//Les 3 variables suivantes sont utilisées dans v_listeFraisForfait.php
$mois = getMois(date('d/m/Y'));
$numAnnee = substr($mois, 0, 4);
$numMois = substr($mois, 4, 2);

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);

switch ($action) {
case 'validerMajFraisForfait':
    $lesFraisForfait = filter_input(INPUT_POST, 'lesFraisForfait', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
    if (lesQteFraisValides($lesFraisForfait)) {
        executeThenReturnMessage(
            $pdo->majFraisForfait($idVisiteur, $mois, $lesFraisForfait),
            'Les frais ont bien été mis a jour',
            'Les frais n\'ont pas pu être mis à jour. Veuillez contacter l\'administrateur
            si ce problème persiste.'
        );
    } else {
        ajouterErreur('Les valeurs des frais doivent être numériques');
        include 'vues/v_erreurs.php';
    }
    break;
case 'validerCreationFrais':
    $dateFrais = filter_input(INPUT_POST, 'dateFrais', FILTER_SANITIZE_STRING);
    $libelle = filter_input(INPUT_POST, 'libelle', FILTER_SANITIZE_STRING);
    var_dump($_POST['libelle']);
    $montant = filter_input(INPUT_POST, 'montant', FILTER_VALIDATE_FLOAT);

    valideInfosFrais($dateFrais, $libelle, $montant);
    if (nbErreurs() > 0) {
        include 'vues/v_erreurs.php';
    } else {
        $creerFrais = $pdo->creeNouveauFraisHorsForfait(
            $idVisiteur,
            $mois,
            $libelle,
            $dateFrais,
            $montant
        );
        executeThenReturnMessage(
            $creerFrais,
            'Ce frais hors forfait a bien été ajouté.',
            'Ce frais n\'a pas pu être ajouté. Veuillez contacter l\'administrateur
            si ce problème persiste.'
        );
    }
    break;
case 'supprimerFrais':
    $idFrais = filter_input(INPUT_GET, 'idFrais', FILTER_SANITIZE_STRING);
    executeThenReturnMessage(
        $pdo->supprimerFraisHorsForfait($idFrais),
        'Ce frais hors forfait a bien été suprimé.',
        'Ce frais n\'a pas pu être supprimé. Veuillez contacter l\'administrateur
        si ce problème persiste.'
    );
    break;
default:
    if ($pdo->estPremierFraisMois($idVisiteur, $mois)) {
        $pdo->creeNouvellesLignesFrais($idVisiteur, $mois);
    }
    break;
}

$lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $mois);
$lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $mois);

require 'vues/v_listeFraisForfait.php';
require 'vues/v_listeFraisHorsForfait.php';
