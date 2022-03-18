<?php

namespace GPDEmailManager\Entities;

use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use GPDCore\Entities\AbstractEntityModelStringId;
use Doctrine\ORM\Mapping as ORM;
use GraphQL\Doctrine\Annotation as API;

/**
 * @ORM\Entity()
 * @ORM\Table(name="gpd_email_recipient")
 */
class EmailRecipient extends AbstractEntityModelStringId
{

    const RELATIONS_MANY_TO_ONE = ['queue'];
    const STATUS_WAITING = 'WAITING';
    const STATUS_PAUSE = 'PAUSE';
    const STATUS_CANCELED = 'CANCELED';
    const STATUS_SENT = 'SENT';


    /**
     * @ORM\Column(name="email", type="string", length=255, nullable= false)
     *
     * @var string
     */
    protected $email;

    /**
     * @ORM\Column(type="integer", name="priority", length=3, nullable=false, options={"default": 0})
     * @var int
     */
    protected $priority;
    /**
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
     * @ORM\Column(name="viewed", type="datetime", nullable=true )
     *
     * @var DateTimeImmutable
     */
    protected $viewed;
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
     *
     * @return  array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Set the value of params
     *
     * @param  array  $params
     *
     * @return  self
     */
    public function setParams(array $params)
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
     * Set the value of status
     *
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
     * Get the value of viewed
     *
     * @return  DateTimeImmutable
     */
    public function getViewed()
    {
        return $this->viewed;
    }

    /**
     * Set the value of viewed
     *
     * @param  DateTimeImmutable  $viewed
     *
     * @return  self
     */
    public function setViewed(DateTimeImmutable $viewed)
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
     * @param  \GPDEmailManager\Entities\EmailQueue  $queue
     *
     * @return  self
     */
    public function setQueue(\GPDEmailManager\Entities\EmailQueue $queue)
    {
        $this->queue = $queue;

        return $this;
    }
}
