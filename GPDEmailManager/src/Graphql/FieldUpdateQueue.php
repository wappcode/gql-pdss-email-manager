<?php

namespace GPDEmailManager\Graphql;

use GraphQL\Type\Definition\Type;
use GPDCore\Library\IContextService;
use GPDEmailManager\Entities\EmailQueue;
use GPDCore\Library\GeneralDoctrineUtilities;
use GPDCore\Library\GQLException;
use GPDEmailManager\Library\EmailQueueManager;

class FieldUpdateQueue
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
                'id' => [
                    'type' => Type::nonNull(Type::id()),
                ],
                'input' => [
                    'type' => Type::nonNull($types->getPartialInput(EmailQueue::class))
                ],
            ],
            'resolve' => $proxyResolve

        ];
    }

    protected static function createResolve()
    {
        return function ($root, $args, IContextService $context, $info) {
            $input = $args["input"];
            $id = $args["id"];
            $entityManager = $context->getEntityManager();
            if (empty($id)) {
                throw new GQLException("Ivalid Id");
            }
            $emailQueue = $entityManager->find(EmailQueue::class, $id);

            if (!($emailQueue instanceof EmailQueue)) {
                throw new GQLException("The queue does not exist");
            }

            $entityManager = $context->getEntityManager();
            $emailQueue = EmailQueueManager::save($context, $input, $emailQueue);
            $result = GeneralDoctrineUtilities::getArrayEntityById($entityManager, EmailQueue::class, $emailQueue->getId(), EmailQueue::RELATIONS_MANY_TO_ONE);
            return $result;
        };
    }
}
