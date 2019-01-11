<!-- Breadcrumbs-->
<ol class="breadcrumb">
  <li class="breadcrumb-item">
    <a href="#">Gestion des contrôle</a>
  </li>
  <li class="breadcrumb-item active">Attribution des sujets</li>
</ol>

<?php
  if(!isset($_POST['ok']) && empty($_POST['choix_promotion'])){
 ?>
  <form action="#" method="POST">

    <h2> Choisir un sujet</h2>
    <p>
       Promotion : <select name="choix_promotion">
                      <option value=""></option>
                      <option value="annee1"> Année 1</option>
                      <option value="annee2"> Année 2</option>

                    </select>
    </p>

    <button type="submit" value="ok" class="button">Valider</button>
  </form>

  <?php
}
else if($_POST['choix_promotion']=='annee1'){
  ?>
  <form action="#" method="POST">

    <h2> Choisir un sujet</h2>
    <p>
       Promotion : <select name="choix_promotion">
                      <option value=""></option>
                      <option value="annee1"> Année 1</option>
                      <option value="annee2"> Année 2</option>

                    </select>
    </p>

    <button type="submit" value="ok" class="button">Valider</button>
  </form>

  </br></br>

  <h2> Voici les énoncés pour l'année 1</h2>

  <table>
    <thead>
      <tr>
        <th> Liste des sujets : <?php echo $sujetManager->countSujet();?> sujet(s) a/ont été trouvé</th>
      </tr>
      <?php
      $listSujets = $sujetManager->getListEnonces();
      foreach ($listSujets as $sujet){
      ?>
      <tr>
        <td>Contrôle n° <?php echo $sujet->getIdEnonce(); ?> de Mécanique </td>
      </tr>
    </thead>

    <?php
    }
     ?>
  </table>

<?php
  }
  else{
  ?>
  <form action="#" method="POST">

    <h2> Choisir un sujet</h2>
    <p>
       Promotion : <select name="choix_promotion">
                      <option value=""></option>
                      <option value="annee1"> Année 1</option>
                      <option value="annee2"> Année 2</option>

                    </select>
    </p>

    <button type="submit" value="ok" class="button">Valider</button>
  </form>

  </br></br>

  <h2> Voici les énoncés pour l'année 2</h2>

  <table>
    <thead>
      <tr>
        <th> Liste des sujets : <?php echo $sujetManager->countSujet();?> sujet(s) a/ont été trouvé</th>
      </tr>
      <?php
      $listSujets = $sujetManager->getListEnonces();
      foreach ($listSujets as $sujet){
      ?>
      <tr>
        <td>Contrôle n° <?php echo $sujet->getIdEnonce(); ?> de Mécanique </td>
      </tr>
    </thead>

    <?php
    }
     ?>
  </table>

  <?php
  }
  ?>
