<?php

namespace App\Console;

use App\Models\Images;
use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Storage;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            $publicDisk = Storage::disk('public');
            $olderThan = now()->subDay()->timestamp;

            $this->purgeOrphanPublicFiles(
                $publicDisk,
                'residences',
                Images::whereNotNull('residence_id')->pluck('url')->filter()->all(),
                $olderThan
            );

            $this->purgeOrphanPublicFiles(
                $publicDisk,
                'tours',
                Images::whereNotNull('tour_id')->pluck('url')->filter()->all(),
                $olderThan
            );

            $this->purgeOrphanPublicFiles(
                $publicDisk,
                'friends',
                Images::whereNotNull('friend_id')->pluck('url')->filter()->all(),
                $olderThan
            );

            $this->purgeOrphanPublicFiles(
                $publicDisk,
                'food_store',
                Images::whereNotNull('store_id')->pluck('url')->filter()->all(),
                $olderThan
            );

            $userImages = User::query()
                ->whereNotNull('profile_image')
                ->pluck('profile_image')
                ->filter()
                ->all();

            $userImages[] = 'User.png';

            $this->purgeOrphanPublicFiles(
                $publicDisk,
                'user',
                $userImages,
                $olderThan
            );

            $tmpDiskName = config('livewire.temporary_file_upload.disk') ?: config('filesystems.default');
            $tmpDirectory = trim(config('livewire.temporary_file_upload.directory') ?: 'livewire-tmp', '/');
            $tmpDisk = Storage::disk($tmpDiskName);

            $this->purgeLivewireTmp($tmpDisk->path($tmpDirectory), now()->subDay()->timestamp);
        })->dailyAt('03:30');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    private function purgeOrphanPublicFiles($disk, string $directory, array $keepFiles, int $olderThanTimestamp): void
    {
        $keepMap = array_fill_keys($keepFiles, true);

        foreach ($disk->files($directory) as $path) {
            $filename = basename($path);

            if (isset($keepMap[$filename])) {
                continue;
            }

            if ($disk->lastModified($path) > $olderThanTimestamp) {
                continue;
            }

            $disk->delete($path);
        }
    }

    private function purgeLivewireTmp(string $basePath, int $olderThanTimestamp): void
    {
        if (!is_dir($basePath)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($basePath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $item) {
            $itemPath = $item->getPathname();

            if ($item->isFile() && $item->getMTime() <= $olderThanTimestamp) {
                @unlink($itemPath);
                continue;
            }

            if ($item->isDir()) {
                @rmdir($itemPath);
            }
        }
    }
}
