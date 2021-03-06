<!DOCTYPE html>

<html lang="fr">


<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <!--Title / favicon-->
    <title>Projet GMP</title>
    <link href="ressources/favicon.ico" rel="icon" type="image/x-icon" />

    <!--Reset CSS-->
    <link rel="stylesheet" type="text/css" href="css/resetStyle.css"/>

    <!-- Bootstrap core CSS-->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css"
          integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"
            integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut"
            crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"
            integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k"
            crossorigin="anonymous"></script>

    <!-- Custom styles for this template-->
    <link href="css/stylesheet.css" rel="stylesheet">

    <!-- Custom fonts for this template-->
    <link href="packages/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

</head>


<?php //test si la personne est connectée
if (isset($_SESSION['droits'])){

//la procédure de connexion n'est pas en cours
$connexion_en_cours = false;
?>

<body id="page-top">


<nav class="navbar navbar-expand navbar-dark bg-dark static-top">

    <button class="btn btn-link btn-sm text-white order-1 order-sm-0" id="sidebarToggle" href="#">
        <i class="fas fa-bars"></i>
    </button>

    <a class="navbar-brand mr-1" href="index.php">Projet GMP</a>

    <div class="ml-auto mr-0">
        <span class="btn navbar-text " data-toggle="dropdown"><?php echo $_SESSION["co"] ?></span>
    </div>
    <!-- Navbar -->
    <ul class="navbar-nav ml-auto ml-md-0">
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
               aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-user-circle fa-fw"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="index.php?page=14">Paramètres</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">Deconnexion</a>
            </div>
        </li>
    </ul>

</nav>
<?php }else{
?>
<body class="bg-dark">
<?php
} ?>
