<?php
/**
 * Fonctions pour l'application GSB
 * @package default
 * @author Cheri Bibi
 * @version    1.0
 */


/**
 * initialise un client URL 
 * @author Marianne
 * @return le client URL 
 */
function initCurl($complementURL) {
    // construire l'URL
    $url = API_URL.$complementURL;
    // initialiser la session http
    $unCurl = curl_init($url);
    // Préciser que la réponse est souhaitée
    curl_setopt($unCurl, CURLOPT_RETURNTRANSFER, true);
    // retourner le client HTTP
    return $unCurl;
}


/**
 * consommer le service d'authentification 
 * @author md
 * @return le code HTTP et le jeton (ou un message d'erreur)
 */
function authAPI($login, $mdp) {
    // initialiser le client URL
    $unCurl = initCurl(LOGIN);
    // Préciser le Content-Type
    curl_setopt($unCurl,CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
    
    // Préciser le type de requête HTTP : POST 
    curl_setopt($unCurl, CURLOPT_POST, true);
    
    // créer le tableau des données à envoyer par POST
    $champsPost = array(
        '_username'  => $login,
        '_password'  => $mdp
    );     
    // Créer la chaine url encodée selon la RFC1738 à partir du tableau de paramètres séparés par le caractère &
    $trame = http_build_query($champsPost, '', '&');
    // Ajouter les paramètres
    curl_setopt($unCurl, CURLOPT_POSTFIELDS, $trame);
    
    // Envoyer la requête 
    $reponse = curl_exec($unCurl);
    // convertir la chaîne encodée JSON en une variable PHP    
    $retour = json_decode($reponse, false);
    // récupérer le status
    $resultStatus = curl_getinfo($unCurl);
    // vérifier si le jeton a été obtenu
    if ($resultStatus['http_code'] == 200) {
        // dans ce cas le retour est un objet qui expose la propriété token       
        $laReponse = (object) [
            'code'  => $resultStatus['http_code'],
            'token'  => $retour->token ];
    } else {
        // dans ce cas le retour est un objet qui expose les propriétés code et message
        $laReponse = $retour;  
    }
        
    // fermer la session
    curl_close($unCurl);
    
    // retourner la réponse 
    return $laReponse;     
}

/**
 * méthode GET  - API Rest Frais 
 * @author md
 * @return la réponse 
 */
function getAPI($url) {
    // initialiser le client URL
    $unCurl = initCurl($url);
    // Définir l'entête avec le jeton d'authentification
    $header = array();
    $header[] = 'Authorization: Bearer '.$_SESSION['token'];
    curl_setopt($unCurl, CURLOPT_HTTPHEADER, $header);
    
    // Envoyer la requête
    $reponse = curl_exec($unCurl);

    // récupérer le status, ici juste le code HTTP
    $httpCode = curl_getinfo($unCurl, CURLINFO_HTTP_CODE);
    // vérifier si il y a une réponse
    if ($httpCode == 404) {
        // false indiquera qu'il n'y a pas de ligne retournée
        $laReponse = false;
    }
    else
    {
        // convertir la chaîne encodée JSON en une variable PHP    
        $laReponse = json_decode($reponse, false);
    }
    
    // fermer la session
    curl_close($unCurl);

    // retourner la réponse 
    return $laReponse;
}


/**
 * Teste si un quelconque visiteur est connecté
 * @return vrai ou faux 
 */
function estConnecte() {
    return isset($_SESSION['idVisiteur']);
}
/**
 * Enregistre dans une variable session les infos d'un visiteur
 * 
 * @param $id 
 * @param $nom
 * @param $prenom
 */
function connecter($id, $nom, $prenom, $token) {
    $_SESSION['idVisiteur'] = $id;
    $_SESSION['nom'] = $nom;
    $_SESSION['prenom'] = $prenom;
    $_SESSION['token'] = $token;
}
/**
 * Détruit la session active
 */
function deconnecter() {
    session_destroy();
}
/**
 * Transforme une date au format français jj/mm/aaaa vers le format anglais aaaa-mm-jj
 * 
 * @param $madate au format  jj/mm/aaaa
 * @return la date au format anglais aaaa-mm-jj
 */
function dateFrancaisVersAnglais($maDate) {
    @list($jour, $mois, $annee) = explode('/', $maDate);
    return date('Y-m-d', mktime(0, 0, 0, $mois, $jour, $annee));
}
/**
 * Transforme une date au format format anglais aaaa-mm-jj vers le format français jj/mm/aaaa 
 * 
 * @param $madate au format  aaaa-mm-jj
 * @return la date au format format français jj/mm/aaaa
 */
function dateAnglaisVersFrancais($maDate) {
    @list($annee, $mois, $jour) = explode('-', $maDate);
    $date = "$jour" . "/" . $mois . "/" . $annee;
    return $date;
}
/**
 * retourne le mois au format aaaamm selon le jour dans le mois
 * 
 * @param $date au format  jj/mm/aaaa
 * @return le mois au format aaaamm
 */
function getMois($date) {
    @list($jour, $mois, $annee) = explode('/', $date);
    if (strlen($mois) == 1) {
        $mois = "0" . $mois;
    }
    return $annee . $mois;
}
/* gestion des erreurs */
/**
 * Indique si une valeur est un entier positif ou nul
 * 
 * @param $valeur
 * @return vrai ou faux
 */
function estEntierPositif($valeur) {
    return preg_match("/[^0-9]/", $valeur) == 0;
}
/**
 * Indique si un tableau de valeurs est constitué d'entiers positifs ou nuls
 * 
 * @param $tabEntiers : le tableau
 * @return vrai ou faux
 */
function estTableauEntiers($tabEntiers) {
    $ok = true;
    foreach ($tabEntiers as $unEntier) {
        if (!estEntierPositif($unEntier)) {
            $ok = false;
        }
    }
    return $ok;
}
/**
 * Vérifie si une date est inférieure d'un an à la date actuelle
 * 
 * @param $dateTestee 
 * @return vrai ou faux
 */
function estDateDepassee($dateTestee) {
    $dateActuelle = date("d/m/Y");
    @list($jour, $mois, $annee) = explode('/', $dateActuelle);
    $annee--;
    $AnPasse = $annee . $mois . $jour;
    @list($jourTeste, $moisTeste, $anneeTeste) = explode('/', $dateTestee);
    return ($anneeTeste . $moisTeste . $jourTeste < $AnPasse);
}
/**
 * Vérifie si une date est postérieure à la date du jour
 * 
 * @param $dateTestee 
 * @return vrai ou faux
 */
function estDatePosterieure($dateTestee) {
    $dateActuelle = new DateTime("now");
    @list($jourTestee, $moisTestee, $anneeTestee) = explode('/', $dateTestee);
    $dateFrais = new DateTime($anneeTestee.'-'.$moisTestee.'-'.$jourTestee);
    return ($dateFrais > $dateActuelle);
}
/**
 * Vérifie la validité du format d'une date française jj/mm/aaaa 
 * 
 * @param $date 
 * @return vrai ou faux
 */
function estDateValide($date) {
    $tabDate = explode('/', $date);
    $dateOK = true;
    if (count($tabDate) != 3) {
        $dateOK = false;
    } else {
        if (!estTableauEntiers($tabDate)) {
            $dateOK = false;
        } else {
            if (!checkdate($tabDate[1], $tabDate[0], $tabDate[2])) {
                $dateOK = false;
            } 
        }
    }
    return $dateOK;
}
/**
 * Vérifie que le tableau de frais ne contient que des valeurs numériques 
 * 
 * @param $lesFrais 
 * @return vrai ou faux
 */
function lesQteFraisValides($lesFrais) {
    return estTableauEntiers($lesFrais);
}
/**
 * Vérifie la validité des trois arguments : la date, le libellé du frais et le montant 
 * 
 * des message d'erreurs sont ajoutés au tableau des erreurs
 * 
 * @param $dateFrais 
 * @param $libelle 
 * @param $montant
 */
function valideInfosFrais($dateFrais, $libelle, $montant) {
    if ($dateFrais == "") {
        ajouterErreur("Le champ date ne doit pas être vide");
    } else {
        if (!estDatevalide($dateFrais)) {
            ajouterErreur("Date invalide");
        } else {
            if (estDateDepassee($dateFrais)) {
                ajouterErreur("date d'enregistrement du frais dépassé, plus de 1 an");
            } else {
            if (estDatePosterieure($dateFrais)) {
                ajouterErreur("date d'enregistrement du frais postérieure à la date du jour");
            }
            }
        }
    }
    if ($libelle == "") {
        ajouterErreur("Le champ description ne peut pas être vide");
    }
    if ($montant == "") {
        ajouterErreur("Le champ montant ne peut pas être vide");
    } else
    if (!is_numeric($montant)) {
        ajouterErreur("Le champ montant doit être numérique");
    }
}
/**
 * Ajoute le libellé d'une erreur au tableau des erreurs 
 * 
 * @param $msg : le libellé de l'erreur 
 */
function ajouterErreur($msg) {
    if (!isset($_REQUEST['erreurs'])) {
        $_REQUEST['erreurs'] = array();
    }
    $_REQUEST['erreurs'][] = $msg;
}
/**
 * Retoune le nombre de lignes du tableau des erreurs 
 * 
 * @return le nombre d'erreurs
 */
function nbErreurs() {
    if (!isset($_REQUEST['erreurs'])) {
        return 0;
    } else {
        return count($_REQUEST['erreurs']);
    }
}
?>