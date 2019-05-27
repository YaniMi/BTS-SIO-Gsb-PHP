<?php
/**
 * Vue Menu Comptable
 *
 * PHP Version 7
 *
**/
?>
<script>
    function showMonths(id) {
        var xhttp;
        var listeMoisSelect = document.getElementById("lstMois");

        if (id == "") {
            var option = document.createElement('option');
            option.innerHtml = "";
            listeMoisSelect.appendChild(option);
            return;
        }
        xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var listeMois = JSON.parse(this.responseText);

                if(listeMois.length > 0){
                    listeMois.forEach(function(mois) {
                        var option = document.createElement('option');
                        option.innerHTML = mois['numMois'] + '/' + mois['numAnnee'];
                        option.value = mois['mois'];
                        listeMoisSelect.appendChild(option);
                    });
                } else {
                    //afficher "aucune fiche clotorée pour ce visiteur"
                }
            }
        };
        xhttp.open("GET", "ajax/getMoisFicheFraisCL.php?idVisiteur="+id, true);
        xhttp.send();
    }

</script>
    <?php
    if(!empty($idVisiteur)){
        ?>
<script>
    window.addEventListener("DOMContentLoaded", function() {
        showMonths(<?php echo $idVisiteur ?>);
    }, false);
</script>
            <?php
    }
    ?>

<h2><?php if(isset($v_title)) echo $v_title;?></h2>
<div class="row">

        <?php if(!empty($lesVisiteurs)) {
            ?>
    <div class="col-md-4">
        <h3>Sélectionner un visiteur : </h3>
    </div>
    <div class="col-md-4">
        <form action="/index.php?uc=validerFicheFrais&action=afficherFiche"
              method="post" role="form">
            <div class="form-group">
                <label for="lstVisiteur" accesskey="n">Nom, Prénom : </label>
                <select id="lstVisiteur" name="idVisiteur" class="form-control"
                        onchange="showMonths(this.value)">
                    <option value=""></option>
                    <?php
                    foreach ($lesVisiteurs as $unVisiteur) {
                        $id = $unVisiteur['id'];
                        $nom = $unVisiteur['nom'];
                        $prenom = $unVisiteur['prenom'];
                        if(!empty($idVisiteur) && $idVisiteur == $unVisiteur['id']){
                            ?>
                    <option value="<?php echo $id ?>" selected="true">
                            <?php echo $nom . ' ' . $prenom ?>
                    </option>
                                <?php
                        } else {
                    ?>
                    <option value="<?php echo $id ?>">
                            <?php echo $nom . ' ' . $prenom ?>
                    </option>
                        <?php
                        }
                    }
                    ?>
                </select>

                <label for="lstMois" accesskey="n">Mois : </label>

                <select id="lstMois" name="mois" class="form-control">
                </select>


            </div>
            <input id="ok" type="submit" value="Valider" class="btn btn-success"
                   role="button">
            <input id="annuler" type="reset" value="Effacer" class="btn btn-danger"
                   role="button">
        </form>
    </div>
        <?php
        } else {
            ?>
    <div class="alert alert-warning" role="alert">
        Aucun visiteur dans cette entreprise
    </div>
            <?php
        }
            ?>

</div>
