<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Sales;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            RolePermissionSeeder::class,
            UserSeeder::class,
            ProductSeeder::class,
            AreaSeeder::class,
            AMSeeder::class, 
            AMSSeeder::class, 
            MaintenanceSeeder::class,
            RegionCountrySeeder::class,
            ProspectTypeSeeder::class,
            TransactionTypeSeeder::class,
            StrategicInitiativesSeeder::class,
            ApuSeeder::class,
            ComponentSeeder::class,
            EngineSeeder::class,
            AircraftTypeSeeder::class,
            IgteSeeder::class,
            LearningSeeder::class,
            CustomerSeeder::class,
            ContactPersonSeeder::class,
            AMSCustomerSeeder::class,
            // ProspectSeeder::class,
            // PBTHSeeder::class,
            // TMBSeeder::class,
            HangarSeeder::class,
            LineSeeder::class,
            // SalesSeeder::class,
            // SalesHistorySeeder::class,
            CancelCategorySeeder::class,
            // SalesRejectSeeder::class,
            // SalesRescheduleSeeder::class,
            // SalesUpdateSeeder::class,
            LevelSeeder::class,
            // SalesLevelSeeder::class,
            RequirementSeeder::class,
            // SalesRequirementSeeder::class,
            ApprovalSeeder::class,
            FileSeeder::class,
            AMSTargetSeeder::class,
            // ExcelSeeder::class,
        ]);
    }
}
