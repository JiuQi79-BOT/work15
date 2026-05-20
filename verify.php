<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>账号激活</title>
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
            margin-bottom: 30px;
        }
        .message {
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 16px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: transform 0.2s;
        }
        a:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        require_once __DIR__ . '/functions.php';

        $user = $_GET['user'] ?? '';
        $code = $_GET['code'] ?? '';

        if (empty($user) || empty($code)) {
            echo '<div class="message error">无效的激活链接</div>';
            echo '<a href="index.php">返回注册页面</a>';
            exit;
        }

        try {
            $pdo = getDBConnection();

            $stmt = $pdo->prepare("
                SELECT id, nickname, email, is_active, expires_at, verify_code
                FROM users
                WHERE nickname = ? AND verify_code = ?
            ");
            $stmt->execute([$user, $code]);
            $userData = $stmt->fetch();

            if (!$userData) {
                echo '<div class="message error">激活链接无效或用户不存在</div>';
                echo '<a href="index.php">返回注册页面</a>';
                exit;
            }

            if ($userData['is_active']) {
                echo '<div class="message info">该账号已经激活过了</div>';
                echo '<a href="login.php">前往登录</a>';
                exit;
            }

            if (strtotime($userData['expires_at']) < time()) {
                echo '<div class="message error">激活链接已过期，昵称已释放</div>';
                echo '<a href="index.php">返回注册页面</a>';
                exit;
            }

            $stmt = $pdo->prepare("UPDATE users SET is_active = 1 WHERE id = ?");
            $stmt->execute([$userData['id']]);

            echo '<div class="message success">';
            echo '<h2>激活成功！</h2>';
            echo '<p>恭喜 ' . htmlspecialchars($userData['nickname']) . '，您的账号已成功激活。</p>';
            echo '</div>';
            echo '<a href="login.php">前往登录</a>';

        } catch (Exception $e) {
            echo '<div class="message error">激活失败: ' . htmlspecialchars($e->getMessage()) . '</div>';
            echo '<a href="index.php">返回注册页面</a>';
        }
        ?>
    </div>
</body>
</html>