# PaintingAI backend integration notes

## Local test flow
1. Copy `.env.example` to `.env`.
2. Set DB credentials.
3. Set:
   - `COMFYUI_DRIVER=comfyui`
   - `COMFYUI_BASE_URL=http://127.0.0.1:8188`
   - `COMFYUI_INPUT_DIR=/absolute/path/to/ComfyUI/input`
   - `COMFYUI_OUTPUT_DIR=/absolute/path/to/ComfyUI/output`
4. Run:
   - `composer install`
   - `php artisan key:generate`
   - `php artisan migrate --seed`
   - `php artisan storage:link`
   - `php artisan queue:work`
   - `php artisan serve`
5. Login from Angular with `demo@example.com` / `password` if you keep the original seeder password, or register a new user.

## Workflow templates
Templates are stored in `storage/app/comfy/workflows`:
- `txt2img_basic.json`
- `img2img_variation.json`
- `img_upscale.json`
- `img2vid_basic.json`
- `txt2vid_basic.json`

They are plain API-format templates with placeholders such as `{{PROMPT}}`, `{{WIDTH}}`, `{{HEIGHT}}`, `{{INPUT_IMAGE}}`, and `{{JOB_ID}}`.

## Important note
`img2vid_basic.json` and `txt2vid_basic.json` assume you have the VHS/Video Combine node installed in ComfyUI. If not, replace those templates with the video workflow you prefer.
