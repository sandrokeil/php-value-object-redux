<?php

/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/php-value-object-redux for the canonical source repository
 * @copyright Copyright (c) 2023 Sandro Keil
 * @license   http://github.com/sandrokeil/php-value-object-redux/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Sake\PhpValueObjectRedux\ValueObject;

/**
 * @psalm-immutable
 */
final class Age implements Immutable
{
    private function __construct(public readonly int $val)
    {
    }

    public static function fromNative(int|self $age): self
    {
        return $age instanceof self ? $age : new self($age);
    }

    public function jsonSerialize(): int
    {
        return $this->val;
    }
}
