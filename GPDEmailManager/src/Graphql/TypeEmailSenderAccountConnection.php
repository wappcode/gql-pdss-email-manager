<?php

namespace GPDEmailManager\Graphql;

use GPDCore\Library\AbstractConnectionTypeServiceFactory;

class TypeEmailSenderAccountConnection extends AbstractConnectionTypeServiceFactory
{

    const NAME = 'EmailSenderAccountConnection';
    const DESCRIPTION = '';
    protected static $instance = null;
}
