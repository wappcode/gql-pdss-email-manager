<?php

namespace GPDEmailManager\Entities;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use GraphQL\Doctrine\Annotation as API;
use GPDEmailManager\Entities\EmailQueue;
use GPDCore\Entities\AbstractEntityModelStringId;

/**
 * @ORM\Entity()
 * @ORM\Table(name="gpd_email_recipient", uniqueConstraints={
 * @ORM\UniqueConstraint(name="owner_code", columns={"queue_id","owner_code"})
 * })
 */
class EmailRecipient extends AbstractEntityModelStringId
{

    const RELATIONS_MANY_TO_ONE = ['queue'];
    const STATUS_WAITING = 'WAITING';
    const STATUS_PAUSE = 'PAUSE';
    const STATUS_CANCELED = 'CANCELED';
    const STATUS_SENT = 'SENT';
    const STATUS_ERROR = 'ERROR';
    const PRIORITY_HIGHT = 100;
    const PRIORIRY_MEDIUM = 10;
    const PRIORITY_LOW = 0;

    /**
     * @ORM\Column(name="email", type="string", length=255, nullable= false)
     *
     * @var string
     */
    protected $email;

    /**
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     *
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="integer", name="priority", length=3, nullable=false, options={"default": 0})
     * @var int
     */
    protected $priority;
    /**
     * The params type is an array of 2 string key and value
     * Expected value example : [ ["key1", "value1"], ["key2", "value2"] ]
     * @ORM\Column(name="params", type="json", nullable=true)
     * @var array
     */
    protected $params;

    /** 
     * @ORM\Column(name="status", type="string", length=255, nullable=false) 
     * @var string 
     */
    protected $status;
    /**
     * @ORM\Column(name="sent", type="boolean", nullable=false, options={"default": 0})
     * @var bool
     */
    protected $sent;
    /**
     * @ORM\Column(name="sending_date", type="datetime", nullable=false )
     *
     * @var DateTimeImmutable
     */
    protected $sendingDate;
    /**
     * @ORM\Column(name="viewed", type="datetime", nullable=true )
     *
     * @var ?DateTimeImmutable
     */
    protected $viewed;

    /**
     * Extern reference to the owner 
     * 
     * @ORM\Column(type="string", length=500, name="owner_code", nullable=true) 
     * @var ?string
     */
    protected $ownerCode;

    /**
     * @ORM\ManyToOne(targetEntity="\GPDEmailManager\Entities\EmailQueue", inversedBy="recipients")
     * @ORM\JoinColumn(name="queue_id", referencedColumnName="id", nullable=false)
     *
     * @var \GPDEmailManager\Entities\EmailQueue
     */
    protected $queue;


    public function __construct()
    {
        parent::__construct();
        $this->sent = false;
        $this->priority = static::PRIORITY_LOW;
        $this->status = static::STATUS_WAITING;
    }

    /**
     * Get the value of name
     *
     * @return  ?string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param  string  $name
     *
     * @return  self
     */
    public function setName(?string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of email
     *
     * @return  string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @param  string  $email
     *
     * @return  self
     */
    public function setEmail(string $email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of params
     * Params can be null but this method always return an array
     * The params type is an array of 2 string key and value
     * Expected value example : [ ["key1", "value1"], ["key2", "value2"] ]
     * @API\Field(type="?GPDEmailManager\Graphql\TypeEmailRecipientParams")
     * @return  array
     */
    public function getParams(): array
    {
        return $this->params ?? [];
    }

    /**
     * Set the value of params
     * The params type is an array of 2 string key and value
     * Expected value example : [ ["key1", "value1"], ["key2", "value2"] ]
     * @API\Input(type="?GPDEmailManager\Graphql\TypeEmailRecipientParams")
     * @param  array  $params
     *
     * @return  self
     */
    public function setParams(?array $params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Get the value of status
     *
     * @return  string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * 
     *
     * 
     * @API\Input(type="GPDEmailManager\Graphql\TypeEmailRecipientStatus")
     * @param  string  $status
     *
     * @return  self
     */
    public function setStatus(string $status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the value of sent
     *
     * @return  bool
     */
    public function getSent()
    {
        return $this->sent;
    }

    /**
     * Set the value of sent
     *
     * @API\Exclude()
     * @param  bool  $sent
     *
     * @return  self
     */
    public function setSent(bool $sent)
    {
        $this->sent = $sent;

        return $this;
    }


    /**
     * Get the value of sendingDate
     *
     * @return  DateTimeImmutable
     */
    public function getSendingDate()
    {
        return $this->sendingDate;
    }

    /**
     * Date after which the message can be process
     *
     * @param  DateTimeImmutable  $sendingDate
     *
     * @return  self
     */
    public function setSendingDate(DateTimeImmutable $sendingDate)
    {
        $this->sendingDate = $sendingDate;

        return $this;
    }

    /**
     * Get the value of viewed
     *
     * @return  ?DateTimeImmutable
     */
    public function getViewed()
    {
        return $this->viewed;
    }

    /**
     * Set the value of viewed
     * @API\Exclude()
     * @param  DateTimeImmutable  $viewed
     *
     * @return  self
     */
    public function setViewed(?DateTimeImmutable $viewed)
    {
        $this->viewed = $viewed;

        return $this;
    }

    /**
     * Get the value of queue
     *
     * @return  \GPDEmailManager\Entities\EmailQueue
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * Set the value of queue
     *
     * @param  EmailQueue  $queue
     *
     * @return  self
     */
    public function setQueue(\GPDEmailManager\Entities\EmailQueue $queue)
    {
        $this->queue = $queue;

        return $this;
    }

    /**
     * Get the value of priority
     *
     * @return  int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Sets the value of the property PRIORITY_HIGHT = 100, PRIORIRY_MEDIUM = 10 ,PRIORITY_LOW = 0
     *
     * @param  int  $priority
     *
     * @return  self
     */
    public function setPriority(int $priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get the extern reference to the owner 
     *
     * @return  ?string
     */
    public function getOwnerCode()
    {
        return $this->ownerCode;
    }

    /**
     * Set the extern reference to the owner 
     *
     * @param  string  $ownerCode
     *
     * @return  self
     */
    public function setOwnerCode(?string $ownerCode)
    {
        $this->ownerCode = $ownerCode;

        return $this;
    }
}
