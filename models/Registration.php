<?php

require_once 'models/Database.php';


class Registration
{
    /**
     * Enregistre un bateau à une compétition avec son skipper et ses membres d'équipage.
     *
     * @param int $boatId L'ID du bateau à enregistrer.
     * @param int $competitionId L'ID de la compétition à laquelle enregistrer le bateau.
     * @param int $skipperId L'ID du skipper du bateau.
     * @param array $crewMembers Un tableau contenant les identifiants des membres d'équipage.
     * @return bool True si l'enregistrement est réussi, sinon False.
     */
    public static function registerBoatToCompetition(int $boatId, int $competitionId, int $skipperId, array $crewMembers): bool
    {
        // Vérifier si la compétition est toujours ouverte
        if (!self::isCompetitionOpen($competitionId)) {
            return false;
        }

        // Vérifier si le bateau est déjà inscrit à cette compétition
        if (self::isBoatAlreadyRegisteredInCompetition($boatId, $competitionId)) {
            return false;
        }

        // Vérifier si le skipper ou les membres d'équipage sont déjà inscrits dans une autre compétition qui se chevauche
        if (
            self::checkIfUserIsAlreadyRegistered($skipperId, $competitionId) ||
            self::areCrewMembersAlreadyRegistered($crewMembers, $competitionId)
        ) {
            return false;
        }

        // Commencer une transaction pour assurer l'intégrité des données
        Database::beginTransaction();

        try {
            // Insérer l'inscription du bateau dans la base de données
            $stmt = Database::prepare(
                "INSERT INTO Registration (boatId, competitionId, skipperId) VALUES (:boatId, :competitionId, :skipperId)"
            );
            $stmt->bindParam(':boatId', $boatId);
            $stmt->bindParam(':competitionId', $competitionId);
            $stmt->bindParam(':skipperId', $skipperId);
            $stmt->execute();

            $registrationId = Database::lastInsertId();

            // Insérer les membres d'équipage
            foreach ($crewMembers as $memberId) {
                $stmt = Database::prepare(
                    "INSERT INTO Crew (registrationId, userId) VALUES (:registrationId, :userId)"
                );
                $stmt->bindParam(':registrationId', $registrationId);
                $stmt->bindParam(':userId', $memberId);
                $stmt->execute();
            }

            // Valider la transaction
            Database::commit();
            return true;
        } catch (Exception $e) {
            // En cas d'erreur, annuler la transaction
            ErrorLogger::logError($e, __FUNCTION__, __FILE__);
            Database::rollback();
            return false;
        }
    }


    /**
     * Vérifie si un bateau est déjà inscrit dans une compétition.
     *
     * @param int $boatId L'identifiant du bateau.
     * @param int $competitionId L'identifiant de la compétition.
     * @return bool Retourne true si le bateau est déjà inscrit à la compétition, sinon false.
     */
    public static function isBoatAlreadyRegisteredInCompetition(int $boatId, int $competitionId): bool
    {
        try {
            $stmt = Database::prepare("SELECT COUNT(*) FROM Registration WHERE boatId = :boatId AND competitionId = :competitionId");
            $stmt->bindParam(':boatId', $boatId);
            $stmt->bindParam(':competitionId', $competitionId);
            $stmt->execute();
            $count = $stmt->fetchColumn();
            return $count > 0;
        } catch (PDOException $e) {
            ErrorLogger::logError($e, __FUNCTION__, __FILE__);
            return false;
        }
    }




    /**
     * Vérifie si une compétition est encore ouverte pour les inscriptions.
     *
     * @param int $competitionId L'ID de la compétition.
     * @return bool
     */
    private static function isCompetitionOpen(int $competitionId): bool
    {
        try {
            $stmt = Database::prepare(
                "SELECT COUNT(*) FROM Competition WHERE competitionId = :competitionId AND startDate > CURDATE()"
            );
            $stmt->bindParam(':competitionId', $competitionId);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            ErrorLogger::logError($e, __FUNCTION__, __FILE__);
            return false;
        }
    }

    /**
     * Vérifie si le bateau est déjà inscrit dans une autre compétition qui se chevauche.
     *
     * @param int $boatId L'ID du bateau.
     * @param int $competitionId L'ID de la compétition actuelle.
     * @return bool
     */
    public static function isBoatAlreadyRegistered(int $boatId, int $competitionId): bool
    {
        $stmt = Database::prepare(
            "SELECT COUNT(*) FROM Registration AS r
             JOIN Competition AS c ON r.competitionId = c.competitionId
             WHERE r.boatId = :boatId AND c.endDate >= CURDATE() AND r.competitionId <> :competitionId"
        );
        $stmt->bindParam(':boatId', $boatId);
        $stmt->bindParam(':competitionId', $competitionId);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Récupère tous les bateaux inscrits avec le détail de leur inscription.
     *
     * @return array Liste des bateaux inscrits avec détails de compétitions.
     */
    public static function getAllRegisteredBoats(): array
    {
        $stmt = Database::prepare(
            "SELECT Boat.boatId, Boat.boatName, Boat.sailId, Competition.title, Competition.startDate, 
                    Competition.endDate, Registration.validationDate
             FROM Registration
             JOIN Boat ON Registration.boatId = Boat.boatId
             JOIN Competition ON Registration.competitionId = Competition.competitionId
             ORDER BY Competition.startDate DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Valide l'inscription d'un bateau en mettant à jour la date de validation.
     *
     * @param int $boatId L'identifiant du bateau à valider.
     * @return bool Retourne true si la mise à jour a réussi, sinon false en cas d'erreur.
     */
    public static function validateRegistration(int $boatId): bool
    {
        try {
            $stmt = Database::prepare("UPDATE Registration SET validationDate = NOW() WHERE boatId = :boatId");
            $stmt->bindParam(':boatId', $boatId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            ErrorLogger::logError($e, __FUNCTION__, __FILE__);
            return false;
        }
    }


    /**
     * Invalide une inscription en renseignant la date de validation à NULL.
     *
     * @param int $boatId L'ID du bateau dont il faut invalider l'inscription.
     * @return bool Retourne true si la mise à jour a réussi, false sinon.
     */
    public static function invalidateRegistration(int $boatId): bool
    {
        try {
            $stmt = Database::prepare("UPDATE Registration SET validationDate = NULL WHERE boatId = :boatId");
            $stmt->bindParam(':boatId', $boatId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            ErrorLogger::logError($e, __FUNCTION__, __FILE__);
            return false;
        }
    }


    /**
     * Récupère toutes les inscriptions de compétition pour un utilisateur spécifique.
     * 
     * @param int $userId ID de l'utilisateur
     * @return array Liste des inscriptions avec détails de la compétition et du bateau
     */
    public static function getRegistrationsByUserId(int $userId): array
    {
        $stmt = Database::prepare(
            "SELECT Registration.*, Boat.boatName, Competition.title, Competition.startDate, Competition.endDate
             FROM Registration
             JOIN Boat ON Registration.boatId = Boat.boatId
             JOIN Competition ON Registration.competitionId = Competition.competitionId
             WHERE Boat.ownerId = :userId"
        );
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Vérifie si une inscription peut être supprimée.
     *
     * @param int $registrationId L'ID de l'inscription à vérifier.
     * @return bool True si l'inscription peut être supprimée, sinon False.
     */
    public static function canDeleteRegistration(int $registrationId): bool
    {
        $stmt = Database::prepare(
            "SELECT validationDate FROM Registration WHERE registrationId = :registrationId"
        );
        $stmt->bindParam(':registrationId', $registrationId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return empty($result['validationDate']); // Retourne true si la date de validation est nulle
    }

    /**
     * Supprime une inscription si elle n'est pas encore validée.
     *
     * @param int $registrationId L'ID de l'inscription à supprimer.
     * @return bool True si l'inscription est supprimée avec succès, sinon False.
     */
    public static function deleteRegistration(int $registrationId): bool
    {
        try {
            $stmt = Database::prepare(
                "DELETE FROM Registration WHERE registrationId = :registrationId AND validationDate IS NULL"
            );
            $stmt->bindParam(':registrationId', $registrationId);
            return $stmt->execute();
        } catch (PDOException $e) {
            ErrorLogger::logError($e, __FUNCTION__, __FILE__);
            return false;
        }
    }


    /**
     * Vérifie si un utilisateur est déjà enregistré comme skipper ou membre d'équipage dans la même compétition.
     * 
     * @param int $userId ID de l'utilisateur à vérifier.
     * @param int $competitionId ID de la compétition à vérifier.
     * @return bool Retourne true si l'utilisateur est déjà enregistré, false sinon.
     */
    public static function checkIfUserIsAlreadyRegistered(int $userId, int $competitionId): bool
    {
        // Préparer la requête SQL pour vérifier l'existence de l'utilisateur comme skipper ou membre d'équipage dans la même compétition
        $stmt = Database::prepare(
            "SELECT COUNT(*) FROM Registration 
             JOIN Crew ON Registration.registrationId = Crew.registrationId
             WHERE (Registration.skipperId = :userId OR Crew.userId = :userId)
             AND Registration.competitionId = :competitionId"
        );

        // Lier les paramètres à la requête préparée
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':competitionId', $competitionId, PDO::PARAM_INT);

        // Exécuter la requête
        $stmt->execute();

        // Récupérer le résultat
        $count = $stmt->fetchColumn();

        // Retourner vrai si un ou plusieurs enregistrements ont été trouvés
        return $count > 0;
    }

    /**
     * Vérifie si un ou plusieurs membres d'équipage sont déjà inscrits à une compétition donnée.
     *
     * @param array $crewMembers Un tableau contenant les identifiants des membres d'équipage.
     * @param int $competitionId L'identifiant de la compétition à vérifier.
     *
     * @return bool Retourne true si au moins un des membres d'équipage est déjà inscrit à la compétition, false sinon.
     */
    public static function areCrewMembersAlreadyRegistered(array $crewMembers, int $competitionId): bool
    {
        foreach ($crewMembers as $memberId) {
            if (self::checkIfUserIsAlreadyRegistered($memberId, $competitionId)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Récupère une inscription par son ID, incluant les détails du skipper et des membres d'équipage.
     * 
     * @param int $registrationId L'ID de l'inscription à récupérer.
     * @return mixed Les détails de l'inscription, y compris le skipper et les membres d'équipage.
     */
    public static function getRegistrationById(int $registrationId): mixed
    {
        $stmt = Database::prepare(
            "SELECT 
                r.*, 
                b.boatName, 
                c.title AS competitionTitle, 
                s.firstName AS skipperFirstName, 
                s.lastName AS skipperLastName,
                GROUP_CONCAT(u.firstName, ' ', u.lastName ORDER BY u.lastName SEPARATOR ', ') AS crewNames
             FROM Registration r
             INNER JOIN Boat b ON r.boatId = b.boatId
             INNER JOIN Competition c ON r.competitionId = c.competitionId
             LEFT JOIN User s ON r.skipperId = s.userId
             LEFT JOIN Crew cr ON r.registrationId = cr.registrationId
             LEFT JOIN User u ON cr.userId = u.userId
             WHERE r.registrationId = :registrationId
             GROUP BY r.registrationId"
        );

        $stmt->bindParam(':registrationId', $registrationId, PDO::PARAM_INT);

        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Retourner les résultats ou un tableau vide si aucun enregistrement n'est trouvé
        return $result ? $result : [];
    }

    /**
     * Met à jour le skipper pour une inscription donnée.
     *
     * @param int $registrationId L'identifiant de l'inscription à mettre à jour.
     * @param int $skipperId L'identifiant du nouveau skipper.
     * @return void
     */
    public static function updateSkipper(int $registrationId, int $skipperId): void
    {
        try {
            $stmt = Database::prepare("UPDATE Registration SET skipperId = :skipperId WHERE registrationId = :registrationId");
            $stmt->bindParam(':registrationId', $registrationId);
            $stmt->bindParam(':skipperId', $skipperId);
            $stmt->execute();
        } catch (PDOException $e) {
            ErrorLogger::logError($e, __FUNCTION__, __FILE__);
        }
    }


    /**
     * Met à jour une inscription en modifiant le skipper et en remplaçant les membres d'équipage.
     *
     * @param int $registrationId L'identifiant de l'inscription à mettre à jour.
     * @param int $newSkipperId L'identifiant du nouveau skipper.
     * @param array $newCrewMembers Les identifiants des nouveaux membres d'équipage.
     * @return bool Retourne vrai si la mise à jour est réussie, faux en cas d'échec.
     */
    public static function updateRegistration(int $registrationId, int $newSkipperId, array $newCrewMembers): bool
    {
        Database::beginTransaction();
        try {
            self::updateSkipper($registrationId, $newSkipperId);
            Crew::replaceCrew($registrationId, $newCrewMembers);
            Database::commit();
            return true;
        } catch (Exception $e) {
            Database::rollback();
            ErrorLogger::logError($e, __FUNCTION__, __FILE__);
            return false;
        }
    }

    /**
     * Récupère les bateaux inscrits dans une compétition donnée.
     *
     * @param int $competitionId L'identifiant de la compétition.
     * @return array Retourne un tableau associatif des bateaux inscrits avec leurs détails.
     */
    public static function getBoatsInCompetition(int $competitionId): array
    {
        $stmt = Database::prepare("
            SELECT 
            Registration.registrationId,     
                Boat.boatId,
                Boat.sailId, 
                Boat.boatName, 
                Class.className, 
                Boat.handicap,
                User.firstName AS skipperFirstName, 
                User.lastName AS skipperLastName, 
                Registration.validationDate AS status,
                Boat.ownerId,
                Registration.skipperId,
                GROUP_CONCAT(CONCAT(CrewUser.firstName, ' ', CrewUser.lastName) SEPARATOR ', ') AS crewMembers
            FROM 
                Registration
            JOIN 
                Boat ON Boat.boatId = Registration.boatId
            JOIN 
                User ON User.userId = Registration.skipperId
            JOIN 
                Class ON Class.classId = Boat.classId
            JOIN 
                Competition ON Competition.competitionId = Registration.competitionId
            LEFT JOIN 
                Crew ON Crew.registrationId = Registration.registrationId
            LEFT JOIN 
                User AS CrewUser ON CrewUser.userId = Crew.userId
            WHERE 
                Registration.competitionId = :competitionId
            GROUP BY 
                Registration.registrationId
        ");
        $stmt->bindParam(':competitionId', $competitionId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Vérifie si un utilisateur est inscrit dans des compétitions en tant que skipper ou membre d'équipage.
     *
     * @param int $userId L'identifiant de l'utilisateur à vérifier.
     * @return bool Retourne true si l'utilisateur est impliqué dans au moins une compétition, sinon false.
     */
    public static function isUserInCompetition(int $userId): bool
    {
        $sql = "SELECT COUNT(*) FROM Registration 
                WHERE skipperId = :userId 
                OR registrationId IN (SELECT registrationId FROM Crew WHERE userId = :userId)";
        $stmt = Database::prepare($sql);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        return $count > 0;
    }
}
