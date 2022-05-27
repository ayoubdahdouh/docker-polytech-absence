<?php

// supprimer une absence
if (isset($_POST["supprimer_absence"])) {
    require_once("u_consulter_supprimer.php");
} elseif (isset($_POST["ajouter_justificatif"])) {
    require_once("u_consulter_ajouter.php");
}
if (empty($errorMessage)) {
    if (isset($_POST["consulter"])) {
        $id_e = intval($_POST['consulter']);

        // $sql = "SELECT t1.id_u, t1.login, t2.groupe, CONCAT(t1.prenom, ' ', t1.nom) AS prenom_nom FROM utilisateur t1 JOIN " .
        $sql = "SELECT t1.id_u, t2.groupe, CONCAT(t1.prenom, ' ', t1.nom) AS prenom_nom FROM utilisateur t1 JOIN " .
            "etudiant t2 ON t1.id_u = t2.id_e WHERE t1.role='e' AND t1.id_u=?";
        $etudiant = sqlQuery($sql, [$id_e]);

        if (empty($etudiant)) {
            $errorMessage = "Cet identifiant n'existe pas.";
        } else {
            $sql = "SELECT DISTINCT t1.id_c, t1.nom FROM cours t1 JOIN ametice t2 ON t1.id_c=t2.id_c WHERE t2.id_e=?";
            $cours = sqlQueryAll($sql, [$etudiant['id_u']]);

            if (empty($cours)) {
                $errorMessage = "L'étudiant avec cet identifiant n'est inscrit à aucun cours..";
            } else {
                $_SESSION[$s]['re']["cours"] = $cours;
                $_SESSION[$s]['re']["etudiant"] = $etudiant;
                $id_c = 0;
            }
        }
    } elseif (isset($_POST["consulter_autre"])) {
        $id_c = intval($_POST["index"]);
    }

    if (empty($errorMessage)) {
        if ($id_c == 0) {
            $tmp = [];
            foreach ($_SESSION[$s]['re']["cours"] as $i => $c) {
                $tmp[$i] = $c["id_c"];
            }

            // tous les cours
            $sql = "SELECT DISTINCT id_n FROM enseignement WHERE id_c IN (" . implode(',', $tmp) . ")  AND groupe IN (?,0)";
            $res = sqlQueryAll($sql, [$_SESSION[$s]['re']["etudiant"]["groupe"]]);
        } else {
            $sql = "SELECT DISTINCT id_n FROM enseignement WHERE id_c=? AND groupe IN (?,0)";
            $res = sqlQueryAll($sql, [$id_c, $_SESSION[$s]['re']["etudiant"]["groupe"]]);
        }

        $liste_id_n = [];
        foreach ($res as $i => $r) {
            $liste_id_n[$i] = $r["id_n"];
        }

        // recherche toutes les absences
        $sql = "SELECT T1.id_a, T1.date_heure,T1.nom, T1.type, T2.id_e, T2.justificatif, T2.status FROM " .
            "(SELECT t1.id_a, t1.date_heure, t2.type, t3.nom FROM absence t1 JOIN enseignement t2 ON t1.id_n=t2.id_n JOIN cours t3 ON t2.id_c=t3.id_c WHERE t1.id_n IN (" . implode(',', $liste_id_n) . ")) AS T1 " .
            "JOIN " .
            "(SELECT t1.id_a, t2.id_e, t2.justificatif, t3.status FROM absence t1 JOIN historique t2 on t1.id_a=t2.id_a LEFT JOIN justificatif t3 ON t3.id_j=t2.justificatif WHERE t1.id_n IN (" . implode(',', $liste_id_n) . ") AND t2.id_e=?) AS T2 " .
            "ON T1.id_a=T2.id_a ORDER BY T1.date_heure;";

        $matrix_abs = sqlQueryAll($sql, [$_SESSION[$s]['re']["etudiant"]["id_u"]]);

        if (count($matrix_abs) == 0) {
            $warningMessage = "Aucun enregistrement trouvé";
        }
    }
}

?>
<div class="card mt-3">
    <h5 class="card-header">Résultat de recherche</h5>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-sm-6">
                <div class="form-floating">
                    <!-- <input type="text" class="form-control" id="identifiant" name="identifiant" placeholder="Identifiant" value="<?php // echo $_SESSION[$s]['re']["etudiant"]["login"]; ?>" readonly="readonly"> -->
                    <input type="text" class="form-control" id="identifiant" name="identifiant" placeholder="Identifiant" value="<?php echo $_SESSION[$s]['re']["etudiant"]["id_u"]; ?>" readonly="readonly">
                    <label for="identifiant">Identifiant</label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-floating">
                    <input type="text" class="form-control" id="prenom_nom" name="prenom_nom" placeholder="Prénom Nom" value="<?php echo $_SESSION[$s]['re']["etudiant"]["prenom_nom"]; ?>" readonly="readonly">
                    <label for="prenom_nom">Prénom Nom</label>
                </div>
            </div>
        </div>
        <form method="POST" action="index.php?req=gestionUtilisateurs">
            <div class="mt-3">
                <div class="form-floating">
                    <select name="index" class="form-select" id="index" required>
                        <option value="0"> -- Tous les cours -- </option>
                        <?php
                        foreach ($_SESSION[$s]['re']["cours"] as $cours) {
                            if ($cours["id_c"] == $id_c) {
                                echo "<option value=\"" . $cours["id_c"] . "\" selected>" . $cours['nom'] . "</option>\n";
                            } else {
                                echo "<option value=\"" . $cours["id_c"] . "\">" . $cours['nom'] . "</option>\n";
                            }
                        }
                        ?>
                    </select>
                    <label for="index">Cours</label>
                </div>
                <div class="mt-2">
                    <button class="btn btn-primary" name="consulter_autre" type="submit">Consulter</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php
if (!empty($successMessage)) {
    echo "<div class=\"alert alert-success mt-4\">" . $successMessage . "</div>";
} elseif (!empty($errorMessage)) {
    echo "<div class=\"alert alert-danger mt-4\">" . $errorMessage . "</div>";
} elseif (!empty($warningMessage)) {
    echo "<div class=\"alert alert-warning mt-4\">" . $warningMessage . "</div>";
} else {
?>
    <table class="table table-hover table-bordered mt-4">
        <thead class="table-light">
            <tr>
                <th>Date</th>
                <th>Type</th>
                <?php
                if ($id_c == 0) {
                    echo "<th>Nom</th>";
                }
                ?>
                <th>Justificatif</th>
                <th class="text-center">Operations</th>
            </tr>
        </thead>
        <tbody>
            <form method="POST" action="index.php">
                <input type="hidden" name="id_e" value="<?php echo  $_SESSION[$s]['re']["etudiant"]["id_u"]; ?>">
                <?php
                foreach ($matrix_abs as $abs) {
                    // Nom et prénom
                    echo "<tr><td>";
                    echo $abs['date_heure'];

                    // Absent(e)
                    echo "</td><td>";
                    switch ($abs['type']) {
                        case 'cm':
                            echo "CM";
                            break;
                        case 'td':
                            echo "TD";
                            break;
                        case 'tp':
                            echo "TP";
                            break;

                        default:
                            echo "AUTRE";
                            break;
                    }
                    // nom du cours
                    echo "</td><td>";
                    if ($id_c == 0) {
                        echo $abs["nom"];
                    }
                    // Justificatif
                    echo "</td><td>";
                    if ($abs["justificatif"]) {
                        if ($abs["status"] == "e") {
                            echo "EN COURS";
                        } elseif ($abs["status"] == "r") {
                            echo "REFUS";
                        } else  {
                            echo "OUI";
                        }
                    } else {
                        echo "NON";
                    }
                ?>
                    </td>
                    <td class="text-center">
                        <?php

                        if (!$abs["justificatif"] || $abs["status"] == "r" ) {
                        ?>
                            <button type="submit" name="ajouter_justificatif" value="<?php echo $abs['id_a']; ?>" class="btn btn-success btn-sm">Ajouter justificatif</button>
                        <?php
                        }
                        ?>
                        <button type="submit" name="supprimer_absence" value="<?php echo $abs['id_a']; ?>" class="btn btn-danger btn-sm">Supprimer absence</button>
                    </td>
                    </tr>
                <?php
                }
                ?>
            </form>
        </tbody>
    </table>
<?php
}
