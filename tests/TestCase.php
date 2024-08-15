<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // $this->seed(\Database\Seeders\RolesTableSeeder::class);
        // $this->seed(\Database\Seeders\PermissionsTableSeeder::class);

    }
}
