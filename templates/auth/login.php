<h1>Connexion</h1>

<div class="card" style="max-width: 400px; margin: 0 auto;">
    <form action="index.php?page=auth&action=login_process" method="POST">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <div>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div>
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div style="margin-top: 1.5rem;">
            <button type="submit" class="btn" style="width: 100%;">Se connecter</button>
        </div>

        <p style="margin-top: 1rem; text-align: center; font-size: 0.875rem;">
            Pas encore de compte ? <a href="index.php?page=auth&action=register">S'inscrire</a>
        </p>
    </form>
</div>