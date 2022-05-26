<?php
$warningMessage = "";

if (isset($_POST["consulter"])) {
	$id_a = intval($_POST["index"]);
} elseif (!isset($_POST["consulter"])) {

	// id_n
	$sql = "select id_n from enseignement where id_c=? and type=? and groupe=?";
	$res = sqlQuery($sql, [$_SESSION["rc"]["id_c"], $_SESSION["rc"]["type"], $_SESSION["rc"]["groupe"]]);
	$id_n = $res['id_n'];

	// recherche toutes les absences
	$sql = "SELECT id_a, date_heure FROM absence WHERE id_n=? ORDER BY id_a DESC";
	$res = sqlQueryAll($sql, [$id_n]);
	if (count($res) != 0) {
		$_SESSION["rc"]["liste_id_a"] = $res;
		$id_a = $_SESSION["rc"]["liste_id_a"][0]["id_a"];
	} else {
		$warningMessage = "Aucun enregistrement trouvé";
	}
}

if (!empty($warningMessage)) {
	echo "<div class=\"alert alert-warning\">" . $warningMessage . "</div>";
} else {
	if ($_SESSION["rc"]["type"] == "cm") {
		$sql = "select etu.id_u, etu.prenom_nom , abs.id_a, abs.justificatif, abs.status from" .
			"(select t1.id_u, CONCAT(t1.prenom,\" \",t1.nom) as prenom_nom from utilisateur t1 join etudiant t2 on t1.id_u=t2.id_e join ametice t3 on t2.id_e=t3.id_e " .
			"where t3.id_c=?) as etu left join " .
			"(select t4.id_e, t4.id_a, t4.justificatif, t6.status from etudiant t5 join historique t4 on t5.id_e=t4.id_e left join justificatif t6 on t6.id_j=t4.justificatif where t4.id_a=?) as abs " .
			"on etu.id_u=abs.id_e";

		$matrix_abs = sqlQueryAll($sql, [$_SESSION["rc"]["id_c"], $id_a]);
	} else {
		$sql = "select etu.id_u, etu.prenom_nom , abs.id_a, abs.justificatif, abs.status from" .
			"(select t1.id_u, CONCAT(t1.prenom,\" \",t1.nom) as prenom_nom from utilisateur t1 join etudiant t2 on t1.id_u=t2.id_e join ametice t3 on t2.id_e=t3.id_e " .
			"where t3.id_c=? and t2.groupe=?) as etu left join " .
			"(select t4.id_e, t4.id_a, t4.justificatif, t6.status from etudiant t5 join historique t4 on t5.id_e=t4.id_e left join justificatif t6 on t6.id_j=t4.justificatif where t4.id_a=?) as abs " .
			"on etu.id_u=abs.id_e";

		$matrix_abs = sqlQueryAll($sql, [$_SESSION["rc"]["id_c"], $_SESSION["rc"]["groupe"], $id_a]);
	}

?>

	<div class="card mt-3">
		<h5 class="card-header">Résultat de recherche</h5>
		<div class="card-body">

			<div class="row g-3">
				<div class="col-sm-6">
					<div class="form-floating">
						<input type="text" class="form-control" id="filiere" name="filiere_nom" placeholder="Filière" value="<?php echo $_SESSION["rc"]["filiere_nom"]; ?>" readonly="readonly">
						<label for="filiere">Filière</label>
					</div>
				</div>

				<div class="col-sm-6">
					<div class="form-floating">
						<input type="text" class="form-control" id="annee" name="annee" placeholder="Année" value="<?php echo $_SESSION["rc"]["annee"]; ?>" readonly="readonly">
						<label for="annee">Année</label>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-floating">
						<input type="text" name="cours_id_c" value="<?php echo $_SESSION["rc"]["id_c"]; ?>" hidden>
						<input type="text" class="form-control" id="cours" name="cours_nom" placeholder="Cours" value="<?php echo $_SESSION["rc"]["cours_nom"]; ?>" readonly="readonly">
						<label for="cours">Cours</label>
					</div>
				</div>

				<div class="col-sm-4">
					<div class="form-floating">
						<input type="text" class="form-control" id="type" name="type" placeholder="Type" value="<?php echo $_SESSION["rc"]["type"]; ?>" readonly="readonly">
						<label for="type">Type</label>
					</div>
				</div>

				<div class="col-sm-4">
					<div class="form-floating">
						<input type="text" class="form-control" id="groupe" name="groupe" placeholder="Groupe" value="<?php if ($_SESSION["rc"]["groupe"] == 0) {
																															echo "Tous les groupes";
																														} else {
																															echo $_SESSION["rc"]["groupe"];
																														} ?>" readonly="readonly">
						<label for="groupe">Groupe</label>
					</div>
				</div>

				<div class="col-sm-12">
					<form method="POST" action="rechercher_cours.php">
						<div class="form-floating">
							<select name="index" class="form-select" id="index" required>
								<?php
								foreach ($_SESSION["rc"]["liste_id_a"] as $abs) {
									if ($abs["id_a"] ==	$id_a) {
										echo "<option value=\"" . $abs["id_a"] . "\" selected>" . $abs['date_heure'] . "</option>\n";
									} else {
										echo "<option value=\"" . $abs["id_a"] . "\">" . $abs['date_heure'] . "</option>\n";
									}
								}
								?>
							</select>
							<label for="index">Historique</label>
						</div>
						<div class="mt-2">
							<button class="btn btn-danger" name="modifier" type="submit">Modifier</button>
							<button class="btn btn-primary" name="consulter" type="submit">Consulter</button>
						</div>
					</form>
				</div>

			</div>
			<table class="table table-hover table-bordered mt-4">
				<thead class="table-light">
					<tr>
						<th>Nom et prénom</th>
						<th>Absent(e)</th>
						<th>Justificatif</th>
					</tr>
				</thead>
				<tbody>

					<?php
					foreach ($matrix_abs as $abs) {
						// Nom et prénom
						echo "<tr><td>";
						echo $abs['prenom_nom'];

						// Absent(e)
						echo "</td><td>";
						if ($abs["id_a"]) {
							echo "OUI";
						}

						// Justificatif
						echo "</td><td>";
						if ($abs["id_a"]) {
							if ($abs["justificatif"]) {
								if ($abs["status"] == "e") {
									echo "EN COURS";
								} elseif ($abs["status"] == "r") {
									echo "REFUSE";
								} else  {
									echo "OUI";
								}
							} else {
								echo "NON";
							}
						}

						echo "</td></tr>\n";
					} ?>
				</tbody>
			</table>

		</div>
	</div>
<?php
} ?>