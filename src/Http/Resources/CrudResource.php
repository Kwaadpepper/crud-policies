<?php

namespace Kwaadpepper\CrudPolicies\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CrudResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundInExtendedClass
    public function toArray($request)
    {
        return [
            'label' => $this->crudLabel,
            'value' => $this->crudValue
        ];
    }
}
