<?php

use App\Models\Config;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



Route::get('/', \App\Livewire\User\Residence\Index::class);
Route::get('/contact', \App\Livewire\User\ContactUs::class);
Route::get('/ticket/{id}', \App\Livewire\User\Tickets\Tickets::class);
Route::get('/detail/{id}', \App\Livewire\User\Residence\Detail::class);
Route::get('/dashboard', \App\Livewire\User\Dashboard::class);
Route::get('/add-residence', \App\Livewire\User\Residence\AddResidence::class);
Route::get('/edit-residence/{id}', \App\Livewire\User\Residence\AddResidence::class);
Route::get('/profile', \App\Livewire\User\Profile::class);
Route::get('/login', \App\Livewire\User\Login::class);


Route::get("/linkedstorage", function () {
    $res = \Illuminate\Support\Facades\Artisan::call("storage:link");
    return $res;
});
Route::get("/lastWeekAm", function () {
    foreach (\App\Models\Residence::all() as $item){
        $item->last_week_amount=$item->amount;
        $item->update();
    }
    foreach (\App\Models\User::all() as $item){
        $item->phone=convertPersianToEnglishNumbers($item->phone);
        $item->national_code=convertPersianToEnglishNumbers($item->national_code);
        $item->update();
    }
});
Route::get('/p/{urlTitle}', \App\Livewire\User\Pages::class);

Route::get('/add-tour', \App\Livewire\User\Tour\Add::class);
Route::get('/edit-tour/{id}', \App\Livewire\User\Tour\Add::class);
Route::get('/tours', \App\Livewire\User\Tour\Index::class);
Route::get('/tour/{id}', \App\Livewire\User\Tour\Detail::class);

Route::get('/add-friend', \App\Livewire\User\Friend\Add::class);
Route::get('/edit-friend/{id}', \App\Livewire\User\Friend\Add::class);
Route::get('/friends', \App\Livewire\User\Friend\Index::class);
Route::get('/friend/{id}', \App\Livewire\User\Friend\Detail::class);

Route::get('/add-foodstore', \App\Livewire\User\FoodStore\Add::class);
Route::get('/edit-foodstore/{id}', \App\Livewire\User\FoodStore\Add::class);
Route::get('/stores', \App\Livewire\User\FoodStore\Index::class);
Route::get('/store/{id}', \App\Livewire\User\FoodStore\Detail::class);

Route::get('/admin', \App\Livewire\Admin\Index::class);

Route::get('/admin/supportAreas', \App\Livewire\Admin\SupportAreas::class);
Route::get('/admin/message', \App\Livewire\Admin\Tickets::class);
Route::get('/admin/message/{id}', \App\Livewire\Admin\Message::class);

Route::get('/admin/dashboard', \App\Livewire\Admin\Index::class);
Route::get('/admin/users', \App\Livewire\Admin\Users::class);

Route::get('/admin/pages', \App\Livewire\Admin\Pages\AllPages::class);
Route::get('/admin/pages/{id}', \App\Livewire\Admin\Pages\Add::class);

Route::get('/admin/provinces', \App\Livewire\Admin\Countries::class);
Route::get('/admin/provinces/{cId}', \App\Livewire\Admin\Provinces::class);
Route::get('/admin/provinces/{pId}/{cId}', \App\Livewire\Admin\Cities::class);
Route::get('/admin/residences', \App\Livewire\Admin\Residences::class);

Route::get('/admin/tools', \App\Livewire\Admin\Categories::class)
    ->defaults('type', 'residence');
Route::get('/admin/tools/{cId}', \App\Livewire\Admin\Tools::class);


Route::get('/admin/tools-foodstore', \App\Livewire\Admin\Categories::class)
    ->defaults('type', 'foodstore');
Route::get('/admin/tools-foodstore/{cId}', \App\Livewire\Admin\Tools::class)
    ->defaults('type', 'foodstore');

Route::get('/admin/tools-friends', \App\Livewire\Admin\Categories::class)
    ->defaults('type', 'friend');
Route::get('/admin/tools-friends/{cId}', \App\Livewire\Admin\Tools::class)
    ->defaults('type', 'friend');

Route::get('/admin/comments', \App\Livewire\Admin\Comments::class);
Route::get('/admin/website-settings', \App\Livewire\Admin\WebsiteManager::class);
Route::get('/admin/logout', function (){
    auth()->logout();
    return \redirect()->to("");
});
