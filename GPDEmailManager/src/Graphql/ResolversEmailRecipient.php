<?php

declare(strict_types=1);

namespace GPDEmailManager\Graphql;

use GPDCore\Library\EntityBuffer;
use GPDCore\Library\ResolverFactory;
use GPDEmailManager\Entities\EmailQueue;

class ResolversEmailRecipient
{

    public static function getQueueResolver(?callable $proxy): callable
    {
        $entityBufer = new EntityBuffer(EmailQueue::class, EmailQueue::RELATIONS_MANY_TO_ONE);
        $resolver = ResolverFactory::createEntityResolver($entityBufer, 'queue');
        return is_callable($proxy) ? $proxy($resolver) : $resolver;
    }
}
