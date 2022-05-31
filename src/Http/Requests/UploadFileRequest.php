<?php

namespace Kwaadpepper\CrudPolicies\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadFileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return boolean
     */
    public function authorize()
    {
        // * $this->ajax wont validate form direct file upload.
        return $this->wantsJson() and $this->expectsJson();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'upload' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ];
    }
}
