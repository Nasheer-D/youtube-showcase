jQuery( document ).ready( function( $ )
{
	var $form = $( '#post' );

	// Required field styling
	$.each( emd_mb.validationOptions.rules, function( k, v )
	{
		if ( v['required'] )
		{
			//$( '#' + k ).parent().siblings( '.mb-label' ).addClass( 'required' ).append( '<span>*</span>' );
			$('input[name='+k+'],select[name='+k+'],textarea[name='+k+']').parents().find('label[for='+k+']').parent().addClass( 'required' ).append( '<span>*</span>' );
		}
	} );

	emd_mb.validationOptions.invalidHandler = function( form, validator )
	{
		// Re-enable the submit ( publish/update ) button and hide the ajax indicator
		$( '#publish' ).removeClass( 'button-primary-disabled' );
		$( '#ajax-loading' ).attr( 'style', '' );
		$form.siblings( '#message' ).remove();
		$form.before( '<div id="message" class="error"><p>' + emd_mb.summaryMessage + '</p></div>' );
	};
	
	//added for validation to work on select2 (required and selects)
	emd_mb.validationOptions.ignore= null;
	$.extend($.validator.messages,validate_msg);

	$form.validate(emd_mb.validationOptions );


	//functions for conditional checks , not called if no conditional set
	$.fn.checkCond = function (k,v){
		if(v.type == 'radio'){
			check_val = $('input[type='+ v.type + '][name='+k+']').filter(':checked').val();
		}
		else if(v.type == 'checkbox'){
			check_val = $('#'+k).is(":checked");
		}
		else {
			check_val = $('#'+k).val();
		}
		$.each(v.rules,function(attr,j){
			switch(j.depend_check) {
				case 'is':
					if(check_val ===  j.depend_value){
						$.fn.changeInput(attr,j.view,j.valid,0);
					}
					else {
						$.fn.changeInput(attr,j.view,j.valid,1);
					}	
					break;
				case 'greater':
					if(check_val >  j.depend_value){
						$.fn.changeInput(attr,j.view,j.valid,0);
					}
					else {
						$.fn.changeInput(attr,j.view,j.valid,1);
					}
					break;
				case 'less':
					if(check_val <  j.depend_value){
						$.fn.changeInput(attr,j.view,j.valid,0);
					}
					else {
						$.fn.changeInput(attr,j.view,j.valid,1);
					}
					break;
				case 'contains':
					if(check_val.indexOf(j.depend_value) !== -1){
						$.fn.changeInput(attr,j.view,j.valid,0);
					}
					else {
						$.fn.changeInput(attr,j.view,j.valid,1);
					}
					break;
				case 'starts':
					if(check_val.match("^"+j.depend_value)){
						$.fn.changeInput(attr,j.view,j.valid,0);
					}
					else {
						$.fn.changeInput(attr,j.view,j.valid,1);
					}
					break;
				case 'ends':
					if(check_val.match(j.depend_value+"$")){
						$.fn.changeInput(attr,j.view,j.valid,0);
					}
					else {
						$.fn.changeInput(attr,j.view,j.valid,1);
					}
					break;
			}
		});
	}
	$.fn.changeInput = function (attr,view,valid,rev){
		if(rev == 1 && view == 'hide'){
			view = 'show';
		}
		else if(rev == 1 && view == 'show'){
			view = 'hide';
		}
		if(view == 'hide'){
			$('#'+attr).closest('.emd-mb-input').hide();
			$('#'+attr).val('');
			$('label[for='+attr+']').closest('.emd-mb-label').removeClass('required').hide();
			$('#'+attr).rules("remove");
		}
		else if(view == 'show'){
			$('#'+attr).closest('.emd-mb-input').show();
			$('label[for='+attr+']').closest('.emd-mb-label').show();
			if(valid.required == true){
				$('label[for='+attr+']').closest('.emd-mb-label').addClass('required');
			}
			$('#'+attr).rules("add",valid);
		}
	}
	
	
	//emd_mb conditional options 
	//first check if there is any conditional set for any of the attributes for this entity
	//if there is loop and process them
	if($(emd_mb.conditional).length != 0){
		$.each(emd_mb.conditional, function (k, v) {
			show_start_hide = 0;
			if(v.type == 'radio'){
				change_on = 'input[type='+ v.type + '][name='+k+']';
				check_val = $(change_on).filter(':checked').val();
				if(typeof check_val === "undefined"){
					show_start_hide = 1;
				}
			}
 			else if(v.type == 'checkbox'){
				change_on = '#'+k;
				check_val = $(change_on).is(":checked");
				if(!check_val){
					show_start_hide = 1;
				}
			}
			else {
				change_on = '#'+k;
				check_val = $(change_on).val();
				if(typeof check_val === 'undefined'){
					show_start_hide = 1;
				}
			}
			if(v.start_hide.length != 0 ){
				$.each(v.start_hide,function(a,b){
						v.rules[b]['valid'] = emd_mb.validationOptions.rules[b];
						emd_mb.validationOptions.rules[b] = {};
						$('#'+b).closest('.emd-mb-input').hide();
						$('label[for='+b+']').closest('.emd-mb-label').removeClass('required').hide();
				});
			}
			if(show_start_hide != 1){
				$.fn.checkCond(k,v);
			}
				
				
			$(change_on).change(function(){
				$.fn.checkCond(k,v);		
			});
		});
	}
} );
