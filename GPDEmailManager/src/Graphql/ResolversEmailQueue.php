<?php

declare(strict_types=1);

namespace GPDEmailManager\Graphql;

use GPDCore\Library\EntityBuffer;
use GPDCore\Library\ResolverFactory;
use GPDEmailManager\Entities\EmailMessage;
use GPDEmailManager\Entities\EmailSenderAccount;

class ResolversEmailQueue
{

    public static function getMessageResolver(?callable $proxy): callable
    {
        $entityBufer = new EntityBuffer(EmailMessage::class, EmailMessage::RELATIONS_MANY_TO_ONE);
        $resolver = ResolverFactory::createEntityResolver($entityBufer, 'message');
        return is_callable($proxy) ? $proxy($resolver) : $resolver;
    }
    public static function getSenderAccountResolver(?callable $proxy): callable
    {
        $entityBufer = new EntityBuffer(EmailSenderAccount::class, EmailSenderAccount::RELATIONS_MANY_TO_ONE);
        $resolver = ResolverFactory::createEntityResolver($entityBufer, 'senderAccount');
        return is_callable($proxy) ? $proxy($resolver) : $resolver;
    }
}
