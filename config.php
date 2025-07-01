<?php
function getEmailConfig() {
    return [
        'to_email' => 'muxammad.001.com@gmail.com',
        'from_email' => 'noreply@yoursite.com',
        'from_name' => 'Сайт портфолио'
    ];
}

// Настройки безопасности
function getSecurityConfig() {
    return [
        'max_message_length' => 5000,
        'max_name_length' => 100,
        'max_subject_length' => 200,
        'rate_limit_per_ip' => 5, // максимум 5 сообщений в час с одного IP
        'blocked_words' => ['spam', 'viagra', 'casino'] // список запрещенных слов
    ];
}

// Проверка на спам
function isSpam($name, $email, $subject, $message) {
    $securityConfig = getSecurityConfig();
    $blockedWords = $securityConfig['blocked_words'];
    
    $content = strtolower($name . ' ' . $email . ' ' . $subject . ' ' . $message);
    
    foreach ($blockedWords as $word) {
        if (strpos($content, strtolower($word)) !== false) {
            return true;
        }
    }
    
    // Проверка на подозрительные паттерны
    if (preg_match('/http[s]?:\/\//', $message)) {
        return true; // блокируем сообщения с ссылками
    }
    
    return false;
}

// Проверка лимита запросов
function checkRateLimit($ip) {
    $logFile = 'logs/rate_limit.log';
    $currentTime = time();
    $oneHourAgo = $currentTime - 3600;
    
    // Создаем папку logs если её нет
    if (!file_exists('logs')) {
        mkdir('logs', 0755, true);
    }
    
    // Читаем существующие записи
    $requests = [];
    if (file_exists($logFile)) {
        $lines = file($logFile, FILE_IGNORE_NEW_LINES);
        foreach ($lines as $line) {
            $parts = explode('|', $line);
            if (count($parts) === 2) {
                $timestamp = intval($parts[0]);
                $requestIp = $parts[1];
                
                // Оставляем только записи за последний час
                if ($timestamp > $oneHourAgo) {
                    $requests[] = ['timestamp' => $timestamp, 'ip' => $requestIp];
                }
            }
        }
    }
    
    // Считаем запросы с текущего IP за последний час
    $ipRequests = array_filter($requests, function($request) use ($ip) {
        return $request['ip'] === $ip;
    });
    
    $securityConfig = getSecurityConfig();
    if (count($ipRequests) >= $securityConfig['rate_limit_per_ip']) {
        return false; // превышен лимит
    }
    
    // Добавляем текущий запрос
    $requests[] = ['timestamp' => $currentTime, 'ip' => $ip];
    
    // Записываем обновленный лог
    $logContent = '';
    foreach ($requests as $request) {
        $logContent .= $request['timestamp'] . '|' . $request['ip'] . "\n";
    }
    file_put_contents($logFile, $logContent);
    
    return true;
}
?>
