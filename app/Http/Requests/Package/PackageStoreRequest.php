<?php

namespace App\Http\Requests\Package;

use Illuminate\Foundation\Http\FormRequest;

class PackageStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $rules =  [
            'title' => 'required|max:255',
            'term' => 'required',
            'price' => 'required',
            'status' => 'required',
            'product_limit' => 'required',
            'categories_limit' => 'required',
            'subcategories_limit' => 'required',
            'order_limit' => 'required',
            'language_limit' => 'required',
            'trial_days' => 'required_if:is_trial,1'
        ];

        $features = $this->features;
        if (!is_null($features)) {
            if (in_array('Blog', $features)) {
                $rules['post_limit'] = 'required';
            }
            if (in_array('Custom Page', $features)) {
                $rules['number_of_custom_page'] = 'required';
            }
        }
        return $rules;
    }
    public function messages(): array
    {
        return [
            'trial_days.required_if' => 'Trial days is required'
        ];
    }
}
