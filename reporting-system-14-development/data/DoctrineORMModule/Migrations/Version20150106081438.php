<?php

namespace DoctrineORMModule\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * 
 * Import reports and office assignments
 * 
 */
class Version20150106081438 extends AbstractMigration
{
    
    public function up(Schema $schema)
    {
        
        $sql = "show tables like 'old_reports'";
        $tables= $this->connection->fetchArray($sql);

        if(!($tables)){
            return; //do not migrate users if ther is no old_users table
        }
                
        /*Add dpeartment of President and VP */
        $sql="REPLACE INTO `departments` (`id`, `department_name`, `department_code`, `rules`, `guide_lines`, `status`,`reportable`)
              VALUES
                (26, 'President', 'P', '', '', 'active',0),
                (27, 'Vice President', 'VP', '', '', 'active',0)";
        $this->connection->executeQuery($sql);         

        /* create office assignemnts*/
        $sql="
            create table staging_offices
            
            select old_users.branch_code, branches.id as branch_id, user_type as department_code, departments.id as department_id,
            'Jan-2015' as periodFrom,'Jun-2016' as periodTo, user_id as username,users.id as user_id,
            'approved' as status
            from old_users
            join users on cast(users.username as char) = old_users.user_id
            join branches on cast(branches.branch_code as char) = old_users.branch_code
            left outer join departments on trim(cast(departments.department_code as char)) = trim(cast(old_users.user_type as char))
        ";
        $this->connection->executeQuery($sql);         

        /* update remaining departments */
        $sql = "
            update staging_offices set department_id = case when department_code='WS' then 21 
                                                when department_code='FE' then 4 
                                                when department_code='RA' then 9 
                                                when department_code='TI' then 23 
                                                when department_code='SM' then 1 
                                                end where department_code in ('WS','FE','RA','TI','SM')        
        ";

        $this->connection->executeQuery($sql);         

        /* delete exisitng  office assignments adn dependencies*/
        $sql = "
            delete from office_assignment_requests
        ";

        $this->connection->executeQuery($sql);         

        $sql = "delete from office_assignments
        ";

        $this->connection->executeQuery($sql);         

        /* import office assignments*/
        $sql = "
                insert into office_assignments
                select null,branch_id,department_id,periodfrom,user_id,status,periodto,'' as supervise,'' as oversee
                from staging_offices
                where department_id is not null
                ";
        
        $this->connection->executeQuery($sql);         

        /* update oversee and supervise*/
        $sql = "
                update office_assignments set supervise_departments = '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,222,23,24' 
                                                where department_id = 16
                ";
                
        $this->connection->executeQuery($sql);         

        $sql = "
                update office_assignments set supervise_departments = '1,2,3,4,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,222,23,24'
                                                where  department_id = 5
            ";
            
        $this->connection->executeQuery($sql);         


        /* update oversee and supervise*/
                                                     
        $sql = "
        update office_assignments set oversee_departments =  '1,2,3,4,6,7,8,9,10,11,12,13,14,15,16,17,18' 
                                                where department_id = 27
        ";
                                          
        $this->connection->executeQuery($sql);         


        /*Done drop staging table now*/
        /*Add reports config for Local level */
        $sql=" drop table staging_offices";
        $this->connection->executeQuery($sql);         
            
    }


    public function preUp(Schema $schema)
    {            
        $sql = "show tables like 'old_reports'";
        $tables= $this->connection->fetchArray($sql);

        if(!($tables)){
            return; //do not migrate users if ther is no old_users table
        }
        
        
        $sql = "
            REPLACE INTO `periods` (`period_code`, `year_code`, `period_start`, `period_end`)
            VALUES
                ('Jul-2015', '2015-2016', '2014-07-01 00:00:00', '2015-07-31 23:59:59'),
                ('Aug-2015', '2015-2016', '2015-08-01 00:00:00', '2015-08-31 23:59:59'),
                ('Sep-2015', '2015-2016', '2015-09-01 00:00:00', '2015-09-30 23:59:59'),
                ('OCt-2015', '2015-2016', '2015-10-01 00:00:00', '2015-10-31 23:59:59'),
                ('Nov-2015', '2015-2016', '2015-11-01 00:00:00', '2015-11-30 23:59:59'),
                ('Dec-2015', '2015-2016', '2015-12-01 00:00:00', '2015-12-31 23:59:59'),
                ('Jan-2016', '2015-2016', '2016-01-01 00:00:00', '2016-01-31 23:59:59'),
                ('Feb-2016', '2015-2016', '2016-02-01 00:00:00', '2016-02-28 23:59:59'),
                ('Mar-2016', '2015-2016', '2016-03-01 00:00:00', '2016-03-31 23:59:59'),
                ('Apr-2016', '2015-2016', '2016-04-01 00:00:00', '2016-04-30 23:59:59'),
                ('May-2016', '2015-2016', '2016-05-01 00:00:00', '2016-05-31 23:59:59'),
                ('Jun-2016', '2015-2016', '2016-06-01 00:00:00', '2016-06-30 23:59:59')
        ";
        $this->connection->executeQuery($sql);  

     }


    public function postUp(Schema $schema)
    {
            
        /*Import duplicate users and their offices*/
        
            
        $sql = "show tables like 'old_reports'";
        $tables= $this->connection->fetchArray($sql);

        if(!($tables)){
            return; //do not migrate users if ther is no old_users table
        }
        
        /* First Import duplicate users*/

        $sql="
            create  table users_staging_dupl
        
            select (1000000+u_id) as u_id,group_concat(user_id) as user_id,user_email,user_name,'$2y$14$sdUJOMokhABTdMXhDoyzEuKJeBIIbkUZ106/LYRzHvP7im/.gIWbu' as user_password,
            case when status=1 then 'active' when status='0' then 'inactive' end as user_status,max(expiry_date) as expiry_date,now() password_changed,
            case when mem_code is null or mem_code in ('1111','0000','') then 10000000+u_id else mem_code end as mem_code
            ,max(user_phone) as user_phone,'imported_dupl_user_have_no_alt_phone' as alt_phone
                    
            from old_users 
            where user_name!='Nil'
            group by user_name, user_email, mem_code,status
            having count(u_id)>1
        ";            
        $this->connection->executeQuery($sql);  
            
         //fix username
        $sql= "update  users_staging_dupl set user_id= replace(user_id,',','.')" ;
        $this->connection->executeQuery($sql);  

        $sql="
            update users_staging_dupl 
            set alt_phone='already_imported_match_offices'
            where mem_code in (select member_code from users)
            ";//ignore users that are already imported
        $this->connection->executeQuery($sql);  
        
        //import users
        $sql="
            insert into users
             select null, user_id,user_email,user_name, user_password,
             user_status,expiry_date,password_changed,   mem_code
            ,user_phone,alt_phone
             from users_staging_dupl
             where alt_phone='imported_dupl_user_have_no_alt_phone'
             and mem_code>=1
             and user_status='active'
        ";            
        $this->connection->executeQuery($sql);  
                    
            
        /*Add user roles for non General secratery*/
        $sql="
            insert into user_role_linker
            select distinct user_id,19 from office_assignments where department_id not in (5,25,26,27);
        ";
          $this->connection->executeQuery($sql);  
                
        /*Add user roles for non General secratery*/
         $sql="
            insert into user_role_linker
            select distinct user_id,15 from office_assignments where department_id  in (5);
        ";
        
        
          $this->connection->executeQuery($sql);  
                    
            
        /* Add reports*/
        $sql="
        create table staging_reports
        select rid as report_id, date_posted,branch_code, report_code, concat(left(date_format(date(concat(year,'-',case when month<10 then '0' else '' end,month,'-01')),'%M'),3),'-',year) as period,
        prepared_by, 0 as user_id, 0 as department_id, 0 as branch_id
         from old_reports.ami_all_reports r 
        where year >= 2014
        ";
        
        $this->connection->executeQuery($sql);  
        $sql="alter table staging_reports 
        ";
        $this->connection->executeQuery($sql);  
        
        $sql="add index(`prepared_by`),
        ";
        $this->connection->executeQuery($sql);  
                
        $sql="add index(`report_code`),
        ";
        $this->connection->executeQuery($sql);  
        
        $sql="add index(`branch_code`)
        ";
        $this->connection->executeQuery($sql);  
    }


    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
