<div class="container">
    <h2>Produits GSB</h2>

    <?php if ($message !== ''): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php if ($erreur !== ''): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <p>
        <a class="btn btn-default" href="index.php?uc=produits">Liste des produits</a>
        <?php if ($gestionAutorisee): ?>
            <a class="btn btn-default" href="index.php?uc=produits&action=statistiques">Statistiques</a>
            <a class="btn btn-primary" href="index.php?uc=produits&action=ajouter">Ajouter un produit</a>
        <?php endif; ?>
    </p>

    <?php if ($action === 'detail' && $produit !== null): ?>
        <h3><?= htmlspecialchars($produit['nom'], ENT_QUOTES, 'UTF-8') ?></h3>
        <dl>
            <dt>Objectif</dt>
            <dd><?= nl2br(htmlspecialchars($produit['objectif'], ENT_QUOTES, 'UTF-8')) ?></dd>
            <dt>Informations</dt>
            <dd><?= nl2br(htmlspecialchars($produit['information'], ENT_QUOTES, 'UTF-8')) ?></dd>
            <dt>Effets indésirables</dt>
            <dd><?= nl2br(htmlspecialchars($produit['effetIndesirable'], ENT_QUOTES, 'UTF-8')) ?></dd>
        </dl>
        <?php if ($gestionAutorisee): ?>
            <a class="btn btn-primary" href="index.php?uc=produits&action=modifier&id=<?= (int)$produit['id'] ?>">Modifier</a>
            <a class="btn btn-danger" href="index.php?uc=produits&action=supprimer&id=<?= (int)$produit['id'] ?>">Supprimer</a>
        <?php endif; ?>
    <?php elseif (in_array($action, array('ajouter', 'modifier'), true) && $produit !== null): ?>
        <h3><?= $action === 'ajouter' ? 'Ajouter un produit' : 'Modifier un produit' ?></h3>
        <form method="post" action="index.php?uc=produits&action=<?= htmlspecialchars($action, ENT_QUOTES, 'UTF-8') ?><?= $action === 'modifier' ? '&id='.(int)$produit['id'] : '' ?>">
            <input type="hidden" name="jetonCsrf" value="<?= htmlspecialchars(jetonCsrf(), ENT_QUOTES, 'UTF-8') ?>">
            <div class="form-group">
                <label for="nom">Nom</label>
                <input class="form-control" id="nom" name="nom" maxlength="60" required value="<?= htmlspecialchars($produit['nom'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label for="objectif">Objectif</label>
                <textarea class="form-control" id="objectif" name="objectif" required><?= htmlspecialchars($produit['objectif'], ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <div class="form-group">
                <label for="information">Informations</label>
                <textarea class="form-control" id="information" name="information" required><?= htmlspecialchars($produit['information'], ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <div class="form-group">
                <label for="effetIndesirable">Effets indésirables</label>
                <textarea class="form-control" id="effetIndesirable" name="effetIndesirable" required><?= htmlspecialchars($produit['effetIndesirable'], ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <button class="btn btn-success" type="submit">Enregistrer</button>
            <a class="btn btn-default" href="index.php?uc=produits">Annuler</a>
        </form>
    <?php elseif ($action === 'supprimer' && $produit !== null): ?>
        <h3>Supprimer un produit</h3>
        <p>Confirmez-vous la suppression de « <?= htmlspecialchars($produit['nom'], ENT_QUOTES, 'UTF-8') ?> » ?</p>
        <form method="post" action="index.php?uc=produits&action=supprimer&id=<?= (int)$produit['id'] ?>">
            <input type="hidden" name="jetonCsrf" value="<?= htmlspecialchars(jetonCsrf(), ENT_QUOTES, 'UTF-8') ?>">
            <button class="btn btn-danger" type="submit">Confirmer la suppression</button>
            <a class="btn btn-default" href="index.php?uc=produits">Annuler</a>
        </form>
    <?php elseif ($action === 'statistiques' && $statistiques !== null): ?>
        <h3>Statistiques des consultations</h3>
        <p>Nombre total de consultations : <strong><?= (int)$statistiques['totaux']['nombreConsultations'] ?></strong></p>
        <p>Nombre de médecins ayant consulté un produit : <strong><?= (int)$statistiques['totaux']['nombreMedecins'] ?></strong></p>
        <h4>Produits les plus consultés</h4>
        <?php if (count($statistiques['produits']) === 0): ?>
            <p>Aucune consultation n'a encore été enregistrée.</p>
        <?php else: ?>
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
    <?php else: ?>
        <?php if (count($produits) === 0): ?>
            <p>Aucun produit n'est enregistré.</p>
        <?php else: ?>
            <table class="table table-striped">
                <thead><tr><th>Produit</th><th>Objectif</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($produits as $ligne): ?>
                    <tr>
                        <td><?= htmlspecialchars($ligne['nom'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($ligne['objectif'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><a href="index.php?uc=produits&action=detail&id=<?= (int)$ligne['id'] ?>">Consulter</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>
</div>
