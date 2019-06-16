/**
 * @file This file contains Seo Writing Assistant SEMrush Custom Fields JavaScript class
 * @author Enrico Sorcinelli 
 * @version 1.1.0
 * @title SEO Writing Assistant SEMrush Custom Fields
 */

/**
 * Allow to pass to SEMrush SWA arbirtrary html in addition to post content.
 *
 * @param {Object}  args - The arguments has following properties.
 * @param {boolean} [args.debug=false] - Debug mode. If `true`, it will print on `console.log` (if defined) some debug useful informations.
 * @param {number}  [args.interval=5] - Sets interval, in seconds, for event trigger. Set to `0` to turn off.
 *
 * @fires `seo-writing-assistant-semrush`
 *
 * @constructor
 */
SeoWritingAssistantSEMrushCustomFields = function( args ) {

	// Default arguments.
	args = $.extend(true, {
			debug: false,
			interval: 5
		},
		args
	);

	this.params = {
		debug: args.debug,
		interval: args.interval
	};

	// Check for SEMrush plugin.
	if ( $( '#swa-meta-box' ).length < 1 ) {
		return;
	}

	// Create textarea element if necessary.
	this.$cmsms_content_composer_text = $( '#cmsms_content_composer_text' );
	if ( this.$cmsms_content_composer_text.length < 1 ) {
		this.$cmsms_content_composer_text = $( '<textarea id="cmsms_content_composer_text" style="display: none;"></textarea>' );
		$( 'body' ).append( this.$cmsms_content_composer_text );
	}

	// Create interval.
	if ( this.params.interval > 0 ) {
		this.interval = setInterval( this.trigger.bind( this ), this.params.interval * 1000 );
	}

	// Add listener for elements with `swa-scf` class or `data-swa-scf` attribute.
	$( document ).on( 'seo-writing-assistant-semrush', function( event, data ) {

		$( '.swa-scf, [data-swa-scf]' ).each( function( i, e ) {

			if ( 'select' === $( e ).prop( 'tagName' ).toLowerCase() ) {
				data.html += "\n" + $( e ).val();
				return;
			}
			switch ( $( e ).prop('type') ) {
				case 'text' :
				case 'textarea' :
					data.html += "\n" + $( e ).val();
					break;
				case 'radio' :
				case 'checkbox' :
					if ( $( e ).is( ':checked' ) ) {
						data.html += "\n" + $( e ).val();
					}
					break;
				default:
					break;
			}
		} );

	} );

};

/**
 * Class methods.
 */
SeoWritingAssistantSEMrushCustomFields.prototype = {

	/**
	 * Trigger `seo-writing-assistant-semrush` event.
	 */
	'trigger': function() {
		var data = { html: '' };
		$( window.document ).trigger( 'seo-writing-assistant-semrush', [ data ] );
		this.setHtml( data.html );
	},

	/**
	 * Allow to add custom HTML.
	 *
	 * @param {String} html - Arbitrary HTML added for analisys.
	 */
	'setHtml': function( html ) {
		this.$cmsms_content_composer_text.html( html );
	}
};

// Auto-enable object.
$( document ).ready( function() {
	if ( swa_semrush_custom_fields_i18n.enable ) {
		swa_semrush_custom_fields = new SeoWritingAssistantSEMrushCustomFields( { interval: swa_semrush_custom_fields_i18n.interval } );
	}
} );