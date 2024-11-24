<?php

require_once 'models/Database.php';


/**
 * Classe pour gérer les erreurs
 */
class ErrorLogger
{
    /**
     * Fonction générique pour gérer les erreurs et enregistrer les logs
     *
     * @param Exception $e L'exception à gérer
     * @param string $functionName Le nom de la fonction où l'erreur s'est produite
     * @param string $fileName Le nom du fichier où l'erreur s'est produite
     */
    public static function logError(Exception $e, string $functionName, string $fileName)
    {
        // Récupérer l'heure actuelle au format 'Y-m-d H:i:s'
        $currentTime = date("Y-m-d H:i:s");

        // Construire le message d'erreur avec l'heure, le nom du fichier, le nom de la fonction et un message descriptif
        $errorMessage = "[$currentTime] Error in $fileName - $functionName(): " . $e->getMessage();

        // Enregistrer le message d'erreur dans le fichier de log
        error_log($errorMessage . PHP_EOL, 3, PATH_ERROR);
    }
}