# AI Image App Backend

Backend Laravel cho hệ thống tạo ảnh AI sử dụng ComfyUI.

## Công nghệ

- Laravel
- MySQL
- Queue Jobs
- Broadcasting

## Cài đặt

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
````

## Chạy server

```bash
php artisan serve
```

## Chạy queue worker

```bash
php artisan queue:work
```

## Storage

Tạo symbolic link:

```bash
php artisan storage:link
```

## Luồng generate

1. frontend gửi request generate
2. backend tạo generation job
3. queue worker gửi workflow sang ComfyUI
4. backend polling kết quả
5. lưu media vào storage
6. frontend hiển thị media

## API

Base URL:

```
http://127.0.0.1:8000/api
```

````

---

## Commit

```bash
git add README.md
git commit -m "rewrite backend README"
git push
````
