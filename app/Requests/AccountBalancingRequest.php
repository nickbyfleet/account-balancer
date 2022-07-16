<?php

namespace App\Requests;

use App\Rules\TotalsMustEqualRule;
use Illuminate\Foundation\Http\FormRequest;

class AccountBalancingRequest extends FormRequest
{
    public function rules()
    {
        return [
            'current_state' => 'json',
            'desired_state' => ['json', new TotalsMustEqualRule($this->get('current_state', ''))]
        ];
    }
}
