<?php

namespace Tests\Unit\Services;

use App\Services\AccountBalancingService;
use PHPUnit\Framework\TestCase;

class AccountBalancingServiceTest extends TestCase
{
    /**
     * @dataProvider stateDataProvider
     */
    public function testGetMovements($currentState, $desiredState, $expectedMovement)
    {
        $accountBalancingService = app(AccountBalancingService::class);
        $actualMovements = $accountBalancingService->getMovements($currentState, $desiredState);
        $this->assertArraySimilar($expectedMovement, $actualMovements);
    }

    public function stateDataProvider()
    {
        return [
            'Empty Arrays' => [[], [], []],
            'Basic Example' => [
                [
                    [
                        "account_id" => 1,
                        "balance" => 500000
                    ],
                    [
                        "account_id" => 2,
                        "balance" => 200000
                    ],
                    [
                        "account_id" => 3,
                        "balance" => 300000
                    ]
                ],
                [
                    [
                        "account_id" => 1,
                        "balance" => 900000
                    ],
                    [
                        "account_id" => 2,
                        "balance" => 30000
                    ],
                    [
                        "account_id" => 3,
                        "balance" => 70000
                    ]
                ],
                [
                    [
                        "from_account_id" => 3,
                        "to_account_id" => 1,
                        "amount" => 230000
                    ],
                    [
                        "from_account_id" => 2,
                        "to_account_id" => 1,
                        "amount" => 170000
                    ]
                ]
            ],
            'One Account to Two Accounts' => [
                [
                    [
                        "account_id" => 1,
                        "balance" => 500000
                    ]
                ],
                [
                    [
                        "account_id" => 1,
                        "balance" => 250000
                    ],
                    [
                        "account_id" => 2,
                        "balance" => 250000
                    ]
                ],
                [
                    [
                        "from_account_id" => 1,
                        "to_account_id" => 2,
                        "amount" => 250000
                    ]
                ]
            ],
            'One Account to Two Other Accounts' => [
                [
                    [
                        "account_id" => 1,
                        "balance" => 500000
                    ]
                ],
                [
                    [
                        "account_id" => 2,
                        "balance" => 250000
                    ],
                    [
                        "account_id" => 3,
                        "balance" => 250000
                    ]
                ],
                [
                    [
                        "from_account_id" => 1,
                        "to_account_id" => 2,
                        "amount" => 250000
                    ],
                    [
                        "from_account_id" => 1,
                        "to_account_id" => 3,
                        "amount" => 250000
                    ]
                ]
            ],
            'Two Accounts to One' => [
                [
                    [
                        "account_id" => 45,
                        "balance" => 500000
                    ],
                    [
                        "account_id" => 34,
                        "balance" => 250000
                    ]
                ],
                [
                    [
                        "account_id" => 4,
                        "balance" => 750000
                    ]
                ],
                [
                    [
                        "from_account_id" => 45,
                        "to_account_id" => 4,
                        "amount" => 500000
                    ],
                    [
                        "from_account_id" => 34,
                        "to_account_id" => 4,
                        "amount" => 250000
                    ]
                ]
            ]
        ];
    }

    protected function assertArraySimilar(array $expected, array $array)
    {
        $this->assertTrue(count(array_diff_key($array, $expected)) === 0);

        foreach ($expected as $key => $value) {
            if (is_array($value)) {
                $this->assertArraySimilar($value, $array[$key]);
            } else {
                $this->assertContains($value, $array);
            }
        }
    }
}
