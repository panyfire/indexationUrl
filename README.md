REST API сервис для проверки индексации URL через XMLStock API.

# Запуск проекта

---

## 1. Создание .env

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

# Проверка Цепочки проверок от 1 до 3

Поскольку XMLStock использует внешний поисковый индекс,
результаты exact/inurl/site:title могут изменяться со временем.

Для гарантированной проверки каждой стратегии рекомендуется:

- временно принудительно возвращать fail() в предыдущих checks
- проверять переход chain на следующий handler

Это позволяет детерминированно протестировать:
- ExactUrlCheck
- InUrlCheck
- SiteTitleCheck