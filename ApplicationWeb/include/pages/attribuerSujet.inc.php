<!-- Breadcrumbs-->
<ol class="breadcrumb">
  <li class="breadcrumb-item">
    <a>Gestion des contrôle</a>
  </li>
  <li class="breadcrumb-item active">Attribution des sujets</li>
</ol>

<?php
if( empty($_POST['choix_promotion'])){
 ?>
  <form action="#" method="POST">

    <h2> Choisir un sujet</h2>
    <p>
       Promotion : <select name="choix_promotion">
                      <option value="1"> Année 1</option>
                      <option value="2"> Année 2</option>

                    </select>


        Liste des sujets : <?php echo $sujetManager->countSujet();?> sujet(s) a/ont été trouvé


        <select name="choix_sujet">

            <?php
            $listSujets = $sujetManager->getListEnonces();

            foreach ($listSujets as $sujet){
              echo $sujet->getIdEnonce();
            ?>
            <option value="<?php echo $sujet->getIdEnonce();?>">Contrôle n° <?php echo $sujet->getIdEnonce(); ?> de Mécanique</option>
            <?php
            }
             ?>
        </select>

        Date de limite de réponse :
        <input type="date" name="date_limite" value="<?php echo date("Y-m-d"); ?>">

      </p>

      <button class="button">Confirmer</button>
    </form>


<?php
}
else if($_POST['choix_promotion']=="1"){

  $sujetChoisi = $_POST['choix_sujet'];

  $listEtudiant = $utilisateurManager->recupererPromotionEtudiante($_POST['choix_promotion']);
  foreach ($listEtudiant as $etudiant) {

    $idMaxSujet = $attribueManager->getIdSujetMaximumByIdEnonce($sujetChoisi);
    $idSujetAlea = rand( 1 ,$idMaxSujet );

    $attribuerSujet = new Attribue(array('idUtilisateur' => $etudiant->getIdUtilisateur(),
                                        'idSujet' => $idSujetAlea,
                                        'dateAttribution' => date("Y-m-d"),
                                        'dateLimite' => $_POST["date_limite"],
                                        ));
    if($attribueManager->countNombreDeSujetAttribuerAUnEtudiant($etudiant->getIdUtilisateur()) < 1){
      $attribueManager->addAttribue($attribuerSujet);
    }
  }
?>

  <script type="text/javascript">
    $(document).ready(function() {
      $('#confirmerModal').modal('show');
    });

    document.getElementById("myButton").onclick = function(){
      location.href="index.php?page=6";
    };
  </script>

<?php

}
else if($_POST['choix_promotion']=="2"){

  $sujetChoisi = $_POST['choix_sujet'];


  $listEtudiant = $utilisateurManager->recupererPromotionEtudiante($_POST['choix_promotion']);
  foreach ($listEtudiant as $etudiant) {

    $idMaxSujet = $attribueManager->getIdSujetMaximumByIdEnonce($sujetChoisi);
    $idSujetAlea = rand( 1 ,$idMaxSujet );

    $attribuerSujet = new Attribue(array('idUtilisateur' => $etudiant->getIdUtilisateur(),
                                        'idSujet' => $idSujetAlea,
                                        'dateAttribution' => date("Y-m-d"),
                                        'dateLimite' => $_POST["date_limite"],
                                        ));
    if($attribueManager->countNombreDeSujetAttribuerAUnEtudiant($etudiant->getIdUtilisateur()) < 1){
        $attribueManager->addAttribue($attribuerSujet);
    }
  }
?>

  <script type="text/javascript">
    $(document).ready(function() {
      $('#confirmerModal').modal('show');
    });

    document.getElementById("myButton").onclick = function(){
      location.href="index.php?page=6";
    };
  </script>



<?php
}
?>

<div class="modal fade" id="confirmerModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Un sujet aléatoire a été ou  est déjà attribué à tout les étudiants ! </h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Appuyer sur "Continuer" pour attribuer d'autres sujets.</div>
            <div class="modal-footer">
                <form id="formConfirmAttribution" name="formConfirmAttribution" method="post" action="#">
                    <button id="myButton" onclick="location.href = 'index.php?page=6';" class="btn btn-secondary" type="button" data-dismiss="modal">Continuer</button>
                </form>
            </div>
        </div>
    </div>
</div>
