<?php

$warningMessage = "";
$errorMessage = "";
$successMessage = "";

if (isset($_POST['mise_a_jour'])) {
	require_once('admin/mise_a_jour.php');
} elseif (isset($_POST['modifier'])) {
	$_SESSION[$s]["rc"]["id_a"] = isset($_POST['index']) ? intval($_POST['index']) : "";

	// date, heure et minites
	$sql = "select date_heure from absence where id_a=?";
	$date_heure = sqlQuery($sql, [$_SESSION[$s]["rc"]["id_a"]]);
	$res = explode(" ", $date_heure["date_heure"]);
	$res2 = explode(":", $res[1]);

	$_SESSION[$s]["rc"]["date"] = $res[0];
	$_SESSION[$s]["rc"]["heure"] = $res2[0];
	$_SESSION[$s]["rc"]["minutes"] = $res2[1];
} else {
	if (!isset($_SESSION[$s]["rc"]["id_a"])) {
		// une acceès anormale à cette page
		$errorMessage = "Une erreur s'est produite, veuillez réessayer";
	}
}


if (!empty($errorMessage)) {
	echo "<div class=\"alert alert-danger\">" . $errorMessage . "</div>";
} elseif (!empty($successMessage)) {
	echo "<div class=\"alert alert-success\">" . $successMessage . "</div>";
} else {
	if (isset($_POST['modifier'])) {
		if ($_SESSION[$s]["rc"]["type"] == "cm") {
			$sql = "select etu.id_u, etu.prenom_nom , abs.id_a from" .
				"(select t1.id_u, CONCAT(t1.prenom,\" \",t1.nom) as prenom_nom from utilisateur t1 join etudiant t2 on t1.id_u=t2.id_e join ametice t3 on t2.id_e=t3.id_e " .
				"where t3.id_c=?) as etu left join " .
				"(select t4.id_e, t4.id_a from etudiant t5 join historique t4 on t5.id_e=t4.id_e where t4.id_a=?) as abs " .
				"on etu.id_u=abs.id_e";

			$_SESSION[$s]["rc"]["matrix"] = sqlQueryAll($sql, [$_SESSION[$s]["rc"]["id_c"], $_SESSION[$s]["rc"]["id_a"]]);
		} else {
			$sql = "select etu.id_u, etu.prenom_nom , abs.id_a from" .
				"(select t1.id_u, CONCAT(t1.prenom,\" \",t1.nom) as prenom_nom from utilisateur t1 join etudiant t2 on t1.id_u=t2.id_e join ametice t3 on t2.id_e=t3.id_e " .
				"where t3.id_c=? and t2.groupe=?) as etu left join " .
				"(select t4.id_e, t4.id_a from etudiant t5 join historique t4 on t5.id_e=t4.id_e where t4.id_a=?) as abs " .
				"on etu.id_u=abs.id_e";

			$_SESSION[$s]["rc"]["matrix"] = sqlQueryAll($sql, [$_SESSION[$s]["rc"]["id_c"], $_SESSION[$s]["rc"]["groupe"], $_SESSION[$s]["rc"]["id_a"]]);
		}
	}


?>
	<div class="card mt-3">
		<h5 class="card-header">Modifier l'absence</h5>
		<div class="card-body">
			<form method="POST" action="index.php?req=rechercherCours">
				<div class="row g-3">
					<div class="col-sm-6">
						<div class="form-floating">
							<input type="text" class="form-control" id="filiere" name="filiere_nom" placeholder="Filière" value="<?php echo $_SESSION[$s]["rc"]["filiere_nom"]; ?>" readonly="readonly">
							<label for="filiere">Filière</label>
						</div>
					</div>

					<div class="col-sm-6">
						<div class="form-floating">
							<input type="text" class="form-control" id="annee" name="annee" placeholder="Année" value="<?php echo $_SESSION[$s]["rc"]["annee"]; ?>" readonly="readonly">
							<label for="annee">Année</label>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-floating">
							<input type="text" class="form-control" id="cours" name="cours_nom" placeholder="Cours" value="<?php echo $_SESSION[$s]["rc"]["cours_nom"]; ?>" readonly="readonly">
							<label for="cours">Cours</label>
						</div>
					</div>

					<div class="col-sm-4">
						<div class="form-floating">
							<input type="text" class="form-control" id="type" name="type" placeholder="Type" value="<?php echo $_SESSION[$s]["rc"]["type"]; ?>" readonly="readonly">
							<label for="type">Type</label>
						</div>
					</div>

					<div class="col-sm-4">
						<div class="form-floating">
							<input type="text" class="form-control" id="groupe" name="groupe" placeholder="Groupe" value="<?php if ($_SESSION[$s]["rc"]["groupe"] == 0) {
																																echo "Tous les groupes";
																															} else {
																																echo $_SESSION[$s]["rc"]["groupe"];
																															} ?>" readonly="readonly">
							<label for="groupe">Groupe</label>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-floating">
							<input type="text" id="date" class="form-control" name="date" placeholder="Date" value="<?php echo $_SESSION[$s]["rc"]["date"]; ?>" readonly="readonly">
							<label for="date">Date</label>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-floating">
							<input type="text" id="heure" class="form-control" name="heure" placeholder="Heure" value="<?php echo $_SESSION[$s]["rc"]["heure"]; ?>" readonly="readonly">
							<label for="heure">Heure</label>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-floating">
							<input type="text" id="minutes" class="form-control" name="minutes" placeholder="Minutes" value="<?php echo $_SESSION[$s]["rc"]["minutes"]; ?>" readonly="readonly">
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
						if (!isset($_POST['mise_a_jour'])) {
							foreach ($_SESSION[$s]["rc"]["matrix"] as $abs) { ?>
								<tr>
									<?php
									echo "<td class=\"col-sm-10\">" . $abs['prenom_nom'] . " </td>";
									echo "<td class=\"col-sm-2\"><input type=\"checkbox\" class=\"form-check-input\" name=\"absents[]\" value=\"" . $abs['id_u'] . "\"";
									if ($abs['id_a']) {
										echo "checked";
									}
									echo "/></td>";
									?>
								</tr>
						<?PHP
							}
						} ?>
					</tbody>
				</table>

				<div class="mt-2">
					<a href="index.php" class="btn btn-danger">Annuler</a>
					<button class="btn btn-success" name="mise_a_jour" type="submit">Mise à jour</button>
				</div>
			</form>

		</div>
	</div>
<?php
}

?>