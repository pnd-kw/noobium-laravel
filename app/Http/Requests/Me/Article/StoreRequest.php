<?php

namespace App\Http\Requests\Me\Article;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\ErrorResponseJson;
use Illuminate\Validation\Rule;
use App\Models\Category;

class StoreRequest extends FormRequest
{
    use ErrorResponseJson;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => 'required|' . Rule::in(Category::pluck('id')),
            'title' => 'required|string|max:190',
            'content' => 'required|string',
            'featured_image' => 'required|image|mimes:jpg,jpeg,bmp,png',
        ];
    }
}
