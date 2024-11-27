<?php

namespace DoctrineORMModule\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150519072459 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $sql="CREATE OR REPLACE ALGORITHM=MERGE SQL SECURITY INVOKER VIEW `reportswithperiod` AS
              SELECT * FROM 
              reports r JOIN periods p ON p.period_code=r.period_from
        ";

        $this->addSql($sql);
        
        $sql = "
            CREATE OR REPLACE ALGORITHM=MERGE SQL SECURITY INVOKER VIEW `offices_with_details` AS
            
            SELECT b.`branch_name`, b.branch_code,d.department_name,d.department_code, 
            u.display_name,u.email, u.status user_status,u.phone_primary,u.phone_alternate,u.username,
            oa.*
            FROM 
            office_assignments oa JOIN departments d ON d.id = oa.department_id JOIN branches b ON b.id=oa.branch_id JOIN users u ON u.id=oa.user_id
        ";
        
        $this->addSql($sql);
        
        $sql = "
        CREATE OR REPLACE ALGORITHM=MERGE SQL SECURITY INVOKER VIEW `reports_submission_reports` AS
        
        
        SELECT owd.branch_id office_branch_id,branch_name,branch_code,owd.department_id AS office_department_id,department_name, department_code, 
        display_name office_bearer, email, phone_primary, phone_alternate, username, owd.period_to AS office_expiry,p.period_code, p.period_start,
        p.period_end, r.id AS report_id, r.period_from, r.period_to, created_by_user_id, completed_by_user_id, modified_by_user_id, verified_by_user_id, 
        received_by_user_id, date_created, date_completed, date_modified, date_verified, date_received, ifnull(r.STATUS,'outstanding') AS report_status
        
         FROM 
        reports r JOIN periods p ON p.`period_code` = r.period_from
        JOIN offices_with_details owd ON owd.department_id=r.department_id AND owd.branch_id = r.`branch_id`
        
        WHERE  owd.status ='active' 
        ";

        
        $this->addSql($sql);
        
        
        $sql = "
        CREATE OR REPLACE ALGORITHM=MERGE SQL SECURITY INVOKER VIEW `reports_submission_missing` AS
        
        SELECT  owd.branch_id office_branch_id,branch_name,branch_code,owd.department_id AS office_department_id,department_name, department_code, 
        display_name office_bearer, email, phone_primary, phone_alternate,username, owd.period_to AS office_expiry,p.period_code, p.period_start,
        p.period_end, r.id AS report_id, r.period_from, r.period_to, created_by_user_id, completed_by_user_id, modified_by_user_id,  verified_by_user_id,
         received_by_user_id, date_created, date_completed, date_modified, date_verified, date_received, ifnull(r.STATUS,'outstanding') AS report_status
        
        FROM 
        periods p,
        offices_with_details owd LEFT OUTER JOIN  reports r ON r.id=-1
        
        WHERE 
        
        owd.status ='active' 
        
        
        AND concat(owd.branch_id,'_',owd.department_id,'_',p.period_code) NOT IN 
             (SELECT concat(rwp2.branch_id,'_',rwp2.department_id,'_',rwp2.period_code) 
                    FROM reports_with_periods rwp2 
             )
        
        ";                
        
        $this->addSql($sql);
        
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
