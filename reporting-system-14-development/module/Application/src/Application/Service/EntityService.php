<?php


namespace Application\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;

use Doctrine\ORM\Query\ResultSetMapping;

use Doctrine\ORM\Query;


class EntityService implements FactoryInterface{
    
    private $serviceLocator;
    private $entityManager;
    private $arrayObjHydrator;
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator){       
        $this->serviceLocator = $serviceLocator;  
        $this->arrayObjHydrator = new \Zend\Stdlib\Hydrator\ClassMethods(true);
        $this->entityManager = $serviceLocator->get('Doctrine\ORM\EntityManager');

        return $this;
    }
    
    public function getObject($entity,$id){
        $repo = $this->entityManager->getRepository('Application\Entity\\'.$entity);
        return $repo->find($id);
    }    

    public function findBy($entity,$criteria,$order_by=null){
        $repo = $this->entityManager->getRepository('Application\Entity\\'.$entity);
        return $repo->findBy($criteria,$order_by);
    }    

    public function findOneBy($entity,$criteria){
        $repo = $this->entityManager->getRepository('Application\Entity\\'.$entity);
        return $repo->findOneBy($criteria);
    }    
    
    public function findAll($entity){
        $repo = $this->entityManager->getRepository('Application\Entity\\'.$entity);
        return $repo->findAll();
    }    

    public function saveObject($obj,$data=array()){
        if(! empty($data)){
            $this->arrayObjHydrator->hydrate();
        }
        
        $this->entityManager->persist($obj);
        $this->entityManager->flush();
    }
	
	
	
	public function runNativeQuery($query){

		$stmt = $this->entityManager->getConnection()->prepare($query);
		$stmt->execute();
		$result = $stmt->fetchAll();
		
		return  $result;
	}
}
