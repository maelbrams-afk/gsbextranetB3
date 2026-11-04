<div class="container mt-4">
    <h2>Droit à la portabilité des données (Article 20 RGPD)</h2>

    <!-- Affichage d'un message d'erreur s'il y en a un -->
    <?php if (isset($erreur)): ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($erreur) ?>
        </div>
    <?php endif; ?>

    <?php if (!isset($succes)): ?>
        <!-- Étape 1 : Demande de confirmation à l'utilisateur -->
        <div class="card p-4 my-3 shadow-sm">
            <p class="lead"><strong>Souhaitez-vous récupérer la totalité de vos données personnelles sous format JSON ?</strong></p>
            <p class="text-muted">Conformément à l'Article 20 du RGPD, ce fichier contiendra l'ensemble de vos informations enregistrées dans notre système, à l'exception de vos données d'authentification.</p>
            
            <form action="index.php?uc=portabilite&action=generer" method="POST" class="mt-2">
                <button type="submit" name="confirmation" value="oui" class="btn btn-success me-2">Oui, générer mon fichier JSON</button>
                <a href="index.php?uc=sommaire" class="btn btn-secondary">Non, annuler</a>
            </form>
        </div>
    <?php else: ?>
        <!-- Étape 2 : Affichage du succès, du téléchargement et de l'empreinte -->
        <div class="alert alert-success" role="alert">
            <h4 class="alert-heading">Fichier généré avec succès !</h4>
            <p>Votre fichier de portabilité a été créé dans le dossier sécurisé du serveur.</p>
            <hr>
            <a href="<?= htmlspecialchars($resultatExport['chemin_relatif']) ?>" download class="btn btn-primary btn-lg">
                📥 Télécharger <?= htmlspecialchars($resultatExport['nom_fichier']) ?>
            </a>
        </div>

        <div class="card p-4 shadow-sm bg-light mt-4">
            <h4>Vérification de l'intégrité</h4>
            <p class="text-muted">Pour vous assurer que le fichier n'a pas été altéré, vous pouvez comparer son empreinte cryptographique.</p>
            
            <div class="mb-3">
                <label class="form-label">Algorithme utilisé :</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($resultatExport['algorithme']) ?>" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">Empreinte SHA-256 :</label>
                <input type="text" class="form-control font-monospace" value="<?= htmlspecialchars($resultatExport['empreinte']) ?>" readonly>
            </div>

            <a href="index.php?uc=portabilite&action=verifier" class="btn btn-outline-info mt-2">
                Vérifier l'intégrité d'un fichier téléchargé
            </a>
        </div>
    <?php endif; ?>
</div>