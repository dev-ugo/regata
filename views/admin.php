<?php require_once 'header.php'; ?>

<div class="container mt-5 min-vh-100">

    <div class="row justify-content-center mt-4">

        <div class="col-md-10">
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
                <h1 class="card-title text-center">Admin</h1>
            </div>
            <!-- Carte des utilisateurs -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h2 class="card-title text-center">Liste des utilisateurs</h2>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Email</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user) : ?>
                                    <tr>
                                        <td><?= htmlspecialchars($user['email']); ?></td>
                                        <td>
                                            <a href="index.php?url=editUser&userId=<?= $user['userId']; ?>" class="btn btn-sm btn-primary">Modifier</a>
                                            <a href="#" class="btn btn-sm btn-danger delete-user-button" data-user-id="<?= $user['userId']; ?>" data-bs-toggle="modal" data-bs-target="#deleteUserModal">Supprimer</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Carte des bateaux -->
            <div class="card shadow-lg mb-5">
                <div class="card-header bg-primary text-white">
                    <h2 class="card-title text-center">Liste des bateaux</h2>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Nom du Bateau</th>
                                    <th>Numéro d'immatriculation</th>
                                    <th>Numéro de Voile</th>
                                    <th>Classe</th>
                                    <th>Handicap</th>
                                    <th>Nom du Propriétaire</th>
                                    <th>Prénom du Propriétaire</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($boats as $boat) : ?>
                                    <tr>
                                        <td><?= htmlspecialchars_decode($boat['boatName']); ?></td>
                                        <td><?= htmlspecialchars($boat['officialId']); ?></td>
                                        <td><?= htmlspecialchars($boat['sailId']); ?></td>
                                        <td><?= htmlspecialchars($boat['className']); ?></td>
                                        <td><?= htmlspecialchars($boat['handicap']); ?></td>
                                        <td><?= htmlspecialchars(ucwords($boat['lastName'])); ?></td>
                                        <td><?= htmlspecialchars(ucwords($boat['firstName'])); ?></td>
                                        <td>
                                            <div class="d-grid gap-2">
                                                <a href="index.php?url=editBoatAsAdmin&boatId=<?= $boat['boatId']; ?>" class="btn btn-primary btn-sm">Éditer</a>
                                                <a href="#" class="btn btn-danger btn-sm delete-button" data-boat-id="<?= $boat['boatId']; ?>" data-bs-toggle="modal" data-bs-target="#deleteModal">Supprimer</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmation de suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer ce bateau ?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <a href="#" class="btn btn-danger" id="deleteConfirmButton">Supprimer</a>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteUserModalLabel">Confirmation de suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer cet utilisateur ?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <a href="#" class="btn btn-danger" id="deleteUserConfirmButton">Supprimer</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const deleteUserButtons = document.querySelectorAll('.delete-user-button');
        const deleteUserConfirmButton = document.getElementById('deleteUserConfirmButton');

        deleteUserButtons.forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-user-id');
                deleteUserConfirmButton.href = `index.php?url=deleteUser&userId=${userId}`; 
            });
        });
    });
    document.addEventListener("DOMContentLoaded", function() {
        const deleteButtons = document.querySelectorAll('.delete-button');
        const deleteConfirmButton = document.getElementById('deleteConfirmButton');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const boatId = this.getAttribute('data-boat-id');
                deleteConfirmButton.href = `index.php?url=deleteBoatAsAdmin&boatId=${boatId}`; 
            });
        });
    });
</script>

<?php require_once 'footer.php'; ?>