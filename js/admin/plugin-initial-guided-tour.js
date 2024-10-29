( function() {
    
    window.ASS = window.ASS || {
        Admin: {}
    };

} () );

( function( $ ) {

    function Tour() {

        if ( !ass_initial_guided_tour_params.screen.elem )
            return;

        this.initPointer();

    }

    Tour.prototype.initPointer = function() {

        var self = this;

        self.$elem = $( ass_initial_guided_tour_params.screen.elem ).pointer( {
            content: ass_initial_guided_tour_params.screen.html,
            position: {
                align: ass_initial_guided_tour_params.screen.align,
                edge: ass_initial_guided_tour_params.screen.edge,
            },
            buttons: function( event , t ) {
                return self.createButtons(t);
            },
        } ).pointer( 'open' );

    };

    Tour.prototype.createButtons = function( t ) {

        this.$buttons = $( '<div></div>', {
            'class': 'ass-tour-buttons'
        } );

        this.createCloseButton( t );
        this.createPrevButton( t );
        this.createNextButton( t );

        return this.$buttons;

    };

    Tour.prototype.createCloseButton = function(t) {

        var $btnClose = $( '<button></button>' , {
            'class': 'button button-large',
            'type': 'button'
        } ).html( ass_initial_guided_tour_params.texts.btn_close_tour );

        $btnClose.click(function() {
            
            var data = {
                action : ass_initial_guided_tour_params.actions.close_tour,
                nonce  : ass_initial_guided_tour_params.nonces.close_tour,
            };

            $.post( ass_initial_guided_tour_params.urls.ajax , data , function( response ) {

                if ( response.success )
                    t.element.pointer( 'close' );
                
            } );

        });

        this.$buttons.append($btnClose);

    };

    Tour.prototype.createPrevButton = function( t ) {

        if ( !ass_initial_guided_tour_params.screen.prev )
            return;

        var $btnPrev = $( '<button></button>' , {
            'class': 'button button-large',
            'type': 'button'
        } ).html( ass_initial_guided_tour_params.texts.btn_prev_tour );

        $btnPrev.click( function() {
            window.location.href = ass_initial_guided_tour_params.screen.prev;
        } );

        this.$buttons.append($btnPrev);

    };

    Tour.prototype.createNextButton = function( t ) {

        if (!ass_initial_guided_tour_params.screen.next)
            return;

        // Check if this is the first screen of the tour.
        var text = ( !ass_initial_guided_tour_params.screen.prev ) ? ass_initial_guided_tour_params.texts.btn_start_tour : ass_initial_guided_tour_params.texts.btn_next_tour;

        var $btnStart = $( '<button></button>' , {
            'class' : 'button button-large button-primary',
            'type'  : 'button'
        } ).html( text );

        $btnStart.click( function() {
            window.location.href = ass_initial_guided_tour_params.screen.next;
        } );

        this.$buttons.append( $btnStart );
    };

    ASS.Admin.Tour = Tour;

}( jQuery ) );

( function( $ ) {
    
    // DOM ready
    $( function() {

        new ASS.Admin.Tour();

        $( "#ass-add-first-survey" ).on( 'click' , function( e ) {

            e.preventDefault();
            var $this = $( this ),
                href = this.href,
                data = {
                            action : ass_initial_guided_tour_params.actions.close_tour,
                            nonce  : ass_initial_guided_tour_params.nonces.close_tour,
                        };
            
            $this.attr( 'disabled' , 'disabled' );

            $.post( ass_initial_guided_tour_params.urls.ajax , data , function( response ) {

                if ( response.success ) {

                    window.location = href;
                    t.element.pointer( 'close' );

                } else {

                    console.log( response );
                    $this.removeAttr( 'disabled' );

                }
                
            } );

            return false;

        } );

    } );

}( jQuery ) );
