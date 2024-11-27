<?php


namespace Application\Service;

use Application\Entity\UserToken;
use Application\Entity\User;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;
use Doctrine\ORM\Query;

use Rhumsaa\Uuid\Uuid;


class MemberService implements FactoryInterface{
    
    private $serviceLocator;
    private $entityManager;
    private $entityFactory;
    private $authService;
    
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator){       
        $this->serviceLocator = $serviceLocator;  
        $this->entityManager = $serviceLocator->get('Doctrine\ORM\EntityManager');
        $this->entityFactory = $this->serviceLocator->get('entityFactory');
        $this->authService = $serviceLocator->get('zfcuser_auth_service');

        return $this;
    }
    
    public function getMemberByHash($mhash){
                
        if(empty($mhash)){
            return null;
        }else{
	    error_log("$mhash"."testestes");
	}
        
        //->createQuery('select mlist from Application\Entity\MList mlist where mlist.mhash = :hash')
        //->setParameter('hash',$mhash)
        $query = $this->entityManager
            ->createQuery('select mlist from Application\Entity\MList mlist where mlist.mhash = :hash')
            ->setParameter('hash',sha3($mhash));

        
        $list = $query->getResult();
       error_log(count($list)); 
       error_log(($list!=null || (is_array($list) && sizeof($list)<1)));
        return $list;
    }

    public function updateMlist($mlist,$user){
                
        if($user->hasRole('sys-admin')){

            $mlist->setDateUpdated(new \DateTime());
            
            $this->entityManager->transactional(function($em) use(&$mlist){
                $em->persist($mlist);
            });
                
            $this->entityManager->flush();
            return true;
        }else{
            return false;
        }
    }
    
}
