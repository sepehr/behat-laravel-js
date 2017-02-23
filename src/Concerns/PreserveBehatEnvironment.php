<?php

namespace Sepehr\BehatLaravelJs\Concerns;

trait PreserveBehatEnvironment
{
    /**
     * Backup and preserve the testing environment.
     *
     * @BeforeScenario
     */
    public static function backupAndPreserve()
    {
        rename('.env', '.env.bak');
        copy('.env.behat', '.env');
    }

    /**
     * Restore the environment.
     *
     * @AfterScenario
     */
    public static function restore()
    {
        unlink('.env');
        rename('.env.bak', '.env');
    }
}
