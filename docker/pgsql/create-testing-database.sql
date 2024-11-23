SELECT 'CREATE DATABASE telegram_bot'
WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = 'telegram_bot')\gexec
