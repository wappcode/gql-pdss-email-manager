<?php

namespace GPDEmailManager\Entities;

use GPDCore\Entities\AbstractEntityModelStringId;

class SenderAccount  extends AbstractEntityModelStringId{

    protected $title;

    protected $server;
    
    protected $port;

    protected $security;

    protected $username;
    
    protected $password;

    protected $emailsPerMinute;
}