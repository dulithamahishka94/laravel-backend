<?php

namespace App\Http\Requests;

use App\Models\ResponseJson;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\JsonResponseTrait;

class CreateForumRequest extends FormRequest
{
    use JsonResponseTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ];
    }
}
