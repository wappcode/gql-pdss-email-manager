<?php

namespace GPDEmailManager\Library;

use Exception;
use GPDCore\Graphql\ArrayToEntity;
use GPDCore\Library\IContextService;
use GPDEmailManager\Entities\EmailQueue;
use GPDEmailManager\Entities\EmailRecipient;

class CreateEmailQueue
{

    public static function create(IContextService $context, $input): EmailQueue
    {
        $entityManager = $context->getEntityManager();
        $entityManager->beginTransaction();
        try {
            $recipients = $input["recipients"] ?? [];
            unset($input["recipients"]);
            $emailQueue = new EmailQueue();
            $emailQueue = ArrayToEntity::apply($emailQueue, $input);
            $entityManager->persist($emailQueue);
            static::addRecipients($context, $recipients, $emailQueue);
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
            $recipient = ArrayToEntity::apply($recipient, $recipientInput);
            $recipient->setQueue($emailQueue);
            $entityManager->persist($recipient);
            return $recipient;
        }), $recipients);
        return $result;
    }
}
