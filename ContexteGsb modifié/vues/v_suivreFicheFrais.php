<?php
/**
* Vue Liste Fiche : Gère l'affichage des fiches

*/
?>

<div class="panel panel-info">
    <form id="sort" action="/index.php?uc=suivreFicheFrais" method="post" 
          role="form"></form>
    <table class="table table-bordered table-responsive table-hover">
        
        
        <tr>
            <th></th>
            <th>
                <button name="submit" value="mois" form="sort"
                        class="btn btn-link">Mois création</button>
            </th>
            <th>
                <button name="submit" value="nom" form="sort"
                        class="btn btn-link">Nom</button>
            </th>
            <th>
                <button name="submit" value="prenom" form="sort"
                        class="btn btn-link">Prénom</button>
            </th>
            <th>
                <button name="submit" value="montantvalide" form="sort"
                        class="btn btn-link">Montant validé</button>
            </th>
            <th>
                <button name="submit" value="etatlibelle" form="sort"
                        class="btn btn-link">Etat</button>
            </th>
        </tr>
        
        
        <?php
        foreach ($ficheFraisList as $ficheFrais) {
            $mois = $ficheFrais['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $iduser = $ficheFrais['iduser'];
            $nom = $ficheFrais['nom'];
            $prenom = $ficheFrais['prenom'];
            $montantvalide = $ficheFrais['montantvalide'];
            $idetat = $ficheFrais['idetat'];
            $etatlibelle = $ficheFrais['etatlibelle'];
            $class = $idetat == 'VA' ? 'active' : 'info';
            $action = $idetat == 'VA' ? 'Mettre en paiement' : 'Indiquer comme remboursée';
            ?>
        
        <tr class="<?php echo $class?>" onclick="document.location = '/index.php?uc=suivreFicheFrais&action=afficherFiche&idVisiteur=<?php echo $iduser ?>&mois=<?php echo $mois ?>';">
            <td>
                <a href="/index.php?uc=suivreFicheFrais&action=afficherFiche&idVisiteur=<?php echo $iduser ?>&mois=<?php echo $mois ?>">
                        <?php echo $action ?>
                </a>
            </td>
            <td>
                <?php echo $numAnnee . "-" . $numMois ?>
            </td>
            <td>
                <?php echo $nom ?>
            </td>
            <td>
                <?php echo $prenom ?>
            </td>
            <td>
                <?php echo $montantvalide ?>
            </td>
            <td>
                <?php echo $etatlibelle ?>
            </td>
            
        </tr>
            <?php
        }
        ?>
    </table>
    
</div>
