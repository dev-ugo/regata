<?php

require_once 'models/User.php';
require_once 'models/Boat.php';
require_once 'models/Competition.php';
require_once 'models/Registration.php';
require_once 'models/Crew.php';
require_once 'models/ErrorLogger.php';


class RegistrationController
{
    /**
     * Enregistre un bateau à une compétition.
     * Cette fonction récupère les bateaux possédés par l'utilisateur actuel,
     * les compétitions ouvertes et tous les utilisateurs. Elle valide les données du formulaire
     * et enregistre le bateau à la compétition si la validation réussit.
     *
     * @return void
     */
    public function registerBoatToCompetition(): void
    {
        $registerBoatToCompetition = filter_input(INPUT_POST, 'registerBoatToCompetition', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        // Récupération des bateaux appartenant à l'utilisateur actuel
        $boats = Boat::getBoatsByOwner($_SESSION['userId']);

        // Récupération des compétitions ouvertes
        $competitions = Competition::getOpenCompetitions();

        // Récupération de tous les utilisateurs
        $users = User::getAllUsers();

        // Indicateur de redirection
        $redirect = false;

        // Récupération des données du formulaire
        $formData = [
            'boatId' => filter_input(INPUT_POST, 'boatId', FILTER_VALIDATE_INT) ?? '',
            'competitionId' => filter_input(INPUT_POST, 'competitionId', FILTER_VALIDATE_INT) ?? '',
            'skipperId' => filter_input(INPUT_POST, 'skipperId', FILTER_VALIDATE_INT) ?? '',
            'crewMembers' => filter_input(INPUT_POST, 'crewMembers', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY) ?? []
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $registerBoatToCompetition == "registerBoatToCompetition") {

            // Initialisation d'un tableau pour stocker les messages d'erreur
            $errorMessages = [];

            // Vérification si le skipper est sélectionné comme membre d'équipage
            if (in_array($formData['skipperId'], $formData['crewMembers'])) {
                $errorMessages[] = "Le skipper ne peut pas être aussi un membre d'équipage.";
            }

            // Vérification si au moins un membre d'équipage est sélectionné
            if (empty($formData['crewMembers'])) {
                $errorMessages[] = "Au moins un membre d'équipage doit être inscrit.";
            }

            // Vérification si le bateau est déjà inscrit à la compétition
            if (Registration::isBoatAlreadyRegisteredInCompetition($formData['boatId'], $formData['competitionId'])) {
                $errorMessages[] = "Ce bateau est déjà inscrit dans cette compétition.";
            }

            // Vérification si le skipper est déjà inscrit à la compétition sur un autre bateau
            if (Registration::checkIfUserIsAlreadyRegistered($formData['skipperId'], $formData['competitionId'])) {
                $errorMessages[] = "Le skipper est déjà inscrit dans cette compétition sur un autre bateau.";
            }

            // Vérification pour chaque membre d'équipage s'il est déjà inscrit à la compétition sur un autre bateau
            foreach ($formData['crewMembers'] as $memberId) {
                if (Registration::checkIfUserIsAlreadyRegistered($memberId, $formData['competitionId'])) {
                    $errorMessages[] = "Un ou plusieurs membres d'équipage sont déjà inscrits dans cette compétition sur un autre bateau.";
                }
            }

            // Si aucune erreur n'est survenue, enregistrement du bateau à la compétition
            if (empty($errorMessages) && Registration::registerBoatToCompetition($formData['boatId'], $formData['competitionId'], $formData['skipperId'], $formData['crewMembers'])) {
                $_SESSION['success_message'] = "Bateau inscrit avec succès.";
                $redirect = true;
            } else {
                // Sinon, stockage des messages d'erreur
                $_SESSION['error_message'] = implode("<br>", $errorMessages);
            }
        }

        // Redirection si nécessaire
        if ($redirect) {
            header('Location: index.php?url=myRegisteredBoats');
            exit;
        }

        // Inclusion du fichier de vue pour afficher le formulaire d'inscription du bateau à la compétition
        require_once('views/registerBoatToCompetition.php');
    }


    /**
     * Affiche les bateaux inscrits par l'utilisateur connecté.
     *
     * @return void
     */
    public function myRegisteredBoats()
    {
        // Vérifie si l'utilisateur est connecté
        User::redirectIfNotConnected();

        // Récupère les inscriptions pour l'utilisateur connecté
        $userId = $_SESSION['userId'];
        $registrations = Registration::getRegistrationsByUserId($userId);

        // Charge la vue avec les inscriptions de l'utilisateur
        require_once('views/myRegisteredBoats.php');
    }


    /**
     * Supprime une inscription
     *
     * @return void
     */
    public function deleteRegistration()
    {
        // Vérifiez si l'utilisateur est connecté
        User::redirectIfNotConnected();

        // Récupération de l'ID de l'inscription à partir de la requête
        $registrationId = $_GET['registrationId'] ?? null;

        // Initialisation d'un tableau pour stocker les messages d'erreur
        $errorMessages = [];

        // Vérifie si l'ID d'inscription est fourni
        if (!$registrationId) {
            $errorMessages[] = "Aucune inscription spécifiée.";
        }

        // Si des erreurs existent déjà, évitez les opérations coûteuses
        if (empty($errorMessages)) {
            // Vérifie si l'inscription peut être supprimée
            if (Registration::canDeleteRegistration($registrationId)) {
                if (Registration::deleteRegistration($registrationId)) {
                    $_SESSION['success_message'] = "L'inscription a été supprimée avec succès.";
                    header('Location: index.php?url=myRegisteredBoats');
                    exit;
                } else {
                    $errorMessages[] = "Erreur lors de la suppression de l'inscription.";
                }
            } else {
                $errorMessages[] = "Cette inscription ne peut pas être supprimée car elle a déjà été validée.";
            }
        }

        // Gestion des messages d'erreur
        if (!empty($errorMessages)) {
            $_SESSION['error_message'] = implode("<br>", $errorMessages);
            header('Location: index.php?url=myRegisteredBoats');
            exit;
        }
    }



    /**
     * Affiche tous les bateaux inscrits à des compétitions.
     *
     * @return void
     */
    public function registeredBoats(): void
    {
        // Redirige l'utilisateur s'il n'est pas un administrateur
        User::redirectIfNotAdmin();

        // Récupère tous les bateaux inscrits à des compétitions
        $boats = Registration::getAllRegisteredBoats();

        // Charge la vue avec la liste des bateaux inscrits
        require_once('views/registeredBoats.php');
    }


    /**
     * Valide une inscription à une compétition.
     *
     * @return void
     */
    public function validateBoat()
    {
        $boatId = filter_input(INPUT_GET, 'boatId', FILTER_VALIDATE_INT); // Récupère l'ID du bateau depuis l'URL

        if (Registration::validateRegistration($boatId)) {
            $_SESSION['success_message'] = "L'inscription a été validée avec succès.";
        } else {
            $_SESSION['error_message'] = "Erreur lors de la validation de l'inscription.";
        }

        header('Location: index.php?url=registeredBoats');
        exit;
    }

    /**
     * Invalide l'inscription d'un bateau à une compétition.
     * 
     * @return void Redirige l'utilisateur vers la page des bateaux inscrits après l'opération.
     */
    public function invalidateBoat()
    {
        $boatId = filter_input(INPUT_GET, 'boatId', FILTER_VALIDATE_INT); // Récupère l'ID du bateau depuis l'URL

        if (Registration::invalidateRegistration($boatId)) {
            $_SESSION['success_message'] = "L'inscription a été invalidée avec succès.";  // Ajoute un message de succès à la session
        } else {
            $_SESSION['error_message'] = "Erreur lors de l'invalidation de l'inscription.";  // Ajoute un message d'erreur à la session
        }

        header('Location: index.php?url=registeredBoats');  // Redirige vers la page des bateaux inscrits
        exit;  // Assure que le script PHP s'arrête après la redirection
    }

    /**
     * Edite une inscription existante.
     *
     * @return void 
     */
    public function editRegistration(): void
    {
        $registrationId = $_GET['registrationId'];
        $registration = Registration::getRegistrationById($registrationId);
        if (!$registration) {
            $_SESSION['error_message'] = "Inscription introuvable.";
            header('Location: index.php?url=myRegisteredBoats');
            exit;
        }

        $users = User::getAllUsers();
        $crewMembers = Crew::getByRegistrationId($registrationId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newSkipperId = $_POST['skipperId'];
            $newCrewMembers = $_POST['crewMembers'] ?? [];

            if (empty($newCrewMembers)) {
                $_SESSION['error_message'] = "Au moins un membre d'équipage doit être inscrit.";
                header("Location: index.php?url=editRegistration&registrationId={$registrationId}");
                exit;
            }

            // Vérifie si le nouveau skipper est déjà inscrit comme skipper dans une autre inscription pour la même compétition
            // et n'est pas le skipper actuel déjà enregistré pour cette inscription.
            if ($newSkipperId != $registration['skipperId'] && Registration::checkIfUserIsAlreadyRegistered($newSkipperId, $registration['competitionId'])) {
                $_SESSION['error_message'] = "Ce skipper est déjà inscrit comme skipper d'un autre bateau dans cette compétition.";
                header("Location: index.php?url=editRegistration&registrationId={$registrationId}");
                exit;
            }

            // Tentative de mise à jour de l'inscription
            if (Registration::updateRegistration($registrationId, $newSkipperId, $newCrewMembers)) {
                $_SESSION['success_message'] = "Inscription mise à jour avec succès.";
                header('Location: index.php?url=myRegisteredBoats');
                exit;
            } else {
                $_SESSION['error_message'] = "Erreur lors de la mise à jour de l'inscription.";
                header("Location: index.php?url=editRegistration&registrationId={$registrationId}");
                exit;
            }
        }

        require_once('views/editRegistration.php');
    }
}