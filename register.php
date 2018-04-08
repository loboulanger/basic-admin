<?php
session_start();
require_once "inc/functions.php";

// Si le formulaire a été envoyé
if (!empty($_POST)){
    // On crée un tableau pour contenir les valeurs envoyées par l'utilisateur
    $post = array();
    // Et pour chacune des valeurs du tableau :
    // On supprime les éventuels espaces en début et fin de chaîne avec trim()
    // On supprime les éventuelles balises HTML et PHP avec strip_tags() de la chaîne
    foreach ($_POST as $key => $value) {
        $post[$key] = trim(strip_tags($value));
    }

    // On crée une variable pour contenir les éventuelles erreurs du formulaire
    $errors = array();
    
    // Connexion à la base de données
    require_once "inc/db.php";

    // Si le champ username n'existe pas ou que la chaîne contient :
    // Moins de 4 caractères ou 
    // Plus de 16 caractères ou
    // Un espace
	if (!isset($post['username']) || !preg_match('/^\S{4,16}$/', $post['username'])){
        // On crée le message d'erreur correspondant
		$errors['username'] = "Votre pseudo doit contenir 4 à 16 caractères sans espace";
	} else { // Sinon fait une requête à la base de données pour vérifier si le pseudo n'existe pas déjà
		$query = $pdo->prepare("SELECT id FROM users WHERE username = :username");
        $query->bindValue(':username', $post['username']);
        $query->execute();
        $user = $query->fetch(PDO::FETCH_ASSOC);
        // Si le pseudo existe déjà (=true)
        if ($user){
            // On crée le message d'erreur correspondant
            $errors['username'] = 'Ce pseudo est déjà pris';
        }
    }

    // Si le champ email n'existe pas ou que l'adresse email n'est pas valide :
	if (!isset($post['email']) || !filter_var($post['email'], FILTER_VALIDATE_EMAIL)){
        // On crée le message d'erreur correspondant
		$errors['email'] = "Votre email n'est pas valide";
	} else { // Sinon fait une requête à la base de données pour vérifier si l'email n'existe pas déjà
		$query = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $query->bindValue(':email', $post['email']);
        $query->execute();
        $user = $query->fetch(PDO::FETCH_ASSOC);
        // Si l'email existe déjà
        if ($user){
            // On crée le message d'erreur correspondant
            $errors['email'] = 'Cet email est déjà utilisé';
        }
    }

    // Si le champ password n'existe pas ou est vide :
	if (!isset($post['password']) || empty($post['password'])){
        // On crée le message d'erreur correspondant
		$errors['password'] = "Vous devez renseigner un mot de passe";
	}
    
    // Si le champ password :
    // Contient moins de 8 caractères ou
    // Ne contient pas au moins une lettre majuscule ou
    // Ne contient pas au moins une lettre minuscule ou
    // Ne contient pas au moins un chiffre ou un caractère spécial
    // Si le champ password n'existe pas ou est vide :
	if (!preg_match('/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/', $post['password'])){
        // On crée le message d'erreur correspondant
		$errors['password'] = "Le mot de passe doit contenir au minimum 8 caractères dont une lettre majuscule et une minuscule et un chiffre ou cacractère spécial";
	}

    // Si le mot de passe entré ne match pas avec le mot de passe confirmé
	if ($post['password'] != $post['password_confirm']){
         // On crée le message d'erreur correspondant
		$errors['password'] = "Les mots de passe ne correspondent pas";
    }
    
    // Si le formulaire ne contient pas d'erreurs
    if (empty($errors)) {
        // On ajoute le nouvel utilisateur dans la base de données en créant une clé de hachage pour le mot de passe avec password_hash()
        $req = $pdo->prepare("INSERT INTO users SET username = ?, password = ?, email = ?");
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        
        $req->execute([$post['username'], $password, $post['email']]);

        $_SESSION['flash']['success'] = "Votre inscription a bien été prise en compte !";

        header('Location: login.php');
        exit();
    }
}
?>


<?php require "templates/header.php"; ?>
<h1>S'inscrire</h1>

<?php if(!empty($errors)): ?>
<div class="alert alert-danger">
    <p>Le formulaire n'a pas été rempli correctement !</p>
    <ul>
    <!-- Affichage des erreurs éventuelles contenues dans le formulaire -->
    <?php
    foreach ($errors as $error) {
        ?>
        <li><?= $error; ?></li>
        <?php
    }
    ?>
    </ul>
</div>
<?php endif; ?>

<form action="" method="POST">
	<div class="form-group">
		<label for="">Pseudo</label>
		<input type="text" name="username" class="form-control" value="<?php if(isset($_POST['username'])) { echo $post['username']; } ?>">
	</div>

	<div class="form-group">
		<label for="">Email</label>
		<input type="email" name="email" class="form-control" value="<?php if(isset($_POST['email'])) { echo $post['email']; } ?>">
	</div>

	<div class="form-group">
		<label for="">Mot de passe</label>
		<input type="password" name="password" class="form-control" />
	</div>

	<div class="form-group">
		<label for="">Confirmer votre mot de passe</label>
		<input type="password" name="password_confirm" class="form-control" />
	</div>

	<button type="submit" class="btn btn-primary">M'inscrire</button>
</form>

<?php require "templates/footer.php"; ?>