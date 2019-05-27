<?php
/**
 * Suivi du paiment des fraits
 *
 * PHP Version 7
 */

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);

$idVisiteur = filter_input(INPUT_POST, 'idVisiteur', FILTER_SANITIZE_STRING);
$mois = filter_input(INPUT_POST, 'mois', FILTER_SANITIZE_STRING);

switch ($action) {
    case 'afficherFiche':
        $idVisiteur = filter_input(INPUT_GET, 'idVisiteur', FILTER_SANITIZE_STRING);
        $mois = filter_input(INPUT_GET, 'mois', FILTER_SANITIZE_STRING);
        
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $mois);
        $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $mois);
        $lesInfosFicheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $mois);
        $libEtat = $lesInfosFicheFrais['libEtat'];
        $montantValide = $lesInfosFicheFrais['montantValide'];
        $nbJustificatifs = $lesInfosFicheFrais['nbJustificatifs'];
        $dateModif = dateAnglaisVersFrancais($lesInfosFicheFrais['dateModif']);
        
        $idEtat = $lesInfosFicheFrais['idEtat'];
        
        include 'vues/v_suivreFicheFrais_afficherFiche.php';
        
        break;
    case 'majFiche':
        $ficheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $mois);
        $ficheEtat = $ficheFrais['idEtat'];
        
        echo '<a href="/index.php?uc=suivreFicheFrais">Retour à la liste des fiches</a>';
        if($ficheEtat == 'VA'){
            executeThenReturnMessage(
                $pdo->majEtatFicheFrais($idVisiteur, $mois, 'PM'),
                'La fiche a bien été mise en paiment',
                'La fiche n\'a pas pu être mise à jour'
            );
        } elseif($ficheEtat == 'PM') {
            executeThenReturnMessage(
                $pdo->majEtatFicheFrais($idVisiteur, $mois, 'RB'),
                'La fiche a bien été indiquée comme remboursée',
                'La fiche n\'a pas pu être mise à jour'
            );
            
        }
        
        break;
    default:
        //afficher toutes les fiches "validées" et "mises en paiement" avec distinctions pour les deux types

        $ficheFraisListVA = $pdo->getFicheFraisListForIdEtat('VA');
        $ficheFraisListMP = $pdo->getFicheFraisListForIdEtat('PM');
        $ficheFraisList = array_merge($ficheFraisListVA, $ficheFraisListMP);
        //La variable "$sortButton" récupère la valeur du bouton pressé, ce qui
        //permet de déterminer l'action à exécuter
        $sortButton = filter_input(INPUT_POST, 'submit',
                FILTER_DEFAULT, FILTER_SANITIZE_STRING);
        switch ($sortButton){
            case 'mois': //tri par date
                array_multisort (array_column($ficheFraisList, 'mois'), SORT_ASC, $ficheFraisList);
                break;
            case 'prenom':
                array_multisort (array_column($ficheFraisList, 'prenom'), SORT_ASC, $ficheFraisList);
                break;
            case 'nom':
                array_multisort (array_column($ficheFraisList, 'nom'), SORT_ASC, $ficheFraisList);
                break;
            case 'montantvalide':
                array_multisort (array_column($ficheFraisList, 'montantvalide'), SORT_ASC, $ficheFraisList);
                break;
            case 'etatlibelle':
                array_multisort (array_column($ficheFraisList, 'etatlibelle'), SORT_ASC, $ficheFraisList);
                break;
        }

        include 'vues/v_suivreFicheFrais.php';

        break;
}
