<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\RecurringTransactionController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\CoaController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\AccountingPeriodController;
use App\Http\Controllers\AuditTrailController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\BankReconciliationController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PurchasingController;
use App\Http\Controllers\SellingController;
use App\Http\Controllers\FixedAssetController;
use App\Http\Controllers\ManufacturingController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\StockOpnameController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('companies')->name('companies.')->group(function () {
        Route::get('/', [CompanyController::class, 'index'])->name('index');
        Route::get('/create', [CompanyController::class, 'create'])->name('create');
        Route::post('/', [CompanyController::class, 'store'])->name('store');
        Route::get('/{company}/edit', [CompanyController::class, 'edit'])->name('edit');
        Route::put('/{company}', [CompanyController::class, 'update'])->name('update');
        Route::post('/{company}/switch', [CompanyController::class, 'switch'])->name('switch');
    });

    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('transactions', TransactionController::class);
    Route::prefix('recurring')->name('recurring.')->group(function () {
        Route::get('/', [RecurringTransactionController::class, 'index'])->name('index');
        Route::get('/create', [RecurringTransactionController::class, 'create'])->name('create');
        Route::post('/', [RecurringTransactionController::class, 'store'])->name('store');
        Route::get('/{recurring}/edit', [RecurringTransactionController::class, 'edit'])->name('edit');
        Route::put('/{recurring}', [RecurringTransactionController::class, 'update'])->name('update');
        Route::post('/{recurring}/toggle', [RecurringTransactionController::class, 'toggle'])->name('toggle');
        Route::delete('/{recurring}', [RecurringTransactionController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('approvals')->name('approvals.')->group(function () {
        Route::get('/', [ApprovalController::class, 'index'])->name('index');
        Route::post('/{approvable}/approve', [ApprovalController::class, 'approve'])->name('approve');
        Route::post('/{approvable}/reject', [ApprovalController::class, 'reject'])->name('reject');
        Route::get('/config', [ApprovalController::class, 'config'])->name('config');
        Route::post('/config', [ApprovalController::class, 'configStore'])->name('config.store');
        Route::delete('/config/{approvalConfig}', [ApprovalController::class, 'configDestroy'])->name('config.destroy');
    });

    Route::resource('budgets', BudgetController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('invoices', InvoiceController::class);
    Route::post('/invoices/{invoice}/mark-paid', [InvoiceController::class, 'markPaid'])->name('invoices.mark-paid');
    Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'exportPdf'])->name('invoices.pdf');

    Route::resource('debts', DebtController::class)->except(['show']);
    Route::post('/debts/{debt}/pay', [DebtController::class, 'pay'])->name('debts.pay');

    Route::resource('users', UserController::class)->except(['show']);

    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [UserRoleController::class, 'index'])->name('index');
        Route::put('/{user}', [UserRoleController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserRoleController::class, 'remove'])->name('remove');
    });

    Route::prefix('coa')->name('coa.')->group(function () {
        Route::get('/', [CoaController::class, 'index'])->name('index');
        Route::get('/create', [CoaController::class, 'create'])->name('create');
        Route::post('/', [CoaController::class, 'store'])->name('store');
        Route::get('/{coa}/edit', [CoaController::class, 'edit'])->name('edit');
        Route::put('/{coa}', [CoaController::class, 'update'])->name('update');
        Route::delete('/{coa}', [CoaController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('journals')->name('journals.')->group(function () {
        Route::get('/', [JournalController::class, 'index'])->name('index');
        Route::get('/ledger', [JournalController::class, 'ledger'])->name('ledger');
        Route::get('/create', [JournalController::class, 'create'])->name('create');
        Route::post('/', [JournalController::class, 'store'])->name('store');
        Route::get('/{journal}', [JournalController::class, 'show'])->name('show');
    });

    Route::get('/reports/balance-sheet', [JournalController::class, 'balanceSheet'])->name('reports.balance-sheet');
    Route::get('/reports/income-statement', [JournalController::class, 'incomeStatement'])->name('reports.income-statement');

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/export-pdf', [ReportController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/export-excel', [ReportController::class, 'exportExcel'])->name('export-excel');
        Route::get('/stock', [ReportController::class, 'stockReport'])->name('stock');
        Route::get('/stock/export-pdf', [ReportController::class, 'exportStockPdf'])->name('stock.export-pdf');
        Route::get('/purchases', [ReportController::class, 'purchaseReport'])->name('purchases');
        Route::get('/sales', [ReportController::class, 'salesReport'])->name('sales');
    });

    Route::get('/reports/cash-flow', [JournalController::class, 'cashFlow'])->name('reports.cash-flow');

    Route::prefix('accounting-periods')->name('accounting-periods.')->group(function () {
        Route::get('/', [AccountingPeriodController::class, 'index'])->name('index');
        Route::get('/create', [AccountingPeriodController::class, 'create'])->name('create');
        Route::post('/', [AccountingPeriodController::class, 'store'])->name('store');
        Route::post('/{period}/close', [AccountingPeriodController::class, 'close'])->name('close');
        Route::post('/{period}/open', [AccountingPeriodController::class, 'open'])->name('open');
        Route::delete('/{period}', [AccountingPeriodController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('audit-trails')->name('audit-trails.')->group(function () {
        Route::get('/', [AuditTrailController::class, 'index'])->name('index');
        Route::get('/export/csv', [AuditTrailController::class, 'exportCsv'])->name('export-csv');
        Route::get('/{auditTrail}', [AuditTrailController::class, 'show'])->name('show');
    });

    Route::prefix('bank-accounts')->name('bank-accounts.')->group(function () {
        Route::get('/', [BankAccountController::class, 'index'])->name('index');
        Route::get('/create', [BankAccountController::class, 'create'])->name('create');
        Route::post('/', [BankAccountController::class, 'store'])->name('store');
        Route::get('/{bankAccount}/edit', [BankAccountController::class, 'edit'])->name('edit');
        Route::put('/{bankAccount}', [BankAccountController::class, 'update'])->name('update');
        Route::delete('/{bankAccount}', [BankAccountController::class, 'destroy'])->name('destroy');
        Route::get('/{bankAccount}/balance', [BankAccountController::class, 'balance'])->name('balance');
    });

    Route::prefix('bank-reconciliations')->name('bank-reconciliations.')->group(function () {
        Route::get('/', [BankReconciliationController::class, 'index'])->name('index');
        Route::get('/create', [BankReconciliationController::class, 'create'])->name('create');
        Route::post('/', [BankReconciliationController::class, 'store'])->name('store');
        Route::get('/{reconciliation}', [BankReconciliationController::class, 'show'])->name('show');
        Route::post('/{reconciliation}/complete', [BankReconciliationController::class, 'complete'])->name('complete');
        Route::delete('/{reconciliation}', [BankReconciliationController::class, 'destroy'])->name('destroy');
    });

    // ============ SUPPLIERS ============
    Route::prefix('suppliers')->name('suppliers.')->group(function () {
        Route::get('/', [SupplierController::class, 'index'])->name('index');
        Route::get('/create', [SupplierController::class, 'create'])->name('create');
        Route::post('/', [SupplierController::class, 'store'])->name('store');
        Route::get('/{supplier}/edit', [SupplierController::class, 'edit'])->name('edit');
        Route::put('/{supplier}', [SupplierController::class, 'update'])->name('update');
        Route::delete('/{supplier}', [SupplierController::class, 'destroy'])->name('destroy');
    });

    // ============ CUSTOMERS ============
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/create', [CustomerController::class, 'create'])->name('create');
        Route::post('/', [CustomerController::class, 'store'])->name('store');
        Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
        Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
        Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');
    });

    // ============ CURRENCIES ============
    Route::prefix('currencies')->name('currencies.')->group(function () {
        Route::get('/', [CurrencyController::class, 'index'])->name('index');
        Route::get('/create', [CurrencyController::class, 'create'])->name('create');
        Route::post('/', [CurrencyController::class, 'store'])->name('store');
        Route::get('/{currency}/edit', [CurrencyController::class, 'edit'])->name('edit');
        Route::put('/{currency}', [CurrencyController::class, 'update'])->name('update');
        Route::delete('/{currency}', [CurrencyController::class, 'destroy'])->name('destroy');
    });

    // ============ TAXES ============
    Route::prefix('taxes')->name('taxes.')->group(function () {
        Route::get('/', [TaxController::class, 'index'])->name('index');
        Route::get('/create', [TaxController::class, 'create'])->name('create');
        Route::post('/', [TaxController::class, 'store'])->name('store');
        Route::get('/{tax}/edit', [TaxController::class, 'edit'])->name('edit');
        Route::put('/{tax}', [TaxController::class, 'update'])->name('update');
        Route::delete('/{tax}', [TaxController::class, 'destroy'])->name('destroy');
    });

    // ============ BRANCHES ============
    Route::prefix('branches')->name('branches.')->group(function () {
        Route::get('/', [BranchController::class, 'index'])->name('index');
        Route::get('/create', [BranchController::class, 'create'])->name('create');
        Route::post('/', [BranchController::class, 'store'])->name('store');
        Route::get('/{branch}/edit', [BranchController::class, 'edit'])->name('edit');
        Route::put('/{branch}', [BranchController::class, 'update'])->name('update');
        Route::delete('/{branch}', [BranchController::class, 'destroy'])->name('destroy');
    });

    // ============ PRODUCTS ============
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [InventoryController::class, 'indexProducts'])->name('index');
        Route::get('/create', [InventoryController::class, 'createProduct'])->name('create');
        Route::post('/', [InventoryController::class, 'storeProduct'])->name('store');
        Route::get('/{product}/edit', [InventoryController::class, 'editProduct'])->name('edit');
        Route::put('/{product}', [InventoryController::class, 'updateProduct'])->name('update');
        Route::delete('/{product}', [InventoryController::class, 'destroyProduct'])->name('destroy');
        Route::get('/{product}/stock-card', [InventoryController::class, 'stockCard'])->name('stock-card');
        Route::get('/{product}/stock', [StockOpnameController::class, 'getProductStock'])->name('stock');
    });

    // ============ WAREHOUSES ============
    Route::prefix('warehouses')->name('warehouses.')->group(function () {
        Route::get('/', [InventoryController::class, 'indexWarehouses'])->name('index');
        Route::get('/create', [InventoryController::class, 'createWarehouse'])->name('create');
        Route::post('/', [InventoryController::class, 'storeWarehouse'])->name('store');
        Route::get('/{warehouse}/edit', [InventoryController::class, 'editWarehouse'])->name('edit');
        Route::put('/{warehouse}', [InventoryController::class, 'updateWarehouse'])->name('update');
        Route::delete('/{warehouse}', [InventoryController::class, 'destroyWarehouse'])->name('destroy');
    });

    // ============ PURCHASE REQUESTS ============
    Route::prefix('purchase-requests')->name('purchase-requests.')->group(function () {
        Route::get('/', [PurchasingController::class, 'indexRequests'])->name('index');
        Route::post('/', [PurchasingController::class, 'storeRequest'])->name('store');
        Route::post('/{purchaseRequest}/status', [PurchasingController::class, 'updateRequestStatus'])->name('status');
    });

    // ============ PURCHASE ORDERS ============
    Route::prefix('purchase-orders')->name('purchase-orders.')->group(function () {
        Route::get('/', [PurchasingController::class, 'indexOrders'])->name('index');
        Route::post('/', [PurchasingController::class, 'storeOrder'])->name('store');
        Route::get('/{purchaseOrder}', [PurchasingController::class, 'showOrder'])->name('show');
        Route::get('/{purchaseOrder}/pdf', [PurchasingController::class, 'exportOrderPdf'])->name('pdf');
    });

    // ============ GOODS RECEIVES ============
    Route::prefix('goods-receives')->name('goods-receives.')->group(function () {
        Route::get('/', [PurchasingController::class, 'indexReceives'])->name('index');
        Route::post('/', [PurchasingController::class, 'storeReceive'])->name('store');
        Route::get('/{receive}/receive', [PurchasingController::class, 'receive'])->name('receive');
    });

    // ============ PURCHASE RETURNS ============
    Route::prefix('purchase-returns')->name('purchase-returns.')->group(function () {
        Route::get('/', [PurchasingController::class, 'indexReturns'])->name('index');
        Route::post('/', [PurchasingController::class, 'storeReturn'])->name('store');
    });

    // ============ QUOTATIONS ============
    Route::prefix('quotations')->name('quotations.')->group(function () {
        Route::get('/', [SellingController::class, 'indexQuotations'])->name('index');
        Route::post('/', [SellingController::class, 'storeQuotation'])->name('store');
        Route::post('/{quotation}/convert', [SellingController::class, 'convertToOrder'])->name('convert');
        Route::get('/{quotation}/pdf', [SellingController::class, 'exportQuotationPdf'])->name('pdf');
    });

    // ============ SALES ORDERS ============
    Route::prefix('sales-orders')->name('sales-orders.')->group(function () {
        Route::get('/', [SellingController::class, 'indexOrders'])->name('index');
        Route::post('/', [SellingController::class, 'storeOrder'])->name('store');
        Route::get('/{salesOrder}', [SellingController::class, 'showOrder'])->name('show');
        Route::get('/{salesOrder}/pdf', [SellingController::class, 'exportSalesOrderPdf'])->name('pdf');
    });

    // ============ DELIVERY ORDERS ============
    Route::prefix('delivery-orders')->name('delivery-orders.')->group(function () {
        Route::get('/', [SellingController::class, 'indexDeliveries'])->name('index');
        Route::post('/', [SellingController::class, 'storeDelivery'])->name('store');
        Route::get('/{do}/deliver', [SellingController::class, 'deliver'])->name('deliver');
    });

    // ============ SALES RETURNS ============
    Route::prefix('sales-returns')->name('sales-returns.')->group(function () {
        Route::get('/', [SellingController::class, 'indexReturns'])->name('index');
        Route::post('/', [SellingController::class, 'storeReturn'])->name('store');
    });

    // ============ FIXED ASSETS ============
    Route::prefix('fixed-assets')->name('fixed-assets.')->group(function () {
        Route::get('/', [FixedAssetController::class, 'index'])->name('index');
        Route::get('/create', [FixedAssetController::class, 'create'])->name('create');
        Route::post('/', [FixedAssetController::class, 'store'])->name('store');
        Route::get('/{fixedAsset}/edit', [FixedAssetController::class, 'edit'])->name('edit');
        Route::put('/{fixedAsset}', [FixedAssetController::class, 'update'])->name('update');
        Route::delete('/{fixedAsset}', [FixedAssetController::class, 'destroy'])->name('destroy');
        Route::post('/{fixedAsset}/calculate-depreciation', [FixedAssetController::class, 'calculateDepreciation'])->name('calculate-depreciation');
        Route::get('/{fixedAsset}/depreciation', [FixedAssetController::class, 'showDepreciation'])->name('depreciation');
    });

    // ============ BOMS ============
    Route::prefix('boms')->name('boms.')->group(function () {
        Route::get('/', [ManufacturingController::class, 'indexBoms'])->name('index');
        Route::post('/', [ManufacturingController::class, 'storeBom'])->name('store');
        Route::get('/{bom}/edit', [ManufacturingController::class, 'editBom'])->name('edit');
        Route::put('/{bom}', [ManufacturingController::class, 'updateBom'])->name('update');
        Route::delete('/{bom}', [ManufacturingController::class, 'destroyBom'])->name('destroy');
    });

    // ============ WORK ORDERS ============
    Route::prefix('work-orders')->name('work-orders.')->group(function () {
        Route::get('/', [ManufacturingController::class, 'indexWorkOrders'])->name('index');
        Route::post('/', [ManufacturingController::class, 'storeWorkOrder'])->name('store');
        Route::post('/{workOrder}/complete', [ManufacturingController::class, 'completeWorkOrder'])->name('complete');
    });

    // ============ STOCK OPNAME ============
    Route::prefix('stock-opnames')->name('stock-opnames.')->group(function () {
        Route::get('/', [StockOpnameController::class, 'index'])->name('index');
        Route::get('/create', [StockOpnameController::class, 'create'])->name('create');
        Route::post('/', [StockOpnameController::class, 'store'])->name('store');
        Route::get('/{stockOpname}', [StockOpnameController::class, 'show'])->name('show');
        Route::post('/{stockOpname}/complete', [StockOpnameController::class, 'complete'])->name('complete');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::fallback(function () {
    abort(404);
});
