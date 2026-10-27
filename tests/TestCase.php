<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        if (is_file(dirname(__DIR__).'/bootstrap/cache/config.php')) {
            throw new \RuntimeException(
                'Tests require an uncached configuration. Run "php artisan config:clear" before running PHPUnit.'
            );
        }

        return parent::createApplication();
    }
}
