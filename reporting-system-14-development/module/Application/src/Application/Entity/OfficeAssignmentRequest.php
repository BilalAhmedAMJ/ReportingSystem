<?php
/**
 *
 */
namespace Application\Entity;
 

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation\MaxDepth;

use DoctrineEncrypt\Configuration\Encrypted;

/**
 * TODO : Add trsacking/audit trail for office assignment changes, e.g. created_by_user, date_created, last_modified_by_user, date_last_modified
 */

/**
 * An OfficeAssignment object represents assignment of an office (a department in a branch) 
 * to a user for the specified period. An office assignment can be in one of foolowing states:
 *   approved : User is approved for the given office assignment
 *   former   : User was once approved and held given office assignment
 *   pending  : Office is assigned to the user, however, user is not yet approved or active for the given office assignment
 *   deleted  : Office is assigned instance is meant to be deleted, however, it might still exist due to existance of related entities e.g. reports etc.
 * 
 * An office assignment will also have an additional constraint that we will not allow 
 * @ORM\Entity
 * @ORM\Table(name="office_assignment_requests")
 * @ ORM \ HasLifecycleCallbacks
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 * 
 * @author Haroon
 */
class OfficeAssignmentRequest 
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
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Encrypted
     */
    protected $full_name;

	 /**
     * @var string
     * @ORM\Column(type="string", nullable=false,  length=255)
     * @Encrypted
     */
    protected $email;


  /**
     * @var string
     * @ORM\Column(type="string", nullable=false,  length=255)
     * @Encrypted
     */
    protected $member_code;
    
    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     * @Encrypted
     */
    protected $phone_primary;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     * @Encrypted
     */
    protected $phone_alternate;


    /**
     * @var OfficeAssignment
     * @ORM\ManyToOne(targetEntity="Application\Entity\OfficeAssignment",cascade="all")
     * @ORM\JoinColumn(name="office_assignment_id", referencedColumnName="id", nullable=true)
     * 
     * @MaxDepth(2)
     * 
     */
    protected $office_assignment;   

    /**
     * @var srtring
     * @ORM\Column(type="string")
     */
     protected $status;
     
     private $validStatus=array('requested','pending','approved', 'deleted','denied');

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Application\Entity\User")
     * @ORM\JoinColumn(name="requested_by_user_id", referencedColumnName="id", nullable=false)
     * 
     * @MaxDepth(1)
     */
    protected $requested_by;   
        
    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     * @Encrypted
     */
    protected $request_reason;   
        
    /**
     * @var datetime
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $date_requested;   
    

    /**
     * @var datetime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $date_processed;   


    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Application\Entity\User")
     * @ORM\JoinColumn(name="processed_by_user_id", referencedColumnName="id", nullable=true)
     * 
     * @MaxDepth(1)
     * 
     */
    protected $processed_by;   
        
    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     * @Encrypted
     */
    protected $processor_comments;   

    
    
    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */       
    protected $term;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */       
    protected $expires_period;



  /**
     * @var OfficeAssignment
     * @ORM\ManyToOne(targetEntity="Application\Entity\Branch")
     * @ORM\JoinColumn(name="branch_id", referencedColumnName="id", nullable=false)
     * @MaxDepth(2)
     */               
    protected $branch;
    
    /**
     * @var OfficeAssignment
     * @ORM\ManyToOne(targetEntity="Application\Entity\Department")
     * @ORM\JoinColumn(name="department_id", referencedColumnName="id", nullable=false)
     * @MaxDepth(2)
     */               
    protected $department;

    public function __construct(){
        $this->status=$this->validStatus[0];     
        $this->date_requested = new \DateTime();
        
    }


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
     * Set full_Name
     *
     * @param string $fullName
     * @return OfficeAssignmentRequest
     */
    public function setFullName($fullName)
    {
        $this->full_name = $fullName;

        return $this;
    }

    /**
     * Get full_Name
     *
     * @return string 
     */
    public function getFullName()
    {
        return $this->full_name;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return OfficeAssignmentRequest
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set member_code
     *
     * @param string $memberCode
     * @return OfficeAssignmentRequest
     */
    public function setMemberCode($memberCode)
    {
        $this->member_code = $memberCode;

        return $this;
    }

    /**
     * Get member_code
     *
     * @return string 
     */
    public function getMemberCode()
    {
        return $this->member_code;
    }

    /**
     * Set phone_primary
     *
     * @param string $phonePrimary
     * @return OfficeAssignmentRequest
     */
    public function setPhonePrimary($phonePrimary)
    {
        $this->phone_primary = $phonePrimary;

        return $this;
    }

    /**
     * Get phone_primary
     *
     * @return string 
     */
    public function getPhonePrimary()
    {
        return $this->phone_primary;
    }

    /**
     * Set phone_alternate
     *
     * @param string $phoneAlternate
     * @return OfficeAssignmentRequest
     */
    public function setPhoneAlternate($phoneAlternate)
    {
        $this->phone_alternate = $phoneAlternate;

        return $this;
    }

    /**
     * Get phone_alternate
     *
     * @return string 
     */
    public function getPhoneAlternate()
    {
        return $this->phone_alternate;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return OfficeAssignmentRequest
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
     * Set request_reason
     *
     * @param string $requestReason
     * @return OfficeAssignmentRequest
     */
    public function setRequestReason($requestReason)
    {
        $this->request_reason = $requestReason;

        return $this;
    }

    /**
     * Get request_reason
     *
     * @return string 
     */
    public function getRequestReason()
    {
        return $this->request_reason;
    }

    /**
     * Set date_requested
     *
     * @param \DateTime $dateRequested
     * @return OfficeAssignmentRequest
     */
    public function setDateRequested($dateRequested)
    {
        $this->date_requested = $dateRequested;

        return $this;
    }

    /**
     * Get date_requested
     *
     * @return \DateTime 
     */
    public function getDateRequested()
    {
        return $this->date_requested;
    }

    /**
     * Set date_processed
     *
     * @param \DateTime $dateProcessed
     * @return OfficeAssignmentRequest
     */
    public function setDateProcessed($dateProcessed)
    {
        $this->date_processed = $dateProcessed;

        return $this;
    }

    /**
     * Get date_processed
     *
     * @return \DateTime 
     */
    public function getDateProcessed()
    {
        return $this->date_processed;
    }

    /**
     * Set processor_comments
     *
     * @param string $processorComments
     * @return OfficeAssignmentRequest
     */
    public function setProcessorComments($processorComments)
    {
        $this->processor_comments = $processorComments;

        return $this;
    }

    /**
     * Get processor_comments
     *
     * @return string 
     */
    public function getProcessorComments()
    {
        return $this->processor_comments;
    }

    /**
     * Set office_assignment
     *
     * @param \Application\Entity\OfficeAssignment $officeAssignment
     * @return OfficeAssignmentRequest
     */
    public function setOfficeAssignment(\Application\Entity\OfficeAssignment $officeAssignment = null)
    {
        $this->office_assignment = $officeAssignment;

        return $this;
    }

    /**
     * Get office_assignment
     *
     * @return \Application\Entity\OfficeAssignment 
     */
    public function getOfficeAssignment()
    {
        return $this->office_assignment;
    }

    /**
     * Set requested_by
     *
     * @param \Application\Entity\User $requestedBy
     * @return OfficeAssignmentRequest
     */
    public function setRequestedBy(\Application\Entity\User $requestedBy)
    {
        $this->requested_by = $requestedBy;

        return $this;
    }

    /**
     * Get requested_by
     *
     * @return \Application\Entity\User 
     */
    public function getRequestedBy()
    {
        return $this->requested_by;
    }

    /**
     * Set processed_by
     *
     * @param \Application\Entity\User $processedBy
     * @return OfficeAssignmentRequest
     */
    public function setProcessedBy(\Application\Entity\User $processedBy = null)
    {
        $this->processed_by = $processedBy;

        return $this;
    }

    /**
     * Get processed_by
     *
     * @return \Application\Entity\User 
     */
    public function getProcessedBy()
    {
        return $this->processed_by;
    }



    /**
     * Set term
     *
     * @param string $term
     *
     * @return OfficeAssignmentRequest
     */
    public function setTerm($term)
    {
        $this->term = $term;

        return $this;
    }

    /**
     * Get term
     *
     * @return string
     */
    public function getTerm()
    {
        return $this->term;
    }

    /**
     * Set expiresPeriod
     *
     * @param string $expiresPeriod
     *
     * @return OfficeAssignmentRequest
     */
    public function setExpiresPeriod($expiresPeriod)
    {
        $this->expires_period = $expiresPeriod;

        return $this;
    }

    /**
     * Get expiresPeriod
     *
     * @return string
     */
    public function getExpiresPeriod()
    {
        return $this->expires_period;
    }

    /**
     * Set branch
     *
     * @param \Application\Entity\Branch $branch
     *
     * @return OfficeAssignmentRequest
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
     *
     * @return OfficeAssignmentRequest
     */
    public function setDepartment(\Application\Entity\Department $department)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department
     *
     * @return \Application\Entity\Department
     */
    public function getDepartment()
    {
        return $this->department;
    }
}
