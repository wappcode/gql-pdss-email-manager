<?php

namespace GPDEmailManager\Graphql;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use GPDCore\Library\GQLException;
use GraphQL\Type\Definition\Type;
use GPDCore\Library\IContextService;
use GPDEmailManager\Entities\EmailRecipient;
use GPDCore\Library\GeneralDoctrineUtilities;


class FieldForwardEmailRecipient
{


    public static function get($context, ?callable $proxy)
    {
        $resolve = static::createResolve();
        $proxyResolve = is_callable($proxy) ? $proxy($resolve) : $resolve;
        $serviceManager = $context->getServiceManager();
        $types = $context->getTypes();
        return [
            'type' => $types->getOutput(EmailRecipient::class),
            'description' => 'Create a new email recipient using the recipient related to the id argument',
            'args' => [
                'id' => [
                    'type' => Type::nonNull(Type::id())
                ],
                'input' => [
                    'type' => Type::nonNull($types->getInput(TypeEmailRecipientForwardInput::NAME))
                ]

            ],
            'resolve' => $proxyResolve
        ];
    }

    private static function createResolve()
    {
        return function ($root, $args, IContextService $context, $info) {
            $entityManager = $context->getEntityManager();
            $id = $args["id"];
            $input = $args["input"];
            $email = $input["email"];
            $name = $input["name"] ?? null;
            $sendingDate = $input["sendingDate"];
            $status = $input["status"];
            $priority = $input["priority"];
            $isOwnerReference = $input["isOwnerReference"] ?? false;
            $emailRecipient = $entityManager->find(EmailRecipient::class, $id);
            if (!($emailRecipient instanceof EmailRecipient)) {
                throw new GQLException("The recipient doesn't exist", 400);
            }
            $entityManager->beginTransaction();
            try {
                $newRecipient = static::createNewRecipient($emailRecipient, $email, $sendingDate, $status, $priority, $isOwnerReference, $name);
                if ($isOwnerReference) {
                    $emailRecipient->setOwnerCode(null);
                    $entityManager->flush();
                }
                $entityManager->persist($newRecipient);
                $entityManager->flush();
                $result = GeneralDoctrineUtilities::getArrayEntityById($entityManager, EmailRecipient::class, $emailRecipient->getId());
                $entityManager->commit();
                return $result;
            } catch (Exception $e) {
                $entityManager->rollback();
                throw $e;
            }
        };
    }

    private static function createNewRecipient(EmailRecipient $recipient, string $email, DateTimeInterface $sendingDate,  string $status, string $priority, bool $isOwnerReference = false, ?string $name): EmailRecipient
    {
        $newRecipient = new EmailRecipient();
        $finalName = $name ?? $recipient->getName();
        $finalOwnercode = $isOwnerReference ? $recipient->getOwnerCode() : null;
        $finalSendingDate = new DateTimeImmutable($sendingDate->format('c'));
        $newRecipient->setEmail($email)
            ->setName($finalName)
            ->setOwnerCode($finalOwnercode)
            ->setParams($recipient->getParams())
            ->setQueue($recipient->getQueue())
            ->setPriority($priority)
            ->setSendingDate($finalSendingDate)
            ->setStatus($status);

        return $newRecipient;
    }
}
