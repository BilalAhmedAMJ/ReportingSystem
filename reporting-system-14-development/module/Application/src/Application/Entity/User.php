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
 * An example of how to implement a role aware user entity.
 *
 * @ORM\Entity
 * @ORM\Table(name="users")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 *
 */
class User implements UserInterface, ProviderInterface
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
     * @ORM\Column(name="`username`", type="string", length=255, unique=true, nullable=true)
     * @Encrypted
     */
    protected $username_id;

    /**
     * @var string
     * @ORM\Column(name="email",type="string",  length=255)
     * @Encrypted
     */
    protected $email_address;

    /**
     * @var string
     * @ORM\Column(type="string",  nullable=true)
     * @Encrypted
     */
    protected $display_name;

    /**
     * @var string
     * @ORM\Column(type="string", length=128)
     * @Exclude
     */
    protected $password;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false, columnDefinition="ENUM('active','locked','inactive','deleted','expired')")
     */
    protected $status='active';


    /**
     * @var date
     * @ORM\Column(type="date", nullable=false)
     * 
     */
    protected $password_expiry_date;

    /**
     * @var date
     * @ORM\Column(type="date", nullable=false)
     * 
     */
    protected $password_last_reset;

    /**
     * @var int
     * @ORM\Column(name="member_code",type="string", nullable=false, unique=true)
     * @Encrypted
     */
    protected $membership_code;
    
    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     * @Encrypted
     */
    protected $phone_primary;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     * @Encrypted
     */
    protected $phone_alternate;


    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\ManyToMany(targetEntity="Application\Entity\Role")
     * @ORM\JoinTable(name="user_role_linker",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     */
    protected $roles;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="Application\Entity\OfficeAssignment", 
     *                 mappedBy="user", cascade={"persist"}, orphanRemoval=true,fetch="EAGER")
     * @ORM\JoinTable(name="office_assignments",
     *                joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *               )
     */
    protected $office_assignments;


   /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="Application\Entity\UserLogin", 
     *                  mappedBy="user", cascade={"persist"}, orphanRemoval=true,fetch="EAGER")
     * @ORM\JoinTable(name="user_logins",
     *                joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *               )
     */
    protected $user_logins;


    

    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     */
     private $settings;

     /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     * 
     */
    private $migrated_user_id;     


    private $dynamic_roles;
    /**
     * Initialies ref. associations.
     */
     
     
    /**
     * 
     * @ORM\Column(type="string", nullable=false)
     * 
     */ 
     protected $ehash;

    /**
     * 
     * @ORM\Column(type="string", nullable=false)
     * 
     */ 
     protected $mhash;


    /**
     * 
     * @ORM\Column(type="string", nullable=false)
     * 
     */ 
     protected $uhash;

          
    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->dynamic_roles = new ArrayCollection();
        
        $this->office_assignments = new ArrayCollection();
        $this->user_logins = new ArrayCollection();
        //password for new user is set to 'NULL' so effective creation date is passowrd reset date
        $this->password_last_reset = new \DateTime(); 
        // and init password is also expired as soon as created
        $this->password_expiry_date = new \DateTime(); 
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
     * Get username.
     *
     * @return string
     */
    public function getUsername()
    {
        return strtolower($this->getUsernameId());
    }

    /**
     * Set username.
     *
     * @param string $username
     *
     * @return void
     */
    public function setUsername($username)
    {
        $username = strtolower($username);
        
        $this->setUsernameId($username);
        $this->setUhash(sha3($username));
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return strtolower($this->getEmailAddress() );
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return void
     */
    public function setEmail($email)
    {
        $email = strtolower($email);
        
        $this->setEmailAddress($email);
        $this->setEhash(sha3($email));
    }

    /**
     * Get display_name.
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->display_name;
    }

    /**
     * Set display_name.
     *
     * @param string $display_name
     *
     * @return void
     */
    public function setDisplayName($display_name)
    {
        $this->display_name = $display_name;
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set password.
     *
     * @param string $password
     *
     * @return void
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Get state.
     *
     * @return int
     */
    public function getState()
    {
        //return $this->state;
        return $this->getStatus()=='active';
    }

    /**
     * Set state.
     *
     * @param int $state
     *
     * @return void
     */
    public function setState($state)
    {
        $this->state = $state;
    }


    public function addDynamicRoles($roles){
        if(! $this->dynamic_roles){
            $this->dynamic_roles = new ArrayCollection();
        }
        //now add roles
        foreach ($roles as $role) {
            $this->dynamic_roles->add($role);
        }
        
    }
    
    /**
     * Get role.
     *
     * @return array
     */
    public function getRoles()
    {
        //////////////////var_dump(array_merge($this->dynamic_roles?$this->dynamic_roles->getValues():array(),$this->roles->getValues()),true);
        //return $this->roles->getValues();
        return array_merge($this->dynamic_roles?$this->dynamic_roles->getValues():array(),$this->roles->getValues());
    }

    /**
     * Add a role to the user.
     *
     * @param Role $role
     *
     * @return void
     */
    public function addRole($role)
    {
        $this->roles[] = $role;
    }

    /**
     * Remove roles
     *
     * @param \Application\Entity\Role $roles
     */
    public function removeRole(\Application\Entity\Role $roles)
    {
        $this->roles->removeElement($roles);
    }
    
    
    public function hasRole($role){
        if(!is_string($role)){
            $role = $role->getRoleId();
        }
        foreach($this->getRoles() as $r){
            if($r->getRoleId()==$role){
                return true;
            }
        }
        return false;
    }
    
    public function isAdmin(){
        //error_log("Roles \n\t\t".print_r($this->getRoles(),true));
        return $this->hasRole('sys-admin') || $this->hasRole('admin') || $this->hasRole('national-general-secretary');
    }

    /**
     * Add office_assignments
     *
     * @param \Application\Entity\OfficeAssignment $officeAssignments
     * @return User
     */
    public function addOfficeAssignment(\Application\Entity\OfficeAssignment $officeAssignments)
    {
        $this->office_assignments[] = $officeAssignments;

        return $this;
    }

    /**
     * Remove office_assignments
     *
     * @param \Application\Entity\OfficeAssignment $officeAssignments
     */
    public function removeOfficeAssignment(\Application\Entity\OfficeAssignment $officeAssignments)
    {
        $this->office_assignments->removeElement($officeAssignments);
    }

    /**
     * Get office_assignments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOfficeAssignments()
    {
        return $this->office_assignments;
    }

    /**
     * Add user_logins
     *
     * @param \Application\Entity\UserLogin $userLogin
     * @return User
     */
    public function addUserLogin(\Application\Entity\UserLogin $userLogin)
    {
        $this->user_logins[] = $userLogin;

        return $this;
    }

    /**
     * Remove user_logins
     *
     * @param \Application\Entity\UserLogins $userLogins
     */
    public function removeUserLogin(\Application\Entity\OfficeAssignment $userLogins)
    {
        $this->office_assignments->removeElement($officeAssignments);
    }

    /**
     * Get user_logins
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserLogins()
    {
        return $this->user_logins;
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
     * Set password_expiry_date
     *
     * @param \DateTime $passwordExpiryDate
     * @return User
     */
    public function setPasswordExpiryDate($passwordExpiryDate)
    {
        $this->password_expiry_date = $passwordExpiryDate;

        return $this;
    }

    /**
     * Get password_expiry_date
     *
     * @return \DateTime 
     */
    public function getPasswordExpiryDate()
    {
        return $this->password_expiry_date;
    }

    /**
     * Set password_last_reset
     *
     * @param \DateTime $passwordLastReset
     * @return User
     */
    public function setPasswordLastReset($passwordLastReset)
    {
        $this->password_last_reset = $passwordLastReset;

        return $this;
    }

    /**
     * Get password_last_reset
     *
     * @return \DateTime 
     */
    public function getPasswordLastReset()
    {
        return $this->password_last_reset;
    }

    /**
     * Set member_code
     *
     * @param integer $memberCode
     * @return User
     */
    public function setMemberCode($memberCode)
    {
        $this->setMembershipCode($memberCode);
        $this->setMhash(sha3($memberCode));
        return $this;
    }

    /**
     * Get member_code
     *
     * @return integer 
     */
    public function getMemberCode()
    {
        return $this->getMembershipCode();
    }

    /**
     * Set phone_primary
     *
     * @param string $phonePrimary
     * @return User
     */
    public function setPhonePrimary($phonePrimary)
    {
        $this->phone_primary = $phonePrimary;

        return $this;
    }

    /**
     * Get phone_primary
     *
     * @return string 
     */
    public function getPhonePrimary()
    {
        return $this->phone_primary;
    }

    /**
     * Set phone_alternate
     *
     * @param string $phoneAlternate
     * @return User
     */
    public function setPhoneAlternate($phoneAlternate)
    {
        $this->phone_alternate = $phoneAlternate;

        return $this;
    }

    /**
     * Get phone_alternate
     *
     * @return string 
     */
    public function getPhoneAlternate()
    {
        return $this->phone_alternate;
    }


    /**
     * Set settings
     *
     * @param string $settings
     * @return User
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;

        return $this;
    }

    /**
     * Get settings
     *
     * @return string 
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * Set migrated_user_id
     *
     * @param string $migratedUserId
     * @return User
     */
    public function setMigratedUserId($migratedUserId)
    {
        $this->migrated_user_id = $migratedUserId;

        return $this;
    }

    /**
     * Get migrated_user_id
     *
     * @return string 
     */
    public function getMigratedUserId()
    {
        return $this->migrated_user_id;
    }
    
    /**
     * Return array of roles for the current user
     * Roles are based on two criteria
     * 1- Roles assigned to user, via user_role mapping
     * 2- Roles based on user's office assignments
     * 
     */    
    public function getIdentityRoles(){
        error_log(new \Exception('getIdentityRoles called'));
        /* TODO  for now we use user assigned roles, will add departmental roles once that is figured out */
        return $this->getRoles();
    }

	public function addSetting($key,$value){
		$settingsArray=$this->getSettingsArray();
		$settingsArray[$key]=$value;
		$this->setSettings(json_encode($settingsArray)); 
	}    

	public function getSettingsArray(){
		$settingsArray=array();
		if(!empty($this->settings)){
			$settingsArray = json_decode($this->settings,true);	
		}
		return $settingsArray;
	}
	
    public function getSettingValue($key){
		$settingsArray=$this->getSettingsArray();
		return (is_array($settingsArray)&&key_exists($key, $settingsArray))?$settingsArray[$key]:null;  	
    }
    
    public function isValid(){
        return
        $this->status == 'active'
        &&
        $this->password_expiry_date != null
        &&
        $this->password_expiry_date > new \DateTime();
    }
    
    public function __toString(){
        //return sprintf("User: %s (%s)",$this->id,$this->username_id);
        return "$this->username_id ($this->id)";
    }
    
    

    /**
     * Set usernameId
     *
     * @param string $usernameId
     *
     * @return User
     */
    public function setUsernameId($usernameId)
    {
        $this->username_id = $usernameId;

        return $this;
    }

    /**
     * Get usernameId
     *
     * @return string
     */
    public function getUsernameId()
    {
        return $this->username_id;
    }

    /**
     * Set emailAddress
     *
     * @param string $emailAddress
     *
     * @return User
     */
    public function setEmailAddress($emailAddress)
    {
        $this->email_address = $emailAddress;

        return $this;
    }

    /**
     * Get emailAddress
     *
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->email_address;
    }

    /**
     * Set membershipCode
     *
     * @param integer $membershipCode
     *
     * @return User
     */
    public function setMembershipCode($membershipCode)
    {
        $this->membership_code = $membershipCode;

        return $this;
    }

    /**
     * Get membershipCode
     *
     * @return integer
     */
    public function getMembershipCode()
    {
        return $this->membership_code;
    }

    /**
     * Set ehash
     *
     * @param string $ehash
     *
     * @return User
     */
    public function setEhash($ehash)
    {
        $this->ehash = $ehash;

        return $this;
    }

    /**
     * Get ehash
     *
     * @return string
     */
    public function getEhash()
    {
        return $this->ehash;
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
     * Set uhash
     *
     * @param string $uhash
     *
     * @return User
     */
    public function setUhash($uhash)
    {
        $this->uhash = $uhash;

        return $this;
    }

    /**
     * Get uhash
     *
     * @return string
     */
    public function getUhash()
    {
        return $this->uhash;
    }
    
    /**
     * @VirtualProperty
     */
    public function getOfficeTitles(){
        $titles='';
        if($this->getOfficeAssignments()){
            foreach ($this->office_assignments as $office) {
                if($office->isReportable()){
                    $titles .= ($titles?', ':'').$office->getTitle(true).' ('.$office->getStatus().')';
                }
                
            }
        }
        return $titles;
    }

  public function  update(){

  }
}
