<?php

require_once("db_service.php");

$warningMessage = "";
$successMessage = "";

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if (isset($_POST['modifier_enregister'])) {
    $email = trim($_POST['email']);
    $role = trim($_POST['role']);
    $prenom = trim($_POST['prenom']);
    $nom = trim($_POST['nom']);

    $password = isset($_POST['password']) ? test_input($_POST['password']) : "";
    $password_confirm = isset($_POST['confirm_password']) ? test_input($_POST['confirm_password']) : "";

    if (
        empty($email) ||
        empty($role) ||
        empty($prenom) ||
        empty($nom)
    ) {
        $errorMessage = "Veuillez remplir les champs suivants : nom, prénom, profession et courriel.";
    } else if (
        strcmp($email, $_SESSION[$s]["email"]) != 0 ||
        strcmp($role, $_SESSION[$s]["role"]) != 0 ||
        strcmp($prenom, $_SESSION[$s]["prenom"]) != 0 ||
        strcmp($nom, $_SESSION[$s]["nom"]) != 0
    ) {
        $sql = "UPDATE utilisateur SET role=?, prenom=?, nom=?, email=? WHERE id_u=?";
        $exec = sqlUpdate($sql, [$role, $prenom, $nom, $email, $_SESSION[$s]["id_u"]]);

        if ($exec) {
            $successMessage = "Les modifications ont été effectuées avec succès.";
            $_POST["modifier"] = $_SESSION[$s]["id_u"];
            unset($_POST["modifier_enregister"]);
        } else {
            $warningMessage = "Les modifications ont échoué, veuillez réessayer!";
        }
    }

    if (!empty($password) || !empty($password_confirm)) {
        if (empty($password) || empty($password_confirm)) {
            $warningMessage = "Veuillez sélectionner les deux champs de mot de passe";
        }
        if (strcmp($password, $password_confirm) != 0) {
            $warningMessage = "Les deux champs de mot de passe ne sont pas identiques, veuillez réessayer";
        } else {
            $statement = $pdo->prepare("UPDATE utilisateur SET password=? WHERE id_u=?");
            $exec = $statement->execute([$password, $_SESSION[$s]['id_u']]);
            if ($exec) {
                $successMessage = "Le mot de passe a été changé avec succès.";
            } else {
                $warningMessage = "Le changement de mot de passe a échoué, veuillez réessayer";
            }
        }
    }
}

if (isset($_POST["modifier"])) {
    $id_u = intval($_POST["modifier"]);

    // $sql = "SELECT id_u, login, email, role, etat, prenom, nom FROM utilisateur WHERE id_u=?";
    $sql = "SELECT id_u, email, role, etat, prenom, nom FROM utilisateur WHERE id_u=?";
    $res = sqlQuery($sql, [$id_u]);

    $_SESSION[$s]["id_u"] = $res['id_u'];
    // $_SESSION[$s]["login"] = $res['login'];
    $_SESSION[$s]["email"] = $res['email'];
    $_SESSION[$s]["role"] = $res['role'];
    $_SESSION[$s]["etat"] = $res['etat'];
    $_SESSION[$s]["prenom"] = $res['prenom'];
    $_SESSION[$s]["nom"] = $res['nom'];
}

$sql = "SELECT DISTINCT role FROM utilisateur";
$liste_profession = sqlQueryAll($sql, null);

if (!empty($errorMessage)) {
    echo "<div class=\"alert alert-danger\">" . $errorMessage . "</div>";
} elseif (!empty($warningMessage)) {
    echo "<div class=\"alert alert-warning\">" . $warningMessage . "</div>";
} elseif (!empty($successMessage)) {
    echo "<div class=\"alert alert-success mt-4\">" . $successMessage . "</div>";
}
?>
<div class="card mt-3">
    <h5 class="card-header">Coordonnées du profil</h5>
    <div class="card-body">
        <form method="post" action="index.php?req=gestionUtilisateurs">
            <div class="row g-3">


                <div class="col-sm-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="identifiant" name="identifiant" value="<?php echo $_SESSION[$s]["id_u"] ?>" disabled>
                        <label for="identifiant" class="form-label">Identifiant</label>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-floating">
                        <select class="form-select" name="role" id="role" aria-label="Floating label select example">
                            <?php
                            foreach ($liste_profession as $prof) {
                                if ($prof["role"] == $_SESSION[$s]["role"]) {
                                    echo "<option value=\"" . $prof["role"] . "\" selected>";
                                } else {
                                    echo "<option value=\"1\">";
                                }
                                switch ($prof["role"]) {
                                    case 'a':
                                        echo "Admin";
                                        break;

                                    case 'p':
                                        echo "Professeur";
                                        break;

                                    case 'e':
                                        echo "Etudiant(e)";
                                        break;

                                    default:
                                        echo  "Inconnu";
                                        break;
                                }
                                echo "</option>";
                            }
                            ?>
                        </select>
                        <label for="role" class="form-label">Profession</label>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo $_SESSION[$s]["prenom"] ?>">
                        <label for="prneom" class="form-label">Prénom</label>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="nom" name="nom" value="<?php echo $_SESSION[$s]["nom"] ?>">
                        <label for="nom" class="form-label">Nom</label>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-floating">
                        <input type="text" class="form-control" name="email" id="email" value="<?php echo $_SESSION[$s]["email"] ?>">
                        <label for="email" class="form-label">Adresse</label>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="password" name="password" placeholder="" value="">
                        <label for="password">Nouveau mot de passe</label>
                    </div>
                </div>

                <div class="col-sm-6 ">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="confirm_password" name="confirm_password" placeholder="" value="">
                        <label for="confirm_password">Confirmer le mot de passe</label>
                    </div>
                </div>

                <div class="col-sm-12 ">
					<a href="index.php?req=gestionUtilisateurs" class="btn btn-danger">Annuler</a>
                    <button class="btn btn-success" name="modifier_enregister" type="submit">Enregistrer</button>
                </div>
            </div>
        </form>
    </div>
</div>