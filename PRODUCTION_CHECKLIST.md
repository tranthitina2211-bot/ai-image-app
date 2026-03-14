# Production checklist

## Cần chốt trước khi go-live
- Domain frontend/backend và CORS chính xác
- Driver storage: local hay S3/R2
- Queue: database hay Redis
- Reverb deploy riêng hay cùng app
- ComfyUI gọi nội bộ hay qua gateway
- Chính sách quota/rate limit cho generate
- Chính sách retention cho media/job logs
- Backup MySQL và object storage

## Khuyến nghị kỹ thuật
- Bật HTTPS bắt buộc
- Dùng Redis cho queue/cache
- Chạy queue worker bằng Supervisor hoặc Horizon
- Log tập trung và alert lỗi
- Thêm CI chạy lint + tests + migrate check
- Thêm signed URL hoặc private bucket nếu media không public
