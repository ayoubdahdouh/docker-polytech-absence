<?php

$script = $_SERVER["SCRIPT_NAME"];
?>
<nav class="navbar navbar-expand-md navbar-dark bg-dark mb-3">
	<div class="container-fluid">
		<a class="navbar-brand" href="/">Polytech Absence</a>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarCollapse">
			<ul class="navbar-nav me-auto mb-2 mb-md-0">

				<?php if ($_SESSION[$s]['user']['role'] == 'e') { ?>
					<li class="nav-item">
						<a class="nav-link <?php
											echo (strcmp($script, "/polytech-absences/pages/etudiant.php") == 0) ? "active" : "";
											?>" href="?req=etudiant">Consulter</a>
					</li>
				<?php } else { ?>
					<li class="nav-item">
						<a class="nav-link <?php
											echo (strcmp($script, "/polytech-absences/pages/rechercherCours.php") == 0) ? "active" : "";
											?>" href="?req=rechercherCours">Gestion Absences</a>
					</li>

					<?php if ($_SESSION[$s]['user']['role'] == 'p') { ?>
						<li class="nav-item">
							<a class="nav-link <?php
												echo (strcmp($script, "/polytech-absences/pages/rechercherEtudiant.php") == 0) ? "active" : "";
												?>" href="?req=rechercherEtudiant">Rechercher Etudiant</a>
						</li>

					<?php }
					if ($_SESSION[$s]['user']['role'] == 'a') { ?>
						<li class="nav-item">
							<a class="nav-link <?php
												echo (strcmp($script, "/polytech-absences/pages/gestionUtilisateurs.php") == 0) ? "active" : "";
												?>" href="?req=gestionUtilisateurs">Gestion Utilisateurs</a>
						</li>
						<?php
						$sql = "SELECT COUNT(*) cnt FROM notification";
						$cnt = sqlQuery($sql, null);
						?>
						<li class="nav-item">
							<a class="nav-link <?php
												echo (strcmp($script, "/polytech-absences/pages/notification.php") == 0) ? "active" : "";
												?>" href="?req=notification">Notifications <?php echo ($cnt["cnt"] > 0) ? "<span class=\"badge bg-primary\">" . $cnt["cnt"] . "</span>" : ""; ?></a>
						</li>
				<?php }
				} ?>

				<li class="nav-item">
					<a class="nav-link <?php
										echo (strcmp($script, "/polytech-absences/pages/profile.php") == 0) ? "active" : "";
										?>" href="?req=profile">Profile</a>
				</li>

			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li>
					<a class="nav-link" href="?req=seDeconnecter">
						Se déconnecter
					<i class="bi bi-box-arrow-in-right"></i>
					</a>
				</li>

			</ul>
		</div>
	</div>
</nav>