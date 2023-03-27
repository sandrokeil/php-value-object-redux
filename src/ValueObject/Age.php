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
final readonly class Age implements Immutable
{
    use EqualsTrait;

    private function __construct(public readonly int $v)
    {
    }

    public static function fromNative(int|self $age): self
    {
        return $age instanceof self ? $age : new self($age);
    }

    public function toNative(): int
    {
        return $this->jsonSerialize();
    }

    public function jsonSerialize(): int
    {
        return $this->v;
    }
}
