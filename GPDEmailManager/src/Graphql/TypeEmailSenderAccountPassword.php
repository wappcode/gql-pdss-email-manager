<?php

declare(strict_types=1);

namespace GPDEmailManager\Graphql;

use Exception;
use GraphQL\Error\Error;
use GraphQL\Utils\Utils;
use GPDCore\Library\IContextService;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Language\AST\StringValueNode;
use GPDEmailManager\Library\EmialPassworEncoder;

final class TypeEmailSenderAccountPassword extends ScalarType
{
    const NAME = 'EmailSenderAccountPassword';

    /**
     * @var IContextService
     */
    protected $context;
    public function __construct(IContextService $context, array $config = [])
    {
        parent::__construct($config);
        $this->context = $context;
        $this->name = static::NAME;
    }
    public function parseLiteral($valueNode, array $variables = null)
    {
        // Note: throwing GraphQL\Error\Error vs \UnexpectedValueException to benefit from GraphQL
        // error location in query:
        if (!($valueNode instanceof StringValueNode)) {
            throw new Error('Query error: Can only parse strings got: ' . $valueNode->kind, $valueNode);
        }

        return $this->parseValue($valueNode->value);
    }

    public function parseValue($value, array $variables = null)
    {
        if (!is_string($value)) {
            throw new \UnexpectedValueException('Cannot represent value: ' . Utils::printSafe($value));
        }
        try {
            $appConfig = $this->context->getConfig();
            $encriptPassword = $appConfig->get("gpd_email_manager__secret_key");
            $iv = $appConfig->get('gql_email_manager__iv');
            $password = EmialPassworEncoder::encrypt($value, $encriptPassword, $iv);
            return $password;
        } catch (Exception $e) {
            throw new Error('Email Sender Password Parse Error');
        }
    }

    public function serialize($value)
    {
        try {
            $appConfig = $this->context->getConfig();
            $encriptPassword = $appConfig->get("gpd_email_manager__secret_key");
            $iv = $appConfig->get('gql_email_manager__iv');
            $password = EmialPassworEncoder::decrypt($value, $encriptPassword, $iv);
            return $password;
        } catch (Exception $e) {
            throw new Error('Email Sender Password Parse Error');
        }
    }
}
