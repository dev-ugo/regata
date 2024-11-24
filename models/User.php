<?php

require_once 'models/Database.php';

/**
 * Classe User
 * Gère les opérations liées aux utilisateurs dans la base de données
 */
class User
{
    /**
     * Enregistre un nouvel utilisateur dans la base de données.
     *
     * @param string $email L'adresse email de l'utilisateur.
     * @param string $password Le mot de passe de l'utilisateur.
     * @return int L'ID de l'utilisateur inséré.
     */
    public static function register(string $email, string $firstname, string $lastname,  string $password): int
    {
        // Vérifie le nombre d'utilisateurs dans la base de données
        $countStmt = Database::prepare('SELECT COUNT(*) FROM User');
        $countStmt->execute();
        $userCount = $countStmt->fetchColumn();

        // Détermine si l'utilisateur doit être un administrateur
        $isAdmin = ($userCount == 0) ? 1 : 0;

        // Préparation de la requête d'insertion avec la colonne isAdmin
        $stmt = Database::prepare('INSERT INTO User (email, firstname, lastname, pwd, isAdmin) VALUES (:email, :firstname, :lastname, :password, :isAdmin)');
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':firstname', $firstname);
        $stmt->bindParam(':lastname', $lastname);
        $stmt->bindParam(':password', password_hash($password, PASSWORD_DEFAULT)); // Hash du mot de passe
        $stmt->bindParam(':isAdmin', $isAdmin);

        $stmt->execute();

        // Retourne l'ID de l'utilisateur inséré
        return Database::lastInsertId();
    }

    /**
     * Vérifie si un email existe déjà dans la base de données.
     *
     * @param string $email L'adresse email à vérifier.
     * @return bool True si l'email existe déjà, false sinon.
     */
    public static function emailExists(string $email): bool
    {
        $stmt = Database::prepare('SELECT COUNT(*) FROM User WHERE email = :email');
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $count = $stmt->fetchColumn();

        return $count > 0;
    }

    /**
     * Connecte l'utilisateur.
     *
     * @return bool
     */
    public static function login(string $email, string $password): bool
    {
        // Vérifier si l'utilisateur existe
        if (self::emailExists($email)) {
            $stmt = Database::prepare('SELECT userId, pwd FROM User WHERE email = :email');
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['pwd'])) {
                // Le mot de passe est correct, stocker les données de l'utilisateur dans la session
                $_SESSION['emailUser'] = $email;
                $_SESSION['isLoggedIn'] = true;
                $_SESSION['userId'] = $user['userId'];

                return true;
            }
        }

        // L'utilisateur n'existe pas ou le mot de passe est incorrect
        return false;
    }

    /**
     * Récupère les détails de l'utilisateur.
     *
     * @return mixed
     */
    public static function getUserDetails(): mixed
    {
        $stmt = Database::prepare('SELECT * FROM User WHERE email = :email');
        $stmt->bindParam(':email', $_SESSION['emailUser']);
        $stmt->execute();

        $userDetails = $stmt->fetch();

        return $userDetails;
    }

    /**
     * Récupère les détails de l'utilisateur.
     *
     * @return mixed
     */
    public static function getUserById(int $id): mixed
    {
        $stmt = Database::prepare('SELECT * FROM User WHERE userId = :userId');
        $stmt->bindParam(':userId', $id);
        $stmt->execute();

        $userDetails = $stmt->fetch();

        return $userDetails;
    }

    /**
     * Vérifie si l'utilisateur actuel est un administrateur.
     *
     * @return bool True si l'utilisateur est un administrateur, false sinon.
     */
    public static function isAdmin(): bool
    {
        if (isset($_SESSION['userId'])) {
            $userDetails = self::getUserDetails();
            return $userDetails['isAdmin'] === 1;
        } else {
            return false;
        }
    }

    /**
     * Déconnecte l'utilisateur en détruisant la session.
     *
     * Cette fonction détruit la session utilisateur.
     *
     * @return void
     */
    public static function logout(): void
    {
        $_SESSION = array();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();
    }

    /**
     * Récupère tous les utilisateurs
     *
     * @return array Un tableau contenant les détails de tous les utilisateurs.
     */
    public static function getAllUsers(): array
    {
        $stmt = Database::prepare('SELECT * FROM User');
        $stmt->execute();

        $users = $stmt->fetchAll();

        return $users;
    }


    /**
     * Modifie le mot de passe de l'utilisateur.
     *
     * @param string $oldPassword Le mot de passe actuel de l'utilisateur.
     * @param string $newPassword Le nouveau mot de passe de l'utilisateur.
     * @return bool True si le mot de passe est modifié avec succès, sinon False.
     */
    public static function editPassword(string $oldPassword, string $newPassword): bool
    {

        $email = $_SESSION['emailUser'];
        // Vérifier si l'ancien mot de passe est correct
        $stmt = Database::prepare('SELECT pwd FROM User WHERE email = :email');
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $currentPasswordHash = $stmt->fetchColumn();

        if (!password_verify($oldPassword, $currentPasswordHash)) {
            // L'ancien mot de passe est incorrect
            return false;
        }

        // Hasher le nouveau mot de passe
        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

        // Mettre à jour le mot de passe dans la base de données
        $updateStmt = Database::prepare('UPDATE User SET pwd = :newPasswordHash WHERE email = :email');
        $updateStmt->bindParam(':newPasswordHash', $newPasswordHash);
        $updateStmt->bindParam(':email', $email);

        return $updateStmt->execute();
    }

    /**
     * Met à jour les informations de l'utilisateur, y compris le mot de passe si spécifié.
     *
     * @param int $userId Identifiant de l'utilisateur à mettre à jour.
     * @param string $email Nouvel email de l'utilisateur.
     * @param string $firstName Nouveau prénom de l'utilisateur.
     * @param string $lastName Nouveau nom de famille de l'utilisateur.
     * @param int $isAdmin Statut d'administrateur (1 pour admin, 0 sinon).
     * @param string|null $password Nouveau mot de passe, si spécifié.
     * @return bool Retourne true si la mise à jour a réussi, false sinon.
     */
    public static function updateUser(int $userId, string $email, string $firstName, string $lastName, int $isAdmin, ?string $password = null): bool
    {
        try {
            // Détermine si le mot de passe doit être mis à jour
            $passwordSql = $password ? ", pwd = :pwd" : "";
            $sql = "UPDATE User SET email = :email, firstName = :firstName, lastName = :lastName, isAdmin = :isAdmin" . $passwordSql . " WHERE userId = :userId";

            $stmt = Database::prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':firstName', $firstName);
            $stmt->bindParam(':lastName', $lastName);
            $stmt->bindParam(':isAdmin', $isAdmin);
            $stmt->bindParam(':userId', $userId);

            // Lie le mot de passe seulement si un nouveau est fourni
            if ($password) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt->bindParam(':pwd', $hashedPassword);
            }

            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            ErrorLogger::logError($e, __FUNCTION__, __FILE__);
            return false;
        }
    }


    /**
     * Supprime un utilisateur de la base de données.
     *
     * @param int $userId L'ID de l'utilisateur à supprimer.
     * @return bool True si l'utilisateur est supprimé avec succès, sinon False.
     */
    public static function deleteUser(int $userId): bool
    {
        try {
            $sql = "DELETE FROM User WHERE userId = :userId";
            $stmt = Database::prepare($sql);
            $stmt->bindParam(':userId', $userId);
            $stmt->execute();

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            ErrorLogger::logError($e, __FUNCTION__, __FILE__);
            return false;
        }
    }

    /**
     * Met à jour les préférences d'un utilisateur.
     *
     * @param string $email L'email de l'utilisateur.
     * @param string $firstName Le nouveau prénom de l'utilisateur.
     * @param string $lastName Le nouveau nom de l'utilisateur.
     * @return bool True si les préférences sont mises à jour avec succès, sinon False.
     */
    public static function updateUserPreferences(string $email, string $firstName, string $lastName): bool
    {
        try {
            $sql = "UPDATE User SET firstName = :firstName, lastName = :lastName WHERE email = :email";
            $stmt = Database::prepare($sql);
            $stmt->bindParam(':firstName', $firstName);
            $stmt->bindParam(':lastName', $lastName);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            ErrorLogger::logError($e, __FUNCTION__, __FILE__);
            return false;
        }
    }

    /**
     * Redirige l'utilisateur vers la page d'accueil si celui-ci n'est pas un administrateur.
     * 
     * @return void
     */
    public static function redirectIfNotAdmin(): void
    {
        if (!User::isAdmin()) {
            header('Location: index.php?url=home');
            exit;
        }
    }

    /**
     * Redirige l'utilisateur vers la page de connexion si celui-ci n'est pas connecté.
     *
     * @return void Aucune valeur de retour, mais la méthode effectue une redirection si nécessaire.
     */
    public static function redirectIfNotConnected(): void
    {
        if (!$_SESSION['isLoggedIn']) {
            header('Location: index.php?url=login');
            exit;
        }
    }
}
