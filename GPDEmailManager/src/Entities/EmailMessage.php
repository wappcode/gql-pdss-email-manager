<?php

namespace GPDEmailManager\Entities;

use GPDCore\Entities\AbstractEntityModelStringId;
use Doctrine\ORM\Mapping as ORM;
use GraphQL\Doctrine\Annotation as API;

/**
 * @ORM\Entity()
 * @ORM\Table(name="gpd_email_message")
 */
class EmailMessage extends AbstractEntityModelStringId
{

  
    /**
     * @ORM\Column(name="subject", type="string", length=255, nullable=false)
     *
     * @var string
     */
    protected $subject;
    /**
     * @ORM\Column(name="body", type="text", nullable=false)
     *
     * @var string
     */
    protected $body;
    /**
     * @ORM\Column(name="plain_text_body", type="text", nullable=true)
     *
     * @var string
     */
    protected $plainTextBody;
    /**
     * @ORM\Column(name="chartset", type="text", nullable=false, options={"default":"UTF-8"})
     *
     * @var string
     */
    protected $chartset;


    public function __construct()
    {
        $this->chartset = 'UTF-8';
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
     * Get the value of body
     *
     * @return  string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set the value of body
     *
     * @param  string  $body
     *
     * @return  self
     */
    public function setBody(string $body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get the value of chartset
     *
     * @return  string
     */ 
    public function getChartset()
    {
        return $this->chartset;
    }

    /**
     * Set the value of chartset
     *
     * @param  string  $chartset
     *
     * @return  self
     */ 
    public function setChartset(string $chartset)
    {
        $this->chartset = $chartset;

        return $this;
    }

    /**
     * Get the value of plainTextBody
     *
     * @return  ?string
     */ 
    public function getPlainTextBody()
    {
        return $this->plainTextBody;
    }

    /**
     * Set the value of plainTextBody
     *
     * @param  string  $plainTextBody
     *
     * @return  self
     */ 
    public function setPlainTextBody(?string $plainTextBody)
    {
        $this->plainTextBody = $plainTextBody;

        return $this;
    }
}