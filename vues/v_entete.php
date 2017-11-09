<!DOCTYPE html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta charset="UTF-8" />
    <title>Intranet du Laboratoire Galaxy-Swiss Bourdin</title> 
    <meta name="description" content="" />
    <meta name="author" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="./css/style.min.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <div class="container">
        <?php
        if ($estConnecte) {
            ?>
            <div class="header">
                <div class="row vertical-align">
                    <div class="col-md-4">
                        <h1><img src="./img/logo.jpg" class="img-responsive" alt="Laboratoire Galaxy-Swiss Bourdin" title="Laboratoire Galaxy-Swiss Bourdin"></h1>
                    </div>
                    <div class="col-md-8">
                        <ul class="nav nav-pills pull-right" role="tablist">
                            <li <?php if (!isset($_REQUEST['uc']) || $_REQUEST['uc'] == 'accueil') { ?> class="active"<?php } ?>><a href="index.php">Accueil</a></li>
                            <li <?php if (isset($_REQUEST['uc']) && $_REQUEST['uc'] == 'gererFrais') { ?> class="active"<?php } ?>><a href="index.php?uc=gererFrais&action=saisirFrais">Saisie fiche de frais</a></li>
                            <li <?php if (isset($_REQUEST['uc']) && $_REQUEST['uc'] == 'etatFrais') { ?> class="active"<?php } ?>><a href="index.php?uc=etatFrais&action=selectionnerMois">Mes fiches de frais</a></li>
                            <li <?php if (isset($_REQUEST['uc']) && $_REQUEST['uc'] == 'deconnexion') { ?> class="active"<?php } ?>><a href="index.php?uc=deconnexion&action=demandeDeconnexion">Déconnexion</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        <?php
        } else {
            ?>   
            <h1><img src="./img/logo.jpg" class="img-responsive center-block" alt="Laboratoire Galaxy-Swiss Bourdin" title="Laboratoire Galaxy-Swiss Bourdin"></h1>
        <?php
        }
        ?>