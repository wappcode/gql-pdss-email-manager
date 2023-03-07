<?php

namespace GPDEmailManager\Graphql;

use GPDCore\Library\AbstractConnectionTypeServiceFactory;

class TypeEmailQueueConnection extends AbstractConnectionTypeServiceFactory
{

    const NAME = 'EmailQueueConnection';
    const DESCRIPTION = '';
    protected static $instance = null;
}
