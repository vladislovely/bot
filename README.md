<p align="center">
    <a href="https://laravel.com" target="_blank">
        <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
    </a>
</p>

## О проекте

Это проект скриптового бота, работающий по заложенному в него сценарию.

## Stack technology

Laravel 11, Supervisor, PHP 8.3, PostgresSQL 15

## Процесс разворачивания

- mv .env.example .env, заполнить пустые значения
- composer install
- php artisan key:generate or set .env APP_KEY string up to 32 length
- docker compose up -d

## Schema how is working service

[Тык](https://drive.google.com/file/d/1RYdbA-nO7q0rQ86mc2hROiO035xGcmJr/view?usp=sharing)

## Как работать с приложением?

Для добавления новых шагов для бота вам нужно опираться на класс *app/Telegram/TelegramCaller.php*
Не забудьте описать логику шага в *app/Telegram/CommandProcessor.php*

## Env Variables

| Variable Name          | Type   | Description                   |
|------------------------|--------|-------------------------------|
| TELEGRAM_BOT_TOKEN     | string | Берется из bot father         |
| TELEGRAM_PAYLOAD_TOKEN | string | Рандомная строка              |
| APP_URL                | string | Url до приложения с https     |
| DB_CONNECTION          | string | Для postgresql значение pgsql |
| DB_HOST                | string | Контейнер или url             |
