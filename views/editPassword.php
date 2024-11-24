<?php require_once 'header.php'; ?>

<div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="card text-center">
        <?php if (isset($_SESSION['error_message'])) : ?>
            <div class="alert alert-danger" role="alert">
                <?= $_SESSION['error_message']; ?>
                <?php unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success_message'])) : ?>
            <div class="alert alert-success" role="alert">
                <?= $_SESSION['success_message']; ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        <div class="card-header bg-primary text-white">
            <h2 class="card-title text-center">Changer mot de passe</h1>
        </div>
        <div class="card-body">
            <form action="" method="post">
                <div class="mb-3">
                    <label for="old_password" class="form-label">Mot de passe actuel</label>
                    <input type="password" class="form-control" id="old_password" name="old_password" required>
                </div>
                <div class="mb-3">
                    <label for="new_password" class="form-label">Nouveau mot de passe</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                </div>
                <button type="submit" class="btn btn-primary" name="editPassword" value="editPassword">Changer le mot de passe</button>
            </form>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>