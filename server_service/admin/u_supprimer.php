<?php
if (isset($_POST["supprimer_confirmer"])) {
    $id_u = intval($_POST["supprimer_confirmer"]);
    $sql = "DELETE * utilisateur WHERE id_u=?;";
    sqlDelete($sql, [$id_u]);
    $sql = "DELETE * ametice WHERE id_u=?;";
    sqlDelete($sql, [$id_u]);
    $sql = "DELETE * historique WHERE id_u=?;";
    // sqlDelete($sql, [$id_u]);
    // $sql = "SELECT * FROM justification WHERE ";
    // $sql = "DELETE * justificatif WHERE id_e=?;";
    // sqlDelete($sql, [$id_u]);

}
$id_u = intval($_POST["supprimer"]);

$sql = "SELECT prenom, nom FROM utilisateur WHERE id_u=?";
$usr = sqlQuery($sql, [$id_u]);
?>
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Supprimer Compte</h5>
        <p class="card-text">vous voulez vraiment supprimer le profile de <b><?php echo $usr["prenom"] . " " . $usr["nom"]; ?> </b>?</p>

        <a href="gestion_utilisateurs.php" class="btn btn-secondary">Annuler</a>
        <button type="submit" name="supprimer_confirmer" value="<?php echo $usr['id_u']; ?>" class="btn btn-danger">Supprimer</button>

    </div>
</div>