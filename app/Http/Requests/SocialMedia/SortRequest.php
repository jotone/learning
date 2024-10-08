<?php

namespace App\Http\Requests\SocialMedia;

use App\Http\Requests\DefaultRequest;

class SortRequest extends DefaultRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return $this->sortRequestRules('social_media');
    }
}