<?php
session_start();

require_once "inc/functions.php";

// // Si le formulaire a été envoyé
if (!empty($_POST)) {
    // Connexion à la base de données
    require_once "inc/db.php";

    // On crée un tableau pour contenir les valeurs envoyées par l'utilisateur
    $post = array();

}


require "templates/header.php"; 
?>
<h1>Se connecter</h1>

<form method="POST">
    <div class="form-group">
        <label for="">Pseudo ou E-mail</label>
        <input type="text" name="username" class="form-control" />
    </div>

    <div class="form-group">
        <label for="">Mot de passe <a href="forget.php">(oublié ?)</a></label>
        <input type="password" name="password" class="form-control" />
    </div>

    <div class="form-group">
        <label>
            <input type="checkbox" name="remember" value="1" />Se souvenir de moi
        </label>
    </div>

    <button type="submit" class="btn btn-primary">Se connecter</button>
</form>

<?php require "templates/footer.php"; ?>
