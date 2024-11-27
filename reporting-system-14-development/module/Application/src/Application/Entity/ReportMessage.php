<?php
/**
 * Copied from  
 *
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 *
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Entity;


use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\MaxDepth;


/**
 * An example entity that represents a role.
 *
 * @ORM\Entity
 * @ORM\Table(name="report_messages")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 *
 */
class ReportMessage
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", name="description", length=255, nullable=true)
     */
    protected $description;

    /**
     * @var string
     * @ORM\Column(type="string", name="link_type", length=255, nullable=true)
     * expected values are "help", "feedback" etc.
     */
    protected $link_type;

     /**
     * @var Message
     * @ORM\ManyToOne(targetEntity="Application\Entity\Message",fetch="EAGER",cascade={"all"})
     * @ORM\JoinColumn(name="message_id", referencedColumnName="id", nullable=false)
     * @MaxDepth(1)
     */
    protected $message;

     /**
     * @var Report
     * @ORM\ManyToOne(targetEntity="Application\Entity\Report",fetch="EAGER")
     * @ORM\JoinColumn(name="report_id", referencedColumnName="id", nullable=false)
     * @MaxDepth(1)
     */
    protected $report;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return ReportMessage
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set link_type
     *
     * @param string $linkType
     * @return ReportMessage
     */
    public function setLinkType($linkType)
    {
        $this->link_type = $linkType;

        return $this;
    }

    /**
     * Get link_type
     *
     * @return string 
     */
    public function getLinkType()
    {
        return $this->link_type;
    }

    /**
     * Set message
     *
     * @param \Application\Entity\Message $message
     * @return ReportMessage
     */
    public function setMessage(\Application\Entity\Message $message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return \Application\Entity\Message 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set report
     *
     * @param \Application\Entity\Report $report
     * @return ReportMessage
     */
    public function setReport(\Application\Entity\Report $report)
    {
        $this->report = $report;

        return $this;
    }

    /**
     * Get report
     *
     * @return \Application\Entity\Report 
     */
    public function getReport()
    {
        return $this->report;
    }
}
