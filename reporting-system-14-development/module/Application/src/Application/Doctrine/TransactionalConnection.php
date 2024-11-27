<?php

namespace Application\Doctrine;

use Doctrine\DBAL\Connection as DoctrineDBALConnection;
use PDO;

/**
 * Wrapper class that ensures all connections made will set "auto-commit"
 * to false. 
 *
 * @author Haroon
 */
class TransactionalConnection extends DoctrineDBALConnection {
    
    public function connect(){
        
        $result = parent::connect();
        
        if($result){
            $this->_conn->setAttribute(PDO::ATTR_AUTOCOMMIT,false);
        }
        
        return $result;    
    }
    
    public function getConnection(){
        
        return $this->_conn;
    }
}
