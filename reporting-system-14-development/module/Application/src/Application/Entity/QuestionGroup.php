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
 * @ORM\Table(name="question_group")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 *
 * @author Fakhar
 */ 
class QuestionGroup 
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
     * @ORM\Column(type="string", nullable=false, length=255, unique=true)
     */
    protected $group_name;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }    
}
