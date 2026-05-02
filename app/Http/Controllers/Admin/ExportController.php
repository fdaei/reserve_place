<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ExportRequest;
use App\Services\Admin\ActivityLogService;
use App\Support\Admin\AdminResourceRegistry;

class ExportController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()?->hasPermissionBySlug('exports-manage'), 403);

        return view('admin.export.index', [
            'resources' => collect(AdminResourceRegistry::all())
                ->map(fn ($config, $key) => ['key' => $key, 'title' => $config['title']])
                ->sortBy('title')
                ->values(),
        ]);
    }

    public function export(ExportRequest $request)
    {
        abort_unless(auth()->user()?->hasPermissionBySlug('exports-manage'), 403);

        $resource = $request->validated('resource');
        $config = AdminResourceRegistry::get($resource);
        $columns = $config['columns'] ?? [];
        $query = $config['model']::query();

        app(ActivityLogService::class)->log('export', $config['model'], $request, ['resource' => $resource], 'دریافت خروجی از '.$config['title']);

        if (! empty($config['with'])) {
            $query->with($config['with']);
        }

        $filename = $resource.'-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($query, $columns) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, array_map(fn ($column) => $column['label'], $columns));

            $query->latest('id')->chunk(200, function ($items) use ($handle, $columns) {
                foreach ($items as $item) {
                    fputcsv($handle, array_map(fn ($column) => AdminResourceRegistry::displayValue($item, $column), $columns));
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
