<?php
if (!isset($_REQUEST['action'])) {
    $_REQUEST['action'] = 'demandeDeconnexion';
}
$action = $_REQUEST['action'];
switch ($action) {
    case 'demandeDeconnexion': {
            include("vues/v_deconnexion.php");
            break;
        }
    case 'valideDeconnexion': {
            if (estConnecte()) {
                include("vues/v_deconnexion.php");
            } else {
                ajouterErreur("Vous n'êtes pas connecté");
                include("vues/v_erreurs.php");
                include("vues/v_connexion.php");
            }
            break;
        }
    default : {
            include("vues/v_connexion.php");
            break;
        }
}
?>