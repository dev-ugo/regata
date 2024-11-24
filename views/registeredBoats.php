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
    <div class="card-header bg-primary text-white">
        <h1 class="card-title text-center">Bateaux inscrits</h1>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="bg-dark text-white">
                <tr>
                    <th>Nom du Bateau</th>
                    <th>Numéro de Voile</th>
                    <th>Compétition</th>
                    <th>Date de Début</th>
                    <th>Date de Fin</th>
                    <th>Date de Validation</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($boats as $boat) : ?>
                    <tr>
                        <td><?= htmlspecialchars_decode($boat['boatName']); ?></td>
                        <td><?= htmlspecialchars($boat['sailId']); ?></td>
                        <td><?= htmlspecialchars($boat['title']); ?></td>
                        <td><?= htmlspecialchars($boat['startDate']); ?></td>
                        <td><?= htmlspecialchars($boat['endDate']); ?></td>
                        <td><?= $boat['validationDate'] ? htmlspecialchars($boat['validationDate']) : '<span class="text-danger">Non validé</span>'; ?></td>
                        <td>
                            <?php if (!$boat['validationDate']) : ?>
                                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#validateModal" data-boat-id="<?= $boat['boatId']; ?>">Valider</button>
                            <?php else : ?>
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#invalidateModal" data-boat-id="<?= $boat['boatId']; ?>">Invalider</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- Validate Modal -->
<div class="modal fade" id="validateModal" tabindex="-1" aria-labelledby="validateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="validateModalLabel">Confirmation de Validation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir valider cette inscription ?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary confirm-validate">Valider</button>
            </div>
        </div>
    </div>
</div>

<!-- Invalidate Modal -->
<div class="modal fade" id="invalidateModal" tabindex="-1" aria-labelledby="invalidateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="invalidateModalLabel">Confirmation d'Invalidation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir invalider cette inscription ?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger confirm-invalidate">Invalider</button>
            </div>
        </div>
    </div>
</div>

<script>
    /**
     * Initialisation et gestion de la modale de validation
     */
    var validateModal = document.getElementById('validateModal');
    validateModal.addEventListener('show.bs.modal', function(event) {
        // Obtient le bouton qui a déclenché la modale
        var button = event.relatedTarget;
        // Récupère l'ID du bateau à partir de l'attribut de données du bouton
        var boatId = button.getAttribute('data-boat-id');
        // Sélectionne le bouton de confirmation dans la modale de validation
        var confirmButton = validateModal.querySelector('.confirm-validate');
        // Ajoute un gestionnaire d'événement au clic pour rediriger vers l'URL de validation
        confirmButton.onclick = function() {
            window.location.href = `index.php?url=validateBoat&boatId=${boatId}`;
        };
    });

    /**
     * Initialisation et gestion de la modale d'invalidation
     */
    var invalidateModal = document.getElementById('invalidateModal');
    invalidateModal.addEventListener('show.bs.modal', function(event) {
        // Obtient le bouton qui a déclenché la modale
        var button = event.relatedTarget;
        // Récupère l'ID du bateau à partir de l'attribut de données du bouton
        var boatId = button.getAttribute('data-boat-id');
        // Sélectionne le bouton de confirmation dans la modale d'invalidation
        var confirmButton = invalidateModal.querySelector('.confirm-invalidate');
        // Ajoute un gestionnaire d'événement au clic pour rediriger vers l'URL d'invalidation
        confirmButton.onclick = function() {
            window.location.href = `index.php?url=invalidateBoat&boatId=${boatId}`;
        };
    });
</script>

<?php require_once 'footer.php'; ?>