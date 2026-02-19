# 🎮 Minecraft Server Web Page

A modern, responsive landing page template for Minecraft servers, featuring a sleek glassmorphism design, essential server information, and a comprehensive Wiki.

![License](https://img.shields.io/badge/license-MIT-blue.svg)
![HTML5](https://img.shields.io/badge/html5-%23E34F26.svg?style=flat&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/css3-%231572B6.svg?style=flat&logo=css3&logoColor=white)
![JS](https://img.shields.io/badge/javascript-%23F7DF1E.svg?style=flat&logo=javascript&logoColor=black)
![Status](https://img.shields.io/badge/status-active-success.svg)

## ✨ Features

- **Modern UI/UX**: Dark theme with glassmorphism effects and neon accents (Purple/Cyan).
- **Responsive Design**: Fully optimized for desktop, tablet, and mobile devices.
- **Server Status**: Visual indicators for server online status.
- **One-Click Copy**: Click-to-copy functionality for the server IP address.
- **Wiki Section**:
  - Complete command guide (TPATools, Homes, Claims).
  - Interactive code blocks (click-to-copy commands).
  - Sticky sidebar navigation.
- **Tutorial Page**: Step-by-step guide to join (CurseForge installation, modpack download).
- **Phosphor Icons**: High-quality, consistent iconography.
- **Clean Codebase**: Semantic HTML5, CSS3 variables, and modular JS.

## 🚀 Live Demo

[https://bmc4.strator.gg](https://bmc4.strator.gg) _(Replace with your actual link)_

## 🛠️ Installation

1.  **Clone the repository**

    ```bash
    git clone https://github.com/Skyral1/minecraft-server-web-page.git
    cd minecraft-server-web-page
    ```

2.  **Customize the content**
    - Open `index.html` to update your server name, description, and IP.
    - Edit `wiki/index.html` to modify server commands or rules.
    - Change colors in `assets/css/main.css` and `assets/css/wiki.css`.

3.  **Deploy**
    - Upload the files to your web host (Apache/Nginx) or use GitHub Pages / Vercel / Netlify.

## 📂 Project Structure

```text
minecraft-server-web-page/
├── assets/
│   ├── css/
│   │   ├── main.css      # Homepage styles
│   │   └── wiki.css      # Wiki specific styles
│   ├── js/
│   │   ├── main.js       # Homepage scripts (IP copy, etc.)
│   │   └── wiki.js       # Wiki scripts (Command copy, sticky nav)
│   └── images/           # Project images (favicons, backgrounds)
├── comment-rejoindre/
│   └── index.html        # Tutorial page
├── wiki/
│   └── index.html        # Wiki & Commands page
├── index.html            # Homepage
└── README.md             # Project documentation
```

## 🎨 Customization

You can easily change the theme by modifying the `:root` variables in `assets/css/main.css`:

```css
:root {
	--primary: #8c5ce7; /* Main purple color */
	--primary-glow: rgba(140, 92, 231, 0.35);
	--accent: #00e6b8; /* Cyan accent color */
	--bg: #030305; /* Dark background */
	--surface: #0e0e12; /* Card background */
	--text-main: #ffffff;
}
```

## 🤝 Contributing

Contributions are welcome! Feel free to submit a Pull Request.

1.  Fork the project
2.  Create your feature branch (`git checkout -b feature/AmazingFeature`)
3.  Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4.  Push to the branch (`git push origin feature/AmazingFeature`)
5.  Open a Pull Request

## 📄 License

Distributed under the MIT License. See `LICENSE` for more information.

---

Made with ❤️ by **Skyral1**
