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
final class LastLogin implements \Stringable, Immutable
{
    private const OUTPUT_FORMAT = 'Y-m-d\TH:i:sP';

    public static function fromNative(string|int|DateTimeImmutable|self $val): self
    {
        switch (\gettype($val)) {
            case 'integer':
                $datetime = (new DateTimeImmutable())->setTimestamp($val);

                break;
            case 'string':
                $datetime = new DateTimeImmutable($val);

                break;
            default:
                if ($val instanceof self) {
                    return $val;
                }
                $datetime = $val;

                break;
        }

        return new self(self::ensureUtc($datetime));
    }

    private function __construct(public readonly DateTimeImmutable $val)
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
        return $this->val->format(self::OUTPUT_FORMAT);
    }
}
