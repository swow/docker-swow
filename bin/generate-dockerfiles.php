#!/usr/bin/env php
<?php

/**
 * How to use the script:
 *    ./bin/generate-dockerfiles.php [php-major-version]
 *    e.g.,
 *    ./bin/generate-dockerfiles.php 8.0
 * Above command will have PHP 8.0 dockerfiles created under folder images/dockerfiles/.
 */

declare(strict_types=1);

use Swow\Docker\Dockerfile;

require_once dirname(__DIR__) . '/vendor/autoload.php';

if (!empty($argv[1])) {
    (new Dockerfile($argv[1]))->render();
} else {
    echo "Usage:\n";
    echo "    php " . __FILE__ . " php-major-version\n";
    echo "e.g., \n";
    echo "    php " . __FILE__ . " 8.0\n\n";
}
