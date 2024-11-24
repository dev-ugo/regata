<?php require_once 'header.php'; ?>

<div class="container min-vh-100 mt-5">
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
        <h1 class="card-title text-center">Mes bateaux</h1>
    </div>
    <?php if (!empty($boats)) : ?>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nom du Bateau</th>
                                <th>Numéro de Voile</th>
                                <th>Numéro officiel</th>
                                <th>Classe</th>
                                <th>Handicap</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($boats as $boat) : ?>
                                <tr>
                                    <td><?= htmlspecialchars_decode($boat['boatName']); ?></td>
                                    <td><?= htmlspecialchars($boat['sailId']); ?></td>
                                    <td><?= htmlspecialchars($boat['officialId']); ?></td>
                                    <td><?= htmlspecialchars($boat['className']); ?></td>
                                    <td><?= htmlspecialchars($boat['handicap']); ?></td>
                                    <td>
                                        <a href="index.php?url=editBoat&boatId=<?= $boat['boatId']; ?>" class="btn btn-primary btn-sm">Modifier</a>
                                        <a href="#" class="btn btn-danger btn-sm delete-boat-button" data-boat-id="<?= $boat['boatId']; ?>" data-bs-toggle="modal" data-bs-target="#deleteBoatModal">Supprimer</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php else : ?>
        <div class="alert alert-info" role="alert">
            Vous n'avez pas encore de bateaux.
        </div>
    <?php endif; ?>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteBoatModal" tabindex="-1" aria-labelledby="deleteBoatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteBoatModalLabel">Confirmation de suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer ce bateau ?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <a href="#" class="btn btn-danger" id="deleteBoatConfirmButton">Supprimer</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const deleteBoatButtons = document.querySelectorAll('.delete-boat-button');
        const deleteBoatConfirmButton = document.getElementById('deleteBoatConfirmButton');

        deleteBoatButtons.forEach(button => {
            button.addEventListener('click', function() {
                const boatId = this.getAttribute('data-boat-id');
                deleteBoatConfirmButton.href = `index.php?url=deleteBoat&boatId=${boatId}`;
            });
        });
    });
</script>

<?php require_once 'footer.php'; ?>
