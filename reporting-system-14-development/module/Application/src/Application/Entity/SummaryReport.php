<?php
/**
 *
 */
namespace Application\Entity;
 
use Doctrine\ORM\Mapping as ORM;

/**
 * Summary Report Questions
 * 
 * @ORM\Entity(readOnly=false)
 * @ORM\Table(name="summary_report_questions")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 *
 * @author Fakhar
 */ 
class SummaryReport
{


    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * report id
     * @var string
     * @ORM\Column(type="integer", nullable=false)
     */

    /**
     * @var Report
     * @ORM\ManyToOne(targetEntity="Application\Entity\SummaryListReport",fetch="EAGER")
     * @ORM\JoinColumn(name="report_id", referencedColumnName="id", nullable=false)
     */
    protected $report_id; 

    /**
     * Question IDs
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    protected $question_id;
       
    /**
     * question caption
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    protected $caption;

    /**
     * Action required on questions
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    protected $action;

    /**
     * Branch Level on which action need to apply
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    protected $level;    

    /**
     * Condition to check against answers
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $question_condition;

    /**
     * ondition to check against answers
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $condition_value;    

    /**
     * Sort order of question in Summary Report
     * @var string
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $sort_order; 
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
     * Get question ids
     *
     * @return string 
     */
    public function getQuestion()
    {
        return $this->question_id;
    }

    /**
     * Set Question IDs
     *
     * @param string $answerType
     * @return string
     */
    public function setQuestion( $question_ids )
    {
        $this->question_id = $question_ids;

        return $this;
    }

   /**
     * Get report id
     *
     * @return string 
     */
    public function getReport()
    {
        return $this->report_id;
    }

    /**
     * Set Report
     *
     * @param string $report_id
     * @return string
     */
    public function setReport( $report_id )
    {
        $this->report_id = $report_id;

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
     * Set caption
     *
     * @param string $caption
     * @return string
     */
    public function setCaption( $caption )
    {
        $this->caption = $caption;

        return $this;
    }

   /**
     * Get action
     *
     * @return string 
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set action
     *
     * @param string $action
     * @return string
     */
    public function setAction( $action )
    {
        $this->action = $action;

        return $this;
    }

   /**
     * Get Level
     *
     * @return string 
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set level
     *
     * @param string $level
     * @return string
     */
    public function setLevel( $level )
    {
        $this->level = $level;

        return $this;
    }

       /**
     * Get condition
     *
     * @return string 
     */
    public function getCondition()
    {
        return $this->question_condition;
    }

    /**
     * Set condition
     *
     * @param string $question_condition
     * @return string
     */
    public function setCondition( $question_condition )
    {
        $this->question_condition = $question_condition;

        return $this;
    }

   /**
     * Get Condition value
     *
     * @return string 
     */
    public function getConditionValue()
    {
        return $this->condition_value;
    }

    /**
     * Set Condition value
     *
     * @param string $condition_value
     * @return string
     */
    public function setConditionValue( $condition_value )
    {
        $this->condition_value = $condition_value;

        return $this;
    }    
   /**
     * Get sort order
     *
     * @return string 
     */
    public function getSortOrder()
    {
        return $this->sort_order;
    }

    /**
     * Set level
     *
     * @param string $sort_order
     * @return string
     */
    public function setSortOrder( $sort_order )
    {
        $this->sort_order = $sort_order;

        return $this;
    }    
}
