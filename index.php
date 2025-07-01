<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');

// Обработка preflight запросов
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Проверяем метод запроса
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Метод не разрешен']);
    exit();
}

// Получаем данные из POST запроса
$input = json_decode(file_get_contents('php://input'), true);

// Валидация данных
$name = isset($input['name']) ? trim($input['name']) : '';
$email = isset($input['email']) ? trim($input['email']) : '';
$subject = isset($input['subject']) ? trim($input['subject']) : '';
$message = isset($input['message']) ? trim($input['message']) : '';

// Проверка обязательных полей
if (empty($name) || empty($email) || empty($message)) {
    http_response_code(400);
    echo json_encode(['error' => 'Заполните все обязательные поля']);
    exit();
}

// Валидация email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Некорректный email адрес']);
    exit();
}

// Подключаем файлы
require_once 'send_email.php';
require_once 'config.php';

// Получаем IP адрес клиента
$clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

// Проверяем лимит запросов
if (!checkRateLimit($clientIp)) {
    http_response_code(429);
    echo json_encode(['error' => 'Превышен лимит запросов. Попробуйте позже.']);
    exit();
}

// Проверяем длину полей
$securityConfig = getSecurityConfig();
if (strlen($name) > $securityConfig['max_name_length']) {
    http_response_code(400);
    echo json_encode(['error' => 'Имя слишком длинное']);
    exit();
}

if (strlen($subject) > $securityConfig['max_subject_length']) {
    http_response_code(400);
    echo json_encode(['error' => 'Тема слишком длинная']);
    exit();
}

if (strlen($message) > $securityConfig['max_message_length']) {
    http_response_code(400);
    echo json_encode(['error' => 'Сообщение слишком длинное']);
    exit();
}

// Проверяем на спам
if (isSpam($name, $email, $subject, $message)) {
    http_response_code(400);
    echo json_encode(['error' => 'Сообщение заблокировано системой безопасности']);
    exit();
}

// Отправляем email
$result = sendFeedbackEmail($name, $email, $subject, $message);

if ($result['success']) {
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Сообщение успешно отправлено']);
} else {
    http_response_code(500);
    echo json_encode(['error' => $result['error']]);
}
?>
