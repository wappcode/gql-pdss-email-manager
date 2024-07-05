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
                'name' => [
                    'type' => Type::string()
                ],
                'email' => [
                    'type' => Type::nonNull(Type::string())
                ],
                'sendingDate' => [
                    'type' => Type::nonNull($serviceManager->get(DateTimeInterface::class))
                ],
                'status' => [
                    'type' => Type::nonNull($serviceManager->get(TypeEmailRecipientStatus::NAME))
                ],
                'priority' => [
                    'type' => Type::nonNull(Type::int()),
                    'defaultValue' => EmailRecipient::PRIORITY_LOW
                ],
                'isOwnerReference' => [
                    'type' => Type::nonNull(Type::boolean()),
                    'default' => false,
                    'description' => 'Indicates if the record must have the owner code. Only one record per queue can have a specific owner code. If it is set to true the older record will update de owner code to null. All of this do not apply if the source record do not have an owner code'
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
            $email = $args["email"];
            $name = $args["name"] ?? null;
            $sendingDate = $args["sendingDate"];
            $status = $args["status"];
            $priority = $args["priority"];
            $isOwnerReference = $args["isOwnerReference"] ?? false;
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
