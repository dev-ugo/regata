<?php

require_once 'models/User.php';
require_once 'models/Boat.php';
require_once 'models/ErrorLogger.php';

class HomeController
{
    /**
     * Affiche la page d'accueil
     *
     * @return void
     */
    public function index()
    {

        $boats = Boat::getBoats();

        // Si aucune erreur n'est survenue, inclure la page d'accueil
        require_once('views/home.php');
    }
}