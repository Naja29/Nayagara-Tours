<?php
define('DB_HOST', 'localhost');
define('DB_PORT', '3307');        // Change to 3306 on cPanel hosting
define('DB_NAME', 'nayagara_tours');
define('DB_USER', 'root');        // Change to your cPanel DB username
define('DB_PASS', '');            // Change to your cPanel DB password
define('DB_CHARSET', 'utf8mb4');

function getPDO(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die('<div style="font-family:sans-serif;padding:2rem;color:#c0392b;">
                <h2>Database Connection Failed</h2>
                <p>' . htmlspecialchars($e->getMessage()) . '</p>
                <p>Check your <code>admin/config/db.php</code> settings.</p>
            </div>');
        }
    }
    return $pdo;
}
