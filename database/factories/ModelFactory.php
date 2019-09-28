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

$factory->define(Bhoechie\Checklist\Models\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => str_random(8),
    ];
});

$factory->define(Bhoechie\Checklist\Models\CheckList\CheckList::class, function (Faker\Generator $faker, $data) {
    return [
        "object_domain" => "contact",
        "object_id" => 1,
        "due" => "2019-01-25T12:50:14+00:00",
        "urgency" => 1,
        "description" => "Need to verify this guy house.",
    ];
});
