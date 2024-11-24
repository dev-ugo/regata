<?php
require_once 'models/Database.php';

/**
 * Classe permettant de gérer les compétitions
 */
class Competition
{
    /**
     * Récupère toutes les compétitions passées et en cours.
     *
     * @return array Liste des compétitions.
     */
    public static function getCompetitions(): array
    {
        try {
            $stmt = Database::prepare(
                "SELECT * FROM Competition 
             WHERE endDate >= CURDATE() OR startDate <= CURDATE() 
             ORDER BY startDate DESC"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            ErrorLogger::logError($e, __FUNCTION__, __FILE__);
            return []; // Retourner un tableau vide en cas d'erreur
        }
    }


    /**
     * Récupère toutes les compétitions ouvertes pour lesquelles la phase d'inscription est en cours.
     *
     * @return array|false Un tableau associatif contenant les détails des compétitions ouvertes,
     *                     ou false en cas d'erreur.
     */
    public static function getOpenCompetitions(): array|false
    {
        try {
            $stmt = Database::prepare(
                "SELECT * FROM Competition WHERE status = 'Phase d’inscription'"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            ErrorLogger::logError($e, __FUNCTION__, __FILE__);
            return false; // Retourner false en cas d'erreur
        }
    }


    /**
     * Récupère les détails d'une compétition à partir de son ID.
     *
     * @param int $competitionId L'ID de la compétition à récupérer.
     * @return array|false Un tableau associatif contenant les détails de la compétition,
     *                     ou false si aucune compétition correspondante n'est trouvée.
     */
    public static function getById(int $competitionId): array|false
    {
        try {
            $stmt = Database::prepare("SELECT * FROM Competition WHERE competitionId = :competitionId");
            $stmt->bindParam(':competitionId', $competitionId);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            ErrorLogger::logError($e, __FUNCTION__, __FILE__);
            return false; // Retourner false en cas d'erreur
        }
    }


    /**
     * Récupère les compétitions publiées.
     *
     * Cette fonction récupère toutes les compétitions dont le statut n'est pas "Non publiée".
     *
     * @return array Un tableau associatif contenant les détails des compétitions publiées.
     */
    public static function getPublishedCompetitions()
    {
        try {
            $stmt = Database::prepare(
                "SELECT * FROM Competition WHERE status != 'Non publiée'"
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            ErrorLogger::logError($e, __FUNCTION__, __FILE__);
            return []; // Retourner un tableau vide en cas d'erreur
        }
    }
}