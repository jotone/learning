<?php

namespace App\Exports;

use App\Models\Settings;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class CourseExport implements FromCollection
{
    protected string $time_format = 'j/M/Y H:i';

    public function __construct(protected Collection $collection)
    {
    }

    /**
     * Transform File data into proper view
     *
     * @return Collection
     * @throws \Exception
     */
    public function collection(): Collection
    {
        /**
         * TODO:
         * Add fields
         *   - Rating
         *   - Feedback Count
         *   - Featured
         *   - Course Level
         *   - Time Commitment
         *   - Difficulty level
         */
        // File heading
        $result = [
            [
                'id' => '#ID',
                'name' => 'Name',
                'categories' => 'Categories',
                'status' => 'Status',
                'certificate' => 'Certificate',
                'users_num' => 'Users number',
                'avg_progress' => 'Average progress',
                'language' => 'Language',
                'instructor' => 'Instructor',
                'duration' => 'Course Duration',
                'published_at' => 'Newly released',
                'created_at' => 'Creation Date',
            ]
        ];

        // Available course statuses
        $statuses = config('enums.course.statuses');

        // The result collection content
        foreach ($this->collection as $course) {
            $result[] = [
                'id' => $course->id,
                'name' => $course->name,
                'categories' => implode("\n", $course->categories()->pluck('name')->toArray()),
                'status' => $course->status,
                'certificate' => $course->certificate_enable ? '+' : '',
                'users_num' => $course->enrollment_number,
                'avg_progress' => $course->average_progress,
                'language' => $course->lang,
                'instructor' => $course->instructor?->email,
                'duration' => $course->optional_duration,
                'published_at' => $course->published_at ? $course->published_at->format($this->time_format) : '',
                'created_at' => $course->created_at->format($this->time_format . ' H:i')
            ];
        }

        return collect($result);
    }
}