# AI Image App Backend

Backend Laravel cho hệ thống tạo ảnh AI sử dụng ComfyUI.

## Công nghệ

- Laravel
- MySQL
- Queue Jobs
- Broadcasting
- ComfyUI

## Yêu cầu môi trường

- PHP 8.2+
- Composer
- MySQL
- Node.js (nếu build assets)

## Cài đặt

Clone repo và chạy:

```bash
composer install


Tạo file .env

cp .env.example .env

Tạo key ứng dụng:

php artisan key:generate

Cấu hình database trong .env

Sau đó migrate database:

php artisan migrate
Chạy server
php artisan serve

Server mặc định chạy tại:

http://127.0.0.1:8000
Chạy queue worker

Hệ thống generate sử dụng queue job nên cần chạy worker:

php artisan queue:work

Bạn nên mở terminal thứ hai để chạy worker.

Storage

Tạo symbolic link để truy cập file media:

php artisan storage:link
API

Base URL:

http://127.0.0.1:8000/api
Luồng generate

Frontend gửi request generate

Backend tạo generation job

Queue worker gửi workflow sang ComfyUI

Backend polling kết quả

Lưu media vào storage

Frontend hiển thị kết quả

Debug

Xem log Laravel tại:

storage/logs/laravel.log
Lưu ý

Khi chạy local cần đảm bảo:

ComfyUI đang chạy

Queue worker đang chạy

Database đã migrate


---

# 4️⃣ Lưu file

---

# 5️⃣ Commit và push

Trong terminal backend chạy:

```bash
git status