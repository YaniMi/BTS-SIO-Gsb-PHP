<?php
/**
 * Vue Formulaire Validation de frais
 *
 */
?>
<h3><?php ?></h3>
<hr>
<div class="panel panel-primary">
    <div class="panel-heading">Fiche de frais de du mois
        <?php echo $numMois . '-' . $numAnnee ?> : </div>
    <div class="panel-body">
        <strong><u>Etat :</u></strong> <?php echo $libEtat ?>
        depuis le <?php echo $dateModif ?> <br>
        <strong><u>Montant validé :</u></strong> <?php echo $montantValide ?>
    </div>
</div>
<div class="panel panel-info">
    <div class="panel-heading">Eléments forfaitisés</div>
    <table class="table table-bordered table-responsive">
        <tr>
            <?php
            foreach ($lesFraisForfait as $fraisForfait) {
                $libelle = $fraisForfait['libelle']; ?>
            <th> <?php echo htmlspecialchars($libelle) ?></th>
                <?php
            }
            ?>
            <th>Actions comptables</th>
        </tr>
        <tr>

            <?php
                foreach ($lesFraisForfait as $unFrais) {
                    $idFrais = $unFrais['idfrais'];
                    $libelle = htmlspecialchars($unFrais['libelle']);
                    $quantite = $unFrais['quantite']; ?>
            <td>
                <input type="number" id="idFrais"
                       name="lesFraisForfait[<?php echo $idFrais ?>]"
                       size="10" maxlength="5"
                       value="<?php echo $quantite ?>"
                       form="majFraisForfait"
                       class="form-control">
            </td>
                    <?php
                }
                ?>
            <td>
                <form id="majFraisForfait"
                      action="/index.php?uc=validerFicheFrais&action=majFraisForfait"
                      method="post" role="form">
                    <input type="hidden"
                           name="idVisiteur"
                           size="10" maxlength="5"
                           value="<?php echo $idVisiteur ?>">
                    <input type="hidden"
                           name="mois"
                           size="10" maxlength="5"
                           value="<?php echo $mois ?>">
                    <button name="submit" value="majFraisForfait" class="btn btn-xs btn-success"
                            onclick="return confirm('Voulez-vous vraiment mettre à jour ces frais?');"
                            role="button">Mettre-à-jour</button>

                </form>
            </td>
        </tr>
    </table>
</div>

<div class="panel panel-info">
    <div class="panel-heading">Descriptif des éléments hors forfait -
        <?php echo $nbJustificatifs ?> justificatifs reçus</div>

    <table class="table table-bordered table-responsive">

        <tr>
            <th class="date">Date</th>
            <th class="libelle">Libellé</th>
            <th class='montant'>Montant</th>
            <th class="action">Actions comptables</th>
        </tr>

        <?php
        foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
            $date = $unFraisHorsForfait['date'];
            $libelle = htmlspecialchars($unFraisHorsForfait['libelle']);
            $montant = $unFraisHorsForfait['montant'];
            $idFraisHorsForfait = $unFraisHorsForfait['id'];
            $refus = $unFraisHorsForfait['refus']?>
        <tr<?php if($refus) {echo ' class="danger"';}?>>
            <td><?php echo $date ?></td>
            <td><?php
                    if($refus) {echo 'REFUSE : ';}
                    echo $libelle ?>
            </td>
            <td><?php echo $montant ?></td>
            <td>
                <form id="majFraisHorsForfait_<?php echo $idFraisHorsForfait ?>"
                      action="/index.php?uc=validerFicheFrais&action=majFraisHorsForfait"
                      method="post" role="form">
                    <input type="hidden"
                           name="idVisiteur"
                           size="10" maxlength="5"
                           value="<?php echo $idVisiteur ?>">
                    <input type="hidden"
                           name="mois"
                           size="10" maxlength="5"
                           value="<?php echo $mois ?>">
                    <input type="hidden"
                           name="idFraisHorsForfait"
                           size="10" maxlength="5"
                           value="<?php echo $idFraisHorsForfait ?>">
                </form>
                <button form="majFraisHorsForfait_<?php echo $idFraisHorsForfait ?>"
                        name="submit" value="reporter" class="btn btn-xs btn-warning"
                        role="button"
                        onclick="return confirm('Voulez-vous vraiment reporter ce frais?');">
                    Reporter</button>
                <?php
                if($refus){
                ?>
                <button form="majFraisHorsForfait_<?php echo $idFraisHorsForfait ?>"
                        name="submit" value="annulerRefus" class="btn btn-xs btn-info"
                        onclick="return confirm('Voulez-vous vraiment autoriser ce frais?');"
                        role="button">Annuler ce refus</button>
                <?php
                } else {
                    ?>
                <button form="majFraisHorsForfait_<?php echo $idFraisHorsForfait ?>"
                        name="submit" value="refuser" class="btn btn-xs btn-danger"
                        role="button"
                        onclick="return confirm('Voulez-vous vraiment refuser ce frais?');">
                    Refuser</button>
                            <?php
                }
                ?>

            </td>
        </tr>
            <?php
        }
        ?>
    </table>

</div>

<div class="panel panel-info">
    <div class="panel-heading">Mettre à jour le nombre de justificatif(s)</div>
    <table class="table table-bordered table-responsive">
        <tr>
            <td>
                <form id="majNbJustificatif"
                      action="/index.php?uc=validerFicheFrais&action=majNbJustificatif"
                      method="post" role="form">
                    <input type="hidden"
                           name="idVisiteur"
                           size="10" maxlength="5"
                           value="<?php echo $idVisiteur ?>">
                    <input type="hidden"
                           name="mois"
                           size="10" maxlength="5"
                           value="<?php echo $mois ?>">
                    <input type="number"
                           name="nbJustificatifs"
                           size="10" maxlength="5"
                           class="form-control"
                           value="<?php echo $nbJustificatifs ?>">
                </form>
            </td>
            <td>
                <input type="submit" value="Mettre à jour"
                       class="btn btn-xs btn-success"
                       role="button" form="majNbJustificatif">
            </td>
        </tr>
    </table>
</div>

<div class="row">
    <div class="col-md-2 col-md-offset-5">
        <form id="validerFiche"
              action="/index.php?uc=validerFicheFrais&action=validerFiche"
              method="post" role="form">
            <input type="hidden"
                   name="idVisiteur"
                   size="10" maxlength="5"
                   value="<?php echo $idVisiteur ?>">
            <input type="hidden"
                   name="mois"
                   size="10" maxlength="5"
                   value="<?php echo $mois ?>">
            <input type="hidden"
                   name="idFiche"
                   size="10" maxlength="5"
                   value="<?php echo $mois ?>">
            <button form="validerFiche" name="submit" value="validerFiche"
                    class="btn btn-success btn-lg"
                    role="button"
                    onclick="return confirm('Voulez-vous vraiment valider cette fiche?');">
                Valider cette fiche</button>
        </form>
    </div>

</div>
