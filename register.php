<?php
require_once __DIR__ . '/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$nickname = trim($_POST['nickname'] ?? '');
$password = $_POST['password'] ?? '';
$email = trim($_POST['email'] ?? '');

if (empty($nickname) || empty($password) || empty($email)) {
    header('Location: index.php?error=' . urlencode('所有字段都不能为空'));
    exit;
}

if (strlen($nickname) < 2 || strlen($nickname) > 50) {
    header('Location: index.php?error=' . urlencode('昵称长度必须在2-50个字符之间'));
    exit;
}

if (strlen($password) < 6 || strlen($password) > 50) {
    header('Location: index.php?error=' . urlencode('口令长度必须在6-50个字符之间'));
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: index.php?error=' . urlencode('邮箱格式不正确'));
    exit;
}

try {
    $pdo = getDBConnection();

    $stmt = $pdo->prepare("SELECT id FROM users WHERE nickname = ?");
    $stmt->execute([$nickname]);
    if ($stmt->fetch()) {
        header('Location: index.php?error=' . urlencode('该昵称已被占用'));
        exit;
    }

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        header('Location: index.php?error=' . urlencode('该邮箱已被注册'));
        exit;
    }

    $verifyCode = generateVerifyCode($nickname);
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $createdAt = date('Y-m-d H:i:s');
    $expiresAt = date('Y-m-d H:i:s', strtotime('+' . VERIFY_EXPIRE_HOURS . ' hours'));

    $stmt = $pdo->prepare("
        INSERT INTO users (nickname, password, email, verify_code, is_active, created_at, expires_at)
        VALUES (?, ?, ?, ?, 0, ?, ?)
    ");
    $stmt->execute([$nickname, $hashedPassword, $email, $verifyCode, $createdAt, $expiresAt]);

    if (sendVerificationEmail($email, $nickname, $verifyCode)) {
        header('Location: index.php?success=' . urlencode('注册成功！请登录邮箱点击链接激活账号，' . VERIFY_EXPIRE_HOURS . '小时内未激活将释放昵称'));
        exit;
    } else {
        header('Location: index.php?error=' . urlencode('注册成功，但发送激活邮件失败'));
        exit;
    }

} catch (Exception $e) {
    header('Location: index.php?error=' . urlencode('注册失败: ' . $e->getMessage()));
    exit;
}