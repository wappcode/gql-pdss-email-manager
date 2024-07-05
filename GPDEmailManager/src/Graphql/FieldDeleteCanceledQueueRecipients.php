<?php


namespace GPDEmailManager\Graphql;

use Exception;
use GPDCore\Library\GQLException;
use GraphQL\Type\Definition\Type;
use GPDCore\Library\IContextService;
use GPDEmailManager\Entities\EmailQueue;
use GPDEmailManager\Entities\EmailRecipient;
use GPDCore\Library\GeneralDoctrineUtilities;

class FieldDeleteCanceledQueueRecipients
{

    public static function get(IContextService $context, ?callable $proxy)
    {

        $resolve = static::createResolve();
        return [
            'type' => $context->getTypes()->getOutput(EmailQueue::class),
            'description' => 'Delete all canceled recipients related to the queue',
            'args' => [
                'id' => [
                    'type' => Type::nonNull(Type::id())
                ],
            ],
            'resolve' => is_callable($proxy) ? $proxy($resolve) : $resolve
        ];
    }


    private static function createResolve(): callable
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

                static::removeCanceledRecipients($context, $emailQueue);
                $entityManager->flush();
                $result = GeneralDoctrineUtilities::getArrayEntityById($entityManager, EmailQueue::class, $emailQueue->getId());
                $entityManager->commit();
                return $result;
            } catch (Exception $e) {
                $entityManager->rollback();
                throw $e;
            }
        };
    }

    protected static function removeCanceledRecipients(IContextService $context, EmailQueue $emailQueue): void
    {
        $entityManager = $context->getEntityManager();
        $qb = $entityManager->createQueryBuilder()->delete(EmailRecipient::class, 'recipient')
            ->andWhere("recipient.queue = :queueId")
            ->andWhere("recipient.status like :status")
            ->setParameter(":queueId", $emailQueue->getId())
            ->setParameter(":status", EmailRecipient::STATUS_CANCELED);
        $qb->getQuery()->execute();
    }
}
