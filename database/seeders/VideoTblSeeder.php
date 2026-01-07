<?php

namespace Database\Seeders;

use App\Models\StreamContent;
use App\Models\VideoTbl;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VideoTblSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */ 
    public function run(): void
    {
        // VideoTbl::truncate();

        // Get or create stream contents specifically for videos
        $videoStreamContents = StreamContent::where('type', 'video')->get();

        if ($videoStreamContents->isEmpty()) {
            // If no video stream contents exist, create some
            $this->call(StreamContentSeeder::class);
            $videoStreamContents = StreamContent::where('type', 'video')->get();
        }

        foreach ($videoStreamContents as $streamContent) {
            VideoTbl::create([
                'title' => 'Video for ' . $streamContent->id,
                'description' => 'This is a test video content for stream ID ' . $streamContent->id,
                'video_url' => 'https://example.com/videos/' . Str::random(10) . '.mp4',
                'is_public' => rand(0, 1),
                'stream_content_id' => $streamContent->id,
            ]);

            // Update the stream_content to point to this new video_tbl record
            $streamContent->update([
                'target_table' => 'video_tbls',
                'target_id' => VideoTbl::latest()->first()->id, // Get the ID of the just-created video
            ]);
        }
    }
}
