\Illuminate\Support\Facades\Route::group([
    'prefix' => 'dummy-package',
    'namespace' => 'DummyVendor\DummyPackage\Http\Controllers',
    'as' => 'dummy-package.',
], function () {
    $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
});
