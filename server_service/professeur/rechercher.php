<?php
$warningMessage = "";

if (isset($_POST["index"])) {
	$id_a = intval($_POST["index"]);
} else {
	$_SESSION[$s]["absence"]["id_c"] = $cours_id_c;
	$_SESSION[$s]["absence"]["type"] = $type;
	$_SESSION[$s]["absence"]["groupe"] = $groupe;

	$sql = "select nom from cours where id_c=?";
	$res = sqlQuery($sql, [$cours_id_c]);
	$cours_nom  = $res['nom'];

	$_SESSION[$s]["absence"]["cours_nom"] = $cours_nom;

	// id_n
	$sql = "select id_n from enseignement where id_p=? and id_c=? and type=? and groupe=?";
	$res = sqlQuery($sql, [$_SESSION[$s]['user']['id_u'], $cours_id_c, $type, $groupe]);
	$en_id_n = $res['id_n'];

	// recherche toutes les absences
	$sql = "SELECT id_a, date_heure FROM absence WHERE id_n=? ORDER BY id_a DESC";
	$res = sqlQueryAll($sql, [$en_id_n]);
	if (count($res) != 0) {
		$_SESSION[$s]["absence"]["id"] = $res;
		$id_a = $_SESSION[$s]["absence"]["id"][0]["id_a"];
	} else {
		$warningMessage = "Aucun d'enregistrment trouvé";
	}
}
if (!empty($warningMessage)) {
	echo "<div class=\"alert alert-warning\">" . $warningMessage . "</div>";
} elseif (true) {
	if ($_SESSION[$s]["absence"]["type"] == "cm") {
		$sql = "select etu.id_u, etu.prenom_nom , abs.id_a, abs.justificatif from" .
			"(select t1.id_u, CONCAT(t1.prenom,\" \",t1.nom) as prenom_nom from utilisateur t1 join etudiant t2 on t1.id_u=t2.id_e join ametice t3 on t2.id_e=t3.id_e " .
			"where t3.id_c=?) as etu left join " .
			"(select t4.id_e, t4.id_a, t4.justificatif from etudiant t5 join historique t4 on t5.id_e=t4.id_e where t4.id_a=?) as abs " .
			"on etu.id_u=abs.id_e";

		$matrix_abs = sqlQueryAll($sql, [$_SESSION[$s]["absence"]["id_c"], $id_a]);
	} else {
		$sql = "select etu.id_u, etu.prenom_nom , abs.id_a, abs.justificatif from" .
			"(select t1.id_u, CONCAT(t1.prenom,\" \",t1.nom) as prenom_nom from utilisateur t1 join etudiant t2 on t1.id_u=t2.id_e join ametice t3 on t2.id_e=t3.id_e " .
			"where t3.id_c=? and t2.groupe=?) as etu left join " .
			"(select t4.id_e, t4.id_a, t4.justificatif from etudiant t5 join historique t4 on t5.id_e=t4.id_e where t4.id_a=?) as abs " .
			"on etu.id_u=abs.id_e";

		$matrix_abs = sqlQueryAll($sql, [$_SESSION[$s]["absence"]["id_c"], $_SESSION[$s]["absence"]["groupe"], $id_a]);
	}

?>

	<div class="card mt-3">
		<h5 class="card-header">Résultat de recherche</h5>
		<div class="card-body">

			<form method="POST" action="index.php?req=rechercherCours">
				<div class="row g-3">
					<div class="col-sm-4">
						<div class="form-floating">
							<input type="text" name="cours_id_c" value="<?php echo $_SESSION[$s]["absence"]["id_c"]; ?>" hidden>
							<input type="text" class="form-control" id="cours" name="cours_nom" placeholder="Cours" value="<?php echo $_SESSION[$s]["absence"]["cours_nom"]; ?>" readonly="readonly">
							<label for="cours">Cours</label>
						</div>
					</div>

					<div class="col-sm-4">
						<div class="form-floating">
							<input type="text" class="form-control" id="type" name="type" placeholder="Type" value="<?php echo $_SESSION[$s]["absence"]["type"]; ?>" readonly="readonly">
							<label for="type">Type</label>
						</div>
					</div>

					<div class="col-sm-4">
						<div class="form-floating">
							<input type="text" class="form-control" id="groupe" name="groupe" placeholder="Groupe" value="<?php if ($_SESSION[$s]["absence"]["groupe"] == 0) {
																																echo "Tous les groupes";
																															} else {
																																echo $_SESSION[$s]["absence"]["groupe"];
																															} ?>" readonly="readonly">
							<label for="groupe">Groupe</label>
						</div>
					</div>
				</div>

				<div class="mt-3">
					<label for="historique" class="form-label">historique: </label>
					<div class="input-group" id="historique">
						<select class="form-select" id="historique" name="index">
							<?php

							foreach ($_SESSION[$s]["absence"]["id"] as $abs) {
								if ($abs["id_a"] ==	$id_a) {
									echo "<option value=\"" . $abs["id_a"] . "\" selected>" . $abs['date_heure'] . "</option>\n";
								} else {
									echo "<option value=\"" . $abs["id_a"] . "\">" . $abs['date_heure'] . "</option>\n";
								}
							}
							?>
						</select>
						<label for="historique"></label>
						<button class="btn btn-primary" name="consulter" type="submit">Consulter</button>
						<button class="btn btn-secondary" name="modifier" type="submit">Modifier</button>
					</div>
				</div>
			</form>
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
								echo "OUI";
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