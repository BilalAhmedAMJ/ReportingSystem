<?php
/**
 *
 */
namespace Application\Entity;
 

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation\MaxDepth;


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
 * An office assignment will also have an additional costraint that we will not allow 
 * @ORM\Entity
 * @ORM\Table(name="office_assignments")
 * @ ORM \ HasLifecycleCallbacks
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 * 
 * @author Haroon
 */
class OfficeAssignment 
{
    
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    
    /**
     * @var Branch
     * @ORM\ManyToOne(targetEntity="Application\Entity\Branch")
     * @ORM\JoinColumn(name="branch_id", referencedColumnName="id", nullable=false)
     * 
     * @MaxDepth(1)
     * 
     */
    protected $branch;     

    /**
     * @var Department
     * @ORM\ManyToOne(targetEntity="Application\Entity\Department")
     * @ORM\JoinColumn(name="department_id", referencedColumnName="id", nullable=false)
     * 
     * @MaxDepth(1)
     * 
     */
    protected $department;     
     
    /**
     * Office holder will be asigned office for a range of period that starts from period_from
     *  
     *
     * @var Period
     * @ORM\ManyToOne(targetEntity="Application\Entity\Period")
     * @ORM\JoinColumn(name="period_from", referencedColumnName="period_code", nullable=false)
     * @MaxDepth(1)
     */
    protected $period_from;

   /**
     * Office holder will be asigned office for a range of period, that ends at period_to
     *  
     *
     * @var Period
     * @ORM\ManyToOne(targetEntity="Application\Entity\Period")
     * @ORM\JoinColumn(name="period_to", referencedColumnName="period_code", nullable=true)
     * @MaxDepth(1)
     */
    protected $period_to;
        
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="Application\Entity\User")
	 * Make sure we allow officeAssinment without uer attached, as we will need this for approvals
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     * 
     * @MaxDepth(1)
     * 
     */
    protected $user;   
        

    /**
     * @var srtring
     * @ORM\Column(type="string", nullable=false, columnDefinition="ENUM('active','approved', 'former','pending','deleted','requested','denied')")
     */
     protected $status;
     
     private $validStatus=array('requested','pending','approved', 'former','deleted','denied','active','disabled');
     private $reportableStatus=array('approved','disabled','active');
    
     /**
      * List of department ids that this office can supervise. ie current office can submitt and edit reports for supervised dept.
      *
      * @var array
      * @ORM\Column(type="simple_array", nullable=true)
      */
     protected $supervise_departments;
    
    /**
      *
      * List of department ids that this office can oversee. ie current office can view reports for supervised dept.
      * 
      * @var array
      * @ORM\Column(type="simple_array", nullable=true)     
     */
     protected $oversee_departments;
    

    public function __construct(){
        $this->status=$this->validStatus[0];     
        $this->supervise_departments=array();
        $this->oversee_departments=array();
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
     * Set status
     *
     * @param string $status
     * @return OfficeAssignment
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
     * Set branch
     *
     * @param \Application\Entity\Branch $branch
     * @return OfficeAssignment
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
     * @return OfficeAssignment
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

    /**
     * Set user
     *
     * @param \Application\Entity\User $user
     * @return OfficeAssignment
     */
    public function setUser(\Application\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Application\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set period_from
     *
     * @param \Application\Entity\Period $periodFrom
     * @return OfficeAssignment
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
     * @return OfficeAssignment
     */
    public function setPeriodTo(\Application\Entity\Period $periodTo)
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
     * 
     * @ORM\PrePersist 
     * @ORM\PreUpdate 
     */
    public function validate(){
        if($this->branch==null||$this->department==null|$this->period_from==null){
                  throw new \InvalidArgumentException(" Not null able property is set to null: branch[".($this->branch==null)."], dept[".($this->department==null)."], period_from[".($this->period_from==null)."]");
        }
        
        if ( ($this->status == null) || ! in_array($this->status, $this->validStatus)){
            throw new \InvalidArgumentException(" Status can't be [$this->status]");
        }
    }


    /**
     * Set supervise_departments
     *
     * @param array $superviseDepartments
     * @return OfficeAssignment
     */
    public function setSuperviseDepartments($superviseDepartments)
    {
        $this->supervise_departments = $superviseDepartments;

        return $this;
    }

    /**
     * Get supervise_departments
     *
     * @return array 
     */
    public function getSuperviseDepartments()
    {
        return $this->supervise_departments;
    }

    /**
     * Set oversee_departments
     *
     * @param array $overseeDepartments
     * @return OfficeAssignment
     */
    public function setOverseeDepartments($overseeDepartments)
    {
        $this->oversee_departments = $overseeDepartments;

        return $this;
    }

    /**
     * Get oversee_departments
     *
     * @return array 
     */
    public function getOverseeDepartments()
    {
        return $this->oversee_departments;
    }
    
    /**
     * TODO Fix hardcoded titles and conditions below
     * We should have a "BranchLevel" title config for each department
     * convert office_bearer to a json config and add config for each branch level
     */
    public function getTitle($branch=false){
        $title = '';
        $department_name = $this->getDepartment()->getDepartmentName();
        $office_holder = $this->getDepartment()->getOfficeBearer();
        if($department_name == $office_holder){
            $title = $office_holder;
        }else{
               $title = $office_holder.' '.$department_name;
        }
        
        if(strpos($department_name,'President')!== false){
           $title = $this->branch->getBranchHeadTitle();
        }

        if($branch){
            if($this->branch->getBranchLevel() == 'Markaz'){
               $title = $this->branch->getOfficeBearerDesignation().' '.$title;
            }else {
                $title = $title.', '.$this->branch->getBranchName();
            }
        }
        return $title;
    }
    
    public function isValid(){
        return 
        $this->status == 'active'
        &&
        $this->user!=null
        &&
        $this->user->isValid();
        
    }
    
    
    public function isReportable(){
        
        return array_search($this->status, $this->reportableStatus);    
        
    }
    
    public function __toString(){
        return $this->department->__toString()."_".$this->branch->__toString()."_".$this->user->__toString();
    }
}
