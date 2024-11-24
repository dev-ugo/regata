<?php require_once 'header.php'; ?>

<div class="container mt-5 min-vh-100">
    <div class="card">
        <?php if (isset($_SESSION['success_message'])) : ?>
            <div class="alert alert-success" role="alert">
                <?= $_SESSION['success_message']; ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error_message'])) : ?>
            <div class="alert alert-danger" role="alert">
                <?= $_SESSION['error_message']; ?>
                <?php unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>
        <div class="card-header bg-primary text-white">
            <h1 class="card-title text-center">Modifier les informations de l'utilisateur</h1>
        </div>
        <div class="card-body">
            <form action="" method="post">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="firstName" class="form-label">Prénom</label>
                    <input type="text" class="form-control" id="firstName" name="firstName" value="<?= htmlspecialchars($user['firstName']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="lastName" class="form-label">Nom</label>
                    <input type="text" class="form-control" id="lastName" name="lastName" value="<?= htmlspecialchars($user['lastName']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Entrez un nouveau mot de passe si vous souhaitez le changer" autocomplete="new-password">
                </div> 


                <?php if ($user['userId'] == $_SESSION['userId'] && $user['isAdmin']) : ?>
                    <input type="hidden" name="isAdmin" value="1"> <!-- Cache et fixe la valeur isAdmin à 1 -->
                    <div class="mb-3">
                        <label for="isAdmin" class="form-label">Statut Administrateur</label>
                        <select class="form-control" id="isAdmin" disabled>
                            <option value="1" selected>Oui</option>
                        </select>
                    </div>
                <?php else : ?>
                    <div class="mb-3">
                        <label for="isAdmin" class="form-label">Statut Administrateur</label>
                        <select class="form-control" id="isAdmin" name="isAdmin">
                            <option value="0" <?= $user['isAdmin'] ? '' : 'selected'; ?>>Non</option>
                            <option value="1" <?= $user['isAdmin'] ? 'selected' : ''; ?>>Oui</option>
                        </select>
                    </div>
                <?php endif; ?>

                <button type="submit" class="btn btn-primary">Mettre à jour</button>
                <a href="index.php?url=admin" class="btn btn-secondary">Annuler</a>
            </form>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>