# Test for FMSmax interview

## Задача
1. Создать парсер матчей с ресурса  [pandascore.co](https://developers.pandascore.co/doc/index_csgo.htm#operation/get_csgo_matches_running)
⋅⋅1.1. добавление новых лиг в соотвествующую таблицу
⋅⋅1.2. добавление новых команд в соотвествующую таблицу
2. Результат работы как минимум 3 заполненные таблицы и
3. Грамотно оформленный код.
4. Выполнить на фреймворке Laravel

## Запуск решения
1. Собрать контейнер, Dockerfile в корне проекта.
2. При запуске контейнера прокинуть нужные переменные окружение, а именно:
⋅⋅2.1. Подключение к БД mysql
```bash
DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

2.2. API ключ от pandascore.co
```bash
PANDASCORE_TOKEN=
```