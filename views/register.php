<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regata</title>
    <link href="https://api.fontshare.com/v2/css?f[]=bespoke-slab@400&f[]=poppins@600&display=swap" rel="stylesheet">
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
                        <h2 class="card-title text-center mb-4">Inscription</h2>

                        <form id="register-form" action="#" method="post">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($formData['email']) ?>" required>
                                <div id="email-feedback" class="invalid-feedback">
                                    Veuillez saisir une adresse e-mail valide.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="firstName" class="form-label">Prénom</label>
                                <input type="text" class="form-control" id="firstName" name="firstName" value="<?= htmlspecialchars($formData['firstName']) ?>" required>

                            </div>

                            <div class="mb-3">
                                <label for="lastName" class="form-label">Nom</label>
                                <input type="text" class="form-control" id="lastName" name="lastName" value="<?= htmlspecialchars($formData['lastName']) ?>" required>

                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Mot de passe</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div id="password-feedback" class="invalid-feedback">
                                    Le mot de passe doit contenir au moins 6 caractères.
                                </div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" id="submit-register" class="btn btn-primary" name="submitRegister" value="submitRegister">S'inscrire</button>
                            </div>
                        </form>
                        <div class="text-center mt-3">
                            Vous avez déjà un compte ? <a href="index.php?url=login">Connectez-vous</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="js/validation.js"></script>
</body>

</html>