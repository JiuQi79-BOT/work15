# 用户注册与邮箱激活系统

一套基于PHP的用户注册、邮箱验证激活系统。

## 功能特性

- 用户注册（昵称、口令、邮箱）
- 邮箱激活链接验证
- 1小时未激活自动释放昵称
- 用户登录/退出
- 过期用户自动清理

## 环境要求

- PHP 7.4+
- MySQL 5.7+
- Web服务器（Apache/Nginx）或PHP内置服务器
- 邮件服务（mail函数或SMTP）

## 文件结构

```
d:\MyGit\work15\
├── config.php          # 配置文件
├── functions.php       # 核心函数库
├── db.sql              # 数据库结构
├── index.php           # 注册页面
├── register.php        # 注册处理
├── verify.php          # 激活验证
├── login.php           # 登录页面
├── login_process.php    # 登录处理
├── welcome.php         # 欢迎页面
├── logout.php          # 退出登录
├── cleanup.php          # 清理过期用户
└── README.md           # 说明文档
```

## 安装步骤

### 1. 创建数据库

登录MySQL，执行以下命令创建数据库和表：

```sql
CREATE DATABASE user_system DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE user_system;
```

然后执行 [db.sql](file:///d:/MyGit/work15/db.sql) 中的SQL语句，或直接：

```sql
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nickname` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `verify_code` VARCHAR(64) NOT NULL,
    `is_active` TINYINT(1) DEFAULT 0,
    `created_at` DATETIME NOT NULL,
    `expires_at` DATETIME NOT NULL,
    INDEX `idx_nickname` (`nickname`),
    INDEX `idx_email` (`email`),
    INDEX `idx_verify_code` (`verify_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 2. 修改配置文件

编辑 [config.php](file:///d:/MyGit/work15/config.php)：

```php
define('DB_HOST', 'localhost');       // 数据库主机
define('DB_NAME', 'user_system');     // 数据库名
define('DB_USER', 'root');            // 数据库用户名
define('DB_PASS', 'your_password');   // 数据库密码
define('SITE_URL', 'http://127.0.0.1'); // 网站URL
define('VERIFY_EXPIRE_HOURS', 1);     // 激活链接有效期（小时）
```

### 3. 启动Web服务器

**方式一：PHP内置服务器（推荐用于开发）**

```bash
cd d:\MyGit\work15
php -S 127.0.0.1:80
```

**方式二：Apache/Nginx**

配置虚拟主机指向 `d:\MyGit\work15` 目录。

### 4. 配置邮件服务

系统使用PHP的 `mail()` 函数发送邮件。确保：

- Linux服务器配置好sendmail或postfix
- Windows服务器配置好SMTP（如使用PHPMailer）

如需使用SMTP发送邮件，可集成 [PHPMailer](https://github.com/PHPMailer/PHPMailer)。

### 5. 配置定时任务（清理过期用户）

**Linux (crontab)：**

```bash
*/5 * * * * php /path/to/cleanup.php
```

**Windows (任务计划程序)：**

```cmd
php d:\MyGit\work15\cleanup.php
```

建议每5分钟执行一次，清理未激活且已过期的用户。

## 使用流程

### 用户注册

1. 访问 `http://127.0.0.1/`
2. 填写昵称、口令、邮箱
3. 点击"提交注册"
4. 系统发送激活邮件到邮箱

### 激活账号

1. 登录邮箱，查收激活邮件
2. 点击邮件中的链接，格式如：
   ```
   http://127.0.0.1/verify.php?user=hw&code=ABCDEF
   ```
3. 激活成功，进入登录页面

### 用户登录

1. 访问 `http://127.0.0.1/login.php`
2. 输入昵称和口令
3. 登录成功进入欢迎页面

## 激活链接说明

- 验证码由用户名通过SHA256哈希运算生成
- 格式：`http://127.0.0.1/verify.php?user=用户名&code=验证码`
- 有效期：`VERIFY_EXPIRE_HOURS` 配置的时间（默认1小时）
- 过期后昵称自动释放，可被其他用户注册

## 安全建议

1. 生产环境务必使用HTTPS
2. 修改数据库默认端口
3. 使用强密码策略
4. 考虑添加图形验证码防止恶意注册
5. 限制同一IP的注册频率
