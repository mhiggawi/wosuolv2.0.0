<?php
// auto_backup.php - سكريبت النسخ الاحتياطي التلقائي
// يمكن تشغيله عبر Cron Job أو يدوياً

require_once 'db_config.php';

class AutoBackup {
    private $mysqli;
    private $backup_dir;
    private $log_file;
    
    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
        $this->backup_dir = 'backups';
        $this->log_file = 'logs/backup_log.txt';
        
        // Create directories if not exist
        if (!is_dir($this->backup_dir)) {
            mkdir($this->backup_dir, 0755, true);
        }
        if (!is_dir('logs')) {
            mkdir('logs', 0755, true);
        }
    }
    
    public function backupAllEvents() {
        $this->log("Starting automatic backup process...");
        
        // Get all events
        $result = $this->mysqli->query("SELECT id, event_name FROM events ORDER BY created_at DESC");
        $events = $result->fetch_all(MYSQLI_ASSOC);
        
        $success_count = 0;
        $total_count = count($events);
        
        foreach ($events as $event) {
            $result = $this->createEventBackup($event['id']);
            if ($result['success']) {
                $success_count++;
                $this->log("✓ Backup created for: " . $event['event_name']);
            } else {
                $this->log("✗ Failed to backup: " . $event['event_name'] . " - " . $result['message']);
            }
        }
        
        $this->log("Backup process completed. $success_count/$total_count events backed up successfully.");
        $this->cleanOldBackups(); // Clean old backups
        
        return [
            'success' => true,
            'total' => $total_count,
            'success_count' => $success_count,
            'failed_count' => $total_count - $success_count
        ];
    }
    
    public function backupSingleEvent($event_id) {
        return $this->createEventBackup($event_id);
    }
    
    private function createEventBackup($event_id) {
        try {
            // Get event data
            $stmt = $this->mysqli->prepare("SELECT * FROM events WHERE id = ?");
            $stmt->bind_param("i", $event_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $event = $result->fetch_assoc();
            $stmt->close();
            
            if (!$event) {
                return ['success' => false, 'message' => 'Event not found'];
            }
            
            // Get all related data
            $backup_data = [
                'backup_info' => [
                    'created_at' => date('Y-m-d H:i:s'),
                    'original_event_id' => $event_id,
                    'version' => '1.0',
                    'system' => 'WOSUOL Events',
                    'backup_type' => 'auto',
                    'event_name' => $event['event_name']
                ],
                'event' => $event,
                'guests' => $this->getEventGuests($event_id),
                'send_results' => $this->getEventSendResults($event_id),
                'reminder_logs' => $this->getEventReminderLogs($event_id),
                'images' => $this->getEventImages($event)
            ];
            
            // Generate filename
            $safe_name = preg_replace('/[^a-zA-Z0-9\-_]/', '_', $event['event_name']);
            $filename = 'auto_backup_' . $safe_name . '_' . date('Y-m-d_H-i-s') . '.json';
            $filepath = $this->backup_dir . '/' . $filename;
            
            // Save backup file
            $json_data = json_encode($backup_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            if (file_put_contents($filepath, $json_data)) {
                return ['success' => true, 'filename' => $filename, 'size' => strlen($json_data)];
            } else {
                return ['success' => false, 'message' => 'Failed to save backup file'];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    private function getEventGuests($event_id) {
        $stmt = $this->mysqli->prepare("SELECT * FROM guests WHERE event_id = ?");
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $guests = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $guests;
    }
    
    private function getEventSendResults($event_id) {
        $stmt = $this->mysqli->prepare("SELECT * FROM send_results WHERE event_id = ?");
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $results = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $results;
    }
    
    private function getEventReminderLogs($event_id) {
        $stmt = $this->mysqli->prepare("SELECT * FROM reminder_logs WHERE event_id = ?");
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $logs = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $logs;
    }
    
    private function getEventImages($event) {
        $images = [];
        $image_fields = ['background_image_url', 'whatsapp_image_url', 'reminder_image_url'];
        
        foreach ($image_fields as $field) {
            if (!empty($event[$field]) && file_exists($event[$field])) {
                try {
                    $image_data = base64_encode(file_get_contents($event[$field]));
                    $images[$field] = [
                        'filename' => basename($event[$field]),
                        'path' => $event[$field],
                        'data' => $image_data,
                        'size' => filesize($event[$field])
                    ];
                } catch (Exception $e) {
                    $this->log("Warning: Could not backup image " . $event[$field] . " - " . $e->getMessage());
                }
            }
        }
        
        return $images;
    }
    
    private function cleanOldBackups($days_to_keep = 30) {
        $this->log("Cleaning old backups (older than $days_to_keep days)...");
        
        $files = glob($this->backup_dir . '/*.json');
        $deleted_count = 0;
        $cutoff_time = time() - ($days_to_keep * 24 * 60 * 60);
        
        foreach ($files as $file) {
            if (filemtime($file) < $cutoff_time) {
                if (unlink($file)) {
                    $deleted_count++;
                    $this->log("Deleted old backup: " . basename($file));
                }
            }
        }
        
        $this->log("Cleanup completed. Deleted $deleted_count old backup files.");
    }
    
    public function getBackupStats() {
        $files = glob($this->backup_dir . '/*.json');
        $total_size = 0;
        $newest = 0;
        $oldest = time();
        
        foreach ($files as $file) {
            $total_size += filesize($file);
            $mtime = filemtime($file);
            if ($mtime > $newest) $newest = $mtime;
            if ($mtime < $oldest) $oldest = $mtime;
        }
        
        return [
            'total_backups' => count($files),
            'total_size' => $total_size,
            'total_size_formatted' => $this->formatFileSize($total_size),
            'newest_backup' => $newest ? date('Y-m-d H:i:s', $newest) : null,
            'oldest_backup' => $oldest != time() ? date('Y-m-d H:i:s', $oldest) : null
        ];
    }
    
    private function formatFileSize($size) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $unit = 0;
        while ($size >= 1024 && $unit < 3) {
            $size /= 1024;
            $unit++;
        }
        return round($size, 2) . ' ' . $units[$unit];
    }
    
    private function log($message) {
        $timestamp = date('Y-m-d H:i:s');
        $log_entry = "[$timestamp] $message" . PHP_EOL;
        file_put_contents($this->log_file, $log_entry, FILE_APPEND | LOCK_EX);
        echo $log_entry; // Also output to console
    }
}

// Usage examples:

// If called directly from command line or cron
if (php_sapi_name() === 'cli' || !isset($_SERVER['HTTP_HOST'])) {
    $backup = new AutoBackup($mysqli);
    
    // Check command line arguments
    $action = $argv[1] ?? 'all';
    
    switch ($action) {
        case 'all':
            echo "Starting backup of all events...\n";
            $result = $backup->backupAllEvents();
            echo "Backup completed: {$result['success_count']}/{$result['total']} events\n";
            break;
            
        case 'single':
            if (!isset($argv[2])) {
                echo "Usage: php auto_backup.php single <event_id>\n";
                exit(1);
            }
            $event_id = (int)$argv[2];
            echo "Starting backup of event ID: $event_id\n";
            $result = $backup->backupSingleEvent($event_id);
            echo $result['success'] ? "Backup successful\n" : "Backup failed: {$result['message']}\n";
            break;
            
        case 'stats':
            $stats = $backup->getBackupStats();
            echo "Backup Statistics:\n";
            echo "Total backups: {$stats['total_backups']}\n";
            echo "Total size: {$stats['total_size_formatted']}\n";
            echo "Newest backup: {$stats['newest_backup']}\n";
            echo "Oldest backup: {$stats['oldest_backup']}\n";
            break;
            
        default:
            echo "Usage: php auto_backup.php [all|single|stats] [event_id]\n";
            echo "  all    - Backup all events\n";
            echo "  single - Backup specific event (requires event_id)\n";
            echo "  stats  - Show backup statistics\n";
            break;
    }
}

// If called via web (for API access)
if (isset($_GET['action']) && isset($_GET['key'])) {
    // Simple API key check (replace with your own secure method)
    $api_key = 'wosuol_backup_2024'; // Change this to a secure key
    
    if ($_GET['key'] !== $api_key) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
    
    header('Content-Type: application/json');
    
    $backup = new AutoBackup($mysqli);
    
    switch ($_GET['action']) {
        case 'backup_all':
            $result = $backup->backupAllEvents();
            echo json_encode($result);
            break;
            
        case 'backup_event':
            if (!isset($_GET['event_id'])) {
                echo json_encode(['error' => 'event_id required']);
                exit;
            }
            $result = $backup->backupSingleEvent((int)$_GET['event_id']);
            echo json_encode($result);
            break;
            
        case 'stats':
            $stats = $backup->getBackupStats();
            echo json_encode($stats);
            break;
            
        default:
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
}

?>