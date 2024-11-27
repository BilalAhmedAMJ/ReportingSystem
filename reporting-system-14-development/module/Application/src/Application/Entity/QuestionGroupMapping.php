<?php
/**
 *
 */
namespace Application\Entity;
 
use Doctrine\ORM\Mapping as ORM;

/**
 * Group Similat similar (same) questions together
 * spread accros different departments
 * 
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="question_group_mapping")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 *
 * @author Fakhar
 */ 
class QuestionGroupMapping 
{


    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var QuestionGroup
     * @ORM\ManyToOne(targetEntity="Application\Entity\QuestionGroup",fetch="EAGER")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", nullable=false)
     */
    protected $group;
    
    /**
     * @var QuestionGroup
     * @ORM\ManyToOne(targetEntity="Application\Entity\Question",fetch="EAGER")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id", nullable=false)
     */
    protected $question;

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
     * Get Question
     *
     * @return \Application\Entity\Question 
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set parent
     *
     * @param \Application\Entity\Question $question
     * @return Question
     */
    public function setQuestion(\Application\Entity\Question $question = null)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get Group
     *
     * @return \Application\Entity\QuestionGroup 
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set parent
     *
     * @param \Application\Entity\QuestionGroup $group
     * @return QuestionGroup
     */
    public function setGroup(\Application\Entity\QuestionGroup $group = null)
    {
        $this->group = $group;

        return $this;
    }

    public function getGroupQuestions()
    {

    }

}
