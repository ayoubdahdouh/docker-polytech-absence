<?php
require_once('identifier.php');
require_once("connexiondb.php");
error_reporting(E_ALL);
ini_set('display_errors', 'on');

if (isset($_POST["ajouter_justificatif"])) {
    $_SESSION["justificatif"]["id_a"] = intval($_POST["ajouter_justificatif"]);
    if ($_SESSION["user"]["role"] == 'a') {
        $_SESSION["justificatif"]["id_e"] = intval($_POST["id_e"]);
    } else {
        $_SESSION["justificatif"]["id_e"] = $_SESSION["user"]["id_u"];
    }
}
if (isset($_POST["valider_justificatif"])) {

    if (!isset($_FILES) || empty($_FILES)) {
        $errorMessage = "Vueillez drop une justificatif  !";
    } else {
        $target_dir = "../justificatifs/";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (
            $imageFileType == "jpg" ||
            $imageFileType == "png" ||
            $imageFileType == "jpeg" ||
            $imageFileType == "pdf"
        ) {
            if ($_FILES["fileToUpload"]["size"] <= 2000048) {
                $file =  "../justificatifs/" . $_SESSION["justificatif"]["id_a"] . "_" . $_SESSION["justificatif"]["id_e"] . "." . $imageFileType;
                if (!file_exists($file)) {
                    $tmp_name = trim($_FILES["fileToUpload"]["tmp_name"]);
                    if (move_uploaded_file($tmp_name, $file)) {
                        $sql = "SELECT max(id_j) AS mx FROM justificatif";
                        $res = sqlQuery($sql, null);
                        $max = empty($res) ? 1 : $max = $res["mx"] + 1;
                        $sql = "INSERT INTO justificatif(id_j, id_a, id_e, justificatif, status) VALUES (?, ?, ?, ?, ?)";
                        $sql2 = "UPDATE historique SET justificatif=? WHERE id_a=? AND id_e=?";
                        if (
                            sqlInsert($sql, [$max, $_SESSION["justificatif"]["id_a"], $_SESSION["justificatif"]["id_e"], $file, 'e']) &&
                            sqlInsert($sql2, [$max, $_SESSION["justificatif"]["id_a"], $_SESSION["justificatif"]["id_e"]])
                        ) {
                            $sql = "INSERT INTO notification (type, arg1) VALUES (?, ?)";
                            if (sqlInsert($sql, ["justificatif", $max])) {
                                if ($_SESSION["user"]["role"] == 'a') {
                                    $successMessage = "Le justificatif est bien ajouté";;
                                } else {
                                    $successMessage = "Votre justificatif est envoyé au secrétariat";
                                }
                            } else {
                                $errorMessage = "Une erreur s'est produite, veuillez réessayer";
                            }
                        } else {
                            $errorMessage = "Une erreur s'est produite, veuillez réessayer";
                        }
                    } else {
                        $errorMessage = "Une erreur s'est produite, veuillez réessayer";
                    }
                } else {
                    $errorMessage = "Une justification est déjà fournie pour cette absence";
                }
            } else {
                $warningMessage = "La taille maximale autorisée est de 2Mo.";
            }
        } else {
            $warningMessage = "Seuls les fichiers JPG, JPEG, PNG et PDF sont autorisés.";
        }
    }
}
if (!empty($errorMessage)) {
    echo "<div class=\"alert alert-danger\" role=\"alert\">" . $errorMessage . "</div>";
} elseif (!empty($successMessage)) {
    echo "<div class=\"alert alert-success\" role=\"alert\">" . $successMessage . "</div>";
} else {
    if (!empty($warningMessage)) {
        echo "<div class=\"alert alert-warning\" role=\"alert\">" . $warningMessage . "</div>";
    }
?>
    <form action="<?php echo ($_SESSION["user"]["role"] == "a") ? 'gestion_utilisateurs.php' : 'etudiant.php'; ?>" method="post" enctype="multipart/form-data">
        <div class="card">
            <div class="card-header">
                Ajouter un justificatif
            </div>
            <div class="card-body">
                <div>
                    <label for="fileToUpload" class="form-label">Veuillez sélectionner un fichier au format PDF, PNG ou JPEG</label>
                    <input class="form-control" id="fileToUpload" type="file" name="fileToUpload">
                </div>
                <div class="mt-2">
                    <?php
                    if ($_SESSION["user"]["role"] == "a") {
                        echo "<a href=\"gestion_utilisateurs.php\" class=\"btn btn-danger\">Annuler</a>";
                    } else {
                        echo "<a href=\"etudiant.php\" class=\"btn btn-danger\">Annuler</a>";
                    }
                    ?>

                    <button type="submit" name="valider_justificatif" class="btn btn-success">Valider</button>
                </div>

            </div>
        </div>
    </form>

<?php
}
