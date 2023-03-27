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

use DateTimeImmutable;

/**
 * @psalm-immutable
 */
final readonly class LastLogin implements \Stringable, Immutable
{
    use EqualsTrait;

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
