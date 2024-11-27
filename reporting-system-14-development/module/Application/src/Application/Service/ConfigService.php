<?php


namespace Application\Service;

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\FactoryInterface;


use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\CompositeExpression;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Doctrine\ORM\Query;

use Doctrine\ORM\QueryBuilder as QueryBuilder;
use Doctrine\ORM\Query\ResultSetMapping;

use ZfcDatagrid\DataSource\Doctrine2 as GridDataSource;


class ConfigService implements FactoryInterface{
    
    private $serviceLocator;
    private $entityManager;
    private $configItems;
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator){       
        $this->serviceLocator = $serviceLocator;  
        $this->entityManager = $serviceLocator->get('Doctrine\ORM\EntityManager');
        
        //Load config at startup
        $this->loadConfig();
        return $this;
    }
    
    protected function loadConfig(){
        $repo=$this->entityManager->getRepository('Application\Entity\Config');
        $items_list=$repo->findAll();
        
        $this->configItems=array();
        
        foreach ($items_list as $item) {
            $value = json_decode($item->getConfigValue(),true);
            if(!$value)
                $value=$item->getConfigValue();
            $this->configItems[$item->getConfigItem()]=$value;    
        }
    }
    
    public function listDepartments($dept_ids=array(),$filters=array()){
                
        //return $this->entityManager->createQuery('select d from Application\Entity\Department d')->getArrayResult();

        /**
         * @var \Doctrine\ORM\QueryBuilder
         **/        
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('d')        
           ->from('Application\Entity\Department','d')
		   ->where(' 1 = 1 ')
           ->orderBy('d.sort_order','ASC')
           ;

        if(is_array($dept_ids) && !(empty($dept_ids))){
            $qb=$qb->andWhere(' d.id in (:dept_ids)');
            $qb->setParameter('dept_ids', $dept_ids);
        }
		
        $dept_obj = $this->serviceLocator->get('entityFactory')->getDepartment();
        
		if(is_array($filters) && !(empty($filters))){
			foreach($filters as $key => $value){
			    //ignore empty filter value as well as query item "q"
				if(empty($value) || ! property_exists($dept_obj,$key) ){
				     continue;
                }
				$qb=$qb->andWhere(' d.'.$key.' = :'.$key);
				$qb->setParameter($key, $value);
			}
		}
        
        $list=$qb->getQuery()->getArrayResult();
        /*
	$depts = $qb->getQuery()->getResult();
        $list=array();
        
        $hydrator = new \DoctrineModule\Stdlib\Hydrator\DoctrineObject($this->entityManager);
        foreach ($depts as $dept) {
            $list[]=$dept->toArray();
        }
        */
        return $list;        
        
    }
    
    public function listDepartmentNames($list_ids=array(),$filters=array()){
		
        $depts = $this->listDepartments($list_ids,$filters);
		
        $values=array();
        
        foreach ($depts as $dept) {
            $values[$dept['id']]=$dept['department_name'];
        }
        
        return $values;
    }


    public function getDepartment($dept_key){
        
        $dept_field=is_numeric($dept_key)?'id':'department_code';
         
        $repo = $this->entityManager->getRepository('Application\Entity\Department');        
        $dept = $repo->findOneBy(array($dept_field=>$dept_key));
        
        return $dept;        
    }
    
    public function updateDepartment($deptData){
        
        $repo = $this->entityManager->getRepository('Application\Entity\Department');
        $dept=null;
        if(is_numeric($deptData['id'])){
            $dept = $repo->find($deptData['id']);
        }else if (isset($deptData['oper']) && ! empty($deptData['oper'])){
            $dept=$this->serviceLocator->get('entity.Department');          
        }else{
            //no update action provided return without update
        }
        $dept->updateFromArray($deptData);
        $this->entityManager->persist($dept);
    }
    
    public function getPeriod($period_code)
    {
        $repo = $this->entityManager->getRepository('Application\Entity\Period');
        $priod = $repo->find(array('period_code'=>$period_code));        
        return $priod;
    }

    public function getConfig($config_item){
        if(key_exists($config_item,$this->configItems)){
            return $this->configItems[$config_item];
        }else{
            return null;
        }
    }
    
    
    public function getConfigValues($item=null){
        if(!is_array($this->configItems)){
            $this->loadConfig();
        }
        $resutl=null;
        if($item!=null){
            $result=$this->configItems[$item];
        }
        else {
            $result=$this->configItems;
        }
        return $result;
    }
    
    
    public function getPeriods($from,$filter='',$sort='desc'){
            
        $repo = $this->entityManager->getRepository('Application\Entity\Period');
        $qb = $repo->createQueryBuilder('p');
        $qb=$qb->orderBy('p.period_start',$sort);
        $qb=$qb->where('p.display_ui = 1 ');

        $oper = '>';
        if($filter == 'past'){
            $oper='<';
        }
        if($from){
           $qb=$qb->andWhere("p.period_start $oper :from");
           $qb=$qb->setParameter('from',$from);
        }
        
        $query=$qb->getQuery();


        return $query->getResult();        
    }
    
    public function getYears($from,$past=true){
        

        $sql = 'SELECT DISTINCT (year_code) AS Year FROM periods';
        
        $oper = '>';
        if($past){
            $oper='<';
        }
        if($from){
            $sql .= " where period_start $oper :from ";
        } 
        $sql .= ' ORDER BY period_start desc ';

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('Year','period_code');
        $qb = $this->entityManager->createNativeQuery($sql,$rsm);  
        
        if($from){
            $qb->setParameter('from',$from);    
        }      
        return $qb->getResult();
        
        /**
         * @var \Doctrine\ORM\QueryBuilder
         **/
        // $qb = $this->entityManager->createQuery();
        // $qb->select('distinct year(p.period_start)');
        // $qb->from('Application\Entity\Period');
        //$query=$qb->getQuery();        
        //return $query->getResult();        
        
    }
    
    
    public function getYearStart(){
        if(key_exists('year_start', $this->configItems)){
            return $this->configItems['year_start'];
        }
        //if no config defined use default year stat
        return Application\Entity\Period::DEFAULT_YEAR_START;
    }
    
    
    public function questionsDataSource(){
       
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('question')        
            ->from('\Application\Entity\Question','question')
            ->join('question.department','department')
            //->join('question.parent','parent')
            ->join('question.report_config','reportconfig')
            ->orderBy('department.id','question.sort_order')                                 
           ;

       // we don't want ORs as first where clause expr
       // otherwise add on expr might cause issues TODO confirm if we really need this
       $qb->where(' 1 = 1 ');

       //$qb->where('( question.id = parent.id or parent.id is null)');
       
       $data_source = new GridDataSource($qb);
        
       return $data_source;
    }
    
    public function getLastMonth($month){
        $now = new \DateTime();
        
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('period')
           ->from('\Application\Entity\Period','period')
           ->where('period.period_start < :now')
           ->andWhere('period.period_code LIKE :month')
           ->setParameter('now',$now)
           ->setParameter('month',"${month}%") 
           ->orderBy('period.period_start','desc')
           ->setMaxResults(1)
        ;
        
        $result = $qb->getQuery()->getResult();
        if(count($result)>0){
            return $result[0];
        }
        
        return null;
    }

    public function getConfigValuesForUser($current_user,$config_item){
      
      $config = $this->getConfigValues($config_item); 

      //check if this is a role based config
      $values = $this->findRoleBasedConfig($config);

      if($config_item == 'dept_allowed_uploaded_docs')
	error_log("Basic Config Values:".$config_item.':'.var_export($config,true). 'ConfigKeys:'.var_export(array_keys($config),true) );
      else
        error_log('ConfItem:'.$config_item);

      $result = array();
      if(is_array($values) && !empty($values) ){

          $roles = $current_user->getRoles();

          $role_keys = array_keys($values);

          foreach ($roles as $role){
            if( in_array($role->getRoleId(),$role_keys) ) {
               $result = array_merge($result,$values[$role->getRoleId()]);
            }
          }

          //user role is not restricted
          if(empty($result)){                          
              $result = $this->findRoleBasedConfig($config,false);
          }

      }else{
         if(!is_array($values) || empty($values)){
             error_log('SimpleConfig:'.is_array($values). ' OR is empty?'.empty($values) );
             $result = $config;
         }
      }

      if($config_item == 'dept_allowed_uploaded_docs')
      	error_log("Final Config Values:".$config_item.':'.var_export($result,true));

      return $result;

    }
    
    private function findRoleBasedConfig($config_values,$return_role_based_config=true){
      $result = array();


      //config is key value and we have a role base key
      if($return_role_based_config && is_array($config_values) 
          && array_key_exists('role_based_config',$config_values))
      {
          $result=$config_values['role_based_config'];

      }elseif( (! $return_role_based_config) && is_array($config_values) 
          && array_key_exists('role_based_config',$config_values))
      {

          $result=$config_values;
          unset($result['role_based_config']);
          
      }elseif (is_array($config_values)) {
      //config is array and one of the elements is nested array of role base config
          //index of role_based config
          $index=-1;
          foreach ($config_values as $key=>$value){
              if (is_array($value) && in_array('role_based_config', array_keys($value) )) {
                  $result = $value['role_based_config'];
                  $index=$key;
              }
          }
          if (! $return_role_based_config){
              $result = $config_values;
              if($index >= 0){
                unset($result[$index]);
              }
          }
      }

error_log("Role Based Config Value:".var_export($config_values,true));

      return $result;
  }
    
}
