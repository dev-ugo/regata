<?php require_once 'header.php'; ?>

<div class="container min-vh-100 mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h1 class="card-title text-center">Compétitions</h1>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Titre</th>
                            <th>Description</th>
                            <th>Date de Début</th>
                            <th>Date de Fin</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($competitions as $competition) : ?>
                            <tr>
                                <td><?= htmlspecialchars($competition['title']); ?></td>
                                <td><?= htmlspecialchars($competition['description']); ?></td>
                                <td><?= date('d/m/Y', strtotime($competition['startDate'])); ?></td>
                                <td><?= date('d/m/Y', strtotime($competition['endDate'])); ?></td>
                                <td>
                                    <span class="badge bg-info text-dark"><?= htmlspecialchars($competition['status']); ?></span>
                                </td>
                                <td>
                                    <a href="index.php?url=competitionDetails&competitionId=<?= $competition['competitionId']; ?>" class="btn btn-sm btn-primary">Détails</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
