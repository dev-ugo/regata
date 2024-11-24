<?php require_once 'header.php'; ?>

<div class="container mt-5 min-vh-100">
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
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h1 class="card-title text-center">Modifier le Bateau</h1>
        </div>

        <div class="card-body">
            <form action="" method="post">
                <div class="mb-3">
                    <label for="boatName" class="form-label">Nom du bateau</label>
                    <input type="text" class="form-control" id="boatName" name="boatName" value="<?= htmlspecialchars_decode($boat['boatName']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="officialId" class="form-label">Numéro officiel</label>
                    <input type="text" class="form-control" id="officialId" name="officialId" value="<?= htmlspecialchars($boat['officialId']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="sailId" class="form-label">Numéro de voile</label>
                    <input type="text" class="form-control" id="sailId" name="sailId" value="<?= htmlspecialchars($boat['sailId']) ?>" required>
                </div>
                <div class="mb-3">
                    <label for="handicap" class="form-label">Handicap</label>
                    <input type="number" class="form-control" id="handicap" name="handicap" value="<?= htmlspecialchars($boat['handicap']) ?>" step="0.01" required>
                </div>
                <div class="mb-3">
                    <label for="classId" class="form-label">Classe du bateau</label>
                    <select class="form-control" id="classId" name="classId">
                        <?php foreach ($classes as $class) : ?>
                            <option value="<?= $class['classId']; ?>" <?= $class['classId'] == $boat['classId'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($class['className']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                    <a href="index.php?url=myBoats" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>