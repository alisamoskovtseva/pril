<?php
// Настройки подключения к базе данных
$servername = "localhost";
$username = "root";
$password = "usbw";
$dbname = "my_blog_db";

// Обработка отправки формы
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Ошибка подключения: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
    
    // Получение и очистка данных
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $subject = htmlspecialchars(trim($_POST['subject'] ?? 'Без темы'));
    $message = htmlspecialchars(trim($_POST['message']));
    
    // Валидация email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Неверный формат email";
    } else {
        // Подготовленный запрос для защиты от SQL-инъекций
        $stmt = $conn->prepare("INSERT INTO contacts (name, email, subject, message, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssss", $name, $email, $subject, $message);
        
        if ($stmt->execute()) {
            $success = "Сообщение успешно отправлено!";
        } else {
            $error = "Ошибка при сохранении: " . $conn->error;
        }
        
        $stmt->close();
    }
    
    $conn->close();
}

// Получение всех сообщений из базы
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
$result = $conn->query("SELECT * FROM contacts ORDER BY created_at DESC");
$contacts = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="manifest" href="/manifest.json">
	<meta name="theme-color" content="#3498db">
    <title>Мой Блог | Контакты</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
        }
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        header {
            background-color: #2c3e50;
            color: #fff;
            padding: 1rem 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        nav a {
            color: #fff;
            text-decoration: none;
            margin-left: 1.5rem;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        nav a:hover {
            color: #3498db;
        }
        
        main {
            padding: 2rem 0;
            min-height: 70vh;
        }
        
        h1, h2, h3 {
            color: #34495e;
            margin-bottom: 1rem;
        }
        
        /* Стили формы */
        .contact-form {
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .form-group textarea {
            min-height: 150px;
        }
        
        .btn {
            display: inline-block;
            background: #3498db;
            color: #fff;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #2980b9;
        }
        
        /* Стили уведомлений */
        .notification {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            color: white;
        }
        
        .success {
            background-color: #2ecc71;
        }
        
        .error {
            background-color: #e74c3c;
        }
        
        /* Стили таблицы сообщений */
        .messages-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
            background: #fff;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .messages-table th,
        .messages-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .messages-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
        .messages-table tr:hover {
            background-color: #f5f5f5;
        }
        
        /* Адаптивность */
        @media (max-width: 768px) {
            header .container {
                flex-direction: column;
                text-align: center;
            }
            
            nav {
                margin-top: 1rem;
            }
            
            nav a {
                margin: 0 0.8rem;
            }
            
            .messages-table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Мой Блог</h1>
            <nav>
                <a href="index.html">Главная</a>
                <a href="about.html">Обо мне</a>
                <a href="portfolio.html">Портфолио</a>
                <a href="blog.html">Блог</a>
                <a href="contact.php">Контакты</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <h2>Свяжитесь со мной</h2>
        
        <?php if (isset($success)): ?>
            <div class="notification success"><?= $success ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="notification error"><?= $error ?></div>
        <?php endif; ?>
        
        <div class="contact-info">
            <div class="contact-method">
                <h3>Email</h3>
                <p>alice21022007@mail.ru</p>
            </div>
            <div class="contact-method">
                <h3>Телефон</h3>
                <p>+7 910 996 22 43</p>
            </div>
            <div class="contact-method">
                <h3>Соцсети</h3>
                <div class="social-links">
                    <a href="https://t.me/rryqqq" target="_blank">Телеграм</a>
                    <a href="https://vk.com/rryqqq" target="_blank">Вконтакте</a>
                    <a href="https://github.com/alisamoskovtseva" target="_blank">GitHub</a>
                </div>
            </div>
        </div>
        
        <form class="contact-form" method="POST">
            <div class="form-group">
                <label for="name">Ваше имя:</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="subject">Тема:</label>
                <input type="text" id="subject" name="subject">
            </div>
            
            <div class="form-group">
                <label for="message">Сообщение:</label>
                <textarea id="message" name="message" rows="5" required></textarea>
            </div>
            
            <button type="submit" class="btn">Отправить сообщение</button>
        </form>
        
        <h3>Последние обращения</h3>
        <?php if (!empty($contacts)): ?>
            <table class="messages-table">
                <thead>
                    <tr>
                        <th>Имя</th>
                        <th>Email</th>
                        <th>Тема</th>
                        <th>Сообщение</th>
                        <th>Дата</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contacts as $contact): ?>
                    <tr>
                        <td><?= htmlspecialchars($contact['name']) ?></td>
                        <td><?= htmlspecialchars($contact['email']) ?></td>
                        <td><?= htmlspecialchars($contact['subject']) ?></td>
                        <td><?= htmlspecialchars($contact['message']) ?></td>
                        <td><?= date('d.m.Y H:i', strtotime($contact['created_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Пока нет отправленных сообщений.</p>
        <?php endif; ?>
    </main>
</body>
</html>