# PHP ^8.1 Value Objects Redux

Opinionated PHP immutable value object example with deep nesting, `\JsonSerializable`, `\IteratorAggregate`, `snake_case` and `camelCase`. Please see `example-php`, source and tests for more details.

- simple and short immutable value objects
- plain value objects have 4 methods `fromNative(...)`, `toNative()`, `equals(...)` and `jsonSerialize()`
- records have additional 2 methods `with(iterable|self $data)` and `getIterator()` 
- records are traversable, you can iterate over the properties
- union types for creating value objects from scalar or same value object type
- `init()` method example to initialize optional properties with default values
- `snake_case` and `camelCase` keys


**Example for a String value object:**

```php
final readonly class FirstName implements Immutable, \Stringable
{
    use FuncTrait;

    private function __construct(public readonly string $v)
    {
    }

    public static function fromNative(string|self $firstName): self
    {
        return $firstName instanceof self ? $firstName : new self($firstName);
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
```

**Example for a date value object:**

```php
final readonly class LastLogin implements \Stringable, Immutable
{
    use FuncTrait;

    private const OUTPUT_FORMAT = 'Y-m-d\TH:i:sP';

    public static function fromNative(string|int|DateTimeImmutable|self $v): self
    {
        switch (\gettype($v)) {
            case 'integer':
                $datetime = (new DateTimeImmutable())->setTimestamp($v);

                break;
            case 'string':
                $datetime = new DateTimeImmutable($v);

                break;
            default:
                if ($v instanceof self) {
                    return $v;
                }
                $datetime = $v;

                break;
        }

        return new self(self::ensureUtc($datetime));
    }

    public function toNative(): string
    {
        return $this->jsonSerialize();
    }

    private function __construct(public readonly DateTimeImmutable $v)
    {
    }

    private static function ensureUtc(DateTimeImmutable $timestamp): DateTimeImmutable
    {
        if ($timestamp->getTimezone()->getName() !== 'UTC') {
            $timestamp = $timestamp->setTimezone(new \DateTimeZone('UTC'));
        }

        return $timestamp;
    }

    public function __toString(): string
    {
        return $this->jsonSerialize();
    }

    public function jsonSerialize(): string
    {
        return $this->v->format(self::OUTPUT_FORMAT);
    }
}
```

**Example for a record value object:**

```php
final class Account implements ImmutableRecord
{
    use FuncTrait;

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
                case 'first_name': // snakeCase
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
```
