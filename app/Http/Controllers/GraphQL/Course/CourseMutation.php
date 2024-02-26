<?php

namespace App\Http\Controllers\GraphQL\Course;

use App\Enums\{CourseStatus, CourseTracking};
use GraphQL\Type\Definition\Type;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Mutation;

class CourseMutation extends Mutation
{
    /**
     * Default mutation request fields
     *
     * @return array[]
     */
    protected function mutationDefaultArgs(): array
    {
        return [
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
            ],
            'published_at' => [
                'name' => 'published_at',
                'type' => Type::string(),
                'rules' => ['nullable', 'string']
            ],
            'certificate_enable' => [
                'name' => 'certificate_enable',
                'type' => Type::int(),
                'rules' => ['nullable', 'numeric']
            ],
            'certificate_img_url' => [
                'name' => 'certificate_img_url',
                'type' => GraphQL::type('Upload'),
                'rules' => ['nullable', 'image']
            ],
            'certificate_coordinates' => [
                'name' => 'certificate_coordinates',
                'type' => Type::string(),
                'rules' => ['nullable', 'string']
            ]
        ];
    }

    /**
     * @return Type
     */
    public function type(): Type
    {
        return GraphQL::type('Course');
    }
}