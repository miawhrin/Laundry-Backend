    <?php

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\Api\Admin\RoleController;
    use App\Http\Controllers\Api\Admin\PermissionController;
    use App\Http\Controllers\Api\Admin\UserController;
    /*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register API routes for your application. These
    | routes are loaded by the RouteServiceProvider and all of them will
    | be assigned to the "api" middleware group. Make something great!
    |
    */

    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });

    /**
     * route "/register"
     * @method "POST"
     */
    Route::post('/register', App\Http\Controllers\Api\Auth\RegisterController::class)->name('register');

    //route login
    Route::post('/login',
    [App\Http\Controllers\Api\Auth\LoginController::class, 'index']);

    //group route with middleware "auth'
    Route::group(['middleware' => 'auth:api'], function() {
        
        //logout
        Route::post('/logout',
        [App\Http\Controllers\Api\Auth\LoginController::class, 'logout']);
    });


    Route::prefix('admin')->group(function () {
        Route::middleware(['auth:api', 'checkRole:1'])->group(function () {


            //permissions
            Route::get('/permissions', [\App\Http\Controllers\Api\Admin\PermissionController::class,
            'index'])->middleware('permission:permissions.index');

            //permissions all
            Route::get('/permissions/all', [\App\Http\Controllers\Api\Admin\PermissionController::class, 'all'])->middleware('permission:permissions.index');

            //roles all
            Route::get('/roles/all', [\App\Http\Controllers\Api\Admin\RoleController::class, 'all'])
            ->middleware('permission:roles.index');

            //roles
            Route::apiResource('/roles', App\Http\Controllers\Api\Admin\RoleController::class)
            ->middleware('permission:roles.index|roles.store|roles.update|roles.delete');

            //users
            Route::apiResource('/users', App\Http\Controllers\Api\Admin\UserController::class)
            ->middleware('permission:users.index|users.store|users.update|users.delete');
        });
    });

    Route::prefix('customer')->group(function () {
    Route::middleware(['auth:api', 'checkRole:2'])->group(function () {
        
    });
});