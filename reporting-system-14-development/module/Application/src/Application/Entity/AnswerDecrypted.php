<?php
namespace Application\Entity;
 

use Doctrine\ORM\Mapping as ORM;


use DoctrineEncrypt\Configuration\Encrypted;

 
 /**
 *
 * An Answer represents a "response" from auser for a question for a particular report
 * 
 * @ORM\Entity
 * @ORM\Table(name="answers")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 *
 * @author Haroon
 */
class AnswerDecrypted 
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
     * @var Question
     * @ORM\ManyToOne(targetEntity="Application\Entity\Question",fetch="EAGER")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id", nullable=true)
     */
    protected $question;
    
    
    /**
     * Label of quesiton
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    protected $caption;     


    /**
     * Response from user for linked question for given report
     * @var string
     * @ORM\Column(type="string", nullable=true)
     * 
     * @Encrypted
     * 
     */
    protected $value;     


    /**
     * Response from user for linked question for given report
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $answer_number=1;//first answer is always answer_number_1
             

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        return $this->id = $id;
    }

    /**
     * Set caption
     *
     * @param string $caption
     * @return Answer
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
     * Set value
     *
     * @param string $value
     * @return Answer
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set report
     *
     * @param \Application\Entity\Report $report
     * @return Answer
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
     * Set question
     *
     * @param \Application\Entity\Question $question
     * @return Answer
     */
    public function setQuestion(\Application\Entity\Question $question = null)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return \Application\Entity\Question 
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set answer_number
     *
     * @param integer $answerNumber
     * @return Answer
     */
    public function setAnswerNumber($answerNumber)
    {
        $this->answer_number = $answerNumber;

        return $this;
    }

    /**
     * Get answer_number
     *
     * @return integer 
     */
    public function getAnswerNumber()
    {
        return $this->answer_number;
    }
    
    
    public function hasChildren(){
        return count($this->getAnswersForChildQuestions())>0;
    }    
    private $answersForChildQuestions = array();
    
    public function setAnswersForChildQuestions($answersForChildQuestions){
        $this->answersForChildQuestions=$answersForChildQuestions;
    }

    public function getAnswersForChildQuestions(){
        return $this->answersForChildQuestions;
    }
    
    public function getAnswerKey(){
        if( ! $this->question || ! $this->question->getId() || $this->answer_number <1 ){
            throw new \InvalidArgumentException("Question or answer number for this answer is not correct");
        }
        
        return 'Q_'.$this->question->getId().'_'.$this->answer_number;
    }
}
