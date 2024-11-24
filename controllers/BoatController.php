<?php

require_once 'models/User.php';
require_once 'models/Boat.php';
require_once 'models/Competition.php';
require_once 'models/ErrorLogger.php';

class BoatController
{

    /**
     * Gère l'ajout d'un nouveau bateau dans le système.
     *
     * @return void
     */
    public function addBoat(): void
    {
        // Chargement des classes de bateaux disponibles pour affichage dans le formulaire
        $classes = Boat::getAllClasses();

        // Préparer les données à repasser au formulaire en cas d'erreur
        $formData = [
            'boatName' => '',
            'officialId' => '',
            'sailId' => '',
            'handicap' => '',
            'classId' => ''
        ];

        $redirect = false;
        $submitAddBoat = filter_input(INPUT_POST, 'addBoat', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        // Vérifie la soumission du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $submitAddBoat == "addBoat") {
            // Récupération et validation des données du formulaire
            $formData['boatName'] = filter_input(INPUT_POST, 'boatName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $formData['sailId'] = filter_input(INPUT_POST, 'sailId', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $formData['handicap'] = filter_input(INPUT_POST, 'handicap', FILTER_VALIDATE_FLOAT);
            $formData['classId'] = filter_input(INPUT_POST, 'classId', FILTER_VALIDATE_INT);
            $formData['officialId'] = filter_input(INPUT_POST, 'officialId', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $ownerId = $_SESSION['userId'];

            // Initialisation d'un tableau pour stocker les messages d'erreur
            $errorMessages = [];

            // Validation spécifique
            if (Boat::officialIdExists($formData['officialId'])) {
                $errorMessages[] = "Ce numéro d'immatriculation est déjà utilisé. Veuillez en choisir un autre.";
            }

            if (empty($formData['boatName'])) {
                $errorMessages[] = "Le nom du bateau est obligatoire.";
            }

            if (!$formData['handicap']) {
                $errorMessages[] = "Le handicap doit être un nombre valide.";
            }

            if (!$formData['classId']) {
                $errorMessages[] = "Veuillez sélectionner une classe de bateau valide.";
            }

            if (!$formData['sailId']) {
                $errorMessages[] = "Le numéro de voile est obligatoire";
            }

            if (!$formData['officialId']) {
                $errorMessages[] = "Le numéro d'immatriculation est obligatoire";
            }

            // Tentative d'ajout du bateau dans la base de données si aucune erreur
            if (empty($errorMessages) && Boat::addBoat($formData['boatName'], $formData['officialId'], $formData['sailId'], $formData['handicap'], $ownerId, $formData['classId'])) {
                $_SESSION['success_message'] = "Le bateau a été ajouté avec succès.";
                $redirect = true;
            } else {
                $errorMessages[] = "Erreur lors de l'ajout du bateau.";
            }

            if (!empty($errorMessages)) {
                $_SESSION['error_message'] = implode("<br>", $errorMessages);
            }
        }

        if ($redirect) {
            header('Location: index.php?url=myBoats');
            exit;
        }

        // Chargement de la vue du formulaire d'ajout de bateau si la méthode n'est pas POST ou en cas d'erreur
        require_once('views/addBoat.php');
    }




    /**
     * Affiche la liste des bateaux appartenant à l'utilisateur connecté.
     *
     * @return void
     */
    public function myBoats(): void
    {
        // Vérifier si l'utilisateur est connecté en s'assurant que 'userId' est disponible dans la session
        if (!isset($_SESSION['userId'])) {
            // Rediriger vers la page de connexion si non connecté
            header('Location: index.php?url=login');
            exit;
        }

        $ownerId = $_SESSION['userId'];  // Récupération de l'ID de l'utilisateur connecté
        $boats = Boat::getBoatsByOwner($ownerId);  // Récupération des bateaux appartenant à l'utilisateur

        // Chargement de la vue pour afficher les bateaux
        require_once('views/myBoats.php');
    }


    /**
     * Traite l'ajout de bateaux par un administrateur avec la possibilité de spécifier un propriétaire.
     *
     * @return void
     */
    public function addBoatAdmin(): void
    {
        User::redirectIfNotAdmin();

        // Préparation des données pour le formulaire
        $formData = [
            'boatName' => '',
            'sailId' => '',
            'handicap' => '',
            'ownerId' => '',
            'classId' => '',
            'officialId' => ''
        ];

        $redirect = false;

        $users = User::getAllUsers();
        $classes = Boat::getAllClasses();
        $submitAddBoatAdmin = filter_input(INPUT_POST, 'addBoatAdmin', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        // Vérifier la soumission du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $submitAddBoatAdmin == "addBoatAdmin") {
            // Récupération et validation des données du formulaire
            $formData['boatName'] = filter_input(INPUT_POST, 'boatName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $formData['sailId'] = filter_input(INPUT_POST, 'sailId', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $formData['handicap'] = filter_input(INPUT_POST, 'handicap', FILTER_VALIDATE_FLOAT);
            $formData['ownerId'] = $_POST['ownerId'];
            $formData['classId'] = filter_input(INPUT_POST, 'classId', FILTER_VALIDATE_INT);
            $formData['officialId'] = filter_input(INPUT_POST, 'officialId', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            // Initialisation d'un tableau pour stocker les messages d'erreur
            $errorMessages = [];

            // Validation spécifique
            if (Boat::officialIdExists($formData['officialId'])) {
                $errorMessages[] = "Ce numéro d'immatriculation est déjà utilisé. Veuillez en choisir un autre.";
            }

            if (empty($formData['boatName'])) {
                $errorMessages[] = "Le nom du bateau est obligatoire.";
            }

            if (!$formData['handicap']) {
                $errorMessages[] = "Le handicap doit être un nombre valide.";
            }

            if (!$formData['classId']) {
                $errorMessages[] = "Veuillez sélectionner une classe de bateau valide.";
            }

            if (!$formData['sailId']) {
                $errorMessages[] = "Le numéro de voile est obligatoire";
            }

            if (!$formData['officialId']) {
                $errorMessages[] = "Le numéro d'immatriculation est obligatoire";
            }

            // Tentative d'ajout du bateau dans la base de données si aucune erreur
            if (empty($errorMessages) && Boat::addBoat($formData['boatName'], $formData['officialId'], $formData['sailId'], $formData['handicap'], $formData['ownerId'], $formData['classId'])) {
                $_SESSION['success_message'] = "Bateau ajouté avec succès.";
                $redirect = true;
            } else {
                $errorMessages[] = "Erreur lors de l'ajout du bateau.";
            }

            if (!empty($errorMessages)) {
                $_SESSION['error_message'] = implode("<br>", $errorMessages);
            }
        }

        if ($redirect) {
            header('Location: index.php?url=admin');
            exit;
        }

        // Chargement de la vue du formulaire d'ajout de bateau si la méthode n'est pas POST ou en cas d'erreur
        require_once('views/addBoatAdmin.php');
    }



    /**
     * Permet à un utilisateur de supprimer un de ses bateaux
     *
     * @return void Redirige vers la page de liste des bateaux après exécution.
     */
    public function deleteBoat(): void
    {
        $boatId = $_GET['boatId']; // Récupérer l'ID du bateau à partir de la requête
        $ownerId = $_SESSION['userId']; // ID de l'utilisateur connecté

        // Tentative de suppression du bateau
        if (Boat::deleteBoat($boatId, $ownerId)) {
            $_SESSION['success_message'] = "Bateau supprimé avec succès.";
        } else {
            $_SESSION['error_message'] = "Impossible de supprimer le bateau. Il peut être engagé dans des compétitions validées.";
        }

        // Redirection vers la page affichant la liste des bateaux de l'utilisateur
        header('Location: index.php?url=myBoats');
        exit;
    }


    /**
     * Supprime un bateau sans vérifier la propriété par l'utilisateur connecté.
     *
     * @return void Redirige vers la page admin après tentative de suppression.
     */
    public function deleteBoatAsAdmin(): void
    {
        User::redirectIfNotAdmin();
        $boatId = $_GET['boatId']; // Récupérer l'ID du bateau à partir de la requête

        // Tentative de suppression du bateau avec des privilèges administratifs
        if (Boat::deleteBoatAsAdmin($boatId)) {
            $_SESSION['success_message'] = "Bateau supprimé avec succès.";
        } else {
            $_SESSION['error_message'] = "Impossible de supprimer le bateau.";
        }

        // Redirection vers la page d'administration après la tentative de suppression
        header('Location: index.php?url=admin');
        exit;
    }


    /**
     * Prépare et affiche la page de modification d'un bateau pour un administrateur.
     *
     * @return void Redirection ou chargement de vue selon le contexte.
     */
    public function editBoatAsAdmin(): void
    {

        User::redirectIfNotAdmin();

        // Récupération de l'ID du bateau à partir des paramètres GET
        $boatId = $_GET['boatId']; // Assurez-vous de valider et de nettoyer cet ID pour la sécurité

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Traitement de la soumission du formulaire
            $boatName = filter_input(INPUT_POST, 'boatName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $sailId = filter_input(INPUT_POST, 'sailId', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $handicap = filter_input(INPUT_POST, 'handicap', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $classId = filter_input(INPUT_POST, 'classId', FILTER_VALIDATE_INT);
            $officialId = filter_input(INPUT_POST, 'officialId', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            // Mettre à jour les informations du bateau
            if (Boat::updateBoat($boatId, $boatName, $officialId, $sailId, $handicap, $classId)) {
                $_SESSION['success_message'] = "Bateau mis à jour avec succès.";
                header('Location: index.php?url=admin');
                exit;
            } else {
                $_SESSION['error_message'] = "Erreur lors de la mise à jour du bateau.";
                header('Location: index.php?url=admin');
                exit;
            }
        }

        // Récupération des informations du bateau par son ID
        $boat = Boat::getBoatById($boatId);
        $classes = Boat::getAllClasses();

        // Gestion d'erreur si le bateau n'est pas trouvé
        if (!$boat) {
            $_SESSION['error_message'] = "Le bateau spécifié est introuvable.";
            header('Location: index.php?url=admin');
            exit;
        }

        // Chargement de la vue pour l'édition avec les données du bateau
        require_once('views/editBoatAsAdmin.php');
    }

    /**
     * Prépare et affiche la page de modification d'un bateau.
     *
     * @return void Redirection ou chargement de vue selon le contexte.
     */
    public function editBoat(): void
    {
        // Récupération de l'ID du bateau à partir des paramètres GET
        $boatId = $_GET['boatId']; // Assurez-vous de valider et de nettoyer cet ID pour la sécurité
        $boat = Boat::getBoatById($boatId);
        $classes = Boat::getAllClasses();

        if (!$boat || $boat['ownerId'] != $_SESSION['userId']) {
            $_SESSION['error_message'] = "Ce bateau n'existe pas";
            header('Location: index.php?url=myBoats'); // Redirigez vers la liste des bateaux de l'utilisateur
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Traitement de la soumission du formulaire
            $boatName = filter_input(INPUT_POST, 'boatName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $sailId = filter_input(INPUT_POST, 'sailId', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $handicap = filter_input(INPUT_POST, 'handicap', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $classId = filter_input(INPUT_POST, 'classId', FILTER_VALIDATE_INT);
            $officialId = filter_input(INPUT_POST, 'officialId', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            if (Boat::officialIdExists($officialId)) {
                $_SESSION['error_message'] = "Le numéro d'immatriculation est déja utilisé";
                header("Location: index.php?url=editBoat&boatId=$boatId");
                exit;
            }

            // Mettre à jour les informations du bateau
            if (Boat::updateBoat($boatId, $boatName, $officialId, $sailId, $handicap, $classId)) {
                $_SESSION['success_message'] = "Bateau mis à jour avec succès.";
                header('Location: index.php?url=myBoats');
                exit;
            } else {
                $_SESSION['error_message'] = "Erreur lors de la mise à jour du bateau.";
            }
        }

        // Gestion d'erreur si le bateau n'est pas trouvé
        if (!$boat) {
            $_SESSION['error_message'] = "Le bateau spécifié est introuvable.";
            header('Location: index.php?url=myBoats');
            exit;
        }
        // Chargement de la vue pour l'édition avec les données du bateau
        require_once('views/editBoat.php');
    }
}
