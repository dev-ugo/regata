<?php

/**
 * Classe Crew
 * Gère les opérations liées aux équipages dans la base de données
 */
class Crew
{
    /**
     * Supprime les membres d'équipage associés à une inscription donnée.
     *
     * @param int $registrationId L'identifiant de l'inscription.
     * @return void
     */
    public static function deleteByRegistrationId(int $registrationId): void
    {
        try {
            $stmt = Database::prepare("DELETE FROM Crew WHERE registrationId = :registrationId");
            $stmt->bindParam(':registrationId', $registrationId);
            $stmt->execute();
        } catch (PDOException $e) {
            ErrorLogger::logError($e, __FUNCTION__, __FILE__);
        }
    }



    /**
     * Ajoute un membre d'équipage à une inscription donnée.
     *
     * @param int $registrationId L'identifiant de l'inscription.
     * @param int $userId L'identifiant de l'utilisateur à ajouter comme membre d'équipage.
     * @return void
     */
    public static function addMember(int $registrationId, int $userId): void
    {
        try {
            $stmt = Database::prepare("INSERT INTO Crew (registrationId, userId) VALUES (:registrationId, :userId)");
            $stmt->bindParam(':registrationId', $registrationId);
            $stmt->bindParam(':userId', $userId);
            $stmt->execute();
        } catch (PDOException $e) {
            ErrorLogger::logError($e, __FUNCTION__, __FILE__);
        }
    }


    /**
     * Récupère les identifiants des utilisateurs membres d'équipage inscrits pour une certaine inscription.
     *
     * @param int $registrationId L'ID de l'inscription pour laquelle récupérer les membres d'équipage.
     * @return array|false Un tableau contenant les identifiants des utilisateurs membres d'équipage inscrits,
     *                     ou false en cas d'échec ou si aucun membre n'est trouvé.
     */
    public static function getByRegistrationId(int $registrationId): array|false
    {
        try {
            $stmt = Database::prepare(
                "SELECT userId FROM Crew WHERE registrationId = :registrationId"
            );
            $stmt->bindParam(':registrationId', $registrationId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            ErrorLogger::logError($e, __FUNCTION__, __FILE__);
            return false; // Retourner false en cas d'erreur
        }
    }



    /**
     * Remplace les membres d'équipage inscrits pour une certaine inscription par de nouveaux membres.
     *
     * @param int $registrationId L'ID de l'inscription pour laquelle remplacer les membres d'équipage.
     * @param array $newCrewMembers Un tableau contenant les identifiants des nouveaux membres d'équipage.
     * @return void
     */
    public static function replaceCrew($registrationId, $newCrewMembers)
    {
        try {
            self::deleteByRegistrationId($registrationId); // Supprime les membres d'équipage existants
            foreach ($newCrewMembers as $memberId) {
                self::addMember($registrationId, $memberId); // Ajoute les nouveaux membres d'équipage
            }
        } catch (PDOException $e) {
            ErrorLogger::logError($e, __FUNCTION__, __FILE__);
        }
    }
}
