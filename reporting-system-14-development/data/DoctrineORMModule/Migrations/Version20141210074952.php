<?php

namespace DoctrineORMModule\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141210074952 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql("replace into config values 
                       ('branch_type',\"{'Jama`at':'Jama`at','Halqa':'Halqa','Markaz':'Markaz','Region':'Region'}\"),
                       ('branch_status',\"{'active':'Active','disabled':'Disabled','Pending':'pending'}\")
                      ");
                
        $this->addSql("replace into config values 
                       ('department_status',\"{'active':'Active','disabled':'Disabled'}\")
                      ");

        $this->addSql("replace into config values 
                       ('office_assignment_status',\"{'approved':'Approved','former':'Former','pending':'Pending','deleted':'Deleted','requested':'Requested','denied':'Denied'}\"),
                       ('office_assignment_type',\"{'Responsible':'responsible','Supervise':'supervise','Oversee':'oversee'}\")                                              
                      ");

        $this->addSql("replace into config values 
                    ('question_type',\"{'TEXT':'Text','MEMO':'Memo','DATE':'Date','SELECT':'Select','YES_NO':'Check','TABLE':'Table of Questions','GRID':'Data Grid','LABEL':'Label','FILE':'File','NUMBER':'Numeric'}\")   
                    ");
                    
        $this->addSql("replace into config values 
                       ('report_config_status',\"{'active':'Active','disabled':'Disabled'}\")
                      ");
                    
        $this->addSql("replace into config values 
                       ('report_status',\"{'draft':'Draft','completed':'Completed','verified':'Verified','received':'Received','deleted':'Deleted'}\")
                      ");

        $this->addSql("replace into config values 
                       ('user_status',\"{'active':'Active','locked':'Locked','disabled':'Disabled','deleted':'Deleted','expired':'Expired'}\")
                      ");
                      

        $this->addSql("replace into config values 
                       ('office_assignment_req_status',\"{'requested':'Requested','approved':'Approved','pending':'Pending','denied':'Denied'}\")
                       ");
                                                              
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
