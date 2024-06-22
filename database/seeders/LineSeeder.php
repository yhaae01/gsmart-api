<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Line;

class LineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $hangars = \App\Models\Hangar::all();

        // Hangar 1
        Line::create([
            'hangar_id' => 1,
            'name' => "1A",
        ]);
        Line::create([
            'hangar_id' => 1,
            'name' => "1B",
        ]);
        Line::create([
            'hangar_id' => 1,
            'name' => "2A",
        ]);
        Line::create([
            'hangar_id' => 1,
            'name' => "2B",
        ]);

        // Hangar 2
        Line::create([
            'hangar_id' => 2,
            'name' => "1",
        ]);
        Line::create([
            'hangar_id' => 2,
            'name' => "2",
        ]);
        Line::create([
            'hangar_id' => 2,
            'name' => "3",
        ]);
        Line::create([
            'hangar_id' => 2,
            'name' => "4",
        ]);
        Line::create([
            'hangar_id' => 2,
            'name' => "5",
        ]);
        Line::create([
            'hangar_id' => 2,
            'name' => "6",
        ]);
        Line::create([
            'hangar_id' => 2,
            'name' => "7",
        ]);
        Line::create([
            'hangar_id' => 2,
            'name' => "8",
        ]);
        Line::create([
            'hangar_id' => 2,
            'name' => "9",
        ]);

        // Hangar 3
        Line::create([
            'hangar_id' => 3,
            'name' => "1",
        ]);
        Line::create([
            'hangar_id' => 3,
            'name' => "2",
        ]);
        Line::create([
            'hangar_id' => 3,
            'name' => "3",
        ]);

        // Hangar 4
        Line::create([
            'hangar_id' => 4,
            'name' => "1A",
        ]);
        Line::create([
            'hangar_id' => 4,
            'name' => "1B",
        ]);
        Line::create([
            'hangar_id' => 4,
            'name' => "2A",
        ]);
        Line::create([
            'hangar_id' => 4,
            'name' => "2B",
        ]);
        Line::create([
            'hangar_id' => 4,
            'name' => "3A",
        ]);
        Line::create([
            'hangar_id' => 4,
            'name' => "3B",
        ]);
        Line::create([
            'hangar_id' => 4,
            'name' => "4A",
        ]);
        Line::create([
            'hangar_id' => 4,
            'name' => "4B",
        ]);
        Line::create([
            'hangar_id' => 4,
            'name' => "5A",
        ]);
        Line::create([
            'hangar_id' => 4,
            'name' => "5B",
        ]);
        Line::create([
            'hangar_id' => 4,
            'name' => "6A",
        ]);
        Line::create([
            'hangar_id' => 4,
            'name' => "6B",
        ]);
        Line::create([
            'hangar_id' => 4,
            'name' => "7A",
        ]);
        Line::create([
            'hangar_id' => 4,
            'name' => "7B",
        ]);
        Line::create([
            'hangar_id' => 4,
            'name' => "8A",
        ]);
        Line::create([
            'hangar_id' => 4,
            'name' => "8B",
        ]);
        Line::create([
            'hangar_id' => 4,
            'name' => "9",
        ]);
        Line::create([
            'hangar_id' => 4,
            'name' => "10",
        ]);
        Line::create([
            'hangar_id' => 4,
            'name' => "11A",
        ]);
        Line::create([
            'hangar_id' => 4,
            'name' => "11B",
        ]);
        Line::create([
            'hangar_id' => 4,
            'name' => "12A",
        ]);
        Line::create([
            'hangar_id' => 4,
            'name' => "12B",
        ]);
        Line::create([
            'hangar_id' => 4,
            'name' => "13A",
        ]);
        Line::create([
            'hangar_id' => 4,
            'name' => "13B",
        ]);
        Line::create([
            'hangar_id' => 4,
            'name' => "14A",
        ]);
        Line::create([
            'hangar_id' => 4,
            'name' => "14B",
        ]);
        Line::create([
            'hangar_id' => 4,
            'name' => "15A",
        ]);
        Line::create([
            'hangar_id' => 4,
            'name' => "15B",
        ]);
        Line::create([
            'hangar_id' => 4,
            'name' => "Painting",
        ]);

        // Parking
        Line::create([
            'hangar_id' => 5,
            'name' => "Hangar 1 Wide Body 1A",
        ]);
        Line::create([
            'hangar_id' => 5,
            'name' => "Hangar 1 Wide Body 1B",
        ]);
        Line::create([
            'hangar_id' => 5,
            'name' => "Hangar 1 Wide Body 2A",
        ]);
        Line::create([
            'hangar_id' => 5,
            'name' => "Hangar 1 Wide Body 2B",
        ]);
        Line::create([
            'hangar_id' => 5,
            'name' => "Hangar 1 Wide Body 3A",
        ]);
        Line::create([
            'hangar_id' => 5,
            'name' => "Hangar 1 Wide Body 3B",
        ]);

        Line::create([
            'hangar_id' => 5,
            'name' => "Hangar 2 Wide Body 1",
        ]);
        Line::create([
            'hangar_id' => 5,
            'name' => "Hangar 2 Wide Body 2",
        ]);
        Line::create([
            'hangar_id' => 5,
            'name' => "Hangar 2 Wide Body 3",
        ]);
        Line::create([
            'hangar_id' => 5,
            'name' => "Hangar 2 Narrow Body 1",
        ]);
        Line::create([
            'hangar_id' => 5,
            'name' => "Hangar 2 Narrow Body 2",
        ]);
        Line::create([
            'hangar_id' => 5,
            'name' => "Hangar 2 Narrow Body 3",
        ]);
        Line::create([
            'hangar_id' => 5,
            'name' => "Hangar 2 Narrow Body 4",
        ]);
        Line::create([
            'hangar_id' => 5,
            'name' => "Hangar 2 Narrow Body 5",
        ]);
        Line::create([
            'hangar_id' => 5,
            'name' => "Hangar 2 Narrow Body 6",
        ]);
        Line::create([
            'hangar_id' => 5,
            'name' => "Hangar 2 Narrow Body 7",
        ]);
        Line::create([
            'hangar_id' => 5,
            'name' => "Hangar 2 Narrow Body 8",
        ]);
        Line::create([
            'hangar_id' => 5,
            'name' => "Hangar 2 Narrow Body 9",
        ]);
        Line::create([
            'hangar_id' => 5,
            'name' => "Hangar 2 Narrow Body 10",
        ]);
        Line::create([
            'hangar_id' => 5,
            'name' => "Hangar 2 Narrow Body 11",
        ]);

        Line::create([
            'hangar_id' => 5,
            'name' => "Hangar 3 Narrow Body 1A",
        ]);
        Line::create([
            'hangar_id' => 5,
            'name' => "Hangar 3 Narrow Body 1B",
        ]);
        Line::create([
            'hangar_id' => 5,
            'name' => "Hangar 3 Narrow Body 2A",
        ]);
        Line::create([
            'hangar_id' => 5,
            'name' => "Hangar 3 Narrow Body 2B",
        ]);
        Line::create([
            'hangar_id' => 5,
            'name' => "Hangar 3 Narrow Body 3A",
        ]);
        Line::create([
            'hangar_id' => 5,
            'name' => "Hangar 3 Narrow Body 3B",
        ]);
        Line::create([
            'hangar_id' => 5,
            'name' => "Hangar 3 Narrow Body 4A",
        ]);
        Line::create([
            'hangar_id' => 5,
            'name' => "Hangar 3 Narrow Body 4B",
        ]);
        Line::create([
            'hangar_id' => 5,
            'name' => "Hangar 3 Narrow Body 5A",
        ]);
        Line::create([
            'hangar_id' => 5,
            'name' => "Hangar 3 Narrow Body 5B",
        ]);
        Line::create([
            'hangar_id' => 5,
            'name' => "Hangar 3 Narrow Body 6A",
        ]);
        Line::create([
            'hangar_id' => 5,
            'name' => "Hangar 3 Narrow Body 6B",
        ]);
        Line::create([
            'hangar_id' => 5,
            'name' => "Hangar 3 Narrow Body 7A",
        ]);
        Line::create([
            'hangar_id' => 5,
            'name' => "Hangar 3 Narrow Body 7B",
        ]);
        Line::create([
            'hangar_id' => 6,
            'name' => "1",
        ]);
        Line::create([
            'hangar_id' => 7,
            'name' => "1",
        ]);
        Line::create([
            'hangar_id' => 8,
            'name' => "Turbine part Repair",
        ]);
        Line::create([
            'hangar_id' => 8,
            'name' => "Motor-Generator Repair",
        ]);
    }
}
