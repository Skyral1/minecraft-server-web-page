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

			<!-- MODS (Modrinth API avec LocalStorage, Fallback CurseForge et DL) -->
			<div class="stat-card wide" id="mods-section">
				<h3 style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
					<span style="display: flex; align-items: center; gap: 8px;">
						<i class="ph-bold ph-puzzle-piece" style="color: var(--primary)"></i>
						Mods Installés (<span id="mods-count">...</span>)
					</span>
					<i id="mod-loading-spinner" class="ph-bold ph-spinner"
						style="display: none; animation: spin 1s linear infinite; color: var(--text-muted)"></i>
				</h3>
				<p style="color: var(--text-muted); font-size: 0.9rem; text-align: left; margin-bottom: 20px;">
					Dernière synchronisation serveur : <span id="mods-sync">chargement...</span>
				</p>

				<!-- Zone de notification de mise à jour (Local) -->
				<div id="mods-diff-alert"
					style="display: none; background: rgba(140, 92, 231, 0.1); border: 1px solid var(--primary); border-radius: 12px; padding: 16px; margin-bottom: 20px; text-align: left;">
					<div
						style="display: flex; align-items: center; gap: 10px; color: #fff; font-weight: 600; margin-bottom: 8px;">
						<i class="ph-fill ph-bell-ringing" style="color: var(--primary); font-size: 1.2rem;"></i>
						Mise à jour des mods détectée depuis votre dernière visite !
					</div>
					<ul id="mods-diff-list"
						style="list-style: none; margin: 0; padding: 0; font-size: 0.9rem; color: var(--text-muted);">
						<!-- Rempli par JS -->
					</ul>
				</div>

				<!-- Grille des mods -->
				<div id="mod-container" class="mod-grid">
					<!-- Les cartes des mods apparaîtront ici -->
				</div>
			</div>

			<script>
				document.addEventListener('DOMContentLoaded', async () => {
					// --- CONFIGURATION DE VOTRE SERVEUR POUR LES TELECHARGEMENTS ---
					const SERVER_MC_VERSION = "1.20.1"; // Mettez ici la version de votre serveur
					const SERVER_LOADER = "forge";      // "forge", "fabric", ou "neoforge"
					// ---------------------------------------------------------------

					try {
						const response = await fetch('../php/fetch-mods.php');
						const data = await response.json();

						if (data.success) {
							document.getElementById('mods-count').innerText = data.mods.length;
							document.getElementById('mods-sync').innerText = data.last_updated;

							const container = document.getElementById('mod-container');
							const spinner = document.getElementById('mod-loading-spinner');

							container.innerHTML = '';
							spinner.style.display = 'inline-block';

							// --- 1. LOGIQUE LOCALSTORAGE & DIFFÉRENCES ---
							const currentModFiles = data.mods.map(m => m.filename);
							const cachedModsJson = localStorage.getItem('bmc4_mods_cache');

							let addedMods = [];
							let removedMods = [];

							if (cachedModsJson) {
								const cachedModFiles = JSON.parse(cachedModsJson);
								addedMods = currentModFiles.filter(file => !cachedModFiles.includes(file));
								removedMods = cachedModFiles.filter(file => !currentModFiles.includes(file));

								if (addedMods.length > 0 || removedMods.length > 0) {
									const alertBox = document.getElementById('mods-diff-alert');
									const diffList = document.getElementById('mods-diff-list');
									alertBox.style.display = 'block';
									diffList.innerHTML = '';

									addedMods.forEach(mod => {
										diffList.innerHTML += `<li style="color: #4ade80; margin-bottom: 4px;"><i class="ph-bold ph-plus-circle"></i> Nouveau : ${mod}</li>`;
									});

									removedMods.forEach(mod => {
										diffList.innerHTML += `<li style="color: #ef4444; margin-bottom: 4px;"><i class="ph-bold ph-minus-circle"></i> Retiré : ${mod}</li>`;
									});
								}
							}
							localStorage.setItem('bmc4_mods_cache', JSON.stringify(currentModFiles));

							// --- 2. TRI ET PRÉPARATION DE LA LISTE ---
							let displayList = [];

							const addedModObjects = data.mods.filter(m => addedMods.includes(m.filename));
							addedModObjects.forEach(m => { m.status = 'added'; displayList.push(m); });

							removedMods.forEach(filename => {
								let cleanName = filename.replace(/(-mc\d+\.\d+(\.\d+)?.*|\d+\.\d+.*)\.jar$/i, '');
								cleanName = cleanName.replace('.jar', '').replace(/[-_]/g, ' ');
								displayList.push({ name: cleanName, filename: filename, status: 'removed' });
							});

							const unchangedModObjects = data.mods.filter(m => !addedMods.includes(m.filename));
							unchangedModObjects.forEach(m => { m.status = 'normal'; displayList.push(m); });

							// --- 3. SKELETONS ---
							displayList.forEach((mod, index) => {
								const cardId = `mod-card-${index}`;
								let borderStyle = "";
								let badgeStyle = "background: rgba(255,255,255,0.05); color: var(--text-muted)";
								let badgeText = `<i class="ph-bold ph-file-zip"></i> Installé`;

								if (mod.status === 'added') {
									borderStyle = "border-color: #4ade80; background: rgba(74, 222, 128, 0.05);";
									badgeStyle = "background: rgba(74, 222, 128, 0.15); color: #4ade80;";
									badgeText = `<i class="ph-bold ph-plus-circle"></i> Nouveau`;
								} else if (mod.status === 'removed') {
									borderStyle = "border-color: #ef4444; background: rgba(239, 68, 68, 0.05); opacity: 0.7;";
									badgeStyle = "background: rgba(239, 68, 68, 0.15); color: #ef4444;";
									badgeText = `<i class="ph-bold ph-minus-circle"></i> Retiré`;
								}

								const skeletonHtml = `
					<div class="mod-card" id="${cardId}" style="${borderStyle}">
						<div class="mod-header">
							<div class="mod-icon" style="background: rgba(255,255,255,0.02);">
								${mod.status === 'removed' ? '<i class="ph-bold ph-trash" style="color: #ef4444"></i>' : '<i class="ph-bold ph-spinner" style="animation: spin 1s linear infinite; color: var(--text-muted)"></i>'}
							</div>
							<div class="mod-info">
								<div class="mod-title" title="${mod.name}">${mod.name}</div>
								<div class="mod-author" style="color: rgba(255,255,255,0.3)">
									${mod.status === 'removed' ? 'Mod supprimé du serveur' : 'Recherche en cours...'}
								</div>
							</div>
						</div>
						<div class="mod-description" style="color: rgba(255,255,255,0.2)">
							${mod.filename}
						</div>
						<div class="mod-footer">
							<span class="mod-badge" style="${badgeStyle}">${badgeText}</span>
						</div>
					</div>
				`;
								container.insertAdjacentHTML('beforeend', skeletonHtml);
							});

							// --- 4. RECHERCHE MODRINTH OU REPLI CURSEFORGE ---
							const fetchModrinthData = async (mod, index) => {
								if (mod.status === 'removed') return;

								const cardId = `mod-card-${index}`;
								const cardElement = document.getElementById(cardId);

								try {
									const searchQuery = encodeURIComponent(mod.name);
									const modrinthRes = await fetch(`https://api.modrinth.com/v2/search?query="${searchQuery}"&limit=1`);

									if (modrinthRes.status === 429) throw new Error("Rate limit");

									const modrinthData = await modrinthRes.json();

									// SI TROUVÉ SUR MODRINTH
									if (modrinthData.hits && modrinthData.hits.length > 0) {
										const project = modrinthData.hits[0];
										const iconUrl = project.icon_url || 'https://docs.modrinth.com/img/logo.svg';

										let badgeStyle = "background: rgba(0, 230, 184, 0.1); color: var(--accent);";
										let badgeText = `<i class="ph-bold ph-check"></i> Installé`;

										if (mod.status === 'added') {
											badgeStyle = "background: rgba(74, 222, 128, 0.15); color: #4ade80;";
											badgeText = `<i class="ph-bold ph-plus-circle"></i> Nouveau`;
										}

										// RECUPERATION DU LIEN DE TELECHARGEMENT EXACT (Modrinth)
										let downloadLinkHtml = '';
										try {
											const queryParams = new URLSearchParams({
												game_versions: `["${SERVER_MC_VERSION}"]`,
												loaders: `["${SERVER_LOADER}"]`
											});

											const versionsRes = await fetch(`https://api.modrinth.com/v2/project/${project.project_id}/version?${queryParams.toString()}`);

											if (versionsRes.ok) {
												const versionsData = await versionsRes.json();
												if (versionsData && versionsData.length > 0) {
													const exactVersion = versionsData[0];
													if (exactVersion.files && exactVersion.files.length > 0) {
														const primaryFile = exactVersion.files.find(f => f.primary) || exactVersion.files[0];
														const downloadUrl = primaryFile.url;

														downloadLinkHtml = `
											<a href="${downloadUrl}" class="mod-link" style="border-color: var(--primary); color: #fff;" title="Télécharger pour ${SERVER_MC_VERSION} ${SERVER_LOADER}">
												<i class="ph-bold ph-download-simple" style="color: var(--primary)"></i> DL (${SERVER_MC_VERSION})
											</a>
										`;
													}
												}
											}
										} catch (versionError) {
											console.warn("Erreur récupération version", project.title);
										}

										cardElement.innerHTML = `
							<div class="mod-header">
								<img src="${iconUrl}" alt="${project.title}" class="mod-icon" onerror="this.src='https://docs.modrinth.com/img/logo.svg'">
								<div class="mod-info">
									<div class="mod-title" title="${project.title}">${project.title}</div>
									<div class="mod-author" style="color: var(--primary)">par ${project.author}</div>
								</div>
							</div>
							<div class="mod-description" title="${project.description}">
								${project.description}
							</div>
							<div class="mod-footer">
								<span class="mod-badge" style="${badgeStyle}">${badgeText}</span>
								<div style="display: flex; gap: 8px;">
									${downloadLinkHtml}
									<a href="https://modrinth.com/mod/${project.slug}" target="_blank" class="mod-link">
										Voir <i class="ph-bold ph-arrow-square-out"></i>
									</a>
								</div>
							</div>
						`;
										if (mod.status !== 'added') cardElement.style.borderColor = "rgba(140, 92, 231, 0.3)";
									} else {
										throw new Error("Non trouvé");
									}
								} catch (e) {
									// --- SI NON TROUVÉ SUR MODRINTH => VÉRIFICATION CURSEFORGE VIA PHP ---
									let authorText, badgeStyle, badgeText, iconHtml, actionLink = '', borderColor = '';

									if (e.message === "Rate limit") {
										authorText = "Trop de requêtes API";
										badgeStyle = "background: rgba(255,255,255,0.05); color: var(--text-muted)";
										badgeText = `<i class="ph-bold ph-warning"></i> Attente`;
										iconHtml = `<div class="mod-icon"><i class="ph-bold ph-clock"></i></div>`;
										actionLink = `<span style="color: var(--text-muted); font-size: 0.85rem;">Réessayez plus tard</span>`;
									} else {
										authorText = "CurseForge";
										badgeStyle = "background: rgba(241, 100, 54, 0.15); color: #F16436;";
										badgeText = `<i class="ph-bold ph-fire"></i> Exclusivité ?`;
										iconHtml = `<div class="mod-icon" style="background: rgba(241, 100, 54, 0.1); color: #F16436;"><i class="ph-bold ph-fire"></i></div>`;
										borderColor = "border-color: rgba(241, 100, 54, 0.3);";

										const slug = mod.name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
										const potentialUrl = `https://www.curseforge.com/minecraft/mc-mods/${slug}`;

										try {
											const checkRes = await fetch(`../php/check-url.php?url=${encodeURIComponent(potentialUrl)}`);
											const checkData = await checkRes.json();

											if (checkData.exists) {
												actionLink = `
									<a href="${potentialUrl}" target="_blank" class="mod-link" style="border-color: rgba(241, 100, 54, 0.3); color: #F16436;">
										Page du mod <i class="ph-bold ph-arrow-square-out"></i>
									</a>
								`;
											} else {
												const cfSearchUrl = `https://www.curseforge.com/minecraft/search?class=mc-mods&search=${encodeURIComponent(mod.name)}`;
												actionLink = `
									<a href="${cfSearchUrl}" target="_blank" class="mod-link" style="border-color: rgba(255, 255, 255, 0.2); color: #fff;">
										Rechercher CF <i class="ph-bold ph-magnifying-glass"></i>
									</a>
								`;
											}
										} catch (err) {
											const cfSearchUrl = `https://www.curseforge.com/minecraft/search?class=mc-mods&search=${encodeURIComponent(mod.name)}`;
											actionLink = `<a href="${cfSearchUrl}" target="_blank" class="mod-link">Rechercher <i class="ph-bold ph-magnifying-glass"></i></a>`;
										}
									}

									if (mod.status === 'added') {
										borderColor = "border-color: #4ade80;";
										badgeStyle = "background: rgba(74, 222, 128, 0.15); color: #4ade80;";
										badgeText = `<i class="ph-bold ph-plus-circle"></i> Nouveau`;
									}

									cardElement.innerHTML = `
						<div class="mod-header">
							${iconHtml}
							<div class="mod-info">
								<div class="mod-title" title="${mod.name}">${mod.name}</div>
								<div class="mod-author" style="color: ${e.message === 'Rate limit' ? '#ff6b6b' : '#F16436'}">${authorText}</div>
							</div>
						</div>
						<div class="mod-description" style="color: var(--text-muted)">
							Fichier : ${mod.filename}
						</div>
						<div class="mod-footer">
							<span class="mod-badge" style="${badgeStyle}">${badgeText}</span>
							${actionLink}
						</div>
					`;

									if (borderColor && mod.status !== 'added') {
										cardElement.style.cssText += borderColor;
									}
								}
							};

							// --- 5. BATCHING ---
							const processInBatches = async (items, batchSize) => {
								for (let i = 0; i < items.length; i += batchSize) {
									const batch = items.slice(i, i + batchSize);
									const promises = batch.map((mod, batchIndex) => fetchModrinthData(mod, i + batchIndex));
									await Promise.allSettled(promises);
									if (i + batchSize < items.length) await new Promise(resolve => setTimeout(resolve, 250));
								}
							};

							await processInBatches(displayList, 3);
							spinner.style.display = 'none';

						} else {
							console.error("Erreur serveur:", data.error);
							document.getElementById('mod-container').innerHTML = '<div style="color: #ff6b6b; padding: 15px;">Erreur de chargement des mods.</div>';
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