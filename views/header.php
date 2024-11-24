<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site TPI</title>
    <link href="https://api.fontshare.com/v2/css?f[]=bespoke-slab@400&f[]=poppins@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <!-- Intégration de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand text-white" href="index.php?url=home">Accueil</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">

                    <ul class="navbar-nav ms-auto">

                        <li class="nav-item">
                            <a class="nav-link text-white" href="index.php?url=competitionsList">Compétitions</a>
                        </li>
                        <?php if (isset($_SESSION['isLoggedIn'])) : ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Compte
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="index.php?url=preferences">Préférences</a></li>
                                    <li><a class="dropdown-item" href="index.php?url=logout">Déconnexion</a></li>
                                    <li><a class="dropdown-item" href="index.php?url=editPassword">Modifier mot de passe</a></li>

                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Inscription
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="index.php?url=registerBoatToCompetition">Inscrire un bateau</a></li>
                                    <li><a class="dropdown-item" href="index.php?url=myRegisteredBoats">Mes inscriptions</a></li>
                                </ul>
                            </li>
                            <?php if (User::isAdmin()) : ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Admin
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                        <li><a class="dropdown-item" href="index.php?url=admin">Admin</a></li>

                                        <li><a class="dropdown-item" href="index.php?url=addBoatAdmin">Ajouter un bateau</a></li>
                                        <li><a class="dropdown-item" href="index.php?url=registeredBoats">Bateaux inscrits</a></li>
                                    </ul>
                                </li>

                            <?php endif ?>

                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Bateaux
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="index.php?url=myBoats">Mes bateaux</a></li>
                                    <li><a class="dropdown-item" href="index.php?url=addBoat">Ajouter un bateau</a></li>
                                </ul>
                            </li>
                        <?php else : ?>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="index.php?url=login">Connexion</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="./index.php?url=register">Inscription</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>