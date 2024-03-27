<?php

namespace Feature;

use App\Models\{Course, Role, User};
use Tests\ApiTestCase;

class CourseExportApiTest extends ApiTestCase
{
    protected static string $route = 'api.export.';

    protected  string $path = '';

    protected function setUp(): void
    {
        parent::setUp();

        // Retrieve and delete users with 'admin' or 'coach' roles
        $roles = Role::whereIn('slug', ['admin', 'coach'])->pluck('id')->toArray();
        User::where('role_id', $roles)->delete();

        // Construct the file path where the course export is expected to be saved.
        $this->path = storage_path('exports/course-export-' . date('Y-m-d') . '.csv');
    }

    /**
     * Test the export of all courses.
     * @return void
     */
    public function testAllCourses(): void
    {
        // Act as a superuser and make a request to create a course export list
        $this->actingAs($this->actor)
            ->postJson(route(self::$route . 'course'))
            ->assertCreated();
        // Assert that the export file has been created.
        $this->assertFileExists($this->path);
        // Assert that the IDs from the export file match exactly with the course IDs
        $this->assertEmpty(array_diff($this->getIDsFromCSV(), Course::all()->pluck('id')->toArray()));
    }

    /**
     * Test the export of several random courses.
     * @return void
     */
    public function testRandomCourses(): void
    {
        $courses = Course::inRandomOrder()->take(mt_rand(2, 3))->get()->pluck('id')->toArray();
        // Act as a superuser and make a request to create a course export list
        $this->actingAs($this->actor)
            ->postJson(route(self::$route . 'course'), [
                'list' => $courses
            ])
            ->assertCreated();
        // Assert that the export file has been created.
        $this->assertFileExists($this->path);
        // Assert that the IDs from the export file match exactly with the course IDs
        $this->assertEmpty(array_diff($this->getIDsFromCSV(), $courses));
    }

    /**
     * Get a list of IDs from the course export file
     * @return array
     */
    protected function getIDsFromCSV(): array
    {
        $file_handle = fopen($this->path, 'r');
        $course_ids = [];
        // Read through the CSV, skipping the header row and collect course IDs.
        while ($csv_row = fgetcsv($file_handle, null, ';')) {
            $row_data = explode(',', $csv_row[0]);
            if (is_numeric($row_data[0])) {
                // Extract the first element of the CSV row, assuming it's the course ID.
                $course_ids[] = (int)$row_data[0];
            }
        }
        fclose($file_handle);

        return $course_ids;
    }
}