<?php
require_once __DIR__ . '/../includes/auth.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Read JSON body
$body = json_decode(file_get_contents('php://input'), true);
$artistId = isset($body['artist_id']) ? intval($body['artist_id']) : 0;
action:
$action = isset($body['action']) ? $body['action'] : '';
$csrf = $body['csrf_token'] ?? '';

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

if (!verifyCSRFToken($csrf)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

$user = getCurrentUser();
$conn = getDBConnection();

try {
    if ($action === 'follow') {
        // Prevent duplicates
        $stmt = $conn->prepare("SELECT id FROM user_favorites WHERE user_id = ? AND artist_id = ?");
        $stmt->execute([$user['id'], $artistId]);
        if (!$stmt->fetch()) {
            $ins = $conn->prepare("INSERT INTO user_favorites (user_id, artist_id, created_at) VALUES (?, ?, CURRENT_TIMESTAMP)");
            $ins->execute([$user['id'], $artistId]);

            // Increment followers
            $upd = $conn->prepare("UPDATE artists SET followers = followers + 1 WHERE id = ?");
            $upd->execute([$artistId]);
        }

        // Return current followers count
        $c = $conn->prepare("SELECT followers FROM artists WHERE id = ?");
        $c->execute([$artistId]);
        $count = $c->fetchColumn();

        echo json_encode(['success' => true, 'following' => true, 'followers_count' => intval($count), 'following_user' => $user['id']]);
        exit;
    } elseif ($action === 'unfollow') {
        $del = $conn->prepare("DELETE FROM user_favorites WHERE user_id = ? AND artist_id = ?");
        $del->execute([$user['id'], $artistId]);

        // Decrement followers safely
        $upd = $conn->prepare("UPDATE artists SET followers = GREATEST(followers - 1, 0) WHERE id = ?");
        $upd->execute([$artistId]);

        $c = $conn->prepare("SELECT followers FROM artists WHERE id = ?");
        $c->execute([$artistId]);
        $count = $c->fetchColumn();

        echo json_encode(['success' => true, 'following' => false, 'followers_count' => intval($count)]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['error' => 'Invalid action']);
    exit;
} catch (PDOException $e) {
    error_log('Follow API error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
    exit;
}
