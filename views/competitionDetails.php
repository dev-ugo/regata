<?php require_once 'header.php'; ?>

<div class="container mt-5 min-vh-100">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h1 class="card-title text-center">Détails de la compétition</h1>
        </div>
        <div class="card-body">
            <h2><?= htmlspecialchars($competition['title']); ?></h2>
            <p class="text-muted"><?= htmlspecialchars($competition['description']); ?></p>
            <p><strong>Date de début:</strong> <?= date('d/m/Y', strtotime($competition['startDate'])); ?></p>
            <p><strong>Date de fin:</strong> <?= date('d/m/Y', strtotime($competition['endDate'])); ?></p>
            <p><strong>Statut:</strong> <span class="badge <?= $competition['status'] == 'Phase d\'inscription' ? 'bg-success' : 'bg-secondary'; ?>"><?= htmlspecialchars($competition['status']); ?></span></p>
        </div>
    </div>

    <h2 class="mt-4">Bateaux inscrits</h2>
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Numéro de Voile</th>
                    <th>Nom du Bateau</th>
                    <th>Classe</th>
                    <th>Handicap</th>
                    <th>Nom du Skipper</th>
                    <th>Membres d'équipage</th>
                    <th>Statut de l'Inscription</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($boats as $boat) : ?>
                    <tr>
                        <td><?= htmlspecialchars($boat['sailId']); ?></td>
                        <td><?= htmlspecialchars($boat['boatName']); ?></td>
                        <td><?= htmlspecialchars($boat['className']); ?></td>
                        <td><?= htmlspecialchars($boat['handicap']); ?></td>
                        <td><?= htmlspecialchars($boat['skipperFirstName'] . ' ' . $boat['skipperLastName']); ?></td>
                        <td><?= isset($boat['crewMembers']) ? htmlspecialchars($boat['crewMembers']) : ''; ?></td>
                        <td><span class="badge <?= !empty($boat['status']) ? 'bg-success' : 'bg-danger'; ?>"><?= !empty($boat['status']) ? 'Validée' : 'Non validée'; ?></span></td>
                        <td class="text-center">
                            <div class="d-grid gap-2 d-md-block">
                                <?php if (User::isAdmin()) : ?>
                                    <?php if (empty($boat['status'])) : ?>
                                        <a href="index.php?url=validateBoat&boatId=<?= $boat['boatId']; ?>" class="btn btn-success btn-sm">Valider</a>
                                    <?php else : ?>
                                        <a href="index.php?url=invalidateBoat&boatId=<?= $boat['boatId']; ?>" class="btn btn-warning btn-sm">Invalider</a>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if (empty($boat['status']) && isset($_SESSION['userId']) && ($_SESSION['userId'] == $boat['ownerId'] || $_SESSION['userId'] == $boat['skipperId'])) : ?>
                                    <a href="index.php?url=editRegistration&registrationId=<?= $boat['registrationId']; ?>" class="btn btn-warning btn-sm">Modifier</a>
                                    <a href="index.php?url=deleteRegistration&registrationId=<?= $boat['registrationId']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette inscription?');">Supprimer</a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if ($competition['status'] == "Phase d’inscription" && isset($_SESSION['userId']))  : ?>
        <a href="index.php?url=registerBoatToCompetition&competitionId=<?= $competition['competitionId']; ?>" class="btn btn-primary mt-3">S'inscrire à cette compétition</a>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>