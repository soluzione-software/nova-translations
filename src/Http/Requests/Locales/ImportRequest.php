<?php

namespace SoluzioneSoftware\NovaTranslations\Http\Requests\Locales;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;

class ImportRequest extends FormRequest
{
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
            'file' => 'required|file|mimetypes:text/plain,text/csv',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'file' => Lang::get('nova-translations::validation.attributes.file'),
        ];
    }
}
