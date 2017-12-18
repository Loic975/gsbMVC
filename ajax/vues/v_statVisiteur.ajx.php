<hr>
<br/>
<h4><strong style="text-decoration: underline; color: red;">Frais remboursé du visiteur par année :</strong></h4>
<br/>
<table class="table table-bordered table-responsive">
    <tr>
        <th style="border:none">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
        <th style="text-align:center"> <?php echo htmlspecialchars("Année") ?></th>
        <th style="text-align:center"> <?php echo htmlspecialchars("Montant forfait") ?></th>
        <th style="text-align:center"> <?php echo htmlspecialchars("Montant hors forfait") ?></th>
        <th style="text-align:center"> <?php echo htmlspecialchars("Montant remboursé") ?></th>
    </tr>
    <?php
    $sommeHF = 0;
    $sommeF = 0;
    $sommeMR = 0;
    for ($i = 0; $i < count($lesAnnees); $i++) {
    ?>
    <tr>
        <td style="border:none"></td>
        <td style="text-align:center" class="annee">
            <?php
            echo $lesAnnees[$i];
            ?>
        </td>
        <?php
        if($lesFraisHorsForfait[$i][1] != 0)
        {
        ?>
        <td style="text-align:center" class="fraisHF">
            <?php
            echo $lesFraisForfait[$i][1].' €';
            $sommeHF += $lesFraisForfait[$i][1];
            ?>
        </td>
        <td style="text-align:center" class="fraisF">
            <?php
            echo $lesFraisHorsForfait[$i][1].' €';
            $sommeF += $lesFraisHorsForfait[$i][1];
            ?>
        </td>
        <td style="text-align:center" class="fraisMR">
            <?php
            echo $lesFraisHorsForfait[$i][1]+$lesFraisForfait[$i][1].' €';
            $sommeMR = $sommeMR + $lesFraisHorsForfait[$i][1]+$lesFraisForfait[$i][1];
            ?>
        </td>
        <?php
        }
        else
        {
        ?>
        <td colspan="3" style="text-align: center"><strong>Pas de frais trouvés cette année !</strong></td>
        <?php
        }
        }
        ?>
        </tr>
        <tr>
        <th colspan = "2"><?php echo htmlspecialchars("Montant totaux")
        ?></th>
        <td style="text-align:center"><?php echo $sommeHF.' €'; ?> </td>
        <td style="text-align:center"><?php echo $sommeF.' €'; ?> </td>
        <td style="text-align:center"><?php echo $sommeMR.' €'; ?> </td>
    </tr>
</table>
<br/> <br/>