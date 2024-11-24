<?php require_once 'header.php'; ?>

<div class="container min-vh-100 mt-5">
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
            <h2 class="card-title text-center">Inscrire un Bateau à une Compétition</h1>
        </div>
        <div class="card-body">
            <form action="" method="post">
                <div class="mb-3">
                    <label for="boatId" class="form-label">Sélectionnez votre bateau:</label>
                    <select id="boatId" name="boatId" class="form-select">
                        <?php foreach ($boats as $boat) : ?>
                            <option value="<?= $boat['boatId'] ?>" <?= ($formData['boatId'] == $boat['boatId']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($boat['boatName']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="competitionId" class="form-label">Sélectionnez la compétition:</label>
                    <select id="competitionId" name="competitionId" class="form-select">
                        <?php foreach ($competitions as $competition) : ?>
                            <option value="<?= $competition['competitionId'] ?>" <?= ($formData['competitionId'] == $competition['competitionId']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($competition['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="skipperId" class="form-label">Choisissez le skipper:</label>
                    <select id="skipperId" name="skipperId" class="form-select">
                        <?php foreach ($users as $user) : ?>
                            <option value="<?= $user['userId'] ?>" <?= ($formData['skipperId'] == $user['userId']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($user['firstName'] . ' ' . $user['lastName']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="crewMembers" class="form-label">Membres d'équipage:</label>
                    <select id="crewMembers" name="crewMembers[]" class="form-select" multiple>
                        <?php foreach ($users as $user) : ?>
                            <option value="<?= $user['userId'] ?>" <?= (in_array($user['userId'], $formData['crewMembers'])) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($user['firstName'] . ' ' . $user['lastName']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" name="registerBoatToCompetition" value="registerBoatToCompetition">Inscrire Bateau</button>
            </form>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>