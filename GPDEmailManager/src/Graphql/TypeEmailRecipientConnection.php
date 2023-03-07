<?php

namespace GPDEmailManager\Graphql;

use GPDCore\Library\AbstractConnectionTypeServiceFactory;

class TypeEmailRecipientConnection extends AbstractConnectionTypeServiceFactory
{

    const NAME = 'EmailRecipientConnection';
    const DESCRIPTION = '';
    protected static $instance = null;
}
