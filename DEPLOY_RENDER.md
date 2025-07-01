# Развертывание PHP Backend на Render

## Пошаговая инструкция

### 1. Подготовка репозитория

1. **Создайте новый репозиторий на GitHub:**
   - Зайдите на GitHub.com
   - Нажмите "New repository"
   - Назовите репозиторий, например: `feedback-backend`
   - Сделайте его публичным
   - Нажмите "Create repository"

2. **Загрузите файлы в репозиторий:**
   ```bash
   # В папке feedback-backend выполните:
   git init
   git add .
   git commit -m "Initial commit"
   git branch -M main
   git remote add origin https://github.com/YOUR_USERNAME/feedback-backend.git
   git push -u origin main
   ```

### 2. Развертывание на Render

1. **Зайдите на Render.com:**
   - Перейдите на https://render.com
   - Зарегистрируйтесь или войдите в аккаунт
   - Подключите свой GitHub аккаунт

2. **Создайте новый Web Service:**
   - Нажмите "New +" → "Web Service"
   - Выберите ваш репозиторий `feedback-backend`
   - Нажмите "Connect"

3. **Настройте параметры развертывания:**
   - **Name:** `feedback-backend` (или любое другое имя)
   - **Environment:** `PHP`
   - **Build Command:** `composer install --no-dev --optimize-autoloader`
   - **Start Command:** `php -S 0.0.0.0:$PORT -t .`
   - **Plan:** выберите "Free" (бесплатный план)

4. **Нажмите "Create Web Service"**

### 3. Настройка переменных окружения (опционально)

Если нужно настроить дополнительные параметры:
- В панели Render перейдите в "Environment"
- Добавьте переменные если необходимо

### 4. Получение URL

После успешного развертывания:
- Render предоставит вам URL вида: `https://your-service-name.onrender.com`
- Ваш API будет доступен по адресу: `https://your-service-name.onrender.com/index.php`

### 5. Обновление frontend на Netlify

В вашем React приложении обновите URL для API:

```javascript
// Замените localhost на ваш Render URL
const API_URL = 'https://your-service-name.onrender.com/index.php';

fetch(API_URL, {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify(formData)
})
```

### 6. Тестирование

1. **Проверьте работу API:**
   - Откройте `https://your-service-name.onrender.com/test.html`
   - Заполните форму и отправьте тестовое сообщение

2. **Проверьте интеграцию с frontend:**
   - Откройте ваш сайт на Netlify
   - Попробуйте отправить сообщение через форму обратной связи

## Важные моменты

### Особенности Render:
- **Холодный старт:** Бесплатные сервисы засыпают после 15 минут неактивности
- **Первый запрос:** Может занять 30-60 секунд после периода неактивности
- **Логи:** Доступны в панели Render для отладки

### Функция mail() на Render:
- Render может не поддерживать стандартную функцию `mail()`
- Рекомендуется использовать внешние SMTP сервисы (Gmail, SendGrid, Mailgun)

### Альтернативная настройка SMTP (рекомендуется):

Если стандартная функция `mail()` не работает, можно использовать PHPMailer:

1. Добавьте в `composer.json`:
```json
{
    "require": {
        "php": ">=7.4",
        "phpmailer/phpmailer": "^6.8"
    }
}
```

2. Обновите код отправки email для использования SMTP

## Устранение неполадок

### Проблема: Сервис не запускается
- Проверьте логи в панели Render
- Убедитесь, что все файлы загружены в репозиторий

### Проблема: CORS ошибки
- Убедитесь, что заголовки CORS настроены правильно
- Проверьте, что frontend использует правильный URL

### Проблема: Email не отправляются
- Проверьте логи на наличие ошибок
- Рассмотрите использование внешнего SMTP сервиса

## Мониторинг

- **Логи:** Доступны в панели Render
- **Метрики:** Render предоставляет базовую аналитику
- **Уведомления:** Можно настроить уведомления о сбоях

## Обновление

Для обновления кода:
1. Внесите изменения в локальный репозиторий
2. Сделайте commit и push в GitHub
3. Render автоматически пересоберет и развернет новую версию
