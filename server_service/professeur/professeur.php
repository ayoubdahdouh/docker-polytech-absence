<?php
$ajoute_absense = false;

$sql = "select distinct t2.id_c, t2.nom from enseignement t1 inner join cours t2 on t2.id_c=t1.id_c where t1.id_p=?";
$list_cours = sqlQueryAll($sql, [$_SESSION[$s]['user']['id_u']]);

$sql = "select distinct type from enseignement where id_p=?";
$list_types = sqlQueryAll($sql, [$_SESSION[$s]['user']['id_u']]);

$sql = "select distinct groupe from enseignement where id_p=?";
$list_groupes = sqlQueryAll($sql, [$_SESSION[$s]['user']['id_u']]);

$errorMessage = "";

if (isset($_POST['rechercher']) || isset($_POST['ajouter'])) {
    $cours_id_c = isset($_POST['cours']) ? intval($_POST['cours']) : 0;
    $type  = isset($_POST['type']) ? trim($_POST['type']) : "";
    $groupe  = isset($_POST['groupe']) ? intval($_POST['groupe']) : 0;

    if ($type == 'cm') {
        $groupe = 0;
    }
    if ($cours_id_c <= 0 || empty($type) || $groupe < 0) {
        $errorMessage = "Veuillez sélectionner le cours, le type et le groupe svp !";
    }
    // if (isset($_POST['rechercher'])) {
    //     if () {
            
    //     }
    //     $errorMessage = "Veuillez sélectionner un cours et son type de séance";
    // } elseif (isset($_POST['ajouter'])) {
        
    // }
}

?>

<form method="POST" action="index.php?req=rechercherCours">
    <div class="row py-lg-2">
        <div class="col-lg-12 col-md-8 mx-auto">
            <?php
            if (strlen($errorMessage) != 0) {
                echo "<div class=\"alert alert-danger\">" . $errorMessage . "</div>";
            }
            ?>
            <div class="row g-3">
                <div class="col-md">
                    <div class="form-floating">
                        <select name="cours" class="form-select" id="cours" required>
                            <option disabled selected>--- Vide ---</option>
                            <?php
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
                    <button class="btn btn-primary" name="rechercher" type="submit">Rechercher</button>
                    <button class="btn btn-primary" name="ajouter" type="submit">Ajouter les absences</button>
                </div>
            </div>
        </div>
    </div>
</form>