<?php

require_once 'models/Competition.php';
require_once 'models/User.php';
require_once 'models/Registration.php';
require_once 'models/ErrorLogger.php';


class CompetitionController
{
    /**
     * Affiche la liste des compétitions disponibles.
     *
     * @return void 
     */
    public function competitionsList() : void
    {
        // Vérifier si l'utilisateur est un administrateur
        if (!User::isAdmin()) {
            // Si l'utilisateur n'est pas un administrateur, récupérer uniquement les compétitions publiées
            $competitions = Competition::getPublishedCompetitions();
        } else {
            // Si l'utilisateur est un administrateur, récupérer toutes les compétitions
            $competitions = Competition::getCompetitions();
        }

        // Charger la vue pour afficher la liste des compétitions
        require_once('views/competitionsList.php');
    }

    /**
     * Affiche les détails d'une compétition.
     *
     * @return void 
     */
    public function competitionDetails() : void
    {
        if (isset($_GET['competitionId'])) {
            $competitionId = filter_input(INPUT_GET, 'competitionId', FILTER_VALIDATE_INT);
            $competition = Competition::getById($competitionId);

            if ($competition) {
                $boats = Registration::getBoatsInCompetition($competitionId);
                require_once('views/competitionDetails.php');
            } else {
                $_SESSION['error_message'] = "La compétition avec l'identifiant spécifié n'existe pas.";
                header('Location: index.php?url=competitionsList');
                exit;
            }
        } else {
            $_SESSION['error_message'] = "Aucun identifiant de compétition fourni.";
            header('Location: index.php');
            exit;
        }
    }
}