<?php

require_once("include/fct.inc.php");
require_once ("include/class.pdogsb.inc.php");
session_start();
$pdo = PdoGsb::getPdoGsb();
$estConnecte = estConnecte();
include("vues/v_entete.php");
if (!isset($_REQUEST['uc']) && !$estConnecte) {
    $_REQUEST['uc'] = 'connexion';
} else if (empty($_REQUEST['uc'])) {
    $_REQUEST['uc'] = 'accueil';
}
$uc = $_REQUEST['uc'];

switch ($uc) {
    case 'connexion': {
            include("controleurs/c_connexion.php");
            break;
        }
    case 'accueil': {
            include("controleurs/c_accueil.php");
            break;
        }
    case 'gererFrais' : {
            include("controleurs/c_gererFrais.php");
            break;
        }
    case 'etatFrais' : {
            include("controleurs/c_etatFrais.php");
            break;
        }
    case 'deconnexion' : {
            include("controleurs/c_deconnexion.php");
            break;
        }
}
include("vues/v_pied.php");
?>