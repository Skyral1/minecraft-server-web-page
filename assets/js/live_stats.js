function safeSetText(id, text) {
	const el = document.getElementById(id);
	if (el) el.innerText = text;
}

function safeSetStyle(id, styleProp, value) {
	const el = document.getElementById(id);
	if (el) el.style[styleProp] = value;
}

// 1. Fonction appelée UNE SEULE FOIS pour lire la configuration
function fetchServerConfig() {
	fetch("../../server-config.php") // METS LE BON CHEMIN ICI
		.then((response) => response.json())
		.then((data) => {
			if (data.success && data.config) {
				const configSection = document.getElementById("config-section");
				const configGrid = document.getElementById("config-grid");

				if (configSection && configGrid) {
					configSection.style.display = "block";
					configGrid.innerHTML = "";

					for (const [key, value] of Object.entries(data.config)) {
						configGrid.innerHTML += `
                            <div style="background: rgba(255, 255, 255, 0.05); padding: 15px; border-radius: 8px;">
                                <span style="display: block; font-size: 0.85rem; color: #aaa; margin-bottom: 5px;">${key}</span>
                                <span style="font-size: 1.1rem; font-weight: 600; color: #fff;">${value}</span>
                            </div>
                        `;
					}
				}
			} else {
				console.warn("Impossible de charger la config", data.error);
			}
		})
		.catch((err) => console.error("Erreur Fetch Config:", err));
}

// 2. Fonction appelée TOUTES LES SECONDES pour le direct
// Fonction pour déterminer la couleur en fonction du pourcentage
function getColorForPercentage(pct) {
	if (pct < 60) return "#4ade80"; // Vert (OK)
	if (pct < 85) return "#fbbf24"; // Jaune/Orange (Attention)
	return "#ef4444"; // Rouge (Critique)
}

function fetchServerStats() {
	fetch("../../server-stats.php") // Remets '/server-stats.php' si tu avais mis le slash
		.then((response) => response.json())
		.then((data) => {
			if (data.success) {
				// --- CPU ---
				if (data.cpu !== undefined) {
					const cpuVal = parseFloat(data.cpu).toFixed(1);
					const cpuPct = Math.min(cpuVal, 100);

					safeSetText("cpu-val", cpuVal + " %");
					safeSetStyle("cpu-bar", "width", cpuPct + "%");
					// Nouvelle ligne pour la couleur :
					safeSetStyle(
						"cpu-bar",
						"backgroundColor",
						getColorForPercentage(cpuPct),
					);
				}

				// --- RAM ---
				if (
					data.ram_used_bytes !== undefined &&
					data.ram_max_bytes !== undefined
				) {
					const ramGo = (data.ram_used_bytes / 1024 / 1024 / 1024).toFixed(1);
					const ramMaxGo = (data.ram_max_bytes / 1024 / 1024 / 1024).toFixed(0);
					const ramPct = (ramGo / ramMaxGo) * 100;

					safeSetText("ram-val", ramGo + " Go");
					safeSetStyle("ram-bar", "width", Math.min(ramPct, 100) + "%");
					// Nouvelle ligne pour la couleur :
					safeSetStyle(
						"ram-bar",
						"backgroundColor",
						getColorForPercentage(ramPct),
					);

					safeSetText("ram-detail", `${ramGo} Go / ${ramMaxGo} Go`);
				}

				// --- DISQUE ---
				if (
					data.disk_bytes !== undefined &&
					data.disk_max_bytes !== undefined
				) {
					const diskGo = (data.disk_bytes / 1024 / 1024 / 1024).toFixed(1);
					const diskMaxGo = (data.disk_max_bytes / 1024 / 1024 / 1024).toFixed(
						0,
					);
					const diskPct = (diskGo / diskMaxGo) * 100;

					safeSetText("disk-val", diskGo + " Go");
					safeSetStyle("disk-bar", "width", Math.min(diskPct, 100) + "%");
					// Nouvelle ligne pour la couleur :
					safeSetStyle(
						"disk-bar",
						"backgroundColor",
						getColorForPercentage(diskPct),
					);

					safeSetText("disk-detail", `${diskGo} Go / ${diskMaxGo} Go`);
				}
			} else {
				console.error("Erreur stats serveur:", data.error);
			}
		})
		.catch((err) => console.error("Erreur Fetch Stats:", err));
}

// Lancement
fetchServerConfig(); // Une seule fois au début
fetchServerStats(); // Premier appel immédiat
setInterval(fetchServerStats, 1000); // Boucle toutes les secondes
