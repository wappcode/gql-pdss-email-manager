<?php

declare(strict_types=1);

namespace GPDEmailManager\Graphql;

use GPDEmailManager\Entities\EmailRecipient;
use GraphQL\Type\Definition\EnumType;

class TypeEmailRecipientStatus extends EnumType
{
    const NAME = 'EmailRecipientStatus';
    public function __construct()
    {
        $config = [
            'name' => static::NAME,
            'values' => [
                EmailRecipient::STATUS_PAUSE,
                EmailRecipient::STATUS_WAITING,
                EmailRecipient::STATUS_CANCELED,
                EmailRecipient::STATUS_ERROR,
                EmailRecipient::STATUS_PAUSE,
                EmailRecipient::STATUS_SENT,
            ],
        ];

        parent::__construct($config);
    }
}
