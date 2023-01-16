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
     * @return static
     */
    public static function fromNative(iterable $data): self
    {
        return new self(...self::convertFromNative($data));
    }

    public function with(iterable $data): self
    {
        return new self(...self::convertFromNative([...\get_object_vars($this), ...$data]));
    }

    public function getIterator(): Traversable
    {
        yield 'firstName' => $this->firstName->val;
        yield 'age' => $this->age?->val;
        yield 'address' => $this->address->getIterator();
    }

    public function jsonSerialize(): array
    {
        return [
            'firstName' => $this->firstName->val,
            'lastName' => $this->lastName->val,
            'age' => $this->age?->val,
            'address' => $this->address->jsonSerialize(),
            'active' => $this->active->val,
        ];
    }

    private static function convertFromNative(iterable $data): iterable
    {
        if (! isset(self::$__objectKeys)) {
            self::$__objectKeys = \array_keys(\get_class_vars(self::class));
        }

        $initialized = [];

        foreach ($data as $field => $value) {
            switch ($field) {
                case 'firstName': // camelCase
                case 'first_name': // snakeCase
                    yield 'firstName' => FirstName::fromNative($value);

                    break;
                case 'lastName':
                case 'last_name':
                    yield 'lastName' => LastName::fromNative($value);

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
