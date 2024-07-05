<?php

namespace GPDEmailManager\Graphql;

use DateTimeInterface;
use GraphQL\Type\Definition\Type;
use GPDCore\Library\IContextService;
use GPDEmailManager\Entities\EmailRecipient;
use GraphQL\Type\Definition\InputObjectType;

class TypeForwardEmailRecipientInput extends InputObjectType
{


    const NAME = 'ForwardEmailRecipientInput';

    public function __construct(IContextService $context)
    {
        $serviceManager = $context->getServiceManager();
        $config = [
            'name' => static::NAME,
            'fields' => [
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
            ]
        ];
        parent::__construct($config);
    }
}
