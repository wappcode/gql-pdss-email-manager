<?php

declare(strict_types=1);

namespace GPDEmailManager\Graphql;

use GraphQL\Type\Definition\EnumType;
use PHPMailer\PHPMailer\PHPMailer;

class TypeEmailSenderAccountSecure extends EnumType
{
    const NAME = 'EmailSenderAccountSecure';
    public function __construct()
    {
        $config = [
            'name' => static::NAME,
            'values' => [
                PHPMailer::ENCRYPTION_SMTPS,
                PHPMailer::ENCRYPTION_STARTTLS,
            ],
        ];

        parent::__construct($config);
    }
}
