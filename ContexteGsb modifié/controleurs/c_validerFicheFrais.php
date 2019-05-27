<?php
/**
 * Validation des frais
 *
 * PHP Version 7
 */

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);

$idComptable = $_SESSION['idUser'];

$idVisiteur = filter_input(INPUT_POST, 'idVisiteur', FILTER_SANITIZE_STRING);
$mois = filter_input(INPUT_POST, 'mois', FILTER_SANITIZE_STRING); //reçu après avoir cliqué sur "Valider"
$numAnnee = substr($mois, 0, 4);
$numMois = substr($mois, 4, 2);

//--VUE
$lesVisiteurs = $pdo->getVisiteurList();
include 'vues/v_validerFicheFrais.php';
//--END VUE

switch ($action) {
    case 'afficherFiche':
        if(empty($mois) || empty($idVisiteur)) {
            ajouterErreur('Veuillez choisir un visiteur et/ou un mois.');
            include 'vues/v_erreurs.php';
        }
        break;

    case 'majFraisForfait':
        $lesFraisForfait = filter_input(INPUT_POST, 'lesFraisForfait', FILTER_DEFAULT, FILTER_FORCE_ARRAY);

        if (lesQteFraisValides($lesFraisForfait)) {
            executeThenReturnMessage(
                $pdo->majFraisForfait($idVisiteur, $mois, $lesFraisForfait),
                "Les frais ont bien été mis à jour",
                'Un problème est survenu durant la mise à jour des frais forfaits'
            );
        } else {
            ajouterErreur('Les valeurs des frais doivent être numériques');
            include 'vues/v_erreurs.php';
        }

        break;

    case 'majFraisHorsForfait':
        $idFraisHorsForfait = filter_input(INPUT_POST, 'idFraisHorsForfait',
                FILTER_DEFAULT, FILTER_SANITIZE_STRING);
        //La variable "$majFraisHorsFaitButton" récupère la valeur du bouton pressé, ce qui
        //permet de déterminer l'action à exécuter
        $majFraisHorsFaitButton = filter_input(INPUT_POST, 'submit',
                FILTER_DEFAULT, FILTER_SANITIZE_STRING);
        switch ($majFraisHorsFaitButton){
            case 'reporter':
                $nextMois = getNextMonth($mois);

                if($pdo->estPremierFraisMois($idVisiteur, $nextMois)){
                    //L'affiche du mois suivant n'existe pas :on en crée une
                     $pdo->creeNouvellesLignesFrais($idVisiteur, $nextMois);
                }
                executeThenReturnMessage(
                    $pdo->majReportFraisHorsForfait($idFraisHorsForfait, $nextMois),
                    'Ce frais hors forfait a bien été reporté au mois suivant.',
                    'Erreur: ce frais n\'a pas pu être reporté. Veuillez contacter l\'administrateur
                    si ce problème persiste.'
                );
                break;

            case 'refuser':
                executeThenReturnMessage(
                    $pdo->majRefusFraisHorsForfait($idFraisHorsForfait, true),
                    'Ce frais hors forfait a bien été marqué comme "REFUSE".',
                    'Erreur: ce frais n\'a pas pu être marqué à "REFUSE". Veuillez contacter l\'administrateur
                    si ce problème persiste.'
                );
                break;
            case 'annulerRefus':
                executeThenReturnMessage(
                    $pdo->majRefusFraisHorsForfait($idFraisHorsForfait, false),
                    'Ce frais hors forfait n\'est plus marqué comme "REFUSE".',
                    'Erreur: la mise à jour a échoué. Veuillez contacter l\'administrateur
                    si ce problème persiste.'
                );
                break;
            default:
                ajouterErreur('Erreur: aucune action n\'a pas pu se réaliser');
                include 'vues/v_erreurs.php';
                break;
        }

        break;

    case 'majNbJustificatif':
        $nbJustificatifs = filter_input(INPUT_POST, 'nbJustificatifs', FILTER_DEFAULT, FILTER_SANITIZE_STRING);

        if(is_numeric($nbJustificatifs)){
            $nbJustificatifs = intval($nbJustificatifs);
            executeThenReturnMessage(
                $pdo->majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs),
                'Le nombre de justificatifs a bien était mis à jour.',
                'Un problème est survenu durant la mise à jour du nombre de
                justificatifs. Veuillez contacter l\'administrateur si ce problème
                persiste.'
            );
        } else {
             ajouterErreur('Le champ "justificatifs" doit être numérique.');
                include 'vues/v_erreurs.php';
        }
        break;

    case 'validerFiche':
        $montant = $pdo->getMontantTotalFicheFrais($idVisiteur, $mois);
        executeThenReturnMessage(
            validerFicheFrais($pdo, $idVisiteur, $mois, $montant),
            'La fiche a bien été validée.',
            'Un problème est survenu durant la validation de cette fiche.
            Veuillez contacter l\'administrateur si ce problème persiste.'
        );

        include 'vues/v_pied.php';
        exit(); //on ne veut pas executer la suite du code après la validation d'une fiche
        break;

    default:
        //--Met à jour toutes les fiches du dernier mois à l'état "Cloturé".
        $moisCourrant = getMois(date('d/m/Y'));
        foreach($lesVisiteurs as $visiteur){
            $idVisiteur = $visiteur['id'];
            if ($pdo->estPremierFraisMois($idVisiteur, $moisCourrant)) {
                //il n'y a pas encore de frais pour le mois courant
				//Cela signifie que le visiteur n'a pas encore saisi de fiche pour ce 
				//mois.
				//Or, lorsqu'il saisi une nouvelle fiche, ses fiches précédentes sont 
				//cloturées par un script, donc pas besoin de les clôturer ici, d'où
				//cette condition : "s'il n'a pas saisi encore de fiche (celle-ci ne
				//sont pas clôturées), alors on va les clôturer maintenant".
                $dernierMois = $pdo->dernierMoisSaisi($idVisiteur);
                $laDerniereFiche = $pdo->getLesInfosFicheFrais($idVisiteur, $dernierMois);
                if ($laDerniereFiche['idEtat'] == 'CR') {
                    $pdo->majEtatFicheFrais($idVisiteur, $dernierMois, 'CL');
                }
            }
        }
        //--FIN SCRIPT
        break;
}


if(!empty($mois) && !empty($idVisiteur)){

    $ficheFrais = $pdo->getLesInfosFicheFrais($idVisiteur, $mois);

    if($ficheFrais['idEtat'] == 'CL'){
        //on contrôle que la fiche est bien cloturé
        $lesFraisForfait = $pdo->getLesFraisForfait($idVisiteur, $mois);
        $lesFraisHorsForfait = $pdo->getLesFraisHorsForfait($idVisiteur, $mois);

        $libEtat = $ficheFrais['libEtat'];
        $montantValide = $ficheFrais['montantValide'];
        $nbJustificatifs = $ficheFrais['nbJustificatifs'];
        $dateModif = dateAnglaisVersFrancais($ficheFrais['dateModif']);


        include 'vues/v_formValidationFrais.php';
    } else {
        ajouterErreur('La fiche sélectionnée a déjà été validée, remboursée '
                . 'ou est en cours de saisie.');
        include 'vues/v_erreurs.php';
    }
}

//---------------FONCTIONS-----------------//

/**
 * Valide une fiche de frais. Passe son état à VA et valorise le montant valide.
 *
 * @param PDO   $pdo          Connexion à la base.
 * @param String $idVisiteur  L'id du visiteur à valider
 * @param String $mois        Mois de la fiche à valider
 * @param Integer $montant    Montant validé
 */
function validerFicheFrais($pdo, $idVisiteur, $mois, $montant){
    if($pdo->majEtatFicheFrais($idVisiteur, $mois, 'VA') &&
    $pdo->majMontantValideFicheFrais($idVisiteur, $mois, $montant)){
        return true;
    }
    return false;

}
