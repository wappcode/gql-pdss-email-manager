<?php

namespace GPDEmailManager\Graphql;

use GPDCore\Library\AbstractConnectionTypeServiceFactory;

class TypeEmailMessageConnection extends AbstractConnectionTypeServiceFactory
{

    const NAME = 'EmailMessageConnection';
    const DESCRIPTION = '';
    protected static $instance = null;
}
