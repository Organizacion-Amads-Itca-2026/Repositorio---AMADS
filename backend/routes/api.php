<?php

use App\Http\Controllers\API\AdminDescuentoController;
use App\Http\Controllers\API\CategoriaTicketController;
use App\Http\Controllers\API\CompraController;
use App\Http\Controllers\API\DescuentoController;
use App\Http\Controllers\API\DetalleCompraController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\EventosController;
use App\Http\Controllers\API\TipoEventoController;
use App\Http\Controllers\API\UsuarioController;
use App\Http\Controllers\API\PerfilesController;
use App\Http\Controllers\API\EstablecimientosController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ClienteController;
use App\Http\Controllers\API\LugaresEventoController;
use App\Http\Controllers\API\ProveedorController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ── : Auth ──────────────────────────────────────
Route::post('/seg/login', [AuthController::class, 'login']);
Route::post('/seg/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('role:USUARIO')->group(function () {

        // Rutas existentes (Marjorie & Otros)
        Route::get("/eve/descuentos/{codigo}/{idEvento}", [DescuentoController::class, 'VerificarDescuento']);
        Route::get("/vta/facturas/{idUser}", [CompraController::class, "show"]);
        Route::post("/vta/factura/store", [CompraController::class, "store"]);
        Route::post("/vta/facturaDetalle/store", [DetalleCompraController::class, "store"]);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/change-password', [\App\Http\Controllers\API\AuthController::class, 'changePassword']);
    });
    Route::middleware('role:ADMINISTRADOR')->group(function () {
        // Rutas auxiliares para dropdowns
        Route::get('/seg/perfiles', [PerfilesController::class, 'index']);
        Route::get('/adm/establecimientos', [EstablecimientosController::class, 'index']);

        // ── Kevin: Lugares de Evento ─────────────────────────
        Route::get('/eve/lugares', [LugaresEventoController::class, 'index']);
        Route::post('/eve/lugares', [LugaresEventoController::class, 'store']);
        Route::put('/eve/lugares/{id}', [LugaresEventoController::class, 'update']);
        Route::delete('/eve/lugares/{id}', [LugaresEventoController::class, 'destroy']);

        // ── Kevin: Alias lugares_evento (compatibilidad con frontend) ─
        Route::get('/eve/lugares_evento', [LugaresEventoController::class, 'index']);
        Route::post('/eve/lugares_evento', [LugaresEventoController::class, 'store']);
        Route::put('/eve/lugares_evento/{id}', [LugaresEventoController::class, 'update']);
        Route::delete('/eve/lugares_evento/{id}', [LugaresEventoController::class, 'destroy']);

        // ── Kevin: Usuarios Admin ─────────────────────────────
        Route::get('/seg/usuarios', [UsuarioController::class, 'index']);
        Route::post('/seg/usuarios', [UsuarioController::class, 'store']);
        Route::get('/seg/usuarios/{id}', [UsuarioController::class, 'show']);
        Route::put('/seg/usuarios/{id}', [UsuarioController::class, 'update']);
        Route::delete('/seg/usuarios/{id}', [UsuarioController::class, 'destroy']);

        // ── Kevin: Dashboard metrics ──────────────────────────
        Route::get('/eve/eventos/dashboard/metrics', [EventosController::class, 'metrics']);

        // ── Kevin: Eventos CRUD ───────────────────────────────
        Route::post('/eve/eventos', [EventosController::class, 'store']);
        Route::put('/eve/eventos/{id}', [EventosController::class, 'update']);
        Route::delete('/eve/eventos/{id}', [EventosController::class, 'destroy']);

        // ── Kevin: Foto eventos ───────────────────────────────
        Route::post('/cte/eventos/{id}/foto', [EventosController::class, 'subirFoto']);
        Route::put('/cte/eventos/{id}/foto', [EventosController::class, 'subirFoto']);
        Route::delete('/cte/eventos/{id}/foto', [EventosController::class, 'eliminarFoto']);

        // ── Kevin: Tipos de Evento CRUD ───────────────────────
        Route::post('/eve/tipos_evento', [TipoEventoController::class, 'store']);
        Route::put('/eve/tipos_evento/{id}', [TipoEventoController::class, 'update']);
        Route::delete('/eve/tipos_evento/{id}', [TipoEventoController::class, 'destroy']);

        // ── Kevin: Proveedores ────────────────────────────────
        Route::get('/prv/proveedores', [ProveedorController::class, 'index']);
        Route::get('/prv/proveedores/estado/{estado}', [ProveedorController::class, 'porEstado']);
        Route::get('/prv/proveedores/{id}', [ProveedorController::class, 'show']);
        Route::post('/prv/proveedores', [ProveedorController::class, 'store']);
        Route::put('/prv/proveedores/{id}', [ProveedorController::class, 'update']);
        Route::delete('/prv/proveedores/{id}', [ProveedorController::class, 'destroy']);

        // ── Kevin: Clientes ───────────────────────────────────
        Route::get('/vta/clientes', [ClienteController::class, 'index']);
        Route::get('/vta/clientes/estado/{estado}', [ClienteController::class, 'porEstado']);
        Route::get('/vta/clientes/{id}', [ClienteController::class, 'show']);
        Route::post('/vta/clientes', [ClienteController::class, 'store']);
        Route::put('/vta/clientes/{id}', [ClienteController::class, 'update']);
        Route::delete('/vta/clientes/{id}', [ClienteController::class, 'destroy']);

        // ── Kevin: Descuentos Admin ───────────────────────────
        Route::get('/vta/descuentos', [AdminDescuentoController::class, 'index']);
        Route::get('/vta/descuentos/vigentes', [AdminDescuentoController::class, 'vigentes']);
        Route::get('/vta/descuentos/disponibles', [AdminDescuentoController::class, 'disponibles']);
        Route::get('/vta/descuentos/codigo/{codigo}', [AdminDescuentoController::class, 'porCodigo']);
        Route::get('/vta/descuentos/estado/{estado}', [AdminDescuentoController::class, 'porEstado']);
        Route::get('/vta/descuentos/{id}', [AdminDescuentoController::class, 'show']);
        Route::post('/vta/descuentos', [AdminDescuentoController::class, 'store']);
        Route::post('/vta/descuentos/{id}/usar', [AdminDescuentoController::class, 'usar']);
        Route::put('/vta/descuentos/{id}', [AdminDescuentoController::class, 'update']);
        Route::delete('/vta/descuentos/{id}', [AdminDescuentoController::class, 'destroy']);

        // ── Kevin: Categorias Ticket ──────────────────────────
        Route::post('/cte/categorias-ticket', [CategoriaTicketController::class, 'store']);
        Route::put('/cte/categorias-ticket/{id}', [CategoriaTicketController::class, 'update']);
        Route::delete('/cte/categorias-ticket/{id}', [CategoriaTicketController::class, 'destroy']);
    });
    Route::get("/eve/eventos", [EventosController::class, 'index']);
    Route::get("/eve/tipos_evento", [TipoEventoController::class, 'index']);
    Route::get("/eve/categorias_ticket/{idEvento}", [CategoriaTicketController::class, 'showEventsCategories']);

    //login jossue
    //Route::post('/seg/login', [AuthController::class, 'login']);
    // Logout 
    //Route::post('/seg/register', [\App\Http\Controllers\API\AuthController::class, 'register']);
    
    Route::middleware('auth:sanctum')->post('/seg/logout', [AuthController::class, 'logout']);
    // Registro de usuarios
    //Route::post('/seg/register', [AuthController::class, 'register']);

    
});
