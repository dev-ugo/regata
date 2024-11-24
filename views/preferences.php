<?php require_once 'header.php'; ?>

<div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="card text-center" style="width: 24rem;">
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
            <h2 class="card-title text-center">Préférences</h1>
        </div>
        <div class="card-body">
            <form method="post" action="">
                <div class="mb-3">
                    <label for="email" class="form-label">Adresse email</label>
                    <input type="email" class="form-control" id="email" disabled value="<?= htmlspecialchars($userData['email']); ?>">
                </div>

                <div class="mb-3">
                    <label for="firstname" class="form-label">Prénom</label>
                    <input type="text" class="form-control" id="firstname" name="firstname" value="<?= htmlspecialchars($userData['firstName']); ?>">
                </div>

                <div class="mb-3">
                    <label for="lastname" class="form-label">Nom</label>
                    <input type="text" class="form-control" id="lastname" name="lastname" value="<?= htmlspecialchars($userData['lastName']); ?>">
                </div>

                <!-- Bouton de soumission avec style Bootstrap -->
                <button type="submit" class="btn btn-primary" name="editProfil" value="editProfil">Enregistrer les modifications</button>
            </form>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>