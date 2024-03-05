<?php

namespace App\GraphQL\Types;

use App\Models\Course;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CourseType extends GraphQLType
{
    /**
     * @var string[] $attributes
     */
    protected $attributes = [
        'name' => 'Course',
        'description' => 'A course',
        'model' => Course::class,
    ];

    public function fields(): array
    {
        return [
            'id' => ['type' => Type::nonNull(Type::int()),],
            'name' => ['type' => Type::nonNull(Type::string())],
            'url' => ['type' => Type::nonNull(Type::string())],
            'description' => ['type' => Type::string()],
            'img_url' => ['type' => GraphQL::type('StringOrListOfStrings')],
            'lang' => ['type' => Type::string()],
            'sale_page_url' => ['type' => Type::string()],
            'expire_url' => ['type' => Type::string()],
            'status' => ['type' => Type::string()],
            'tracking_type' => ['type' => Type::string()],
            'tracking_status' => ['type' => Type::int()],
            'optional_duration' => ['type' => Type::int()],
            'optional_expire_page' => ['type' => Type::string()],
            'category_id' => ['type' => Type::int()],
            'instructor_id' => ['type' => Type::int()],
            'invitation_email' => ['type' => Type::boolean()],
            'position' => ['type' => Type::int()],
            'terms_conditions_enable' => ['type' => Type::boolean()],
            'terms_conditions_text' => ['type' => Type::string()],
            'signature_enable' => ['type' => Type::boolean()],
            'certificate_enable' => ['type' => Type::boolean()],
            'certificate_img_url' => ['type' => GraphQL::type('StringOrListOfStrings')],
            'certificate_coordinates' => [Type::string()],
            'free_trial_enable' => [Type::boolean()],
            'free_trial_upgrade_url' => [Type::string()],
            'free_trial_upgrade_title' => [Type::string()],
            'published_at' => ['type' => Type::string()],
            'created_at' => ['type' => Type::string()],
            'updated_at' => ['type' => Type::string()],
            'category' => [
                'type' => GraphQL::type('Category'),
                'resolve' => fn($course) => $course->category
            ],
            'category_name' => [
                'type' => Type::string(),
            ],
            'instructor' => [
                'type' => GraphQL::type('User'),
                'resolve' => fn($course) => $course->instructor
            ],
            'instructor_email' => [
                'type' => Type::string()
            ],
            'users' => [
                'type' => Type::listOf(GraphQL::type('User')),
                'resolve' => fn($course) => $course->users
            ],
            'users_count' => [
                'type' => Type::int()
            ]
        ];
    }
}