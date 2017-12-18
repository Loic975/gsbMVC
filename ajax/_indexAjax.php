 <?php
session_start();
require_once("../include/config.inc.php");
require_once("../include/fct.inc.php");
require_once ("../include/class.pdogsb.inc.php");
$pdo = PdoGsb::getPdoGsb();

// Verifier qu'un utilisateur est connecté
if ( estConnecte() && isset($_POST['uc']) ) {
    $uc = $_POST['uc'];

    switch ($uc) {
        case 'statVisiteur': {
            include("controleurs/c_statVisiteur.ajx.php");
            break;
        }
    }
}
?>
