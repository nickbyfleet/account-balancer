<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class TotalsMustEqualRule implements Rule
{
    public function __construct(
        private string $currentState
    )
    {
    }

    /**
     * Will return true if there are any json decoding errors, because it doesn't make sense to display an error
     * if we can't even parse the input.
     *
     * @param $attribute
     * @param $value
     * @return bool|void
     */
    public function passes($attribute, $value)
    {
        $currentState = json_decode($this->currentState, true);

        if (json_last_error() !== JSON_ERROR_NONE) { return true; }

        $desiredState = json_decode($value, true);

        if (json_last_error() !== JSON_ERROR_NONE) { return true; }

        if ($currentState && $desiredState) {
            $currentBalances = array_column($currentState, 'balance');
            $desiredBalances = array_column($desiredState, 'balance');

            return array_sum($currentBalances) === array_sum($desiredBalances);
        }

        return true;
    }

    public function message()
    {
        return 'The total balances from the desired state must equal the total balances from the current state';
    }

}
