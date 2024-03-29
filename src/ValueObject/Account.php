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
final class Account implements ImmutableRecord
{
    use EqualsTrait;

    private static array $__objectKeys;

    private function __construct(
        public readonly FirstName $firstName,
        public readonly LastName $lastName,
        public readonly Address $address,
        public readonly Active $active,
        public readonly ?Age $age = null
    ) {
    }

    /**
     * @param iterable{firstName : string, lastName : string, age : int|null, address : array} $data
     */
    public static function fromNative(iterable|self $data): static
    {
        return $data instanceof self ? $data : new self(...self::convertFromNative($data));
    }

    public function toNative(): array
    {
        return $this->jsonSerialize();
    }

    public function with(iterable|self $data): static
    {
        return new self(...self::convertFromNative([...\get_object_vars($this), ...$data]));
    }

    public function getIterator(): Traversable
    {
        yield 'firstName' => $this->firstName;
        yield 'lastName' => $this->lastName;
        yield 'age' => $this->age;
        yield 'address' => $this->address->getIterator();
        yield 'active' => $this->active;
    }

    public function jsonSerialize(): array
    {
        return [
            'firstName' => $this->firstName->v,
            'lastName' => $this->lastName->v,
            'age' => $this->age?->v,
            'address' => $this->address->jsonSerialize(),
            'active' => $this->active->v,
        ];
    }

    private static function convertFromNative(iterable $data): iterable
    {
        if (! isset(self::$__objectKeys)) {
            self::$__objectKeys = \array_keys(\get_class_vars(self::class));
        }

        $initialized = [];

        foreach ($data as $field => $value) {
            // order independent
            switch ($field) {
                case 'lastName':
                case 'last_name':
                    yield 'lastName' => LastName::fromNative($value);

                    break;
                case 'firstName': // camelCase
                case 'first_name': // snake_case
                    yield 'firstName' => FirstName::fromNative($value);

                    break;
                case 'address': // deep nesting
                    yield $field => Address::fromNative($value);

                    break;
                case 'age': // can be null
                    if ($value !== null) {
                        yield $field => Age::fromNative($value);
                    }

                    break;
                case 'active':
                    yield $field => Active::fromNative($value);

                    break;
                default:
                    throw new \InvalidArgumentException(\sprintf(
                        'Invalid property passed to Record %s. Got property with key ' . $field,
                        __CLASS__
                    ));
            }
            $initialized[] = $field;
        }

        yield from self::init(\array_diff(self::$__objectKeys, $initialized));
    }

    private static function init(array $properties): \Generator
    {
        foreach ($properties as $property) {
            switch ($property) {
                case 'active':
                    yield $property => Active::fromNative(true);

                    break;
                default:
                    // intentionally omitted
                    break;
            }
        }
    }
}
