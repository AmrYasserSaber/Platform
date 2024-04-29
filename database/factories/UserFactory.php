<?php

namespace Database\Factories;


use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'username' => $this->faker->unique()->userName,
            'email' => $this->faker->unique()->safeEmail,
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'job_title' => $this->faker->jobTitle,
            'phone' => $this->faker->phoneNumber,
            'birthdate' => $this->faker->date(),
            'cv' => $this->faker->url,
            'profile_picture' => $this->faker->imageUrl(),
            'password' => bcrypt($this->faker->password),
        ];
    }
}

