<?php
require_once('identifier.php');
require_once("connexiondb.php");

$warningMessage = "";
$successMessage = "";

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
if (isset($_POST["voir"])) {
    $id_u = intval($_POST["voir"]);
    $sql = "SELECT id_u, login, email, role, etat, prenom, nom FROM utilisateur WHERE id_u=?";
    $res = sqlQuery($sql, [$id_u]);

    $login = $res['login'];
    $email = $res['email'];
    $role = $res['role'];
    $etat = $res['etat'];
    $prenom = $res['prenom'];
    $nom = $res['nom'];
} else {
    $login = $_SESSION['user']['login'];
    $email = $_SESSION['user']['email'];
    $role = $_SESSION['user']['role'];
    $etat = $_SESSION['user']['etat'];
    $prenom = $_SESSION['user']['prenom'];
    $nom = $_SESSION['user']['nom'];
}
if (isset($_POST['enregister'])) {
    $mp = isset($_POST['password']) ? test_input($_POST['password']) : "";
    $confirm_mp = isset($_POST['confirm_password']) ? test_input($_POST['confirm_password']) : "";

    if (empty($mp) || empty($confirm_mp)) {
        $warningMessage = "Veuillez sélectionner les deux champs de mot de passe";
    }
    if (strcmp($mp, $confirm_mp) != 0) {
        $warningMessage = "Les deux champs de mot de passe ne sont pas identiques, veuillez réessayer";
    } else {
        $statement = $pdo->prepare("UPDATE utilisateur SET password=? WHERE id_u=?");
        $exec = $statement->execute([$mp, $_SESSION['user']['id_u']]);
        if ($exec) {
            $successMessage = "Le mot de passe a été modifié avec succès";
        } else {
            $warningMessage = "Le changement de mot de passe a échoué, veuillez réessayer";
        }
    }
}



?>
<!DOCTYPE HTML>
<HTML>

<head>
    <meta charset="utf-8">
    <title>Profile</title>
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="../css/monstyle.css">
</head>

<body>
    <?php include("menu.php"); ?>
    <div class="container">

        <?php
        if (!empty($successMessage)) {
            echo "<div class=\"alert alert-success mt-4\">" . $successMessage . "</div>";
        } ?>
        <div class="card mt-3">
            <h5 class="card-header">Coordonnées du profil</h5>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="address" value="<?php echo $login ?>" disabled>
                            <label for="address" class="form-label">Identifiant</label>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-floating">
                            <?php
                            switch ($role) {
                                case 'a':
                                    $profession = "Admin";
                                    break;

                                case 'p':
                                    $profession = "Professeur";
                                    break;

                                case 'e':
                                    $profession = "Etudiant(e)";
                                    break;

                                default:
                                    $profession = "Inconnu";
                                    break;
                            }
                            ?>
                            <input type="text" class="form-control" id="address" value="<?php echo $profession; ?>" disabled>
                            <label for="address" class="form-label">Profession</label>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="address" value="<?php echo $prenom ?>" disabled>
                            <label for="address" class="form-label">Prénom</label>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="nom" value="<?php echo $nom ?>" disabled>
                            <label for="nom" class="form-label">Nom</label>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="address" value="<?php echo $email ?>" disabled>
                            <label for="nom" class="form-label">Adresse</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <h5 class="card-header">Changer le mot de passe</h5>
            <div class="card-body">
                <?php
                if ($warningMessage) {
                    echo "<div class=\"alert alert-warning\">" . $warningMessage . "</div>";
                }
                ?>
                <form method="post" action="profile.php">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="password" name="password" placeholder="" value="" required>
                                <label for="password">Nouveau mot de passe</label>
                            </div>
                        </div>

                        <div class="col-sm-6 ">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="confirm_password" name="confirm_password" placeholder="" value="" required>
                                <label for="confirm_password">Confirmer le mot de passe</label>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary mt-2" name="enregister" type="submit">Enregistrer</button>
                </form>
            </div>
        </div>
    </div>
</body>

</HTML>