<?php
require_once __DIR__ . '/functions.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$nickname = trim($_POST['nickname'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($nickname) || empty($password)) {
    header('Location: login.php?error=' . urlencode('昵称和口令都不能为空'));
    exit;
}

try {
    $pdo = getDBConnection();

    $stmt = $pdo->prepare("SELECT id, nickname, password, is_active FROM users WHERE nickname = ?");
    $stmt->execute([$nickname]);
    $user = $stmt->fetch();

    if (!$user) {
        header('Location: login.php?error=' . urlencode('用户不存在'));
        exit;
    }

    if (!$user['is_active']) {
        header('Location: login.php?error=' . urlencode('账号未激活，请先激活账号'));
        exit;
    }

    if (!password_verify($password, $user['password'])) {
        header('Location: login.php?error=' . urlencode('口令错误'));
        exit;
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_nickname'] = $user['nickname'];

    header('Location: welcome.php');
    exit;

} catch (Exception $e) {
    header('Location: login.php?error=' . urlencode('登录失败: ' . $e->getMessage()));
    exit;
}