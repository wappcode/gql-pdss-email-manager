<?php

namespace GPDEmailManager\Graphql;

use Exception;
use GPDCore\Library\GeneralDoctrineUtilities;
use GPDCore\Library\GQLException;
use GraphQL\Type\Definition\Type;
use GPDCore\Library\IContextService;
use GPDEmailManager\Entities\EmailQueue;
use GPDEmailManager\Entities\EmailRecipient;

class FieldPauseQueue
{

    public static function get(IContextService $context, ?callable $proxy)
    {
        $resolve = static::createResolve();
        $proxyResolve = is_callable($proxy) ? $proxy($resolve) : $resolve;
        $types = $context->getTypes();
        return [
            'type' => Type::nonNull($types->getOutput(EmailQueue::class)),
            'description' => "Pause all recipients related to the queue witch has status equals WAITING",
            'args' => [
                'id' => [
                    'type' => Type::nonNull(Type::id())
                ],
            ],
            'resolve' => $proxyResolve

        ];
    }

    protected static function createResolve()
    {
        return function ($root, $args, IContextService $context, $info) {
            $entityManager = $context->getEntityManager();
            $id = $args["id"];
            $emailQueue = $entityManager->find(EmailQueue::class, $id);
            if (!($emailQueue instanceof EmailQueue)) {
                throw new GQLException("The queue doesn't exist", 400);
            }
            $entityManager->beginTransaction();
            try {

                static::updateRcipients($context, $emailQueue);
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

    protected static function updateRcipients(IContextService $context, EmailQueue $emailQueue): void
    {
        $entityManager = $context->getEntityManager();
        $qb = $entityManager->createQueryBuilder()->from(EmailRecipient::class, 'recipient')->select("recipient");
        $qb->andWhere("recipient.queue = :queueId")
            ->setParameter(":queueId", $emailQueue->getId())
            ->andWhere($qb->expr()->in("recipient.status", ":status"))
            ->setParameter(":status", [EmailRecipient::STATUS_WAITING]);
        $recipients = $qb->getQuery()->getResult();
        foreach ($recipients as $recipient) {
            $recipient->setStatus(EmailRecipient::STATUS_PAUSE);
        }
    }
}
