<?php

namespace Testing;

use Laravel\Lumen\Testing\TestCase as BaseTestCase;
use Testing\Concerns\InteractsWithExceptionHandling;

abstract class TestCase extends BaseTestCase
{
    use InteractsWithExceptionHandling;

    protected static $runMigration = false;

    public function setUp(): void
    {
        parent::setUp();

        if (!static::$runMigration)
        {
            $this->artisan('migrate:fresh');
            static::$runMigration = true;
        }
    }

    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../../bootstrap/app.php';
    }
}
