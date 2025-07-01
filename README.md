# PHP Backend для обратной связи

Простой и безопасный PHP backend для обработки форм обратной связи с отправкой на email.

## Структура проекта

```
feedback-backend/
├── index.php          # Основной файл API
├── send_email.php     # Логика отправки email
├── config.php         # Конфигурация и настройки безопасности
├── .htaccess          # Настройки Apache и безопасность
├── logs/              # Папка для логов (создается автоматически)
└── README.md          # Документация
```

## Настройка

1. **Загрузите файлы на сервер** с поддержкой PHP и Apache
2. **Убедитесь, что функция `mail()` работает** на вашем хостинге
3. **Настройте email** в файле `config.php` (по умолчанию: muxammad.001.com@gmail.com)

## API Endpoint

**URL:** `POST /feedback-backend/index.php`

**Content-Type:** `application/json`

### Параметры запроса

```json
{
  "name": "Имя отправителя (обязательно)",
  "email": "email@example.com (обязательно)",
  "subject": "Тема сообщения (необязательно)",
  "message": "Текст сообщения (обязательно)"
}
```

### Ответы

**Успешная отправка (200):**
```json
{
  "success": true,
  "message": "Сообщение успешно отправлено"
}
```

**Ошибка валидации (400):**
```json
{
  "error": "Заполните все обязательные поля"
}
```

**Превышен лимит запросов (429):**
```json
{
  "error": "Превышен лимит запросов. Попробуйте позже."
}
```

## Пример использования

### JavaScript (Fetch API)

```javascript
async function sendFeedback(formData) {
  try {
    const response = await fetch('https://yoursite.com/feedback-backend/index.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        name: formData.name,
        email: formData.email,
        subject: formData.subject,
        message: formData.message
      })
    });

    const result = await response.json();

    if (response.ok) {
      alert('Сообщение отправлено успешно!');
      console.log(result.message);
    } else {
      alert('Ошибка: ' + result.error);
    }
  } catch (error) {
    console.error('Ошибка отправки:', error);
    alert('Произошла ошибка при отправке сообщения');
  }
}

// Пример использования
const formData = {
  name: 'Иван Иванов',
  email: 'ivan@example.com',
  subject: 'Вопрос по проекту',
  message: 'Здравствуйте! У меня есть вопрос...'
};

sendFeedback(formData);
```

### React Hook

```jsx
import { useState } from 'react';

export const useFeedback = () => {
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  const sendFeedback = async (formData) => {
    setLoading(true);
    setError(null);

    try {
      const response = await fetch('https://yoursite.com/feedback-backend/index.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData)
      });

      const result = await response.json();

      if (!response.ok) {
        throw new Error(result.error || 'Ошибка отправки');
      }

      return result;
    } catch (err) {
      setError(err.message);
      throw err;
    } finally {
      setLoading(false);
    }
  };

  return { sendFeedback, loading, error };
};
```

## Функции безопасности

- **Rate Limiting:** Максимум 5 запросов в час с одного IP
- **Валидация данных:** Проверка email, длины полей
- **Антispам:** Блокировка подозрительного контента
- **CORS:** Настроенные заголовки для кроссдоменных запросов
- **Логирование:** Все действия записываются в логи
- **Защита файлов:** .htaccess блокирует прямой доступ к служебным файлам

## Настройки безопасности

В файле `config.php` можно изменить:

```php
'max_message_length' => 5000,     // Максимальная длина сообщения
'max_name_length' => 100,         // Максимальная длина имени
'max_subject_length' => 200,      // Максимальная длина темы
'rate_limit_per_ip' => 5,         // Лимит запросов в час
'blocked_words' => ['spam', ...]  // Запрещенные слова
```

## Логи

Система создает логи в папке `logs/`:
- `email.log` - логи отправки email
- `rate_limit.log` - логи ограничения запросов

## Требования

- PHP 7.0+
- Apache с mod_rewrite
- Функция mail() (или настроенный SMTP)
- Права на запись в папку проекта

## Устранение неполадок

1. **Письма не отправляются:**
   - Проверьте работу функции `mail()` на хостинге
   - Убедитесь, что email не попадает в спам
   - Проверьте логи в `logs/email.log`

2. **Ошибки CORS:**
   - Убедитесь, что .htaccess загружен на сервер
   - Проверьте поддержку mod_headers в Apache

3. **Превышен лимит запросов:**
   - Подождите час или измените настройки в `config.php`
   - Проверьте `logs/rate_limit.log`

## Лицензия

MIT License - используйте свободно в своих проектах.
