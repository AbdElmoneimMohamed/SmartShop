<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;
use Smpita\ConfigAs\ConfigAs;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        ConfigAs::flush();

        $this->afterApplicationCreated(fn () => Http::preventStrayRequests());
    }
}
