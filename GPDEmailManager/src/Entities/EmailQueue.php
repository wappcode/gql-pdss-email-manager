<?php

namespace GPDEmailManager\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use GraphQL\Doctrine\Annotation as API;
use Doctrine\Common\Collections\Collection;
use GPDCore\Entities\AbstractEntityModelStringId;

/**
 * @ORM\Entity()
 * @ORM\Table(name="gpd_email_queue")
 */
class EmailQueue extends AbstractEntityModelStringId
{

    const RELATIONS_MANY_TO_ONE = ['message', 'senderAccount'];
    const PRIORITY_HIGHT = 100;
    const PRIORIRY_MEDIUM = 10;
    const PRIORITY_LOW = 0;



    /**
     * Title to identify the list
     * @ORM\Column(type="string", name="title", length=255, nullable=false)
     * @var string
     */
    protected $title;
    /**
     * @ORM\Column(name="subject", type="string", length=255, nullable=false)
     *
     * @var string
     */
    protected $subject;
    /**
     * Email address to reply the message
     * @ORM\Column(name="reply_to", type="string", length=255, nullable=true)
     * @var string
     */
    protected $replyTo;
    /**
     * Name or label for the replay address
     * @ORM\Column(name="reply_to_name", type="string", length=255, nullable=true)
     * @var ?string
     */
    protected $replyToName;
    /**
     * Alias or substitute for the sender email address 
     * @ORM\Column(name="sender_name", type="string", length=255, nullable=true)
     * @var ?string
     */
    protected $senderName;
    /**
     * Alias or substitute for the sender email address 
     * @ORM\Column(name="sender_email_address", type="string", length=255, nullable=true)
     * @var ?string
     */
    protected $senderAddress;
    /**
     * 
     * @ORM\ManyToOne(targetEntity="\GPDEmailManager\Entities\EmailMessage", inversedBy="queue")
     * @ORM\JoinColumn(name="message_id", referencedColumnName="id", nullable=false)
     * @var \GPDEmailManager\Entities\EmailMessage
     */
    protected $message;
    /**
     * 
     * @ORM\ManyToOne(targetEntity="\GPDEmailManager\Entities\EmailSenderAccount", inversedBy="queues")
     * @ORM\JoinColumn(name="sender_account_id", referencedColumnName="id", nullable=false)
     * @var \GPDEmailManager\Entities\EmailSenderAccount
     */
    protected $senderAccount;
    /**
     * @ORM\OneToMany(targetEntity="\GPDEmailManager\Entities\EmailRecipient", mappedBy="queue")
     *
     * @var Collection
     */
    protected $recipients;

    public function __construct()
    {
        parent::__construct();
        $this->recipients = new ArrayCollection();
    }

    /**
     * Get title to identify the list
     *
     * @return  string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title to identify the list
     *
     * @param  string  $title  Title to identify the list
     *
     * @return  self
     */
    public function setTitle(string $title)
    {
        $this->title = $title;

        return $this;
    }

      /**
     * Get the value of subject
     *
     * @return  string
     */ 
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set the value of subject
     *
     * @param  string  $subject
     *
     * @return  self
     */ 
    public function setSubject(string $subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get email address to reply the message
     *
     * @return  ?string
     */
    public function getReplyTo()
    {
        return $this->replyTo;
    }

    /**
     * Set email address to reply the message
     *
     * @param  ?string  $replyTo  Email address to reply the message
     *
     * @return  self
     */
    public function setReplyTo(?string $replyTo)
    {
        $this->replyTo = $replyTo;

        return $this;
    }

    /**
     * Get name or label for the replay address
     *
     * @return  ?string
     */
    public function getReplyToName()
    {
        return $this->replyToName;
    }

    /**
     * Set name or label for the replay address
     *
     * @param  string  $replyToName  Name or label for the replay address
     *
     * @return  self
     */
    public function setReplyToName(?string $replyToName)
    {
        $this->replyToName = $replyToName;

        return $this;
    }

    /**
     * Get alias or substitute for the sender email address
     *
     * @return  ?string
     */
    public function getSenderName()
    {
        return $this->senderName;
    }

    /**
     * Set alias or substitute for the sender email address
     *
     * @param  string  $senderName  Alias or substitute for the sender email address
     *
     * @return  self
     */
    public function setSenderName(?string $senderName)
    {
        $this->senderName = $senderName;

        return $this;
    }

    /**
     * Get alias or substitute for the sender email address
     *
     * @return  ?string
     */
    public function getSenderAddress()
    {
        return $this->senderAddress;
    }

    /**
     * Set alias or substitute for the sender email address
     *
     * @param  string  $senderAddress  Alias or substitute for the sender email address
     *
     * @return  self
     */
    public function setSenderAddress(?string $senderAddress)
    {
        $this->senderAddress = $senderAddress;

        return $this;
    }

    /**
     * Get the value of message
     *
     * @return  \GPDEmailManager\Entities\EmailMessage
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set the value of message
     *
     * @param  \GPDEmailManager\Entities\EmailMessage  $message
     *
     * @return  self
     */
    public function setMessage(\GPDEmailManager\Entities\EmailMessage $message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get the value of senderAccount
     *
     * @return  \GPDEmailManager\Entities\EmailSenderAccount
     */
    public function getSenderAccount()
    {
        return $this->senderAccount;
    }

    /**
     * Set the value of senderAccount
     *
     * @param  \GPDEmailManager\Entities\EmailSenderAccount  $senderAccount
     *
     * @return  self
     */
    public function setSenderAccount(\GPDEmailManager\Entities\EmailSenderAccount $senderAccount)
    {
        $this->senderAccount = $senderAccount;

        return $this;
    }

    /**
     * Get the value of recipients
     *
     * @return  Collection
     */
    public function getRecipients()
    {
        return $this->recipients;
    }

    /**
     * Set the value of recipients
     *
     * @param  Collection  $recipients
     *
     * @return  self
     */
    public function setRecipients(Collection $recipients)
    {
        $this->recipients = $recipients;

        return $this;
    }

  
}
