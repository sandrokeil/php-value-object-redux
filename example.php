<?php

/**
 * Sandro Keil (https://sandro-keil.de)
 *
 * @link      http://github.com/sandrokeil/php-value-object-redux for the canonical source repository
 * @copyright Copyright (c) 2023 Sandro Keil
 * @license   http://github.com/sandrokeil/php-value-object-redux/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

use Sake\PhpValueObjectRedux\ValueObject\Account;
use Sake\PhpValueObjectRedux\ValueObject\FirstName;

require_once 'vendor/autoload.php';

$account = Account::fromNative([
    'firstName' => FirstName::fromNative('Jane'),
    'last_name' => 'Doe',
    'address' => [
        'street' => 'Awesome Avenue',
    ],
    'active' => false,
]);

$lastName = $account->lastName;

\var_dump($lastName);
\var_dump($lastName->v);
\var_dump((string) $lastName);
\var_dump(\json_encode($lastName));

foreach ($account as $key => $value) {
    echo $key . ': ' . ($value ? \get_class($value) : \var_export($value, true)) . PHP_EOL;
}

echo 'JSON:' . PHP_EOL . \json_encode($account) . PHP_EOL;
