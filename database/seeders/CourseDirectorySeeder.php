<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseDirectories;
use App\Models\CourseDirectory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseDirectorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // CourseDirectory::truncate();

        $courses = Course::all();

        if ($courses->isEmpty()) {
            $this->call(CourseSeeder::class); // Ensure courses exist
            $courses = Course::all();
        }

        foreach ($courses as $course) {
            // Create root directories for each course
            $introDir = CourseDirectory::create([
                'name' => 'Introduction',
                'course_id' => $course->id,
                'parent_id' => null,
            ]);

            $modulesDir = CourseDirectory::create([
                'name' => 'Modules',
                'course_id' => $course->id,
                'parent_id' => null,
            ]);

            // Create sub-directories within "Modules"
            for ($i = 1; $i <= 3; $i++) {
                CourseDirectory::create([
                    'name' => 'Module ' . $i,
                    'course_id' => $course->id,
                    'parent_id' => $modulesDir->id,
                ]);
            }

            CourseDirectory::create([
                'name' => 'Conclusion',
                'course_id' => $course->id,
                'parent_id' => null,
            ]);
        }
    }
}
