<?php

namespace DoctrineORMModule\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141104212952 extends AbstractMigration
{
	
	
        /** 
		 * MANUAL : manually run following create tables before applying migrations 
		 *
		 */
    public function preUp(Schema $schema)
    {
		 $sql="
            CREATE TABLE `role` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `parent_id` int(11) DEFAULT NULL,
              `role_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `UNIQ_57698A6AD60322AC` (`role_id`),
              KEY `IDX_57698A6A727ACA70` (`parent_id`),
              CONSTRAINT `FK_57698A6A727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `role` (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
			";          
	        $this->connection->executeQuery($sql);    
			$sql="            
            CREATE TABLE `users` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `username` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
              `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              `displayName` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
              `password` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `UNIQ_1483A5E9E7927C74` (`email`),
              UNIQUE KEY `UNIQ_1483A5E9F85E0677` (`username`)
            ) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
        	";
		 $this->connection->executeQuery($sql);    
		 $sql="
            CREATE TABLE `user_role_linker` (
              `user_id` int(11) NOT NULL,
              `role_id` int(11) NOT NULL,
              PRIMARY KEY (`user_id`,`role_id`),
              KEY `IDX_61117899A76ED395` (`user_id`),
              KEY `IDX_61117899D60322AC` (`role_id`),
              CONSTRAINT `FK_61117899A76ED395` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
              CONSTRAINT `FK_61117899D60322AC` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci                
			";     

		$this->connection->executeQuery($sql);
		
		$sql="	
		INSERT INTO `users` VALUES (1,'sysadmin','sysadmin@email.com','Sys Admin','$2y$14$sdUJOMokhABTdMXhDoyzEuKJeBIIbkUZ106/LYRzHvP7im/.gIWbu')
	    ,(2,'gsnational','gsnational@email.com','GS National','$2y$14$sdUJOMokhABTdMXhDoyzEuKJeBIIbkUZ106/LYRzHvP7im/.gIWbu')
	    ,(3,'muhasibnational','muhasibnational@email.com','Muhasib National','$2y$14$sdUJOMokhABTdMXhDoyzEuKJeBIIbkUZ106/LYRzHvP7im/.gIWbu')
	    ,(4,'gsjamaat','gsjamaat@email.com','GS Jama`at','$2y$14$sdUJOMokhABTdMXhDoyzEuKJeBIIbkUZ106/LYRzHvP7im/.gIWbu')
	    ,(5,'muhasibjamaat','muhasibjamaat@email.com','Muhasib Jama`at','$2y$14$sdUJOMokhABTdMXhDoyzEuKJeBIIbkUZ106/LYRzHvP7im/.gIWbu')
	    ,(6,'gshalqa','gshalqa@email.com','GS Halqa','$2y$14$sdUJOMokhABTdMXhDoyzEuKJeBIIbkUZ106/LYRzHvP7im/.gIWbu')
	    ,(7,'muhasibhalqa','muhasibhalqa@email.com','Muhasib Halqa','$2y$14$sdUJOMokhABTdMXhDoyzEuKJeBIIbkUZ106/LYRzHvP7im/.gIWbu')
	    ";
        
		$this->connection->executeQuery($sql);
		
		$sql="                
        INSERT INTO `role` VALUES (1,NULL,'guest')
        ,(2,1,'user')
        ,(3,2,'admin')
        ,(4,3,'sys-admin')
		";
		$this->connection->executeQuery($sql);		
		$sql=" 
        INSERT INTO `user_role_linker` VALUES (1,4);        
       	"; 		 
         
		$this->connection->executeQuery($sql);
	}	
	
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE branches (id INT AUTO_INCREMENT NOT NULL, parent_branch_id INT DEFAULT NULL, branch_name VARCHAR(256) NOT NULL, branch_code VARCHAR(128) NOT NULL, status ENUM(\'active\', \'disabled\'), branch_type ENUM(\'Markaz\', \'Jama`at\',\'Region\',\'Halqa\'), UNIQUE INDEX UNIQ_D760D16FAD11800F (branch_code), INDEX IDX_D760D16F2D9DE3A7 (parent_branch_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE branch_hierarchy (parent_id INT NOT NULL, sub_branch_id INT NOT NULL, INDEX IDX_F8F0187D727ACA70 (parent_id), INDEX IDX_F8F0187DCEDB8AF7 (sub_branch_id), PRIMARY KEY(parent_id, sub_branch_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE departments (id INT AUTO_INCREMENT NOT NULL, department_name VARCHAR(256) NOT NULL, department_code VARCHAR(128) NOT NULL, rules LONGTEXT NOT NULL, guide_lines LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_deptcd (department_code),PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE branches ADD CONSTRAINT FK_D760D16F2D9DE3A7 FOREIGN KEY (parent_branch_id) REFERENCES branches (id)');
        $this->addSql('ALTER TABLE branch_hierarchy ADD CONSTRAINT FK_F8F0187D727ACA70 FOREIGN KEY (parent_id) REFERENCES branches (id)');
        $this->addSql('ALTER TABLE branch_hierarchy ADD CONSTRAINT FK_F8F0187DCEDB8AF7 FOREIGN KEY (sub_branch_id) REFERENCES branches (id)');
       
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE branches DROP FOREIGN KEY FK_D760D16F2D9DE3A7');
        $this->addSql('ALTER TABLE branch_hierarchy DROP FOREIGN KEY FK_F8F0187D727ACA70');
        $this->addSql('ALTER TABLE branch_hierarchy DROP FOREIGN KEY FK_F8F0187DCEDB8AF7');
        $this->addSql('DROP TABLE branches');
        $this->addSql('DROP TABLE branch_hierarchy');
        $this->addSql('DROP TABLE departments');
    }
    
    public function postUp(Schema $schema){
        $sql_branches="
                        insert into branches values 
                        (27,NULL,'Canada','CA','active','Markaz'),
                        (77,27,'Imarat Toronto','ITO','active','Jama`at'),
                        (78,27,'Imarat Brampton','IBN','active','Jama`at'),
                        (79,27,'Imarat Mississauga','IMA','active','Jama`at'),
                        (80,27,'Imarat Peace Village','IPV','active','Jama`at'),
                        (81,27,'Imarat Vancouver','IVR','active','Jama`at'),
                        (82,27,'Imarat Vaughan','IVN','active','Jama`at'),
                        
                        (1,27,'Barrie','BAR','active','Jama`at'),
                        (2,27,'Brantford','BRT','active','Jama`at'),
                        (3,27,'Imarat Calgary','CAL','active','Jama`at'),
                        (4,27,'Cornwall','CWL','active','Jama`at'),
                        (5,27,'Durham','DUR','active','Jama`at'),
                        (6,27,'Edmonton','EDM','active','Jama`at'),
                        (7,27,'Kingston','KIN','active','Jama`at'),
                        (8,27,'London','LDN','active','Jama`at'),
                        (9,27,'Lloydminster','LYD','active','Jama`at'),
                        (10,79,'Malton','MAL','active','Halqa'),
                        (11,27,'Markham','MKM','active','Jama`at'),
                        (12,27,'Newfoundland','NFD','active','Jama`at'),
                        (13,27,'Sydney-Nova Scotia','NSK','active','Jama`at'),
                        (14,77,'North York','NYK','active','Halqa'),
                        (15,27,'Ottawa','OTW','active','Jama`at'),
                        (16,27,'Regina','REG','active','Jama`at'),
                        (17,27,'Richmond Hill','RHL','active','Jama`at'),
                        (18,77,'Scarborough','SCR','active','Halqa'),
                        (19,27,'St. Catherines','STC','active','Jama`at'),
                        (20,27,'Sudbury','SUD','active','Jama`at'),
                        (21,77,'Toronto Central','TOC','active','Halqa'),
                        (22,77,'Toronto East','TOE','active','Halqa'),
                        (23,81,'Vancouver (Delta)','VAN','active','Halqa'),
                        (24,27,'Windsor','WDS','active','Jama`at'),
                        (25,27,'Winnipeg','WIN','active','Jama`at'),
                        (26,77,'Weston South','WST','active','Halqa'),
                        (28,77,'Ahmadiyya Abode of Peace','AAP','active','Halqa'),
                        (29,78,'Brampton Centre','BRC','active','Halqa'),
                        (30,78,'Brampton Flower Town','BRF','active','Halqa'),
                        (31,78,'Brampton Peel Village','BRP','active','Halqa'),
                        (32,27,'Burlington','BUR','active','Jama`at'),
                        (33,3,'Calgary North East','CAE','active','Halqa'),
                        (34,3,'Calgary North West','CAW','active','Halqa'),
                        (35,3,'Calgary South','CAS','active','Halqa'),
                        (36,27,'Milton-Gerogetown','MIL','active','Jama`at'),
                        (37,79,'Mississauga East','MAE','active','Halqa'),
                        (38,79,'Mississauga North','MAN','active','Halqa'),
                        (39,79,'Mississauga South','MAS','active','Halqa'),
                        (40,79,'Mississauga West','MAW','active','Halqa'),
                        (41,27,'Montreal East','MTE','active','Jama`at'),
                        (42,27,'Montreal West','MTW','active','Jama`at'),
                        (43,27,'Newmarket','NEW','active','Jama`at'),
                        (44,27,'Oakville','OAK','active','Jama`at'),
                        (45,80,'Peace Village East','PVE','active','Halqa'),
                        (46,80,'Peace Village West','PVW','active','Halqa'),
                        (47,77,'Rexdale','REX','active','Halqa'),
                        (48,81,'Vancouver Surrey West','VSW','active','Halqa'),
                        (49,82,'Vaughan East','VNE','active','Halqa'),
                        (50,77,'Weston Islington','WSI','active','Halqa'),
                        (51,82,'Woodbridge','WDB','active','Halqa'),
                        (52,78,'Brampton Springdale','BRS','active','Halqa'),
                        (53,27,'Halifax','HFX','active','Jama`at'),
                        (54,27,'Thompson','THN','active','Jama`at'),
                        (55,27,'Hamilton North','HMN','active','Jama`at'),
                        (56,27,'Hamilton South','HMS','active','Jama`at'),
                        (57,77,'Weston North East','WNE','active','Halqa'),
                        (58,77,'Weston North West','WNW','active','Halqa'),
                        (59,27,'Fort McMurray','FMY','active','Jama`at'),
                        (60,3,'Calgary West','CWE','active','Halqa'),
                        (61,3,'Calgary Taradale','CTA','active','Halqa'),
                        (62,27,'Bradford','BRD','active','Jama`at'),
                        (63,82,'Maple','MPL','active','Halqa'),
                        (64,27,'Montreal Center','MTC','active','Jama`at'),
                        (65,27,'Quebec City','QCC','active','Jama`at'),
                        (66,27,'Kitchener Waterloo','KHW','active','Jama`at'),
                        (67,78,'Brampton Heartlake','BHS','active','Halqa'),
                        (68,78,'Brampton Caledon','BHN','active','Halqa'),
                        (69,78,'Brampton Castlemore','BCM','active','Halqa'),
                        (70,78,'Brampton East','BRE','active','Halqa'),
                        (71,27,'Saskatoon South','SKS','active','Jama`at'),
                        (72,27,'Saskatoon North','SKN','active','Jama`at'),
                        (73,82,'Vaughan North','VNN','active','Halqa'),
                        (74,82,'Vaughan South','VNS','active','Halqa'),
                        (75,77,'Emery Village','EVG','active','Halqa'),
                        (76,27,'Yellowknife','YNF','active','Jama`at'),
                        (83,3,'Calgary Saddleridge','CSR','active','Halqa'),
                        (84,3,'Calgary Martindale Skyview','CMD','active','Halqa'),
                        (85,80,'Peace Village Centre East','PCE','active','Halqa'),
                        (86,80,'Peace Village Centre West','PCW','active','Halqa'),
                        (87,80,'Peace Village South East','PSE','active','Halqa'),
                        (88,80,'Peace Village South West','PSW','active','Halqa'),
                        (89,27,'Prince George','PRG','active','Jama`at'),
                        (90,27,'Salmon Arms','SAA','active','Jama`at'),
                        (91,27,'Terrace','TER','active','Jama`at'),
                        (92,27,'Moosejaw','MOJ','active','Jama`at'),
                        (93,81,'Vancouver Surrey North','VSN','active','Halqa'),
                        (94,81,'Vancouver Surrey South','VSS','active','Halqa'),
                        (95,27,'Peterborough','PBH','active','Jama`at')
                                 
                        ";
        $this->connection->executeQuery($sql_branches );    

        $sql_branch_hierarchy = "
                                 insert into branch_hierarchy values
        
                                (3,33),
                                (3,34),
                                (3,35),
                                (3,60),
                                (3,61),
                                (3,83),
                                (3,84),
                                (77,14),
                                (77,18),
                                (77,21),
                                (77,22),
                                (77,26),
                                (77,28),
                                (77,47),
                                (77,50),
                                (77,57),
                                (77,58),
                                (77,75),
                                (78,29),
                                (78,30),
                                (78,31),
                                (78,52),
                                (78,67),
                                (78,68),
                                (78,69),
                                (78,70),
                                (79,10),
                                (79,37),
                                (79,38),
                                (79,39),
                                (79,40),
                                (80,45),
                                (80,46),
                                (80,85),
                                (80,86),
                                (80,87),
                                (80,88),
                                (81,23),
                                (81,28),
                                (81,48),
                                (81,93),
                                (81,94),
                                (82,49),
                                (82,51),
                                (82,63),
                                (82,73),
                                (82,74)        
                                ";
        $this->connection->executeQuery($sql_branch_hierarchy );    
        
        //All Jamaat report to "Canada" Markaz except Canada markaz itself
		$sql_branch_hierarchy="insert into branch_hierarchy select 27,id from branches where id!=27 and branch_type='Jama`at'";        
        $this->connection->executeQuery($sql_branch_hierarchy );
                        
        $sql_departments="
            INSERT INTO `departments` VALUES 
             (1,'Additional Secretary Mal','SM','','<table border=\"0\"><tr><td width=\"200\">Steps taken to improve methods & strategies for Chanda Collection.</td></tr><tr><td width=\"200\">&nbsp;</td></tr><tr><td width=\"200\">Activities carried out to create the spirit of financial sacrifice in members of Jama`at, specially, those who don\'t pay or are irregular in paying the Chanda.</td></tr><tr><td width=\"200\">&nbsp;</td></tr><tr><td width=\"200\"><b>Report on:</b></td></tr><tr><td width=\"200\">&nbsp;</td></tr><tr><td width=\"200\">Effort made to encourage those members who do not pay Chandas and also those who do not pay at the prescribed rate.</td></tr></table>')
            ,(2,'Amin','AN','','<table border=\"0\"><tr><td width=\"200\"><b>Report on:</b></td></tr><tr><td width=\"200\">&nbsp;</td></tr><tr><td width=\"200\">Record of all receipts received</td></tr>\r\n<tr><td width=\"200\">&nbsp;</td></tr><tr><td width=\"200\">Record of all collection deposits in the Bank</td></tr><tr><td width=\"200\">&nbsp;</td></tr><tr><td width=\"200\">Record of all Bank accounts of Jama`at</td></tr><tr><td width=\"200\">&nbsp;</td></tr><tr><td width=\"200\">Record of payments on receipt of demand Muhasib / Secretary Mal.</td></tr></table>\r\n')
            ,(3,'Diyafat','DT','','<table border=\"0\"><tr><td width=\"200\">Efforts made to improve Diyafat Services including:</td></tr><tr><td width=\"200\">Setup & Management</td></tr><tr><td width=\"200\">Personnel training</td></tr><tr><td width=\"200\">Equipment</td></tr><tr><td width=\"200\">Other</td></tr><tr><td width=\"200\">&nbsp;</td></tr><tr><td width=\"200\">Report events where Diyafat services were provided. Include time, place, occasion, no. of guests accommodated, food served etc.</td></tr><tr><td width=\"200\">&nbsp;</td></tr><tr><td width=\"200\">Steps taken to enlist those members who are ready to offer accommodation or other hospitality services to the guests of Promised Messiah (as).</td></tr><tr><td width=\"200\">&nbsp;</td></tr><tr><td width=\"200\">Efforts made to inculcate the spirit of hospitality in Jama`at.</td></tr><tr><td width=\"200\">&nbsp;</td></tr></table>')
            ,(4,'Mal','FE','','<table border=\"0\"><tr><td width=\"200\">Steps taken to improve methods & strategies for Chanda Collection.</td></tr><tr><td width=\"200\">&nbsp;</td></tr><tr><td width=\"200\">Activities carried out to create the spirit of financial sacrifice in members of Jama`at, specially, those who don’t pay or are irregular in paying the chanda.</td></tr><tr><td width=\"200\">&nbsp;</td></tr><tr><td width=\"200\">Report total chanda collected:</td></tr><tr><td width=\"200\">Chanda Aam</td></tr><tr><td width=\"200\">Jalsa Salana</td></tr><tr><td width=\"200\">Wasiyat</td></tr><tr><td width=\"200\">&nbsp;</td></tr><tr><td width=\"200\">Number of Chanda Batches sent to Markaz</td></tr><tr><td width=\"200\">&nbsp;</td></tr><tr><td width=\"200\">Specify total Expenses for the month. Attach a detailed expense report.</td></tr><tr><td width=\"200\">&nbsp;</td></tr><tr><td width=\"200\">Provide total expenses year to date.</td></tr><tr><td width=\"200\">&nbsp;</td></tr><tr><td width=\"200\">Efforts made to update the current budget and pledges.</td></tr><tr><td width=\"200\">&nbsp;</td></tr><tr><td width=\"200\">Report total number of receipts issued this month with total dollar amount.</td></tr></table>')
            ,(5,'General Secretary','GS','','<table border=\"0\"><tr><td width=\"200\"><b>Report on:</b></td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Minutes of Majlis `Amila Meetings</td></tr> <tr><td width=\"200\">Details of General Body Meetings</td></tr> <tr><td width=\"200\">Implementation of decisions of Majlis `Amila</td></tr> <tr><td width=\"200\">Implementation of instructions from Markaz</td></tr> <tr><td width=\"200\">Implementation of Shura decisions</td></tr> <tr><td width=\"200\">Monthly Report of all departments</td></tr> <tr><td width=\"200\">Inventory of all properties and belongings of Jama`at</td></tr> <tr><td width=\"200\">Special events held by the Jama`at</td></tr> <tr><td width=\"200\">Activities of Sa`iqeen</td></tr> <tr><td width=\"200\">Any other activities carried out by the department</td></tr></table>')
            ,(6,'Isha`at','IT','','<table border=\"0\"><tr><td width=\"200\">Activities carried out to:</td></tr> <tr><td width=\"200\">Organize exhibitions on Islam & Ahmadiyyat</td></tr> <tr><td width=\"200\">Publish articles in support of Islam in newspapers or other media</td></tr> <tr><td width=\"200\">Translate Jama`at literature</td></tr> <tr><td width=\"200\">Acquire books for local library</td></tr> <tr><td width=\"200\">Sell Jama`at books</td></tr> <tr><td width=\"200\">Promote books of promised Messiah among members, specially the youth</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Efforts made to identify, bring forward and educate members about the art of writing and public speaking.</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Efforts made to:</td></tr> <tr><td width=\"200\">Encourage members to subscribe to various Jama`at publications, such as, Alfazal, Gazette, Review of Religions etc.</td></tr> <tr><td width=\"200\">Create and expand the team of members who will write articles for Jama`at publications & in media.</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Attach copies of any publication by the local Jama`at.</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Steps taken to improve secular and religious knowledge of members.</td></tr></table>')
            ,(7,'Ja`idad','JD','','<table border=\"0\"><tr><td width=\"200\">Efforts made to:</td></tr> <tr><td width=\"200\">Carry out the maintenance of Jama`at’s property</td></tr> <tr><td width=\"200\">Ensure security and safety of these assets</td></tr> <tr><td width=\"200\">Keep the property clean and in shape</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Report any expenses incurred in carrying out above activities.</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Strategy devised to:</td></tr> <tr><td width=\"200\">Meet future requirements of Jama`at</td></tr> <tr><td width=\"200\">Search for a suitable property</td></tr></table>')
            ,(8,'Muhasib','MB','','<table border=\"0\"><tr><td width=\"200\"><b>Report on:</b></td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Account of all the receipts (Chandas as well as other incomes)</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Account of all expenses incurred</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Record of receipt books in custody and those issued for collection</td></tr></table>')
            ,(9,'Rishta Nata','RA','','<table border=\"0\"><tr><td width=\"200\">Efforts made to:</td></tr> <tr><td width=\"200\">Create a database of marriageable members of Jama`at</td></tr> <tr><td width=\"200\">Educate families or individuals of their responsibilities in this process</td></tr> <tr><td width=\"200\">Assist in the match making process</td></tr> <tr><td width=\"200\">Provide matrimonial counseling to would be brides and grooms.</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Report the strategy devised to:</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Keep the database up-to-date</td></tr> <tr><td width=\"200\">Ensure privacy of families and individuals</td></tr> <tr><td width=\"200\">Increase awareness about the importance of this department in Jama`at</td></tr> <tr><td width=\"200\">Make the match making process effective and successful</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Specify any counseling sessions held for the young generation.</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">List any informational events organized for parents.</td></tr></table>')
            ,(10,'Sam`I Wa Basri','SB','','<table border=\"0\"><tr><td width=\"200\">Efforts made to:</td></tr> <tr><td width=\"200\">Produce programs for MTA or local Radio Channel</td></tr> <tr><td width=\"200\">Contact local TV, Cable, Radio stations to broadcast Jama`at programs</td></tr> <tr><td width=\"200\">Increase awareness among members about available audio/video programs and facilities</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Steps taken to:</td></tr> <tr><td width=\"200\">Connect households with MTA</td></tr> <tr><td width=\"200\">Remove technical/other difficulties in providing MTA access</td></tr> <tr><td width=\"200\">Build technically capable teams for the department</td></tr> <tr><td width=\"200\">Encourage members to acquire MTA system and watch programs</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Activities carried out to set up an Audio/Video Library in your Jama`at.</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Any cassettes distributed during the month from the local A/V Library.</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Any plans to improve the A/V assistance provided during Jama`at Programs</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Efforts made to spread the message of Islam and Ahmadiyyat using A/V equipment from Jama`at or other resources.</td></tr></table>')
            ,(11,'San`at-o-Tijarat','ST','','<table border=\"0\"><tr><td width=\"200\">Efforts made to:</td></tr> <tr><td width=\"200\">Educate members about available skills and trades training programs</td></tr> <tr><td width=\"200\">Educate members on how to establish a small business</td></tr> <tr><td width=\"200\">Help the unemployed members of Jama`at secure a job by learning skills</td></tr> <tr><td width=\"200\">Train members in skills and trades needed for the present/future job market</td></tr> <tr><td width=\"200\">Educate members about job search techniques and using resources available through government and other organizations</td></tr> <tr><td width=\"200\">Provide technical-writing assistance, such as, resume, covering letter etc.</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Report number of employed and unemployed members.</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">How many of these unemployed are actively seeking job?</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Specify any assistance provided to encourage self-employment.</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Strategies devised to:</td></tr> <tr><td width=\"200\">Create a network of information-flow to get members a job as soon as possible</td></tr> <tr><td width=\"200\">Help new immigrants understand the Canadian job market and get into the system</td></tr></table>')
            ,(12,'Ta`lim','TM','','<table border=\"0\"><tr><td width=\"200\">Efforts made to create the habit of reading the Holy Qur’an regularly in members of Jama`at:</td></tr> <tr><td width=\"200\">Without translation (Nazra)</td></tr> <tr><td width=\"200\">With Translation</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Activities carried out to teach Namaz to the members of Jama`at:</td></tr> <tr><td width=\"200\">Simple Namaz</td></tr> <tr><td width=\"200\">Namaz with translation</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Steps taken to have members of Jama`at memorize the first 17 verses of Surah Al-Baqarah.</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Which book was assigned to the members to be read this month? What strategy has been devised to have members read the assigned publication?</td></tr> <tr><td width=\"200\">Efforts made for the educational uplift of Jama’at members by holding educational classes or other means:</td></tr> <tr><td width=\"200\">Religious education</td></tr> <tr><td width=\"200\">Secular & Academic education</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Report any member of Jama`at who has demonstrated outstanding academic performance (please attach detail)?</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Efforts made to encourage members to pursue higher education?</td></tr></table>')
            ,(13,'Tabligh','TH','','<table border=\"0\"><tr><td width=\"200\">Efforts made to:</td></tr> <tr><td width=\"200\">Expand the team of Da`een ilAllah</td></tr> <tr><td width=\"200\">Sharpen the Tabligh skills of the team and other members of Jama`at</td></tr> <tr><td width=\"200\">Establish Tabligh contacts and hold Q/A sessions</td></tr> <tr><td width=\"200\">Organize training sessions for Da`een ilAllah</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">What was planned and discussed in Tabligh Amila Meeting? Attach minutes and attendance list.</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Provide summary of report of all Da`een ilAllah in Jama`at.</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Efforts made to:</td></tr> <tr><td width=\"200\">Encourage members for Waqf-e-Aarzi</td></tr> <tr><td width=\"200\">Establish contact and friendly relationship for tabligh with non-Ahmadis</td></tr> <tr><td width=\"200\">Educate members about present/future challenges and prepare them for an effective response</td></tr> <tr><td width=\"200\">Distribute Tabligh literature</td></tr> <tr><td width=\"200\">Accelerate Tabligh team building activities</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Number of Ba`its: This month’s target and number of Ba`its achieved.</td></tr></table>')
            ,(14,'Tahrik Jadid','TJ','','<table border=\"0\"><tr><td width=\"200\">Efforts made to:</td></tr> <tr><td width=\"200\">Educate members about the philosophy, history and importance of Tahrik Jadid</td></tr> <tr><td width=\"200\">Include non-participants in this blessed Tahrik</td></tr> <tr><td width=\"200\">Encourage members to participate with enhanced vigor & devotion</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Activities carried out to:</td></tr> <tr><td width=\"200\">Get pledges from members</td></tr> <tr><td width=\"200\">Improve collection procedures for timely submission</td></tr> <tr><td width=\"200\">Approach members who have not made any payment towards their pledge yet</td></tr> <tr><td width=\"200\">Implement demands of Tahrik Jadid. List which demand was focused on this month.</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\"><b>Pledges:</b></td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Report the target of your Jama`at for the current year.</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Specify collection this Month and collection year-to-date.</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Provide total number of participants year-to-date.</td></tr></table>')
            ,(15,'Tarbiyat','TT','','<table border=\"0\"><tr><td width=\"200\">Efforts made to:</td></tr> <tr><td width=\"200\">Inculcate the habit of offering the five daily prayers regularly in the members of Jama`at</td></tr> <tr><td width=\"200\">Improve attendance in Jum`a prayers</td></tr> <tr><td width=\"200\">Increase number of people watching MTA, specially, Huzur’s Friday sermon</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Strategies devised to:</td></tr> <tr><td width=\"200\">Create the habit of reading various religious books/literature such as Ahadith, books of promised Messiah etc.</td></tr> <tr><td width=\"200\">Eradicate Un-Islamic practices & traditions and to protect young generation from social evils</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Efforts made to:</td></tr> <tr><td width=\"200\">Organize Dars and setup prayer centers to facilitate congregational prayers</td></tr> <tr><td width=\"200\">Promote matrimonial harmony in families</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Report any assistance provided in resolution of domestic disputes.</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Activities carried out to:</td></tr> <tr><td width=\"200\">Develop a bond with new generation and make them realize their responsibilities</td></tr> <tr><td width=\"200\">Provide counselling to parents & children.</td></tr> <tr><td width=\"200\">Identify & reactivate members drifting away from Jama`at</td></tr></table>')
            ,(16,'Umur Amma','UA','','<table border=\"0\"><tr><td width=\"200\"><b>Dispute Resolution:</b></td></tr> <tr><td width=\"200\">Activities carried out to:</td></tr> <tr><td width=\"200\">Analyze the root cause of issues that arise among individuals, families and societies</td></tr> <tr><td width=\"200\">Organize educational sessions to educate members on how to avoid or resolve these issues</td></tr> <tr><td width=\"200\">Promote an atmosphere of reconciliation in case of disputes</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Report any dispute settled down this month? What measures were taken to avoid such things in the future? (Please attach details)</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\"><b>Employment & Guidance:</b></td></tr> <tr><td width=\"200\">Strategy devised to:</td></tr> <tr><td width=\"200\">Assist new members in getting settled and integrated in the Canadian system</td></tr> <tr><td width=\"200\">Train members in various trades and professions according to the market demand.</td></tr> <tr><td width=\"200\">Guide individuals to step into the Canadian business market</td></tr> <tr><td width=\"200\">Assist unemployed members in getting jobs</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\"><b>Media Coverage:</b></td></tr> <tr><td width=\"200\">Report any significant or relevant news item in media. Provide details of action taken with original transcript</td></tr> <tr><td width=\"200\"><b>Majlis Sehat:</b></td></tr> <tr><td width=\"200\">Report any sports activities organized or promoted</td></tr></table>')
            ,(17,'Umur Kharijiyya','UK','','<table border=\"0\"><tr><td width=\"200\">Efforts made to:</td></tr> <tr><td width=\"200\">Contact government officials and members of Parliament to ensure observance of Human rights in the society</td></tr> <tr><td width=\"200\">Create awareness among officials about persecutions of Ahmadis in Pakistan and elsewhere</td></tr> <tr><td width=\"200\">Write articles in newspapers for educating the general public on Islam and Ahmadiyyat</td></tr> <tr><td width=\"200\">Contact other communities to promote mutual understanding</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Report the number of articles written in the local news papers on Human Rights and Islam</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Strategy devised to:</td></tr> <tr><td width=\"200\">Ensure good relations between Jama`at and other communities and religions</td></tr> <tr><td width=\"200\">Educate general public on Islamic teachings and concepts</td></tr> <tr><td width=\"200\">Protect the rights of members</td></tr> <tr><td width=\"200\">Establish an atmosphere of mutual trust and friendship with government officials and agencies working for Human rights</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Efforts made to develop relationship with local and national media.</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Results of your efforts and strategies?</td></tr></table>')
            ,(18,'Waqf Jadid','WJ','','<table border=\"0\"><tr><td width=\"200\">Efforts made to:</td></tr> <tr><td width=\"200\">Educate members about the philosophy, history and importance of Waqf Jadid</td></tr> <tr><td width=\"200\">Include non-participating members into this Tahrik</td></tr> <tr><td width=\"200\">Encourage members to participate with enhanced vigor & devotion</td></tr> <tr><td width=\"200\">Include children to the scheme of Nanhay Mujahedeen of Waqf Jadid</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Activities carried out to:</td></tr> <tr><td width=\"200\">Get pledges from members</td></tr> <tr><td width=\"200\">Improve collection procedures for timely submission</td></tr> <tr><td width=\"200\">Approach members who have not made any payment towards their pledge yet</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\"><b>Pledges:</b></td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Report collection this Month.</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Specify collection year-to-date.</td></tr></table>')
            ,(19,'Waqf Jadid (for new Ahmadis)','WS','','<table border=\"0\"><tr><td width=\"200\">Efforts made to:</td></tr> <tr><td width=\"200\">Educate new Ahmadis about the philosophy, history and importance of Waqf Jadid</td></tr> <tr><td width=\"200\">Include non-participating new Ahmadis into this Tahrik</td></tr> <tr><td width=\"200\">Contact and build a strong relationship with New Ahmadis and their families.</td></tr></table>')
            ,(20,'Waqf Nau','WN','','<table border=\"0\"><tr><td width=\"200\"><b>Tarbiyat:</b></td></tr> <tr><td width=\"200\">Efforts made to:</td></tr> <tr><td width=\"200\">Make the Waqfeen punctual in offering Namaz, Tahajud and reciting the Holy Quran</td></tr> <tr><td width=\"200\">Encourage them to watch MTA daily, specially, Huzur’s Friday sermon</td></tr> <tr><td width=\"200\">Make the Ijlas Aam for Waqfeen an effective session</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\"><b>Ta`lim & Tabligh:</b></td></tr> <tr><td width=\"200\">Strategies devised to:</td></tr> <tr><td width=\"200\">Teach Waqifeen simple Namaz, Namaz with translation and Qur`an with translation?</td></tr> <tr><td width=\"200\">Educate them with religious knowledge, such as, books of Promised Messiah (AS), Ahadith etc.</td></tr> <tr><td width=\"200\">Equip them with tools to excel in religious & general knowledge for Tabligh</td></tr> <tr><td width=\"200\">Train Waqifeen in Tabligh</td></tr> <tr><td width=\"200\">Teach them multiple languages</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Steps taken to:</td></tr> <tr><td width=\"200\">Ensure physical fitness by promoting sports</td></tr> <tr><td width=\"200\">Provide counseling for the education of the child</td></tr> <tr><td width=\"200\">Arrange trips or Outings</td></tr> <tr><td width=\"200\">Make the meeting with parents effective</td></tr> <tr><td width=\"200\">Total Number of Waqifeen: Male? Female?</td></tr></table>')
            ,(21,'Wasayya','WA','','<table border=\"0\"><tr><td width=\"200\">Efforts made to:</td></tr> <tr><td width=\"200\">Increase the number of Musis in Jama`at</td></tr> <tr><td width=\"200\">Educate members of the philosophy and history the Nizam Wasiyyat</td></tr> <tr><td width=\"200\">Implement the conditions of Wasiyyat among members</td></tr> <tr><td width=\"200\">Organize meeting of Musis to promote the concept, importance and responsibilities</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Strategy devised to:</td></tr> <tr><td width=\"200\">Develop an education program for Musis and make it effective</td></tr> <tr><td width=\"200\">Improve the Wasiyyat chanda collection procedures</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Total No. of members registered in the Nizam-e-Wasiyyat.</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">No. of declaration and verification of income forms submitted.</td></tr> <tr><td width=\"200\">&nbsp;</td></tr> <tr><td width=\"200\">Report any educational or informational programs organized for Musis.</td></tr></table>')
            ,(22,'Zira`at','ZT','','<table border=\"0\"><tr><td width=\"200\">Efforts made to:</td></tr> <tr><td width=\"200\">Promote agriculture and farming as a profession in members</td></tr> <tr><td width=\"200\">Educate members in agricultural knowledge</td></tr> <tr><td width=\"200\">Create awareness about benefits of gardening and greenhouse</td></tr> <tr><td width=\"200\">Encourage kitchen/vegetable gardens in backyards</td></tr></table>')
            ,(23,'Ta`limul Qur`an Waqf `Ardi','TI','','<table border=\"0\"><tr><td width=\"200\">Efforts made to:</td></tr> <tr><td width=\"200\">He shall make arrangements to teach bare reading of the Holy Qur’an to those who cannot and to teach its meanings and commentary to those who already know the bare reading. For these purposes he shall organize Ta`limul Qur’an classes at the local regional and national level.</td></tr> <tr><td width=\"200\">He shall motivate the members of the Jama’at to lean reading of the Holy Qur’an with correct pronunciation. For this purpose he will hold classes in the Jama`at. The object of these classes will be to prepare teachers who should be able to teach to other members of the Jama`at the reading of the Holy Qur’an with correct pronunciation.</td></tr> <tr><td width=\"200\">He shall motivate the members of the Jama`at to participate in Waqf ‘Ardi. After obtaining approval from the Amir, he shall send delegations of Waqf ‘Ardi to different Jama`ats.<br><br>The delegations of Waqf ‘Ardi shall submit reports comprising of their observations to the secretary.</td></tr></table>')
            ,(24,'Internal Auditor','IA','','')
            ,(25,'Tabshir','TR','','')
           ";             
           
        $this->connection->executeQuery($sql_departments );    

    }    

}
