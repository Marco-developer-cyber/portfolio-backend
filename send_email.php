<?php
require_once 'config.php';

function sendFeedbackEmail($name, $email, $subject, $message) {
    // Получаем конфигурацию
    $config = getEmailConfig();
    
    // Подготавливаем данные для отправки
    $to = $config['to_email'];
    $from = $config['from_email'];
    $fromName = $config['from_name'];
    
    // Формируем тему письма
    $emailSubject = !empty($subject) ? "Обратная связь: " . $subject : "Новое сообщение с сайта";
    
    // Формируем тело письма
    $emailBody = "
    <html>
    <head>
        <title>Новое сообщение с сайта</title>
        <meta charset='UTF-8'>
    </head>
    <body>
        <h2>Новое сообщение с сайта</h2>
        <p><strong>Имя:</strong> " . htmlspecialchars($name) . "</p>
        <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
        <p><strong>Тема:</strong> " . htmlspecialchars($subject) . "</p>
        <p><strong>Сообщение:</strong></p>
        <div style='background-color: #f5f5f5; padding: 15px; border-left: 4px solid #007bff; margin: 10px 0;'>
            " . nl2br(htmlspecialchars($message)) . "
        </div>
        <hr>
        <p><small>Отправлено: " . date('d.m.Y H:i:s') . "</small></p>
    </body>
    </html>";
    
    // Заголовки для HTML письма
    $headers = array(
        'MIME-Version' => '1.0',
        'Content-type' => 'text/html; charset=UTF-8',
        'From' => $fromName . ' <' . $from . '>',
        'Reply-To' => $email,
        'X-Mailer' => 'PHP/' . phpversion()
    );
    
    // Преобразуем массив заголовков в строку
    $headerString = '';
    foreach ($headers as $key => $value) {
        $headerString .= $key . ': ' . $value . "\r\n";
    }
    
    // Отправляем письмо
    try {
        $result = mail($to, $emailSubject, $emailBody, $headerString);
        
        if ($result) {
            // Логируем успешную отправку
            logMessage("Email успешно отправлен от: $email");
            return ['success' => true];
        } else {
            // Логируем ошибку
            logMessage("Ошибка отправки email от: $email");
            return ['success' => false, 'error' => 'Не удалось отправить сообщение'];
        }
    } catch (Exception $e) {
        // Логируем исключение
        logMessage("Исключение при отправке email: " . $e->getMessage());
        return ['success' => false, 'error' => 'Произошла ошибка при отправке сообщения'];
    }
}

function logMessage($message) {
    $logFile = 'logs/email.log';
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] $message" . PHP_EOL;
    
    // Создаем папку logs если её нет
    if (!file_exists('logs')) {
        mkdir('logs', 0755, true);
    }
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}
?>
