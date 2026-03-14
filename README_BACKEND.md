# PaintingAI Laravel Backend v2

Backend này được dựng để bám sát frontend Angular bạn đã gửi, với các domain chính:
- auth + Sanctum
- settings
- media library
- stacks
- generation jobs
- favorites
- collections
- presets
- prompt history
- queue + Reverb + ComfyUI integration hook

## Trạng thái
Đây là bản **production-oriented scaffold**: kiến trúc, bảng, route, auth, queue, broadcast và service layer đã được tổ chức để đi vào triển khai thật. Phần còn phụ thuộc môi trường thật của bạn gồm:
- biến môi trường
- storage/CDN
- queue worker / supervisor
- Reverb server
- ComfyUI endpoint thật
- logging / monitoring / backup / CI/CD
- test end-to-end với frontend

## Route chính
### Public
- `POST /api/auth/register`
- `POST /api/auth/login`

### Authenticated
- `GET /api/me`
- `POST /api/auth/logout`
- `GET|PUT /api/settings`
- `GET /api/media`
- `GET /api/media/by-day`
- `GET /api/media/{id}`
- `DELETE /api/media/{id}`
- `GET /api/stacks`
- `GET /api/stacks/{id}`
- `POST /api/generate`
- `GET /api/generate/{jobId}`
- `POST /api/jobs/{jobId}/cancel`
- `POST /api/media/{id}/variation`
- `POST /api/media/{id}/upscale`
- `POST /api/media/{id}/image-to-video`
- `GET /api/favorites`
- `POST|DELETE /api/media/{id}/favorite`
- `GET|POST /api/collections`
- `GET|PATCH|DELETE /api/collections/{id}`
- `POST /api/collections/{id}/items`
- `DELETE /api/collections/{id}/items/{mediaId}`
- `POST /api/collections/{id}/reorder`
- `GET /api/presets`
- `GET /api/presets/{id}`
- `GET|POST /api/prompt-history`
- `DELETE /api/prompt-history/{id}`

## Chạy nhanh
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Nếu dùng queue + Reverb:
```bash
php artisan queue:work
php artisan reverb:start
```

## Frontend mapping quan trọng
### MediaItem
API resource đã map theo frontend:
- `id`
- `kind`
- `url`
- `type`
- `prompt`
- `ratio`
- `resolution`
- `seed`
- `createdAt`
- `favorite`
- `status`
- `progress`
- `id_stack`
- `order_in_stack`
- `order_in_board`
- `jobId`
- `parentId`
- `ghostOf`

## Auth trên frontend Angular
Header chuẩn:
```http
Authorization: Bearer <token>
Accept: application/json
```

## Gợi ý trước khi đưa production thật
- chuyển `QUEUE_CONNECTION` sang Redis
- cấu hình S3/R2 thay vì local disk
- cấu hình HTTPS + CORS cụ thể domain frontend
- thêm rate limit auth/generate
- thêm feature tests / e2e tests
- thêm error tracking
- thêm cleanup jobs cho placeholder media lỗi
