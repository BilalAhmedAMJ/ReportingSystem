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
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="users_with_logins")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 *
 */
class UserWithLogin extends User
{
    /**
     * @var integer
     * @ORM\Column(name="`logins`", type="integer")
     * @Encrypted
     */
    protected $logins;

    /**
     * @var date
     * @ORM\Column(name="`last_login`", type="datetime")
     * @Encrypted
     */
    protected $last_login;
}
