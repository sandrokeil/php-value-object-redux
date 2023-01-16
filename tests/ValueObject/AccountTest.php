<?php

/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/php-value-object-redux for the canonical source repository
 * @copyright Copyright (c) 2023 Sandro Keil
 * @license   http://github.com/sandrokeil/php-value-object-redux/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Sake\PhpValueObjectReduxTest;

use PHPUnit\Framework\TestCase;
use Sake\PhpValueObjectRedux\ValueObject\Account;
use Sake\PhpValueObjectRedux\ValueObject\FirstName;

final class AccountTest extends TestCase
{
    /**
     * @test
     */
    public function it_supports_snake_case(): void
    {
        $account = Account::fromNative([
            'firstName' => FirstName::fromNative('Jane'),
            'last_name' => 'Doe',
            'address' => [
                'street' => 'Awesome Avenue',
            ],
            'active' => false,
        ]);

        $this->assertSame('Doe', $account->lastName->val);
    }

    /**
     * @test
     */
    public function it_is_iterable(): void
    {
        $account = Account::fromNative([
            'firstName' => FirstName::fromNative('Jane'),
            'last_name' => 'Doe',
            'address' => [
                'street' => 'Awesome Avenue',
            ],
            'active' => false,
        ]);

        foreach ($account as $key => $value) {
            switch ($key) {
                case 'firstName':
                    $this->assertSame('Jane', $value);

                    break;
                case 'lastName':
                    $this->assertSame('Doe', $value);

                    break;
                case 'address':
                    foreach ($value as $itemKey => $item) {
                        if ($itemKey === 'street') {
                            $this->assertSame('Awesome Avenue', $item);
                        } else {
                            $this->assertNull($item);
                        }
                    }

                    break;
                case 'active':
                    $this->assertFalse($value);

                    break;
                case 'age':
                    $this->assertNull($value);

                    break;
                default:
                    $this->assertFalse(true);

                    break;
            }
        }
    }

    /**
     * @test
     */
    public function it_is_json_serializable(): void
    {
        $account = Account::fromNative([
            'firstName' => FirstName::fromNative('Jane'),
            'last_name' => 'Doe',
            'address' => [
                'street' => 'Awesome Avenue',
            ],
            'active' => false,
        ]);

        $this->assertSame(
            [
                'firstName' => 'Jane',
                'lastName' => 'Doe',
                'age' => null,
                'address' => [
                    'street' => 'Awesome Avenue',
                    'streetNo' => null,
                ],
                'active' => false,
            ],
            $account->jsonSerialize()
        );
    }

    /**
     * @test
     */
    public function it_supports_with(): void
    {
        $account = Account::fromNative([
            'firstName' => FirstName::fromNative('Jane'),
            'last_name' => 'Doe',
            'address' => [
                'street' => 'Awesome Avenue',
            ],
            'active' => false,
        ]);

        $this->assertSame('Jane', $account->firstName->val);
        $this->assertSame('Doe', $account->lastName->val);

        $account = $account->with(['firstName' => 'Doe', 'lastName' => 'Jane']);

        $this->assertSame('Jane', $account->lastName->val);
        $this->assertSame('Doe', $account->firstName->val);
    }

    /**
     * @test
     */
    public function it_can_be_created_from_native(): void
    {
        $account = Account::fromNative([
            'firstName' => FirstName::fromNative('Jane'),
            'lastName' => 'Doe',
            'address' => [
                'street' => 'Awesome Avenue',
            ],
            'active' => false,
        ]);

        $this->assertSame(false, $account->active->val);
        $this->assertSame('Awesome Avenue', $account->address->street->val);
    }

    /**
     * @test
     */
    public function it_can_init_not_set_properties(): void
    {
        $account = Account::fromNative([
            'firstName' => FirstName::fromNative('Jane'),
            'lastName' => 'Doe',
            'address' => [
                'street' => 'Awesome Avenue',
            ],
        ]);

        $this->assertSame(true, $account->active->val);
    }
}
