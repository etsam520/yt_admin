<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\TradeNode;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    /** 
     * Run the database seeds.
     */
    public function run(): void
    {
        // Course::truncate();

        $tradeNodes = TradeNode::all();

        if ($tradeNodes->isEmpty()) {
            $this->call(TradeNodeSeeder::class); // Ensure trade nodes exist
            $tradeNodes = TradeNode::all();
        }

        foreach ($tradeNodes as $tradeNode) {
            for ($i = 0; $i < 3; $i++) { // Create 3 courses per trade node
                $courseName = $tradeNode->name . ' Course ' . ($i + 1);
                Course::create([
                    'name' => $courseName,
                    'description' => 'A comprehensive course covering ' . Str::lower($tradeNode->name) . ' fundamentals.',
                    'slug' => Str::slug($courseName),
                    'image' => 'course_image_' . ($i + 1) . '.jpg',
                    'status' => ['draft', 'published', 'archived'][array_rand(['draft', 'published', 'archived'])],
                    'trade_node_id' => $tradeNode->id,
                ]);
            }
        }
    }
}
