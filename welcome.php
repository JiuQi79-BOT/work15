<?php
require_once __DIR__ . '/functions.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?error=' . urlencode('请先登录'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>欢迎页面</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        p {
            color: #666;
            margin-bottom: 10px;
            font-size: 18px;
        }
        .logout-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 30px;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .logout-btn:hover {
            background: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>欢迎回来！</h1>
        <p>您好，<?= htmlspecialchars($_SESSION['user_nickname']) ?>！</p>
        <p>您已成功登录。</p>
        <a href="logout.php" class="logout-btn">退出登录</a>
    </div>
</body>
</html>