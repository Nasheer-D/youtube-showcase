/**
 * Update date picker element
 * Used for static & dynamic added elements (when clone)
 */
jQuery( document ).ready( function( $ )
{
	$( ':input.rwmb-date' ).each( rwmb_update_date_picker );
	$( '.rwmb-input' ).on( 'clone', ':input.rwmb-date', rwmb_update_date_picker );
	
	function rwmb_update_date_picker()
	{
		var $this = $( this ),
			options = $this.data( 'options' );
	
		$this.siblings( '.ui-datepicker-append' ).remove();         // Remove appended text
		$.datepicker.regional[vars.locale] = vars.date;
        	$.datepicker.setDefaults($.datepicker.regional[vars.locale]);
		$this.removeClass( 'hasDatepicker' ).attr( 'id', '' ).datepicker( options );
	}
} );
