<?php require_once 'header.php'; ?>

<div class="container mt-5 min-vh-100">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
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
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h1 class="card-title text-center">Ajouter un nouveau bateau</h1>
                </div>
                <div class="card-body">
                    <form action="" method="post">
                        <div class="mb-3">
                            <label for="boatName" class="form-label">Nom du bateau</label>
                            <input type="text" class="form-control" id="boatName" name="boatName" value="<?= htmlspecialchars($formData['boatName']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="officialId" class="form-label">Numéro d'identification</label>
                            <input type="text" class="form-control" id="officialId" name="officialId" value="<?= htmlspecialchars($formData['officialId']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="sailId" class="form-label">Numéro de Voile</label>
                            <input type="text" class="form-control" id="sailId" name="sailId" value="<?= htmlspecialchars($formData['sailId']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="handicap" class="form-label">Handicap</label>
                            <input type="number" step="0.01" class="form-control" id="handicap" name="handicap" value="<?= htmlspecialchars($formData['handicap']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="classId" class="form-label">Classe du bateau</label>
                            <select class="form-control" id="classId" name="classId">
                                <?php foreach ($classes as $class) : ?>
                                    <option value="<?= $class['classId']; ?>" <?= $class['classId'] == $formData['classId'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($class['className']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary" name="addBoat" value="addBoat">Ajouter Bateau</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>