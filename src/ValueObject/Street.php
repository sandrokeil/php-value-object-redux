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
final readonly class Street implements Immutable, \Stringable
{
    use EqualsTrait;

    private function __construct(public readonly string $v)
    {
    }

    public static function fromNative(string|self $street): self
    {
        return $street instanceof self ? $street : new self($street);
    }

    public function toNative(): string
    {
        return $this->jsonSerialize();
    }

    public function jsonSerialize(): string
    {
        return $this->v;
    }

    public function __toString(): string
    {
        return $this->v;
    }
}
