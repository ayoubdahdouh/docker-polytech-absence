<?php
$sql = "SELECT DISTINCT * FROM filiere;";
$filieres = sqlQueryAll($sql, null);

$sql = "SELECT DISTINCT annee FROM cours ORDER BY annee ASC";
$annees = sqlQueryAll($sql, null);

$errorMessage = "";

if (isset($_POST["filtrer"])) {
    $id_f = isset($_POST['id_f']) ? intval($_POST['id_f']) : 0;
    $annee = isset($_POST['annee']) ? intval($_POST['annee']) : 0;

    $mn = $annees[0]["annee"];
    $mx = $annees[count($annees) - 1]["annee"];
    if (empty($id_f) || $annee < $mn || $annee > $mx) {
        $errorMessage = "Veuillez sélectionner la filière et l'année.";
    } else {
        $_SESSION["rc"]["id_f"] = $id_f;
        $_SESSION["rc"]["annee"] = $annee;

        // nom de filiere
        $sql = "select nom from filiere where id_f=?";
        $res = sqlQuery($sql, [$_SESSION["rc"]["id_f"]]);
        $_SESSION["rc"]["filiere_nom"]  = $res['nom'];

        $sql = "SELECT id_c, nom FROM cours WHERE id_f=? AND annee=?";
        $list_cours = sqlQueryAll($sql, [$id_f, $annee]);
        $_SESSION["rc"]["list_cours"] = $list_cours;

        $sql = "SELECT DISTINCT type FROM enseignement WHERE id_c IN (SELECT id_c FROM cours WHERE id_f=? AND annee=?)";
        $list_types = sqlQueryAll($sql, [$id_f, $annee]);
        $_SESSION["rc"]["list_types"] = $list_types;

        $sql = "SELECT DISTINCT groupe FROM enseignement WHERE id_c IN (SELECT id_c FROM cours WHERE id_f=? AND annee=?)";
        $list_groupes = sqlQueryAll($sql, [$id_f, $annee]);
        $_SESSION["rc"]["list_groupes"] = $list_groupes;
    }
} elseif (isset($_POST['rechercher']) || isset($_POST['ajouter'])) {
    $id_c = isset($_POST['cours']) ? intval($_POST['cours']) : 0;
    $type  = isset($_POST['type']) ? trim($_POST['type']) : "";
    $groupe  = isset($_POST['groupe']) ? intval($_POST['groupe']) : 0;

    if ($type == 'cm') {
        $groupe = 0;
    }
    if ($id_c <= 0 || empty($type) || $groupe < 0) {
        $errorMessage = "Veuillez sélectionner le cours, le type et le groupe";
    } else {
        $_SESSION["rc"]["id_c"] = $id_c;
        $_SESSION["rc"]["type"] = $type;
        $_SESSION["rc"]["groupe"] = $groupe;



        $sql = "select nom from cours where id_c=?";
        $res = sqlQuery($sql, [$_SESSION["rc"]["id_c"]]);
        $_SESSION["rc"]["cours_nom"] = $res['nom'];
    }
}
?>

<form method="POST" action="rechercher_cours.php">
    <div class="row py-lg-2">
        <div class="col-lg-12 col-md-8 mx-auto">
            <?php
            if (!empty($errorMessage)) {
                echo "<div class=\"alert alert-danger\">" . $errorMessage . "</div>";
            }
            if (
                (!isset($_POST["filtrer"]) &&
                    !isset($_POST["rechercher"]) &&
                    !isset($_POST["ajouter"]) &&
                    !isset($_POST["enregistrer"])) ||
                (isset($_POST["filtrer"]) && !empty($errorMessage))
            ) {
            ?>
                <div class="row g-3 mb-3">
                    <div class="col-md">
                        <div class="form-floating">
                            <select name="id_f" class="form-select" id="id_f" required>
                                <option disabled selected>--- Vide ---</option>
                                <?php
                                foreach ($filieres as $fil) {
                                    echo "<option value=\"" . $fil['id_f'] . "\">" . $fil['nom'] . "</option>";
                                }
                                ?>
                            </select>
                            <label for="id_f">Filière</label>
                        </div>
                    </div>

                    <div class="col-md">
                        <div class="form-floating">
                            <select name="annee" class="form-select" id="annee" required>
                                <option disabled selected>--- Vide ---</option>

                                <?php
                                foreach ($annees as $an) {
                                    echo "<option value=\"" . $an['annee'] . "\">" . $an['annee'] . "</option>";
                                }
                                ?>
                            </select>
                            <label for="annee">Année</label>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button class="btn btn-primary" name="filtrer" type="submit">Rechercher Cours</button>
                    </div>
                </div>
            <?php
            } else {
            ?>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="id_f" class="form-select" id="id_f" required>
                                <option disabled>--- Vide ---</option>
                                <?php
                                foreach ($filieres as $fil) {
                                    if ($_SESSION["rc"]["id_f"] == $fil['id_f']) {
                                        echo "<option value=\"" . $fil['id_f'] . "\" selected>" . $fil['nom'] . "</option>";
                                    } else {
                                        echo "<option value=\"" . $fil['id_f'] . "\">" . $fil['nom'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                            <label for="id_f">Filière</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="annee" class="form-select" id="annee" required>
                                <option disabled>--- Vide ---</option>

                                <?php
                                foreach ($annees as $an) {
                                    if ($_SESSION["rc"]["annee"] == $an['annee']) {
                                        echo "<option value=\"" . $an['annee'] . "\" selected>" . $an['annee'] . "</option>";
                                    } else {
                                        echo "<option value=\"" . $an['annee'] . "\">" . $an['annee'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                            <label for="annee">Année</label>
                        </div>
                    </div>
                    <div class="col-md">
                        <div class="form-floating">
                            <select name="cours" class="form-select" id="cours" required>
                                <option disabled selected>--- Vide ---</option>
                                <?php
                                $list_cours = $_SESSION["rc"]["list_cours"];
                                for ($i = 0; $i < count($list_cours); $i++) {
                                    echo "<option value=\"" . $list_cours[$i]['id_c'] . "\">" . $list_cours[$i]['nom'] . "</option>";
                                }
                                ?>
                            </select>
                            <label for="cours">Choisir un cours</label>
                        </div>
                    </div>

                    <div class="col-md">
                        <div class="form-floating">
                            <select name="type" class="form-select" id="type" required>
                                <option disabled selected>--- Vide ---</option>
                                <?php
                                $list_types = $_SESSION["rc"]["list_types"];
                                for ($i = 0; $i < count($list_types); $i++) {
                                    echo "<option value=\"" . $list_types[$i]['type'] . "\">" . $list_types[$i]['type'] . "</option>";
                                }
                                ?>
                            </select>
                            <label for="type">Choisir un type</label>
                        </div>
                    </div>

                    <div class="col-md">
                        <div class="form-floating">
                            <select name="groupe" class="form-select" id="groupe">
                                <?php
                                $list_groupes = $_SESSION["rc"]["list_groupes"];
                                for ($i = 0; $i < count($list_groupes); $i++) {
                                    if ($list_groupes[$i]['groupe'] == 0) {
                                        echo "<option value=\"0\" selected>Tous les groupes</option>";
                                    } else {
                                        echo "<option value=\"" . $list_groupes[$i]['groupe'] . "\">" . $list_groupes[$i]['groupe'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                            <label for="groupe">Choisir un groupe</label>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button class="btn btn-primary" name="rechercher" type="submit">Rechercher les absences</button>
                        <button class="btn btn-primary" name="ajouter" type="submit">Ajouter les absences</button>
                        <button class="btn btn-danger" name="filtrer" type="submit">Modifier la recherche</button>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
</form>