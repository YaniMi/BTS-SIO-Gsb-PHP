<?php
/**
 * Fonctions pour l'application GSB
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <0>
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */

/**
 * Teste si un quelconque utilisateur est connecté
 *
 * @return vrai ou faux
 */
function estConnecte()
{
    if(isset($_SESSION['idUser'])){
        return true;
    } else {
        return false;
    }
}

/**
 * Enregistre dans une variable session les infos d'un utilisateur
 *
 * @param String $idUser    ID de l'utilisateur
 * @param String $nom       Nom de l'utilisateur
 * @param String $prenom    Prénom de l'utilisateur
 *
 * @return null
 */
function connecter($idUser, $nom, $prenom, $idTypeUser)
{
    $_SESSION['idUser'] = $idUser;
    $_SESSION['nom'] = $nom;
    $_SESSION['prenom'] = $prenom;
    $_SESSION['idTypeUser'] = $idTypeUser;
}

/**
 * Détruit la session active
 *
 * @return null
 */
function deconnecter()
{
    session_destroy();
}

/**
 * Transforme une date au format français jj/mm/aaaa vers le format anglais
 * aaaa-mm-jj
 *
 * @param String $dateFR au format  jj/mm/aaaa
 *
 * @return Date au format anglais aaaa-mm-jj
 */
function dateFrancaisVersAnglais($dateFR)
{
    return date("Y-m-d", strtotime($dateFR));
}

/**
 * Transforme une date au format format anglais aaaa-mm-jj vers le format
 * français jj/mm/aaaa
 *
 * @param String $dateEN au format  aaaa-mm-jj
 *
 * @return Date au format format français jj/mm/aaaa
 */
function dateAnglaisVersFrancais($dateEN)
{
    return date("d-m-Y", strtotime($dateEN));
}

/**
 * Retourne le mois au format aaaamm selon le jour dans le mois
 *
 * @param String $date au format  jj/mm/aaaa
 *
 * @return String Mois au format aaaamm
 */
function getMois($date)
{
    @list($jour, $mois, $annee) = explode('/', $date);
    unset($jour);
    if (strlen($mois) == 1) {
        $mois = '0' . $mois;
    }
    return $annee . $mois;
}

/**
 * Retourne la date aaaamm du mois suivant.
 *
 * @param String $mois  Le mois au format aaaamm
 *
 * @return String       Le mois suivant au format aaaamm
 */
 function getNextMonth($mois){
     $numAnnee = substr($mois, 0, 4);
     $numMois = substr($mois, 4, 2);
     if ($numMois == '12') {
         $numMois = '01';
         $numAnnee++;
     } else {
         $numMois++;
     }
     if (strlen($numMois) == 1) {
         $numMois = '0' . $numMois;
     }
     return $numAnnee . $numMois;
 }

/* gestion des erreurs */

/**
 * Indique si une valeur est un entier positif ou nul
 *
 * @param Integer $valeur Valeur
 *
 * @return Boolean vrai ou faux
 */
function estEntierPositif($valeur)
{
    return preg_match('/[^0-9]/', $valeur) == 0;
}

/**
 * Indique si un tableau de valeurs est constitué d'entiers positifs ou nuls
 *
 * @param Array $tabEntiers Un tableau d'entier
 *
 * @return Boolean vrai ou faux
 */
function estTableauEntiers($tabEntiers)
{
    $boolReturn = true;
    foreach ($tabEntiers as $unEntier) {
        if (!estEntierPositif($unEntier)) {
            $boolReturn = false;
        }
    }
    return $boolReturn;
}

/**
 * Vérifie si une date est inférieure d'un an à la date actuelle
 *
 * @param String $dateTestee Date à tester
 *
 * @return Boolean vrai ou faux
 */
function estDateDepassee($dateTestee)
{
    $dateActuelle = date('d/m/Y');
    @list($jour, $mois, $annee) = explode('/', $dateActuelle);
    $annee--;
    $anPasse = $annee . $mois . $jour;
    @list($jourTeste, $moisTeste, $anneeTeste) = explode('/', $dateTestee);
    return ($anneeTeste . $moisTeste . $jourTeste < $anPasse);
}

/**
 * Vérifie la validité du format d'une date française jj/mm/aaaa
 *
 * @param String $date Date à tester
 *
 * @return Boolean vrai ou faux
 */
function estDateValide($date, $format = 'Y-m-d')
{
    var_dump($date);
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

/**
 * Vérifie que le tableau de frais ne contient que des valeurs numériques
 *
 * @param Array $lesFrais Tableau d'entier
 *
 * @return Boolean vrai ou faux
 */
function lesQteFraisValides($lesFrais)
{
    return estTableauEntiers($lesFrais);
}

/**
 * Vérifie la validité des trois arguments : la date, le libellé du frais
 * et le montant
 *
 * Des message d'erreurs sont ajoutés au tableau des erreurs
 *
 * @param String $dateFrais Date des frais
 * @param String $libelle   Libellé des frais
 * @param Float  $montant   Montant des frais
 *
 * @return null
 */
function valideInfosFrais($dateFrais, $libelle, $montant)
{
    if ($dateFrais == '') {
        ajouterErreur('Le champ date ne doit pas être vide');
    } else {
        if (!estDatevalide($dateFrais)) {
            ajouterErreur('Date invalide');
        } else {
            if (estDateDepassee($dateFrais)) {
                ajouterErreur("
                    Date d'enregistrement du frais dépassé de plus de 1 an
                ");
            }
        }
    }
    if ($libelle == '') {
        ajouterErreur('Le champ description ne peut pas être vide');
    }
    if ($montant == '') {
        ajouterErreur('Le champ montant ne peut pas être vide');
    } elseif (!is_numeric($montant)) {
        ajouterErreur('Le champ montant doit être numérique');
    }
}

/**
* Ajoute un message d'erreur ou de succès suivant le retour de la fonction test.
* Si la fonction test retourne vrai, on considère que c'est un succès et on ajoute
* le message de succès. Le cas échant, on ajoute le message d'erreurs. Les messages
* sont passés en paramètre.
*
* @param Boolean $test      La fonction à tester
* @param String $successMsg Le message de succès à ajouter
* @param String $errorMsg   Le message d'erreur à ajouter
* @return null
*/
function executeThenReturnMessage($test, $successMsg, $errorMsg){
    if($test){
        ajouterSucces($successMsg);
        include 'vues/v_succes.php';
    } else {
        ajouterErreur($errorMsg);
        include 'vues/v_erreurs.php';
    }
}

/**
 * Ajoute le libellé d'une erreur au tableau des erreurs
 *
 * @param String $msg Libellé de l'erreur
 *
 * @return null
 */
function ajouterErreur($msg)
{
    if (!isset($_REQUEST['erreurs'])) {
        $_REQUEST['erreurs'] = array();
    }
    $_REQUEST['erreurs'][] = $msg;
}

/**
 * Ajoute le libellé d'un succès au tableau des succès
 *
 * @param String $msg Libellé du succès
 *
 * @return null
 */
function ajouterSucces($msg)
{
    if (!isset($_REQUEST['succes'])) {
        $_REQUEST['succes'] = array();
    }
    $_REQUEST['succes'][] = $msg;
}


/**
 * Retoune le nombre de lignes du tableau des erreurs
 *
 * @return Integer le nombre d'erreurs
 */
function nbErreurs()
{
    if (!isset($_REQUEST['erreurs'])) {
        return 0;
    } else {
        return count($_REQUEST['erreurs']);
    }
}
