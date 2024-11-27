

var SimpleDataGrid={

	
	create:function(config){
		var grid_selector=config['grid_selector'];
		var pager_selector=config['pager_selector'];
		//load scripts required for grid and upon successful load build Grid element 
		CommonDataGrid.loadScripts(function(){
									SimpleDataGrid.buildJqGrid(grid_selector,pager_selector,config);
									});

		//if(config[edit_grid]){
		//	this.setupEdit(grid_selector);
		//}
		//trigger window resize to make the grid get the correct size
		$(window).triggerHandler('resize.jqGrid');
		
	},
	
	buildJqGrid:function(grid_selector,pager_selector,config){

		//hook  grid resize upon window resizing   
	    $(window).resize(function() {
			CommonDataGrid.windowResize(grid_selector);
			$(window).triggerHandler('resize.jqGrid');
	    });


		var params= {
		   orig_config:config,
		   url: config['url'],
           datatype: "json",
		   jsonReader : {
					      root:config['dataRoot']?config['dataRoot']:'data',
					      page: 1,
					      total: config['paginator_total']?config['paginator_total']:function(obj){ 
												return Math.ceil(obj.data.length/jQuery(grid_selector).jqGrid('getGridParam','rowNum')); 
											},//total number of pages
					      records: config['paginator_records']?config['paginator_records']:function(obj){return obj.data.length;},
					      repeatitems: false,
					      id: "0"
					   },
		  loadComplete : function (data) {
			  	//jQuery(grid_selector).jqGrid('setGridParam',{datatype:'local'});
				  //to enable client side loading 
				CommonDataGrid.updatePagerIcons(this);
				 //if there is memoSubGrid, expand all rows after a while
				var orig_config=jQuery(grid_selector).jqGrid('getGridParam','orig_config');		        
				//select first row on load
		        jQuery(grid_selector).setSelection(1);
		        jQuery(grid_selector).setGridParam({datatype:'json'});
		        
				if(orig_config['memoSubGrid']){
					var sub_data={};
					for(var row in data.data){
						sub_data[data.data[row].id]=data.data[row];
					}
					jQuery(grid_selector).jqGrid('setGridParam',{sub_data:sub_data});
					console.log('loadCompleted');
					setTimeout(function() {
							//var rows=jQuery(grid_selector).jqGrid('getGridParam','rowNum');
							//for(var row=1;row<=rows;row++){
							//	jQuery(grid_selector).expandSubGridRow(row);						
							//}
							//jQuery(grid_selector).expandSubGridRow(1);	
							//var ids = jQuery(grid_selector).jqGrid("getDataIDs");
					        //if(ids.length > 0) { 
					        //    grid.jqGrid("setSelection", ids[0]);
					        //}
						}, 100);
				 }
			
		 },
		 
		 
	    beforeRequest: function () {
		 
		 },
		 
		afterSaveCell: function(rowid,name,val,iRow,iCol) {
		    $(grid_selector).setGridParam({datatype:'json'});
			$(grid_selector).trigger("reloadGrid"); 
        },		 
		jqGridAddEditAfterSubmit:function(rowid,name,val,iRow,iCol) {
		    $(grid_selector).setGridParam({datatype:'json'});
			$(grid_selector).trigger("reloadGrid"); 
        },		  
		  //mtype:'post',
		  loadonce: config['paginator']?false:true,
   		  colModel:config['colModel'],
   		  rowNum:config['rowNum']?config['rowNum']:10,
   		  pager:pager_selector,
		  altRows: true,
		  viewrecords: true,
		  gridview: true,
		  height: (config['height']?config['height']:325),
		  //height:'100%',
		  autowidth:(config['width']?false:true),
		  shrinkToFit: true,
		  ignoreCase: true,
		};
		
		if(config['mtype']){
			params['mtype']=config['mtype'];
		}
				//set edit col for editable grids
		if(config['editable']){
			
			 //params['onSelectRow'] 
			params['editurl']=config['editurl'];
			params['editparameters']={keys:false};
			
		}
		
		
		params['ondblClickRow']=function(id){
							 var lastSel=jQuery(grid_selector).jqGrid('getGridParam','lastSel');
						     if(id && id!==lastSel){ 
						        jQuery(grid_selector).restoreRow(lastSel); 
						        lastSel=id;
						        jQuery(grid_selector).jqGrid('setGridParam',{'lastSel':lastSel});
						        
						     }
						     //console.log(config);
						     if(config['editable'] && ( !('noinline' in config) || config['noinline']==false) && ( !('nodblclick' in config) || config['nodblclick']==false) ){
						     	//console.log(config);
								jQuery(grid_selector).jqGrid('editGridRow',id,
											{	width:'700',labelswidth:'75%',
											    closeAfterEdit : true,
    										    reloadAfterSubmit:true,
												beforeShowForm : function(e) {
													//console.log('beforeedit');
													var form = $(e[0]);
													form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />');
													CommonDataGrid.style_edit_form(form);
												},
                                                afterSubmit:function(){
                                                        var grid=$(this);
                                                        grid.setGridParam({datatype:'json'});
                                                        grid.trigger("reloadGrid");
                                                        return true;
                                                    },
											});
								
						     }else if(config['editable'] && config['noinline'] && !(config['nodblclick']) ){
								//console.log('else no inline');
								// form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />');
								// CommonDataGrid.style_edit_form(form);
								jQuery(grid_selector).jqGrid('editRow',id,  {width:'600',labelswidth:'75%'});
								//jQuery(grid_selector).editGridRow(id, true);
							 }else{
							 	//console.log('else edit');
								//jQuery(grid_selector).viewGridRow(id, false);
								jQuery(grid_selector).jqGrid('viewGridRow',id, {width:'400',labelswidth:'65%'});
							 } 
						   };
			
		
		if(config['memoSubGrid']){
			
			params['subGrid']=true;
			params['subGridOptions']=CommonDataGrid.memoSubGrid['subGridOptions'];
			params['subGridRowExpanded']=CommonDataGrid.memoSubGrid['subGridRowExpanded'](grid_selector,config['memoSubGrid']['col_model']);
		}

		if (config['gridTitle']) {
			params['caption']=config['gridTitle'];
		}
		
		if (config['postparams']){
			params['editParams']=config['postparams'];
			params['editData']=config['postparams'];
			params['postData']=config['postparams'];
			
		}
		
		//build grid with provided parameters
	     jQuery(grid_selector).jqGrid(params);
	     
	     //set nav after building grid if requested
	     //jQuery(grid_selector).jqGrid('navGrid',pager_selector,{del:false});
	     
		 jQuery(grid_selector).jqGrid('navGrid',pager_selector,CommonDataGrid.navButtons(config['editable']),
		 								CommonDataGrid.navEdit,
		 								CommonDataGrid.navAdd,
		 								CommonDataGrid.navDel,
		 								CommonDataGrid.navSearch,
		 								CommonDataGrid.navView
		 								);
				 								   
		jQuery(grid_selector).jqGrid('filterToolbar',{autosearch:true,searchOnEnter:true,defaultSearch:'cn',enableClear:true});
	
		jQuery(grid_selector).jqGrid('bindKeys',{scrollingRows :true,onEnter:function(rowId){jQuery(grid_selector).jqGrid('editRow',rowId,true);}});
	
		//resize grid initially after creation   
		setTimeout(function() {
					CommonDataGrid.windowResize(grid_selector);
					$(grid_selector).setGridParam({datatype:'json'});
					//console.log(jQuery(grid_selector).getRowData());//setSelection);
				}, 100);		
	
	},//Done buildGrid
	
	setupEdit:function(grid_selector){
		
	}
		
	
};




var CommonDataGrid={
	windowResize:function(grid_selector){
		
		//resize to fit page size
		$(window).on('resize.jqGrid', function () {			
			if($(grid_selector).jqGrid){
				
				var container=$(grid_selector).parent().parent().parent().parent().parent();
				$(grid_selector).jqGrid( 'setGridWidth', container.width() );
				//$(grid_selector).jqGrid( 'setGridHeight', container.height() );
				//console.log(container.length);	
			}
	   });
	   
		// //resize on sidebar collapse/expand
		// var parent_column = $(grid_selector).closest('[class*="col-"]');
		// $(document).on('settings.ace.jqGrid' , function(ev, event_name, collapsed) {
			// if( event_name === 'sidebar_collapsed' || event_name === 'main_container_fixed' ) {
				// //setTimeout is for webkit only to give time for DOM changes and then redraw!!!
				// setTimeout(function() {
					// $(grid_selector).jqGrid( 'setGridWidth', parent_column.width() );
				// }, 0);
			// }
	   // });
	},
	loadScripts:function(callBack){
		var scripts = [null,"../assets/js/date-time/bootstrap-datepicker.min.js"
						   ,"../assets/js/jqGrid/jquery.jqGrid.min.js",
						   "../assets/js/jqGrid/i18n/grid.locale-en.js", null];
		ace.load_ajax_scripts(scripts, callBack);
		
	},
	//replace icons with FontAwesome icons like above
	updatePagerIcons: function (table) {
			var replacement = 
			{
				'ui-icon-seek-first' : 'ace-icon fa fa-angle-double-left bigger-140',
				'ui-icon-seek-prev' : 'ace-icon fa fa-angle-left bigger-140',
				'ui-icon-seek-next' : 'ace-icon fa fa-angle-right bigger-140',
				'ui-icon-seek-end' : 'ace-icon fa fa-angle-double-right bigger-140',
				'ui-icon-pencil'   : 'ace-icon fa fa-edit bigger-140'
			};
			$('.ui-pg-table:not(.navtable) > tbody > tr > .ui-pg-button > .ui-icon').each(function(){
				var icon = $(this);
				var $class = $.trim(icon.attr('class').replace('ui-icon', ''));
				
				if($class in replacement) icon.attr('class', 'ui-icon '+replacement[$class]);
			});
			$('.ui-inline-edit .ui-icon-pencil').each(function(){
				var icon = $(this);
				var $class = $.trim(icon.attr('class').replace('ui-icon', ''));
				if($class in replacement)
				{
					 icon.attr('class', 'ui-icon '+replacement[$class]);
				}
			});
	},
	style_edit_form:	function (form) {
			//enable datepicker on "sdate" field and switches for "stock" field
			form.find('input[name=sdate]').datepicker({format:'yyyy-mm-dd' , autoclose:true})
				.end().find('input[name=stock]')
					.addClass('ace ace-switch ace-switch-5').after('<span class="lbl"></span>');
					   //don't wrap inside a label element, the checkbox value won't be submitted (POST'ed)
					  //.addClass('ace ace-switch ace-switch-5').wrap('<label class="inline" />').after('<span class="lbl"></span>');
	
			//update buttons classes
			var buttons = form.next().find('.EditButton .fm-button');
			buttons.addClass('btn btn-sm').find('[class*="-icon"]').hide();//ui-icon, s-icon
			buttons.eq(0).addClass('btn-primary').prepend('<i class="ace-icon fa fa-check"></i>');
			buttons.eq(1).prepend('<i class="ace-icon fa fa-times"></i>');
			
			buttons = form.next().find('.navButton a');
			buttons.find('.ui-icon').hide();
			buttons.eq(0).append('<i class="ace-icon fa fa-chevron-left"></i>');
			buttons.eq(1).append('<i class="ace-icon fa fa-chevron-right"></i>');
			
			
			
	},

	style_delete_form:function (form) {
		var buttons = form.next().find('.EditButton .fm-button');
		buttons.addClass('btn btn-sm btn-white btn-round').find('[class*="-icon"]').hide();//ui-icon, s-icon
		buttons.eq(0).addClass('btn-danger').prepend('<i class="ace-icon fa fa-trash-o"></i>');
		buttons.eq(1).addClass('btn-default').prepend('<i class="ace-icon fa fa-times"></i>');
	},
	
	style_search_filters:function (form) {
		form.find('.delete-rule').val('X');
		form.find('.add-rule').addClass('btn btn-xs btn-primary');
		form.find('.add-group').addClass('btn btn-xs btn-success');
		form.find('.delete-group').addClass('btn btn-xs btn-danger');
	},
	style_search_form:function (form) {
		var dialog = form.closest('.ui-jqdialog');
		var buttons = dialog.find('.EditTable');
		buttons.find('.EditButton a[id*="_reset"]').addClass('btn btn-sm btn-info').find('.ui-icon').attr('class', 'ace-icon fa fa-retweet');
		buttons.find('.EditButton a[id*="_query"]').addClass('btn btn-sm btn-inverse').find('.ui-icon').attr('class', 'ace-icon fa fa-comment-o');
		buttons.find('.EditButton a[id*="_search"]').addClass('btn btn-sm btn-purple').find('.ui-icon').attr('class', 'ace-icon fa fa-search');
	},
	
	beforeDeleteCallback : function (e) {
		var form = $(e[0]);
		if(form.data('styled')) return false;
		
		form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />');
		CommonDataGrid.style_delete_form(form);
		
		form.data('styled', true);
	},
	
	beforeEditCallback:function (e) {
		var form = $(e[0]);
		form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />');
		CommonDataGrid.style_edit_form(form);
	},

	memoSubGrid:{
	    subGridOptions : {
	        plusicon : "ace-icon fa fa-plus center bigger-110 blue",
	        minusicon  : "ace-icon fa fa-minus center bigger-110 blue",
	        openicon : "ace-icon fa fa-chevron-right center orange"
	    },
		subGridRowExpanded:function(grid_selector,col_model){ 
			//wrapper function to pass in grid_selector,and model parameters 
			 return function (subgridDivId, rowId) {
				

			 	var subgrid_data = [{}];
			 	
				var row=jQuery(grid_selector).jqGrid('getRowData',rowId);
				
				//get data from parent row
				var colClass='col-xs-'+(12/col_model.length);//colClass is size of widget, we set all equal size, assume no more than 6 cols
				var html='';
				for(var col in col_model){
					//subgrid_data[0][col_model[col]['name']]=row[col_model[col]['name']];
					html=html+'<div class="widget-box '+ colClass+'">'
						 +'<div class="widget-header">'+col_model[col]['label']+'</div>'
						 +'<div class="widget-body widget-main">'+row[col_model[col]['name']]+'</div>'
						 +'</div>';
				}
				var subgridTableId = subgridDivId + "_t";				
				$("#" + subgridDivId).html("<div data-row='"+rowId+"' data-grid='"+grid_selector+"' id='" + subgridTableId + "' class=''>"+html+"</div>");
				$(document).on('click', '#'+subgridTableId, function (e) {
				    var $this = $(this);
					jQuery($this.data('grid')).jqGrid('setSelection', $this.data('row'));				 
				});

				/*
				$("#" + subgridTableId).jqGrid({
					datatype: 'local',
					data: subgrid_data,
					autowidth:true,
					height:200,
					colNames: [],
					colModel: col_model
				});
				*/
			};
		},
				
	},

	//it causes some flicker when reloading or navigating grid
	//it may be possible to have some custom formatter to do this as the grid is being created to prevent this
	//or go back to default browser checkbox styles for the grid
	styleCheckbox:function (table) {
		/**
			$(table).find('input:checkbox').addClass('ace')
			.wrap('<label />')
			.after('<span class="lbl align-top" />')
	
	
			$('.ui-jqgrid-labels th[id*="_cb"]:first-child')
			.find('input.cbox[type=checkbox]').addClass('ace')
			.wrap('<label />').after('<span class="lbl align-top" />');
		*/
	},
	
	tinymce_element: function (value, options) {
              var elm = $("<textarea></textarea>");
              elm.val(value);
              // give the editor time to initialize
              setTimeout(function () {
                  //tinymce.remove();
                  //var ctr = $("#" + options.id).tinymce();
                  //if (ctr !== null) {
                  //    ctr.remove();
                  //}
                  try {
                      tinymce.remove("#" + options.id);
                  } catch(ex) {}
                  tinymce.init({selector: "#" + options.id, plugins: "link code"});
              }, 50);
              return elm;
	},
    tinymce_value: function (element, oper, gridval) {
            var id;
            if (element.length > 0) {
                id = element.attr("id");
            } else if (typeof element.selector === "string") {
                var sels = element.selector.split(" "),
                    idSel = sels[sels.length - 1];
                if (idSel.charAt(0) === "#") {
                    id = idSel.substring(1);
                } else {
                    return "";
                }
            }
            if (oper === "get") {
                return tinymce.get(id).getContent({format: "row"});
            } else if (oper === "set") {
                if (tinymce.get(id)) {
                    tinymce.get(id).setContent(gridval);
                }
            }
    },
		
	
	navButtons:function(editable){
				return { 	//navbar options
					edit: editable,
					editicon : 'ace-icon fa fa-pencil blue',
					add: editable,
					addicon : 'ace-icon fa fa-plus-circle purple',
					del: editable,//no deletions 
					delicon : 'ace-icon fa fa-trash-o red',
					search: false,
					searchicon : 'ace-icon fa fa-search orange',
					refresh: false,
					refreshicon : 'ace-icon fa fa-refresh green',
					view: false,
					viewicon : 'ace-icon fa fa-search-plus grey',
					
				};
		},
	navEdit:{
			//edit record form
			//closeAfterEdit: true,
			width: 700,
			recreateForm: true,
			closeOnEscape:true,
            savekey: [true,13],
            closeAfterEdit:true,
            reloadAfterSubmit:true,
            afterSubmit:function(xhr,data,id){
                //alert('done');
                var grid=$(this);
                grid.setGridParam({datatype:'json'});
                grid.trigger("reloadGrid");
                
                return true;
            },
			beforeShowForm : function(e) {
				//console.log('beforeedit');
				var form = $(e[0]);
				form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />');
				CommonDataGrid.style_edit_form(form);
				console.log('in beforeEdit');
			},			
		},
	navAdd:{
			//new record form
			width: 700,
			closeAfterAdd: true,
			recreateForm: true,
			viewPagerButtons: false,
			afterSubmit:function(xhr,data,id){
				//alert('done');
				var grid=$(this);
				grid.setGridParam({datatype:'json'});
				grid.trigger("reloadGrid");
				
				return true;
			},
			beforeShowForm : function(e) {
				var form = $(e[0]);
				form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar')
				.wrapInner('<div class="widget-header" />');
				CommonDataGrid.style_edit_form(form);
			}
		},
	navDel:{
			//delete record form
			recreateForm: true,
			beforeShowForm : function(e) {
				var form = $(e[0]);
				if(form.data('styled')) return false;
				
				form.closest('.ui-jqdialog').find('.ui-jqdialog-titlebar').wrapInner('<div class="widget-header" />');
				CommonDataGrid.style_delete_form(form);
				
				form.data('styled', true);
			},
			// onClick : function(e) {
				// //alert(1);
			// }
		},
	 navSearch: {
			//search form
			recreateForm: true,
			afterShowSearch: function(e){
				var form = $(e[0]);
				form.closest('.ui-jqdialog').find('.ui-jqdialog-title').wrap('<div class="widget-header" />');
				CommonDataGrid.style_search_form(form);
			},
			afterRedraw: function(){
				CommonDataGrid.style_search_filters($(this));
			}
			,
			multipleSearch: true,
			
			//multipleGroup:true,
			//showQuery: true
			
		},
	 navView: {
			//view record form
			width: 700,
			recreateForm: true,				
			beforeShowForm: function(e){
				var form = $(e[0]);
				form.closest('.ui-jqdialog').find('.ui-jqdialog-title').wrap('<div class="widget-header" />');
			}
		},
		
		
	getRowDataFromClick:function (target){
            var $tr = $(target).closest("tr.jqgrow");
            rid = $tr.attr("id");
            var row=jQuery('#requests_grid').jqGrid('getRowData',rid);
            return row;
            //document.location.href='/office-assignment/request';
   }
};

/*To force re-load after add edit
 http://stackoverflow.com/questions/17140667/jqgrid-reload-after-add-record
 */
// $.extend($.jgrid.edit, {
    // beforeSubmit: function () {
        // $(this).jqGrid("setGridParam", {datatype: "json"});
        // return [true,"",""];
    // }
// });


