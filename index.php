<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Récupération de l'url demandée
$action = isset($_GET['url']) ? $_GET['url'] : 'home';

switch ($action) {
    case 'home':
        include('controllers/HomeController.php');
        $controller = new HomeController();
        $controller->index();
        break;

    case 'login':
    case 'register':
    case 'logout':
    case 'preferences':
    case 'admin':
    case 'editPassword':
    case 'editUser':
    case 'deleteUser':
        include('controllers/AuthController.php');
        $controller = new AuthController();
        $controller->{$action}(); // Appel dynamique de la méthode en fonction de l'action
        break;

    case 'addBoat':
    case 'myBoats':
    case 'deleteBoat':
    case 'addBoatAdmin':
    case 'deleteBoatAsAdmin':
    case 'editBoatAsAdmin':
    case 'editBoat':
        include('controllers/BoatController.php');
        $controller = new BoatController();
        $controller->{$action}(); // Appel dynamique de la méthode en fonction de l'action
        break;

    case 'registerBoatToCompetition':
    case 'registeredBoats':
    case 'validateBoat':
    case 'invalidateBoat':
    case 'myRegisteredBoats':
    case 'deleteRegistration':
    case 'editRegistration':
        include('controllers/RegistrationController.php');
        $controller = new RegistrationController();
        $controller->{$action}(); // Appel dynamique de la méthode en fonction de l'action
        break;

    case 'competitionsList':
    case 'competitionDetails':
        include('controllers/CompetitionController.php');
        $controller = new CompetitionController();
        $controller->{$action}(); // Appel dynamique de la méthode en fonction de l'action
        break;

    case 'error':
        include('errors/403.html');
        break;

    default:
        echo "404 Page Not Found";
        break;
}
