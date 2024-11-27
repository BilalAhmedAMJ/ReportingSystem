<?php

namespace DoctrineORMModule\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141128075408 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE answers (id INT AUTO_INCREMENT NOT NULL, report_id INT NOT NULL, question_id INT DEFAULT NULL, caption VARCHAR(255) DEFAULT NULL, value VARCHAR(255) DEFAULT NULL,answer_number INT NOT NULL, INDEX IDX_50D0C6064BD2A4C0 (report_id), INDEX IDX_50D0C6061E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE questions (id INT AUTO_INCREMENT NOT NULL, report_config VARCHAR(255) NOT NULL, department_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, question_type ENUM(\'TEXT\',\'MEMO\',\'DATE\',\'SELECT\',\'YES_NO\',\'TABLE\',\'GRID\',\'LABEL\',\'FILE\',\'NUMBER\',\'GROUP_SECTION\',\'GROUP_MIXED\',\'GROUP_CHECKBOX\',\'GROUP_RADIO\'), answer_type VARCHAR(255) DEFAULT NULL, caption VARCHAR(255) DEFAULT NULL, details VARCHAR(255) DEFAULT NULL, display_config LONGTEXT DEFAULT NULL, contraints LONGTEXT DEFAULT NULL, sort_order VARCHAR(255) DEFAULT NULL, active_question TINYINT(1) DEFAULT \'1\' NOT NULL, INDEX IDX_8ADC54D5FD79FAE3 (report_config), INDEX IDX_8ADC54D5AE80F5DF (department_id), INDEX IDX_8ADC54D5727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reports (id INT AUTO_INCREMENT NOT NULL, report_config VARCHAR(255) NOT NULL, period_from VARCHAR(128) NOT NULL, period_to VARCHAR(128) DEFAULT NULL, branch_id INT NOT NULL, department_id INT DEFAULT NULL, created_by_user_id INT NOT NULL, completed_by_user_id INT DEFAULT NULL, modified_by_user_id INT NOT NULL, verified_by_user_id INT DEFAULT NULL, received_by_user_id INT DEFAULT NULL, report_type VARCHAR(256) NOT NULL, date_created DATETIME NOT NULL, date_completed DATETIME DEFAULT NULL, date_modified DATETIME NOT NULL, date_verified DATETIME DEFAULT NULL, date_received DATETIME DEFAULT NULL, status ENUM(\'draft\', \'completed\',\'verified\',\'received\',\'deleted\'), INDEX IDX_F11FA745FD79FAE3 (report_config), INDEX IDX_F11FA7457F1C2BDE (period_from), INDEX IDX_F11FA7458435CF4A (period_to), INDEX IDX_F11FA745DCD6CC49 (branch_id), INDEX IDX_F11FA745AE80F5DF (department_id), INDEX IDX_F11FA7457D182D95 (created_by_user_id), INDEX IDX_F11FA745277E2183 (completed_by_user_id), INDEX IDX_F11FA745DD5BE62E (modified_by_user_id), INDEX IDX_F11FA745C60EADF2 (verified_by_user_id), INDEX IDX_F11FA7457C1D6AE1 (received_by_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report_comments (id INT AUTO_INCREMENT NOT NULL, report_id INT NOT NULL, created_by_user_id INT NOT NULL, acknowledged_by_user_id INT DEFAULT NULL, date_created DATETIME NOT NULL, date_acknowledge DATETIME DEFAULT NULL, comment LONGTEXT NOT NULL, INDEX IDX_5FFE0F574BD2A4C0 (report_id), INDEX IDX_5FFE0F577D182D95 (created_by_user_id), INDEX IDX_5FFE0F57F6563C59 (acknowledged_by_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report_config (report_code VARCHAR(255) NOT NULL, report_name VARCHAR(256) NOT NULL, levels LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', freq VARCHAR(255) NOT NULL, role_create LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', role_view LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', role_verify LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', role_receive LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', status ENUM(\'active\', \'disabled\'), PRIMARY KEY(report_code)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE answers ADD CONSTRAINT FK_50D0C6064BD2A4C0 FOREIGN KEY (report_id) REFERENCES reports (id)');
        $this->addSql('ALTER TABLE answers ADD CONSTRAINT FK_50D0C6061E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id)');
        $this->addSql('ALTER TABLE questions ADD CONSTRAINT FK_8ADC54D5FD79FAE3 FOREIGN KEY (report_config) REFERENCES report_config (report_code)');
        $this->addSql('ALTER TABLE questions ADD CONSTRAINT FK_8ADC54D5AE80F5DF FOREIGN KEY (department_id) REFERENCES departments (id)');
        $this->addSql('ALTER TABLE questions ADD CONSTRAINT FK_8ADC54D5727ACA70 FOREIGN KEY (parent_id) REFERENCES questions (id)');
        $this->addSql('ALTER TABLE reports ADD CONSTRAINT FK_F11FA745FD79FAE3 FOREIGN KEY (report_config) REFERENCES report_config (report_code)');
        $this->addSql('ALTER TABLE reports ADD CONSTRAINT FK_F11FA7457F1C2BDE FOREIGN KEY (period_from) REFERENCES periods (period_code)');
        $this->addSql('ALTER TABLE reports ADD CONSTRAINT FK_F11FA7458435CF4A FOREIGN KEY (period_to) REFERENCES periods (period_code)');
        $this->addSql('ALTER TABLE reports ADD CONSTRAINT FK_F11FA745DCD6CC49 FOREIGN KEY (branch_id) REFERENCES branches (id)');
        $this->addSql('ALTER TABLE reports ADD CONSTRAINT FK_F11FA745AE80F5DF FOREIGN KEY (department_id) REFERENCES departments (id)');
        $this->addSql('ALTER TABLE reports ADD CONSTRAINT FK_F11FA7457D182D95 FOREIGN KEY (created_by_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE reports ADD CONSTRAINT FK_F11FA745277E2183 FOREIGN KEY (completed_by_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE reports ADD CONSTRAINT FK_F11FA745DD5BE62E FOREIGN KEY (modified_by_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE reports ADD CONSTRAINT FK_F11FA745C60EADF2 FOREIGN KEY (verified_by_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE reports ADD CONSTRAINT FK_F11FA7457C1D6AE1 FOREIGN KEY (received_by_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE report_comments ADD CONSTRAINT FK_5FFE0F574BD2A4C0 FOREIGN KEY (report_id) REFERENCES reports (id)');
        $this->addSql('ALTER TABLE report_comments ADD CONSTRAINT FK_5FFE0F577D182D95 FOREIGN KEY (created_by_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE report_comments ADD CONSTRAINT FK_5FFE0F57F6563C59 FOREIGN KEY (acknowledged_by_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE office_assignments ADD period_to VARCHAR(128) DEFAULT NULL');
        $this->addSql('ALTER TABLE office_assignments ADD CONSTRAINT FK_A60126F8435CF4A FOREIGN KEY (period_to) REFERENCES periods (period_code)');
        $this->addSql('CREATE INDEX IDX_A60126F8435CF4A ON office_assignments (period_to)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE answers DROP FOREIGN KEY FK_50D0C6061E27F6BF');
        $this->addSql('ALTER TABLE questions DROP FOREIGN KEY FK_8ADC54D5727ACA70');
        $this->addSql('ALTER TABLE answers DROP FOREIGN KEY FK_50D0C6064BD2A4C0');
        $this->addSql('ALTER TABLE report_comments DROP FOREIGN KEY FK_5FFE0F574BD2A4C0');
        $this->addSql('ALTER TABLE questions DROP FOREIGN KEY FK_8ADC54D5FD79FAE3');
        $this->addSql('ALTER TABLE reports DROP FOREIGN KEY FK_F11FA745FD79FAE3');
        $this->addSql('DROP TABLE answers');
        $this->addSql('DROP TABLE questions');
        $this->addSql('DROP TABLE reports');
        $this->addSql('DROP TABLE report_comments');
        $this->addSql('DROP TABLE report_config');
        $this->addSql('ALTER TABLE office_assignments DROP FOREIGN KEY FK_A60126F8435CF4A');
        $this->addSql('DROP INDEX IDX_A60126F8435CF4A ON office_assignments');
        $this->addSql('ALTER TABLE office_assignments DROP period_to');
    }

 public function postUp(Schema $schema)
    {
        /*Add reports config for Local level */
        $sql="
        insert into report_config values
        ('gs_report_monthly','General Secretary Report','Halqa,Jamaat','monthly','general_secretary',
                'general_secretary,president,local_amir,national_amir','president,local_amir','general_secretary',
                'active'
        ),
        ('secretary_report_monthly','Secretary Report','Halqa,Jamaat','monthly','secretary',
                'secretary,president,local_amir,national_amir','president,local_amir','secretary',
                'active'
        )
        ";
        $this->connection->executeQuery($sql);         
        
        /*Add newly required roles*/
        $sql="replace into role (id,parent_id,role_id) values 
                (15,1,'general_secretary'),
                (16,1,'local_amir'),
                (17,1,'national_amir'),
                (18,1,'president'),
                (19,1,'secretary'),
                (20,1,'national-secretary'),
                (21,2,'national-general-secretary')
        "; 
        $this->connection->executeQuery($sql);         

       /*Add national GS to role*/
        $sql="replace into user_role_linker values 
                (2,21),
                (3,20),
                (4,15),
                (5,19),
                (6,15),
                (7,19)
        "; 
        $this->connection->executeQuery($sql);         
        
		/*Create questions for GS department */
		$sql= <<<'SQL'
		   insert into questions values
            
            (150, 'gs_report_monthly',5, 150,'GROUP_SECTION','NONE','Majlis `Amila Meeting','','','{"multiple_answers":true}',1,1),
            (151, 'gs_report_monthly',5, 150,'GROUP_MIXED','NONE','Majlis `Amila Meeting','','{"display_columns":4}','',1,1),
            
            (152, 'gs_report_monthly',5, 151,'YES_NO','YES_NO','Meeting held<br>(Please check)','','','',2,1),
            (153, 'gs_report_monthly',5, 151,'DATE','DATE','Date<br>(YYYY-MM-DD)','','','',3,1),
            (154, 'gs_report_monthly',5, 151,'NUMBER','NUMBER','Attendance','','','',4,1),
            (155, 'gs_report_monthly',5, 151,'FILE','FILE','Attachment','','','',5,1),
            
            (156, 'gs_report_monthly',5, 150,'MEMO','MEMO','Meeting minutes (if not attached)','','','',6,1),

            (190, 'gs_report_monthly',5, 190,'GROUP_SECTION','NONE','General Body Meeting','','','{"multiple_answers":true}',7,1),
            (157, 'gs_report_monthly',5, 190,'GROUP_MIXED','NONE','General Body Meeting Data','','{"display_columns":4}','',8,1),
            (158, 'gs_report_monthly',5, 157,'YES_NO','YES_NO','Meeting held<br>(Please check)','','','',8,1),
            (159, 'gs_report_monthly',5, 157,'DATE','DATE','Date<br>(YYYY-MM-DD)','','','',9,1),
            (160, 'gs_report_monthly',5, 157,'TEXT','TEXT','Location','','','',10,1),
            (161, 'gs_report_monthly',5, 157,'FILE','FILE','Attachment','','','',11,1),
            (162, 'gs_report_monthly',5, 157,'NUMBER','NUMBER','Total Attendance','','','',12,1),
            (163, 'gs_report_monthly',5, 157,'NUMBER','NUMBER','Gents Attendance','','','',13,1),
            (164, 'gs_report_monthly',5, 157,'NUMBER','NUMBER','Ladies Attendance','','','',14,1),
            (165, 'gs_report_monthly',5, 157,'NUMBER','NUMBER','Children Attendance','','','',15,1),
            (166, 'gs_report_monthly',5, 190,'MEMO','MEMO','Brief report of the meeting (if not attached)','','','',16,1),


            (195, 'gs_report_monthly',5, 195,'GROUP_SECTION','NONE','Visits (Attach report if required)','','','{"multiple_answers":true}',7,1),
            (167, 'gs_report_monthly',5, 195,'GROUP_MIXED','NONE','Visits (Attach report if required) Data','','{"display_columns":4}','',17,1),
            (168, 'gs_report_monthly',5, 167,'NUMBER','NUMBER','No. Households visited by President / General Secretary this month','','','',18,1),
            (169, 'gs_report_monthly',5, 167,'TEXT','TEXT','Comments','','','',19,1),
            (170, 'gs_report_monthly',5, 167,'NUMBER','NUMBER','No. Households visited by Members of Majlis `Amila this month','','','',20,1),
            (171, 'gs_report_monthly',5, 195,'TEXT','TEXT','Comments','','','',21,1),
            (172, 'gs_report_monthly',5, 195,'FILE','FILE','Attachment','','','',22,1),
            
            (173, 'gs_report_monthly',5, 173,'GRID','GRID','Report of Sa`iqeen','','','',23,1),
            (174, 'gs_report_monthly',5, 23,'TEXT','TEXT','Name of Sa`iq','','','',24,1),
            (175, 'gs_report_monthly',5, 23,'NUMBER','NUMBER','No. of households visited in person','','','',25,1),
            (176, 'gs_report_monthly',5, 23,'NUMBER','NUMBER','No. of households contacted via phone','','','',26,1),
            (177, 'gs_report_monthly',5, 23,'NUMBER','NUMBER',' No. of households messages conveyed','','','',27,1),

            (178, 'gs_report_monthly',5, 178,'MEMO','MEMO','List all departmental activities & achievements for this month','','','{"validation":[{"empty":"false"}]}',28,1),
            (179, 'gs_report_monthly',5, 179,'FILE','FILE','Attachment','','','',29,1),
            (180, 'gs_report_monthly',5, 180,'MEMO','MEMO','Problems encountered in carrying out above activities','','','',30,1),
            (181, 'gs_report_monthly',5, 181,'MEMO','MEMO','Any help required from Markaz for your department','','','',31,1),
            (182, 'gs_report_monthly',5, 182,'MEMO','MEMO','Activities planned for next month','','','{"validation":[{"empty":"false"}]}',32,1),
            (183, 'gs_report_monthly',5, 183,'MEMO','MEMO','Any other comments','','','',33,1)
SQL;

        $this->connection->executeQuery($sql);      
        
		/*Create questions for first department other than GS*/                
        $sql="insert into questions values
			(0, 'secretary_report_monthly',1, null,'MEMO','MEMO','List all departmental activities & achievements for this month','','','{\"validation\":[{\"empty\":\"false\"}]}',1,1),
			(0, 'secretary_report_monthly',1, null,'FILE','FILE','Attachment','','','',2,1),
			(0, 'secretary_report_monthly',1, null,'MEMO','MEMO','Problems encountered in carrying out above activities','','','',3,1),
			(0, 'secretary_report_monthly',1, null,'MEMO','MEMO','Any help required from Markaz for your department','','','',4,1),
			(0, 'secretary_report_monthly',1, null,'MEMO','MEMO','Activities planned for next month','','','{\"validation\":[{\"empty\":\"false\"}]}',5,1),
			(0, 'secretary_report_monthly',1, null,'MEMO','MEMO','Any other comments','','','',6,1)        	         
        ";
        $this->connection->executeQuery($sql);         
        
		/*Copy questions from above ot all departments except GS*/
		$sql="
			insert into questions
			select 0,report_config, d.id , parent_id , question_type , answer_type , caption , details , display_config , contraints , sort_order,1 
			from questions q , departments d where d.id not in (1,5) and q.department_id=1  
		";                
        $this->connection->executeQuery($sql);         
		
	}
}
