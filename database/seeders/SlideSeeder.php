<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SlideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sliders = [
            'dummy/dummy1.png',
            'dummy/dummy2.png',
            'dummy/dummy3.png',
        ];
        foreach ($sliders as $slider) {
            \App\Models\Slider::create([
                'image' => $slider
            ]);
        }


    }
}
