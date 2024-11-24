<?php require_once 'header.php'; ?>

<div class="container-fluid mt-5 min-vh-100">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <?php if (isset($_SESSION['success_message'])) : ?>
                <div class="alert alert-success" role="alert">
                    <?= $_SESSION['success_message']; ?>
                    <?php unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_COOKIE['success_message'])) : ?>
                <div class="alert alert-success" role="alert">
                    <?= $_COOKIE['success_message']; ?>
                    <?php setcookie('success_message', '', time() - 3600); ?>
                </div>
            <?php endif; ?>

            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h1 class="card-title text-center">Liste des Bateaux</h1>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nom du Bateau</th>
                                    <th>Numéro d'identification</th>
                                    <th>Numéro de Voile</th>
                                    <th>Classe</th>
                                    <th>Handicap</th>
                                    <th>Propriétaire</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($boats as $boat) : ?>
                                    <tr class="table-row" style="cursor:pointer;">
                                        <td><?= htmlspecialchars($boat['boatName']); ?></td>
                                        <td><?= htmlspecialchars($boat['officialId']); ?></td>
                                        <td><?= htmlspecialchars($boat['sailId']); ?></td>
                                        <td><?= htmlspecialchars($boat['className']); ?></td>
                                        <td><?= htmlspecialchars($boat['handicap']); ?></td>
                                        <td><?= htmlspecialchars(ucwords($boat['firstName']) . ' ' . ucwords($boat['lastName'])); ?></td>
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

<?php require_once 'footer.php'; ?>