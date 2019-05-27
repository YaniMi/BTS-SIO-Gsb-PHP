<?php

/**
 * Classe d'accès aux données.
 *
 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO
 * $monPdoGsb qui contiendra l'unique instance de la classe
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Cheri Bibi - Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @copyright 2017 Réseau CERTA
 * @license   Réseau CERTA
 * @version   Release: 1.0
 * @link      http://www.php.net/manual/fr/book.pdo.php PHP Data Objects sur php.net
 */

class PdoGsb
{
    private static $serveur = 'mysql:host=localhost';
    private static $bdd = 'dbname=gsb_frais';
    private static $user = 'userGsb';
    private static $mdp = 'secret';
    private static $monPdo;
    private static $monPdoGsb = null;


    /**
     * Constructeur privé, crée l'instance de PDO qui sera sollicitée
     * pour toutes les méthodes de la classe
     */
    private function __construct()
    {
        PdoGsb::$monPdo = new PDO(
            PdoGsb::$serveur . ';' . PdoGsb::$bdd,
            PdoGsb::$user,
            PdoGsb::$mdp
        );
        PdoGsb::$monPdo->query('SET CHARACTER SET utf8');
    }

    /**
     * Méthode destructeur appelée dès qu'il n'y a plus de référence sur un
     * objet donné, ou dans n'importe quel ordre pendant la séquence d'arrêt.
     */
    public function __destruct()
    {
        PdoGsb::$monPdo = null;
    }

    /**
     * Fonction statique qui crée l'unique instance de la classe
     * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();
     *
     * @return l'unique objet de la classe PdoGsb
     */
    public static function getPdoGsb()
    {
        if (PdoGsb::$monPdoGsb == null) {
            PdoGsb::$monPdoGsb = new PdoGsb();
        }
        return PdoGsb::$monPdoGsb;
    }

    /**
     * Retourne les informations d'un utiilsateur
     *
     * @param String $login Login de l'utilisateur
     * @param String $mdp   Mot de passe de l'utilisateur
     *
     * @return ArrayObject  L'id, le nom, le prénom et l'id du type d'utilisateur sous la forme d'un tableau associatif
     */
    public function getInfosUser($login, $mdp)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
           "SELECT user.id AS id, user.nom AS nom, user.prenom AS prenom, "
        .   "user.idtypeuser AS idtypeuser "
        .   "FROM user "
        .   "WHERE user.login = :login AND user.mdp = SHA2(:mdp, 512)"
        );

        $requetePrepare->bindParam(':login', $login, PDO::PARAM_STR);
        $requetePrepare->bindParam(':mdp', $mdp, PDO::PARAM_STR);

        $requetePrepare->execute();

        return $requetePrepare->fetch();
    }

    /**
     * Retourne tous les visiteurs triés par nom prénom.
     *
     * @return ArrayObject  L'id, le prénom, le nom sous la forme d'un tableau association
     */
    public function getVisiteurList(){

        $requetePrepare = PdoGsb::$monPdo->prepare(
           "SELECT user.id AS id, user.prenom AS prenom, user.nom AS nom "
        .   "FROM user JOIN typeuser ON user.idtypeuser = typeuser.id"
        .   " WHERE typeuser.libelle = 'visiteur' "
        .   " ORDER BY user.nom, user.prenom"
        );

        $requetePrepare->execute();

        //print_r($requetePrepare->errorInfo());

        return $requetePrepare->fetchAll();
    }

    /**
     * Retourne tous les utilisateurs triés par nom prénom.
     *
     * @return ArrayObject  L'id, nom, prénom, adresse, cp, ville, date embauche, idtypeuser
     * sous la forme d'un tableau association
     */
    public function getUserList(){
        $requetePrepare = PdoGsb::$monPdo->prepare(
            "SELECT id, nom, prenom, adresse, cp, ville, dateembauche, "
            . "idtypeuser FROM user ORDER BY user.nom, user.prenom"
        );
        $requetePrepare->execute();

        return $requetePrepare->fetchAll();
    }

    /**
     * Retourne toutes les fiches d'un état donné.
     *
     * @param String $idEtat    "ID" de l'état parmi 'VA', 'RB', 'MP', 'CL', 'CR'
     * @return ArrayObject      Les fiches avec prenom, nom du visiteur, le mois,
     * le montant validé, l'ID de l'état et son libellé.
     */
    public function getFicheFraisListForIdEtat($idEtat){
        $requetePrepare = PdoGsb::$monPdo->prepare('
            SELECT user.prenom, user.nom, fichefrais.iduser, fichefrais.mois,
            fichefrais.montantvalide, fichefrais.idetat, etat.libelle as etatlibelle
            FROM fichefrais
            JOIN user ON fichefrais.iduser = user.id
            JOIN etat ON fichefrais.idetat = etat.id
            WHERE idetat = :idEtat
        ');
        $requetePrepare->bindParam(':idEtat', $idEtat, PDO::PARAM_STR);
        $requetePrepare->execute();

        //print_r($requetePrepare->errorInfo());

        return $requetePrepare->fetchAll();
    }

    /**
     * Retourne toutes les fiches de tous les utilisateurs.
     *
     * @param String $idEtat    "ID" de l'état parmi 'VA', 'RB', 'MP', 'CL', 'CR'
     * @return ArrayObject      Les fiches avec prenom, nom du visiteur, le mois,
     * le montant validé, l'ID de l'état et son libellé.
     */
    public function getFicheFraisList(){
        $requetePrepare = PdoGsb::$monPdo->prepare('
            SELECT user.prenom, user.nom, fichefrais.iduser, fichefrais.mois,
            fichefrais.montantvalide, fichefrais.idetat, etat.libelle as etatlibelle
            FROM fichefrais
            JOIN user ON fichefrais.iduser = user.id
            JOIN etat ON fichefrais.idetat = etat.id
        ');
        $requetePrepare->execute();

        return $requetePrepare->fetchAll();
    }


    /**
     * Retourne toutes les pages accessibles pour un certain type d'utilisateurs.
     * Cela permet par exemple de créer un menu à partir de ces pages.
     *
     * @param Integer $idTypeUser   L'id du type d'utilisateur. Correspond à
     * "visiteur" ou "comptable" par exemple.
     * @param String $uc            La page que l'utilisateur essaie d'atteindre
     *
     * @return ArrayObject          L'uc, le libelle de la page, le type d'utilisateurs,
     */
    public function getPageUserList($idTypeUser){
        $requetePrepare = PdoGsb::$monPdo->prepare(
           "SELECT * "
            ."FROM pageuser "
                . "WHERE idtypeuser = :idtypeuser"
        );

        $requetePrepare->bindParam(':idtypeuser', $idTypeUser, PDO::PARAM_STR);

        $requetePrepare->execute();

        //print_r($requetePrepare->errorInfo());

        return $requetePrepare->fetchAll();
    }



    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * au forfait concernées par les deux arguments
     *
     * @param String $idVisiteur    ID du user
     * @param String $mois          Mois sous la forme aaaamm
     *
     * @return ArrayObject          L'id, le libelle et la quantité sous la forme d'un tableau
     * associatif
     */
    public function getLesFraisForfait($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare('
            SELECT fraisforfait.id AS idfrais, fraisforfait.libelle AS libelle,
            lignefraisforfait.quantite AS quantite,
            lignefraisforfait.idfraisforfait AS idfraisforfait
            FROM lignefraisforfait
            INNER JOIN fraisforfait
            ON fraisforfait.id = lignefraisforfait.idfraisforfait
            WHERE lignefraisforfait.iduser = :idVisiteur
            AND lignefraisforfait.mois = :mois
            ORDER BY lignefraisforfait.idfraisforfait
        ');
        $requetePrepare->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':mois', $mois, PDO::PARAM_STR);

        $requetePrepare->execute();

        //print_r($requetePrepare->errorInfo());
        //var_dump($requetePrepare->fetchAll());

        return $requetePrepare->fetchAll();
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais
     * hors forfait concernées par les deux arguments.
     * La boucle foreach ne peut être utilisée ici car on procède
     * à une modification de la structure itérée - transformation du champ date-
     *
     * @param String $iduser    ID de l'user
     * @param String $mois      Mois sous la forme aaaamm
     *
     * @return ArrayObject      Tous les champs des lignes de frais hors forfait sous la forme
     * d'un tableau associatif
     */
    public function getLesFraisHorsForfait($idUser, $mois)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT * FROM lignefraishorsforfait '
            . 'WHERE lignefraishorsforfait.iduser = :idUser '
            . 'AND lignefraishorsforfait.mois = :unMois'
        );
        $requetePrepare->bindParam(':idUser', $idUser, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $lesLignes = $requetePrepare->fetchAll();
        for ($i = 0; $i < count($lesLignes); $i++) {
            $date = $lesLignes[$i]['date'];
            $lesLignes[$i]['date'] = dateAnglaisVersFrancais($date);
        }
        return $lesLignes;
    }

    /**
     * Retourne le montant total pour une fiche de frais donnée.
     *
     * @param String $idVisiteur    L'ID du visiteur concerné
     * @param String $mois          Le mois de la fiche conernée
     *
     * @return Float              Retourne le montant total.
     */
    public function getMontantTotalFicheFrais($idVisiteur, $mois){
        $totalFraisHorsForfait = $this->getTotalFraisForfait($idVisiteur, $mois);
        $totalFraisForfait = $this->getTotalFraisHorsForfait($idVisiteur, $mois);

        return $totalFraisHorsForfait + $totalFraisForfait;
    }

    /**
     * Retourne le montant total de frais forfaits pour un mois et visiteur donnés.
     *
     * @param String $idVisiteur    L'ID du visiteur concerné
     * @param String $mois          Le mois de la fiche conernée
     *
     * @return Float              Retourne le montant total.
     */
    public function getTotalFraisForfait($idVisiteur, $mois){
        $sql = "SELECT SUM(lignefraisforfait.quantite * fraisforfait.montant) "
                . "FROM lignefraisforfait JOIN fraisforfait "
                . "ON lignefraisforfait.idfraisforfait = fraisforfait.id "
                . "WHERE lignefraisforfait.iduser = :idVisiteur AND lignefraisforfait.mois = :mois";
        $requetePrepare = PdoGSB::$monPdo->prepare($sql);
        $requetePrepare->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':mois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();

        //print_r($requetePrepare->errorInfo());

        $totalFraisForfait = $requetePrepare->fetch()[0];
        return $totalFraisForfait;
    }

    /**
     * Retourne le montant total de frais hors forfaits pour un mois et visiteur
     * donnés.
     *
     * @param String $idVisiteur    L'ID du visiteur concerné
     * @param String $mois          Le mois de la fiche conernée
     *
     * @return Float              Retourne le montant total.
     */
    public function getTotalFraisHorsForfait($idVisiteur, $mois){
        $sql = "SELECT SUM(lignefraishorsforfait.montant) FROM lignefraishorsforfait "
                . " WHERE lignefraishorsforfait.iduser = :idVisiteur "
                . "AND lignefraishorsforfait.mois = :mois "
                . "AND lignefraishorsforfait.refus = false";
        $requetePrepare = PdoGSB::$monPdo->prepare($sql);
        $requetePrepare->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':mois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $totalFraisHorsForfait = $requetePrepare->fetch()[0];

        return $totalFraisHorsForfait;
    }




    /**
     * Retourne le nombre de justificatif d'un user pour un mois donné
     *
     * @param String $idVisiteur    ID du user
     * @param String $mois      Mois sous la forme aaaamm
     *
     * @return Integer          Le nombre entier de justificatifs
     */
    public function getNbjustificatifs($idVisiteur, $mois)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT fichefrais.nbjustificatifs as nb FROM fichefrais '
            . 'WHERE fichefrais.iduser = :idVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne['nb'];
    }

    /**
     * Retourne tous les id de la table FraisForfait
     *
     * @return ArrayObject  un tableau
     */
    public function getLesIdFrais()
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT fraisforfait.id as idfrais '
            . 'FROM fraisforfait ORDER BY fraisforfait.id'
        );
        $requetePrepare->execute();
        return $requetePrepare->fetchAll();
    }

    /**
     * Retourne les mois et l'état de la fiche pour lesquel un utilisateur a
     * une fiche de frais
     *
     * @param String $iduser ID du user
     *
     * @return ArrayObject   un tableau associatif de clé un mois -aaaamm- et de valeurs
     *         l'année et le mois correspondant
     */
    public function getLesMoisDisponibles($iduser)
    {

        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT fichefrais.mois AS mois FROM fichefrais '
            . 'WHERE fichefrais.iduser = :unIduser '
            . 'ORDER BY fichefrais.mois desc'
        );

        $requetePrepare->bindParam(':unIduser', $iduser, PDO::PARAM_STR);

        $requetePrepare->execute();

        $lesMois = array();

        while ($laLigne = $requetePrepare->fetch()) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois[] = array(
                'mois' => $mois,
                'numAnnee' => $numAnnee,
                'numMois' => $numMois
            );
        }

        return $lesMois;
    }

    /**
     * Retourne les informations d'une fiche de frais d'un utilisateur pour un
     * mois donné
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return ArrayObject      un tableau avec des champs de jointure entre une fiche de frais
     *         et la ligne d'état
     */
    public function getLesInfosFicheFrais($idVisiteur, $mois)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'SELECT fichefrais.idetat as idEtat, '
            . 'fichefrais.datemodif as dateModif,'
            . 'fichefrais.nbjustificatifs as nbJustificatifs, '
            . 'fichefrais.montantvalide as montantValide, '
            . 'etat.libelle as libEtat '
            . 'FROM fichefrais '
            . 'INNER JOIN etat ON fichefrais.idetat = etat.id '
            . 'WHERE fichefrais.iduser = :idVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        return $laLigne;
    }

    /**
     * Met à jour la table ligneFraisForfait
     * Met à jour la table ligneFraisForfait pour un user et
     * un mois donné en enregistrant les nouveaux montants
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param Array  $lesFrais   tableau associatif de clé idFrais et
     *                           de valeur la quantité pour ce frais
     *
     * @return Boolean              vrai si la requête a été un succès, faux sinon
     */
    public function majFraisForfait($idVisiteur, $mois, $lesFrais)
    {
        $return = true;
        $lesCles = array_keys($lesFrais);
        foreach ($lesCles as $unIdFrais) {
            $qte = $lesFrais[$unIdFrais];
            $requetePrepare = PdoGSB::$monPdo->prepare(
                'UPDATE lignefraisforfait '
                . 'SET lignefraisforfait.quantite = :uneQte '
                . 'WHERE lignefraisforfait.iduser = :idVisiteur '
                . 'AND lignefraisforfait.mois = :unMois '
                . 'AND lignefraisforfait.idfraisforfait = :idFrais'
            );
            $requetePrepare->bindParam(':uneQte', $qte, PDO::PARAM_INT);
            $requetePrepare->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(':idFrais', $unIdFrais, PDO::PARAM_STR);
            if(!$requetePrepare->execute()){
                $return = false;
            }
        }
        return $return;
    }

     /**
     * Met à jour la table ligneFraisHorsForfait
     * Met à jour la table ligneFraisHorsForfait pour une ligne hors forfait
     * donnée à "REFUSE" devant le libelle.
     *
     * @param String $idLigneFraisHorsForfait   ID de la ligne hors forfait
     * @param Boolean $refus                    La valeur que prendra le champ
      * "refus"
     *
     * @return Boolean              vrai si la requête a été un succès, faux sinon
     */
    public function majRefusFraisHorsForfait($idLigneFraisHorsForfait, $refus)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE lignefraishorsforfait '
            . 'SET refus = :refus '
            . 'WHERE id = :idLigneFraisHorsForfait'
        );
        $requetePrepare->bindParam(':idLigneFraisHorsForfait', $idLigneFraisHorsForfait, PDO::PARAM_STR);
        $requetePrepare->bindParam(':refus', $refus, PDO::PARAM_BOOL );
        return $requetePrepare->execute();
    }


    /**
     * reporte la table ligneFraisHorsForfait au mois suivant pour une ligne
     * hors forfait donnée
     *
     * @param String $idLigneFraisHorsForfait ID de la ligne hors forfait
     *
     * @return Boolean              vrai si la requête a été un succès, faux sinon
     */
    public function majReportFraisHorsForfait($idLigneFraisHorsForfait, $mois)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE lignefraishorsforfait '
            . 'SET mois = :mois '
            . 'WHERE lignefraishorsforfait.id = :idLigneFraisHorsForfait'
        );
        $requetePrepare->bindParam(
                ':idLigneFraisHorsForfait',
                $idLigneFraisHorsForfait,
                PDO::PARAM_STR
        );
        $requetePrepare->bindParam(':mois', $mois, PDO::PARAM_STR);
        return $requetePrepare->execute();
    }


    /**
     * Met à jour le nombre de justificatifs de la table ficheFrais
     * pour le mois et le user concerné
     *
     * @param String  $idVisiteur      ID du visiteur
     * @param String  $mois            Mois sous la forme aaaamm
     * @param Integer $nbJustificatifs Nombre de justificatifs
     *
     * @return Boolean                 vrai si la requête a été un succès, faux sinon
     */
    public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE fichefrais '
            . 'SET nbjustificatifs = :unNbJustificatifs '
            . 'WHERE fichefrais.iduser = :idVisiteur '
            . 'AND fichefrais.mois = :mois'
        );
        $requetePrepare->bindParam(
            ':unNbJustificatifs',
            $nbJustificatifs,
            PDO::PARAM_INT
        );
        $requetePrepare->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':mois', $mois, PDO::PARAM_STR);
        return $requetePrepare->execute();
    }

     /**
     * Modifie l'état et la date de modification d'une fiche de frais.
     * Modifie le champ idEtat et met la date de modif à aujourd'hui.
     *
     * @param String $idVisiteur ID de l'user
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $etat       Nouvel état de la fiche de frais
     *
     * @return Boolean          Vrai si la requête est un succès, faux sinon.
     */
    public function majEtatFicheFrais($idVisiteur, $mois, $etat)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE ficheFrais '
            . 'SET idetat = :unEtat, datemodif = now() '
            . 'WHERE fichefrais.iduser = :idVisiteur '
            . 'AND fichefrais.mois = :unMois'
        );
        $requetePrepare->bindParam(':unEtat', $etat, PDO::PARAM_STR);
        $requetePrepare->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        return $requetePrepare->execute();
    }

    /**
     * Modifie le montant et la date de modification d'une fiche de frais.
     * Modifie le champ montant et met la date de modif à aujourd'hui.
     *
     * @param String $iduser ID de l'user
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $montant    Nouveau montant validé de la fiche de frais
     *
     * @return Boolean      Vrai si la requête est un succès, faux sinon
     */
    public function majMontantValideFicheFrais($iduser, $mois, $montant)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'UPDATE ficheFrais '
            . 'SET montantvalide = :montant, datemodif = now() '
            . 'WHERE iduser = :iduser '
            . 'AND mois = :idmois'
        );
        $requetePrepare->bindParam(':montant', $montant, PDO::PARAM_INT);
        $requetePrepare->bindParam(':iduser', $iduser, PDO::PARAM_STR);
        $requetePrepare->bindParam(':idmois', $mois, PDO::PARAM_STR);
        //print_r($requetePrepare->errorInfo());
        return $requetePrepare->execute();
    }

    /**
     * Crée une nouvelle fiche de frais et les lignes de frais au forfait
     * pour un user et un mois donnés
     *
     * Récupère le dernier mois en cours de traitement, met à 'CL' son champs
     * idEtat, crée une nouvelle fiche de frais avec un idEtat à 'CR' et crée
     * les lignes de frais forfait de quantités nulles
     *
     * @param String $idVisiteur ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return Boolean      Vrai si la requête est un succès, faux sinon
     */
    public function creeNouvellesLignesFrais($idVisiteur, $mois)
    {
        $return = true;
        $dernierMois = $this->dernierMoisSaisi($idVisiteur);
        $laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur, $dernierMois);
        if ($laDerniereFiche['idEtat'] == 'CR') {
            $this->majEtatFicheFrais($idVisiteur, $dernierMois, 'CL');
        }
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'INSERT INTO fichefrais (iduser,mois,nbjustificatifs,'
            . 'montantvalide,datemodif,idetat) '
            . "VALUES (:idVisiteur,:unMois,0,0,now(),'CR')"
        );
        $requetePrepare->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        if(!$requetePrepare->execute()){
            $return = false;
        }
        $lesIdFrais = $this->getLesIdFrais();
        foreach ($lesIdFrais as $unIdFrais) {
            $requetePrepare = PdoGsb::$monPdo->prepare(
                'INSERT INTO lignefraisforfait (iduser,mois,'
                . 'idfraisforfait,quantite) '
                . 'VALUES(:idVisiteur, :unMois, :idFrais, 0)'
            );
            $requetePrepare->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requetePrepare->bindParam(
                ':idFrais',
                $unIdFrais['idfrais'],
                PDO::PARAM_STR
            );
            if(!$requetePrepare->execute()){
                $return = false;
            }
        }

        return $return;
    }

    /**
     * Crée un nouveau frais hors forfait pour un visiteur un mois donné
     * à partir des informations fournies en paramètre
     *
     * @param String $idVisiteur    ID du visiteur
     * @param String $mois       Mois sous la forme aaaamm
     * @param String $libelle    Libellé du frais
     * @param String $date       Date du frais au format français jj//mm/aaaa
     * @param Float  $montant    Montant du frais
     *
     * @return Boolean Vrai si la requête est un succès, faux sinon
     */
    public function creeNouveauFraisHorsForfait(
        $idVisiteur,
        $mois,
        $libelle,
        $date,
        $montant
    ) {
        $dateFr = dateFrancaisVersAnglais($date);
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'INSERT INTO lignefraishorsforfait (iduser, mois, libelle, date,'
            . 'montant) '
            . 'VALUES (:idVisiteur,:unMois, :unLibelle, :uneDateFr,'
            . ':unMontant) '
        );
        $requetePrepare->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unLibelle', $libelle, PDO::PARAM_STR);
        $requetePrepare->bindParam(':uneDateFr', $dateFr, PDO::PARAM_STR);
        $requetePrepare->bindParam(':unMontant', $montant, PDO::PARAM_INT);

        return $requetePrepare->execute();

        //print_r($requetePrepare->errorInfo());
    }

    /**
     * Supprime le frais hors forfait dont l'id est passé en argument
     *
     * @param String $idFrais ID du frais
     *
     * @return Boolean Vrai si la requête est un succès, faux sinon
     */
    public function supprimerFraisHorsForfait($idFrais)
    {
        $requetePrepare = PdoGSB::$monPdo->prepare(
            'DELETE FROM lignefraishorsforfait '
            . 'WHERE lignefraishorsforfait.id = :unIdFrais'
        );
        $requetePrepare->bindParam(':unIdFrais', $idFrais, PDO::PARAM_STR);
        return $requetePrepare->execute();
    }

    /**
     * Teste si un visiteur possède une fiche de frais pour le mois passé en argument
     *
     * @param String $idVisiteur ID du user
     * @param String $mois       Mois sous la forme aaaamm
     *
     * @return Boolean
     */
    public function estPremierFraisMois($idVisiteur, $mois)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT fichefrais.mois FROM fichefrais '
            . 'WHERE fichefrais.mois = :mois '
            . 'AND fichefrais.iduser = :idVisiteur'
        );
        $requetePrepare->bindParam(':mois', $mois, PDO::PARAM_STR);
        $requetePrepare->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);

        $requetePrepare->execute();

        if(!$requetePrepare->fetch()){
            //Si $requetePrepare->fetch() retourne faux, c'est qu'il n'y pas de
            //fiche pour ce mois, on retourne vrai.
            return true;
        }
        //Autrement, si une fiche existe pour ce mois, il ne s'agit pas du
        //premier mois donc on retourne faux
        return false;
    }

    /**
     * Retourne le dernier mois en cours d'un utilisateur
     *
     * @param String $iduser ID du user
     *
     * @return String le mois sous la forme aaaamm
     */
    public function dernierMoisSaisi($iduser)
    {
        $requetePrepare = PdoGsb::$monPdo->prepare(
            'SELECT MAX(mois) as dernierMois '
            . 'FROM fichefrais '
            . 'WHERE fichefrais.iduser = :unIduser'
        );
        $requetePrepare->bindParam(':unIduser', $iduser, PDO::PARAM_STR);
        $requetePrepare->execute();
        $laLigne = $requetePrepare->fetch();
        $dernierMois = $laLigne['dernierMois'];
        return $dernierMois;
    }

    /**
     * Retourne booléen pour savoir si la page avec l'idTypeUser et l'uc en 
	 * paramètres est bien une page accessible pour cet utilisateur.
     *
     * @param String $idTypeUser	L'id du type d'utilisateurs
     * @param String $uc			Le champ 'uc' de la page.
     *
     * @return Boolean      vrai, si la page est une page accessible par ce type
     * d'utilisateur, faux sinon.
    */
    public function isPageAccessibleForUserType($idTypeUser, $uc){
        $requetePrepare = PdoGsb::$monPdo->prepare(
           "SELECT * FROM pageuser "
            . "WHERE idtypeuser = :idtypeuser AND uc = :uc "
        );

        $requetePrepare->bindParam(':idtypeuser', $idTypeUser, PDO::PARAM_STR);
        $requetePrepare->bindParam(':uc', $uc, PDO::PARAM_STR);


        $requetePrepare->execute();

        //print_r($requetePrepare->errorInfo());

        return $requetePrepare->fetch();
    }


}
