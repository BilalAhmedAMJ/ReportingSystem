var ReportForms;

ReportForms = ReportForms || 
{

	validate:function(form,config){		
		
		if(typeof config === "undefined"){
			config={};
		}
		
		if(config['errorElement'] === undefined ){
			config['errorElement']='label';
		}
		if(config['errorClass'] === undefined ){
			config['errorClass']= 'label label-danger bigger-105 validation-error-label';//'help-block';
		}
			

		if(config['highlight'] === undefined ){
			config['highlight']=function (element) {
								if ($(element).hasClass("select2-offscreen")) {
									//
									//DONOT highlight select2 fields otherwise it will not work 
								} else {
									$(element).closest('.form-group').removeClass('has-info').addClass('has-error');
								}
                          };
		}                          
		if(config['success'] === undefined ){
			config['success']=function (element) {
                                $(element).closest('.form-group').removeClass('has-error').addClass('has-info');
                                $(element).remove();
                           };
		}
		
		config['ignoreTitle']=true;
		config['focusInvalid']=false;
		config['focusCleanup']=true;		                            
		config['ignore']='button, .select2-input, .select2-focusser';


		//config['debug']=true;
		
		$(form).validate(config);	 		
	},

	file_input:function(input_id){
	    if( (typeof input_id) == 'string'){
            if( input_id.substr(0,1) != '#' ){
               input_id = '#'+input_id;
            }
	    }

        $(input_id).ace_file_input({
            no_file:'Select a file to attach...',
            btn_choose:'Choose',
            btn_change:'Change',
            droppable:false,
            onchange:null,
            thumbnail:false //| true | large
            //whitelist:'gif|png|jpg|jpeg'
            //blacklist:'exe|php'
            //onchange:''
            //
        });        
        
        $('.ace-file-input').addClass('input-xlarge');//css('width','350px');
        $('.ace-file-name .ace-icon').css('margin-left','3px');
        $('.ace-file-container').parent().css('margin-right','5px');	    
	},
	
	/*
        {toolbar:[


        
                { name: 'styles', items: ['Save', 'Styles', 'Format', 'Font', 'FontSize' ] , '-':'-', 'Language':'Language' },
                { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
                { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], 
                    items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] 
                },
                { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
                { name: 'clipboard', groups: [ 'clipboard', 'undo' ], 
                    items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] 
                },
                { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], 
                    items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent','Blockquote',  '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] 
                },                
                { name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule','base64image' ] },
    
                //{ name: 'document',    items : [ 'Source','-','Save','NewPage','DocProps','Preview','Print','-','Templates' ] },
                //{ name: 'clipboard',   items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
                //{ name: 'editing',     items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
                //{ name: 'forms',       items : [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
                //'/',
                    
            ]}

	 */
	saveForm:function(event){
	    var form = event.editor.element.$.form;
	    //console.log(event);
	    
	    if(event.preventDefault)
	    	event.preventDefault();
	    
	    if(event.stop)
	    	event.stop();
	    
	    var desc = $(form).data('description');
	    desc = desc?desc:'';
	    var ajaxUrl = addParamToUrl(form.action,'view','ajax');
	    //console.log(['saving ',form.action,form.method,form]);
	    //$(form).ajaxSubmit();
	    $.post(ajaxUrl,$(form).serialize(),
	       function(){
            //notify user that we have saved data
            jQuery.gritter.add({
                title: desc+' is saved successfully!',
                //text: 'A new section for '+title+' is added!',
                //image: 'path/to/image',
                sticky: false,
                time: 3000,
                class_name: 'gritter-success gritter-light '
            });
	    }).fail(function(){
            //notify user for error
            jQuery.gritter.add({
                title: 'Unable to save report!',
                text: 'Please use "Save as ..." button on top or bottom of report.' ,
                //image: 'path/to/image',
                sticky: false,
                time: 3000,
                class_name: 'gritter-error gritter-light '
            });
	    })
	    ;
	    
	    
	    return false;
	},
	ckeditor:function(elem){
	    $(elem).ckeditor(
	       function(textarea){
                $(textarea).siblings('.editor_loading').addClass('hidden');
                CKEDITOR.instances[$(textarea).attr('id')].on('save',ReportForms.saveForm);
	        },
	        {
	        //skin:'BootstrapCK-Skin',
	        customConfig : '',
	        uiColor :'#e1eaf2',
	        pasteFromWordRemoveFontStyles : false,
	        //extraPlugins : 'base64image,ckeditor-gwf-plugin',
	        extraPlugins : 'base64image',
	        //font_names:'GoogleWebFonts',
	        toolbar:[
            
                {name:'document',      items:['Save']},
                { name: 'styles',      items : [ 'Styles','Format','Font','FontSize' ] },
                { name: 'colors',      items : [ 'TextColor','BGColor' ] },
                { name: 'insert',      items : [ 'Image','Table','HorizontalRule', 'base64image'] },
                { name: 'tools3',      items : [ 'Zoom'] },
                { name: 'tools',       items : [ 'Maximize'] },
                '/',
                { name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
                { name: 'paragraph',   items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock' ] },
                { name: 'links',       items : [ 'Link','Unlink','Anchor' ] },
                { name: 'tools2',      items : [ 'Print','Preview'] },            
                { name: 'clipboard',   items : [ 'PasteText','PasteFromWord','-','Undo','Redo' ] }
            ],
            
	        })

	        ;
	}
};


function addParamToUrl(url,paramName,paramVal){
	var ajaxUrl=url;
	if(ajaxUrl.indexOf('?')<0){
		ajaxUrl=ajaxUrl+'?';
	}else if(ajaxUrl.indexOf(paramName+'=')>0){
		//remove current paramName/paramVal and add new one
		var start=ajaxUrl.indexOf(paramName+'=');
		var end=ajaxUrl.indexOf('&',start);
		if(end<start){
			end=ajaxUrl.length;
		}
		ajaxUrl=ajaxUrl.substr(0,start)+ajaxUrl.substr(end,ajaxUrl.length);
	}
	return ajaxUrl+'&'+paramName+'='+paramVal;
}

function imageResizeForWebkit(){
    //RESIZE IMAGE
    
    //Add Image Resize Functionality to Chrome and Safari
    //webkit browsers don't have image resize functionality when content is editable
    //so let's add something using jQuery UI resizable
    //another option would be opening a dialog for user to enter dimensions.
    if ( typeof jQuery.ui !== 'undefined' && ace.vars['webkit'] ) {
        
        var lastResizableImg = null;
        function destroyResizable() {
            if(lastResizableImg == null) return;
            lastResizableImg.resizable( "destroy" );
            lastResizableImg.removeData('resizable');
            lastResizableImg = null;
        }

        var enableImageResize = function() {
            $('.wysiwyg-editor')
            .on('mousedown', function(e) {
                var target = $(e.target);
                if( e.target instanceof HTMLImageElement ) {
                    if( !target.data('resizable') ) {
                        target.resizable({
                            aspectRatio: e.target.width / e.target.height,
                        });
                        target.data('resizable', true);
                        
                        if( lastResizableImg != null ) {
                            //disable previous resizable image
                            lastResizableImg.resizable( "destroy" );
                            lastResizableImg.removeData('resizable');
                        }
                        lastResizableImg = target;
                    }
                }
            })
            .on('click', function(e) {
                if( lastResizableImg != null && !(e.target instanceof HTMLImageElement) ) {
                    destroyResizable();
                }
            })
            .on('keydown', function() {
                destroyResizable();
            });
        };
        enableImageResize();
        /**
        //or we can load the jQuery UI dynamically only if needed
        if (typeof jQuery.ui !== 'undefined') enableImageResize();
        else {//load jQuery UI if not loaded
            $.getScript($path_assets+"/js/jquery-ui.custom.min.js", function(data, textStatus, jqxhr) {
                enableImageResize()
            });
        }
        */
      }//if chrome set image resize
}


function dialogForm(url,dlgTitle){		
	jQuery.ajax({
	    type: 'POST',
            async: true,
	    url: addParamToUrl(url,'view','ajax'),
	    success: function(data) {
			//body = $(data);
			//console.log($(data));
	        var dlg = bootbox.dialog({
	        	id:'dlg_Form',
	        	closeButton: false,
	        	onEscape:true,
	        	className:' widget-box2 widget-color-blue widget-main',
	        	backdrop: true,
	            message: '<div class="clearfix" style="margin:auto">'+data+'</div>',	            
	            //title: 'Edit',//dlgTitle?dlgTitle:'Edit Form',
	            buttons: {
	                cancel: {
	                    label: "Cancel",
	                    className: "btn-danger btn-white btn-round",
	                },
	                success: {
	                    label: "Submit",
	                    className: "btn-primary  btn-white  btn-round",
	                    callback:function(){
	                    	 var form = $(dlg).find('form');
						      $.ajax({
						        type: form.attr('method'),
						        url: addParamToUrl(form.attr('action'),'view','ajax'),
						        data: form.serialize()
						      }).done(function(data) {
						      	//console.log(data);
				                 if(data.indexOf('alert-danger')<0){
			                    		 $.gritter.add({
						                     title: 'Updated Office Bearer: Refreshing list',
						                     text: data,
						                     class_name: 'gritter-success  gritter-light'
						                 });
				                 		$('form[name="form_defaultGrid"]').submit();				                 		
				                 	}else{
			                    		 $.gritter.add({
						                     title: 'Failed to update Office Bearer',
						                     text: data,
										     sticky: false,
										     time: 4000,
						                     class_name: 'gritter-error  gritter-light'
						                 });
				                 	}

						      }).fail(function(data) {
	                    		 $.gritter.add({
				                     title: 'Failed to update Office Bearer',
				                     text: data,
								     sticky: false,
								     time: 2000,
				                     class_name: 'gritter-error  gritter-light'
				                 });
						      });
	                    },
	                },
	            }
	        });
	    }
	});	
}


var jsUtils;
jsUtils = jsUtils || (function () {
    var pleaseWaitDiv = $('<div class="modal hide" id="pleaseWaitDialog" data-backdrop="static" data-keyboard="false"><div class="modal-header"><h1>Processing...</h1></div><div class="modal-body"><div class="progress progress-striped active"><div class="bar" style="width: 100%;"></div></div></div></div>');
    return {
        showPleaseWait: function() {
            pleaseWaitDiv.modal();
        },
        hidePleaseWait: function () {
            pleaseWaitDiv.modal('hide');
        },

    };
})();




 function deleteDocument(doc_id){
        var overlay = $('#dialog');
        $.get('/document/edit/'+doc_id )
        .done(function( data ) {
	    		if($("form[name='form_generic_grid']").size()>0){
	    			$("form[name='form_generic_grid']").submit();
	    		}
	
	    		if($("form[name='form_uploaded_grid']").size()>0){
	    			$("form[name='form_uploaded_grid']").submit();
	    		}
        });
 }

