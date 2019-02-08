/**
 * @file This file contains SEMrush Seo Writing Assistant Custom Fields JavaScript class
 * @author Enrico Sorcinelli 
 * @version 1.0.0
 * @title SEMrush Seo Writing Assistant Custom Fields
 */

/**
 * Allow to pass to SEMrush SWA arbirtrary html in addition to post content.
 *
 * @param {Object}  args - The arguments has following properties.
 * @param {boolean} [args.debug=false] - Debug mode. If `true`, it will print on `console.log` (if defined) some debug useful informations.
 * @param {number}  [args.interval=5] - Sets interval, in seconds, for event trigger. Set to `0` to turn off.
 *
 * @fires `semrush-seo-writing-assistant`
 *
 * @constructor
 */
SEMrushSeoWritingAssistantCustomFields = function( args ) {

	// Default arguments.
	args = $.extend(true, {
			debug: false,
			interval: 5
		},
		args
	);

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
	if ( args.interval > 0 ) {
		this.interval = setInterval( this.trigger.bind( this ), args.interval * 1000 );
	}
};

/**
 * Class methods.
 */
SEMrushSeoWritingAssistantCustomFields.prototype = {

	/**
	 * Trigger `semrush-seo-writing-assistant` event.
	 */
	'trigger': function() {
		var data = {};
		$( window.document ).trigger( 'semrush-seo-writing-assistant', [ data ] );
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
