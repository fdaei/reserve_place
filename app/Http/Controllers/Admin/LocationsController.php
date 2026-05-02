<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\PopularCity;
use App\Models\Province;

class LocationsController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()?->hasPermissionBySlug(config('access-control.content_manage_permission')), 403);

        return view('admin.locations.index', [
            'provinces' => Province::query()
                ->with('country')
                ->withCount(['cities', 'residences'])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->paginate(10, ['*'], 'provinces_page'),
            'cities' => City::query()
                ->with('province')
                ->withCount('residences')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->paginate(10, ['*'], 'cities_page'),
            'popularCities' => PopularCity::query()
                ->with('city.province')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->paginate(10, ['*'], 'popular_page'),
        ]);
    }
}
