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

interface ImmutableRecord extends \IteratorAggregate, Immutable
{
    public static function fromNative(iterable|self $data): static;

    public function toNative(): iterable;

    public function with(iterable|self $data): static;
}
