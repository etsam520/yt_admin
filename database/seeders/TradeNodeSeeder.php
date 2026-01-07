<?php

namespace Database\Seeders;

use App\Models\TradeNode;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TradeNodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data (optional, but good for re-seeding)
        // TradeNode::truncate();

        // Create parent trade nodes
        $programming = TradeNode::create([
            'name' => 'Programming',
            'slug' => Str::slug('Programming'),
            'icon' => 'code.svg',
            'parent_id' => null,
        ]);

        $design = TradeNode::create([
            'name' => 'Design',
            'slug' => Str::slug('Design'),
            'icon' => 'design.svg',
            'parent_id' => null,
        ]);

        $business = TradeNode::create([
            'name' => 'Business',
            'slug' => Str::slug('Business'),
            'icon' => 'chart.svg',
            'parent_id' => null,
        ]);

        // Create child trade nodes
        TradeNode::create([
            'name' => 'Web Development',
            'slug' => Str::slug('Web Development'),
            'icon' => 'web.svg',
            'parent_id' => $programming->id,
        ]);

        TradeNode::create([
            'name' => 'Mobile App Development',
            'slug' => Str::slug('Mobile App Development'),
            'icon' => 'mobile.svg',
            'parent_id' => $programming->id,
        ]);

        TradeNode::create([
            'name' => 'UI/UX Design',
            'slug' => Str::slug('UI/UX Design'),
            'icon' => 'uiux.svg',
            'parent_id' => $design->id,
        ]);

        TradeNode::create([
            'name' => 'Graphic Design',
            'slug' => Str::slug('Graphic Design'),
            'icon' => 'graphic.svg',
            'parent_id' => $design->id,
        ]);

        TradeNode::create([
            'name' => 'Digital Marketing',
            'slug' => Str::slug('Digital Marketing'),
            'icon' => 'marketing.svg',
            'parent_id' => $business->id,
        ]);

        // You can use a factory if you prefer more Faker data:
        // TradeNode::factory()->count(10)->create();
    }
}
