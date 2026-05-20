<?php
require_once __DIR__ . '/config.php';

function getDBConnection(): PDO {
    try {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        return $pdo;
    } catch (PDOException $e) {
        die('数据库连接失败: ' . $e->getMessage());
    }
}

function generateVerifyCode(string $username): string {
    return strtoupper(substr(hash('sha256', $username . time() . random_bytes(16)), 0, 8));
}

function sendVerificationEmail(string $email, string $nickname, string $verifyCode): bool {
    $verifyUrl = SITE_URL . '/verify.php?user=' . urlencode($nickname) . '&code=' . $verifyCode;

    $subject = '=?UTF-8?B?' . base64_encode('会员激活邮件') . '?=';
    $headers = "From: noreply@localhost\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    $body = "
    <html>
    <body>
        <h2>亲爱的 {$nickname}，您好！</h2>
        <p>感谢您注册我们的会员，请点击以下链接激活您的账号：</p>
        <p><a href='{$verifyUrl}'>{$verifyUrl}</a></p>
        <p>该链接将在 " . VERIFY_EXPIRE_HOURS . " 小时后失效，请尽快激活。</p>
        <p>如果无法点击链接，请复制到浏览器地址栏访问。</p>
    </body>
    </html>
    ";

    return mail($email, $subject, $body, $headers);
}

function cleanupExpiredUsers(): int {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("DELETE FROM users WHERE is_active = 0 AND expires_at < NOW()");
    $stmt->execute();
    return $stmt->rowCount();
}