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
 * @ORM\Table(name="summary_reports")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 *
 * @author Fakhar
 */ 
class SummaryListReport
{


    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
       
    /**
     * question title
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    protected $title;

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
     * Get report title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set report title
     *
     * @param string $title
     * @return string
     */
    public function setTitle( $title )
    {
        $this->title = $title;

        return $this;
    }
}
