<?php

namespace Database\Seeders;

use App\Models\Requirement;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RequirementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Requirement::create([
            'level_id' => 4,
            'requirement' => 'Fill in Contact Person of Customer',
            'value' => 5,
        ]);
        Requirement::create([
            'level_id' => 4,
            'requirement' => 'Upload Attachment RFQ or Email Request',
            'value' => 10,
        ]);
        Requirement::create([
            'level_id' => 4,
            'requirement' => 'Upload Attachment Workscope',
            'value' => 10,
        ]);
        Requirement::create([
            'level_id' => 4,
            'requirement' => 'Hangar & Line Slot Request',
            'value' => 5,
        ]);
        Requirement::create([
            'level_id' => 3,
            'requirement' => 'Attachment of Financial Assesment Form (optional)',
            'value' => 0,
        ]);
        Requirement::create([
            'level_id' => 3,
            'requirement' => 'Attachment of Maintenance Proposal for Customer',
            'value' => 15,
        ]);
        Requirement::create([
            'level_id' => 3,
            'requirement' => 'Attachment of Profitability Analysis Form Signed',
            'value' => 15,
        ]);
        Requirement::create([
            'level_id' => 2,
            'requirement' => 'Attachment of Customer Approval (SOW Signed / Proposal Approved)',
            'value' => 15,
        ]);
        Requirement::create([
            'level_id' => 1,
            'requirement' => 'Attachment of WO/PO number Customer Document',
            'value' => 15,
        ]);
        Requirement::create([
            'level_id' => 1,
            'requirement' => 'Input SO number',
            'value' => 10,
        ]);
    }
}
