<?php
$action = $_GET['action'];
$idVisiteur = $_SESSION['idVisiteur'];
switch ($action) {
    case 'voirStatsTotal': {
            $lesDateFrais = $pdo->getDateFicheFrais($idVisiteur);
            $lesAnnees = getAnneeFicheFrais($lesDateFrais);
            $lesFraisHorsForfait = $pdo->getLesFraisHorsForfaitAnnee($idVisiteur, $lesAnnees);
            $lesFraisForfait = $pdo->getLesFraisForfaitAnnee($idVisiteur, $lesAnnees);
            include("vues/v_statsTotal.php");
            break;
        }
}
?>
