document.addEventListener("DOMContentLoaded", () => {
	// --- 1. Gestion de la copie des commandes ---
	const codes = document.querySelectorAll("code");

	codes.forEach((codeBlock) => {
		// Ajout du tooltip au survol
		codeBlock.setAttribute("title", "Cliquez pour copier la commande");

		codeBlock.addEventListener("click", () => {
			// Récupère le texte propre
			const commandText = codeBlock.innerText.trim();

			// Copie dans le presse-papier
			navigator.clipboard
				.writeText(commandText)
				.then(() => {
					// Ajoute la classe qui affiche le message "Copié !"
					codeBlock.classList.add("copied");

					// Retire la classe après 1.5 seconde
					setTimeout(() => {
						codeBlock.classList.remove("copied");
					}, 1500);
				})
				.catch((err) => {
					console.error("Erreur lors de la copie :", err);
				});
		});
	});

	// --- 2. Gestion du scroll et menu actif (Sidebar) ---
	const sections = document.querySelectorAll("section");
	const navLi = document.querySelectorAll(".nav-list li a");

	window.addEventListener("scroll", () => {
		let current = "";

		sections.forEach((section) => {
			const sectionTop = section.offsetTop;
			// On détecte la section active quand on est à 200px du haut
			if (pageYOffset >= sectionTop - 200) {
				current = section.getAttribute("id");
			}
		});

		navLi.forEach((a) => {
			a.classList.remove("active");
			// Si le lien correspond à la section active
			if (a.getAttribute("href").includes(current) && current !== "") {
				a.classList.add("active");
			}
		});
	});
});
