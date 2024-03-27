<?php

namespace App\Http\Controllers\Api;

use App\Exports\CourseExport;
use App\Http\Controllers\BaseApiController;
use App\Http\Requests\Export\CourseExportRequest;
use App\Jobs\SendCourseExport;
use App\Models\{Course, User};
use App\Services\FileHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Facades\Excel;

class ExportCourseController extends BaseApiController
{
    /**
     * Handle course export request
     * @param CourseExportRequest $request
     * @return JsonResponse
     */
    public function store(CourseExportRequest $request): JsonResponse
    {
        $list = $request->validated('list');

        $courses = Course::select(['courses.*', 'enroll.enrollment_number', 'enroll.average_progress'])
            ->with(['categories', 'instructor'])
            ->leftJoin(
                DB::raw('(SELECT course_id, certificated_at, COUNT(*) AS enrollment_number, round(AVG(progress), 0) AS average_progress' .
                  ' FROM user_courses GROUP BY course_id) enroll'),
                fn($join) => $join->on('enroll.course_id', '=', 'courses.id')
            );

        // Getting specified courses by ids
        if (!empty($list)) {
            $courses = $courses->whereIn('id', $list);
        }

        // Create destination folder and remove old export files
        $path = $this->handleExport('course-export-*.csv', new CourseExport($courses->get()));

        // Send csv file admin email-boxes
        $admins = User::select('users.*')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->whereIn('roles.slug', ['admin', 'coach'])
            ->get();
        foreach ($admins as $admin) {
            SendCourseExport::dispatch($admin, $path)->afterResponse();
        }

        return response()->json([], 201);
    }

    /**
     * Start export
     *
     * @param string $file_pattern
     * @param FromCollection $collection
     * @return string
     */
    protected function handleExport(string $file_pattern, FromCollection $collection): string
    {
        // Create the export table if id does not exist
        $folder = Str::finish(FileHelper::createFolder(storage_path('exports')), DIRECTORY_SEPARATOR);
        // Remove old export files
        foreach (glob($folder . $file_pattern) as $file) {
            FileHelper::recursiveRemove($file);
        };
        // Split pattern into filename and file extension
        [$filename, $ext] = explode('*', $file_pattern);
        // Full path to export file
        $path = $folder . $filename . now()->format('Y-m-d') . $ext;
        // Create file if not exists
        file_put_contents($path, '');
        // Fill file with data
        Excel::store($collection, $path, null, \Maatwebsite\Excel\Excel::CSV);

        return $path;
    }
}