<?php
$action = $_POST['action'];
$idVisiteur = $_SESSION['idVisiteur'];
switch ($action) {
    case 'voirStatVisiteur': {
            $idVisiteurSelectionne = $_POST["lstVisiteur"];
            $lesDateFrais = $pdo->getDateFicheFrais($idVisiteurSelectionne);
            $lesAnnees = getAnneeFicheFrais($lesDateFrais);
            $lesFraisHorsForfait = $pdo->getLesFraisHorsForfaitAnnee($idVisiteurSelectionne, $lesAnnees);
            $lesFraisForfait = $pdo->getLesFraisForfaitAnnee($idVisiteurSelectionne, $lesAnnees);
            include("vues/v_statVisiteur.ajx.php");
            break;
        }
}
?>
