( function( $ ) {

    $( document ).ready( function() {

        // This is named Subject, but it is actually the Ticket Type Radio Field
        $( document ).on( 'change', '.edd-gf-support-tickets-form-subject', function( event ) {

            let match = $( event.target ).attr( 'id' ).match( /\d+$/ ),
                choiceIndex = ( typeof match[0] !== 'undefined' ) ? match[0] : -1;

            if ( choiceIndex == 2 ) { // Other
                $( '.edd-gf-support-tickets-form-which-extension .gfield_required' ).hide();
            }
            else {
                $( '.edd-gf-support-tickets-form-which-extension .gfield_required' ).show();
            }

        } );

    } );

} )( jQuery );