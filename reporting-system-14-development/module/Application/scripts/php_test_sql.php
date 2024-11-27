<?php

$sql='CREATE TABLE questions (id INT AUTO_INCREMENT NOT NULL, report_config VARCHAR(255) NOT NULL, department_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, question_type ENUM(\'TEXT\',\'MEMO\',\'DATE\',\'SELECT\',\'YES_NO\',\'TABLE\',\'GRID\',\'LABEL\',\'FILE\',\'NUMBER\'), answer_type VARCHAR(255) DEFAULT NULL, caption VARCHAR(255) DEFAULT NULL, details VARCHAR(255) DEFAULT NULL, display_config LONGTEXT DEFAULT NULL, contraints LONGTEXT DEFAULT NULL, sort_order VARCHAR(255) DEFAULT NULL, active_question TINYINT(1) DEFAULT \'1\' NOT NULL, INDEX IDX_8ADC54D5FD79FAE3 (report_config), INDEX IDX_8ADC54D5AE80F5DF (department_id), INDEX IDX_8ADC54D5727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB';
print $sql.PHP_EOL;


 print ('CREATE TABLE report_config (report_code VARCHAR(255) NOT NULL, report_name VARCHAR(256) NOT NULL, levels LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', freq VARCHAR(255) NOT NULL, role_create LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', role_view LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', role_verify LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', role_receive LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', status ENUM(\'active\', \'disabled\'), PRIMARY KEY(report_code)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
print PHP_EOL;
 
 
$sql=('ALTER TABLE questions ADD CONSTRAINT FK_8ADC54D5FD79FAE3 FOREIGN KEY (report_config) REFERENCES report_config (report_code)');
print $sql.PHP_EOL;

$sql=('ALTER TABLE questions ADD CONSTRAINT FK_8ADC54D5AE80F5DF FOREIGN KEY (department_id) REFERENCES departments (id)');
print $sql.PHP_EOL;

$sql=('ALTER TABLE questions ADD CONSTRAINT FK_8ADC54D5727ACA70 FOREIGN KEY (parent_id) REFERENCES questions (id)');
print $sql.PHP_EOL;





$sql="
insert into report_config values
('gs_report_monthly','General Secretary Report','Halqa,Jamaat','monthly','general_secretary',
        'general_secretary,president,local_amir,national_amir','president,local_amir','general_secretary','active'
),
('secretary_report_monthly','Secretary Report','Halqa,Jamaat','monthly','secretary',
        'secretary,president,local_amir,national_amir','president,local_amir','secretary','active'
)
";

print $sql.PHP_EOL;

        $sql=" insert into questions values        
            (1, 'gs_report_monthly',5, null,'TABLE','NONE','Majlis `Amila Meeting','','{\"table_display\":{\"type\":\"simple\",\"size\":\"2,3\",\"dimention\":{1:3,2:1}}}','{\"multiple_answers\":true}',1,1),
            (2, 'gs_report_monthly',5, 1,'YES_NO','YES_NO','Meeting held<br>(Please check)','','','',2,1),
            (3, 'gs_report_monthly',5, 1,'DATE','DATE','Date<br>(YYYY-MM-DD)','','','',3,1),
            (4, 'gs_report_monthly',5, 1,'NUMBER','NUMBER','Attendance','','','',4,1),
            (5, 'gs_report_monthly',5, 1,'FILE','FILE','Attachment','','','',5,1),
            (6, 'gs_report_monthly',5, 1,'MEMO','MEMO','Meeting minutes (if not attached)','','','',6,1),

            (7,  'gs_report_monthly',null, null,'TABLE','NONE','General Body Meeting','','{\"table_display\":{\"type\":\"simple\",\"size\":\"3,4\",\"dimention\":{1:4,2:4,3:1}}}','{\"multiple_answers\":true}',7,1),
            (8,  'gs_report_monthly',5, 7,'YES_NO','YES_NO','Meeting held<br>(Please check)','','','',8,1),
            (9,  'gs_report_monthly',5, 7,'DATE','DATE','Date<br>(YYYY-MM-DD)','','','',9,1),
            (10, 'gs_report_monthly',5, 7,'TEXT','TEXT','Location','','','',10,1),
            (11, 'gs_report_monthly',5, 7,'FILE','FILE','Attachment','','','',11,1),
            (12, 'gs_report_monthly',5, 7,'NUMBER','NUMBER','Total Attendance','','','',12,1),
            (13, 'gs_report_monthly',5, 7,'NUMBER','NUMBER','Gents Attendance','','','',13,1),
            (14, 'gs_report_monthly',5, 7,'NUMBER','NUMBER','Ladies Attendance','','','',14,1),
            (15, 'gs_report_monthly',5, 7,'NUMBER','NUMBER','Children Attendance','','','',15,1),
            (16, 'gs_report_monthly',5, 7,'MEMO','MEMO','Brief report of the meeting (if not attached)','','','',16,1),


            (17, 'gs_report_monthly',null, null,'TABLE','NONE','Visits (Attach report if required)','','{\"table_display\":{\"type\":\"simple\",\"size\":\"2,2\",\"dimention\":{1:2,2:1}}}','{\"multiple_answers\":true}',17,1),
            (18, 'gs_report_monthly',5, 17,'NUMBER','NUMBER','No. Households visited by President / General Secretary this month','','','',18,1),
            (19, 'gs_report_monthly',5, 17,'TEXT','TEXT','Comments','','','',19,1),
            (20, 'gs_report_monthly',5, 17,'NUMBER','NUMBER','No. Households visited by Members of Majlis `Amila this month','','','',20,1),
            (21, 'gs_report_monthly',5, 17,'TEXT','TEXT','Comments','','','',21,1),
            (22, 'gs_report_monthly',5, 17,'FILE','FILE','Attachment','','','',22,1),
            
            (23, 'gs_report_monthly',5, null,'GRID','GRID','Report of Sa`iqeen','','','',23,1),
            (24, 'gs_report_monthly',5, 23,'TEXT','TEXT','Name of Sa`iq','','','',24,1),
            (25, 'gs_report_monthly',5, 23,'NUMBER','NUMBER','No. of households visited in person','','','',25,1),
            (26, 'gs_report_monthly',5, 23,'NUMBER','NUMBER','No. of households contacted via phone','','','',26,1),
            (27, 'gs_report_monthly',5, 23,'NUMBER','NUMBER',' No. of households messages conveyed','','','',27,1),

            (28, 'gs_report_monthly',5, null,'MEMO','MEMO','List all departmental activities & achievements for this month','','','{\"validation\":[{\"empty\":\"false\"}]}',28,1),
            (29, 'gs_report_monthly',5, null,'FILE','FILE','Attachment','','','',29,1),
            (30, 'gs_report_monthly',5, null,'MEMO','MEMO','Problems encountered in carrying out above activities','','','',30,1),
            (31, 'gs_report_monthly',5, null,'MEMO','MEMO','Any help required from Markaz for your department','','','',31,1),
            (32, 'gs_report_monthly',5, null,'MEMO','MEMO','Activities planned for next month','','','{\"validation\":[{\"empty\":\"false\"}]}',32,1),
            (33, 'gs_report_monthly',5, null,'MEMO','MEMO','Any other comments','','','',33,1)                  
                                                                    
        ";                

print $sql.PHP_EOL;
        

        $sql="insert int questions values
            (0, 'secretary_report_monthly',1, null,'MEMO','MEMO','List all departmental activities & achievements for this month','','','{\"validation\":[{\"empty\":\"false\"}]}',1,1),
            (0, 'secretary_report_monthly',1, null,'FILE','FILE','Attachment','','','',2,1),
            (0, 'secretary_report_monthly',1, null,'MEMO','MEMO','Problems encountered in carrying out above activities','','','',3,1),
            (0, 'secretary_report_monthly',1, null,'MEMO','MEMO','Any help required from Markaz for your department','','','',4,1),
            (0, 'secretary_report_monthly',1, null,'MEMO','MEMO','Activities planned for next month','','','{\"validation\":[{\"empty\":\"false\"}]}',5,1),
            (0, 'secretary_report_monthly',1, null,'MEMO','MEMO','Any other comments','','','',6,1)                  
        ";

print $sql.PHP_EOL;

        $sql="
            insert into questions
            select 0,report_config, d.id , parent_id , question_type , answer_type , caption , details , display_config , contraints , sort_order,1 
            from questions q, departments d where d.id not in (1,5) and q.department_id=1  
        ";                
print $sql.PHP_EOL;
                