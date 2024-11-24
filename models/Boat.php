<?php

require_once 'models/Database.php';

class Boat
{
    /**
     * Récupère la liste de tous les bateaux avec leur nom, numéro de voile, classe, handicap,
     * ainsi que le nom et le prénom du propriétaire.
     *
     * @return array Un tableau contenant les informations de tous les bateaux.
     */
    public static function getBoats(): array
    {
        try {
            // Préparation de la requête SQL pour récupérer les informations des bateaux et des propriétaires
            $stmt = Database::prepare(
                "SELECT 
                Boat.boatId, 
                Boat.officialId, 
                Boat.boatName, 
                Boat.sailId, 
                Boat.handicap, 
                User.firstName, 
                User.lastName, 
                Class.className 
            FROM 
                Boat
            INNER JOIN 
                User ON Boat.ownerId = User.userId
            INNER JOIN 
                Class ON Boat.classId = Class.classId"
            );

            // Exécution de la requête
            $stmt->execute();

            // Récupération des résultats
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            ErrorLogger::logError($e, __FUNCTION__, __FILE__);
            return []; // Retourner un tableau vide en cas d'erreur
        }
    }


    /**
     * Récupère toutes les classes de bateaux de la base de données.
     *
     * @return array Un tableau contenant les classes de bateaux.
     */
    public static function getAllClasses(): array
    {
        try {
            $stmt = Database::prepare("SELECT classId, className FROM Class");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            ErrorLogger::logError($e, __FUNCTION__, __FILE__);
            return []; // Retourner un tableau vide en cas d'erreur
        }
    }


    /**
     * Ajoute un nouveau bateau à la base de données.
     *
     * @param string $boatName Le nom du bateau.
     * @param string $officialId L'identifiant officiel du bateau.
     * @param string $sailId L'identifiant de voile du bateau.
     * @param float $handicap Le handicap du bateau.
     * @param int $ownerId L'identifiant du propriétaire du bateau.
     * @param int $classId L'identifiant de la classe du bateau.
     * @return bool true si le bateau a été ajouté avec succès, sinon false.
     */
    public static function addBoat(string $boatName, string $officialId, string $sailId, float $handicap, int $ownerId, int $classId): bool
    {
        try {
            // Vérifier si le bateau avec le même officialId existe déjà
            $existingBoat = self::getBoatByOfficialId($officialId);
            if ($existingBoat !== null) {
                return false; // Un bateau avec le même officialId existe déjà
            }

            $stmt = Database::prepare(
                "INSERT INTO Boat (boatName, officialId, sailId, handicap, ownerId, classId) VALUES (:boatName, :officialId, :sailId, :handicap, :ownerId, :classId)"
            );
            $stmt->bindParam(':boatName', $boatName);
            $stmt->bindParam(':officialId', $officialId);
            $stmt->bindParam(':sailId', $sailId);
            $stmt->bindParam(':handicap', $handicap);
            $stmt->bindParam(':ownerId', $ownerId);
            $stmt->bindParam(':classId', $classId);
            return $stmt->execute();
        } catch (PDOException $e) {
            ErrorLogger::logError($e, __FUNCTION__, __FILE__);
            return false;
        }
    }


    /**
     * Récupère un bateau à partir de son identifiant officiel.
     *
     * @param string $officialId L'identifiant officiel du bateau.
     * @return array|null Un tableau contenant les informations du bateau s'il est trouvé, sinon null.
     */
    public static function getBoatByOfficialId(string $officialId): ?array
    {
        try {
            $stmt = Database::prepare("SELECT * FROM Boat WHERE officialId = :officialId");
            $stmt->bindParam(':officialId', $officialId);
            $stmt->execute();

            $boat = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($boat === false) {
                return null; // Aucun bateau trouvé avec l'officialId spécifié
            }
            return $boat;
        } catch (PDOException $e) {
            ErrorLogger::logError($e, __FUNCTION__, __FILE__);
            return null; // Retourner null en cas d'erreur
        }
    }


    /**
     * Récupère tous les bateaux appartenant à un utilisateur spécifique.
     *
     * @param int $ownerId ID de l'utilisateur.
     * @return array Liste des bateaux de l'utilisateur.
     */
    public static function getBoatsByOwner(int $ownerId): array
    {
        try {
            $stmt = Database::prepare("
            SELECT Boat.boatId, Boat.boatName, Boat.officialId, Boat.sailId, Boat.handicap, Boat.classId, Class.className 
            FROM Boat 
            INNER JOIN Class ON Boat.classId = Class.classId
            WHERE ownerId = :ownerId
        ");
            $stmt->bindParam(':ownerId', $ownerId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            ErrorLogger::logError($e, __FUNCTION__, __FILE__);
            return []; // Retourner un tableau vide en cas d'erreur
        }
    }


    /**
     * Vérifie si un bateau peut être supprimé et le supprime si possible.
     *
     * @param int $boatId ID du bateau à supprimer.
     * @param int $ownerId ID de l'utilisateur propriétaire du bateau.
     * @return bool Retourne true si la suppression a réussi, false sinon.
     */
    public static function deleteBoat(int $boatId, int $ownerId): bool
    {
        // Commencer une transaction pour assurer la cohérence des données
        Database::beginTransaction();

        try {
            // Vérifier si le bateau est engagé dans des compétitions validées
            $stmt = Database::prepare(
                "SELECT COUNT(*) FROM Registration
             WHERE boatId = :boatId AND validationDate IS NOT NULL"
            );
            $stmt->bindParam(':boatId', $boatId);
            $stmt->execute();
            if ($stmt->fetchColumn() > 0) {
                Database::rollback(); // Annuler la transaction
                return false;
            }

            // Supprimer les inscriptions à venir non validées
            $stmt = Database::prepare(
                "DELETE FROM Registration WHERE boatId = :boatId AND validationDate IS NULL"
            );
            $stmt->bindParam(':boatId', $boatId);
            $stmt->execute();

            // Supprimer le bateau
            $stmt = Database::prepare(
                "DELETE FROM Boat WHERE boatId = :boatId AND ownerId = :ownerId"
            );
            $stmt->bindParam(':boatId', $boatId);
            $stmt->bindParam(':ownerId', $ownerId);
            $stmt->execute();

            Database::commit(); // Valider la transaction
            return true;
        } catch (PDOException $e) {
            ErrorLogger::logError($e, __FUNCTION__, __FILE__);
            Database::rollback(); // Annuler la transaction en cas d'erreur
            return false;
        }
    }

    /**
     * Supprime un bateau de la base de données en tant qu'administrateur.
     *
     * Cette fonction permet à un administrateur de supprimer un bateau de la base de données.
     *
     * @param int $boatId L'identifiant du bateau à supprimer.
     * 
     * @return bool Retourne true si la suppression du bateau a réussi, sinon false.
     */
    public static function deleteBoatAsAdmin(int $boatId): bool
    {
        // Commencer une transaction pour assurer la cohérence des données
        Database::beginTransaction();

        try {
            // Supprimer toutes les inscriptions du bateau dans les compétitions à venir non validées
            $stmt = Database::prepare(
                "DELETE FROM Registration WHERE boatId = :boatId AND validationDate IS NULL"
            );
            $stmt->bindParam(':boatId', $boatId);
            $stmt->execute();

            // Supprimer le bateau
            $stmt = Database::prepare(
                "DELETE FROM Boat WHERE boatId = :boatId"
            );
            $stmt->bindParam(':boatId', $boatId);
            $stmt->execute();

            Database::commit(); // Valider la transaction
            return true;
        } catch (PDOException $e) {
            ErrorLogger::logError($e, __FUNCTION__, __FILE__);
            Database::rollback(); // Annuler la transaction en cas d'erreur
            return false;
        }
    }


    /**
     * Récupère les détails d'un bateau à partir de son identifiant.
     *
     * @param int $boatId L'identifiant du bateau à récupérer.
     * 
     * @return array|null Retourne un tableau associatif contenant les détails du bateau s'il existe, sinon null.
     */
    public static function getBoatById(int $boatId): ?array
    {
        try {
            $stmt = Database::prepare("SELECT * FROM Boat WHERE boatId = :boatId");
            $stmt->bindParam(':boatId', $boatId, PDO::PARAM_INT);
            $stmt->execute();

            $boat = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($boat === false) {
                return null; // Aucun bateau trouvé avec l'ID spécifié
            }
            return $boat;
        } catch (PDOException $e) {
            ErrorLogger::logError($e, __FUNCTION__, __FILE__);
            return null; // Retourner null en cas d'erreur
        }
    }


    /**
     * Modifie les détails d'un bateau en tant qu'administrateur.
     *
     * Cette fonction permet à l'administrateur de modifier les détails d'un bateau
     * spécifié par son identifiant.
     *
     * @param int    $boatId   L'identifiant du bateau à modifier.
     * @param string $boatName Le nouveau nom du bateau.
     * @param string $sailId   Le nouveau numéro de voile du bateau.
     * @param float  $handicap Le nouveau handicap du bateau.
     * 
     * @return bool Retourne true si la modification a réussi, sinon false.
     */
    public static function editBoatAsAdmin(int $boatId, string $boatName, string $sailId, float $handicap): bool
    {
        try {
            if (!User::isAdmin()) {
                return false; // Seul un admin peut exécuter cette action
            }

            $stmt = Database::prepare(
                "UPDATE Boat SET boatName = :boatName, sailId = :sailId, handicap = :handicap WHERE boatId = :boatId"
            );
            $stmt->bindParam(':boatId', $boatId);
            $stmt->bindParam(':boatName', $boatName);
            $stmt->bindParam(':sailId', $sailId);
            $stmt->bindParam(':handicap', $handicap);
            return $stmt->execute();
        } catch (PDOException $e) {
            ErrorLogger::logError($e, __FUNCTION__, __FILE__);
            return false; // Retourner false en cas d'erreur
        }
    }


    /**
     * Met à jour les informations d'un bateau dans la base de données.
     *
     * @param int $boatId L'identifiant du bateau à mettre à jour.
     * @param string $boatName Le nouveau nom du bateau.
     * @param string $officialId Numéro d'immatriculation du bateau.
     * @param string $sailId Numéro de voile du bateau.
     * @param float $handicap Le handicap du bateau.
     * @param int $classId L'identifiant de la classe du bateau.
     * @return bool True si la mise à jour est réussie, sinon false.
     */
    public static function updateBoat(int $boatId, string $boatName, string $officialId, string $sailId, float $handicap, int $classId): bool
    {
        try {
            $stmt = Database::prepare("UPDATE Boat SET boatName = :boatName, officialId = :officialId, sailId = :sailId, handicap = :handicap, classId = :classId WHERE boatId = :boatId");
            $stmt->bindParam(':boatId', $boatId, PDO::PARAM_INT);
            $stmt->bindParam(':boatName', $boatName, PDO::PARAM_STR);
            $stmt->bindParam(':officialId', $officialId, PDO::PARAM_STR);
            $stmt->bindParam(':sailId', $sailId, PDO::PARAM_STR);
            $stmt->bindParam(':handicap', $handicap);
            $stmt->bindParam(':classId', $classId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            ErrorLogger::logError($e, __FUNCTION__, __FILE__);
            return false;
        }
    }


    /**
     * Vérifie si un numéro d'immatriculation est déjà utilisé dans la base de données.
     *
     * @param string $officialId Le numéro d'immatriculation à vérifier.
     * @return bool Retourne true si l'officialId existe déjà, false sinon.
     */
    public static function officialIdExists(string $officialId): bool
    {
        try {
            $stmt = Database::prepare("SELECT COUNT(*) FROM Boat WHERE officialId = :officialId");

            $stmt->bindParam(':officialId', $officialId, PDO::PARAM_STR);

            $stmt->execute();

            $count = $stmt->fetchColumn();

            // Retourner vrai si un enregistrement a été trouvé
            return $count > 0;
        } catch (PDOException $e) {
            ErrorLogger::logError($e, __FUNCTION__, __FILE__);
            return false;
        }
    }

    /**
     * Vérifie si un utilisateur possède des bateaux
     *
     * @param int $userId
     * @return boolean
     */
    public static function isUserBoatOwner(int $userId) : bool
    {
        // Préparez une requête SQL pour vérifier si l'utilisateur possède des bateaux
        $sql = "SELECT COUNT(*) FROM Boat WHERE ownerId = :ownerId";
        $stmt = Database::prepare($sql);
        $stmt->bindParam(':ownerId', $userId);
        $stmt->execute();
        // Récupérer le résultat de la requête
        $count = $stmt->fetchColumn();

        // Retourner vrai si l'utilisateur est propriétaire d'au moins un bateau
        return $count > 0;
    }
}
