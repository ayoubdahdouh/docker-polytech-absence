<?php
$is_enregister = isset($_POST['mise_a_jour']);

if ($is_enregister) {
	require_once('professeur/mise_a_jour.php');
} else {
	$cours_id_c = isset($_POST['cours_id_c']) ? intval($_POST['cours_id_c']) : "";
	$cours_nom = isset($_POST['cours_nom']) ? trim($_POST['cours_nom']) : "";
	$type  = isset($_POST['type']) ? trim($_POST['type']) : "";
	$groupe  = isset($_POST['groupe']) ? intval($_POST['groupe']) : "";
	$id_a = isset($_POST['index']) ? intval($_POST['index']) : "";

	$sql = "select date_heure from absence where id_a=?";
	$date_heure = sqlQuery($sql, [$id_a]);
	$res = explode(" ", $date_heure["date_heure"]);
	$date = $res[0];
	$res = explode(":", $res[1]);
	$heure = $res[0];
	$minutes = $res[1];
}
if (!$is_enregister) {

	if ($is_enregister) {
		if ($type == 'cm') {
			$sql = "select t1.id_u as id_e, CONCAT(t1.nom, \" \", t1.prenom) as prenom_nom_nom from utilisateur t1 " .
				"join ametice t2 on t1.id_u=t2.id_e where t2.id_c=?";
			$liste_etudiants = sqlQueryAll($sql, [$cours_id_c]);
		} else {
			$sql = "select t1.id_u as id_e, CONCAT(t1.nom, \" \", t1.prenom) as prenom_nom_nom from utilisateur t1 join etudiant t2 on t2.id_e=t1.id_u " .
				"join ametice t3 on t2.id_e=t3.id_e where t3.id_c=? and t2.groupe=?";
			$liste_etudiants = sqlQueryAll($sql, [$cours_id_c, $groupe]);
		}
		echo "<div class=\"alert alert-warning\">" . $warningMessage . "</div>";
	} else {
		if ($type == "cm") {
			$sql = "select etu.id_u, etu.prenom_nom , abs.id_a from" .
				"(select t1.id_u, CONCAT(t1.prenom,\" \",t1.nom) as prenom_nom from utilisateur t1 join etudiant t2 on t1.id_u=t2.id_e join ametice t3 on t2.id_e=t3.id_e " .
				"where t3.id_c=?) as etu left join " .
				"(select t4.id_e, t4.id_a from etudiant t5 join historique t4 on t5.id_e=t4.id_e where t4.id_a=?) as abs " .
				"on etu.id_u=abs.id_e";

			$matrix_abs = sqlQueryAll($sql, [$cours_id_c, $id_a]);
		} else {
			$sql = "select etu.id_u, etu.prenom_nom , abs.id_a from" .
				"(select t1.id_u, CONCAT(t1.prenom,\" \",t1.nom) as prenom_nom from utilisateur t1 join etudiant t2 on t1.id_u=t2.id_e join ametice t3 on t2.id_e=t3.id_e " .
				"where t3.id_c=? and t2.groupe=?) as etu left join " .
				"(select t4.id_e, t4.id_a from etudiant t5 join historique t4 on t5.id_e=t4.id_e where t4.id_a=?) as abs " .
				"on etu.id_u=abs.id_e";

			$matrix_abs = sqlQueryAll($sql, [$cours_id_c, $groupe, $id_a]);
		}
	}
?>
	<div class="card mt-3">
		<h5 class="card-header">Modifier l'absence</h5>
		<div class="card-body">
			<form method="POST" action="rechercher_cours.php">
				<div class="row g-3">
					<div class="col-sm-4">
						<div class="form-floating">
							<input type="text" name="id_a" value="<?php echo $id_a; ?>" hidden>
							<input type="text" name="cours_id_c" value="<?php echo $cours_id_c; ?>" hidden>
							<input type="text" class="form-control" id="cours" name="cours_nom" placeholder="Cours" value="<?php echo $cours_nom; ?>" readonly="readonly">
							<label for="cours">Cours</label>
						</div>
					</div>

					<div class="col-sm-4">
						<div class="form-floating">
							<input type="text" class="form-control" id="type" name="type" placeholder="Type" value="<?php echo $type; ?>" readonly="readonly">
							<label for="type">Type</label>
						</div>
					</div>

					<div class="col-sm-4">
						<div class="form-floating">
							<input type="text" class="form-control" id="groupe" name="groupe" placeholder="Groupe" value="<?php if ($groupe == 0) {
																																echo "Tous les groupes";
																															} else {
																																echo $groupe;
																															} ?>" readonly="readonly">
							<label for="groupe">Groupe</label>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-floating">
							<input type="text" id="date" class="form-control" name="date" placeholder="Date" value="<?php echo $date; ?>" readonly="readonly">
							<label for="date">Date</label>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-floating">
							<input type="text" id="heure" class="form-control" name="heure" placeholder="Heure" value="<?php echo $heure; ?>" readonly="readonly">
							<label for="heure">Heure</label>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-floating">
							<input type="text" id="minutes" class="form-control" name="minutes" placeholder="Minutes" value="<?php echo $minutes; ?>" readonly="readonly">
							<label for="minutes">Minutes</label>
						</div>
					</div>
				</div>

				<table class="table table-hover table-bordered mt-4">
					<thead class="table-light">
						<tr>
							<th>Nom et prénom</th>
							<th>Cocher si absent</th>
						</tr>
					</thead>

					<tbody>
						<?php
						if (!$is_enregister) {
							foreach ($matrix_abs as $abs) { ?>
								<tr>
									<?php
									echo "<td class=\"col-md-10\">" . $abs['prenom_nom'] . " </td>";
									echo "<td class=\"col-md-2\"><input type=\"checkbox\" class=\"form-check-input\" name=\"absents[]\" value=\"" . $abs['id_u'] . "\"";
									if ($abs['id_a']) {
										echo "checked";
									}
									echo "/></td>";
									?>
								</tr>
							<?PHP
							}
						}?>
					</tbody>
				</table>

				<div class="mt-2">
					<a href="rechercher_cours.php" class="btn btn-secondary">Annuler</a>
					<button class="btn btn-primary" name="mise_a_jour" type="submit">Mise à jour</button>
				</div>
			</form>

		</div>
	</div>
<?php
} elseif (!empty($warningMessage)) {
	echo "<div class=\"alert alert-danger\">" . $errorMessage . "</div>";
} elseif (!empty($successMessage)) {
	echo "<div class=\"alert alert-success\">" . $successMessage . "</div>";
}
else {
	echo "<div class=\"alert alert-info\">Something went wrong !</div>";
}

?>