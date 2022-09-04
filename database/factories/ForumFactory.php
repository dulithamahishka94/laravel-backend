<?php

namespace Database\Factories;

use App\Models\Forum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ForumFactory extends Factory
{
    protected $model = Forum::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->text(100),
            'description' => $this->faker->paragraph(5),
            'approved' => 0,
            'approved_by' => null,
            'deleted' => 0,
            'deleted_by' => null,
            'user_id' => User::inRandomOrder()->where('type', User::DEFAULT_USER)->first('id')->id,
        ];
    }
}
