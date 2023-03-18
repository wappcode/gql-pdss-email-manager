<?php

namespace GPDEmailManager\Graphql;

use DateTime;
use DateTimeImmutable;
use GraphQL\Type\Definition\Type;
use GPDCore\Library\IContextService;
use GPDEmailManager\Entities\EmailRecipient;
use GraphQL\Type\Definition\InputObjectType;

class TypeEmailQueueRecipientsInput extends InputObjectType
{


    const NAME = "CreateEmailQueueRecipientsInput";
    public function __construct(IContextService $context)
    {
        $serviceManager = $context->getServiceManager();
        $config = [
            'name' => static::NAME,
            'fields' => [
                'name' => [
                    'type' => Type::nonNull(Type::string())
                ],
                'email' => [
                    'type' => Type::nonNull(Type::string())
                ],
                'params' => [
                    'type' => $serviceManager->get(TypeEmailRecipientParams::class)
                ],
                'sendingDate' => [
                    'type' => Type::nonNull($serviceManager->get(DateTimeImmutable::class))
                ],
                'priority' => [
                    'type' => Type::int(),
                    'defaultValue' => EmailRecipient::PRIORITY_LOW
                ],
                'ownerCode' => [
                    'type' => Type::string()
                ],

            ]
        ];

        parent::__construct($config);
    }
}
