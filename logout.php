<?php
session_start();
session_destroy();
header('Location: login.php?success=' . urlencode('已成功退出登录'));
exit;