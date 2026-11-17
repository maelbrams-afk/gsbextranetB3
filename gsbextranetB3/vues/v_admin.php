<div class="container">
    <h2>Administration du site</h2>

    <?php if (!empty($erreur)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php if ($message !== ''): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <h3>Maintenance</h3>
    <p>État actuel : <strong><?= $maintenance ? 'Activée' : 'Désactivée' ?></strong></p>
    <form method="post" action="index.php?uc=admin&action=maintenance">
        <input type="hidden" name="jetonCsrf" value="<?= htmlspecialchars(jetonCsrf(), ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="maintenance" value="<?= $maintenance ? '0' : '1' ?>">
        <button class="btn <?= $maintenance ? 'btn-success' : 'btn-warning' ?>" type="submit">
            <?= $maintenance ? 'Remettre le site en service' : 'Mettre le site en maintenance' ?>
        </button>
    </form>

    <h3>Statistiques des produits</h3>
    <p>Nombre total de consultations : <strong><?= (int)$statistiques['totaux']['nombreConsultations'] ?></strong></p>
    <p>Nombre de médecins ayant consulté un produit : <strong><?= (int)$statistiques['totaux']['nombreMedecins'] ?></strong></p>
    <?php if (count($statistiques['produits']) > 0): ?>
        <table class="table table-striped">
            <thead><tr><th>Produit</th><th>Consultations</th></tr></thead>
            <tbody>
            <?php foreach ($statistiques['produits'] as $ligne): ?>
                <tr>
                    <td><?= htmlspecialchars($ligne['nomProduit'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= (int)$ligne['nombreConsultations'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <h3>Historique des opérations sur les produits</h3>
    <?php if (count($operations) === 0): ?>
        <p>Aucune opération n'a encore été enregistrée.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead><tr><th>Date</th><th>Compte</th><th>Opération</th><th>Produit</th><th>Adresse IP</th></tr></thead>
                <tbody>
                <?php foreach ($operations as $operation): ?>
                    <tr>
                        <td><?= htmlspecialchars($operation['dateOperation'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($operation['compte'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($operation['action'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($operation['nomProduit'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($operation['adresseIp'], ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
