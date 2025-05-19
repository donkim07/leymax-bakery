<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\CreditController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\BusinessDataController;

// Public routes
Route::get('/', function () {
    return view('index');
})->name('home');

// Guest routes (only accessible when not logged in)
Route::middleware('guest')->group(function () {
    // Authentication Routes
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);

    // Password Reset Routes
    Route::get('password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');
    
    // Email Verification Routes
    Route::get('email/verify', [App\Http\Controllers\Auth\VerificationController::class, 'show'])->name('verification.notice');
    Route::get('email/verify/{id}/{hash}', [App\Http\Controllers\Auth\VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('email/resend', [App\Http\Controllers\Auth\VerificationController::class, 'resend'])->name('verification.resend');
});

// Protected routes (only accessible when logged in)
Route::middleware('auth')->group(function () {
    // Logout Route
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    
    // Dashboard Routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/analytics', [DashboardController::class, 'analytics'])->name('dashboard.analytics');
    
    // Global Reports Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
        Route::get('/financial', [ReportController::class, 'financial'])->name('financial');
    });
    
    // System Settings Routes
    Route::prefix('settings')->name('settings.')->group(function () {
        // General Settings
        Route::get('/general', [SettingsController::class, 'general'])->name('general');
        Route::post('/general/update', [SettingsController::class, 'updateGeneral'])->name('general.update');
        Route::post('/currency/add', [SettingsController::class, 'addCurrency'])->name('currency.add');
        Route::post('/payment/process', [SettingsController::class, 'processPayment'])->name('payment.process');
        
        // User Management
        Route::get('/users', [SettingsController::class, 'users'])->name('users');
        Route::get('/users/create', [SettingsController::class, 'createUser'])->name('users.create');
        Route::post('/users', [SettingsController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{user}/edit', [SettingsController::class, 'editUser'])->name('users.edit');
        Route::put('/users/{user}', [SettingsController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [SettingsController::class, 'deleteUser'])->name('users.destroy');
        
        // Roles & Permissions
        Route::get('/roles', [SettingsController::class, 'roles'])->name('roles');
        Route::get('/roles/create', [SettingsController::class, 'createRole'])->name('roles.create');
        Route::post('/roles', [SettingsController::class, 'storeRole'])->name('roles.store');
        Route::get('/roles/{role}/edit', [SettingsController::class, 'editRole'])->name('roles.edit');
        Route::put('/roles/{role}', [SettingsController::class, 'updateRole'])->name('roles.update');
        Route::delete('/roles/{role}', [SettingsController::class, 'deleteRole'])->name('roles.destroy');
        
        // Backup & Restore
        Route::get('/backup', [SettingsController::class, 'backup'])->name('backup');
        Route::post('/backup', [SettingsController::class, 'createBackup'])->name('backup.create');
        Route::get('/backup/{filename}/download', [SettingsController::class, 'downloadBackup'])->name('backup.download');
        Route::delete('/backup/{filename}', [SettingsController::class, 'deleteBackup'])->name('backup.destroy');
        Route::post('/backup/restore', [SettingsController::class, 'restoreBackup'])->name('backup.restore');
    });
    
    // Business Switch Route
    Route::get('/switch-business/{business}', [BusinessController::class, 'switch'])->name('switch.business');
    
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::put('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    
    // Language Route
    Route::post('/language/toggle', [LanguageController::class, 'toggle'])->name('language.toggle');
    
    // Theme Route
    Route::post('/theme/toggle', [ThemeController::class, 'toggle'])->name('theme.toggle');
    
    // Business Management Routes
    Route::prefix('business')->name('business.')->group(function () {
        Route::get('/', [BusinessController::class, 'index'])->name('index');
        Route::get('/create', [BusinessController::class, 'create'])->name('create');
        Route::post('/', [BusinessController::class, 'store'])->name('store');
        Route::get('/{business}', [BusinessController::class, 'show'])->name('show');
        Route::get('/{business}/edit', [BusinessController::class, 'edit'])->name('edit');
        Route::put('/{business}', [BusinessController::class, 'update'])->name('update');
        Route::delete('/{business}', [BusinessController::class, 'destroy'])->name('destroy');
        Route::post('/{business}/toggle-status', [BusinessController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Store Management Routes
    Route::prefix('stores')->name('stores.')->group(function () {
        Route::get('/', [StoreController::class, 'index'])->name('index');
        Route::get('/create', [StoreController::class, 'create'])->name('create');
        Route::post('/', [StoreController::class, 'store'])->name('store');
        Route::get('/{store}', [StoreController::class, 'show'])->name('show');
        Route::get('/{store}/edit', [StoreController::class, 'edit'])->name('edit');
        Route::put('/{store}', [StoreController::class, 'update'])->name('update');
        Route::delete('/{store}', [StoreController::class, 'destroy'])->name('destroy');
    });

    // Product Management Routes
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('/{product}', [ProductController::class, 'show'])->name('show');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
    });

    // Inventory Management Routes
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('/movements', [InventoryController::class, 'movements'])->name('movements');
        Route::post('/adjust', [InventoryController::class, 'adjust'])->name('adjust');
        Route::post('/transfer', [InventoryController::class, 'transfer'])->name('transfer');
    });

    // User Management Routes
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });

    // Bakery Shop Routes
    Route::prefix('bakery')->name('bakery.')->middleware(['auth', \App\Http\Middleware\ValidateBusinessType::class])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'bakery'])->name('dashboard');
        Route::get('/stores/main', [StoreController::class, 'bakeryMain'])->name('stores.main');
        Route::get('/stores/sub', [StoreController::class, 'bakerySub'])->name('stores.sub');
        Route::get('/stores/map', [StoreController::class, 'bakeryMap'])->name('stores.map');
        Route::get('/items', [ProductController::class, 'bakeryItems'])->name('items');
        Route::get('/categories', [ProductController::class, 'bakeryCategories'])->name('categories');
        Route::get('/stock/adjustments', [InventoryController::class, 'bakeryAdjustments'])->name('stock.adjustments');
        Route::get('/stock/movement', [InventoryController::class, 'bakeryMovement'])->name('stock.movement');
        Route::get('/transfers', [InventoryController::class, 'bakeryTransfers'])->name('transfers');
        Route::get('/alerts', [InventoryController::class, 'bakeryAlerts'])->name('alerts');
        
        // Manufacturing Routes
        Route::get('/manufacturing/assembly', [App\Http\Controllers\ManufacturingController::class, 'assembly'])->name('manufacturing.assembly');
        Route::post('/manufacturing/assembly', [App\Http\Controllers\ManufacturingController::class, 'storeAssembledItem'])->name('manufacturing.assembly.store');
        Route::put('/manufacturing/assembly/{assembledItem}', [App\Http\Controllers\ManufacturingController::class, 'updateAssembledItem'])->name('manufacturing.assembly.update');
        Route::delete('/manufacturing/assembly/{assembledItem}', [App\Http\Controllers\ManufacturingController::class, 'destroyAssembledItem'])->name('manufacturing.assembly.destroy');
        
        // New routes for AJAX creation of categories, groups, and sizes
        Route::post('/manufacturing/assembly/category', [App\Http\Controllers\ManufacturingController::class, 'storeCategory'])->name('manufacturing.assembly.category.store');
        Route::post('/manufacturing/assembly/group', [App\Http\Controllers\ManufacturingController::class, 'storeGroup'])->name('manufacturing.assembly.group.store');
        Route::post('/manufacturing/assembly/size', [App\Http\Controllers\ManufacturingController::class, 'storeSize'])->name('manufacturing.assembly.size.store');
        
        Route::post('/manufacturing/assembly/{assembledItem}/ingredients', [App\Http\Controllers\ManufacturingController::class, 'addIngredient'])->name('manufacturing.assembly.ingredients.add');
        Route::delete('/manufacturing/assembly/ingredients/{ingredient}', [App\Http\Controllers\ManufacturingController::class, 'removeIngredient'])->name('manufacturing.assembly.ingredients.remove');
        Route::post('/manufacturing/assembly/{assembledItem}/paste-divisions', [App\Http\Controllers\ManufacturingController::class, 'createPasteDivision'])->name('manufacturing.assembly.paste-divisions.create');
        Route::delete('/manufacturing/assembly/paste-divisions/{pasteDivision}', [App\Http\Controllers\ManufacturingController::class, 'removePasteDivision'])->name('manufacturing.assembly.paste-divisions.remove');
        Route::get('/manufacturing/assembly/{assembledItem}', [App\Http\Controllers\ManufacturingController::class, 'getAssembledItem'])->name('manufacturing.assembly.show');
        Route::get('/manufacturing/assembly/{assembledItem}/ingredients', [App\Http\Controllers\ManufacturingController::class, 'getIngredients'])->name('manufacturing.assembly.ingredients');
        Route::get('/manufacturing/assembly/{assembledItem}/paste-divisions', [App\Http\Controllers\ManufacturingController::class, 'getPasteDivisions'])->name('manufacturing.assembly.paste-divisions');
        Route::get('/manufacturing/process', [App\Http\Controllers\ManufacturingController::class, 'process'])->name('manufacturing.process');
        Route::post('/manufacturing/process', [App\Http\Controllers\ManufacturingController::class, 'storeProcess'])->name('manufacturing.process.store');
        Route::post('/manufacturing/process/{process}/complete', [App\Http\Controllers\ManufacturingController::class, 'completeProcess'])->name('manufacturing.process.complete');
        Route::post('/manufacturing/process/complete-all', [App\Http\Controllers\ManufacturingController::class, 'completeAllProcesses'])->name('manufacturing.process.complete-all');
        Route::put('/manufacturing/process/ingredient/{ingredient}', [App\Http\Controllers\ManufacturingController::class, 'updateProcessIngredient'])->name('manufacturing.process.ingredient.update');
        Route::post('/manufacturing/process/{process}/waste', [App\Http\Controllers\ManufacturingController::class, 'recordWaste'])->name('manufacturing.process.waste');
        Route::get('/manufacturing/adjustment', [App\Http\Controllers\ManufacturingController::class, 'adjustment'])->name('manufacturing.adjustment');
        Route::get('/manufacturing/planning', [App\Http\Controllers\ManufacturingController::class, 'planning'])->name('manufacturing.planning');
        Route::get('/manufacturing/waste', [App\Http\Controllers\ManufacturingController::class, 'waste'])->name('manufacturing.waste');
        Route::get('/manufacturing/metrics', [App\Http\Controllers\ManufacturingController::class, 'metrics'])->name('manufacturing.metrics');
        
        // API Routes for Manufacturing Processes
        Route::prefix('api')->group(function () {
            Route::get('/manufacturing-process/{process}/ingredients', [App\Http\Controllers\ManufacturingController::class, 'getProcessIngredients'])->name('api.manufacturing.process.ingredients');
            Route::get('/manufacturing-process/{process}/wastes', [App\Http\Controllers\ManufacturingController::class, 'getProcessWastes'])->name('api.manufacturing.process.wastes');
            Route::get('/user/business-id', function () {
                return response()->json([
                    'success' => true,
                    'business_id' => session('business_id'),
                    'user_id' => Auth::id(),
                    'has_company' => Auth::user() && Auth::user()->company ? true : false,
                    'company_id' => Auth::user() && Auth::user()->company ? Auth::user()->company->id : null,
                ]);
            })->name('api.user.business-id');
        });
        
        Route::get('/sales/orders', [OrderController::class, 'bakeryOrders'])->name('sales.orders');
        Route::get('/sales/pos', [OrderController::class, 'bakeryPos'])->name('sales.pos');
        Route::get('/sales/invoices', [InvoiceController::class, 'bakeryInvoices'])->name('sales.invoices');
        Route::get('/sales/invoices/paid', [InvoiceController::class, 'bakeryPaidInvoices'])->name('sales.invoices.paid');
        Route::get('/sales/invoices/unpaid', [InvoiceController::class, 'bakeryUnpaidInvoices'])->name('sales.invoices.unpaid');
        Route::get('/sales/invoices/draft', [InvoiceController::class, 'bakeryDraftInvoices'])->name('sales.invoices.draft');
        Route::get('/delivery', [OrderController::class, 'bakeryDelivery'])->name('delivery');
        Route::get('/returns', [OrderController::class, 'bakeryReturns'])->name('returns');
        Route::get('/suppliers', [SupplierController::class, 'bakerySuppliers'])->name('suppliers');
        Route::get('/purchases/orders', [PurchaseController::class, 'bakeryOrders'])->name('purchases.orders');
        Route::get('/bills', [PurchaseController::class, 'bakeryBills'])->name('bills');
        Route::get('/credits', [PurchaseController::class, 'bakeryCredits'])->name('credits');
        Route::get('/receive', [PurchaseController::class, 'bakeryReceive'])->name('receive');
        Route::get('/purchase-returns', [PurchaseController::class, 'bakeryReturns'])->name('purchase-returns');
        Route::get('/accounts', [AccountingController::class, 'bakeryAccounts'])->name('accounts');
        Route::get('/expenses', [AccountingController::class, 'bakeryExpenses'])->name('expenses');
        Route::get('/journal', [AccountingController::class, 'bakeryJournal'])->name('journal');
        Route::get('/banking', [AccountingController::class, 'bakeryBanking'])->name('banking');
        Route::get('/payroll', [AccountingController::class, 'bakeryPayroll'])->name('payroll');
        Route::get('/reports', [ReportController::class, 'bakery'])->name('reports');
        
        // Settings Routes
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/company', [SettingsController::class, 'bakeryCompany'])->name('company');
            Route::post('/company', [SettingsController::class, 'updateBakeryCompany'])->name('company.update');
            Route::get('/branches', [SettingsController::class, 'bakeryBranches'])->name('branches');
            Route::get('/users', [SettingsController::class, 'bakeryUsers'])->name('users');
            Route::get('/tax', [SettingsController::class, 'bakeryTax'])->name('tax');
            Route::get('/email', [SettingsController::class, 'bakeryEmail'])->name('email');
            Route::get('/localization', [SettingsController::class, 'bakeryLocalization'])->name('localization');
        });
    });

    // Bakery Financial Management Routes
    Route::prefix('bakery')->middleware(['auth', 'verified'])->group(function () {
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('bakery.sales.invoices.show');
        Route::get('/bills/{bill}', [BillController::class, 'show'])->name('bakery.bills.show');
        Route::get('/credits/{credit}', [CreditController::class, 'show'])->name('bakery.credits.show');
        Route::post('/invoices/{invoice}/mark-paid', [InvoiceController::class, 'markAsPaid']);
    });

    Route::prefix('api/bakery')->middleware(['auth', 'verified'])->group(function () {
        Route::post('/bills/{bill}/pay', 'BillController@pay');
        Route::post('/credits/{credit}/settle', 'CreditController@settle');
    });

    // Cake Tools Routes
    Route::prefix('tools')->name('tools.')->middleware(['auth', \App\Http\Middleware\ValidateBusinessType::class])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'tools'])->name('dashboard');
        Route::get('/stores/main', [StoreController::class, 'toolsMain'])->name('stores.main');
        Route::get('/stores/sub', [StoreController::class, 'toolsSub'])->name('stores.sub');
        Route::get('/stores/map', [StoreController::class, 'toolsMap'])->name('stores.map');
        Route::get('/items', [ProductController::class, 'toolsItems'])->name('items');
        Route::get('/categories', [ProductController::class, 'toolsCategories'])->name('categories');
        Route::get('/stock/adjustments', [InventoryController::class, 'toolsAdjustments'])->name('stock.adjustments');
        Route::get('/stock/movement', [InventoryController::class, 'toolsMovement'])->name('stock.movement');
        Route::get('/transfers', [InventoryController::class, 'toolsTransfers'])->name('transfers');
        Route::get('/alerts', [InventoryController::class, 'toolsAlerts'])->name('alerts');
        Route::get('/sales/orders', [OrderController::class, 'toolsOrders'])->name('sales.orders');
        Route::get('/sales/pos', [OrderController::class, 'toolsPos'])->name('sales.pos');
        Route::get('/invoices', [OrderController::class, 'toolsInvoices'])->name('invoices');
        Route::get('/invoices/paid', [OrderController::class, 'toolsPaidInvoices'])->name('invoices.paid');
        Route::get('/delivery', [OrderController::class, 'toolsDelivery'])->name('delivery');
        Route::get('/returns', [OrderController::class, 'toolsReturns'])->name('returns');
        Route::get('/suppliers', [SupplierController::class, 'toolsSuppliers'])->name('suppliers');
        Route::get('/purchases/orders', [PurchaseController::class, 'toolsOrders'])->name('purchases.orders');
        Route::get('/bills', [PurchaseController::class, 'toolsBills'])->name('bills');
        Route::get('/credits', [PurchaseController::class, 'toolsCredits'])->name('credits');
        Route::get('/receive', [PurchaseController::class, 'toolsReceive'])->name('receive');
        Route::get('/purchase-returns', [PurchaseController::class, 'toolsReturns'])->name('purchase-returns');
        Route::get('/accounts', [AccountingController::class, 'toolsAccounts'])->name('accounts');
        Route::get('/expenses', [AccountingController::class, 'toolsExpenses'])->name('expenses');
        Route::get('/journal', [AccountingController::class, 'toolsJournal'])->name('journal');
        Route::get('/banking', [AccountingController::class, 'toolsBanking'])->name('banking');
        Route::get('/payroll', [AccountingController::class, 'toolsPayroll'])->name('payroll');
        Route::get('/reports', [ReportController::class, 'tools'])->name('reports');
        
        // Settings Routes
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/company', [SettingsController::class, 'toolsCompany'])->name('company');
            Route::get('/branches', [SettingsController::class, 'toolsBranches'])->name('branches');
            Route::get('/users', [SettingsController::class, 'toolsUsers'])->name('users');
            Route::get('/tax', [SettingsController::class, 'toolsTax'])->name('tax');
            Route::get('/email', [SettingsController::class, 'toolsEmail'])->name('email');
            Route::get('/localization', [SettingsController::class, 'toolsLocalization'])->name('localization');
        });
    });

    // Academy Routes
    Route::prefix('academy')->name('academy.')->middleware(['auth', \App\Http\Middleware\ValidateBusinessType::class])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'academy'])->name('dashboard');
        Route::get('/courses', [CourseController::class, 'index'])->name('courses');
        Route::get('/categories', [CourseController::class, 'categories'])->name('categories');
        Route::get('/media', [CourseController::class, 'media'])->name('media');
        Route::get('/students', [StudentController::class, 'index'])->name('students');
        Route::get('/progress', [StudentController::class, 'progress'])->name('progress');
        Route::get('/links', [CourseController::class, 'links'])->name('links');
        Route::get('/orders', [OrderController::class, 'academyOrders'])->name('orders');
        Route::get('/invoices', [OrderController::class, 'academyInvoices'])->name('invoices');
        Route::get('/reports', [ReportController::class, 'academy'])->name('reports');
        
        // Settings Routes
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/company', [SettingsController::class, 'academyCompany'])->name('company');
            Route::get('/users', [SettingsController::class, 'academyUsers'])->name('users');
            Route::get('/email', [SettingsController::class, 'academyEmail'])->name('email');
            Route::get('/localization', [SettingsController::class, 'academyLocalization'])->name('localization');
        });
    });

    // Global Reports Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/sales', [ReportController::class, 'globalSales'])->name('sales');
        Route::get('/inventory', [ReportController::class, 'globalInventory'])->name('inventory');
        Route::get('/financial', [ReportController::class, 'globalFinancial'])->name('financial');
    });

    // Global Settings Routes
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/general', [SettingsController::class, 'general'])->name('general');
        Route::get('/users', [SettingsController::class, 'users'])->name('users');
        Route::get('/roles', [SettingsController::class, 'roles'])->name('roles');
        Route::get('/backup', [SettingsController::class, 'backup'])->name('backup');
    });

    // Academy Dashboard Routes
    Route::get('/dashboard/academy', [DashboardController::class, 'academy'])->name('dashboard.academy');
    Route::get('/dashboard/academy/filter', [DashboardController::class, 'filterAcademyData'])->name('dashboard.academy.filter');
    Route::get('/dashboard/academy/refresh', [DashboardController::class, 'refreshAcademyData'])->name('dashboard.academy.refresh');
});

// Commented out duplicate routes as they are already handled above
/*
Route::get('/login', function () {
    return view('pages-login');
});
Route::get('/register', function () {
    return view('pages-register');
});
Route::get('/profile', function () {
    return view('users-profile');
});
*/

// Redirect /home to dashboard for authenticated users
Route::get('/home', function () {
    return redirect()->route('dashboard');
})->name('home');

// AJAX route for real-time uniqueness check
Route::get('/api/check-assembled-item-name-unique', [App\Http\Controllers\ManufacturingController::class, 'checkNameUnique'])->name('api.check.assembled-item-name-unique');
Route::get('/api/check-product-name-unique', [App\Http\Controllers\ProductController::class, 'checkNameUnique'])->name('api.check.product-name-unique');

// Business data API endpoint for prefetching
Route::get('/api/user/business-data', [BusinessDataController::class, 'index'])->name('api.business-data');

// Bakery Inventory/Items View
Route::get('/bakery/items', [ProductController::class, 'bakeryItems'])->name('bakery.items');
Route::post('/bakery/items', [ProductController::class, 'storeBakeryItem'])->name('bakery.items.store');
Route::put('/bakery/items/{product}', [ProductController::class, 'updateBakeryItem'])->name('bakery.items.update');
Route::delete('/bakery/items/{product}', [ProductController::class, 'destroyBakeryItem'])->name('bakery.items.destroy');

// Routes for bakery categories (for item management)
Route::post('/bakery/categories', [ProductController::class, 'storeCategory'])->name('bakery.categories.store');

// Paste Division API routes
Route::get('/manufacturing/paste/{pasteProcess}/available', [App\Http\Controllers\ManufacturingController::class, 'getPasteAvailability'])->name('manufacturing.paste.available');
Route::post('/manufacturing/paste/divide', [App\Http\Controllers\ManufacturingController::class, 'dividePaste'])->name('manufacturing.paste.divide');
