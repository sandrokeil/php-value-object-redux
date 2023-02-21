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
final class Active implements Immutable
{
    private function __construct(public readonly bool $val)
    {
    }

    public static function fromNative(bool|self $active): static
    {
        return $active instanceof self ? $active : new self($active);
    }

    public function jsonSerialize(): bool
    {
        return $this->val;
    }
}
