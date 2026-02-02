<?php

declare(strict_types=1);

namespace Awtech\LaravelDomainKit\Queue;

use Illuminate\Contracts\Queue\ShouldQueue;

final class DomainQueueResolver
{
    public static function resolve(ShouldQueue $job): ?string
    {
        if (method_exists($job, 'queueName')) {
            return $job->queueName();
        }

        return null;
    }
}
