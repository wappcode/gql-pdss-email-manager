<?php

namespace GPDEmailManager\Graphql;

use Exception;
use GPDCore\Graphql\ArrayToEntity;
use GPDCore\Library\GeneralDoctrineUtilities;
use GraphQL\Type\Definition\Type;
use GPDCore\Library\IContextService;
use GPDEmailManager\Entities\EmailQueue;
use GPDEmailManager\Entities\EmailRecipient;

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
                    'type' => $types->getInput(EmailQueue::class)
                ],
            ],
            'resolve' => $proxyResolve

        ];
    }

    protected static function createResolve()
    {
        return function ($root, $args, IContextService $context, $info) {
            $entityManager = $context->getEntityManager();
            $entityManager->beginTransaction();
            try {
                $input = $args["input"];
                $recipients = $input["recipients"] ?? [];
                unset($input["recipients"]);
                $emailQueue = new EmailQueue();
                $emailQueue = ArrayToEntity::apply($emailQueue, $input);
                $entityManager->persist($emailQueue);
                static::addRecipients($context, $recipients, $emailQueue);
                $entityManager->flush();
                $result = GeneralDoctrineUtilities::getArrayEntityById($entityManager, EmailQueue::class, $emailQueue->getId(), EmailQueue::RELATIONS_MANY_TO_ONE);
                $entityManager->commit();
                return $result;
            } catch (Exception $e) {
                $entityManager->rollback();
                throw $e;
            }
        };
    }

    protected static function addRecipients(IContextService $context, array $recipients, EmailQueue $emailQueue): array
    {
        $result = array_map((function ($recipientInput) use ($context, $emailQueue) {
            $entityManager = $context->getEntityManager();
            $recipient = new EmailRecipient();
            $recipient = ArrayToEntity::apply($recipient, $recipientInput);
            $recipient->setQueue($emailQueue);
            $entityManager->persist($recipient);
            return $recipient;
        }), $recipients);
        return $result;
    }
}
