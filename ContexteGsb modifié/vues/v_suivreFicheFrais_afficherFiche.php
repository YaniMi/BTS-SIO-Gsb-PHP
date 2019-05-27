<?php
    
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<a href="/index.php?uc=suivreFicheFrais">Retour à la liste des fiches</a>

<?php

$numAnnee = substr($mois, 0, 4);
$numMois = substr($mois, 4, 2);
    
include 'v_etatFrais.php';
    
if($idEtat == "VA"){
    $buttonText = 'Mettre en paiement';
    $class = 'warning';
} elseif($idEtat == "PM"){
    $buttonText = 'Indiquer comme Remboursée';
    $class = 'info';
}
?>

<div class="row">
    <div class="col-md-2 col-md-offset-5">
        <form id="majFiche" action="/index.php?uc=suivreFicheFrais&action=majFiche" method="post" 
              role="form">
            <input type="hidden" name="idVisiteur" value="<?php echo $idVisiteur ?>"/>
            <input type="hidden" name="mois" value="<?php echo $mois ?>"/>
            <button type="submit" form="majFiche" 
                    class="btn btn-lg btn-<?php echo $class; ?>"
                    onclick="return confirm('Voulez-vous vraiment <?php echo $buttonText ?> cette fiche?');">
            <?php echo $buttonText ?>
            </button>
        </form>
    </div>
    
</div>