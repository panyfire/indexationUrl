REST API сервис для проверки индексации URL через XMLStock API.

# Запуск проекта

---

## 1. Создание .env.local

```env
XMLSTOCK_URL=api_url
XMLSTOCK_USER=your_user
XMLSTOCK_KEY=your_key
```

---

## 2. Сборка контейнеров

```bash
docker compose build --no-cache
```

---

## 3. Запуск

```bash
docker compose up -d
```

---

## 5. Установка зависимостей

```bash
docker compose exec php composer install
```

---

# API

## POST /api/indexation/check

Проверка индексации URL.

---
# SWAGGER

Находиться по пути /api/doc
---

# Пример запроса

```bash
curl -X POST http://localhost/api/indexation/check \
-H "Content-Type: application/json" \
-d '{
  "urls": [
    "https://docker.com",
    "https://haieronline.ru",
    "test",
    "https://docker.com/",
    "HTTP://HAIERONLINE.RU",
    "https://music.yandex.ru",
    "https://github.com",
    "https://habr.com/ru/articles/",
    "https://symfony.com"
  ]
}'
```

---

# Пример ответа

```json
[
    {
        "url": "https://docker.com",
        "indexed": false
    },
    {
        "url": "https://haieronline.ru",
        "indexed": true
    },
    {
        "url": "https://music.yandex.ru",
        "indexed": true
    },
    {
        "url": "https://github.com",
        "indexed": true
    },
    {
        "url": "https://habr.com/ru/articles/",
        "indexed": true
    },
    {
        "url": "https://symfony.com",
        "indexed": true
    }
]
```
