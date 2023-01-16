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
final class Street implements Immutable, \Stringable
{
    private function __construct(public readonly string $val)
    {
    }

    public static function fromNative(string|self $street): self
    {
        return new self(\is_object($street) ? $street->val : $street);
    }

    public function jsonSerialize(): string
    {
        return $this->val;
    }

    public function __toString(): string
    {
        return $this->val;
    }
}
