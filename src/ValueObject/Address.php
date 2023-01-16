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

use Traversable;

/**
 * @psalm-immutable
 */
final class Address implements ImmutableRecord
{
    private function __construct(public readonly Street $street, public readonly ?StreetNo $streetNo = null)
    {
    }

    public static function fromNative(iterable $data): self
    {
        return new self(...self::convertFromNative($data));
    }

    public function with(iterable $data): self
    {
        return self::fromNative([...\get_object_vars($this), ...$data]);
    }

    public function getIterator(): Traversable
    {
        yield 'street' => $this->street->val;
        yield 'streetNo' => $this->streetNo?->val;
    }

    private static function convertFromNative(iterable $data): iterable
    {
        foreach ($data as $field => $value) {
            switch ($field) {
                case 'street':
                    yield $field => Street::fromNative($value);

                    break;
                case 'streetNo':
                case 'street_no':
                    if ($value !== null) {
                        yield 'streetNo' => StreetNo::fromNative($value);
                    }

                    break;
                default:
                    throw new \InvalidArgumentException(\sprintf(
                        'Invalid property passed to Record %s. Got property with key ' . $field,
                        __CLASS__
                    ));
            }
        }
    }

    public function jsonSerialize(): array
    {
        return [
            'street' => $this->street->val,
            'streetNo' => $this->streetNo?->val,
        ];
    }
}
