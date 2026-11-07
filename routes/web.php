<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\IrsaliyeController;
use App\Http\Controllers\MusteriController;

Route::middleware(['auth'])->group(function () {
  Route::get('/', function () {
    return view('index');
  })->name('/');

  Route::get('GetUser/{id}', [AdminController::class, 'GetUser'])->name('GetUser');
  Route::post('AddUser', [AdminController::class, 'AddUser'])->name('AddUser');
  Route::post('EditUser', [AdminController::class, 'EditUser'])->name('EditUser');
  Route::delete('DeleteUser/{id}', [AdminController::class, 'DeleteUser'])->name('DeleteUser');


  Route::get('calisanlar', [AdminController::class, 'Users'])->name('calisanlar');

  Route::get('uretim', [ProductionController::class, 'Productions'])->name('uretim');
  Route::get('GetProductions', [ProductionController::class, 'GetProductions'])->name('GetProductions');
  Route::post('uretim', [ProductionController::class, 'AddProduction'])->name('productions.post');

  Route::get('productions/{productionId}', [ProductionController::class, 'GetProduction'])->name('productions.get.json');
  Route::post('productions/update', [ProductionController::class, 'Update'])->name('productions.update');
  Route::delete('productions/{productionId}', [ProductionController::class, 'Delete'])->name('productions.delete');

  Route::get('gorevler', [ProductionController::class, 'GetProductionRecords'])->name('gorevler');
  Route::get('gorevler/{id}', [ProductionController::class, 'GetRecordsByUser'])->name('GetRecordsByUser');
  Route::get('Tasks/{mps}', [ProductionController::class, 'GetRecordsByMps'])->name('tasks.get.json');
  Route::delete('DeleteRecords/{mps}', [ProductionController::class, 'DeleteRecords'])->name('DeleteRecords');

  Route::post('/productions/AddProductionRecord', [ProductionController::class, 'AddProductionRecord'])->name('AddProductionRecord');
  Route::post('/productions/AddTask', [ProductionController::class, 'AddTask'])->name('AddTask');
  Route::get('/task/{id}', [ProductionController::class, 'GetTaskById'])->name('GetTaskById');
  Route::get('DeleteTask/{id}', [ProductionController::class, 'DeleteTask'])->name('DeleteTask');

  Route::get('GetEmployees', [UserController::class, 'GetEmployees'])->name('GetEmployees');

  Route::get('StartTask/{id}', [ProductionController::class, 'StartTask'])->name('StartTask');
  Route::get('PauseTask/{id}', [ProductionController::class, 'PauseTask'])->name('PauseTask');
  Route::get('EndTask/{id}', [ProductionController::class, 'EndTask'])->name('EndTask');
  Route::get('ApproveCompletedRecords/{mps}', [ProductionController::class, 'ApproveCompletedRecords'])->name('ApproveCompletedRecords');
  Route::get('ApproveRecord/{id}', [ProductionController::class, 'ApproveRecord'])->name('ApproveRecord');

  Route::post('AddEmployees', [ProductionController::class, 'AddEmployees'])->name('AddEmployees');
  Route::post('RemoveEmployee', [ProductionController::class, 'RemoveEmployee'])->name('RemoveEmployee');
  Route::get('DeleteTaskStep/{id}', [ProductionController::class, 'DeleteTaskStep'])->name('DeleteTaskStep');

  Route::get('raporlar', [ProductionController::class, 'GetReports'])->name('raporlar');
  Route::get('bildirimler', [ProductionController::class, 'Notifications'])->name('bildirimler');
  

  Route::group(['prefix' => 'stok'], function () {
    Route::post('/create', [StokController::class, 'Create'])->name('stok.create');
    Route::post('/update', [StokController::class, 'Update'])->name('stok.update');
    Route::get('/edit/{stok_id}', [StokController::class, 'Edit'])->name('stok.edit');
    Route::post('/edit/{stok_id}', [StokController::class, 'Edit']);
    Route::post('/cikis', [StokController::class, 'Cikis'])->name('stok.cikis');
    Route::post('/deletecikis', [StokController::class, 'DeleteCikis']);
    Route::get('/liste', [StokController::class, 'Liste'])->name('stok.liste');
    Route::get('/getListe', [StokController::class, 'GetListe']);
    Route::get('/arsiv', [StokController::class, 'Arsiv'])->name('stok.arsiv');
    Route::get('/getArsiv', [StokController::class, 'GetArsiv']);
    Route::get('/stokCikis', [StokController::class, 'StokCikis']);
    Route::get('/enFazlaCikis', [StokController::class, 'EnFazlaCikis']);
    Route::get('/enFazlaMiktarCikis', [StokController::class, 'EnFazlaMiktarCikis']);
    Route::get('/cikisYapilmayanStoklar', [StokController::class, 'CikisYapilmayanStoklar']);
    Route::get('/sifirlanmayanStoklar', [StokController::class, 'SifirlanmayanStoklar']);
    Route::get('/stokSearch', [StokController::class, 'StokSearch']);
    Route::get('/getCins', [StokController::class, 'GetCins']);
    Route::get('/getKalan', [StokController::class, 'GetKalan']);
    Route::post('/getdetay', [StokController::class, 'GetDetay']);
    Route::post('/not', [StokController::class, 'Not']);
    Route::post('/getnot', [StokController::class, 'GetNot']);
    Route::post('/getinfo', [StokController::class, 'GetInfo']);
    Route::post('/hide', [StokController::class, 'Hide']);
    Route::post('/showstok', [StokController::class, 'ShowStok']);
  });

  Route::group(['prefix' => 'irsaliye'], function () {
    Route::post('/create', [IrsaliyeController::class, 'Create'])->name('irsaliye.create');
    Route::get('/liste', [IrsaliyeController::class, 'Liste'])->name('irsaliye.liste');
    Route::get('/irsaliyeSearch', [IrsaliyeController::class, 'IrsaliyeSearch']);
    Route::post('/detay', [IrsaliyeController::class, 'Detay']);
  });

  Route::group(['prefix' => 'musteri'], function () {
    Route::post('/create', [MusteriController::class, 'Create'])->name('musteri.create');
    Route::get('/liste', [MusteriController::class, 'Liste'])->name('musteri.liste');
    Route::post('/getilceler', [MusteriController::class, 'GetIlceler']);
    Route::post('/getdetay', [MusteriController::class, 'GetDetay']);
    Route::post('/getililce', [MusteriController::class, 'GetIlIlce']);
  });
});



Route::get('login', function () {
    return view('auth.login');
})->name('login');

Route::post('login', [LoginController::class, 'Login'])->name('login.post');
Route::get('logout', [LoginController::class, 'Logout'])->name('logout');

Route::get('AutoPause', [ProductionController::class, 'AutoPause'])->name('AutoPause');
Route::get('AutoStart', [ProductionController::class, 'AutoStart'])->name('AutoStart');