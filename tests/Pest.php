<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses()->beforeEach(function () {
    config()->set('domain-kit.generate.controllers', true);
});
