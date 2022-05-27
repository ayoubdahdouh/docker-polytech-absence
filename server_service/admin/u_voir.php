<?php

require_once("db_service.php");

if (isset($_POST["voir"])) {
    $id_u = intval($_POST["voir"]);
    // $sql = "SELECT id_u, login, email, role, etat, prenom, nom FROM utilisateur WHERE id_u=?";
    $sql = "SELECT id_u, email, role, etat, prenom, nom FROM utilisateur WHERE id_u=?";
    $res = sqlQuery($sql, [$id_u]);

    $login = $res['id_u'];
    $email = $res['email'];
    $role = $res['role'];
    $etat = $res['etat'];
    $prenom = $res['prenom'];
    $nom = $res['nom'];
} else {
    $login = $_SESSION[$s]['user']['id_u'];
    $email = $_SESSION[$s]['user']['email'];
    $role = $_SESSION[$s]['user']['role'];
    $etat = $_SESSION[$s]['user']['etat'];
    $prenom = $_SESSION[$s]['user']['prenom'];
    $nom = $_SESSION[$s]['user']['nom'];
}
?>

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
            <div class="col-sm-12">
                <div class="form-floating">
                    <input type="text" class="form-control" id="address" value="<?php echo $email ?>" disabled>
                    <label for="nom" class="form-label">Adresse</label>
                </div>
            </div>
        </div>
    </div>
</div>
