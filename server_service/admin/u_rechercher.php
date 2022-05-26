<?php

if (isset($_POST["change"])) {
    $id_u = intval($_POST["change"]);

    $sql = "SELECT etat FROM utilisateur WHERE id_u=?";
    $etat = sqlQuery($sql,[ $id_u]);

    if ($etat["etat"] == 0) {
        $sql = "UPDATE utilisateur SET etat=1 WHERE id_u=?";
    } else {
        $sql = "UPDATE utilisateur SET etat=0 WHERE id_u=?";
    }
    $exec = sqlUpdate($sql, [$id_u]);

    if ($exec) {
        $successMessage = "Les modifications ont été effectuées avec succès !";
    } else {
        $errorMessage = "Les modifications ont échoué, veuillez réessayer !";
    }
} else {
    $identifiant = isset($_POST["identifiant"]) ? trim($_POST["identifiant"]) : "";
    $prenom = isset($_POST["prenom"]) ? trim($_POST["prenom"]) : "";
    $nom = isset($_POST["nom"]) ? trim($_POST["nom"]) : "";

    if (empty($identifiant) && empty($prenom) && empty($nom)) {
        $errorMessage = "Veuillez sélectionner un identifiant, un prénom ou un nom svp !";
    }

    include_once("u_rechercher_utilisateur.php");
}
if (!empty($errorMessage)) {
    echo "<div class=\"alert alert-danger mt-4\">" . $errorMessage . "</div>";
} elseif (!empty($successMessage)) {
    echo "<div class=\"alert alert-success mt-4\">" . $successMessage . "</div>";
} else {
?>

    <form method="POST" action="gestion_utilisateurs.php">
        <table class="table table-hover table-bordered mt-4">
            <thead class="table-light">
                <tr>
                    <th>Identifiant</th>
                    <th>Prénom et Nom</th>
                    <th>Profession</th>
                    <th>Status</th>
                    <th class="text-center">Opérations</th>
                </tr>
            </thead>
            <tbody>

                <?php
                foreach ($liste_etudiant as $etudiant) {
                    echo "<tr><td>";
                    echo $etudiant['login'];
                    echo "</td><td>";
                    echo $etudiant['prenom_nom'];
                    echo "</td><td>";
                    switch ($etudiant['role']) {
                        case 'p':
                            echo "Professeur";
                            break;
                        case 'a':
                            echo "Admin";
                            break;
                        case 'e':
                            echo "Etudiant(e)";
                            break;
                    }
                    echo "</td><td>";
                    if ($etudiant['etat']) {
                        echo "Active";
                    } else {
                        echo "Desactive";
                    }
                ?>
                    </td>
                    <td class="text-center">
                        <?php
                        if ($etudiant['role'] == 'e') {
                            echo "<button type=\"submit\" name=\"consulter\" value=\"" . $etudiant['id_u'] . "\" class=\"btn btn-primary btn-sm\">Consulter absences</button>";
                        }
                        ?>
                        <button type="submit" name="voir" value="<?php echo $etudiant['id_u']; ?>" class="btn btn-success btn-sm">Voir</button>
                        <button type="submit" name="modifier" value="<?php echo $etudiant['id_u']; ?>" class="btn btn-warning btn-sm">Modifier</button>
                        <?php
                        if ($etudiant['etat']) {
                            echo "<button type=\"submit\" name=\"change\" value=\"" . $etudiant['id_u'] . "\" class=\"btn btn-secondary btn-sm\">Desactiver</button>";
                        } else {
                            echo "<button type=\"submit\" name=\"change\" value=\"" . $etudiant['id_u'] . "\" class=\"btn btn-success btn-sm\">Activer</button>";
                        }
                        ?>
                        <button type="submit" name="supprimer" value="<?php echo $etudiant['id_u']; ?>" class="btn btn-danger btn-sm">Supprimer</button>
                    </td>
                    </tr>
                <?php
                } ?>
            </tbody>
        </table>
        
    </form>

<?php
}
