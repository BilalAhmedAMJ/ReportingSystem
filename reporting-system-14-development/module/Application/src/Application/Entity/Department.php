<?php
/**
 *
 */
namespace Application\Entity;
 

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation\Groups as SerializerGroups;

use DoctrineEncrypt\Configuration\Encrypted;

/**
 * A department that can be assigned to an office bearer
 *
 * @ORM\Entity
 * @ORM\Table(name="departments")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 * 
 * @author Haroon
 */
class Department  implements \ArrayAccess
{
    
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    
    /**
     * 
     * /@ Assert\NotBlank()
     * @var srtring
     * @ORM\Column(type="string", length=256)
     */
     protected $department_name;
     
    /**
     * @var srtring
     * @ORM\Column(type="string",unique=true, length=128)
     */
     protected $department_code;

    /**
     * @var srtring
     * @ORM\Column(type="string", columnDefinition="ENUM('active', 'disabled')")
     */
    protected $status;     
     
    /**
     * @var srtring
     * @ORM\Column(type="text")
     * 
     * @SerializerGroups({"Details"})
     * 
     * @Encrypted
     * 
     */
     protected $rules;
     

    /**
     * @var srtring
     * @ORM\Column(type="text")
     * 
     * @SerializerGroups({"Details"})
     * 
     * 
     * @Encrypted
     *  
     */
     protected $guide_lines;
     
     
	/**
     * @var srtring
     * @ORM\Column(type="string",length=256)
     * 
	*/
	private $office_bearer;
   

    /**
     * @var string
     * @ORM\Column(type="boolean", nullable=false,options="default=true")
     */
     protected $reportable;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=false,options="default=1000")
     */
     protected $sort_order;


    /**
     * @var string
     * @ORM\Column(type="boolean", nullable=false,options="default=true")
     */
     protected $elected;

    /**
     * @var Department|null
     * @ORM\ManyToOne(targetEntity="Application\Entity\Department")
     * @ORM\JoinColumn(name="assistant_of", referencedColumnName="id", nullable=true)
     */
    protected $assistant_of;



    /**
     * Set id
     *
     * @param integer 
     */
    public function setId($id)
    {
        $this->id=$id;
        return $this;
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
     * Set department_name
     *
     * @param string $departmentName
     * @return Department
     */
    public function setDepartmentName($departmentName)
    {
        $this->department_name = $departmentName;

        return $this;
    }

    /**
     * Get department_name
     *
     * @return string 
     */
    public function getDepartmentName()
    {
        return $this->department_name;
    }

    /**
     * Set department_code
     *
     * @param string $departmentCode
     * @return Department
     */
    public function setDepartmentCode($departmentCode)
    {
        $this->department_code = $departmentCode;

        return $this;
    }

    /**
     * Get department_code
     *
     * @return string 
     */
    public function getDepartmentCode()
    {
        return $this->department_code;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Department
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
     * Get assistant_of
     *
     * @return Department|null
     */
    public function getAssistantOf()
    {
        return $this->assistant_of;
    }

    /**
     * Set assistantOf
     *
     * @param Department|null $assistantOf
     * @return Department
     */
    public function setAssistantOf($assistantOf)
    {
        $this->assistant_of = $assistantOf;
        return $this;
    }

    /**
     * Set rules
     *
     * @param string $rules
     * @return Department
     */
    public function setRules($rules)
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * Get rules
     *
     * @return string 
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Set guide_lines
     *
     * @param string $guideLines
     * @return Department
     */
    public function setGuideLines($guideLines)
    {
        $this->guide_lines = $guideLines;

        return $this;
    }

    /**
     * Get guide_lines
     *
     * @return string 
     */
    public function getGuideLines()
    {
        return $this->guide_lines;
    }

    /**
     * Set reportable
     *
     * @param boolean $reportable
     * @return Department
     */
    public function setReportable($reportable)
    {
        $this->reportable = $reportable;

        return $this;
    }

    /**
     * Get reportable
     *
     * @return boolean 
     */
    public function getReportable()
    {
        return $this->reportable;
    }
    

    /**
     * Set elected
     *
     * @param boolean $elected
     * @return Department
     */
    public function setElected($elected)
    {
        $this->elected = $elected;

        return $this;
    }

    /**
     * Get elected
     *
     * @return boolean 
     */
    public function getElected()
    {
        return $this->elected;
    }
    
    
    /**
     * Set office_bearer
     *
     * @param string $officeBearer
     * @return Department
     */
    public function setOfficeBearer($officeBearer)
    {
        $this->office_bearer = $officeBearer;

        return $this;
    }

    /**
     * Get office_bearer
     *
     * @return string 
     */
    public function getOfficeBearer()
    {
        return $this->office_bearer;
    }
    
	public function getSortOrder(){
		return $this->sort_order;
	}    
    
	public function setSortOrder($so){
		$this->sort_order= $so;
	}    

    public function offsetExists($offset) {
        return isset($this->$offset);
    }

    public function offsetSet($offset, $value) {
        $this->$offset = $value;
    }

    public function offsetGet($offset) {
        return $this->$offset;
    }

    public function offsetUnset($offset) {
        $this->$offset = null;
    }
        
    public function updateFromArray($arrData){
        foreach ($arrData as $key => $value) {
            $this[$key]=$value;
        }
        return $this;
    }
    
    public function toArray(){
        return get_object_vars($this);
    }

    public function __toString(){
		return "$this->department_code ($this->id)";
	}

}
