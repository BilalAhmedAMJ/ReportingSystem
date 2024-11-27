Some tips for zf2 development
=============================




###Using a custom layout

Layout to be used for site is configured by `view_manager` config of a module.
By default zf2 skelteton app uses `view/layout/layout.phtml` file for layout.
However, in your controller''s action, make sure to change layput to be used for view rendering.
You can do this by making a call like this `$this->layout('layout/layout_login.phtml');`, 
it  will use layout_login.phtml layout template for view rendring. 
Parameter to call location for layout file relative to configured views directory. 
Remember, by convention zf2 assumes there is a "layout" dir. in your view directory for the module. 

- When creating a custom layout make sure you include `<?php echo $this->content; ?>` where you want view contents to be rendered inside main layout. 
- Also include a call to `<?php echo $this->inlineScript() ?>` just before closing body tag, to include any scripts needed by the view


###Custom template

By default config of skelteton app zf2 will match called action method of controller with a tempalte assuming that there is a dir. with the name mattching controller name (without Controler word) and there is a file with action name (without Action word) having .phtml extension.
However, this default behaviour can be changed by manually setting tempalte inside action method. You cna do this by called `$view->setTemplate('location_of_template_inside_view_dir');`, e.g. to use login.phtml view rendering template for any action we will call `$view->setTemplate('auth/login.phtml');`  
