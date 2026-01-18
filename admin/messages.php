<?php
/**
 * Admin - Messages
 * SoundVibe Music Streaming Platform
 */

require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

$pdo = getDBConnection();
$message = '';
$messageType = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    $action = $_POST['action'] ?? '';
    $messageId = (int)($_POST['message_id'] ?? 0);
    
    if ($messageId > 0) {
        switch ($action) {
            case 'mark_read':
                $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
                $stmt->execute([$messageId]);
                $message = 'Message marked as read.';
                $messageType = 'success';
                break;
                
            case 'mark_unread':
                $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = 0 WHERE id = ?");
                $stmt->execute([$messageId]);
                $message = 'Message marked as unread.';
                $messageType = 'success';
                break;
                
            case 'delete':
                $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
                $stmt->execute([$messageId]);
                $message = 'Message deleted.';
                $messageType = 'success';
                break;
        }
    }
}

// Get all messages
$filter = sanitize($_GET['filter'] ?? '');

$sql = "SELECT * FROM contact_messages";
if ($filter === 'unread') {
    $sql .= " WHERE is_read = 0";
} elseif ($filter === 'read') {
    $sql .= " WHERE is_read = 1";
}
$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->query($sql);
$messages = $stmt->fetchAll();

// View single message
$viewMessage = null;
if (isset($_GET['view'])) {
    $viewId = (int)$_GET['view'];
    $stmt = $pdo->prepare("SELECT * FROM contact_messages WHERE id = ?");
    $stmt->execute([$viewId]);
    $viewMessage = $stmt->fetch();
    
    // Mark as read
    if ($viewMessage && !$viewMessage['is_read']) {
        $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?")->execute([$viewId]);
        $viewMessage['is_read'] = 1;
    }
}

$pageTitle = 'Contact Messages';
include __DIR__ . '/../includes/header.php';
?>

        <section class="admin-section">
            <div class="admin-header">
                <h1>Contact Messages</h1>
                <a href="/admin/dashboard.php" class="back-link">← Back to Dashboard</a>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>" role="alert">
                    <span class="alert-icon"><?php echo $messageType === 'success' ? '✓' : '⚠️'; ?></span>
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if ($viewMessage): ?>
                <!-- View Single Message -->
                <div class="admin-panel message-view">
                    <div class="message-header">
                        <h2><?php echo htmlspecialchars($viewMessage['subject']); ?></h2>
                        <a href="/admin/messages.php" class="btn btn-secondary">← Back to List</a>
                    </div>
                    <div class="message-meta">
                        <p><strong>From:</strong> <?php echo htmlspecialchars($viewMessage['name']); ?> (<?php echo htmlspecialchars($viewMessage['email']); ?>)</p>
                        <p><strong>Date:</strong> <?php echo date('F j, Y \a\t g:i A', strtotime($viewMessage['created_at'])); ?></p>
                    </div>
                    <div class="message-body">
                        <?php echo nl2br(htmlspecialchars($viewMessage['message'])); ?>
                    </div>
                    <div class="message-actions">
                        <a href="mailto:<?php echo htmlspecialchars($viewMessage['email']); ?>?subject=Re: <?php echo htmlspecialchars($viewMessage['subject']); ?>" class="btn btn-primary">
                            Reply via Email
                        </a>
                        <form method="POST" class="inline-form">
                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                            <input type="hidden" name="message_id" value="<?php echo $viewMessage['id']; ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this message?');">Delete</button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <!-- Message List -->
                <div class="admin-filters">
                    <a href="/admin/messages.php" class="filter-link <?php echo !$filter ? 'active' : ''; ?>">All</a>
                    <a href="/admin/messages.php?filter=unread" class="filter-link <?php echo $filter === 'unread' ? 'active' : ''; ?>">Unread</a>
                    <a href="/admin/messages.php?filter=read" class="filter-link <?php echo $filter === 'read' ? 'active' : ''; ?>">Read</a>
                </div>

                <div class="admin-panel">
                    <?php if (empty($messages)): ?>
                        <p class="no-data">No messages found.</p>
                    <?php else: ?>
                        <table class="admin-table full-width">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>From</th>
                                    <th>Subject</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($messages as $msg): ?>
                                    <tr class="<?php echo !$msg['is_read'] ? 'unread' : ''; ?>">
                                        <td>
                                            <?php if ($msg['is_read']): ?>
                                                <span class="status-badge status-read">Read</span>
                                            <?php else: ?>
                                                <span class="status-badge status-unread">New</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($msg['name']); ?></strong><br>
                                            <small><?php echo htmlspecialchars($msg['email']); ?></small>
                                        </td>
                                        <td>
                                            <a href="/admin/messages.php?view=<?php echo $msg['id']; ?>">
                                                <?php echo htmlspecialchars($msg['subject']); ?>
                                            </a>
                                        </td>
                                        <td><?php echo date('M j, Y H:i', strtotime($msg['created_at'])); ?></td>
                                        <td>
                                            <a href="/admin/messages.php?view=<?php echo $msg['id']; ?>" class="btn-sm btn-primary">View</a>
                                            <form method="POST" class="inline-form">
                                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                                <input type="hidden" name="action" value="<?php echo $msg['is_read'] ? 'mark_unread' : 'mark_read'; ?>">
                                                <button type="submit" class="btn-sm btn-secondary">
                                                    <?php echo $msg['is_read'] ? 'Mark Unread' : 'Mark Read'; ?>
                                                </button>
                                            </form>
                                            <form method="POST" class="inline-form" onsubmit="return confirm('Delete this message?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <button type="submit" class="btn-sm btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
