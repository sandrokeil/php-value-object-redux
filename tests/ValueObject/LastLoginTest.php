<?php

/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/php-value-object-redux for the canonical source repository
 * @copyright Copyright (c) 2023 Sandro Keil
 * @license   http://github.com/sandrokeil/php-value-object-redux/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Sake\PhpValueObjectReduxTest\ValueObject;

use PHPUnit\Framework\TestCase;
use Sake\PhpValueObjectRedux\ValueObject\LastLogin;

final class LastLoginTest extends TestCase
{
    public static function provideNative(): \Generator
    {
        yield 'int 1660999050' => [1661006250];
        yield 'string 2022-08-20T14:37:30+00:00' => ['2022-08-20T14:37:30+00:00'];
        yield 'string 2022-08-20T12:37:30-02:00' => ['2022-08-20T12:37:30-02:00'];
        yield 'DatetimeImmutable' => [new \DateTimeImmutable('2022-08-20T12:37:30-02:00')];
        yield 'Lastlogin' => [LastLogin::fromNative('2022-08-20T12:37:30-02:00')];
    }

    /**
     * @dataProvider provideNative
     * @test
     */
    public function it_can_be_created_from_native($value): void
    {
        $cut = LastLogin::fromNative($value);
        $this->assertSame('2022-08-20T14:37:30+00:00', (string) $cut);
        $this->assertSame('2022-08-20T14:37:30+00:00', $cut->toNative());
        $this->assertInstanceOf(\DateTimeImmutable::class, $cut->v);
    }
}
