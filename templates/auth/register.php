<h1>Inscription</h1>

<div class="card" style="max-width: 500px; margin: 0 auto;">
    <form action="index.php?page=auth&action=register_process" method="POST">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <div>
            <label for="name">Nom complet</label>
            <input type="text" id="name" name="name" required>
        </div>

        <div>
            <label for="email">Adresse Email</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div>
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required minlength="8">
        </div>

        <div style="margin-top: 1.5rem;">
            <button type="submit" class="btn" style="width: 100%;">Créer mon compte</button>
        </div>

        <p style="margin-top: 1rem; text-align: center; font-size: 0.875rem;">
            Déjà inscrit ? <a href="index.php?page=auth&action=login">Se connecter</a>
        </p>
    </form>
</div>