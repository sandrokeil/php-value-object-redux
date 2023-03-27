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
final readonly class Address implements ImmutableRecord
{
    use EqualsTrait;

    private function __construct(public readonly Street $street, public readonly ?StreetNo $streetNo = null)
    {
    }

    public static function fromNative(iterable|self $data): static
    {
        return $data instanceof self ? $data : new self(...self::convertFromNative($data));
    }

    public function toNative(): iterable
    {
        return $this->jsonSerialize();
    }

    public function with(iterable $data): static
    {
        return self::fromNative([...\get_object_vars($this), ...$data]);
    }

    public function getIterator(): Traversable
    {
        yield 'street' => $this->street;
        yield 'streetNo' => $this->streetNo;
    }

    private static function convertFromNative(iterable $data): iterable
    {
        foreach ($data as $field => $value) {
            // order independent
            switch ($field) {
                case 'streetNo':
                case 'street_no':
                    if ($value !== null) {
                        yield 'streetNo' => StreetNo::fromNative($value);
                    }

                    break;
                case 'street':
                    yield $field => Street::fromNative($value);

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
            'street' => $this->street->v,
            'streetNo' => $this->streetNo?->v,
        ];
    }
}
