<?php

declare(strict_types=1);

namespace Awtechs\LaravelDomainKit\Queue;

trait UsesDomainQueue
{
    public function queueName(): string
    {
        return property_exists($this, 'queue')
            ? (string) $this->queue
            : 'default';
    }
}
