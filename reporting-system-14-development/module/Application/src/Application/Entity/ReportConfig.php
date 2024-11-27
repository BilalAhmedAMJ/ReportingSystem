<?php
 
namespace Application\Entity;
 

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;



/**
 * A report config holds configuration items for a report, that is to be submitted by users
 *
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="report_config")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 *
 * @author Haroon
 */
class ReportConfig 
{
    
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     */
    protected $report_code;

    
    /**
     * @var srtring
     * @ORM\Column(type="string", length=256)
     */
     protected $report_name;
     
    /**
     * @var srtring
     * @ORM\Column(type="simple_array")
     */
     protected $levels;

    /**
     * @var srtring
     * @ORM\Column(type="string")
     */
     protected $freq;

    /**
     * @var srtring
     * @ORM\Column(type="simple_array")
     */
     protected $role_create;

    /**
     * @var srtring
     * @ORM\Column(type="simple_array")
     */
     protected $role_view;

    /**
     * @var srtring
     * @ORM\Column(type="simple_array")
     */
     protected $role_verify;

    /**
     * @var srtring
     * @ORM\Column(type="simple_array")
     */
     protected $role_receive;

    /**
     * @var srtring
     * @ORM\Column(type="simple_array")
     */
     protected $departments;

     
    /**
     * @var srtring
     * @ORM\Column(type="string", columnDefinition="ENUM('active', 'disabled')")
     */
     protected $status;


    /**
     * @var srtring
     * @ORM\Column(type="string")
     */
     protected $config;

     
    public function __construct()
    {
        $this->levels = array();
        $this->role_create=aray();
        $this->role_view=aray();
        $this->role_verify=aray();
        $this->role_receive=aray();
    }

    /**
     * Set report_code
     *
     * @param string $reportCode
     * @return ReportConfig
     */
    public function setReportCode($reportCode)
    {
        $this->report_code = $reportCode;

        return $this;
    }

    /**
     * Get report_code
     *
     * @return string 
     */
    public function getReportCode()
    {
        return $this->report_code;
    }

    /**
     * Set report_name
     *
     * @param string $reportName
     * @return ReportConfig
     */
    public function setReportName($reportName)
    {
        $this->report_name = $reportName;

        return $this;
    }

    /**
     * Get report_name
     *
     * @return string 
     */
    public function getReportName()
    {
        return $this->report_name;
    }

    /**
     * Set levels
     *
     * @param array $levels
     * @return ReportConfig
     */
    public function setLevels($levels)
    {
        $this->levels = $levels;

        return $this;
    }

    /**
     * Get levels
     *
     * @return array 
     */
    public function getLevels()
    {
        return $this->levels;
    }

    /**
     * Set freq
     *
     * @param string $freq
     * @return ReportConfig
     */
    public function setFreq($freq)
    {
        $this->freq = $freq;

        return $this;
    }

    /**
     * Get freq
     *
     * @return string 
     */
    public function getFreq()
    {
        return $this->freq;
    }

    /**
     * Set role_create
     *
     * @param array $roleCreate
     * @return ReportConfig
     */
    public function setRoleCreate($roleCreate)
    {
        $this->role_create = $roleCreate;

        return $this;
    }

    /**
     * Get role_create
     *
     * @return array 
     */
    public function getRoleCreate()
    {
        return $this->role_create;
    }

    /**
     * Set role_view
     *
     * @param array $roleView
     * @return ReportConfig
     */
    public function setRoleView($roleView)
    {
        $this->role_view = $roleView;

        return $this;
    }

    /**
     * Get role_view
     *
     * @return array 
     */
    public function getRoleView()
    {
        return $this->role_view;
    }

    /**
     * Set role_verify
     *
     * @param array $roleVerify
     * @return ReportConfig
     */
    public function setRoleVerify($roleVerify)
    {
        $this->role_verify = $roleVerify;

        return $this;
    }

    /**
     * Get role_verify
     *
     * @return array 
     */
    public function getRoleVerify()
    {
        return $this->role_verify;
    }

    /**
     * Set role_receive
     *
     * @param array $roleReceive
     * @return ReportConfig
     */
    public function setRoleReceive($roleReceive)
    {
        $this->role_receive = $roleReceive;

        return $this;
    }

    /**
     * Get role_receive
     *
     * @return array 
     */
    public function getRoleReceive()
    {
        return $this->role_receive;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return ReportConfig
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
     * Set departments
     *
     * @param array $departments
     * @return ReportConfig
     */
    public function setDepartments($departments)
    {
        $this->departments = $departments;

        return $this;
    }

    /**
     * Get departments
     *
     * @return array 
     */
    public function getDepartments()
    {
        return $this->departments;
    }
    
    /**
     * Set departments
     *
     * @param array $config     
     * @return ReportConfig
     * 
     */
    public function setConfig($config)
    {
        $this->config = $config;
        
        return $this;
    }

    /**
     * Get departments
     *
     * @return array 
     */
    public function getConfig()
    {
        return $this->config;
    }

    private $config_array;
    public function getConfigArray(){
        if(!$this->config_array){
            $this->config_array = json_decode($this->config,true);            
        }

        return $this->config_array;
    }

    /**
     *  Returns a ending period for current report config that is based on report freq
     *  TODO: Fix returning value for freq other than monthly
     *  
     */
     public function getReportEndingPeriod($period_from){
         return $period_from;
     }
}
