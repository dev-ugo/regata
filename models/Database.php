<?php

/**
 * Fichier: Database.php
 * Auteur: dominique@aigroz.com
 * Description: Classe Helper encapsulant l'objet PDO pour la gestion de la base de données.
 */

/**
 * @remark Assurez-vous d'inclure le bon chemin d'accès à votre fichier contenant les constantes de configuration de la base de données.
 */
require_once './config/database.php';

/**
 * @brief Helper class encapsulant l'objet PDO pour la gestion de la base de données.
 */
class Database
{
    /**
     * @var PDO L'instance statique de l'objet PDO créée dans getInstance().
     */
    private static $objInstance;

    /**
     * @brief Constructeur de classe - Crée une nouvelle connexion à la base de données si elle n'existe pas déjà.
     *        Défini comme privé pour éviter la création d'une nouvelle instance via ' = new EDatabase();'.
     */
    private function __construct()
    {
    }

    /**
     * @brief Comme le constructeur, nous rendons __clone privé pour que personne ne puisse cloner l'instance.
     */
    private function __clone()
    {
    }

    /**
     * @brief Retourne une instance de la base de données ou crée une connexion initiale.
     * @return PDO
     */
    private static function getInstance()
    {
        if (!self::$objInstance) {
            try {
                // Construction du DSN (Data Source Name)
                $dsn = EDB_DBTYPE . ':host=' . EDB_HOST . ';port=' . EDB_PORT . ';dbname=' . EDB_DBNAME;
                // Création de l'objet PDO
                self::$objInstance = new PDO($dsn, EDB_USER, EDB_PASS, array('charset' => 'utf8'));
                // Configuration de l'objet PDO pour lever les exceptions en cas d'erreur
                self::$objInstance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                // En cas d'erreur, affichage du message d'erreur
                echo "EDatabase Error: " . $e;
            }
        }
        // Retourne l'instance de l'objet PDO
        return self::$objInstance;
    }

    /**
     * @brief Passe tous les appels statiques à cette classe sur l'instance singleton de PDO.
     * @param string $chrMethod Le nom de la méthode à appeler.
     * @param array $arrArguments Les paramètres de la méthode.
     * @return mixed La valeur retournée par la méthode.
     */
    final public static function __callStatic($chrMethod, $arrArguments)
    {
        // Récupération de l'instance de l'objet PDO
        $objInstance = self::getInstance();
        // Appel de la méthode spécifiée avec les arguments fournis
        return call_user_func_array(array($objInstance, $chrMethod), $arrArguments);
    }
}
