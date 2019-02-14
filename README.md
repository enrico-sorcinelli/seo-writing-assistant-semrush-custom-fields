# SEO Writing Assistant SEMrush Custom Fields

The [SEMrush SEO Writing Assistant](https://wordpress.org/plugins/semrush-seo-writing-assistant/) plugin read only from post `title` and post `content` elements for the real time check.

This plugin allows you to use an arbitrary value for that check in addition to those values.

So, if you are using some kind of a page builders, field managers and so on, you have only to add a little bit of JavaScript code in order to specify which fields.

Note that the plugin don't replace _SEMrush SEO Writing Assistant_ but is intended to be used together.

# Installation  

This section describes how to install the plugin and get it working.

1. Upload the plugin files to the `/wp-content/plugins/seo-writing-assistant-semrush-custom-fields` directory, or install the plugin through the WordPress _Plugins_ screen directly.
1. Activate the plugin through the _Plugins_ screen in WordPress.

# Usage

Once the plugin is installed you can configure it programmatically,
by using `semrush_seo_writing_assistant_post_types` (optional) filter and `SeoWritingAssistantSEMrushCustomFields` JavaScript object (see below).

# API

## WordPress Hooks

### `semrush_seo_writing_assistant_post_types`

Filters post types where to enable plugin. Default to `array( 'post', 'page', 'product' )`.

```php
apply_filters( 'semrush_seo_writing_assistant_post_types', array $post_types )
```

## JavaScript Event

### `seo-writing-assistant-semrush`

The following example will update text for SEMrush check every 5 seconds, using `excerpt` and `my_custom_fields` custom field values (working both with block and classic editors):

```javascript
jQuery( document ).ready( function() {
	var sr = new SeoWritingAssistantSEMrushCustomFields( { interval: 5 } );
	jQuery( document ).on( 'seo-writing-assistant-semrush', function( event, data ) {
		data.html = jQuery( '#excerpt' ).val() + "\n"
			+ jQuery( '.editor-post-excerpt__textarea textarea').val() + "\n"
			+ jQuery( '#my_custom_fields' ).val();
	} )
} );
```

# Frequently Asked Questions

## Does it work with Gutenberg?

Yes.

# License: GPLv2

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.