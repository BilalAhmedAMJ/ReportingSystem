<?php
/**
 * Copied from :
 * BjyAuthorize Module (https://github.com/bjyoungblood/BjyAuthorize)
 * 
 * @link https://github.com/bjyoungblood/BjyAuthorize for the canonical source repository
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application\Entity;
 

#use BjyAuthorize\Provider\Identity\ProviderInterface;
use BjyAuthorize\Provider\Role\ProviderInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use ZfcUser\Entity\UserInterface;
use DoctrineEncrypt\Configuration\Encrypted;

use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\MaxDepth;
use JMS\Serializer\Annotation\VirtualProperty;
/**
 * A simple list.
 *
 *
CREATE TABLE `mlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mhash` varchar(255) NOT NULL,
  `location_code` varchar(255) NOT NULL,
  `display_name` text NOT NULL,
  `branch_name` text,
  `membership_code` text NOT NULL,
  `other_info` text,
  `status` enum('active','inactive','deleted','expired') NOT NULL DEFAULT 'active',
  `date_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mlist_un` (`mhash`),
  KEY `mhash` (`mhash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

 *
 * @ORM\Entity
 * @ORM\Table(name="mlist")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 *
 * @author Haroon
 *
 */
class MList 
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
     * @ORM\Column(type="string",  nullable=true)
     * @Encrypted
     */
    protected $display_name;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false, columnDefinition="ENUM('active','locked','inactive','deleted','expired')")
     */
    protected $status='active';

    /**
     * @var int
     * @ORM\Column(name="membership_code",type="string", nullable=false, unique=true)
     * @Encrypted
     */
    protected $membership_code;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=false)
     * @MaxDepth(1)
     */
    protected $date_updated;
    
    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     * @Encrypted
     */
     private $other_info;

    /**
     * 
     * @ORM\Column(name="mhash", type="string", nullable=false)
     * 
     */ 
     protected $mhash;


     /**
      * @var string
      * @ORM\Column(type="string",  nullable=true)
      */
     protected $location_code;
     
     /**
      * @var string
      * @ORM\Column(type="string",  nullable=true)
      * @Encrypted
      */
     protected $branch_name;
     
    public function __construct()
    {

    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id.
     *
     * @param int $id
     *
     * @return void
     */
    public function setId($id)
    {
        $this->id = (int) $id;
    }

    /**
     * Set mhash
     *
     * @param string $mhash
     *
     * @return User
     */
    public function setMhash($mhash)
    {
        $this->mhash = $mhash;
        
        return $this;
    }
    
    /**
     * Get mhash
     *
     * @return string
     */
    public function getMhash()
    {
        return $this->mhash;
    }
    

    /**
     * Set status
     *
     * @param string $status
     * @return User
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
     * Set date_updated
     *
     * @param \DateTime $passwordExpiryDate
     * @return User
     */
    public function setDateUpdated($DateUpdated)
    {
        $this->date_updated = $DateUpdated;

        return $this;
    }

    /**
     * Get date_updated
     *
     * @return \DateTime 
     */
    public function getDateUpdated()
    {
        return $this->date_updated;
    }


    /**
     * Set membership_code
     *
     * @param integer $memberCode
     * @return User
     */
    public function setMemberCode($memberCode)
    {
        $this->membership_code = ($memberCode);
        $this->setMhash(sha3($memberCode));
        return $this;
    }

    /**
     * Get membership_code
     *
     * @return integer 
     */
    public function getMembershipCode()
    {
        return $this->membership_code;
    }


    /**
     * Set settings
     *
     * @param string $settings
     * @return User
     */
    public function setOtherInfo($OtherInfo)
    {
        $this->other_info= $OtherInfo;

        return $this;
    }

    /**
     * Get settings
     *
     * @return string 
     */
    public function getOtherInfo()
    {
        return $this->other_info;
    }

    
    public function __toString(){
        //return sprintf("User: %s (%s)",$this->id,$this->username_id);
        return "$this->membership_code($this->id)";
    }

    /**
     * Set settings
     *
     * @param string $settings
     * @return User
     */
    public function setDisplayName($DisplayName)
    {
        $this->display_name= $DisplayName;
        
        return $this;
    }
    
    /**
     * Get settings
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->display_name;
    }
    
    
    /**
     * Set settings
     *
     * @param string $settings
     * @return User
     */
    public function setLocationCode($LocationCode)
    {
        $this->location_code= $LocationCode;
        
        return $this;
    }
    
    /**
     * Get settings
     *
     * @return string
     */
    public function getLocationCode()
    {
        return $this->location_code;
    }
    
    
    /**
     * Set settings
     *
     * @param string $settings
     * @return User
     */
    public function setBranchName($BranchName)
    {
        $this->branch_name= $BranchName;
        
        return $this;
    }
    
    /**
     * Get settings
     *
     * @return string
     */
    public function getBranchName()
    {
        return $this->branch_name;
    }
    
}
