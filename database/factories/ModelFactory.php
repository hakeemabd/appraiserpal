<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->userName . '@mailinator.com',
        'password' => bcrypt('123321'),
        'confirmed' => true,
        'auto_delivery' => false,
        'auto_invite' => $faker->randomElement([true, false]),
        'mobile_phone' => $faker->phoneNumber,
        'work_phone' => $faker->phoneNumber,
        'address_line_1' => $faker->streetAddress,
        'address_line_2' => $faker->optional(0.5, '')->streetAddress,
        'zip' => $faker->postcode,
        'license_number' => $faker->creditCardNumber,
        'paypal_email' => $faker->email,
        'bank_name' => $faker->domainName,
        'account_number' => $faker->creditCardNumber,
        'free_order_count' => $faker->randomElement([0,1,2])
    ];
});

$factory->define(App\Models\Order::class, function (Faker\Generator $faker) {
    return [
        'title' => $faker->streetAddress,
        'completed' => $faker->randomElement([0, 1]),
        'status' => \App\Models\Order::STATUS_NEW,
        'effective_date' => $faker->dateTimeBetween('-1 month', 'now'),
        'standard_instructions' => $faker->text(),
        'report_type_id' => $faker->numberBetween(1, 8),
        'software_id' => $faker->numberBetween(1, 5),
    ];
});

$factory->define(App\Models\WorkerGroup::class, function (Faker\Generator $faker) {
    return [
        'sort' => 1,
        'name' => $faker->randomElement(['Group A', 'Group B', 'Group C', 'Group D']),
    ];
});