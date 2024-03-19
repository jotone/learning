<?php

namespace App\Http\Controllers\GraphQL\Course;

use App\Services\Str;
use App\Models\{Course, Settings};
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\DB;

class MutationStore extends CourseMutation
{
    /**
     * @var array
     */
    protected $attributes = [
        'name' => 'create'
    ];

    /**
     * @return array
     */
    public function args(): array
    {
        return array_merge([
            'name' => [
                'name' => 'name',
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required', 'string']
            ]
        ], $this->mutationDefaultArgs());
    }

    /**
     * Store course
     *
     * @param $root
     * @param array $input
     * @return Course|Error
     */
    public function resolve($root, array $input): Course|Error
    {
        DB::beginTransaction();

        // Reset courses positions
        foreach (Course::orderBy('position')->get() as $i => $course) {
            $course->update(['position' => $i]);
        }
        // Form the course url value
        $input['url'] = Str::generateUrl(empty($input['url']) ? $input['name'] : $input['url']);
        // Check if a such url already exists and modify it
        if (Course::where('url', $input['url'])->count()) {
            $input['url'] .= '-' . uniqid();
        }
        // Set current course position
        if (!isset($input['position'])) {
            $input['position'] = Course::count();
        }

        $status = $input['status'] ?? 'draft';
        try {
            // Create course
            $course = Course::create([
                'name' => mb_substr($input['name'], 0, 90),
                'url' => $input['url'],
                'description' => empty($input['description']) ? null : mb_substr($input['description'], 0, 300),
                'img_url' => $input['img_url'] ?? null,
                'lang' => $input['lang'] ?? Settings::where('key', 'main_language')->value('value'),
                'sale_page_url' => $input['sale_page_url'] ?? null,
                'expire_url' => $input['expire_url'] ?? null,
                'status' => $status,
                'tracking_type' => $input['tracking_type'] ?? 'enable_auto_approve',
                'tracking_status' => $input['tracking_status'] ?? 0,
                'optional_duration' => $input['optional_duration'] ?? null,
                'optional_expire_page' => $input['optional_expire_page'] ?? null,
                'instructor_id' => $input['instructor_id'] ?? null,
                'invitation_email' => $input['invitation_email'] ?? 1,
                'position' => $input['position'],
                'published_at' => 'active' === $status ? now() : null,
                'terms_conditions_enable' => $input['terms_conditions_enable'] ?? false,
                'terms_conditions_text' => $input['terms_conditions_text'] ?? null,
                'signature_enable' => $input['signature_enable'] ?? false,
                'free_trial_enable' => $input['free_trial_enable'] ?? false,
                'free_trial_upgrade_url' => $input['free_trial_upgrade_url'] ?? null,
                'free_trial_upgrade_title' => $input['free_trial_upgrade_title'] ?? null
            ]);
            // Set course categories
            isset($input['categories']) && $this->setCategories($course, $input['categories']);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return new Error($e->getMessage());
        }
        return $course;
    }
}