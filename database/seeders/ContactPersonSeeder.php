<?php

namespace Database\Seeders;

use App\Models\ContactPerson as CP;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;

class ContactPersonSeeder extends Seeder
{
    protected $faker;

    public function __construct()
    {
        $this->faker = \Faker\Factory::create();
    }

    public function run()
    {
        // $customer = Customer::all();

        // foreach ($customer as $item) {
        //     CP::create([
        //         'name' => $this->faker->name(),
        //         'phone' => $this->faker->phoneNumber(),
        //         'email' => $this->faker->email(),
        //         'address' => $this->faker->address(),
        //         'customer_id' => $item->id,
        //         'title' => $this->faker->text(50),
        //     ]);
        // }
    }
}
