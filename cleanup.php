<?php
require_once __DIR__ . '/functions.php';

try {
    $deletedCount = cleanupExpiredUsers();
    echo "已清理 {$deletedCount} 个过期用户\n";
} catch (Exception $e) {
    echo "清理失败: " . $e->getMessage() . "\n";
    exit(1);
}