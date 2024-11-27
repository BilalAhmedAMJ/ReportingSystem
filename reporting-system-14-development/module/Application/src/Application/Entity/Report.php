<?php
namespace Application\Entity;
 

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\MaxDepth;

/**
 * A Branch represents a "unit" of Jama`at that can be a Halqa, local Jama`at, Region, Country Jama`at etc.
 *
 * @ORM\Entity
 * @ORM\Table(name="reports")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 * 
 * 
 * @author Haroon
 */
class Report 
{
    
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var ReportConfig
     * @ORM\ManyToOne(targetEntity="Application\Entity\ReportConfig")
     * @ORM\JoinColumn(name="report_config", referencedColumnName="report_code", nullable=false)
     */
    protected $report_config;     

    /**
     * Office holder will be asigned office for a range of period that starts from period_from
     *  
     *
     * @var Period
     * @ORM\ManyToOne(targetEntity="Application\Entity\Period", fetch="EAGER")
     * @ORM\JoinColumn(name="period_from", referencedColumnName="period_code", nullable=false)
     */
    protected $period_from;

   /**
     * Office holder will be asigned office for a range of period, that ends at period_to
     *  
     *
     * @var Period
     * @ORM\ManyToOne(targetEntity="Application\Entity\Period", fetch="EAGER")
     * @ORM\JoinColumn(name="period_to", referencedColumnName="period_code", nullable=true)
     */
    protected $period_to;


    /**
     * @var Branch
     * @ORM\ManyToOne(targetEntity="Application\Entity\Branch",fetch="EAGER")
     * @ORM\JoinColumn(name="branch_id", referencedColumnName="id", nullable=false)
     * @MaxDepth(1)
     */
    protected $branch;     

    /**
     * @var Department
     * @ORM\ManyToOne(targetEntity="Application\Entity\Department",fetch="EAGER")
     * @ORM\JoinColumn(name="department_id", referencedColumnName="id", nullable=true)
     * @MaxDepth(1)
     */
    protected $department;     

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Application\Entity\User",fetch="EAGER")
     * @ORM\JoinColumn(name="created_by_user_id", referencedColumnName="id", nullable=false)
     * @MaxDepth(1)
     */
    protected $user_created;   

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Application\Entity\User",fetch="EAGER")
     * @ORM\JoinColumn(name="completed_by_user_id", referencedColumnName="id", nullable=true)
     * @MaxDepth(1)
     */
    protected $user_completed;   

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Application\Entity\User",fetch="EAGER")
     * @ORM\JoinColumn(name="modified_by_user_id", referencedColumnName="id", nullable=false)
     * @MaxDepth(1)
     */
    protected $user_modified;   
        
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Application\Entity\User",fetch="EAGER")
     * @ORM\JoinColumn(name="verified_by_user_id", referencedColumnName="id", nullable=true)
     * @MaxDepth(1)
     */
    protected $user_verified;   

       
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Application\Entity\User",fetch="EAGER")
     * @ORM\JoinColumn(name="received_by_user_id", referencedColumnName="id", nullable=true)
     * @MaxDepth(1)
     */
    protected $user_received;   

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=false)
     * @MaxDepth(1)
     */
    protected $date_created;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $date_completed;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $date_modified;
    
    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $date_verified;
    
    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $date_received;      
    
    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    protected $submitted_by_name;      
    
          
    /**
     * @var srtring
     * @ORM\Column(type="string", nullable=false, columnDefinition="ENUM('draft', 'completed','verified','received','deleted')")
     */
     protected $status='draft';//initial status on creation of a report is draft
     
     private $validStatus=array('draft', 'completed','verified','received','deleted');


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
     * @return Report
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
     * Set date_completed
     *
     * @param \DateTime $dateCompleted
     * @return Report
     */
    public function setDateCompleted($dateCompleted)
    {
        $this->date_completed = $dateCompleted;

        return $this;
    }

    /**
     * Get date_completed
     *
     * @return \DateTime 
     */
    public function getDateCompleted()
    {
        return $this->date_completed;
    }

    /**
     * Set date_modified
     *
     * @param \DateTime $dateModified
     * @return Report
     */
    public function setDateModified($dateModified)
    {
        $this->date_modified = $dateModified;

        return $this;
    }

    /**
     * Get date_modified
     *
     * @return \DateTime 
     */
    public function getDateModified()
    {
        return $this->date_modified;
    }

    /**
     * Set date_verified
     *
     * @param \DateTime $dateVerified
     * @return Report
     */
    public function setDateVerified($dateVerified)
    {
        $this->date_verified = $dateVerified;

        return $this;
    }

    /**
     * Get date_verified
     *
     * @return \DateTime 
     */
    public function getDateVerified()
    {
        return $this->date_verified;
    }

    /**
     * Set date_received
     *
     * @param \DateTime $dateReceived
     * @return Report
     */
    public function setDateReceived($dateReceived)
    {
        $this->date_received = $dateReceived;

        return $this;
    }

    /**
     * Get date_received
     *
     * @return \DateTime 
     */
    public function getDateReceived()
    {
        return $this->date_received;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Report
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set report_config
     *
     * @param \Application\Entity\ReportConfig $reportConfig
     * @return Report
     */
    public function setReportConfig(\Application\Entity\ReportConfig $reportConfig)
    {
        $this->report_config = $reportConfig;

        return $this;
    }

    /**
     * Get report_config
     *
     * @return \Application\Entity\ReportConfig 
     */
    public function getReportConfig()
    {
        return $this->report_config;
    }

    /**
     * Set period_from
     *
     * @param \Application\Entity\Period $periodFrom
     * @return Report
     */
    public function setPeriodFrom(\Application\Entity\Period $periodFrom)
    {
        $this->period_from = $periodFrom;

        return $this;
    }

    /**
     * Get period_from
     *
     * @return \Application\Entity\Period 
     */
    public function getPeriodFrom()
    {
        return $this->period_from;
    }

    /**
     * Set period_to
     *
     * @param \Application\Entity\Period $periodTo
     * @return Report
     */
    public function setPeriodTo(\Application\Entity\Period $periodTo = null)
    {
        $this->period_to = $periodTo;

        return $this;
    }

    /**
     * Get period_to
     *
     * @return \Application\Entity\Period 
     */
    public function getPeriodTo()
    {
        return $this->period_to;
    }

    /**
     * Set branch
     *
     * @param \Application\Entity\Branch $branch
     * @return Report
     */
    public function setBranch(\Application\Entity\Branch $branch)
    {
        $this->branch = $branch;

        return $this;
    }

    /**
     * Get branch
     *
     * @return \Application\Entity\Branch 
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     * Set department
     *
     * @param \Application\Entity\Department $department
     * @return Report
     */
    public function setDepartment(\Application\Entity\Department $department = null)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department
     * @MaxDepth(1)
     * @return \Application\Entity\Department 
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * Set user_created
     *
     * @param \Application\Entity\User $userCreated
     * @return Report
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
     * Set user_completed
     *
     * @param \Application\Entity\User $userCompleted
     * @return Report
     */
    public function setUserCompleted(\Application\Entity\User $userCompleted)
    {
        $this->user_completed = $userCompleted;

        return $this;
    }

    /**
     * Get user_completed
     *
     * @return \Application\Entity\User 
     */
    public function getUserCompleted()
    {
        return $this->user_completed;
    }

    /**
     * Set user_modified
     *
     * @param \Application\Entity\User $userModified
     * @return Report
     */
    public function setUserModified(\Application\Entity\User $userModified)
    {
        $this->user_modified = $userModified;

        return $this;
    }

    /**
     * Get user_modified
     *
     * @return \Application\Entity\User 
     */
    public function getUserModified()
    {
        return $this->user_modified;
    }

    /**
     * Set user_verified
     *
     * @param \Application\Entity\User $userVerified
     * @return Report
     */
    public function setUserVerified(\Application\Entity\User $userVerified = null)
    {
        $this->user_verified = $userVerified;

        return $this;
    }

    /**
     * Get user_verified
     *
     * @return \Application\Entity\User 
     */
    public function getUserVerified()
    {
        return $this->user_verified;
    }

    /**
     * Set user_received
     *
     * @param \Application\Entity\User $userReceived
     * @return Report
     */
    public function setUserReceived(\Application\Entity\User $userReceived = null)
    {
        $this->user_received = $userReceived;

        return $this;
    }

    /**
     * Get user_received
     *
     * @return \Application\Entity\User 
     */
    public function getUserReceived()
    {
        return $this->user_received;
    }
    
    public function getReportFlow(){
        $config = $this->getReportConfig()->getConfigArray();
        return $config['report_flow'];
    }
        
    
    public function replaceTokens($text){
        $parent = ($this->getBranch()->getParent())?($this->getBranch()->getParent()):$this->getBranch();
        $tokens = array(
                'branch_head_title' => $this->getBranch()->getBranchHeadTitle(),
                'office_bearer_designation' => $this->getBranch()->getOfficeBearerDesignation(),
                'parent_office_bearer_designation' => $parent->getOfficeBearerDesignation(),
                'office_bearer_title' => $this->getDepartment()->getOfficeBearer(),
            );
        
        foreach ($tokens as $key => $value) {
            $text = preg_replace("/##$key##/", $value, $text);
        }
        return $text;
    }
    
    
    public function getReportName(){
        return $this->getDepartment()->getDepartmentName().", ".$this->getBranch()->getBranchName();
    }
    

    /**
     * Set submittedByName
     *
     * @param string $submittedByName
     *
     * @return Report
     */
    public function setSubmittedByName($submittedByName)
    {
        $this->submitted_by_name = $submittedByName;

        return $this;
    }

    /**
     * Get submittedByName
     *
     * @return string
     */
    public function getSubmittedByName()
    {
        return $this->submitted_by_name;
    }
    
    public function getReportTitle(){
        return $this->branch->getBranchName().'-'.$this->department->getDepartmentName().'-'.$this->period_from->getPeriodCode();        
    }
}
