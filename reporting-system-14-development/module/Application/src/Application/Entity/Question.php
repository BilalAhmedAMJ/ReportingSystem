<?php
namespace Application\Entity;
 

use Doctrine\ORM\Mapping as ORM;

/**
 * A Branch represents a "unit" of Jama`at that can be a Halqa, local Jama`at, Region, Country Jama`at etc.
 * It was marked as Entity(readOnly=true)
 * 
 * @ORM\Entity
 * @ORM\Table(name="questions")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 *
 * @author Haroon
 */
class Question 
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
     * @ORM\ManyToOne(targetEntity="Application\Entity\ReportConfig",fetch="EAGER")
     * @ORM\JoinColumn(name="report_config", referencedColumnName="report_code", nullable=false)
     */
    protected $report_config;     

    /**
     * @var Department
     * @ORM\ManyToOne(targetEntity="Application\Entity\Department",fetch="EAGER")
     * @ORM\JoinColumn(name="department_id", referencedColumnName="id", nullable=true)
     */
    protected $department;     

    /**
     * @var Question
     * @ORM\ManyToOne(targetEntity="Application\Entity\Question")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     */
    protected $parent;     

    /**
     * Type of quesiton can only be from values identified in question_types array
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $question_type;     
    private $question_types = array('TEXT','MEMO','DATE','SELECT','YES_NO','TABLE','GRID','LABEL','FILE','NUMBER');

    /**
     * Type of answer can only be from values identified in answer_types array
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $answer_type;              
    private $answer_types = array('TEXT','NUMBER','MEMO','DATE','SELECT','YES_NO','NONE','JSON');

    /**
     * Label of quesiton
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $caption;     

    /**
     * Additional details about quesiton, how to fill answer, what are expectations of this answer etc.
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $details;     

    /**
     * Display config is a set of instructions regarding how to display a question for UI
     * It can add more detials to qustion type and provide addtional information.
     * e.g. it can provide inforamtion for 
     * - option list of a drop down selection
     * - a table type question can be defined to be diaplayed in vertical (labels as rows) or horizontal (labels as columns) direction
     * 
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $display_config;     

    /**
     * Constraints for a quesiton include :
     * - rules that answers for the quesiton need to satisfy, validations rules etc.
     * - dependencies between questions of same report (may be even from an other report_config)
     *
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $constraints;     

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $sort_order;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false, options={"default" = true})
     */
    protected $active_question;


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
     * Set question_type
     *
     * @param string $questionType
     * @return Question
     */
    public function setQuestionType($questionType)
    {
        $this->question_type = $questionType;

        return $this;
    }

    /**
     * Get question_type
     *
     * @return string 
     */
    public function getQuestionType()
    {
        return $this->question_type;
    }

    /**
     * Set answer_type
     *
     * @param string $answerType
     * @return Question
     */
    public function setAnswerType($answerType)
    {
        $this->answer_type = $answerType;

        return $this;
    }

    /**
     * Get answer_type
     *
     * @return string 
     */
    public function getAnswerType()
    {
        return $this->answer_type;
    }

    /**
     * Set caption
     *
     * @param string $caption
     * @return Question
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;

        return $this;
    }

    /**
     * Get caption
     *
     * @return string 
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * Set details
     *
     * @param string $details
     * @return Question
     */
    public function setDetails($details)
    {
        $this->details = $details;

        return $this;
    }

    /**
     * Get details
     *
     * @return string 
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Set display_config
     *
     * @param string $displayConfig
     * @return Question
     */
    public function setDisplayConfig($displayConfig)
    {
        $this->display_config = $displayConfig;

        return $this;
    }

    /**
     * Get display_config
     *
     * @return string 
     */
    public function getDisplayConfig()
    {
        return $this->display_config;
    }

    /**
     * Set constraints
     *
     * @param string $constraints
     * @return Question
     */
    public function setConstraints($constraints)
    {
        $this->constraints = $constraints;

        return $this;
    }

    /**
     * Get constraints
     *
     * @return string 
     */
    public function getConstraints()
    {
        return $this->constraints;
    }
    
    public function getConstraint($constraint){
        $constraints = $this->getConstraintsArray();
        if($constraints && key_exists($constraint,$constraints) ){
            return $constraints[$constraint];
        }
        return null;
    }
    public function getConstraintsArray(){
        if($this->getConstraints()){
            return json_decode($this->getConstraints(),true);
        }
        return null;
    }

    /**
     * Set sort_order
     *
     * @param string $sortOrder
     * @return Question
     */
    public function setSortOrder($sortOrder)
    {
        $this->sort_order = $sortOrder;

        return $this;
    }

    /**
     * Get sort_order
     *
     * @return string 
     */
    public function getSortOrder()
    {
        return $this->sort_order;
    }

    /**
     * Set report_config
     *
     * @param \Application\Entity\ReportConfig $reportConfig
     * @return Question
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
     * Set department
     *
     * @param \Application\Entity\Department $department
     * @return Question
     */
    public function setDepartment(\Application\Entity\Department $department = null)
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
     * Set parent
     *
     * @param \Application\Entity\Question $parent
     * @return Question
     */
    public function setParent(\Application\Entity\Question $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Application\Entity\Question 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set active_question
     *
     * @param boolean $activeQuestion
     * @return Question
     */
    public function setActiveQuestion($activeQuestion)
    {
        $this->active_question = $activeQuestion;

        return $this;
    }

    /**
     * Get active_question
     *
     * @return boolean 
     */
    public function getActiveQuestion()
    {
        return $this->active_question;
    }
    
    /**
     * @return true if parent of this question is not null and not equal to this question
     */
    public function isChild(){
        
        return ($this->parent && $this->id && $this->parent->id!=$this->id);
        
    }
}
