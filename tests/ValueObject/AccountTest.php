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
use Sake\PhpValueObjectRedux\ValueObject\Active;
use Sake\PhpValueObjectRedux\ValueObject\FirstName;
use Sake\PhpValueObjectRedux\ValueObject\LastName;
use Sake\PhpValueObjectRedux\ValueObject\Street;

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
                    $this->assertInstanceOf(FirstName::class, $value);
                    $this->assertSame('Jane', $value->val);

                    break;
                case 'lastName':
                    $this->assertInstanceOf(LastName::class, $value);
                    $this->assertSame('Doe', $value->val);

                    break;
                case 'address':
                    $this->assertInstanceOf(\Generator::class, $value);
                    foreach ($value as $itemKey => $item) {
                        if ($itemKey === 'street') {
                            $this->assertInstanceOf(Street::class, $item);
                            $this->assertSame('Awesome Avenue', $item->val);
                        } else {
                            $this->assertNull($item);
                        }
                    }

                    break;
                case 'active':
                    $this->assertInstanceOf(Active::class, $value);
                    $this->assertFalse($value->val);

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

        $expected = [
            'firstName' => 'Jane',
            'lastName' => 'Doe',
            'age' => null,
            'address' => [
                'street' => 'Awesome Avenue',
                'streetNo' => null,
            ],
            'active' => false,
        ];

        $this->assertSame($expected, $account->jsonSerialize());
        $this->assertSame(\json_encode($expected), \json_encode($account));
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

        $accountCopy = $account->with(['firstName' => 'Doe', 'lastName' => 'Jane']);

        $this->assertSame('Jane', $accountCopy->lastName->val);
        $this->assertSame('Doe', $accountCopy->firstName->val);
        $this->assertNotSame($account->lastName, $accountCopy->lastName);
        $this->assertNotSame($account->firstName, $accountCopy->firstName);
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
    public function it_can_be_created_from_self(): void
    {
        $account = Account::fromNative([
            'firstName' => FirstName::fromNative('Jane'),
            'lastName' => 'Doe',
            'address' => [
                'street' => 'Awesome Avenue',
            ],
            'active' => false,
        ]);

        $accountCopy = Account::fromNative($account);

        $this->assertFalse($accountCopy->active->val);
        $this->assertSame('Awesome Avenue', $accountCopy->address->street->val);
        $this->assertSame($account->active, $accountCopy->active);
        $this->assertSame($account->address, $accountCopy->address);
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
