<?php

namespace GPDEmailManager\Graphql;

use Exception;
use GraphQL\Type\Definition\Type;
use GPDCore\Library\IContextService;
use GPDEmailManager\Entities\EmailQueue;
use GPDCore\Library\GeneralDoctrineUtilities;
use GPDEmailManager\Library\EmailQueueManager;

class FieldCreateQueue
{

    public static function get(IContextService $context, ?callable $proxy)
    {
        $resolve = static::createResolve();
        $proxyResolve = is_callable($proxy) ? $proxy($resolve) : $resolve;
        $types = $context->getTypes();
        $serviceManager = $context->getServiceManager();
        return [
            'type' => Type::nonNull($types->getOutput(EmailQueue::class)),
            'args' => [
                'input' => [
                    'type' => Type::nonNull($types->getInput(EmailQueue::class))
                ],
            ],
            'resolve' => $proxyResolve

        ];
    }

    protected static function createResolve()
    {
        return function ($root, $args, IContextService $context, $info) {
            $input = $args["input"];
            $entityManager = $context->getEntityManager();
            $emailQueue = EmailQueueManager::create($context, $input);
            $result = GeneralDoctrineUtilities::getArrayEntityById($entityManager, EmailQueue::class, $emailQueue->getId(), EmailQueue::RELATIONS_MANY_TO_ONE);
            return $result;
        };
    }
}
