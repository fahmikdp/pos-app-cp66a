<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\File;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class Logs extends Component
{
    use WireUiActions;

    public string $search = '';

    public string $level = '';

    public ?int $expandedIndex = null;

    public function mount(): void
    {
        if (! auth()->check() || ! auth()->user()->isSuperadmin()) {
            abort(403, 'Akses khusus Superadmin');
        }
    }

    public function clearLogs(): void
    {
        $logFile = storage_path('logs/laravel.log');
        if (File::exists($logFile)) {
            File::put($logFile, '');
        }

        $this->expandedIndex = null;
        $this->dispatch('wireui:notification', [
            'title' => 'Log Dibersihkan',
            'description' => 'File laravel.log telah berhasil dikosongkan.',
            'icon' => 'success',
        ]);
    }

    public function toggleExpand(int $index): void
    {
        $this->expandedIndex = $this->expandedIndex === $index ? null : $index;
    }

    public function render()
    {
        $logFile = storage_path('logs/laravel.log');
        $fileSize = File::exists($logFile) ? File::size($logFile) : 0;
        $formattedFileSize = $this->formatBytes($fileSize);

        $logs = $this->parseLogFile($logFile);

        if ($this->level) {
            $logs = array_filter($logs, fn ($log) => strtolower($log['level']) === strtolower($this->level));
        }

        if ($this->search) {
            $search = strtolower($this->search);
            $logs = array_filter($logs, function ($log) use ($search) {
                return str_contains(strtolower($log['timestamp']), $search)
                    || str_contains(strtolower($log['level']), $search)
                    || str_contains(strtolower($log['message']), $search)
                    || str_contains(strtolower($log['context']), $search);
            });
        }

        return view('livewire.settings.logs', [
            'logs' => array_values($logs),
            'logFileSize' => $formattedFileSize,
            'logExists' => File::exists($logFile),
            'levelOptions' => [
                ['label' => 'Semua Level', 'value' => ''],
                ['label' => 'ERROR', 'value' => 'error'],
                ['label' => 'WARNING', 'value' => 'warning'],
                ['label' => 'INFO', 'value' => 'info'],
                ['label' => 'DEBUG', 'value' => 'debug'],
                ['label' => 'CRITICAL', 'value' => 'critical'],
            ],
        ])->layout('layouts.app', ['title' => 'Log Error Sistem']);
    }

    private function parseLogFile(string $filePath): array
    {
        if (! File::exists($filePath)) {
            return [];
        }

        $content = File::get($filePath);
        if (empty(trim($content))) {
            return [];
        }

        $pattern = '/\[(\d{4}-\d{2}-\d{2}[T ]\d{2}:\d{2}:\d{2}(?:\.\d+)?(?:[\+-]\d{2}:\d{2})?)\]\s+([a-zA-Z0-9_\-]+)\.([A-Z]+):\s+(.*?)(?=\n\[\d{4}-\d{2}-\d{2}|\z)/s';

        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

        $logs = [];
        foreach ($matches as $match) {
            $timestamp = $match[1] ?? '';
            $env = $match[2] ?? '';
            $level = $match[3] ?? 'INFO';
            $rawBody = trim($match[4] ?? '');

            $lines = explode("\n", $rawBody);
            $message = array_shift($lines);
            $context = implode("\n", $lines);

            $logs[] = [
                'timestamp' => $timestamp,
                'env' => $env,
                'level' => $level,
                'message' => $message,
                'context' => trim($context),
            ];
        }

        return array_reverse($logs);
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes === 0) {
            return '0 B';
        }
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes, 1024));

        return round($bytes / pow(1024, $i), 2).' '.$units[$i];
    }
}
