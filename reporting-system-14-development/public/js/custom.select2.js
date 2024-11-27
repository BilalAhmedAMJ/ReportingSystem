
var CustomSelect2 = {
	
	htmlEncode:function(value){
		return $("<div/>").html(value).text();		
	},
	
	loadData:function(config,cacheStore,cacheKey,term,callback){
		if(!cacheStore.cacheDataSource){
			cacheStore.cacheDataSource=[];
		}
		$.ajax({
              url: config.url,
              data: { q : term },
              dataType: 'json',
              type: 'GET',
              success: function(response,status,xhr) {
          		if(response.error && response.error.length>1){
          			console.log('Error while fetching '+config.url+' \n'+response.error);
          			return;
          		}
              	var data=[];
              	data['result']=[];
              	if(config.data_target){ //use provided target to get data from response 
	              	$.each( response[config.data_target],function(i,it){
	              							              		
	              		data['result'].push({'id':it[config.id], 'text':CustomSelect2.htmlEncode(it[config.label]) } );
	              	});					              		
              	}else{ //use response data as such for target list
              		data['result']=response;
              	}
                cacheStore.cacheDataSource[cacheKey] = data;
                if($.type(callback) === "function")
                	callback(data);
              },
              error:function(xhr,status,error){
              	console.log('ERROR : '+error+' Status'+status);
              	//console.log(xhr);
              }
        });		
	},
	matcher:function(preload_data,term){
		var data={results:[]};
		$.each(preload_data, function(){	
          if(term.length == 0 || this.text.toUpperCase().indexOf(term.toUpperCase()) >= 0 ){
              data.results.push({id: this.id, text: this.text });
          }
        });			
        return data;
	},
	createSimple:function(config){
		if( //check valid user input was provided otherwise throw exception
			(config == undefined) || (config.element == undefined) || ($(config.element).length<=0) 
		  ||(config.url == undefined) || (config.id == undefined) || (config.label==undefined) 
		  ){
		  	// console.log('ERROR in config provided. Please make sure all required elements are provided');
			// console.log('(config == undefined) '+(config == undefined));
			// console.log('config.element == undefined '+(config.element == undefined));
			// console.log('$(config.element).length<=0 '+($(config.element).length<=0 ));
			// console.log('config.url == undefined '+(config.url == undefined));
			// console.log('config.id == undefined '+(config.id == undefined));
			// console.log('config.label==undefined '+(config.label==undefined)); 
			console.log($(config.element));
		  }
		  
		  
		var select2= $(config.element).select2({
						orignalConfig:config,
						
					    placeholder: config.placeholder,
					    allowClear:true,
					    openOnEnter:true,
					    selectOnBlur:true,
					    matcher: function(term, text, opt) {
							//console.log(text.toUpperCase().indexOf(term.toUpperCase()));
					    	return text.toUpperCase().indexOf(term.toUpperCase())>=0;
					    },
					    query: function(query) {
					        self = window;
					        //TODO at some point we may want to add search term to the cache key for now we are using URL as key  
					        var key = config.url+'_data';
					        var cachedData = self.cacheDataSource[key];
							
					        if(cachedData) {

					            query.callback(CustomSelect2.matcher(cachedData.result,query.term));
					            return;
					        } else {					        	
					            CustomSelect2.loadData(
					            	config,self,key,query.term,
					            	function(d){
				            			query.callback(CustomSelect2.matcher(d.result,query.term));
					            	});
					        }
					        
					    },
					    
					    initSelection:function(element, initCallback){
					    	var self = window;
					    	var key = config.url+' data';
					    	var val=$(element).val();
					    	var initValKey=config.initValLabel?'text':'id';
					    		CustomSelect2.loadData(config,self,key,val,
					            	function(d){
					            		var selected=[];
				            			$.each(d.result,function(key,row){
				            				//console.log(val);
				            				if(config.multiple){
					            				$(val.split(",")).each(function () {
						            				if(row[initValKey]==this){					            					
										            	selected.push({id: this, text: this});
													}
        										});		            				
				            					
				            				}else{
					            				if(row[initValKey]==val){				            									            					
					            					initCallback(row);
					            				}
				            				}
				            			});
			            				if(config.multiple){
				            				initCallback(selected);			            					
			            				}else {
			            					initCallback(selected[0]);
			            				}
					            	} );
					        
					    },
					    multiple:config.multiple?config.multiple:false,
					    //width: '150px'
					    width:config.width?config.width:'90%',
					    
					    createSearchChoice:config.createSearchChoice?config.createSearchChoice:null
					    
					    //containerCss : {"display":"block"}
					    //dropdownAutoWidth : true
					    //formatResult: function(m){ return m;}, 
					    //formatSelection: function(m){return m;}, 
					    //dropdownCssClass: "bigdrop", 
					    //escapeMarkup: function (m) { return m; } 
				}
				);
				
			$(config.element).on("select2-opening",function(){ 
															  $('.validation-error-label').each(function(){
															  	$(this).closest('.has-error').removeClass('has-error');
															  	$(this).remove();
															  });
															  
															  
															  
															 });
			//preload data
            CustomSelect2.loadData(
            	config,window,config.url+'_data','',
            	function(d){
        			//emptyfunction just so we can pre-load data
        	});

											 
			return select2;
	}
};
