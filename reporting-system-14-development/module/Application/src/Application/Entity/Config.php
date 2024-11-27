<?php
/**
 *
 */
namespace Application\Entity;
 

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;



/**
 * A Branch represents a "unit" of Jama`at that can be a Halqa, local Jama`at, Region, Country Jama`at etc.
 *
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="config")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 *
 * @author Haroon
 */
class Config 
{
    
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     */
    protected $config_item;

    
    /**
     * @var srtring
     * @ORM\Column(type="text")
     */
     protected $config_value;
     

    /**
     * Set config_item
     *
     * @param string $configItem
     * @return Config
     */
    public function setConfigItem($configItem)
    {
        $this->config_item = $configItem;

        return $this;
    }

    /**
     * Get config_item
     *
     * @return string 
     */
    public function getConfigItem()
    {
        return $this->config_item;
    }

    /**
     * Set config_value
     *
     * @param string $configValue
     * @return Config
     */
    public function setConfigValue($configValue)
    {
        $this->config_value = $configValue;

        return $this;
    }

    /**
     * Get config_value
     *
     * @return string 
     */
    public function getConfigValue()
    {
        return $this->config_value;
    }
}
