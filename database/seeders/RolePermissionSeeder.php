<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\ModulePermissions;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        //User Permission
        $read_users = Permission::create(['name' => 'read_users']);
        $create_users = Permission::create(['name' => 'create_users']);
        $show_users = Permission::create(['name' => 'show_users']);
        $update_users = Permission::create(['name' => 'update_users']);
        $delete_users = Permission::create(['name' => 'delete_users']);

        $manage_users = Module::create([
            'module_name' => 'Manage User'
        ]);

        ModulePermissions::create(['module_id' => $manage_users->id, 'permission_id' => $read_users->id],);
        ModulePermissions::create(['module_id' => $manage_users->id, 'permission_id' => $create_users->id],);
        ModulePermissions::create(['module_id' => $manage_users->id, 'permission_id' => $show_users->id],);
        ModulePermissions::create(['module_id' => $manage_users->id, 'permission_id' => $update_users->id],);
        ModulePermissions::create(['module_id' => $manage_users->id, 'permission_id' => $delete_users->id],);


        //Role Permission
        $manage_role = Module::create([
            'module_name' => 'Manage Role'
        ]);

        $read_role = Permission::create(['name' => 'read_role']);
        $create_role = Permission::create(['name' => 'create_role']);
        $show_role = Permission::create(['name' => 'show_role']);
        $update_role = Permission::create(['name' => 'update_role']);
        $delete_role = Permission::create(['name' => 'delete_role']);

        ModulePermissions::create(['module_id' => $manage_role->id, 'permission_id' => $read_role->id],);
        ModulePermissions::create(['module_id' => $manage_role->id, 'permission_id' => $create_role->id],);
        ModulePermissions::create(['module_id' => $manage_role->id, 'permission_id' => $show_role->id],);
        ModulePermissions::create(['module_id' => $manage_role->id, 'permission_id' => $update_role->id],);
        ModulePermissions::create(['module_id' => $manage_role->id, 'permission_id' => $delete_role->id],);

        //Permission Permission
        $manage_permission = Module::create([
            'module_name' => 'Manage Permission'
        ]);
        $read_permission = Permission::create(['name' => 'read_permission']);
        $create_permission = Permission::create(['name' => 'create_permission']);
        $show_permission = Permission::create(['name' => 'show_permission']);
        $update_permission = Permission::create(['name' => 'update_permission']);
        $delete_permission = Permission::create(['name' => 'delete_permission']);

        ModulePermissions::create(['module_id' => $manage_permission->id, 'permission_id' => $read_permission->id],);
        ModulePermissions::create(['module_id' => $manage_permission->id, 'permission_id' => $create_permission->id],);
        ModulePermissions::create(['module_id' => $manage_permission->id, 'permission_id' => $show_permission->id],);
        ModulePermissions::create(['module_id' => $manage_permission->id, 'permission_id' => $update_permission->id],);
        ModulePermissions::create(['module_id' => $manage_permission->id, 'permission_id' => $delete_permission->id],);

        //Strategic Initiative Permission
        $manage_strategic_initiative = Module::create([
            'module_name' => 'Manage Strategic Initiative'
        ]);

        $read_strategic_initiative = Permission::create(['name' => 'read_strategic_initiative']);
        $create_strategic_initiative = Permission::create(['name' => 'create_strategic_initiative']);
        $show_strategic_initiative = Permission::create(['name' => 'show_strategic_initiative']);
        $update_strategic_initiative = Permission::create(['name' => 'update_strategic_initiative']);
        $delete_strategic_initiative = Permission::create(['name' => 'delete_strategic_initiative']);

        ModulePermissions::create(['module_id' => $manage_strategic_initiative->id, 'permission_id' => $read_strategic_initiative->id],);
        ModulePermissions::create(['module_id' => $manage_strategic_initiative->id, 'permission_id' => $create_strategic_initiative->id],);
        ModulePermissions::create(['module_id' => $manage_strategic_initiative->id, 'permission_id' => $show_strategic_initiative->id],);
        ModulePermissions::create(['module_id' => $manage_strategic_initiative->id, 'permission_id' => $update_strategic_initiative->id],);
        ModulePermissions::create(['module_id' => $manage_strategic_initiative->id, 'permission_id' => $delete_strategic_initiative->id],);

        //Region Permission
        $manage_region = Module::create([
            'module_name' => 'Manage Region'
        ]);

        $read_region = Permission::create(['name' => 'read_region']);
        $create_region = Permission::create(['name' => 'create_region']);
        $show_region = Permission::create(['name' => 'show_region']);
        $update_region = Permission::create(['name' => 'update_region']);
        $delete_region = Permission::create(['name' => 'delete_region']);

        ModulePermissions::create(['module_id' => $manage_region->id, 'permission_id' => $read_region->id],);
        ModulePermissions::create(['module_id' => $manage_region->id, 'permission_id' => $create_region->id],);
        ModulePermissions::create(['module_id' => $manage_region->id, 'permission_id' => $show_region->id],);
        ModulePermissions::create(['module_id' => $manage_region->id, 'permission_id' => $update_region->id],);
        ModulePermissions::create(['module_id' => $manage_region->id, 'permission_id' => $delete_region->id],);

        //Countries Permission
        $manage_countries = Module::create([
            'module_name' => 'Manage Countries'
        ]);

        $read_countries = Permission::create(['name' => 'read_countries']);
        $create_countries = Permission::create(['name' => 'create_countries']);
        $show_countries = Permission::create(['name' => 'show_countries']);
        $update_countries = Permission::create(['name' => 'update_countries']);
        $delete_countries = Permission::create(['name' => 'delete_countries']);

        ModulePermissions::create(['module_id' => $manage_countries->id, 'permission_id' => $read_countries->id],);
        ModulePermissions::create(['module_id' => $manage_countries->id, 'permission_id' => $create_countries->id],);
        ModulePermissions::create(['module_id' => $manage_countries->id, 'permission_id' => $show_countries->id],);
        ModulePermissions::create(['module_id' => $manage_countries->id, 'permission_id' => $update_countries->id],);
        ModulePermissions::create(['module_id' => $manage_countries->id, 'permission_id' => $delete_countries->id],);

        //Area Permission
        $manage_area = Module::create([
            'module_name' => 'Manage Area'
        ]);

        $read_area = Permission::create(['name' => 'read_area']);
        $create_area = Permission::create(['name' => 'create_area']);
        $show_area = Permission::create(['name' => 'show_area']);
        $update_area = Permission::create(['name' => 'update_area']);
        $delete_area = Permission::create(['name' => 'delete_area']);

        ModulePermissions::create(['module_id' => $manage_area->id, 'permission_id' => $read_area->id],);
        ModulePermissions::create(['module_id' => $manage_area->id, 'permission_id' => $create_area->id],);
        ModulePermissions::create(['module_id' => $manage_area->id, 'permission_id' => $show_area->id],);
        ModulePermissions::create(['module_id' => $manage_area->id, 'permission_id' => $update_area->id],);
        ModulePermissions::create(['module_id' => $manage_area->id, 'permission_id' => $delete_area->id],);

        //Maintenance Permission
        $manage_maintenance = Module::create([
            'module_name' => 'Manage Maintenance'
        ]);

        $read_maintenance = Permission::create(['name' => 'read_maintenance']);
        $create_maintenance = Permission::create(['name' => 'create_maintenance']);
        $show_maintenance = Permission::create(['name' => 'show_maintenance']);
        $update_maintenance = Permission::create(['name' => 'update_maintenance']);
        $delete_maintenance = Permission::create(['name' => 'delete_maintenance']);

        ModulePermissions::create(['module_id' => $manage_maintenance->id, 'permission_id' => $read_maintenance->id],);
        ModulePermissions::create(['module_id' => $manage_maintenance->id, 'permission_id' => $create_maintenance->id],);
        ModulePermissions::create(['module_id' => $manage_maintenance->id, 'permission_id' => $show_maintenance->id],);
        ModulePermissions::create(['module_id' => $manage_maintenance->id, 'permission_id' => $update_maintenance->id],);
        ModulePermissions::create(['module_id' => $manage_maintenance->id, 'permission_id' => $delete_maintenance->id],);

        //Transaction Type Permission
        $manage_transaction_type = Module::create([
            'module_name' => 'Manage Transaction Type'
        ]);

        $read_transaction_type = Permission::create(['name' => 'read_transaction_type']);
        $create_transaction_type = Permission::create(['name' => 'create_transaction_type']);
        $show_transaction_type = Permission::create(['name' => 'show_transaction_type']);
        $update_transaction_type = Permission::create(['name' => 'update_transaction_type']);
        $delete_transaction_type = Permission::create(['name' => 'delete_transaction_type']);

        ModulePermissions::create(['module_id' => $manage_transaction_type->id, 'permission_id' => $read_transaction_type->id],);
        ModulePermissions::create(['module_id' => $manage_transaction_type->id, 'permission_id' => $create_transaction_type->id],);
        ModulePermissions::create(['module_id' => $manage_transaction_type->id, 'permission_id' => $show_transaction_type->id],);
        ModulePermissions::create(['module_id' => $manage_transaction_type->id, 'permission_id' => $update_transaction_type->id],);
        ModulePermissions::create(['module_id' => $manage_transaction_type->id, 'permission_id' => $delete_transaction_type->id],);

        //AM Permission
        $manage_am = Module::create([
            'module_name' => 'Manage AM'
        ]);

        $read_am = Permission::create(['name' => 'read_am']);
        $create_am = Permission::create(['name' => 'create_am']);
        $show_am = Permission::create(['name' => 'show_am']);
        $update_am = Permission::create(['name' => 'update_am']);
        $delete_am = Permission::create(['name' => 'delete_am']);

        ModulePermissions::create(['module_id' => $manage_am->id, 'permission_id' => $read_am->id],);
        ModulePermissions::create(['module_id' => $manage_am->id, 'permission_id' => $create_am->id],);
        ModulePermissions::create(['module_id' => $manage_am->id, 'permission_id' => $show_am->id],);
        ModulePermissions::create(['module_id' => $manage_am->id, 'permission_id' => $update_am->id],);
        ModulePermissions::create(['module_id' => $manage_am->id, 'permission_id' => $delete_am->id],);

        //AMS Permission
        $manage_ams = Module::create([
            'module_name' => 'Manage AMS'
        ]);

        $read_ams = Permission::create(['name' => 'read_ams']);
        $create_ams = Permission::create(['name' => 'create_ams']);
        $show_ams = Permission::create(['name' => 'show_ams']);
        $update_ams = Permission::create(['name' => 'update_ams']);
        $delete_ams = Permission::create(['name' => 'delete_ams']);

        ModulePermissions::create(['module_id' => $manage_ams->id, 'permission_id' => $read_ams->id],);
        ModulePermissions::create(['module_id' => $manage_ams->id, 'permission_id' => $create_ams->id],);
        ModulePermissions::create(['module_id' => $manage_ams->id, 'permission_id' => $show_ams->id],);
        ModulePermissions::create(['module_id' => $manage_ams->id, 'permission_id' => $update_ams->id],);
        ModulePermissions::create(['module_id' => $manage_ams->id, 'permission_id' => $delete_ams->id],);

        //Prospect Type Permission
        $manage_prospect_type = Module::create([
            'module_name' => 'Manage Prospect Type'
        ]);

        $read_prospect_type = Permission::create(['name' => 'read_prospect_type']);
        $create_prospect_type = Permission::create(['name' => 'create_prospect_type']);
        $show_prospect_type = Permission::create(['name' => 'show_prospect_type']);
        $update_prospect_type = Permission::create(['name' => 'update_prospect_type']);
        $delete_prospect_type = Permission::create(['name' => 'delete_prospect_type']);

        ModulePermissions::create(['module_id' => $manage_prospect_type->id, 'permission_id' => $read_prospect_type->id],);
        ModulePermissions::create(['module_id' => $manage_prospect_type->id, 'permission_id' => $create_prospect_type->id],);
        ModulePermissions::create(['module_id' => $manage_prospect_type->id, 'permission_id' => $show_prospect_type->id],);
        ModulePermissions::create(['module_id' => $manage_prospect_type->id, 'permission_id' => $update_prospect_type->id],);
        ModulePermissions::create(['module_id' => $manage_prospect_type->id, 'permission_id' => $delete_prospect_type->id],);

        //Aircraft Type Permission
        $manage_aircraft_type = Module::create([
            'module_name' => 'Manage Aircraft Type'
        ]);

        $read_aircraft_type = Permission::create(['name' => 'read_aircraft_type']);
        $create_aircraft_type = Permission::create(['name' => 'create_aircraft_type']);
        $show_aircraft_type = Permission::create(['name' => 'show_aircraft_type']);
        $update_aircraft_type = Permission::create(['name' => 'update_aircraft_type']);
        $delete_aircraft_type = Permission::create(['name' => 'delete_aircraft_type']);

        ModulePermissions::create(['module_id' => $manage_aircraft_type->id, 'permission_id' => $read_aircraft_type->id],);
        ModulePermissions::create(['module_id' => $manage_aircraft_type->id, 'permission_id' => $create_aircraft_type->id],);
        ModulePermissions::create(['module_id' => $manage_aircraft_type->id, 'permission_id' => $show_aircraft_type->id],);
        ModulePermissions::create(['module_id' => $manage_aircraft_type->id, 'permission_id' => $update_aircraft_type->id],);
        ModulePermissions::create(['module_id' => $manage_aircraft_type->id, 'permission_id' => $delete_aircraft_type->id],);

        //Engine Permission
        $manage_engine = Module::create([
            'module_name' => 'Manage Engine'
        ]);

        $read_engine = Permission::create(['name' => 'read_engine']);
        $create_engine = Permission::create(['name' => 'create_engine']);
        $show_engine = Permission::create(['name' => 'show_engine']);
        $update_engine = Permission::create(['name' => 'update_engine']);
        $delete_engine = Permission::create(['name' => 'delete_engine']);

        ModulePermissions::create(['module_id' => $manage_engine->id, 'permission_id' => $read_engine->id],);
        ModulePermissions::create(['module_id' => $manage_engine->id, 'permission_id' => $create_engine->id],);
        ModulePermissions::create(['module_id' => $manage_engine->id, 'permission_id' => $show_engine->id],);
        ModulePermissions::create(['module_id' => $manage_engine->id, 'permission_id' => $update_engine->id],);
        ModulePermissions::create(['module_id' => $manage_engine->id, 'permission_id' => $delete_engine->id],);

        //Component Permission
        $manage_component = Module::create([
            'module_name' => 'Manage Component'
        ]);

        $read_component = Permission::create(['name' => 'read_component']);
        $create_component = Permission::create(['name' => 'create_component']);
        $show_component = Permission::create(['name' => 'show_component']);
        $update_component = Permission::create(['name' => 'update_component']);
        $delete_component = Permission::create(['name' => 'delete_component']);

        ModulePermissions::create(['module_id' => $manage_component->id, 'permission_id' => $read_component->id],);
        ModulePermissions::create(['module_id' => $manage_component->id, 'permission_id' => $create_component->id],);
        ModulePermissions::create(['module_id' => $manage_component->id, 'permission_id' => $show_component->id],);
        ModulePermissions::create(['module_id' => $manage_component->id, 'permission_id' => $update_component->id],);
        ModulePermissions::create(['module_id' => $manage_component->id, 'permission_id' => $delete_component->id],);

        //APU Permission
        $manage_apu = Module::create([
            'module_name' => 'Manage APU'
        ]);

        $read_apu = Permission::create(['name' => 'read_apu']);
        $create_apu = Permission::create(['name' => 'create_apu']);
        $show_apu = Permission::create(['name' => 'show_apu']);
        $update_apu = Permission::create(['name' => 'update_apu']);
        $delete_apu = Permission::create(['name' => 'delete_apu']);

        ModulePermissions::create(['module_id' => $manage_apu->id, 'permission_id' => $read_apu->id],);
        ModulePermissions::create(['module_id' => $manage_apu->id, 'permission_id' => $create_apu->id],);
        ModulePermissions::create(['module_id' => $manage_apu->id, 'permission_id' => $show_apu->id],);
        ModulePermissions::create(['module_id' => $manage_apu->id, 'permission_id' => $update_apu->id],);
        ModulePermissions::create(['module_id' => $manage_apu->id, 'permission_id' => $delete_apu->id],);

        //Product Permission
        $manage_product = Module::create([
            'module_name' => 'Manage Product'
        ]);

        $read_product = Permission::create(['name' => 'read_product']);
        $create_product = Permission::create(['name' => 'create_product']);
        $show_product = Permission::create(['name' => 'show_product']);
        $update_product = Permission::create(['name' => 'update_product']);
        $delete_product = Permission::create(['name' => 'delete_product']);

        ModulePermissions::create(['module_id' => $manage_product->id, 'permission_id' => $read_product->id],);
        ModulePermissions::create(['module_id' => $manage_product->id, 'permission_id' => $create_product->id],);
        ModulePermissions::create(['module_id' => $manage_product->id, 'permission_id' => $show_product->id],);
        ModulePermissions::create(['module_id' => $manage_product->id, 'permission_id' => $update_product->id],);
        ModulePermissions::create(['module_id' => $manage_product->id, 'permission_id' => $delete_product->id],);

        //Approval Permission
        $manage_approval = Module::create([
            'module_name' => 'Manage Approval'
        ]);

        $read_approval = Permission::create(['name' => 'read_approval']);
        $create_approval = Permission::create(['name' => 'create_approval']);
        $show_approval = Permission::create(['name' => 'show_approval']);
        $update_approval = Permission::create(['name' => 'update_approval']);
        $delete_approval = Permission::create(['name' => 'delete_approval']);

        ModulePermissions::create(['module_id' => $manage_approval->id, 'permission_id' => $read_approval->id],);
        ModulePermissions::create(['module_id' => $manage_approval->id, 'permission_id' => $create_approval->id],);
        ModulePermissions::create(['module_id' => $manage_approval->id, 'permission_id' => $show_approval->id],);
        ModulePermissions::create(['module_id' => $manage_approval->id, 'permission_id' => $update_approval->id],);
        ModulePermissions::create(['module_id' => $manage_approval->id, 'permission_id' => $delete_approval->id],);

        //Customer Permission
        $manage_customer = Module::create([
            'module_name' => 'Manage Customer'
        ]);

        $read_customer = Permission::create(['name' => 'read_customer']);
        $create_customer = Permission::create(['name' => 'create_customer']);
        $show_customer = Permission::create(['name' => 'show_customer']);
        $update_customer = Permission::create(['name' => 'update_customer']);
        $delete_customer = Permission::create(['name' => 'delete_customer']);

        ModulePermissions::create(['module_id' => $manage_customer->id, 'permission_id' => $read_customer->id],);
        ModulePermissions::create(['module_id' => $manage_customer->id, 'permission_id' => $create_customer->id],);
        ModulePermissions::create(['module_id' => $manage_customer->id, 'permission_id' => $show_customer->id],);
        ModulePermissions::create(['module_id' => $manage_customer->id, 'permission_id' => $update_customer->id],);
        ModulePermissions::create(['module_id' => $manage_customer->id, 'permission_id' => $delete_customer->id],);

        //Level Permission
        $manage_level = Module::create([
            'module_name' => 'Manage Level'
        ]);

        $read_level = Permission::create(['name' => 'read_level']);
        $create_level = Permission::create(['name' => 'create_level']);
        $show_level = Permission::create(['name' => 'show_level']);
        $update_level = Permission::create(['name' => 'update_level']);
        $delete_level = Permission::create(['name' => 'delete_level']);

        ModulePermissions::create(['module_id' => $manage_level->id, 'permission_id' => $read_level->id],);
        ModulePermissions::create(['module_id' => $manage_level->id, 'permission_id' => $create_level->id],);
        ModulePermissions::create(['module_id' => $manage_level->id, 'permission_id' => $show_level->id],);
        ModulePermissions::create(['module_id' => $manage_level->id, 'permission_id' => $update_level->id],);
        ModulePermissions::create(['module_id' => $manage_level->id, 'permission_id' => $delete_level->id],);

        //Requirement Permission
        $manage_requirement = Module::create([
            'module_name' => 'Manage Requirement'
        ]);

        $read_requirement = Permission::create(['name' => 'read_requirement']);
        $create_requirement = Permission::create(['name' => 'create_requirement']);
        $show_requirement = Permission::create(['name' => 'show_requirement']);
        $update_requirement = Permission::create(['name' => 'update_requirement']);
        $delete_requirement = Permission::create(['name' => 'delete_requirement']);

        ModulePermissions::create(['module_id' => $manage_requirement->id, 'permission_id' => $read_requirement->id],);
        ModulePermissions::create(['module_id' => $manage_requirement->id, 'permission_id' => $create_requirement->id],);
        ModulePermissions::create(['module_id' => $manage_requirement->id, 'permission_id' => $show_requirement->id],);
        ModulePermissions::create(['module_id' => $manage_requirement->id, 'permission_id' => $update_requirement->id],);
        ModulePermissions::create(['module_id' => $manage_requirement->id, 'permission_id' => $delete_requirement->id],);

        //Sales History Permission
        $manage_sales_history = Module::create([
            'module_name' => 'Manage Sales History'
        ]);

        $read_sales_history = Permission::create(['name' => 'read_sales_history']);
        $create_sales_history = Permission::create(['name' => 'create_sales_history']);
        $show_sales_history = Permission::create(['name' => 'show_sales_history']);
        $update_sales_history = Permission::create(['name' => 'update_sales_history']);
        $delete_sales_history = Permission::create(['name' => 'delete_sales_history']);

        ModulePermissions::create(['module_id' => $manage_sales_history->id, 'permission_id' => $read_sales_history->id],);
        ModulePermissions::create(['module_id' => $manage_sales_history->id, 'permission_id' => $create_sales_history->id],);
        ModulePermissions::create(['module_id' => $manage_sales_history->id, 'permission_id' => $show_sales_history->id],);
        ModulePermissions::create(['module_id' => $manage_sales_history->id, 'permission_id' => $update_sales_history->id],);
        ModulePermissions::create(['module_id' => $manage_sales_history->id, 'permission_id' => $delete_sales_history->id],);

        //Sales Level Permission
        $manage_sales_level = Module::create([
            'module_name' => 'Manage Sales Level'
        ]);

        $read_sales_level = Permission::create(['name' => 'read_sales_level']);
        $create_sales_level = Permission::create(['name' => 'create_sales_level']);
        $show_sales_level = Permission::create(['name' => 'show_sales_level']);
        $update_sales_level = Permission::create(['name' => 'update_sales_level']);
        $delete_sales_level = Permission::create(['name' => 'delete_sales_level']);

        ModulePermissions::create(['module_id' => $manage_sales_level->id, 'permission_id' => $read_sales_level->id],);
        ModulePermissions::create(['module_id' => $manage_sales_level->id, 'permission_id' => $create_sales_level->id],);
        ModulePermissions::create(['module_id' => $manage_sales_level->id, 'permission_id' => $show_sales_level->id],);
        ModulePermissions::create(['module_id' => $manage_sales_level->id, 'permission_id' => $update_sales_level->id],);
        ModulePermissions::create(['module_id' => $manage_sales_level->id, 'permission_id' => $delete_sales_level->id],);

        //Sales Reject Permission
        $manage_sales_reject = Module::create([
            'module_name' => 'Manage Sales Reject'
        ]);

        $read_sales_reject = Permission::create(['name' => 'read_sales_reject']);
        $create_sales_reject = Permission::create(['name' => 'create_sales_reject']);
        $show_sales_reject = Permission::create(['name' => 'show_sales_reject']);
        $update_sales_reject = Permission::create(['name' => 'update_sales_reject']);
        $delete_sales_reject = Permission::create(['name' => 'delete_sales_reject']);

        ModulePermissions::create(['module_id' => $manage_sales_reject->id, 'permission_id' => $read_sales_reject->id],);
        ModulePermissions::create(['module_id' => $manage_sales_reject->id, 'permission_id' => $create_sales_reject->id],);
        ModulePermissions::create(['module_id' => $manage_sales_reject->id, 'permission_id' => $show_sales_reject->id],);
        ModulePermissions::create(['module_id' => $manage_sales_reject->id, 'permission_id' => $update_sales_reject->id],);
        ModulePermissions::create(['module_id' => $manage_sales_reject->id, 'permission_id' => $delete_sales_reject->id],);

        //Sales Requirement Permission
        $manage_sales_requirement = Module::create([
            'module_name' => 'Manage Sales Requirement'
        ]);

        $read_sales_requirement = Permission::create(['name' => 'read_sales_requirement']);
        $create_sales_requirement = Permission::create(['name' => 'create_sales_requirement']);
        $show_sales_requirement = Permission::create(['name' => 'show_sales_requirement']);
        $update_sales_requirement = Permission::create(['name' => 'update_sales_requirement']);
        $delete_sales_requirement = Permission::create(['name' => 'delete_sales_requirement']);

        ModulePermissions::create(['module_id' => $manage_sales_requirement->id, 'permission_id' => $read_sales_requirement->id],);
        ModulePermissions::create(['module_id' => $manage_sales_requirement->id, 'permission_id' => $create_sales_requirement->id],);
        ModulePermissions::create(['module_id' => $manage_sales_requirement->id, 'permission_id' => $show_sales_requirement->id],);
        ModulePermissions::create(['module_id' => $manage_sales_requirement->id, 'permission_id' => $update_sales_requirement->id],);
        ModulePermissions::create(['module_id' => $manage_sales_requirement->id, 'permission_id' => $delete_sales_requirement->id],);

        //Sales Reschedule Permission
        $manage_sales_reschedule = Module::create([
            'module_name' => 'Manage Sales Reschedule'
        ]);

        $read_sales_reschedule = Permission::create(['name' => 'read_sales_reschedule']);
        $create_sales_reschedule = Permission::create(['name' => 'create_sales_reschedule']);
        $show_sales_reschedule = Permission::create(['name' => 'show_sales_reschedule']);
        $update_sales_reschedule = Permission::create(['name' => 'update_sales_reschedule']);
        $delete_sales_reschedule = Permission::create(['name' => 'delete_sales_reschedule']);

        ModulePermissions::create(['module_id' => $manage_sales_reschedule->id, 'permission_id' => $read_sales_reschedule->id],);
        ModulePermissions::create(['module_id' => $manage_sales_reschedule->id, 'permission_id' => $create_sales_reschedule->id],);
        ModulePermissions::create(['module_id' => $manage_sales_reschedule->id, 'permission_id' => $show_sales_reschedule->id],);
        ModulePermissions::create(['module_id' => $manage_sales_reschedule->id, 'permission_id' => $update_sales_reschedule->id],);
        ModulePermissions::create(['module_id' => $manage_sales_reschedule->id, 'permission_id' => $delete_sales_reschedule->id],);

        //Sales Update Permission
        $manage_sales_update = Module::create([
            'module_name' => 'Manage Sales Update'
        ]);

        $read_sales_update = Permission::create(['name' => 'read_sales_update']);
        $create_sales_update = Permission::create(['name' => 'create_sales_update']);
        $show_sales_update = Permission::create(['name' => 'show_sales_update']);
        $update_sales_update = Permission::create(['name' => 'update_sales_update']);
        $delete_sales_update = Permission::create(['name' => 'delete_sales_update']);

        ModulePermissions::create(['module_id' => $manage_sales_update->id, 'permission_id' => $read_sales_update->id],);
        ModulePermissions::create(['module_id' => $manage_sales_update->id, 'permission_id' => $create_sales_update->id],);
        ModulePermissions::create(['module_id' => $manage_sales_update->id, 'permission_id' => $show_sales_update->id],);
        ModulePermissions::create(['module_id' => $manage_sales_update->id, 'permission_id' => $update_sales_update->id],);
        ModulePermissions::create(['module_id' => $manage_sales_update->id, 'permission_id' => $delete_sales_update->id],);

        // Prospect Permission
        $manage_prospect = Module::create([
            'module_name' => 'Manage Prospects'
        ]);

        $read_prospects = Permission::create(['name' => 'read_prospects']);
        $create_prospects = Permission::create(['name' => 'create_prospects']);
        $show_prospects = Permission::create(['name' => 'show_prospects']);
        $pickup_prospects = Permission::create(['name' => 'pickup_prospects']);
        
        ModulePermissions::create(['module_id' => $manage_prospect->id, 'permission_id' => $read_prospects->id],);
        ModulePermissions::create(['module_id' => $manage_prospect->id, 'permission_id' => $create_prospects->id],);
        ModulePermissions::create(['module_id' => $manage_prospect->id, 'permission_id' => $show_prospects->id],);
        ModulePermissions::create(['module_id' => $manage_prospect->id, 'permission_id' => $pickup_prospects->id],);

        // Sales Permission
        $manage_sales = Module::create([
            'module_name' => 'Manage Sales'
        ]);

        $read_sales = Permission::create(['name' => 'read_sales']);
        $show_sales = Permission::create(['name' => 'show_sales']);
        $create_sales = Permission::create(['name' => 'create_sales']);
        $request_hangar = Permission::create(['name' => 'request_hangar']);
        $input_so_number = Permission::create(['name' => 'input_so_number']);
        $switch_ams = Permission::create(['name' => 'switch_ams']);
        $sales_request_upgrade = Permission::create(['name' => 'sales_request_upgrade']);
        $sales_confirm_upgrade = Permission::create(['name' => 'sales_confirm_upgrade']);
        $update_sales = Permission::create(['name' => 'update_sales']);
        $request_reschedule = Permission::create(['name' => 'request_reschedule']);
        $request_cancel = Permission::create(['name' => 'request_cancel']);
        $close_sales = Permission::create(['name' => 'close_sales']);
        $delete_sales = Permission::create(['name' => 'delete_sales']);
        $request_cogs = Permission::create(['name' => 'request_cogs']);
        $approve_hangar = Permission::create(['name' => 'approve_hangar']);
        $approve_reschedule = Permission::create(['name' => 'approve_reschedule']);
        $approve_cancel = Permission::create(['name' => 'approve_cancel']);

        ModulePermissions::create(['module_id' => $manage_sales->id, 'permission_id' => $read_sales->id]);
        ModulePermissions::create(['module_id' => $manage_sales->id, 'permission_id' => $show_sales->id]);
        ModulePermissions::create(['module_id' => $manage_sales->id, 'permission_id' => $create_sales->id]);
        ModulePermissions::create(['module_id' => $manage_sales->id, 'permission_id' => $request_hangar->id]);
        ModulePermissions::create(['module_id' => $manage_sales->id, 'permission_id' => $input_so_number->id]);
        ModulePermissions::create(['module_id' => $manage_sales->id, 'permission_id' => $switch_ams->id]);
        ModulePermissions::create(['module_id' => $manage_sales->id, 'permission_id' => $sales_request_upgrade->id]);
        ModulePermissions::create(['module_id' => $manage_sales->id, 'permission_id' => $sales_confirm_upgrade->id]);
        ModulePermissions::create(['module_id' => $manage_sales->id, 'permission_id' => $update_sales->id]);
        ModulePermissions::create(['module_id' => $manage_sales->id, 'permission_id' => $request_reschedule->id]);
        ModulePermissions::create(['module_id' => $manage_sales->id, 'permission_id' => $request_cancel->id]);
        ModulePermissions::create(['module_id' => $manage_sales->id, 'permission_id' => $close_sales->id]);
        ModulePermissions::create(['module_id' => $manage_sales->id, 'permission_id' => $delete_sales->id]);
        ModulePermissions::create(['module_id' => $manage_sales->id, 'permission_id' => $request_cogs->id]);
        ModulePermissions::create(['module_id' => $manage_sales->id, 'permission_id' => $approve_hangar->id]);
        ModulePermissions::create(['module_id' => $manage_sales->id, 'permission_id' => $approve_reschedule->id]);
        ModulePermissions::create(['module_id' => $manage_sales->id, 'permission_id' => $approve_cancel->id]);
        
        // Line Hangar Permission
        $manage_lines = Module::create([
            'module_name' => 'Manage Lines'
        ]);
        $read_lines = Permission::create(['name' => 'read_lines']);
        ModulePermissions::create(['module_id' => $manage_lines->id, 'permission_id' => $read_lines->id]);
        
        // Hangar Permission
        $manage_hangars = Module::create([
            'module_name' => 'Manage Hangars'
        ]);
        $read_hangars = Permission::create(['name' => 'read_hangars']);
        ModulePermissions::create(['module_id' => $manage_hangars->id, 'permission_id' => $read_hangars->id]);

        // File Permission
        $manage_files = Module::create([
            'module_name' => 'Manage Files'
        ]);

        $read_files = Permission::create(['name' => 'read_files']);
        $upload_files = Permission::create(['name' => 'upload_files']);
        $show_files = Permission::create(['name' => 'show_files']);
        $file_histories = Permission::create(['name' => 'file_histories']);
        $delete_files = Permission::create(['name' => 'delete_files']);

        ModulePermissions::create(['module_id' => $manage_files->id, 'permission_id' => $read_files->id],);
        ModulePermissions::create(['module_id' => $manage_files->id, 'permission_id' => $upload_files->id],);
        ModulePermissions::create(['module_id' => $manage_files->id, 'permission_id' => $show_files->id],);
        ModulePermissions::create(['module_id' => $manage_files->id, 'permission_id' => $file_histories->id],);
        ModulePermissions::create(['module_id' => $manage_files->id, 'permission_id' => $delete_files->id],);
        
        // Contact Person Permission
        $manage_contacts = Module::create([
            'module_name' => 'Manage Contacts'
        ]);

        $read_contacts = Permission::create(['name' => 'read_contacts']);
        $create_contacts = Permission::create(['name' => 'create_contacts']);
        $delete_contacts = Permission::create(['name' => 'delete_contacts']);

        ModulePermissions::create(['module_id' => $manage_contacts->id, 'permission_id' => $read_contacts->id],);
        ModulePermissions::create(['module_id' => $manage_contacts->id, 'permission_id' => $create_contacts->id],);
        ModulePermissions::create(['module_id' => $manage_contacts->id, 'permission_id' => $delete_contacts->id],);

        // Dashboard Permission
        $manage_dashboard = Module::create([
            'module_name' => 'Manage Dashboard'
        ]);
        $read_dashboard = Permission::create(['name' => 'read_dashboard']);
        ModulePermissions::create(['module_id' => $manage_dashboard->id, 'permission_id' => $read_dashboard->id]);

        // Upload Sales Data (Excel)
        $manage_sales_data = Module::create([
            'module_name' => 'Upload Sales Data'
        ]);
        $upload_sales_data = Permission::create(['name' => 'upload_sales_data']);
        ModulePermissions::create(['module_id' => $manage_sales_data->id, 'permission_id' => $upload_sales_data->id]);

        // Admininistrator
        $admin = Role::create([
            'name' => 'Administrator',
            'description' => 'Manage All Modules & Roles',
        ])->givePermissionTo(Permission::all());

        // TP
        $tp = Role::create([
            'name' => 'TP',
            'description' => 'Approve Profitable Analysis',
        ])->givePermissionTo(Permission::all());

        // TPC
        $tpc = Role::create([
            'name' => 'TPC',
            'description' => 'Manage Prospect Data',
        ])->givePermissionTo(Permission::all());

        // TPR
        $tpr = Role::create([
            'name' => 'TPR',
            'description' => 'Manage AMS Sales Plan Request',
        ])->givePermissionTo(Permission::all());

        // CBO
        $cbo = Role::create([
            'name' => 'CBO',
            'description' => 'Approve Sales Plan Requirement',
        ])->givePermissionTo(Permission::all());

        // AM
        $am = Role::create([
            'name' => 'AM',
            'description' => 'Show data AMS',
        ])->givePermissionTo(Permission::all());

        // AMS
        $ams = Role::create([
            'name' => 'AMS',
            'description' => 'Manage Sales Plan Requirement',
        ])->givePermissionTo(Permission::all());

        $init = Role::create([
            'name' => 'Initial',
            'description' => 'Initial Role for First-in LDAP User',
        ])->givePermissionTo([$read_users]);

        // TD
        // $td = Role::create([
        //     'name' => 'TD',
        //     'description' => 'Approve RKAP Sales Plan',
        // ])->givePermissionTo();

        // TP
        // $tp = Role::create([
        //     'name' => 'TP',
        //     'description' => 'Approve Profitable Analysis',
        // ])->givePermissionTo();
    }
}
