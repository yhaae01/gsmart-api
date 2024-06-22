<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\{
    Collection,
    Str,
};
use PhpOffice\PhpSpreadsheet\Shared\Date;
use App\Models\{
    Sales,
    Customer,
    Product,
    TransactionType,
    Line,
    Area,
    AMS,
    AMSCustomer,
    Countries,
    Region,
    Hangar,
    IGTE,
    Component,
    AircraftType,
    Learning,
    Engine,
    Apu,
    Maintenance,
    Prospect,
    TMB,
    PBTH,
    SalesLevel,
    SalesRequirement,
    CancelCategory,
    SalesReject,
    Requirement,
    SalesMaintenance,
};
use Maatwebsite\Excel\Concerns\{
    ToCollection, 
    WithHeadingRow
};
use Illuminate\Support\Facades\{ 
    Log, 
    DB 
};
use Illuminate\Database\RecordsNotFoundException;

class SalesImport implements ToCollection, WithHeadingRow
{
    public $status;
    public $message;
    private $row = 1;

    const TRANSACTION_RETAIL = 'TMB Retail';
    const TRANSACTION_PROJECT = 'TMB Project';
    const TRANSACTION_PBTH = 'PBTH';
    const PROSPECT_ORGANIC = 'Organic';
    const PROSPECT_IN_ORGANIC = 'In Organic';
    const STRATEGIC_MILITARY = 'Military';
    const STRATEGIC_BUSINESS = 'Business';
    const STRATEGIC_CORPORATE = 'Corporate';
    const SALES_RKAP = 'RKAP';
    const SALES_ADDITIONAL = 'Additional';

    const TRANSACTION_TYPES = [
        self::TRANSACTION_RETAIL => 1,
        self::TRANSACTION_PROJECT => 2,
        self::TRANSACTION_PBTH => 3,
    ];

    const PROSPECT_TYPES = [
        self::PROSPECT_ORGANIC => 1,
        self::PROSPECT_IN_ORGANIC => 2,
    ];
    
    const STRATEGIC_INITIATIVES = [
        self::STRATEGIC_MILITARY => 1,
        self::STRATEGIC_BUSINESS => 2,
        self::STRATEGIC_CORPORATE => 3,
    ];

    const SALES_TYPES = [
        self::SALES_RKAP => true,
        self::SALES_ADDITIONAL => false,
    ];

    public function collection(Collection $rows)
    {
        try {
            DB::beginTransaction();

            foreach ($rows as $row) {
                ++$this->row;

                $start_date = $row['start_date'] ? Carbon::instance(Date::excelToDateTimeObject($row['start_date'])) : null;
                $tat = trim($row['tat']) ?? null;

                if ($start_date) {
                    if ($row['end_date']) {
                        $end_date = Carbon::instance(Date::excelToDateTimeObject($row['end_date']));
                    } else if ($tat) {
                        $s_date = Carbon::instance(Date::excelToDateTimeObject($row['start_date']));
                        $end_date = $s_date->addDays($tat);
                    } else {
                        $end_date = null;
                    }
                } else {
                    $end_date = null;
                }

                if (empty($row['country'])) {
                    throw new RecordsNotFoundException("Country name (row {$this->row}) cannot be empty.");
                }

                if (empty($row['region'])) {
                    throw new RecordsNotFoundException("Region name (row {$this->row}) cannot be empty.");
                }

                $country = Countries::firstWhere('name', trim($row['country']));

                if (!$country) {
                    $region = Region::firstWhere('name', trim($row['region']));

                    if (!$region) {
                        throw new RecordsNotFoundException("Region with name '{$row['region']}' (row {$this->row}) does not exists in our records.");
                    }

                    $country = new Countries;
                    $country->name = trim($row['country']);
                    $country->region_id = $region->id;
                    $country->save();
                }

                $customer_code = trim($row['customer_code']);
                $group_type = (trim($row['group_type']) == 'NGA') ? 1 : 0;
                $customer = Customer::where('name', trim($row['customer']))
                                    ->where('code', $customer_code)
                                    ->where('group_type', $group_type)
                                    ->where('country_id', $country->id)
                                    ->first();

                if (!$customer) {
                    $customer = new Customer;
                    $customer->code = $customer_code;
                    $customer->name = trim($row['customer']);
                    $customer->group_type = (trim($row['group_type']) == 'GA') ? 0 : 1;
                    $customer->country_id = $country->id;
                    $customer->is_active = true;
                    $customer->save();
                }

                $area = Area::firstWhere('name', trim($row['area']));
                if (!$area) {
                    throw new RecordsNotFoundException("Area with name '{$row['area']}' (row {$this->row}) does not exists in our records.");
                }

                $ams = AMS::firstWhere('initial', trim($row['ams']));
                if (!$ams) {
                    throw new RecordsNotFoundException("AMS with name '{$row['ams']}' (row {$this->row}) does not exists in our records.");
                }

                $ams_customer = AMSCustomer::where('customer_id', $customer->id)
                                            ->where('area_id', $area->id)
                                            ->where('ams_id', $ams->id)
                                            ->first();
                if (!$ams_customer) {
                    $ams_customer = new AMSCustomer;
                    $ams_customer->customer_id = $customer->id;
                    $ams_customer->area_id = $area->id;
                    $ams_customer->ams_id = $ams->id;
                    $ams_customer->save();
                }

                $product = Product::firstWhere('name', trim($row['product']));
                if (!$product) {
                    $product = new Product;
                    $product->name = trim($row['product']);
                    $product->save();
                }

                if (empty($row['transaction_type'])) {
                    throw new RecordsNotFoundException("Transaction Type (row {$this->row}) cannot be empty.");
                } else {
                    try {
                        $transaction_type = self::TRANSACTION_TYPES[trim($row['transaction_type'])];
                    } catch (\Exception $e) {
                        throw new RecordsNotFoundException("Transaction Type '{$row['transaction_type']}' (row {$this->row}) do not match our records.");
                    }
                }

                if (empty($row['prospect_type'])) {
                    throw new RecordsNotFoundException("Prospect Type (row {$this->row}) cannot be empty.");
                } else {
                    try {
                        $prospect_type = self::PROSPECT_TYPES[trim($row['prospect_type'])];
                    } catch (\Exception $e) {
                        throw new RecordsNotFoundException("Prospect Type '{$row['prospect_type']}' (row {$this->row}) do not match our records.");
                    }
                }

                if (empty($row['strategic_initiative'])) {
                    $strategic_initiative = null;
                } else {
                    try {
                        $strategic_initiative = self::STRATEGIC_INITIATIVES[trim($row['strategic_initiative'])];
                    } catch (\Exception $e) {
                        throw new RecordsNotFoundException("Strategic Initiative '{$row['strategic_initiative']}' (row {$this->row}) do not match our records.");
                    }
                }

                if (empty($row['sales_type'])) {
                    throw new RecordsNotFoundException("Sales Type (row {$this->row}) cannot be empty.");
                } else {
                    try {
                        $is_rkap = self::SALES_TYPES[trim($row['sales_type'])];
                    } catch (\Exception $e) {
                        throw new RecordsNotFoundException("Sales Type '{$row['sales_type']}' (row {$this->row}) do not match our records.");
                    }
                }

                if ($product->name == 'Airframe') {
                    $ac_type = AircraftType::firstWhere('name', $row['acengapucomp']);
                    if (!$ac_type) {
                        if ($row['acengapucomp'] == '-' || empty($row['acengapucomp'])) {
                            $ac_type = null;
                        } else {
                            $ac_type = new AircraftType;
                            $ac_type->name = $row['acengapucomp'];
                            $ac_type->save();
                        }
                    }
                    $igte = null;
                    $learning = null;
                    $engine = null;
                    $apu = null;
                    $component = null;
                } else if ($product->name == 'Component') {
                    $component = Component::firstWhere('name', $row['acengapucomp']);
                    if (!$component) {
                        if ($row['acengapucomp'] == '-' || empty($row['acengapucomp'])) {
                            $compoenent = null;
                        } else {
                            $component = new Component;
                            $component->name = $row['acengapucomp'];
                            $component->save();
                        }
                    }
                    $igte = null;
                    $learning = null;
                    $engine = null;
                    $apu = null;
                } else if ($product->name == 'IGTE') {
                    $igte = IGTE::firstWhere('name', $row['acengapucomp']);
                    if (!$igte) {
                        if ($row['acengapucomp'] == '-' || empty($row['acengapucomp'])) {
                            $igte = null;
                        } else {
                            $igte = new IGTE;
                            $igte->name = $row['acengapucomp'];
                            $igte->save();
                        }
                    }
                    $ac_type = null;
                    $component = null;
                    $learning = null;
                    $engine = null;
                    $apu = null;
                } else if ($product->name == 'Learning') {
                    $learning = Learning::firstWhere('name', $row['acengapucomp']);
                    if (!$learning) {
                        if ($row['acengapucomp'] == '-' || empty($row['acengapucomp'])) {
                            $learning = null;
                        } else {
                            $learning = new Learning;
                            $learning->name = $row['acengapucomp'];
                            $learning->save();
                        }
                    }
                    $ac_type = null;
                    $component = null;
                    $igte = null;
                    $engine = null;
                    $apu = null;
                } else {
                    $engine = Engine::firstWhere('name', $row['acengapucomp']);
                    $apu = Apu::firstWhere('name', $row['acengapucomp']);
                    $ac_type = null;
                    $component = null;
                    $igte = null;
                    $learning = null;
                }

                $maintenances = $row['maintenance_event'];

                if ($maintenances == '-' || empty($maintenances)) {
                    $maintenance = null;
                } else {
                    if (str_contains($maintenances, ";")) {
                        $maintenance_names = explode(";", $maintenances);
                        $maintenance = [];

                        for ($i = 0; $i < count($maintenance_names); $i++) { 
                            $exists_maintenance = Maintenance::firstWhere('name', trim($maintenance_names[$i]));

                            if (!$exists_maintenance) {
                                $new_maintenance = new Maintenance;
                                $new_maintenance->name = trim($maintenance_names[$i]);
                                $new_maintenance->product_id = Product::firstOrNew(['name' => 'Other'])->id;
                                $new_maintenance->save();

                                array_push($maintenance, $new_maintenance->id);
                            } else {
                                array_push($maintenance, $exists_maintenance->id);
                            }
                        }
                    } else {
                        $exists_maintenance = Maintenance::firstWhere('name', trim($maintenances));

                        if (!$exists_maintenance) {
                            $new_maintenance = new Maintenance;
                            $new_maintenance->name = trim($maintenances);
                            $new_maintenance->product_id = Product::firstOrNew(['name' => 'Other'])->id;
                            $new_maintenance->save();

                            $maintenance = $new_maintenance->id;
                        } else {
                            $maintenance = $exists_maintenance->id;
                        }
                    }
                }

                if (empty($row['strategic_initiative'])) {
                    $hangar = null;
                } else {
                    $hangar = Hangar::firstWhere('name', trim($row['hangar']));

                    if (!$hangar) {
                        throw new RecordsNotFoundException("Hangar '{$row['hangar']}' (row {$this->row}) does not exists in our records.");
                    }
                }

                if ($is_rkap) {
                    if (empty($row['market_share']) || $row['market_share'] == '-' || $row['market_share'] == 0) {
                        $sales_value = $row['sales_plan'];
                    } else {
                        $sales_value = $row['market_share'];
                    }

                    $ac_reg = empty($row['ac_reg']) ? null : $row['ac_reg'];
                    $so_number = empty($raw['so_number']) ? null : $raw['so_number'];

                    $year = trim($row['year']);
                    $month = Carbon::parse($start_date)->format('F');
                    $market_share = (empty($row['market_share']) || $row['market_share'] == '0') ? $row['sales_plan'] : $row['market_share'];
                    $remarks = $row['remarks'] ?? null;

                    if (in_array($transaction_type, [1, 2])) {
                        $maintenance_id = is_array($maintenance) ? $maintenance[0] : $maintenance;

                        $prospect = Prospect::where('year', $year)
                                            ->where('transaction_type_id', $transaction_type)
                                            ->where('prospect_type_id', $prospect_type)
                                            ->where('strategic_initiative_id', $strategic_initiative)
                                            ->where('ams_customer_id', $ams_customer->id)
                                            ->whereHas('tmb', function ($query) use ($product, $ac_type, $component, $engine, $apu, $igte, $learning, $maintenance_id, $remarks) {
                                                $query->where('product_id', $product->id)
                                                    ->where('ac_type_id', $ac_type?->id)
                                                    ->where('component_id', $component?->id)
                                                    ->where('engine_id', $engine?->id)
                                                    ->where('apu_id', $apu?->id)
                                                    ->where('igte_id', $igte?->id)
                                                    ->where('learning_id', $learning?->id)
                                                    ->where('maintenance_id', $maintenance_id)
                                                    ->where('remarks', $remarks);
                                            })
                                            ->first();

                        if (!$prospect) {
                            $prospect = new Prospect;
                            $prospect->year = $year;
                            $prospect->transaction_type_id = $transaction_type;
                            $prospect->prospect_type_id = $prospect_type;
                            $prospect->strategic_initiative_id = $strategic_initiative;
                            $prospect->pm_id = null; // NOTE: data not provided in sheet
                            $prospect->ams_customer_id = $ams_customer->id;
                            $prospect->save();

                            $tmb = new TMB;
                            $tmb->prospect_id = $prospect->id;
                            $tmb->product_id = $product->id;
                            $tmb->ac_type_id = $ac_type?->id;
                            $tmb->igte_id = $igte?->id;
                            $tmb->learning_id = $learning?->id;
                            $tmb->component_id = $component?->id;
                            $tmb->engine_id = $engine?->id;
                            $tmb->apu_id = $apu?->id;
                            $tmb->maintenance_id = $maintenance_id;
                            $tmb->market_share = (!$market_share || $market_share == '-') ? 0 : $market_share;
                            $tmb->remarks = $remarks;
                            $tmb->save();
                        }

                        $sales = new Sales;
                        $sales->customer_id = $customer->id;
                        $sales->prospect_id = $is_rkap ? $prospect->id : null;
                        $sales->transaction_type_id = $transaction_type;
                        $sales->ac_reg = $ac_reg;
                        $sales->value = $sales_value;
                        $sales->tat = $tat;
                        $sales->start_date = $start_date->format('Y-m-d');
                        $sales->end_date = $end_date->format('Y-m-d');
                        $sales->so_number = $so_number;
                        $sales->hangar_id = $hangar?->id;
                        $sales->line_id = null;
                        $sales->product_id = $product->id;
                        $sales->ac_type_id = $ac_type?->id;
                        $sales->igte_id = $igte?->id;
                        $sales->learning_id = $learning?->id;
                        $sales->component_id = $component?->id;
                        $sales->engine_id = $engine?->id;
                        $sales->apu_id = $apu?->id;
                        $sales->is_rkap = true;
                        $sales->ams_id = $ams->id;
                        $sales->save();
                    } else {
                        if (!$ac_type) {
                            if ($row['acengapucomp'] == '-' || empty($row['acengapucomp'])) {
                                $ac_type = null;
                            } else {
                                $ac_type = new AircraftType;
                                $ac_type->name = $row['acengapucomp'];
                                $ac_type->save();
                            }
                        }

                        $target_rate = trim($row['rate']) ?? null;
                        $flight_hour = trim($row['flight_hour']) ?? null;

                        $prospect = Prospect::where('year', $year)
                                            ->where('transaction_type_id', $transaction_type)
                                            ->where('prospect_type_id', $prospect_type)
                                            ->where('strategic_initiative_id', $strategic_initiative)
                                            ->where('ams_customer_id', $ams_customer->id)
                                            ->whereDoesntHave('sales', function ($query) use ($month) {
                                                $query->whereMonth('start_date', $month);
                                                })
                                            ->whereDoesntHave('pbth', function ($query) use ($month) {
                                                $query->where('month', Carbon::parse("1 {$month}")->month);
                                            })
                                            ->first();

                        if (!$prospect) {
                            $prospect = new Prospect;
                            $prospect->year = $year;
                            $prospect->transaction_type_id = $transaction_type;
                            $prospect->prospect_type_id = $prospect_type;
                            $prospect->strategic_initiative_id = $strategic_initiative;
                            $prospect->pm_id = null; // NOTE: data not provided in sheet
                            $prospect->ams_customer_id = $ams_customer->id;
                            $prospect->save();
                        }

                        $pbth = new PBTH;
                        $pbth->prospect_id = $prospect->id;
                        $pbth->product_id = $product->id;
                        $pbth->ac_type_id = $ac_type?->id;
                        $pbth->month = Carbon::parse("1 {$month}")->month;
                        $pbth->rate = $target_rate;
                        $pbth->flight_hour = $flight_hour;
                        $pbth->market_share = (!$market_share || $market_share == '-') ? 0 : $market_share;
                        $pbth->save();

                        $sales = new Sales;
                        $sales->customer_id = $customer->id;
                        $sales->prospect_id = $is_rkap ? $prospect->id : null;
                        $sales->transaction_type_id = $transaction_type;
                        $sales->ac_reg = $ac_reg;
                        $sales->value = $sales_value;
                        $sales->tat = $tat;
                        $sales->start_date = $start_date->format('Y-m-d');
                        $sales->end_date = $end_date->format('Y-m-d');
                        $sales->so_number = $so_number;
                        $sales->hangar_id = $hangar?->id;
                        $sales->line_id = null;
                        $sales->product_id = $product->id;
                        $sales->ac_type_id = $ac_type?->id;
                        $sales->igte_id = $igte?->id;
                        $sales->learning_id = $learning?->id;
                        $sales->component_id = $component?->id;
                        $sales->engine_id = $engine?->id;
                        $sales->apu_id = $apu?->id;
                        $sales->is_rkap = true;
                        $sales->ams_id = $ams->id;
                        $sales->save();
                    }

                    if ($maintenance) {
                        if (is_array($maintenance)) {
                            for ($i = 0; $i < count($maintenance); $i++) { 
                                $sales_maintenance = new SalesMaintenance;
                                $sales_maintenance->sales_id = $sales->id;
                                $sales_maintenance->maintenance_id = $maintenance[$i];
                                $sales_maintenance->save();
                            }
                        } else {
                            $sales_maintenance = new SalesMaintenance;
                            $sales_maintenance->sales_id = $sales->id;
                            $sales_maintenance->maintenance_id = $maintenance;
                            $sales_maintenance->save();
                        }
                    }

                    if (in_array($row['sales_level'], [1, 2, 3, 4])) {
                        $level = $row['sales_level'];
                    } else {
                        throw new RecordsNotFoundException("Sales Level '{$row['sales_level']}' (row {$this->row}) do not match our records.");
                    }

                    if (trim($row['status']) == 'Open') {
                        $status = 1;
                    } else if (trim($row['status']) == 'Closed - Sales') {
                        $status = 2;
                    } else if (trim($row['status']) == 'Closed - In') {
                        $status = 3;
                    } else if (trim($row['status']) == 'Cancelled') {
                        $status = 4;
                    } else {
                        throw new RecordsNotFoundException("Sales Status '{$row['status']}' (row {$this->row}) do not match our records.");
                    }

                    $sales_level = new SalesLevel;
                    $sales_level->sales_id = $sales->id;
                    $sales_level->level_id = $level;
                    $sales_level->status = $status;
                    $sales_level->save();

                    if ($level == 1) {
                        if ($status == 3) {
                            for ($i = 1; $i <= 10; $i++) { 
                                $requirement = Requirement::findOrFail($i);

                                $sales_requirement = new SalesRequirement;
                                $sales_requirement->sales_id = $sales->id;
                                $sales_requirement->requirement_id = $requirement->id;
                                $sales_requirement->value = $requirement->value;
                                $sales_requirement->status = 1;
                                $sales_requirement->save();
                            }
                        } else {
                            for ($i = 1; $i <= 10; $i++) { 
                                $requirement = Requirement::findOrFail($i);

                                $sales_requirement = new SalesRequirement;
                                $sales_requirement->sales_id = $sales->id;
                                $sales_requirement->requirement_id = $requirement->id;
                                $sales_requirement->value = $requirement->value;
                                $sales_requirement->status = in_array($i, [9, 10]) ? 0 : 1;
                                $sales_requirement->save();
                            }
                        }
                    } else if ($level == 2) {
                        for ($i = 1; $i <= 10; $i++) { 
                            $requirement = Requirement::findOrFail($i);

                            $sales_requirement = new SalesRequirement;
                            $sales_requirement->sales_id = $sales->id;
                            $sales_requirement->requirement_id = $requirement->id;
                            $sales_requirement->value = $requirement->value;
                            $sales_requirement->status = in_array($i, [8, 9, 10]) ? 0 : 1;
                            $sales_requirement->save();
                        }
                    }else {
                        if ($transaction_type != 3) {
                            for ($i = 1; $i <= 10; $i++) { 
                                $requirement = Requirement::findOrFail($i);

                                $sales_requirement = new SalesRequirement;
                                $sales_requirement->sales_id = $sales->id;
                                $sales_requirement->requirement_id = $requirement->id;
                                $sales_requirement->value = $requirement->value;
                                $sales_requirement->status = in_array($i, [1, 5]) ? 0 : 1;
                                $sales_requirement->save();
                            }
                        } else {
                            for ($i = 1; $i <= 10; $i++) { 
                                $requirement = Requirement::findOrFail($i);

                                $sales_requirement = new SalesRequirement;
                                $sales_requirement->sales_id = $sales->id;
                                $sales_requirement->requirement_id = $requirement->id;
                                $sales_requirement->value = $requirement->value;
                                $sales_requirement->status = in_array($i, [9, 10]) ? 0 : 1;
                                $sales_requirement->save();
                            }
                        }
                    }

                    if ($status == 4) {
                        $cancel_category = CancelCategory::firstWhere('name', trim($row['cancel_reason']));
                        if (!$cancel_category) {
                            $cancel_category = CancelCategory::firstWhere('name', 'Other');
                            if (!$cancel_category) {
                                throw new RecordsNotFoundException("Cancel Category '{$row['cancel_reason']}' (row {$this->row}) does not exists in our records.");
                            }
                        }

                        $cancel_reason = trim($row['detailed_cancel_reason']);

                        $sales_cancel = new SalesReject;
                        $sales_cancel->sales_id = $sales->id;
                        $sales_cancel->category_id = $cancel_category->id;
                        $sales_cancel->reason = $cancel_reason;
                        $sales_cancel->save();
                    }
                } else {
                    if ($transaction_type == 3) {
                        throw new RecordsNotFoundException("Transaction Type for Additional Sales Plan (row {$this->row}) must be in TMB Retail or TMB Project.");
                    }

                    if (empty($row['market_share']) || $row['market_share'] == '-' || $row['market_share'] == 0) {
                        $sales_value = $row['sales_plan'];
                    } else {
                        $sales_value = $row['market_share'];
                    }

                    $ac_reg = empty($row['ac_reg']) ? null : $row['ac_reg'];
                    $so_number = empty($raw['so_number']) ? null : $raw['so_number'];

                    $sales = new Sales;
                    $sales->customer_id = $customer->id;
                    $sales->transaction_type_id = $transaction_type;
                    $sales->ac_reg = $ac_reg;
                    $sales->value = $sales_value;
                    $sales->tat = $tat;
                    $sales->start_date = $start_date->format('Y-m-d');
                    $sales->end_date = $end_date->format('Y-m-d');
                    $sales->so_number = $so_number;
                    $sales->hangar_id = $hangar?->id;
                    $sales->line_id = null;
                    $sales->product_id = $product->id;
                    $sales->ac_type_id = $ac_type?->id;
                    $sales->is_rkap = false;
                    $sales->ams_id = $ams->id;
                    $sales->save();

                    if ($maintenance) {
                        if (is_array($maintenance)) {
                            for ($i = 0; $i < count($maintenance); $i++) { 
                                $sales_maintenance = new SalesMaintenance;
                                $sales_maintenance->sales_id = $sales->id;
                                $sales_maintenance->maintenance_id = $maintenance[$i];
                                $sales_maintenance->save();
                            }
                        } else {
                            $sales_maintenance = new SalesMaintenance;
                            $sales_maintenance->sales_id = $sales->id;
                            $sales_maintenance->maintenance_id = $maintenance;
                            $sales_maintenance->save();
                        }
                    }

                    if (in_array($row['sales_level'], [1, 2, 3, 4])) {
                        $level = $row['sales_level'];
                    } else {
                        throw new RecordsNotFoundException("Sales Level '{$row['sales_level']}' (row {$this->row}) do not match our records.");
                    }

                    if (trim($row['status']) == 'Open') {
                        $status = 1;
                    } else if (trim($row['status']) == 'Closed - Sales') {
                        $status = 2;
                    } else if (trim($row['status']) == 'Closed - In') {
                        $status = 3;
                    } else if (trim($row['status']) == 'Cancelled') {
                        $status = 4;
                    } else {
                        throw new RecordsNotFoundException("Sales Status '{$row['status']}' (row {$this->row}) do not match our records.");
                    }

                    $sales_level = new SalesLevel;
                    $sales_level->sales_id = $sales->id;
                    $sales_level->level_id = $level;
                    $sales_level->status = $status;
                    $sales_level->save();

                    if ($level == 1) {
                        if ($status == 3) {
                            for ($i = 1; $i <= 10; $i++) { 
                                $requirement = Requirement::findOrFail($i);

                                $sales_requirement = new SalesRequirement;
                                $sales_requirement->sales_id = $sales->id;
                                $sales_requirement->requirement_id = $requirement->id;
                                $sales_requirement->value = $requirement->value;
                                $sales_requirement->status = 1;
                                $sales_requirement->save();
                            }
                        } else {
                            for ($i = 1; $i <= 10; $i++) { 
                                $requirement = Requirement::findOrFail($i);

                                $sales_requirement = new SalesRequirement;
                                $sales_requirement->sales_id = $sales->id;
                                $sales_requirement->requirement_id = $requirement->id;
                                $sales_requirement->value = $requirement->value;
                                $sales_requirement->status = in_array($i, [9, 10]) ? 0 : 1;
                                $sales_requirement->save();
                            }
                        }
                    } else if ($level == 2) {
                        for ($i = 1; $i <= 10; $i++) { 
                            $requirement = Requirement::findOrFail($i);

                            $sales_requirement = new SalesRequirement;
                            $sales_requirement->sales_id = $sales->id;
                            $sales_requirement->requirement_id = $requirement->id;
                            $sales_requirement->value = $requirement->value;
                            $sales_requirement->status = in_array($i, [8, 9, 10]) ? 0 : 1;
                            $sales_requirement->save();
                        }
                    }else {
                        if ($transaction_type != 3) {
                            for ($i = 1; $i <= 10; $i++) { 
                                $requirement = Requirement::findOrFail($i);

                                $sales_requirement = new SalesRequirement;
                                $sales_requirement->sales_id = $sales->id;
                                $sales_requirement->requirement_id = $requirement->id;
                                $sales_requirement->value = $requirement->value;
                                $sales_requirement->status = in_array($i, [1, 5]) ? 0 : 1;
                                $sales_requirement->save();
                            }
                        } else {
                            for ($i = 1; $i <= 10; $i++) { 
                                $requirement = Requirement::findOrFail($i);

                                $sales_requirement = new SalesRequirement;
                                $sales_requirement->sales_id = $sales->id;
                                $sales_requirement->requirement_id = $requirement->id;
                                $sales_requirement->value = $requirement->value;
                                $sales_requirement->status = in_array($i, [9, 10]) ? 0 : 1;
                                $sales_requirement->save();
                            }
                        }
                    }

                    if ($status == 4) {
                        $cancel_category = CancelCategory::firstWhere('name', trim($row['cancel_reason']));
                        if (!$cancel_category) {
                            $cancel_category = CancelCategory::firstWhere('name', 'Other');
                            if (!$cancel_category) {
                                throw new RecordsNotFoundException("Cancel Category '{$row['cancel_reason']}' (row {$this->row}) does not exists in our records.");
                            }
                        }

                        $cancel_reason = trim($row['detailed_cancel_reason']);

                        $sales_cancel = new SalesReject;
                        $sales_cancel->sales_id = $sales->id;
                        $sales_cancel->category_id = $cancel_category->id;
                        $sales_cancel->reason = $cancel_reason;
                        $sales_cancel->save();
                    }
                }
            }

            $this->status = 200;
            $this->message = 'Sales Data has been imported successfully.';

            DB::commit();
        } catch (RecordsNotFoundException $e) {
            $this->status = 404;
            $this->message = "Import failed, {$e->getMessage()}";

            DB::rollback();
        } catch (\Exception $e) {
            $this->status = 500;
            $this->message = 'Error occured while importing data, check the data (format or layout) then try again.';

            DB::rollback();
            Log::error($e);
        }
    }
}
