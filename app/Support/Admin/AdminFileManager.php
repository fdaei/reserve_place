<?php

namespace App\Support\Admin;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminFileManager
{
    public static function store(UploadedFile $file, string $directory, string $disk = 'public'): string
    {
        return $file->store($directory, $disk);
    }

    public static function delete(?string $path, string $disk = 'public'): void
    {
        $normalized = self::normalizeStoredPath($path);

        if ($normalized && Storage::disk($disk)->exists($normalized)) {
            Storage::disk($disk)->delete($normalized);
        }
    }

    public static function url(?string $path, string $disk = 'public', ?string $directory = null): ?string
    {
        $normalized = self::normalizeStoredPath($path, $directory);

        if (! $normalized) {
            return null;
        }

        return Storage::disk($disk)->url($normalized);
    }

    public static function normalizeStoredPath(?string $path, ?string $directory = null): ?string
    {
        if (blank($path)) {
            return null;
        }

        $path = (string) $path;
        $path = Str::replaceFirst('/storage/', '', $path);
        $path = Str::replaceFirst('storage/', '', $path);
        $path = ltrim($path, '/');

        if (
            $directory
            && ! str_contains($path, '/')
            && ! str_starts_with($path, 'http://')
            && ! str_starts_with($path, 'https://')
        ) {
            return trim($directory, '/').'/'.$path;
        }

        return $path;
    }
}
