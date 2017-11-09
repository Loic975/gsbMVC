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
 * @package default
 * @author Cheri Bibi
 * @version    1.0
 * @link       http://www.php.net/manual/fr/book.pdo.php
 */
class PdoGsb {

    private static $serveur = 'mysql:host=localhost';
    private static $bdd = 'dbname=gsb_frais';
    private static $user = 'root';
    private static $mdp = '';
    private static $monPdo;
    private static $monPdoGsb = null;
    // Génération :  bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
    private static $salt = 'eca46a4797240dd4936bdf61bf32768c62f539ee46472cf9db01f50231328d2e';

    /**
     * Constructeur privé, crée l'instance de PDO qui sera sollicitée
     * pour toutes les méthodes de la classe
     */
    private function __construct() {
        PdoGsb::$monPdo = new PDO(PdoGsb::$serveur . ';' . PdoGsb::$bdd, PdoGsb::$user, PdoGsb::$mdp);
        PdoGsb::$monPdo->query("SET CHARACTER SET utf8");
    }

    public function _destruct() {
        PdoGsb::$monPdo = null;
    }

    /**
     * Fonction statique qui crée l'unique instance de la classe
     * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();
     * 
     * @return l'unique objet de la classe PdoGsb
     */
    public static function getPdoGsb() {
        if (PdoGsb::$monPdoGsb == null) {
            PdoGsb::$monPdoGsb = new PdoGsb();
        }
        return PdoGsb::$monPdoGsb;
    }

    /**
     * Retourne les informations d'un visiteur
     * @param $login 
     * @param $mdp
     * @return l'id, le nom et le prénom sous la forme d'un tableau associatif 
     */
    public function getInfosVisiteur($login, $mdp) {
        $mdp = PdoGsb::$salt . hash("sha256", $mdp . PdoGsb::$salt);
        $requete_prepare = PdoGsb::$monPdo->prepare("SELECT visiteur.id AS id, visiteur.nom AS nom, visiteur.prenom AS prenom "
                . "FROM visiteur "
                . "WHERE visiteur.login = :unLogin AND visiteur.mdp = :unMdp");
        $requete_prepare->bindParam(':unLogin', $login, PDO::PARAM_STR);
        $requete_prepare->bindParam(':unMdp', $mdp, PDO::PARAM_STR);
        $requete_prepare->execute();
        return $requete_prepare->fetch();
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais hors forfait
     * concernées par les deux arguments
     * La boucle foreach ne peut être utilisée ici car on procède
     * à une modification de la structure itérée - transformation du champ date-
     * 
     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     * @return tous les champs des lignes de frais hors forfait sous la forme d'un tableau associatif 
     */
    public function getLesFraisHorsForfait($idVisiteur, $mois) {
        $requete_prepare = PdoGsb::$monPdo->prepare("SELECT * FROM lignefraishorsforfait "
                . "WHERE lignefraishorsforfait.idvisiteur = :unIdVisiteur "
                . "AND lignefraishorsforfait.mois = :unMois");
        $requete_prepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requete_prepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requete_prepare->execute();
        $lesLignes = $requete_prepare->fetchAll();
        for ($i = 0; $i < count($lesLignes); $i++) {
            $date = $lesLignes[$i]['date'];
            $lesLignes[$i]['date'] = dateAnglaisVersFrancais($date);
        }
        return $lesLignes;
    }

    /**
     * Retourne le nombre de justificatif d'un visiteur pour un mois donné
     * 
     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     * @return le nombre entier de justificatifs 
     */
    public function getNbjustificatifs($idVisiteur, $mois) {
        $requete_prepare = PdoGsb::$monPdo->prepare("SELECT fichefrais.nbjustificatifs as nb "
                . "FROM fichefrais "
                . "WHERE fichefrais.idvisiteur = :unIdVisiteur "
                . "AND fichefrais.mois = :unMois");
        $requete_prepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requete_prepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requete_prepare->execute();
        $laLigne = $requete_prepare->fetch();
        return $laLigne['nb'];
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais au forfait
     * concernées par les deux arguments
     * 
     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     * @return l'id, le libelle et la quantité sous la forme d'un tableau associatif 
     */
    public function getLesFraisForfait($idVisiteur, $mois) {
        $requete_prepare = PdoGSB::$monPdo->prepare("SELECT fraisforfait.id as idfrais, "
                . "fraisforfait.libelle as libelle, lignefraisforfait.quantite as quantite "
                . "FROM lignefraisforfait "
                . "INNER JOIN fraisforfait ON fraisforfait.id = lignefraisforfait.idfraisforfait "
                . "WHERE lignefraisforfait.idvisiteur = :unIdVisiteur "
                . "AND lignefraisforfait.mois = :unMois "
                . "ORDER BY lignefraisforfait.idfraisforfait");
        $requete_prepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requete_prepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requete_prepare->execute();
        return $requete_prepare->fetchAll();
    }

    /**
     * Retourne tous les id de la table FraisForfait
     * 
     * @return un tableau associatif 
     */
    public function getLesIdFrais() {
        $requete_prepare = PdoGsb::$monPdo->prepare("SELECT fraisforfait.id as idfrais "
                . "FROM fraisforfait "
                . "ORDER BY fraisforfait.id");
        $requete_prepare->execute();
        return $requete_prepare->fetchAll();
    }

    /**
     * Met à jour la table ligneFraisForfait
     * Met à jour la table ligneFraisForfait pour un visiteur et
     * un mois donné en enregistrant les nouveaux montants
     * 
     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     * @param $lesFrais tableau associatif de clé idFrais et de valeur la quantité pour ce frais
     * @return un tableau associatif 
     */
    public function majFraisForfait($idVisiteur, $mois, $lesFrais) {
        $lesCles = array_keys($lesFrais);
        foreach($lesCles as $unIdFrais){
            $qte = $lesFrais[$unIdFrais];
            $requete_prepare = PdoGSB::$monPdo->prepare("UPDATE lignefraisforfait "
                    . "SET lignefraisforfait.quantite = :uneQte "
                    . "WHERE lignefraisforfait.idvisiteur = :unIdVisiteur "
                    . "AND lignefraisforfait.mois = :unMois "
                    . "AND lignefraisforfait.idfraisforfait = :idFrais");
            $requete_prepare->bindParam(':uneQte', $qte, PDO::PARAM_INT);
            $requete_prepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requete_prepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requete_prepare->bindParam(':idFrais', $unIdFrais, PDO::PARAM_STR);
            $requete_prepare->execute();
        }
    }

    /**
     * met à jour le nombre de justificatifs de la table ficheFrais
     * pour le mois et le visiteur concerné
     * 
     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     */
    public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs) {
        $requete_prepare = PdoGB::$monPdo->prepare("UPDATE fichefrais "
                . "SET nbjustificatifs = :unNbJustificatifs "
                . "WHERE fichefrais.idvisiteur = :unIdVisiteur "
                . "AND fichefrais.mois = :unMois");
        $requete_prepare->bindParam(':unNbJustificatifs', $nbJustificatifs, PDO::PARAM_INT);
        $requete_prepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requete_prepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requete_prepare->execute();
    }

    /**
     * Teste si un visiteur possède une fiche de frais pour le mois passé en argument
     * 
     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     * @return vrai ou faux 
     */
    public function estPremierFraisMois($idVisiteur, $mois) {
        $ok = false;
        $requete_prepare =  PdoGsb::$monPdo->prepare("SELECT fichefrais.mois "
                . "FROM fichefrais "
                . "WHERE fichefrais.mois = :unMois "
                . "AND fichefrais.idvisiteur = :unIdVisiteur");
        $requete_prepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requete_prepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requete_prepare->execute();
        if (!$requete_prepare->fetch()) {
            $ok = true;
        }
        return $ok;
    }

    /**
     * Retourne le dernier mois en cours d'un visiteur
     * 
     * @param $idVisiteur 
     * @return le mois sous la forme aaaamm
     */
    public function dernierMoisSaisi($idVisiteur) {
       $requete_prepare =  PdoGsb::$monPdo->prepare("SELECT MAX(mois) as dernierMois "
                . "FROM fichefrais "
                . "WHERE fichefrais.idvisiteur = :unIdVisiteur");
        $requete_prepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requete_prepare->execute();
        $laLigne = $requete_prepare->fetch();
        $dernierMois = $laLigne['dernierMois'];
        return $dernierMois;
    }

    /**
     * Crée une nouvelle fiche de frais et les lignes de frais au forfait pour un visiteur et un mois donnés
     * 
     * récupère le dernier mois en cours de traitement, met à 'CL' son champs idEtat, crée une nouvelle fiche de frais
     * avec un idEtat à 'CR' et crée les lignes de frais forfait de quantités nulles 
     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     */
    public function creeNouvellesLignesFrais($idVisiteur, $mois) {
        $dernierMois = $this->dernierMoisSaisi($idVisiteur);
        $laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur, $dernierMois);
        if ($laDerniereFiche['idEtat'] == 'CR') {
            $this->majEtatFicheFrais($idVisiteur, $dernierMois, 'CL');
        }
        $requete_prepare = PdoGsb::$monPdo->prepare("INSERT INTO fichefrais "
                . "(idvisiteur,mois,nbJustificatifs,montantValide,dateModif,idEtat) "
                . "VALUES (:unIdVisiteur,:unMois,0,0,now(),'CR')");
        $requete_prepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requete_prepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requete_prepare->execute();
        $lesIdFrais = $this->getLesIdFrais();
        foreach ($lesIdFrais as $unIdFrais) {
            $requete_prepare = PdoGsb::$monPdo->prepare("INSERT INTO lignefraisforfait "
                    . "(idvisiteur,mois,idFraisForfait,quantite) "
                    . "VALUES(:unIdVisiteur, :unMois, :idFrais, 0)");
            $requete_prepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
            $requete_prepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
            $requete_prepare->bindParam(':idFrais', $unIdFrais['idfrais'], PDO::PARAM_STR);
            $requete_prepare->execute();
        }
    }

    /**
     * Crée un nouveau frais hors forfait pour un visiteur un mois donné
     * à partir des informations fournies en paramètre
     * 
     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     * @param $libelle : le libelle du frais
     * @param $date : la date du frais au format français jj//mm/aaaa
     * @param $montant : le montant
     */
    public function creeNouveauFraisHorsForfait($idVisiteur, $mois, $libelle, $date, $montant) {
        $dateFr = dateFrancaisVersAnglais($date);
        $requete_prepare = PdoGSB::$monPdo->prepare("INSERT INTO lignefraishorsforfait "
                . "VALUES ('', :unIdVisiteur,:unMois, :unLibelle, :uneDateFr, :unMontant) ");
        $requete_prepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requete_prepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requete_prepare->bindParam(':unLibelle', $libelle, PDO::PARAM_STR);
        $requete_prepare->bindParam(':uneDateFr', $dateFr, PDO::PARAM_STR);
        $requete_prepare->bindParam(':unMontant', $montant, PDO::PARAM_INT);
        $requete_prepare->execute();
    }

    /**
     * Supprime le frais hors forfait dont l'id est passé en argument
     * 
     * @param $idFrais 
     */
    public function supprimerFraisHorsForfait($idFrais) {
        $requete_prepare = PdoGSB::$monPdo->prepare("DELETE FROM lignefraishorsforfait "
                . "WHERE lignefraishorsforfait.id = :unIdFrais");
        $requete_prepare->bindParam(':unIdFrais', $idFrais, PDO::PARAM_STR);
        $requete_prepare->execute();
    }

    /**
     * Retourne les mois pour lesquel un visiteur a une fiche de frais
     * 
     * @param $idVisiteur 
     * @return un tableau associatif de clé un mois -aaaamm- et de valeurs l'année et le mois correspondant 
     */
    public function getLesMoisDisponibles($idVisiteur) {
        $requete_prepare = PdoGSB::$monPdo->prepare("SELECT fichefrais.mois AS mois "
                . "FROM fichefrais "
                . "WHERE fichefrais.idvisiteur = :unIdVisiteur "
                . "ORDER BY fichefrais.mois desc");
        $requete_prepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requete_prepare->execute();
        $lesMois = array();
        while ($laLigne = $requete_prepare->fetch()) {		
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois["$mois"] = array(
                "mois" => "$mois",
                "numAnnee" => "$numAnnee",
                "numMois" => "$numMois"
            );
        }
        return $lesMois;
    }

    /**
     * Retourne les informations d'une fiche de frais d'un visiteur pour un mois donné
     * 
     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     * @return un tableau avec des champs de jointure entre une fiche de frais et la ligne d'état 
     */
    public function getLesInfosFicheFrais($idVisiteur, $mois) {
        $requete_prepare = PdoGSB::$monPdo->prepare("SELECT ficheFrais.idEtat as idEtat, ficheFrais.dateModif as dateModif,"
                . "ficheFrais.nbJustificatifs as nbJustificatifs,ficheFrais.montantValide as montantValide, etat.libelle as libEtat "
                . "FROM fichefrais "
                . "INNER JOIN Etat ON ficheFrais.idEtat = Etat.id "
                . "WHERE fichefrais.idvisiteur = :unIdVisiteur "
                . "AND fichefrais.mois = :unMois");
        $requete_prepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requete_prepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requete_prepare->execute();
        $laLigne = $requete_prepare->fetch();
        return $laLigne;
    }

    /**
     * Modifie l'état et la date de modification d'une fiche de frais
     * Modifie le champ idEtat et met la date de modif à aujourd'hui
     * 
     * @param $idVisiteur 
     * @param $mois sous la forme aaaamm
     */
    public function majEtatFicheFrais($idVisiteur, $mois, $etat) {
        $requete_prepare = PdoGSB::$monPdo->prepare("UPDATE ficheFrais "
                . "SET idEtat = :unEtat, dateModif = now() "
                . "WHERE fichefrais.idvisiteur = :unIdVisiteur "
                . "AND fichefrais.mois = :unMois");
        $requete_prepare->bindParam(':unEtat', $etat, PDO::PARAM_STR);
        $requete_prepare->bindParam(':unIdVisiteur', $idVisiteur, PDO::PARAM_STR);
        $requete_prepare->bindParam(':unMois', $mois, PDO::PARAM_STR);
        $requete_prepare->execute();
    }

}
?>