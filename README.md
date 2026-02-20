# 🎮 Minecraft Server Web Page

A modern, responsive landing page for Minecraft servers, featuring a sleek **glassmorphism design**, real-time server stats via the **Minestrator API**, dynamic server configuration fetched via **SFTP**, and a comprehensive Wiki & Tutorial.

![License](https://img.shields.io/badge/license-MIT-blue.svg)
![HTML5](https://img.shields.io/badge/html5-%23E34F26.svg?style=flat&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/css3-%231572B6.svg?style=flat&logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/javascript-%23F7DF1E.svg?style=flat&logo=javascript&logoColor=black)
![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=flat&logo=php&logoColor=white)
![Composer](https://img.shields.io/badge/composer-%23885630.svg?style=flat&logo=composer&logoColor=white)
![Status](https://img.shields.io/badge/status-active-success.svg)

## ✨ Features

- **Modern UI/UX**: Dark theme with glassmorphism effects and neon accents (Purple/Cyan).
- **Responsive Design**: Fully optimized for desktop, tablet, and mobile devices.
- **Real-Time Server Stats**: CPU, RAM, and disk usage fetched live via the Minestrator WebSocket API (PHP backend).
- **Dynamic Server Config**: Game mode, difficulty, whitelist status — pulled directly from `server.properties` via SFTP.
- **One-Click Copy**: Click-to-copy functionality for the server IP address.
- **Wiki Section**:
  - Complete command guide (TPATools, Homes, Claims).
  - Interactive code blocks (click-to-copy commands).
  - Sticky sidebar navigation.
- **Tutorial Page** (`/comment-rejoindre`): Step-by-step guide to join (CurseForge installation, modpack download).
- **Stats Dashboard** (`/stats`): Dedicated page displaying live server performance metrics.
- **Phosphor Icons**: High-quality, consistent iconography.
- **Clean Codebase**: Semantic HTML5, CSS3 variables, modular JS, and a PHP backend managed with Composer.

## 🚀 Live Demo

[https://bmc4.strator.gg](https://bmc4.strator.gg)

## 🛠️ Requirements

- **Web server** with PHP 8.x support (Apache / Nginx)
- **PHP extensions**: `curl`, `json`, `openssl`
- **Composer** (PHP dependency manager)
- A **Minestrator** hosting account (for API & SFTP credentials)

## ⚙️ Installation

### 1. Clone the repository

```bash
git clone https://github.com/Skyral1/minecraft-server-web-page.git
cd minecraft-server-web-page
```

### 2. Install PHP dependencies

```bash
composer install
```

This installs:
- [`vlucas/phpdotenv`](https://github.com/vlucas/phpdotenv) — `.env` file loader
- [`textalk/websocket`](https://github.com/textalk/websocket-php) — PHP WebSocket client (for Minestrator live stats)

### 3. Configure environment variables

Copy the example file and fill in your credentials:

```bash
cp .env.exemple .env
```

Then edit `.env`:

```env
# Minestrator API
MINESTRATOR_API_TOKEN="YOUR_API_KEY_HERE"
MINESTRATOR_SERVER_ID="YOUR_SERVER_ID_HERE"
MINESTRATOR_API_URL="https://mine.sttr.io/server"

# SFTP access (MyBox / Pterodactyl)
SFTP_HOST="HOSTNAME_OR_IP"
SFTP_PORT="PORT_NUMBER"
SFTP_USER="USERNAME"
SFTP_PASS="PASSWORD"
```

> ⚠️ **Never commit `.env` to version control.** It is already listed in `.gitignore`.

### 4. Customize the content

- Edit `index.html` to update your server name, description, and IP.
- Edit `wiki/index.html` to modify server commands and rules.
- Edit `comment-rejoindre/index.html` to update the join tutorial.
- Edit `server-config.php` to adjust which `server.properties` fields are exposed.
- Modify CSS variables in `assets/css/main.css` and `assets/css/wiki.css` to change the theme.

### 5. Deploy

Upload the full project (including `vendor/`) to your web host:

```bash
rsync -avz --exclude '.env' ./ user@yourhost:/var/www/html/
```

Or use FTP/SFTP. Make sure `.env` is **never** publicly accessible — add the following to your `.htaccess` if needed:

```apache
<Files ".env">
    Require all denied
</Files>
```

## 📂 Project Structure

```text
minecraft-server-web-page/
├── assets/
│   ├── css/
│   │   ├── main.css              # Homepage styles
│   │   └── wiki.css              # Wiki-specific styles
│   ├── js/
│   │   ├── main.js               # Homepage scripts (IP copy, stats fetch)
│   │   └── wiki.js               # Wiki scripts (command copy, sticky nav)
│   └── images/                   # Favicons, backgrounds
├── comment-rejoindre/
│   └── index.html                # Step-by-step join tutorial
├── stats/
│   └── index.html                # Live server stats dashboard
├── wiki/
│   └── index.html                # Commands & Wiki
├── .env.exemple                  # Example environment variables (safe to commit)
├── .gitignore
├── composer.json                 # PHP dependencies declaration
├── composer.lock                 # Locked dependency versions
├── index.html                    # Homepage
├── server-config.php             # PHP API — reads server.properties via SFTP
├── server-stats.php              # PHP API — live stats via Minestrator WebSocket
└── README.md
```

## 🔌 PHP Backend

The backend consists of two PHP endpoints called by the frontend JavaScript:

| Endpoint | Description |
|---|---|
| `server-config.php` | Connects via SFTP (cURL) to read `server.properties` and returns a JSON object with game mode, difficulty, whitelist, view distance, port, etc. |
| `server-stats.php` | Authenticates with the Minestrator REST API, opens a WebSocket connection, and returns real-time CPU / RAM / disk stats as JSON |

Both endpoints load credentials from `.env` via `vlucas/phpdotenv` and respond with `Content-Type: application/json`.

## 🎨 Customization

You can easily change the theme by modifying the `:root` variables in `assets/css/main.css`:

```css
:root {
    --primary: #8c5ce7;                      /* Main purple color */
    --primary-glow: rgba(140, 92, 231, 0.35);
    --accent: #00e6b8;                       /* Cyan accent color */
    --bg: #030305;                           /* Dark background */
    --surface: #0e0e12;                      /* Card background */
    --text-main: #ffffff;
}
```

## 🤝 Contributing

Contributions are welcome! Feel free to submit a Pull Request.

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

Distributed under the MIT License. See `LICENCE` for more information.

---

Made with ❤️ by **[Skyral1](https://github.com/Skyral1)**
