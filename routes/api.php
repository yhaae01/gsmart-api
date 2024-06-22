<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AMController;
use App\Http\Controllers\AMSController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CountriesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\ProspectController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProspectTypeController;
use App\Http\Controllers\TransactionTypeController;
use App\Http\Controllers\StrategicInitiativeController;
use App\Http\Controllers\AircraftTypeController;
use App\Http\Controllers\EngineController;
use App\Http\Controllers\ComponentController;
use App\Http\Controllers\ApuController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ModulePermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\RequirementController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SalesHistoryController;
use App\Http\Controllers\SalesLevelController;
use App\Http\Controllers\SalesRejectController;
use App\Http\Controllers\SalesRequirementController;
use App\Http\Controllers\SalesRescheduleController;
use App\Http\Controllers\SalesUpdateController;
use App\Http\Controllers\ContactPersonController;
use App\Http\Controllers\LineController;
use App\Http\Controllers\HangarController;
use App\Http\Controllers\AMSCustomerController;
use App\Http\Controllers\CancelCategoryController;
use App\Http\Controllers\CustomerSwiftController;
use App\Http\Controllers\IGTEController;
use App\Http\Controllers\LearningController;
use App\Http\Controllers\DashboardController;

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
});

Route::middleware('auth:sanctum')->group(function () {

    // Dashboard's resource
    Route::controller(DashboardController::class)
        ->middleware(['permission:read_dashboard'])
        ->group(function () {
            Route::get('dashboard-cancel', 'cancel');
            Route::get('dashboard-total', 'total');
            Route::get('dashboard-area', 'area');
            Route::get('dashboard-group', 'group');
            Route::get('dashboard-product', 'product');
            Route::get('dashboard-ams-area-1', 'amsArea1');
            Route::get('dashboard-ams-area-2', 'amsArea2');
            Route::get('dashboard-ams-area-3', 'amsArea3');
            Route::get('dashboard-ams-area-kam', 'amsAreaKAM');
            Route::get('dashboard-rofo-total-month', 'rofoTotalMonth');
            Route::get('dashboard-rofo-total-year', 'rofoTotalYear');
            Route::get('dashboard-rofo-garuda-month', 'rofoGarudaMonth');
            Route::get('dashboard-rofo-garuda-year', 'rofoGarudaYear');
            Route::get('dashboard-rofo-citilink-month', 'rofoCitilinkMonth');
            Route::get('dashboard-rofo-citilink-year', 'rofoCitilinkYear');
            Route::get('dashboard-export', 'export');
        });

    // Module's resource
    Route::get('module-permission', [ModulePermissionController::class, 'index']);

    // Auth's resource
    Route::controller(AuthController::class)->group(function () {
        Route::post('logout', 'logout');
        Route::post('me', 'me');
    });

    // User's resource
    Route::controller(UserController::class)->group(function () {
        Route::get('users', 'index')->middleware(['permission:read_users']);
        Route::post('users-create', 'create')->middleware(['permission:create_users']);
        Route::get('users-show/{id}', 'show')->middleware(['permission:show_users']);
        Route::put('users-update/{id}', 'update')->middleware(['permission:update_users']);
        Route::delete('users-delete/{id}', 'destroy')->middleware(['permission:delete_users']);
    });

    // Role's resource
    Route::controller(RoleController::class)->group(function () {
        Route::get('role', 'index')->middleware(['permission:read_role']);
        Route::post('role-create', 'create')->middleware(['permission:create_role']);
        Route::get('role-show/{id}', 'show')->middleware(['permission:show_role']);
        Route::put('role-update/{id}', 'update')->middleware(['permission:update_role']);
        Route::delete('role-delete/{id}', 'destroy')->middleware(['permission:delete_role']);
    });

    // Permission's resource
    Route::controller(PermissionController::class)->group(function () {
        Route::get('permission', 'index')->middleware(['permission:read_permission']);
        Route::get('permission-show/{id}', 'show')->middleware(['permission:show_permission']);
        Route::put('permission-update/{id}', 'update')->middleware(['permission:update_permission']);
    });

    // Prospect's resource
    Route::controller(ProspectController::class)->group(function () {
        Route::get('prospect', 'index')->middleware(['permission:read_prospects']);
        Route::post('prospect-create', 'create')->middleware(['permission:create_prospects']);
        Route::get('prospect-show/{id}', 'show')->middleware(['permission:show_prospects']);
        Route::get('prospect-pbth/{id}', 'pbth')->middleware(['permission:pickup_prospects']);
        Route::get('prospect-tmb/{id}', 'tmb')->middleware(['permission:pickup_prospects']);
        Route::get('prospect-get-tmb', 'tmbOnly')->middleware(['permission:read_prospects']);
    });

    // Customer Group's resource
    Route::controller(CustomerController::class)->group(function () {
        Route::get('customer-group', 'index')->middleware(['permission:read_customer']);
        Route::get('customer-group/{id}', 'show')->middleware(['permission:show_customer']);
        Route::post('customer-group-create', 'create')->middleware(['permission:create_customer']);
        Route::put('customer-group-update/{id}', 'update')->middleware(['permission:update_customer']);
        Route::delete('customer-group-delete/{id}', 'destroy')->middleware(['permission:delete_customer']);
    });

    // Customer Swift's resource
    Route::controller(CustomerSwiftController::class)->group(function () {
        Route::get('customer', 'index')->middleware(['permission:read_customer']);
        Route::get('customer-show/{id}', 'show')->middleware(['permission:show_customer']);
    });

    // Strategic Initiative's resource
    Route::controller(StrategicInitiativeController::class)->group(function () {
        Route::get('strategic-initiative', 'index')->middleware(['permission:read_strategic_initiative']);
        Route::post('strategic-initiative-create', 'create')->middleware(['permission:create_strategic_initiative']);
        Route::get('strategic-initiative-show/{id}', 'shodw')->middleware(['permission:show_strategic_initiative']);
        Route::put('strategic-initiative-update/{id}', 'update')->middleware(['permission:update_strategic_initiative']);
        Route::delete('strategic-initiative-delete/{id}', 'destroy')->middleware(['permission:delete_strategic_initiative']);
    });

    // Region's resource
    Route::controller(RegionController::class)->group(function () {
        Route::get('region', 'index')->middleware(['permission:read_region']);
        Route::post('region-create', 'create')->middleware(['permission:create_region']);
        Route::get('region-show/{id}', 'show')->middleware(['permission:show_region']);
        Route::put('region-update/{id}', 'update')->middleware(['permission:update_region']);
        Route::delete('region-delete/{id}', 'destroy')->middleware(['permission:delete_region']);
    });

    // Country's resource
    Route::controller(CountriesController::class)->group(function () {
        Route::get('countries', 'index')->middleware(['permission:read_countries']);
        Route::post('countries-create', 'create')->middleware(['permission:create_countries']);
        Route::get('countries-show/{id}', 'show')->middleware(['permission:show_countries']);
        Route::put('countries-update/{id}', 'update')->middleware(['permission:update_countries']);
        Route::delete('countries-delete/{id}', 'destroy')->middleware(['permission:delete_countries']);
    });

    // Area's resource
    Route::controller(AreaController::class)->group(function () {
        Route::get('area', 'index')->middleware(['permission:read_area']);
        Route::post('area-create', 'create')->middleware(['permission:create_area']);
        Route::get('area-show/{id}', 'show')->middleware(['permission:show_area']);
        Route::put('area-update/{id}', 'update')->middleware(['permission:update_area']);
        Route::delete('area-delete/{id}', 'destroy')->middleware(['permission:delete_area']);;
    });

    // Maintenance's resource
    Route::controller(MaintenanceController::class)->group(function () {
        Route::get('maintenance', 'index')->middleware(['permission:read_maintenance']);
        Route::post('maintenance-create', 'create')->middleware(['permission:create_maintenance']);
        Route::get('maintenance-show/{id}', 'show')->middleware(['permission:show_maintenance']);
        Route::put('maintenance-update/{id}', 'update')->middleware(['permission:update_maintenance']);
        Route::delete('maintenance-delete/{id}', 'destroy')->middleware(['permission:delete_maintenance']);
    });

    // Transaction Type's resource
    Route::controller(TransactionTypeController::class)->group(function () {
        Route::get('transaction-type', 'index')->middleware(['permission:read_transaction_type']);
        Route::post('transaction-type-create', 'create')->middleware(['permission:create_transaction_type']);
        Route::get('transaction-type-show/{id}', 'show')->middleware(['permission:show_transaction_type']);
        Route::put('transaction-type-update/{id}', 'update')->middleware(['permission:update_transaction_type']);
        Route::delete('transaction-type-delete/{id}', 'destroy')->middleware(['permission:delete_transaction_type']);
    });

    // AM's resource
    Route::controller(AMController::class)->group(function () {
        Route::get('am', 'index')->middleware(['permission:read_am']);
        Route::post('am-create', 'create')->middleware(['permission:create_am']);
        Route::get('am-show/{id}', 'show')->middleware(['permission:show_am']);
        Route::put('am-update/{id}', 'update')->middleware(['permission:update_am']);
        Route::delete('am-delete/{id}', 'destroy')->middleware(['permission:delete_am']);
    });

    // AMS's resource
    Route::controller(AMSController::class)->group(function () {
        Route::get('ams', 'index')->middleware(['permission:read_ams']);
        Route::post('ams-create', 'create')->middleware(['permission:create_ams']);
        Route::get('ams-show/{id}', 'show')->middleware(['permission:show_ams']);
        Route::put('ams-update/{id}', 'update')->middleware(['permission:update_ams']);
        Route::delete('ams-delete/{id}', 'destroy')->middleware(['permission:delete_ams']);
    });

    // Prospect Type's resource
    Route::controller(ProspectTypeController::class)->group(function () {
        Route::get('prospect-type', 'index')->middleware(['permission:read_prospect_type']);
        Route::post('prospect-type-create', 'create')->middleware(['permission:create_prospect_type']);
        Route::get('prospect-type-show/{id}', 'show')->middleware(['permission:show_prospect_type']);
        Route::put('prospect-type-update/{id}', 'update')->middleware(['permission:update_prospect_type']);
        Route::delete('prospect-type-delete/{id}', 'destroy')->middleware(['permission:delete_prospect_type']);
    });

    // Aircraft Type's resource
    Route::controller(AircraftTypeController::class)->group(function () {
        Route::get('aircraft-type', 'index')->middleware(['permission:read_aircraft_type']);
        Route::post('aircraft-type-create', 'create')->middleware(['permission:create_aircraft_type']);
        Route::get('aircraft-type-show/{id}', 'show')->middleware(['permission:show_aircraft_type']);
        Route::put('aircraft-type-update/{id}', 'update')->middleware(['permission:update_aircraft_type']);
        Route::delete('aircraft-type-delete/{id}', 'destroy')->middleware(['permission:delete_aircraft_type']);
    });

    // Engine's resource
    Route::controller(EngineController::class)->group(function () {
        Route::get('engine', 'index')->middleware(['permission:read_engine']);
        Route::post('engine-create', 'create')->middleware(['permission:create_engine']);
        Route::get('engine-show/{id}', 'show')->middleware(['permission:show_engine']);
        Route::put('engine-update/{id}', 'update')->middleware(['permission:update_engine']);
        Route::delete('engine-delete/{id}', 'destroy')->middleware(['permission:delete_engine']);
    });

    // Component's resource
    Route::controller(ComponentController::class)->group(function () {
        Route::get('component', 'index')->middleware(['permission:read_component']);
        Route::post('component-create', 'create')->middleware(['permission:create_component']);
        Route::get('component-show/{id}', 'show')->middleware(['permission:show_component']);
        Route::put('component-update/{id}', 'update')->middleware(['permission:update_component']);
        Route::delete('component-delete/{id}', 'destroy')->middleware(['permission:delete_component']);
    });

    // APU's resource
    Route::controller(ApuController::class)->group(function () {
        Route::get('apu', 'index')->middleware(['permission:read_apu']);
        Route::post('apu-create', 'create')->middleware(['permission:create_apu']);
        Route::get('apu-show/{id}', 'show')->middleware(['permission:show_apu']);
        Route::put('apu-update/{id}', 'update')->middleware(['permission:update_apu']);
        Route::delete('apu-delete/{id}', 'destroy')->middleware(['permission:delete_apu']);
    });

    // Product's resource
    Route::controller(ProductController::class)->group(function () {
        Route::get('product', 'index')->middleware(['permission:read_product']);
        Route::post('product-create', 'create')->middleware(['permission:create_product']);
        Route::get('product-show/{id}', 'show')->middleware(['permission:show_product']);
        Route::put('product-update/{id}', 'update')->middleware(['permission:update_product']);
        Route::delete('product-delete/{id}', 'destroy')->middleware(['permission:delete_product']);
    });

    // Sales's resource
    Route::controller(SalesController::class)->group(function () {
        Route::get('sales-dashboard', 'index')->middleware(['permission:read_sales']);
        Route::get('sales-table', 'table')->middleware(['permission:read_sales']);
        Route::get('sales-show/{id}', 'show')->middleware(['permission:show_sales']);
        Route::post('sales-create-tmb', 'createTmb')->middleware(['permission:create_sales']);
        Route::post('sales-create-pbth', 'createPbth')->middleware(['permission:create_sales']);
        Route::put('sales-so-number/{id}', 'inputSONumber')->middleware(['permission:input_so_number']);
        Route::put('sales-switch-ams/{id}', 'switchAMS')->middleware(['permission:switch_ams']);
        Route::get('sales-show-tmb/{id}', 'showTmbSales')->middleware(['permission:pickup_prospects']);
        Route::delete('sales-delete-tmb/{id}', 'deleteTmbSales')->middleware(['permission:delete_sales']);
        Route::get('sales-acreg', 'acReg');

        // Sales - Closed Sales request
        Route::post('sales-request-closed', 'requestClosedSales');
        Route::put('sales-approve-closed/{id}', 'approveClosedSales');
        Route::put('sales-response-closed/{id}', 'responseClosedSales');

        // Sales - Upgrade Level request
        Route::post('sales-request-upgrade', 'requestUpgrade')->middleware(['permission:sales_request_upgrade']);
        Route::put('sales-upgrade-level/{id}', 'approveUpgrade')->middleware(['permission:sales_confirm_upgrade']);
        Route::put('sales-response-upgrade/{id}', 'responseUpgrade')->middleware(['permission:sales_request_upgrade']);

        // Sales - Hangar Slot request
        Route::post('sales-request-hangar', 'requestHangar')->middleware(['permission:request_hangar']);
        Route::put('sales-approve-hangar/{id}', 'approveHangar')->middleware(['permission:approve_hangar']);
        Route::put('sales-response-hangar/{id}', 'responseHangar')->middleware(['permission:request_hangar']);

        // Sales - Reschedule request
        Route::post('sales-request-reschedule', 'requestReschedule')->middleware(['permission:request_reschedule']);
        Route::put('sales-approve-reschedule/{id}', 'approveReschedule')->middleware(['permission:approve_reschedule']);
        Route::put('sales-response-reschedule/{id}', 'responseReschedule')->middleware(['permission:request_reschedule']);

        // Sales - Cancel request
        Route::post('sales-request-cancel', 'requestCancel')->middleware(['permission:request_cancel']);
        Route::put('sales-approve-cancel/{id}', 'approveCancel')->middleware(['permission:approve_cancel']);
        Route::put('sales-response-cancel/{id}', 'responseCancel')->middleware(['permission:request_cancel']);

        // Sales - Update request
        Route::put('sales-update-tmb/{id}', 'updateTmb')->middleware(['permission:read_sales_update']);
        Route::get('sales-detail-history/{id}', 'SalesDetailUpdateLog');
        Route::put('sales-update-pbth/{id}', 'updatePbth')->middleware(['permission:read_sales_update']);

        Route::post('sales-upload', 'uploadSales')->middleware(['permission:upload_sales_data']);
        Route::get('template-excel', 'templateExcel')->middleware(['permission:upload_sales_data']);
    });

    // Line
    Route::get('line', LineController::class)->middleware('permission:read_lines');

    // Hangar
    Route::get('hangar', HangarController::class)->middleware(['permission:read_hangars']);

    // AMS Customer (pivot)
    Route::get('ams-customer/{id}', AMSCustomerController::class)->middleware(['permission:read_ams']);

    // Cancel Category - Sales Reject/Cancel Reason
    Route::get('cancel-category', CancelCategoryController::class)->middleware(['permission:request_cancel']);

    // File's resource
    Route::controller(FileController::class)->group(function () {
        Route::get('file', 'index')->middleware(['permission:read_files']);
        Route::post('file-create', 'store')->middleware(['permission:upload_files']);
        Route::get('file-show/{id}', 'show')->middleware(['permission:show_files']);
        Route::get('file-history/{sales_id}', 'history')->middleware(['permission:file_histories']);
        Route::delete('file-delete/{id}', 'destroy')->middleware(['permission:delete_files']);
    });

    // Contact Person's resource
    Route::controller(ContactPersonController::class)->group(function () {
        Route::get('contact-person', 'index')->middleware(['permission:read_contacts']);
        Route::post('contact-person-create', 'store')->middleware(['permission:create_contacts']);
        Route::delete('contact-person-delete/{id}', 'destroy')->middleware(['permission:delete_contacts']);
    });

    // IGTE
    Route::get('igte', [IGTEController::class, 'index']);

    // Learning
    Route::get('learning', [LearningController::class, 'index']);
});
