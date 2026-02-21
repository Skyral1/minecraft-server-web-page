<!doctype html>
<html lang="fr">

<head>
	<meta charset="UTF-8" />
	<title>BMC4 • Statistiques Serveur</title>
	<!-- Polices -->
	<link rel="preconnect" href="https://fonts.googleapis.com" />
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
	<link
		href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Space+Grotesk:wght@700&family=JetBrains+Mono:wght@500&display=swap"
		rel="stylesheet" />

	<!-- Tes liens CSS habituels -->
	<link rel="stylesheet" href="../assets/css/main.css" />
	<script src="https://unpkg.com/@phosphor-icons/web"></script>

	<style>
		.stats-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
			gap: 20px;
			margin-top: 40px;
		}

		.stat-card {
			background: var(--surface, rgba(20, 20, 30, 0.8));
			border: 1px solid var(--border, rgba(255, 255, 255, 0.05));
			border-radius: 16px;
			padding: 24px;
			text-align: center;
			backdrop-filter: blur(10px);
		}

		.stat-card.wide {
			grid-column: 1 / -1;
			text-align: left;
		}

		.stat-value {
			font-family: "Space Grotesk", sans-serif;
			font-size: 2.2rem;
			color: var(--primary, #8c5ce7);
			margin: 10px 0;
		}

		.stat-label {
			color: var(--text-muted, #a0a0b0);
			font-size: 0.9rem;
			text-transform: uppercase;
			letter-spacing: 1px;
		}

		.progress-bar {
			background: rgba(255, 255, 255, 0.1);
			height: 8px;
			border-radius: 4px;
			margin-top: 15px;
			overflow: hidden;
		}

		.progress-fill {
			height: 100%;
			width: 0%;
			/* Animé par JS */
			transition: width 1s ease-in-out;
		}

		/* Grille pour les Propriétés (Config du serveur) */
		.config-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
			gap: 12px;
			margin-top: 20px;
			text-align: left;
		}

		.config-item {
			background: rgba(255, 255, 255, 0.03);
			padding: 12px 16px;
			border-radius: 8px;
			border: 1px solid rgba(255, 255, 255, 0.05);
			display: flex;
			justify-content: space-between;
			align-items: center;
		}

		.config-item .label {
			color: var(--text-muted);
			font-size: 0.9rem;
		}

		.config-item .value {
			color: #fff;
			font-weight: 600;
			font-family: "JetBrains Mono", monospace;
		}

		/* Liste des joueurs */
		.players-grid {
			display: flex;
			flex-wrap: wrap;
			gap: 12px;
			margin-top: 20px;
		}

		.player-card {
			background: rgba(255, 255, 255, 0.05);
			border: 1px solid rgba(255, 255, 255, 0.1);
			padding: 8px 16px 8px 8px;
			border-radius: 8px;
			display: flex;
			align-items: center;
			gap: 12px;
			color: #fff;
			font-weight: 500;
		}

		.player-card img {
			width: 32px;
			height: 32px;
			border-radius: 6px;
		}
	</style>
</head>

<body>
	<nav class="container">
		<a href="../" class="logo">
			<i class="ph-fill ph-cube" style="color: var(--primary, #8c5ce7)"></i>
			BMC4 <span style="color: var(--primary, #8c5ce7)">SERVER</span>
		</a>
		<div class="nav-links" style="display: flex; gap: 12px">
			<a href="../" class="btn btn-secondary btn-nav"><i class="ph-bold ph-arrow-left"></i> Retour</a>
		</div>
	</nav>

	<div class="container">
		<header class="hero-mini" style="margin-top: 40px; text-align: center">
			<h1 style="font-size: 3rem; margin-bottom: 10px">État du Serveur</h1>
			<p style="color: var(--text-muted); font-size: 1.1rem">
				Données techniques en temps réel de la machine BMC4.
			</p>
		</header>

		<div class="stats-grid">
			<!-- CPU -->
			<div class="stat-card">
				<i class="ph-duotone ph-cpu" style="font-size: 36px; color: #fff"></i>
				<div class="stat-value" id="cpu-val">-- %</div>
				<div class="stat-label">Utilisation CPU</div>
				<div class="progress-bar">
					<div class="progress-fill" id="cpu-bar" style="background: #ff6b6b"></div>
				</div>
			</div>

			<!-- RAM -->
			<div class="stat-card">
				<i class="ph-duotone ph-memory" style="font-size: 36px; color: #fff"></i>
				<div class="stat-value" id="ram-val">-- Go</div>
				<div class="stat-label">RAM (Mémoire)</div>
				<div class="progress-bar">
					<div class="progress-fill" id="ram-bar" style="background: #8c5ce7"></div>
				</div>
				<small id="ram-detail" style="color: var(--text-muted); display: block; margin-top: 10px">-- /
					--</small>
			</div>

			<!-- DISQUE -->
			<div class="stat-card">
				<i class="ph-duotone ph-hard-drives" style="font-size: 36px; color: #fff"></i>
				<div class="stat-value" id="disk-val">-- Go</div>
				<div class="stat-label">Stockage SSD</div>
				<div class="progress-bar">
					<div class="progress-fill" id="disk-bar" style="background: #00e676"></div>
				</div>
				<small id="disk-detail" style="color: var(--text-muted); display: block; margin-top: 10px">-- /
					--</small>
			</div>

			<!-- JOUEURS EN LIGNE (Query) -->
			<div class="stat-card wide" id="players-section">
				<h3 style="
							display: flex;
							align-items: center;
							gap: 8px;
							margin-bottom: 5px;
						">
					<i class="ph-bold ph-users-three" style="color: var(--primary)"></i>
					Joueurs Actuellement Connectés (<span id="players-count-title">0</span>)
				</h3>
				<p style="color: var(--text-muted); font-size: 0.9rem" id="players-status">
					Interrogation du serveur en cours...
				</p>

				<div class="players-grid" id="players-grid">
					<!-- Rempli par JS -->
				</div>
			</div>

			<!-- PROPRIÉTÉS DU SERVEUR (API MineStrator) -->
			<div class="stat-card wide" id="config-section" style="display: none">
				<h3 style="display: flex; align-items: center; gap: 8px">
					<i class="ph-bold ph-sliders" style="color: var(--primary)"></i>
					Configuration du Monde (server.properties)
				</h3>

				<div class="config-grid" id="config-grid">
					<!-- Rempli par JS -->
				</div>
			</div>

			<!-- MODS (Modrinth API) -->
			<div class="stat-card wide" id="mods-section">
				<h3 style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px; text-align: left;">
					<i class="ph-bold ph-puzzle-piece" style="color: var(--primary)"></i>
					Mods Installés (<span id="mods-count">...</span>)
				</h3>
				<p style="color: var(--text-muted); font-size: 0.9rem; text-align: left; margin-bottom: 20px;">
					Dernière synchronisation : <span id="mods-sync">chargement...</span>
				</p>

				<div class="custom-select-wrapper">
					<div class="select-container">
						<select id="server-mods-select" class="custom-select">
							<option value="" disabled selected>Chargement des mods depuis le serveur...</option>
						</select>
						<div class="select-arrow">
							<i class="ph-bold ph-caret-down"></i>
						</div>
					</div>
				</div>
			</div>

			<script>
				document.addEventListener('DOMContentLoaded', async () => {
					try {
						const response = await fetch('../php/fetch-mods.php');
						const data = await response.json();

						if (data.success) {
							const select = document.getElementById('server-mods-select');

							// Mettre à jour les compteurs et dates
							document.getElementById('mods-count').innerText = data.mods.length;
							document.getElementById('mods-sync').innerText = data.last_updated;

							// Vider le select et ajouter l'option par défaut
							select.innerHTML = '<option value="" disabled selected>Sélectionnez un mod pour voir son fichier...</option>';

							// Ajouter dynamiquement les mods
							data.mods.forEach(mod => {
								const option = document.createElement('option');
								option.value = mod.filename;
								option.textContent = mod.name;
								select.appendChild(option);
							});
						} else {
							console.error("Erreur serveur:", data.error);
							document.getElementById('server-mods-select').innerHTML = '<option disabled selected>Erreur de chargement</option>';
						}
					} catch (error) {
						console.error("Erreur Fetch:", error);
					}
				});
			</script>


			

		</div>
	</div>

	<script src="../assets/js/live_stats.js"></script>
</body>

</html>