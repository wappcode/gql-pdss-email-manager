<?php

namespace GPDEmailManager\Library;

use Exception;
use GPDCore\Graphql\ArrayToEntity;
use GPDCore\Library\IContextService;
use GPDEmailManager\Entities\EmailQueue;
use GPDEmailManager\Entities\EmailRecipient;

class EmailQueueManager
{

    public static function create(IContextService $context, $input): EmailQueue
    {

        $emailQueue = new EmailQueue();
        return static::save($context, $input, $emailQueue);
    }
    public static function save(IContextService $context, $input, EmailQueue $emailQueue): EmailQueue
    {
        $entityManager = $context->getEntityManager();
        $entityManager->beginTransaction();
        try {
            $recipients = $input["recipients"] ?? [];
            unset($input["recipients"]);
            $emailQueue = ArrayToEntity::setValues($entityManager, $emailQueue, $input);
            if (empty($id)) {
                $entityManager->persist($emailQueue);
                $entityManager->flush();
            }
            static::addRecipients($context, $recipients, $emailQueue);
            $id = $emailQueue->getId() ?? null;
            $entityManager->flush();
            $entityManager->commit();
            return $emailQueue;
        } catch (Exception $e) {
            $entityManager->rollback();
            throw $e;
        }
    }
    protected static function addRecipients(IContextService $context, array $recipients, EmailQueue $emailQueue): array
    {
        $result = array_map((function ($recipientInput) use ($context, $emailQueue) {
            $entityManager = $context->getEntityManager();
            $recipient = new EmailRecipient();
            $recipient = ArrayToEntity::setValues($entityManager, $recipient, $recipientInput);
            $recipient->setQueue($emailQueue);
            $entityManager->persist($recipient);
            return $recipient;
        }), $recipients);
        return $result;
    }
}
