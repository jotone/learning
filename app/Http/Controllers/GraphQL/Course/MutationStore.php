<?php

namespace App\Http\Controllers\GraphQL\Course;

use App\Models\Settings;
use App\Enums\{CourseStatus, CourseTracking};
use App\Models\Course;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\Type;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class MutationStore extends Mutation
{
    /**
     * @var array
     */
    protected $attributes = [
        'name' => 'create'
    ];

    /**
     * @return Type
     */
    public function type(): Type
    {
        return GraphQL::type('Course');
    }

    /**
     * @return array
     */
    public function args(): array
    {
        return [
            'name' => [
                'name' => 'name',
                'type' => Type::nonNull(Type::string()),
                'rules' => ['required', 'string']
            ],
            'url' => [
                'name' => 'url',
                'type' => Type::string(),
                'rules' => ['nullable', 'string']
            ],
            'description' => [
                'name' => 'description',
                'type' => Type::string(),
                'rules' => ['nullable', 'string']
            ],
            'img_url' => [
                'name' => 'img_url',
                'type' => GraphQL::type('Upload'),
                'rules' => ['nullable', 'image']
            ],
            'lang' => [
                'name' => 'lang',
                'type' => Type::string(),
                'rules' => ['nullable', 'string']
            ],
            'sale_page_url' => [
                'name' => 'sale_page_url',
                'type' => Type::string(),
                'rules' => ['nullable', 'string']
            ],
            'expire_url' => [
                'name' => 'expire_url',
                'type' => Type::string(),
                'rules' => ['nullable', 'string']
            ],
            'status' => [
                'name' => 'status',
                'type' => Type::string(),
                'rules' => ['nullable', 'string', Rule::in(CourseStatus::forSelect())]
            ],
            'tracking_type' => [
                'name' => 'tracking_type',
                'type' => Type::string(),
                'rules' => ['nullable', 'string', Rule::in(CourseTracking::forSelect())]
            ],
            'tracking_status' => [
                'name' => 'tracking_status',
                'type' => Type::int(),
                'rules' => ['nullable', 'numeric']
            ],
            'optional_duration' => [
                'name' => 'tracking_status',
                'type' => Type::int(),
                'rules' => ['nullable', 'numeric']
            ],
            'optional_expire_page' => [
                'name' => 'tracking_type',
                'type' => Type::string(),
                'rules' => ['nullable', 'string']
            ],
            'category_id' => [
                'name' => 'category_id',
                'type' => Type::int(),
                'rules' => ['nullable', 'exists:categories,id']
            ],
            'instructor_id' => [
                'name' => 'instructor_id',
                'type' => Type::int(),
                'rules' => ['nullable', 'exists:users,id']
            ],
            'invitation_email' => [
                'name' => 'invitation_email',
                'type' => Type::int(),
                'rules' => ['nullable', 'numeric']
            ],
            'position' => [
                'name' => 'position',
                'type' => Type::int(),
                'rules' => ['nullable', 'numeric']
            ],
            'terms_conditions_enable' => [
                'name' => 'terms_conditions_enable',
                'type' => Type::int(),
                'rules' => ['nullable', 'numeric']
            ],
            'terms_conditions_text' => [
                'name' => 'terms_conditions_text',
                'type' => Type::string(),
                'rules' => ['nullable', 'string']
            ],
            'signature_enable' => [
                'name' => 'signature_enable',
                'type' => Type::int(),
                'rules' => ['nullable', 'numeric']
            ],
            'free_trial_enable' => [
                'name' => 'free_trial_enable',
                'type' => Type::int(),
                'rules' => ['nullable', 'numeric']
            ],
            'free_trial_upgrade_url' => [
                'name' => 'free_trial_upgrade_url',
                'type' => Type::string(),
                'rules' => ['nullable', 'string']
            ],
            'free_trial_upgrade_title' => [
                'name' => 'free_trial_upgrade_title',
                'type' => Type::string(),
                'rules' => ['nullable', 'string']
            ]
        ];
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
        $input['url'] = generateUrl(empty($input['url']) ? $input['name'] : $input['url']);
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
                'name' => $input['name'],
                'url' => $input['url'],
                'description' => $input['description'] ?? null,
                'img_url' => $input['img_url'] ?? null,
                'lang' => $input['lang'] ?? Settings::where('key', 'main_language')->value('value'),
                'sale_page_url' => $input['sale_page_url'] ?? null,
                'expire_url' => $input['expire_url'] ?? null,
                'status' => $status,
                'tracking_type' => $input['tracking_type'] ?? 'enable_auto_approve',
                'tracking_status' => $input['tracking_status'] ?? 0,
                'optional_duration' => $input['optional_duration'] ?? null,
                'optional_expire_page' => $input['optional_expire_page'] ?? null,
                'category_id' => $input['category_id'] ?? null,
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

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return new Error($e->getMessage());
        }
        return $course;
    }
}