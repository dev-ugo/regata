<?php

require_once 'models/User.php';
require_once 'models/Boat.php';
require_once 'models/ErrorLogger.php';
require_once 'models/Registration.php';

/**
 * Classe AuthController
 * Gère l'authentification des utilisateurs
 */
class AuthController
{
    /**
     * Traite la connexion de l'utilisateur
     *
     * @return void
     */
    public function login(): void
    {
        $submitLogin = filter_input(INPUT_POST, 'submitLogin', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $submitLogin == "submitLogin") {

            // Récupérer les données soumises
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            // Vérifier si les champs sont vides
            if (empty($email) || empty($password)) {
                $_SESSION['error_message'] = "Veuillez remplir tous les champs.";
                header("Location: index.php?url=login");
                exit;
            }

            if (User::login($email, $password)) {
                $_SESSION['success_message'] = "Connexion réussie. Bienvenue!";
                header('Location: index.php?url=home');
                exit;
            } else {
                $_SESSION['error_message'] = "Adresse email ou mot de passe incorrect.";
                header("Location: index.php?url=login");
                exit;
            }
        } else {
            require_once('views/login.php');
        }
    }


    /**
     * Traite l'enregistrement d'un nouvel utilisateur
     *
     * @return void
     */
    public function register(): void
    {
        // Préparation des données à utiliser en cas d'erreur pour repasser au formulaire
        $formData = [
            'email' => filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL) ?? '',
            'firstName' => filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '',
            'lastName' => filter_input(INPUT_POST, 'lastName', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '',
            'password' => filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? ''
        ];

        $redirect = false;

        // Vérifier si le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submitRegister'])) {
            // Initialisation d'un tableau pour stocker les messages d'erreur
            $errorMessages = [];

            // Validation des entrées du formulaire
            if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
                $errorMessages[] = "Veuillez saisir une adresse e-mail valide.";
            }

            if (empty($formData['firstName'])) {
                $errorMessages[] = "Le prénom est obligatoire.";
            }

            if (empty($formData['lastName'])) {
                $errorMessages[] = "Le nom est obligatoire.";
            }

            if (strlen($formData['password']) < 6) {
                $errorMessages[] = "Le mot de passe doit contenir au moins 6 caractères.";
            }

            // Vérification si l'email existe déjà dans la base de données
            if (User::emailExists($formData['email'])) {
                $errorMessages[] = "Email déjà utilisé. Veuillez choisir un autre email.";
            }

            // Si aucune erreur n'est survenue, tenter d'enregistrer l'utilisateur
            if (empty($errorMessages)) {
                $userId = User::register($formData['email'], $formData['firstName'], $formData['lastName'], $formData['password']);
                if ($userId) {
                    $_SESSION['success_message'] = "Inscription réussie.";
                    $redirect = true;
                } else {
                    $errorMessages[] = "Une erreur est survenue lors de l'inscription.";
                }
            }

            // Gérer les messages d'erreur s'il y en a
            if (!empty($errorMessages)) {
                $_SESSION['error_message'] = implode("<br>", $errorMessages);
            }
        }

        // Redirection si nécessaire
        if ($redirect) {
            header('Location: index.php?url=login');
            exit;
        }

        // Affichage du formulaire avec les données repassées en cas d'erreur
        require_once('views/register.php');
    }

    /**
     * Déconnecte l'utilisateur.
     *
     * Cette fonction détruit la session utilisateur et redirige vers la page d'accueil.
     *
     * @return void
     */
    public function logout(): void
    {
        setcookie('success_message', 'Déconnexion réussie. À bientôt!', time() + 10); // Le cookie expire après 10 secondes
        User::logout();
        header("Location: index.php?url=home");
        exit;
    }

    /**
     * Gère la mise à jour des préférences de l'utilisateur
     *
     * @return void
     */
    public function preferences(): void
    {
        User::redirectIfNotConnected();

        $editProfil = filter_input(INPUT_POST, 'editProfil', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        // Récupération des données de l'utilisateur depuis la session
        $emailUser = $_SESSION['emailUser'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $editProfil == "editProfil") {
            // Récupération des données envoyées par le formulaire
            $firstName = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $lastName = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            // Mise à jour des données de l'utilisateur dans la base de données
            if (User::updateUserPreferences($emailUser, $firstName, $lastName)) {
                // Mise à jour réussie, rediriger vers la page de préférences avec un message de succès
                $_SESSION['success_message'] = "Préférences mises à jour avec succès.";
                header('Location: index.php?url=preferences');
                exit;
            } else {
                // Erreur lors de la mise à jour, afficher un message d'erreur
                $_SESSION['error_message'] = "Erreur lors de la mise à jour des préférences.";
                header('Location: index.php?url=preferences');
                exit;
            }
        }

        // Récupération des données de l'utilisateur depuis la base de données en utilisant l'email
        $userData = User::getUserDetails($emailUser);

        // Chargement de la vue des préférences et passage des données de l'utilisateur
        require_once('views/preferences.php');
    }



    /**
     * Affiche la page d'administration de l'application.
     * 
     * @return void
     */
    public function admin(): void
    {
        // Si l'utilisateur n'est pas admin, alors impossbile d'accéder à la page
        User::redirectIfNotAdmin();

        // Récupération de la liste de tous les utilisateurs
        $users = User::getAllUsers();

        // Récupération de la liste de tous les bateaux
        $boats = Boat::getBoats();

        // Chargement de la vue d'administration avec les données récupérées
        require_once('views/admin.php');
    }


    /**
     * Traite la demande de modification du mot de passe de l'utilisateur.
     *
     * @return void
     */
    public function editPassword(): void
    {
        // Vérifie si le formulaire de modification de mot de passe a été soumis
        $submitEditPassword = filter_input(INPUT_POST, 'editPassword', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $submitEditPassword == "editPassword") {

            // Récupération et nettoyage des données du formulaire
            $oldPassword = filter_input(INPUT_POST, 'old_password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $newPassword = filter_input(INPUT_POST, 'new_password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);


            // Validation pour la longueur du mot de passe
            if (!empty($newPassword) && strlen($newPassword) < 6) {
                $_SESSION['error_message'] = "Le mot de passe doit contenir au moins 6 caractères.";
                header('Location: index.php?url=editPassword');  // Redirige vers la page de modification
                exit;
            }

            // Tentative de mise à jour du mot de passe
            if (User::editPassword($oldPassword, $newPassword)) {
                // Enregistrement du message de succès et redirection vers la page de confirmation
                $_SESSION['success_message'] = "Votre mot de passe a été modifié avec succès.";
                header("Location: index.php?url=editPassword");
                exit;
            } else {
                // Enregistrement du message d'erreur et redirection pour réessayer
                $_SESSION['error_message'] = "Échec de la modification du mot de passe. Veuillez réessayer.";
                header("Location: index.php?url=editPassword");
                exit;
            }
        } else {
            // Affichage du formulaire si aucune donnée POST n'est traitée
            require_once('views/editPassword.php');
        }
    }

    /**
     * Gère la requête pour éditer un utilisateur
     *
     * @return void Redirige l'utilisateur ou charge la vue d'édition selon le cas.
     */
    public function editUser()
    {
        User::redirectIfNotAdmin();  // S'assure que seul un administrateur peut accéder à cette fonctionnalité
        $userId = filter_input(INPUT_GET, 'userId', FILTER_VALIDATE_INT);  // Récupère l'ID utilisateur de l'URL
        $user = User::getUserById($userId);  // Récupère les détails de l'utilisateur

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {  // Vérifie si la requête est de type POST
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);  // Sanitise et valide l'email
            $firstName = filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);  // Sanitise le prénom
            $lastName = filter_input(INPUT_POST, 'lastName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);  // Sanitise le nom
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);  // Sanitise le mot de passe
            $isAdmin = filter_input(INPUT_POST, 'isAdmin', FILTER_VALIDATE_INT);  // Sanitise et valide le statut d'administrateur

            // Validation pour la longueur du mot de passe
            if (!empty($password) && strlen($password) < 6) {
                $_SESSION['error_message'] = "Le mot de passe doit contenir au moins 6 caractères.";
                header('Location: index.php?url=editUser&userId=' . $userId);  // Redirige vers la page de modification
                exit;
            }

            // Empêcher un admin de se retirer le statut admin si l'utilisateur modifié est l'administrateur connecté
            if ($userId == $_SESSION['userId'] && $user['isAdmin']) {
                $isAdmin = 1;
            }

            // Si aucune donnée n'a été modifiée
            if ($email == $user['email'] && $firstName == $user['firstName'] && $lastName == $user['lastName'] && empty($password) && $isAdmin == $user['isAdmin']) {
                $_SESSION['error_message'] = "Aucune modification détectée.";
                header('Location: index.php?url=admin');
                exit;
            }

            // Appelle la méthode updateUser et passe le mot de passe seulement s'il a été fourni
            if (User::updateUser($userId, $email, $firstName, $lastName, $isAdmin, $password ? $password : null)) {
                $_SESSION['success_message'] = "Informations de l'utilisateur mises à jour avec succès.";
                header('Location: index.php?url=admin');  // Redirige l'utilisateur vers la page d'administration
                exit;
            } else {
                $_SESSION['error_message'] = "Erreur lors de la mise à jour des informations.";  // Stocke un message d'erreur en session
                exit;
            }
        }

        require_once('views/editUser.php');  // Charge la vue d'édition de l'utilisateur
    }




    /**
     * Supprime un utilisateur spécifié par son identifiant.
     *
     * @return void
     */
    public function deleteUser(): void
    {
        User::redirectIfNotAdmin();
        $userId = $_GET['userId'];

        // Vérifier si l'utilisateur à supprimer est l'administrateur actuellement connecté
        if ($userId == $_SESSION['userId']) {
            $_SESSION['error_message'] = "Vous ne pouvez pas vous supprimer vous-même.";
            header('Location: index.php?url=admin');
            exit;
        }

        // Vérifier si l'utilisateur est propriétaire d'un bateau
        if (Boat::isUserBoatOwner($userId)) {
            $_SESSION['error_message'] = "Impossible de supprimer l'utilisateur: il est propriétaire d'un bateau.";
            header('Location: index.php?url=admin');
            exit;
        }

        // Vérifier si l'utilisateur est impliqué dans une compétition
        if (Registration::isUserInCompetition($userId)) {
            $_SESSION['error_message'] = "Impossible de supprimer l'utilisateur: il est impliqué dans une compétition.";
            header('Location: index.php?url=admin');
            exit;
        }


        if (User::deleteUser($userId)) {  // Appel à la méthode statique de suppression d'utilisateur
            $_SESSION['success_message'] = "Utilisateur supprimé avec succès";
            header('Location: index.php?url=admin');
            exit;
        } else {
            $_SESSION['error_message'] = "Erreur lors de la suppression de l'utilisateur";
            header('Location: index.php?url=admin');
            exit;
        }
    }
}
