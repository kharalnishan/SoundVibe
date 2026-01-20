<?php
/**
 * Logout Handler
 * SoundVibe Music Streaming Platform
 */

require_once __DIR__ . '/../includes/auth.php';

logoutUser();

header('Location: /index.php?logged_out=1');
exit;
?>
