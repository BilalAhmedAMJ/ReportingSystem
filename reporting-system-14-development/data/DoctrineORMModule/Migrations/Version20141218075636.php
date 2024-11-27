<?php

namespace DoctrineORMModule\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Import old reporter tool users into current tool,
 * ASSUMPTION: 
 *      We already have old users in a table called old_users
 */
class Version20141218075636 extends AbstractMigration
{
    
    public function preUp(Schema $schema){
    	
		$sql = "show tables like 'old_users'";
		$tables= $this->connection->fetchArray($sql);

		if(!($tables)){
			return; //do not migrate users if ther is no old_users table
		}else{
			var_dump($tables);
		}
		  
        $sql="create  table users_staging
            select u_id,user_id,user_email,user_name,'\$2y\$14\$sdUJOMokhABTdMXhDoyzEuKJeBIIbkUZ106/LYRzHvP7im/.gIWbu' as user_password,
            case when status=1 then 'active' when status='0' then 'inactive' end as user_status,expiry_date,now() password_changed,
            case when mem_code is null or mem_code in ('1111','0000','') then 10000000+u_id else mem_code end as mem_code
            ,user_phone,'imported_user_have_no_alt_phone' as alt_phone
             from old_users
            ";        
          $this->connection->executeQuery($sql);
          
          $sql="update users_staging set alt_phone='duplicate_member_code'
                where mem_code in 
                (select mem_code from old_users
                group by mem_code
                having count(mem_code)>1
                )
          ";

          $this->connection->executeQuery($sql);
        
    }
    
    
    public function up(Schema $schema)
    {
		$sql = "show tables like 'old_users'";
		$tables= $this->connection->fetchArray($sql);

		if(!($tables)){
			return; //do not migrate users if ther is no old_users table
		}
    	
        $this->addSql("
        insert into users
                select null, user_id,user_email,user_name, user_password,
             user_status,expiry_date,password_changed,
            mem_code
            ,user_phone,alt_phone
             from users_staging
             where alt_phone='imported_user_have_no_alt_phone'
             and mem_code>=1
             and user_status='active'
             
             
        ");        
    }

    public function postUp(Schema $schema){
		$sql = "show tables like 'old_users'";
		$tables= $this->connection->fetchArray($sql);

		if(!($tables)){
			return; //do not migrate users if ther is no old_users table
		}
    	
    	        $sql="
                drop  table users_staging
            ";        
          $this->connection->executeQuery($sql);

        
    }
        
    public function down(Schema $schema)
    {
        $this->addSql("DELETE FROM users where phone_alternate='imported_user_have_no_alt_phone' ");        
    }
}


/*
select * from users
select * from users

;
select * from migrations;
*/
/*
create temporary table test_users
select null,user_id,user_email,user_name,'$2y$14$sdUJOMokhABTdMXhDoyzEuKJeBIIbkUZ106/LYRzHvP7im/.gIWbu',case when status=1 then 'active' when status='0' then 'inactive' end as status,expiry_date,now(),
case when mem_code is null or mem_code in ('1111','0000','') then 10000000+u_id else mem_code end as mem_code
,user_phone,'imported_user_have_no_alt_phone' from old_users
*/
/*
create temporary table test_users
select null,user_id,user_email,user_name,'$2y$14$sdUJOMokhABTdMXhDoyzEuKJeBIIbkUZ106/LYRzHvP7im/.gIWbu',case when status=1 then 'active' when status='0' then 'inactive' end as status,expiry_date,now(),
case when mem_code is null or mem_code in ('1111','0000','') then 10000000+u_id else mem_code end as mem_code
,user_phone,'imported_user_have_no_alt_phone' from old_users


select min(u_id) as u_id,user_name,mem_code,count(mem_code) from test_users
where status='active'
group by mem_code
having count(mem_code)>1;

 */