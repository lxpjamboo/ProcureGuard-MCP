<?php
// db.php - Sovereign SQLite Connector for ProcureGuard Frontend
$db_path = __DIR__ . '/procureguard.db';

try {
    $pdo = new PDO("sqlite:" . $db_path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Fetch helper functions for frontend views
function get_envelopes($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM bid_envelopes");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return []; // Returns empty array if tables aren't built yet instead of crashing
    }
}

function get_drifts($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM forensic_drifts");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

function get_baselines($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM market_baselines");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

function get_registry($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM corporate_registry");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}
?>