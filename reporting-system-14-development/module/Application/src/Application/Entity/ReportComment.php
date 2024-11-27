<?php
namespace Application\Entity;
 

use Doctrine\ORM\Mapping as ORM;
 
 /**
 *
 * An Answer represents a "response" from auser for a question for a particular report
 * 
 * @ORM\Entity
 * @ORM\Table(name="report_comments")
 *
 * @author Haroon
 */
class ReportComment 
{
    
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var Report
     * @ORM\ManyToOne(targetEntity="Application\Entity\Report",fetch="EAGER")
     * @ORM\JoinColumn(name="report_id", referencedColumnName="id", nullable=false)
     */
    protected $report;     


    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Application\Entity\User",fetch="EAGER")
     * @ORM\JoinColumn(name="created_by_user_id", referencedColumnName="id", nullable=false)
     */
    protected $user_created;   
    
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Application\Entity\User",fetch="LAZY")
     * @ORM\JoinColumn(name="acknowledged_by_user_id", referencedColumnName="id", nullable=true)
     */
    protected $user_acknowledged;   
    
    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $date_created;    
    
    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $date_acknowledge; 
    
    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     */
    protected $comment;           


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
     * Set date_created
     *
     * @param \DateTime $dateCreated
     * @return ReportComment
     */
    public function setDateCreated($dateCreated)
    {
        $this->date_created = $dateCreated;

        return $this;
    }

    /**
     * Get date_created
     *
     * @return \DateTime 
     */
    public function getDateCreated()
    {
        return $this->date_created;
    }

    /**
     * Set date_acknowledge
     *
     * @param \DateTime $dateAcknowledge
     * @return ReportComment
     */
    public function setDateAcknowledge($dateAcknowledge)
    {
        $this->date_acknowledge = $dateAcknowledge;

        return $this;
    }

    /**
     * Get date_acknowledge
     *
     * @return \DateTime 
     */
    public function getDateAcknowledge()
    {
        return $this->date_acknowledge;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return ReportComment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set report
     *
     * @param \Application\Entity\Report $report
     * @return ReportComment
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

    /**
     * Set user_created
     *
     * @param \Application\Entity\User $userCreated
     * @return ReportComment
     */
    public function setUserCreated(\Application\Entity\User $userCreated)
    {
        $this->user_created = $userCreated;

        return $this;
    }

    /**
     * Get user_created
     *
     * @return \Application\Entity\User 
     */
    public function getUserCreated()
    {
        return $this->user_created;
    }

    /**
     * Set user_acknowledged
     *
     * @param \Application\Entity\User $userAcknowledged
     * @return ReportComment
     */
    public function setUserAcknowledged(\Application\Entity\User $userAcknowledged = null)
    {
        $this->user_acknowledged = $userAcknowledged;

        return $this;
    }

    /**
     * Get user_acknowledged
     *
     * @return \Application\Entity\User 
     */
    public function getUserAcknowledged()
    {
        return $this->user_acknowledged;
    }
}
