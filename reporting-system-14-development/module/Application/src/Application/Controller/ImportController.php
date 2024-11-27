<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Crypt\Password\Bcrypt as Bcrypt;
use ZfcUser\Controller\UserController;

class ImportController extends AbstractActionController
{
    
    public function userAction(){
        
        throw new \Exception('Throwing Exception from ImportControler');
        
    }

    public function importMemberAction(){
    	throw new \Exception('In Import Member');

        $member_srv = $this->getServiceLocator()->get('MemberService');
        $CODE=0;$NAME=1;$BRCODE=2;$BRNAME=3;
		$row = 1;
		$errors="";

		if (($handle = fopen("C:\\Users\\if992962\\Development\\workspace\\reporting_system\\data\\mysql\\CodesList.csv", "r")) !== FALSE) {
		    while (($data = fgetcsv($handle, 1000, ",","\"")) !== FALSE) {
		        $num = count($data);
		        if($num !=4) {
		        	$errors .= "<p>Invalid parseing $num for $row: $data[0]<br /></p>\n";
		        }else{
					$list = $member_srv->getMemberByHash(sha3("abc123") );        
					if($list!=null || (is_array($list) && sizeof($list)<1) ){

						$mlist = new MList();

						$mlist->setMemberCode($data[$CODE]);
						$mlist->setStatus('active');
						$mlist->setDisplayName($data[$NAME]);
						$mlist->setLocationCode($data[$BRCODE]);
						$mlist->setBranchName($data[$BRNAME]);

						$member_srv->updateMlist($mlist,$this->current_user() );        
				        $row++;
					}
		        }
	    	}
	    }
	    fclose($handle);

        return  new ViewModel(array('result'=>array('records'=>$row,'errors'=>$errors)) );		
    }
}

    