<?php require_once 'header.php'; ?>

<div class="container min-vh-100 mt-5">
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
        <h1 class="card-title text-center">Mes inscriptions</h1>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Nom du Bateau</th>
                    <th>Compétition</th>
                    <th>Date de Début</th>
                    <th>Date de Fin</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($registrations as $registration) : ?>
                    <tr>
                        <td><?= htmlspecialchars($registration['boatName']); ?></td>
                        <td><?= htmlspecialchars($registration['title']); ?></td>
                        <td><?= htmlspecialchars($registration['startDate']); ?></td>
                        <td><?= htmlspecialchars($registration['endDate']); ?></td>
                        <td>
                            <?php if (empty($registration['validationDate'])) : ?>
                                <a href="index.php?url=editRegistration&registrationId=<?= $registration['registrationId']; ?>" class="btn btn-primary btn-sm">Modifier</a>
                                <a href="index.php?url=deleteRegistration&registrationId=<?= $registration['registrationId']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette inscription ?');">Supprimer</a>
                                <span class="badge bg-secondary">En attende de validation</span>

                            <?php else : ?>
                                <span class="badge bg-success">Validée</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($registrations)) : ?>
                    <tr>
                        <td colspan="5" class="text-center">Aucune inscription enregistrée.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'footer.php'; ?>