jQuery(document).ready(function($){
	$.validator.addMethod('uniqueAttr',function(val,element){
		var data_input = {};
		$.each(unique_vars.keys,function(i,val){
			data_input[val] = $('#'+val).val();
		});
		data_input['post_ID'] = $('#post_ID').val();
		var unique = true;
		$.ajax({
			type: 'GET',
			url: ajaxurl,
			cache: false,
			async: false,
			data: {action:'emd_check_unique',data_input:data_input, ptype:pagenow,myapp:unique_vars.app_name},
			success: function(response){
				unique = response;
		    	},
		});
		return unique;
	}, unique_vars.msg);
});
