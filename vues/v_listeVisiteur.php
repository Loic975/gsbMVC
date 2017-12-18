<h2>Statistiques par visiteur</h2>
<div class="row">
    <div class="col-md-4">
        <h3>Sélectionner un visiteur : </h3>
    </div>
    <div class="col-md-4">
        <form method="post" role="form">
            <div class="form-group">
                <label for="lstVisiteur" accesskey="n">Visiteur : </label>
                <select id="lstVisiteur" name="lstVisiteur" class="form-control">
                    <?php
                    foreach ($lesVisiteurs as $unVisiteur) 
                        {
                            $id = $unVisiteur[0];
                            $nomComplet = $unVisiteur[1];
                            ?>
                            <option value="<?php echo $id; ?>"><?php echo $id; echo " - "; echo $nomComplet; ?> </option>
                            <?php
                        }
                        ?>    
                </select>
            </div>
            <input id="ok" value="Valider" class="btn btn-success" role="button" onclick="afficherStatVisiteur()" />
            <input id="annuler" type="reset" value="Effacer" class="btn btn-danger" role="button" />
        </form>      
    </div>
</div>
<div id="zoneStat">
</div>