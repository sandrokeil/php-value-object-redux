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
final class StreetNo implements \Stringable, Immutable
{
    private function __construct(public readonly string $val)
    {
    }

    public static function fromNative(string|self $streetNo): self
    {
        return \is_object($streetNo) ? $streetNo : new self($streetNo);
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
