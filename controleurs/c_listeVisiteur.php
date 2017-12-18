<?php
$action = $_REQUEST['action'];
$idVisiteur = $_SESSION['idVisiteur'];
switch ($action) {
    case 'selectionnerVisiteur': {
            $lesVisiteurs = $pdo->getLesVisiteurs();
            $lesVisiteursFrais = $pdo->getLesVisiteursFrais();
            include("vues/v_listeVisiteur.php");
            break;
        }
}
?>
