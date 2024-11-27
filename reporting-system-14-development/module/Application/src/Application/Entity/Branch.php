<?php
/**
 *
 */
namespace Application\Entity;
 

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation\MaxDepth;


/**
 * A Branch represents a "unit" of Jama`at that can be a Halqa, local Jama`at, Region, Country Jama`at etc.
 *
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="branches")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 *
 * @author Haroon
 */
class Branch 
{
    
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    
    /**
     * @var srtring
     * @ORM\Column(type="string", length=256)
     */
     protected $branch_name;
     
    /**
     * @var srtring
     * @ORM\Column(type="string", unique=true, length=128)
     */
     protected $branch_code;
     
    /**
     * @var srtring
     * @ORM\Column(type="string", columnDefinition="ENUM('active', 'disabled')")
     */
     protected $status;

    /**
     * @var srtring
     * @ORM\Column(type="string", columnDefinition="ENUM('Markaz', 'Jama`at','Region','Halqa')")
     */
     protected $branch_type;

    //const BranchHirarchy=array('Markaz'=>0, 'Region'=>1,'Jama`at'=>2,'Halqa'=>3);
    /**
     * @var srtring
     * @ORM\Column(type="string", length=256)
     */
     protected $branch_head_title;


    /**
     * @var srtring
     * @ORM\Column(type="string", length=256)
     */
    protected $office_bearer_designation;
        
        
    /**
     * @var srtring
     * @ORM\Column(type="string", length=256)
     */
    protected $branch_level;
                
    /**
     * @var Branch
     * @ORM\ManyToOne(targetEntity="Application\Entity\Branch")
     * @ORM\JoinColumn(name="parent_branch_id", referencedColumnName="id", nullable=true)
     * @MaxDepth(1)
     */
    protected $parent;    


    /**
     * @var Branch
     * @ORM\ManyToMany(targetEntity="Application\Entity\Branch")
     * @ORM\JoinTable(name="branch_area_link",
     *      joinColumns={@ORM\JoinColumn(name="branch_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="branch_area_id", referencedColumnName="id", unique=true)}
     *      )
     * @MaxDepth(1)
     */
    protected $branch_area;    


    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ ORM\ManyToMany(targetEntity="Application\Entity\Branch",fetch="EAGER")
     * @ ORM\JoinTable(name="branch_hierarchy",
     *      joinColumns={@ORM\JoinColumn(name="parent_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="sub_branch_id", referencedColumnName="id")}
     * )
     * @MaxDepth(1)
     */
    protected $sub_branches;    
    
    /**
     * Initialies the roles variable.
     */
    public function __construct()
    {
        $this->sub_branches = new ArrayCollection();
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
     * Set branch_name
     *
     * @param string $branchName
     * @return Branch
     */
    public function setBranchName($branchName)
    {
        $this->branch_name = $branchName;

        return $this;
    }

    /**
     * Get branch_name
     *
     * @return string 
     */
    public function getBranchName()
    {
        return $this->branch_name;
    }

    /**
     * Set branch_code
     *
     * @param string $branchCode
     * @return Branch
     */
    public function setBranchCode($branchCode)
    {
        $this->branch_code = $branchCode;

        return $this;
    }

    /**
     * Get branch_code
     *
     * @return string 
     */
    public function getBranchCode()
    {
        return $this->branch_code;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Branch
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
     * Set branch_type
     *
     * @param string $branchType
     * @return Branch
     */
    public function setBranchType($branchType)
    {
        $this->branch_type = $branchType;

        return $this;
    }

    /**
     * Get branch_type
     *
     * @return string 
     */
    public function getBranchType()
    {
        return $this->branch_type;
    }


    /**
     * Set branch_head_title
     *
     * @param string $branchHeadTitle
     * @return Branch
     */
    public function setBranchHeadTitle($branchHeadTitle)
    {
        $this->branch_head_title = $branchHeadTitle;

        return $this;
    }

    /**
     * Get branch_head_title
     *
     * @return string 
     */
    public function getBranchHeadTitle()
    {
        return $this->branch_head_title;
    }
    
    
        /**
     * Set office_bearer_designation
     *
     * @param string $officeDearerDesignation
     * @return Branch
     */
    public function setOfficeBearerDesignation($officeBearerDesignation)
    {
        $this->office_bearer_designation = $officeBearerDesignation;

        return $this;
    }

    /**
     * Get office_bearer_designation
     *
     * @return string 
     */
    public function getOfficeBearerDesignation()
    {
        return $this->office_bearer_designation;
    }
    

    /**
     * Set parent
     *
     * @param \Application\Entity\Branch $parent
     * @return Branch
     */
    public function setParent(\Application\Entity\Branch $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Application\Entity\Branch 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add sub_branches
     *
     * @param \Application\Entity\Branch $subBranches
     * @return Branch
     */
    public function addSubBranch(\Application\Entity\Branch $subBranches)
    {
        $this->sub_branches[] = $subBranches;

        return $this;
    }

    /**
     * Remove sub_branches
     *
     * @param \Application\Entity\Branch $subBranches
     */
    public function removeSubBranch(\Application\Entity\Branch $subBranches)
    {
        $this->sub_branches->removeElement($subBranches);
    }

    /**
     * Get sub_branches
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSubBranches()
    {
        return $this->sub_branches;
    }

    /**
     * Add branch_area
     *
     * @param \Application\Entity\Branch $branchArea
     * @return Branch
     */
    public function addBranchArea(\Application\Entity\Branch $branchArea)
    {
        $this->branch_area[] = $branchArea;

        return $this;
    }

    /**
     * Remove branch_area
     *
     * @param \Application\Entity\Branch $branchArea
     */
    public function removeBranchArea(\Application\Entity\Branch $branchArea)
    {
        $this->branch_area->removeElement($branchArea);
    }

    /**
     * Get branch_area
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBranchArea()
    {
        return $this->branch_area;
    }
    
    public function hasChildBranches(){
        return strpos($this->branch_head_title,'Amir')>0;
    }

    /**
     * Set branchLevel
     *
     * @param string $branchLevel
     *
     * @return Branch
     */
    public function setBranchLevel($branchLevel)
    {
        $this->branch_level = $branchLevel;

        return $this;
    }

    /**
     * Get branchLevel
     *
     * @return string
     */
    public function getBranchLevel()
    {
        return $this->branch_level;
    }
    
    public function __toString(){
        return sprintf("%s(%s)",
        					  ($this->branch_name?$this->branch_name:'NULL'),$this->id);
    }

}
