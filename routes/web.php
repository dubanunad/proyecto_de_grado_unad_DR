<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\CashRegisterTransactionController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MaterialMovementController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aquí es donde puedes registrar las rutas web para tu aplicación. Estas
| rutas son cargadas por el RouteServiceProvider dentro de un grupo que
| contiene el grupo de middleware "web". ¡Haz algo grandioso!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/', [App\Http\Controllers\AdminController::class, 'index'])->name('gestisp.index');

// Rutas del dash con prefijo 'gestisp'
Route::namespace('App\Http\Controllers')->prefix('gestisp')->group(function () {

    // Sucursales
    Route::resource('branches', 'BranchController')->names('branches');

    // Clientes
    Route::resource('clients', 'ClientController')->names('clients');

    // Servicios
    Route::resource('services', 'ServiceController')->names('services');

    // Planes
    Route::resource('plans', 'PlanController')->names('plans');

    // Contratos
    Route::resource('contracts', 'ContractController')->names('contracts');

    // Facturas
    Route::resource('invoices', 'InvoiceController')->names('invoices');

    // Cargos adicionales
    Route::resource('additionalCharges', 'AdditionalChargeController')->names('additionalCharges');

    // Pagos
    Route::resource('payments', 'PaymentController')->names('payments');


    // Cajas
    Route::resource('cashRegisters', 'CashRegisterController')->names('cashRegisters');

    //Almacenes
    Route::resource('warehouses', 'WarehouseController')->names('warehouses');

    //Materiales
    Route::resource('materials', 'MaterialController')->names('materials');

    //Categorías de materiales
    Route::resource('categories', 'CategoryController')->names('categories');

    //Movimientos de material
    Route::resource('movements', 'MaterialMovementController')->names('movements');

});

// Ruta del buscador de clientes
Route::get('/clients/search', [ClientController::class, 'searchView'])->name('clients.searchView');
Route::post('/clients/search', [ClientController::class, 'search'])->name('clients.search');

// Ruta para exportar clientes a excel
Route::get('/clients/export', [ClientController::class, 'export'])->name('clients.export');

// Ruta para crear contrato a cliente
Route::get('contracts/create/{client}', [ContractController::class, 'create'])->name('contracts.create');

// Ruta para obtener las sucursales en el login
Route::get('/user/branches', [LoginController::class, 'getBranches'])->name('user.branches');

// Generación de facturas
Route::post('/invoices/generate', [InvoiceController::class, 'generateInvoices'])->name('invoices.generate');

// Descarga de PDF
Route::get('/invoices/{id}/download-pdf', [InvoiceController::class, 'downloadInvoicePdf'])->name('invoices.download-pdf');

// Ruta para exportar contratos a excel
Route::get('/contracts/export', [ContractController::class, 'export'])->name('contracts.export');

// Gestión de la caja
Route::get('/cash-register/status', [CashRegisterController::class, 'status'])->name('cash_register.status');
Route::post('/cash-register/open', [CashRegisterController::class, 'open'])->name('cash_register.open');
Route::post('/cash-register/close', [CashRegisterController::class, 'close'])->name('cash_register.close');

//pagos
Route::POST('/payments/search', [PaymentController::class, 'search'])->name('payments.search');
Route::get('/payments/search', [PaymentController::class, 'searchView'])->name('payments.searchView');
//Exportar pdf con reporte de pagos
Route::get('/payments/export-pdf', [PaymentController::class, 'exportPaymentsPDF'])->name('payments.export');
//Exportar pagos en excel
Route::get('payments/export-excel', [PaymentController::class, 'export'])->name('payments.export-excel');
//Ruta para movimientos de caja
Route::get('cashRegisters/trasactions', [CashRegisterTransactionController::class, 'index'])->name('transactions.index');
Route::post('cashRegisters/trasactions', [CashRegisterTransactionController::class, 'store'])->name('transactions.store');
Route::get('cashRegisters/transactions/history', [CashRegisterTransactionController::class, 'history'])->name('transactions.history');
Route::get('cashRegisters/transactions/report-pdf', [CashRegisterTransactionController::class, 'exportHistoryTransactionsPDF'])->name('transactions.export');
Route::get('cashRegisters/transactions/export-excel', [CashRegisterTransactionController::class, 'export'])->name('transactions.export-excel');
//Movimiento de material (consulta SN)
Route::get('inventories/{warehouse}/materials/{material}/serial-numbers', [MaterialMovementController::class, 'getAvailableSerialNumbers'])->name('movements.query_sn');
Route::get('inventories/{warehouse}/materials/{material}/quantity', [MaterialMovementController::class, 'getAvailableQuantity'])->name('movements.material_quantity');;
//Pdf de inventarios
Route::get('/warehouse/{warehouse}/pdf', [WarehouseController::class, 'generatePdf'])->name('warehouse.pdf');
//Historial de movimientos de almacen
Route::get('materials/movements/history', [MaterialMovementController::class, 'history'])->name('movements.history');
Route::get('movements/history', [MaterialMovementController::class, 'history'])->name('movements.history_data');
//Exportar historial de movimientos en pdf y excel
Route::get('materials/movements/history/pdf', [MaterialMovementController::class, 'exportMovementsPDF'])->name('movements.pdf');
Route::get('materials/movements/history/excel', [MaterialMovementController::class, 'export'])->name('movements.excel');
