<?php

namespace App\Services;

class AccountBalancingService
{
    public function getMovements(array $currentState, array $desiredState): array
    {
        $movements = [];
        $currentStateKeyedById = $this->getCurrentStateKeyedById($currentState);
        $overflows = $this->getAccountsWithTooMuchMoney($currentStateKeyedById, $desiredState);
        $deficits = $this->getAccountsWithTooLittleMoney($currentStateKeyedById, $desiredState);

        // Sort both arrays from largest to smallest so that we end up with minimum number of transfers
        arsort($deficits);
        arsort($overflows);

        foreach($deficits as $accountId => $deficit) {
            $amountRemaining = $deficit;

            foreach($overflows as $overflowAccountId => $overflow) {
                if ($amountRemaining === 0) {
                    // We've dealt with this deficit completely. Let's move on to the next one
                    continue 2;
                }

                if ($amountRemaining >= $overflow) {
                    // We're going to completely use this overflow account
                    $movements[] = [
                        "from_account_id" => $accountId,
                        "to_account_id" => $overflowAccountId,
                        "amount" => $overflow
                    ];

                    $amountRemaining -= $overflow;

                    continue;
                }

                /**
                 * The overflow is greater than the amount remaining to be fulfilled. We'll take what we need and adjust
                 * the remaining balance in the overflow account.
                 */
                $movements[] = [
                    "from_account_id" => $accountId,
                    "to_account_id" => $overflowAccountId,
                    "amount" => $amountRemaining
                ];

                $overflows[$overflowAccountId] -= $amountRemaining;
            }
        }

        return $movements;
    }

    private function getAccountsWithTooMuchMoney(array $currentStateKeyedById, array $desiredState): array
    {
        $overflows = [];

        // Any accounts that exist in the current state but not in the desired state are impled to have too much money
        $orphanedAccountIds = array_diff(array_keys($currentStateKeyedById), array_column($desiredState, 'account_id'));

        foreach ($orphanedAccountIds as $orphanedAccountId) {
            $overflows[$orphanedAccountId] = $currentStateKeyedById[$orphanedAccountId];
        }

        foreach($desiredState as $item) {
            if (!array_key_exists($item["account_id"], $currentStateKeyedById)) {
                /**
                 * There is an account in the desired state that is not in the current state, therefore the current
                 * state account has too much money by definition
                 */
                continue;
            }

            if ($item["balance"] < $currentStateKeyedById[$item["account_id"]]) {
                $overflows[$item["account_id"]] = $currentStateKeyedById[$item["account_id"]] - $item["balance"];
            }
        }

        return $overflows;
    }

    private function getAccountsWithTooLittleMoney(array $currentStateKeyedById, array $desiredState): array
    {
        $deficits = [];

        foreach($desiredState as $item) {
            if (!array_key_exists($item["account_id"], $currentStateKeyedById)) {
                // There is an account in the desired state that is not in the current state, it is therefore a deficit by definition
                $deficits[$item["account_id"]] = $item["balance"];

                continue;
            }

            if ($item["balance"] > $currentStateKeyedById[$item["account_id"]]) {
                $deficits[$item["account_id"]] = $item["balance"] - $currentStateKeyedById[$item["account_id"]];
            }
        }

        return $deficits;
    }

    private function getCurrentStateKeyedById(array $currentState)
    {
        $keys = array_column($currentState, 'account_id');
        $values = array_column($currentState, 'balance');

        return array_combine($keys, $values);
    }
}
