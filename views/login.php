<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site TPI</title>
    <link href="https://api.fontshare.com/v2/css?f[]=bespoke-slab@400&f[]=poppins@600&display=swap" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link rel="stylesheet" href="css/style.css">
    <!-- Intégration de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body class="vh-100 d-flex align-items-center justify-content-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <?php if (isset($_SESSION['error_message'])) : ?>
                    <div class="alert alert-danger mx-auto w-75" role="alert">
                        <?= $_SESSION['error_message'] ?>
                    </div>
                    <?php unset($_SESSION['error_message']); ?>
                <?php endif; ?>
                <div class="card mx-auto w-75">
                    <div class="card-body">
                        <div class="text-start mb-3">
                            <a href="index.php" class="btn btn-sm btn-outline-secondary">Retour à l'accueil</a>
                        </div>
                        <h2 class="card-title text-center mb-4">Connexion</h2>
                        <form id="login-form" action="#" method="post">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>

                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Mot de passe</label>
                                <input type="password" class="form-control" id="password" name="password" required>

                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary" name="submitLogin" value="submitLogin">Connexion</button>
                            </div>
                        </form>
                        <div class="text-center mt-3">
                            Pas de compte? <a href="index.php?url=register">Créer un compte</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>