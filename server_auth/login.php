<?php
// session_start();
$successMessage = "";
$errorMessage = "";

if (isset($_POST["req"])) {
    require_once("seConnecter.php");
}

if (!empty($successMessage)) {
    sendMessage($successMessage);
} else {
    // echo session_id();
?>
    <!DOCTYPE HTML>
    <HTML>

    <head>
        <meta charset="utf-8">
        <title>Authentification</title>
        <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="../css/com/login.css">
    </head>

    <body class="text-center">

        <main class="form-signin">
            <!-- <form method="post" action="seConnecter.php"> -->
            <form method="post" action="index.php">
                <h1 class="h3 mb-3 fw-normal">Authentification</h1>

                <?php if (!empty($errorMessage)) { ?>
                    <div class="alert alert-danger mb-3">
                        <?php echo $errorMessage ?>
                    </div>
                <?php } ?>

                <div class="form-floating">
                    <input type="text" name="login" class="form-control" id="login" placeholder="A12345">
                    <label for="login">Identifiant</label>
                </div>

                <div class="form-floating mt-2">
                    <input type="password" name="password" class="form-control" id="password" placeholder="Password">
                    <label for="password">Mot de passe</label>
                </div>

                <button class="w-100 btn btn-primary mb-3" type="submit" name="req" value="login">Se connecter</button>

                <a href="motDePasseOublie.php">Vous avez oublié votre mot de passe ?</a>

                <p class="mt-5 mb-3 text-muted">&copy; 2022</p>
            </form>
        </main>

    </HTML>
<?php

}
