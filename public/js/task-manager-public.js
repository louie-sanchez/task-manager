(function( $ ) {
	'use strict';
    $(document).on('click', '#submitTicket', function (e) {
    	var subject = $('#ticketSubject').val();
    	var type = $('#ticketTypeId').val();
    	var details = $('#ticketDetails').val();
    	var data, user_id;


    	if( subject && type && details && typeof ajax_object.user_id !== 'undefined' ){
			user_id = parseInt(ajax_object.user_id);
			data = {
				'action': 'add_new_ticket',
				'ticket_data': {name: subject, description: details, ticket_type_id: parseInt(type), ticket_status_id: 2,
					ticket_for: user_id, 'created_by': user_id, 'last_update_by': user_id}
			};

			jQuery.post(ajax_object.ajax_url, data, function(response) {
				console.log('The Ticket has been inserted, the new ticket id', response);
			});
		}


    });



})( jQuery );
