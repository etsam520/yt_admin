<?php

namespace Database\Seeders;

use App\Models\PptTbl;
use App\Models\StreamContent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PptTblSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // PptTbl::truncate();

        $pptStreamContents = StreamContent::where('type', 'ppt')->get();

        if ($pptStreamContents->isEmpty()) {
            $this->call(StreamContentSeeder::class);
            $pptStreamContents = StreamContent::where('type', 'ppt')->get();
        }

        foreach ($pptStreamContents as $streamContent) {
            PptTbl::create([
                'en_path' => 'ppts/en/' . Str::random(10) . '.ppt',
                'hi_path' => 'ppts/hi/' . Str::random(10) . '.ppt',
                'u_path' => 'ppts/universal/' . Str::random(10) . '.ppt',
                'is_public' => rand(0, 1),
                'is_converted' => true,
                'stream_content_id' => $streamContent->id,
            ]);
            $streamContent->update([
                'target_table' => 'ppt_tbls',
                'target_id' => PptTbl::latest()->first()->id,
            ]);
        }
    }
}
