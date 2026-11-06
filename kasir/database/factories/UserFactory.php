<?php

namespace Database\Factories;

use App\Models\User;
use App\Support\Security\Md5Password;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<User> */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'nama' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'username' => fake()->unique()->userName(),
            'password' => Md5Password::make('password123'),
            'role' => User::KASIR,
            'status' => 'aktif',
        ];
    }
}
