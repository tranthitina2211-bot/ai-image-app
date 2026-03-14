<?php

namespace App\Services\Generation;

use App\Models\GenerationJob;
use App\Support\Generation\WorkflowTemplateBuilder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ComfyUiService
{
    public function queueJob(GenerationJob $job): array
    {
        $baseUrl = rtrim(config('services.comfyui.base_url', 'http://127.0.0.1:8188'), '/');
        $template = WorkflowTemplateBuilder::resolveTemplate($job->action, $job->mode);
        $workflowPath = storage_path('app/comfy/workflows/' . $template);

        if (! File::exists($workflowPath)) {
            throw new RuntimeException("Workflow template not found: {$template}");
        }

        $context = [
            'job_id' => $job->id,
            'prompt' => $job->prompt,
            'negative_prompt' => $job->negative_prompt,
            'ratio' => $job->ratio,
            'resolution' => $job->resolution,
            'seed' => $job->seed,
            'input_image' => $this->resolveInputImage($job),
        ];

        $workflow = File::get($workflowPath);
        $workflow = str_replace(
            array_keys(WorkflowTemplateBuilder::replacements($context)),
            array_values(WorkflowTemplateBuilder::replacements($context)),
            $workflow
        );

        $decoded = json_decode($workflow, true);
        if (! is_array($decoded) || ! isset($decoded['prompt'])) {
            throw new RuntimeException('Workflow template is not valid API JSON.');
        }

        $response = Http::timeout((int) config('services.comfyui.timeout', 90))
            ->acceptJson()
            ->post($baseUrl . '/prompt', [
                'prompt' => $decoded['prompt'],
                'client_id' => $job->id,
            ]);

        if (! $response->successful()) {
            throw new RuntimeException('Failed to queue ComfyUI job: ' . $response->body());
        }

        return [
            'driver' => 'comfyui',
            'provider_job_id' => $response->json('prompt_id'),
            'workflow' => $template,
        ];
    }

    public function fetchHistory(string $providerJobId): array
    {
        $baseUrl = rtrim(config('services.comfyui.base_url', 'http://127.0.0.1:8188'), '/');

        return Http::timeout((int) config('services.comfyui.timeout', 90))
            ->acceptJson()
            ->get($baseUrl . '/history/' . $providerJobId)
            ->json() ?? [];
    }

    public function extractResultFromHistory(string $providerJobId, array $history): ?array
    {
        $nodeOutputs = data_get($history, $providerJobId . '.outputs', []);
        if (! is_array($nodeOutputs)) {
            return null;
        }

        foreach ($nodeOutputs as $nodeId => $payload) {
            if (! empty($payload['images'][0])) {
                $image = $payload['images'][0];
                return [
                    'node' => $nodeId,
                    'type' => 'image',
                    'filename' => $image['filename'] ?? null,
                    'subfolder' => $image['subfolder'] ?? '',
                    'folder_type' => $image['type'] ?? 'output',
                ];
            }

            if (! empty($payload['gifs'][0])) {
                $video = $payload['gifs'][0];
                return [
                    'node' => $nodeId,
                    'type' => 'video',
                    'filename' => $video['filename'] ?? null,
                    'subfolder' => $video['subfolder'] ?? '',
                    'folder_type' => $video['type'] ?? 'output',
                ];
            }
        }

        return null;
    }

    public function resolveOutputAbsolutePath(array $result): string
    {
        $baseOutputDir = rtrim(config('services.comfyui.output_dir', base_path('ComfyUI/output')), DIRECTORY_SEPARATOR);
        $subfolder = trim((string) ($result['subfolder'] ?? ''), '/\\');
        $filename = basename((string) ($result['filename'] ?? ''));

        $parts = array_filter([$baseOutputDir, $subfolder, $filename]);
        return implode(DIRECTORY_SEPARATOR, $parts);
    }

    private function resolveInputImage(GenerationJob $job): string
    {
        $inputDir = rtrim(config('services.comfyui.input_dir', base_path('ComfyUI/input')), DIRECTORY_SEPARATOR);
        File::ensureDirectoryExists($inputDir);

        if ($job->input_file_path && File::exists($job->input_file_path)) {
            $filename = basename($job->input_file_path);
            File::copy($job->input_file_path, $inputDir . DIRECTORY_SEPARATOR . $filename);
            return $filename;
        }

        $parent = $job->parentMediaItem;
        if (! $parent?->storage_path || ! $parent->storage_disk) {
            return '';
        }

        $source = Storage::disk($parent->storage_disk)->path($parent->storage_path);
        if (! File::exists($source)) {
            return '';
        }

        $filename = basename($source);
        File::copy($source, $inputDir . DIRECTORY_SEPARATOR . $filename);
        return $filename;
    }
}
