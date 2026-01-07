<?php

namespace Database\Seeders;

use App\Models\PdfTbl;
use App\Models\StreamContent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PdfTblSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // PdfTbl::truncate();

        $pdfStreamContents = StreamContent::where('type', 'pdf')->get();

        if ($pdfStreamContents->isEmpty()) {
            $this->call(StreamContentSeeder::class);
            $pdfStreamContents = StreamContent::where('type', 'pdf')->get();
        }

        foreach ($pdfStreamContents as $streamContent) {
            PdfTbl::create([
                'en_path' => 'pdfs/en/' . Str::random(10) . '.pdf',
                'hi_path' => 'pdfs/hi/' . Str::random(10) . '.pdf',
                'u_path' => 'pdfs/universal/' . Str::random(10) . '.pdf',
                'is_public' => rand(0, 1),
                'is_converted' => true, // Assuming PDFs are always "converted" for web
                'stream_content_id' => $streamContent->id,
            ]);
            $streamContent->update([
                'target_table' => 'pdf_tbls',
                'target_id' => PdfTbl::latest()->first()->id,
            ]);
        }
    }
}
