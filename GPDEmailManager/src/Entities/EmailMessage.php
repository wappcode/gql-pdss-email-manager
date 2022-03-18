<?php

namespace GPDEmailManager\Entities;

use GPDCore\Entities\AbstractEntityModelStringId;
use Doctrine\ORM\Mapping as ORM;
use GraphQL\Doctrine\Annotation as API;

class EmailMessage extends AbstractEntityModelStringId
{

    /**
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     *
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
     * @ORM\Column(name="body", type="text", nullable=false)
     *
     * @var string
     */
    protected $body;


    /**
     * Get the value of title
     *
     * @return  string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of title
     *
     * @param  string  $title
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
}
