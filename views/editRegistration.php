<?php require_once 'header.php'; ?>

<div class="container mt-5 min-vh-100">
    <div class="card-header bg-primary text-white">
        <h1 class="card-title text-center">Modifier l'Inscription à une Compétition</h1>
    </div>
    <?php if (isset($_SESSION['success_message'])) : ?>
        <div class="alert alert-success">
            <?= $_SESSION['success_message']; ?>
            <?php unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])) : ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error_message']; ?>
            <?php unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <form action="" method="post">
        <input type="hidden" name="registrationId" value="<?= htmlspecialchars($registration['registrationId']); ?>">

        <div class="mb-3">
            <label for="boatId" class="form-label">Nom du bateau:</label>
            <input type="text" class="form-control" id="boatName" value="<?= htmlspecialchars($registration['boatName']); ?>" disabled>
        </div>

        <div class="mb-3">
            <label for="competitionTitle" class="form-label">Compétition:</label>
            <input type="text" class="form-control" id="competitionTitle" value="<?= htmlspecialchars($registration['competitionTitle']); ?>" disabled>
        </div>

        <div class="mb-3">
            <label for="skipperId" class="form-label">Choisissez le skipper:</label>
            <select id="skipperId" name="skipperId" class="form-select">
                <?php foreach ($users as $user) : ?>
                    <option value="<?= $user['userId']; ?>" <?= $user['userId'] == $registration['skipperId'] ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($user['firstName'] . ' ' . $user['lastName']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="crewMembers" class="form-label">Membres d'équipage:</label>
            <select id="crewMembers" name="crewMembers[]" class="form-select" multiple>
                <?php foreach ($users as $user) : ?>
                    <option value="<?= $user['userId']; ?>" <?= isset($crewMembers) && in_array($user['userId'], $crewMembers) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($user['firstName'] . ' ' . $user['lastName']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

        </div>


        <button type="submit" class="btn btn-primary">Mettre à jour</button>
        <a href="index.php?url=myRegisteredBoats" class="btn btn-secondary">Annuler</a>
    </form>
</div>

<?php require_once 'footer.php'; ?>