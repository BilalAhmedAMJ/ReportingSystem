<?php

function emailAsFileTransportFileName($transport) {
        return 'Message_' . microtime(true) . '_' . mt_rand() . '.txt';
}

return array(
    //Application specific config items
    'application' => array(
            'data-dir' => __DIR__.'/../../../../data',
            'uploads_dir'=> __DIR__.'/../../../../data/uploads',
            'cache_dir'=> __DIR__.'/../../../../data/cache',
            'log_dir'=> __DIR__.'/../../../../data/logs',
            'email'=>array(                
                'from'=>array('name'=>'AMJ Reports','email'=>'reports@ahmadiyya.ca'),
                'transport'=>array(
                    //'type'   => 'file',
                    'type' => (getenv('APPLICATION_ENV') == 'production' ? 'sendmail' : 'file'),
                    //'type'=>'smtp',
                    /*
                    'smtp_options'=>array(//smtp ahmadiyya.ca
                        'name'              => 'mail.ahmadiyya.ca',
                        'host'              => 'mail.ahmadiyya.ca',
                        'port'              => '25',
                        'connection_class'  => 'login',
                        'connection_config' => array(
                            'username' => 'reports-admin@ahmadiyya.ca',
                            'password' => 'R3p0rts.AdminTest',
                            ),
                        ),//smtp
                    */
                    //SMTP smtp options AWS SES  mail server 
		 
                    'smtp_options'=>array(
                        'name'              => 'email-smtp.us-east-1.amazonaws.com',
                        'host'              => 'email-smtp.us-east-1.amazonaws.com',
                        'port'              => '587', // Notice port change for TLS is 587
                        'connection_class'  => 'login',
                        'connection_config' => array(
                            'username' => 'AKIAJC3GWOO52JPR3KSQ',
                            'password' => 'AoubezHLT776bRdNBKanFp6SNatZo9PWRBRQ+0qAIL/G',
                            'ssl' => 'tls',
                        ),
                    ),//smtp
		 

                    //SMTP smtp options AMJ mail server 
                    //Google SMTP  
                    /*
                        'smtp_options'=>array(
                        'name'              => 'smtp.gmail.com',
                        'host'              => 'smtp.gmail.com',
                        'port'              => '587', // Notice port change for TLS is 587
                        'connection_class'  => 'login',
                        'connection_config' => array(
                            'username' => 'easycontacts.ca@gmail.com',
                            'password' => `/var/local/amj/email`,
                            'ssl' => 'tls',
                            ),
                        ),//smtp
                    */  
		            'file_options'=>array(
                        'path'      => __DIR__.'/../../../../data/mail/',
                        'callback'  => 'emailAsFileTransportFileName',
                    ),//file options
                ),//transport
             ),//eamil
     )
);

