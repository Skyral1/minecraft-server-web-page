const serverIp = "bmc4.minesr.com"; // Ton IP
const apiUrl = `https://api.mcsrvstat.us/2/${serverIp}`;

fetch(apiUrl)
	.then((response) => response.json())
	.then((data) => {
		const statusElem = document.getElementById("server-status");
		const playersElem = document.getElementById("player-count");
		const ipElem = document.getElementById("server-ip");

		if (data.online) {
			statusElem.innerHTML = '<span class="status-dot"></span> En Ligne';
			statusElem.style.color = "var(--accent)";
			// Affiche "5 / 20" par exemple
			playersElem.innerText = `${data.players.online} / ${data.players.max}`;
		} else {
			statusElem.innerText = "Hors Ligne";
			statusElem.style.color = "#ff4d4d"; // Rouge si hors ligne
			playersElem.innerText = "0 / 0";
		}
	})
	.catch((err) => {
		console.error("Erreur API:", err);
	});
