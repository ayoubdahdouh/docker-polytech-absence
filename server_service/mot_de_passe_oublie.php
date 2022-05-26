<?php
session_start();
if (isset($_SESSION['erreurLogin']))
    $erreurLogin = $_SESSION['erreurLogin'];
else {
    $erreurLogin = "";
}
session_destroy();
?>
<!DOCTYPE HTML>
<HTML>

<head>
    <meta charset="utf-8">
    <title>Réinitialiser le mot de passe</title>
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/com/login.css">
</head>


<body class="text-center">

    <main class="form-signin">
        <form method="post" action="mot_de_passe_oublie.php">
            <h1 class="h3 mb-3 fw-normal">Réinitialiser le mot de passe</h1>

            <?php if (!empty($erreurLogin)) { ?>
                <div class="alert alert-danger mb-3">
                    <?php echo $erreurLogin ?>
                </div>
            <?php } ?>

            <div class="form-floating">
                <input type="text" name="id" class="form-control" id="id" placeholder="Identifiant ou Adresse email">
                <label for="id">Identifiant ou Email</label>
            </div>
            <div class="mt-3">
                <button class="w-100 btn btn-primary mb-3" type="submit">Réinitialiser</button>
                <button class="w-100 btn btn-secondary mb-3" type="submit">Annuler</button>
            </div>

            <p class="mt-5 mb-3 text-muted">&copy; 2022</p>
        </form>
    </main>

</HTML>