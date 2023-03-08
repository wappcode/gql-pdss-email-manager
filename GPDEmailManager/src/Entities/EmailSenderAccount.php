<?php

namespace GPDEmailManager\Entities;

use Doctrine\ORM\Mapping as ORM;
use GraphQL\Doctrine\Annotation as API;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use GPDCore\Entities\AbstractEntityModelStringId;

/**
 * @ORM\Entity()
 * @ORM\Table(name="gpd_email_sender_account")
 */
class EmailSenderAccount  extends AbstractEntityModelStringId
{

    const RELATIONS_MANY_TO_ONE = [];
    /**
     * 
     * Title of the account it is showed to recipients
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     *
     * @var string
     */
    protected $title;
    /**
     * 
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     *
     * @var string
     */
    protected $email;

    /**
     * @ORM\Column(name="server", type="string", length=255, nullable=false)
     *
     * @var string
     */
    protected $host;

    /**
     * Enable SMTP authentication
     *
     * @ORM\Column(name="auth", type="boolean", nullable=false, options={"default":1})
     * @var bool
     */
    protected $auth;

    /**
     * @ORM\Column(name="username", type="string", length=255, nullable=false)
     *
     * @var string
     */
    protected $username;
    /**
     * The password must be encripted
     * @ORM\Column(name="account_password", type="string", length=1000, nullable=false)
     *
     * @var string
     */
    protected $password;

    /**
     * Enable implicit TLS encryption
     * Values can be PHPMailer::ENCRYPTION_SMTPS (ssl) or PHPMailer::ENCRYPTION_STARTTLS(tls)
     * @ORM\Column(name="secure", type="string", length=255, nullable=true)
     * @var string
     */
    protected $secure;
    /**
     * TCP port to connect to; use 587 if you have set SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS
     * @ORM\Column(name="port", type="integer", nullable=false)
     * @var int
     */
    protected $port;

    /**
     * Total the email can be delivery by the account in a hour
     * @ORM\Column(name="max_deliveries_per_hour", type="integer", nullable=false)
     * @var int
     */
    protected $maxDeliveriesPerHour;


    /**
     * @ORM\OneToMany(targetEntity="\GPDEmailManager\Entities\EmailQueue", mappedBy="senderAccount")
     *
     * @var Collection
     */
    protected $queues;


    public function __construct()
    {
        parent::__construct();
        $this->auth = true;
        $this->queues = new ArrayCollection();
    }




    /**
     * Get title of the account it is showed to recipients
     *
     * @return  string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title of the account it is showed to recipients
     *
     * @param  string  $title  Title of the account it is showed to recipients
     *
     * @return  self
     */
    public function setTitle(string $title)
    {
        $this->title = $title;

        return $this;
    }


    /**
     * Get title of the account it is showed to recipients
     *
     * @return  string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set title of the account it is showed to recipients
     *
     * @param  string  $email  Title of the account it is showed to recipients
     *
     * @return  self
     */
    public function setEmail(string $email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of host
     *
     * @return  string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Set the value of host
     *
     * @param  string  $host
     *
     * @return  self
     */
    public function setHost(string $host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Get enable SMTP authentication
     *
     * @return  bool
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * Set enable SMTP authentication
     *
     * @param  bool  $auth  Enable SMTP authentication
     *
     * @return  self
     */
    public function setAuth(bool $auth)
    {
        $this->auth = $auth;

        return $this;
    }

    /**
     * Get the value of username
     *
     * @return  string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set the value of username
     *
     * @param  string  $username
     *
     * @return  self
     */
    public function setUsername(string $username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get the password must be encripted
     * @API\Exclude
     * @return  string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the password must be encripted
     *
     * @param  string  $password  The password must be encripted
     *
     * @return  self
     */
    public function setPassword(string $password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get values can be PHPMailer::ENCRYPTION_SMTPS (ssl) or PHPMailer::ENCRYPTION_STARTTLS(tls)
     *
     * @return  string
     */
    public function getSecure()
    {
        return $this->secure;
    }

    /**
     * Set values can be PHPMailer::ENCRYPTION_SMTPS (ssl) or PHPMailer::ENCRYPTION_STARTTLS(tls)
     *
     * @param  string  $secure  Values can be PHPMailer::ENCRYPTION_SMTPS (ssl) or PHPMailer::ENCRYPTION_STARTTLS(tls)
     *
     * @return  self
     */
    public function setSecure(string $secure)
    {
        $this->secure = $secure;

        return $this;
    }

    /**
     * Get tCP port to connect to; use 587 if you have set SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS
     *
     * @return  int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Set tCP port to connect to; use 587 if you have set SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS
     *
     * @param  int  $port  TCP port to connect to; use 587 if you have set SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS
     *
     * @return  self
     */
    public function setPort(int $port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Get total the email can be delivery by the account in a hour
     *
     * @return  int
     */
    public function getMaxDeliveriesPerHour()
    {
        return $this->maxDeliveriesPerHour;
    }

    /**
     * Set total the email can be delivery by the account in a hour
     *
     * @param  int  $maxDeliveriesPerHour  Total the email can be delivery by the account in a hour
     *
     * @return  self
     */
    public function setMaxDeliveriesPerHour(int $maxDeliveriesPerHour)
    {
        $this->maxDeliveriesPerHour = $maxDeliveriesPerHour;

        return $this;
    }

    /**
     * Get the value of queues
     * @API\Exclude
     * @return  Collection
     */
    public function getQueues()
    {
        return $this->queues;
    }

    /**
     * Set the value of queues
     *
     * @API\Exclude
     * @param  Collection  $queues
     *
     * @return  self
     */
    public function setQueues(Collection $queues)
    {
        $this->queues = $queues;

        return $this;
    }
}
