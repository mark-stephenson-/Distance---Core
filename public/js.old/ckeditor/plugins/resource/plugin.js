/**
 * Basic sample plugin inserting abbreviation elements into CKEditor editing area.
 *
 * Created out of the CKEditor Plugin SDK:
 * http://docs.ckeditor.com/#!/guide/plugin_sdk_sample_1
 */

// Register the plugin within the editor.
CKEDITOR.plugins.add( 'resource', {

	// Register the icons.
	icons: 'resource',

	// The plugin initialization logic goes inside this method.
	init: function( editor ) {

		// Define an editor command that opens our dialog.
        // editor.addCommand( 'abbr', new CKEDITOR.dialogCommand( 'abbrDialog' ) );
		editor.addCommand( 'resource', {
            exec: function( e ) {
                // We're assuming the fancybox exists
                $('#' + e.name + '_resource_link').eq(0).trigger('click');
            }
        });

		// Create a toolbar button that executes the above command.
		editor.ui.addButton( 'Resource', {

			// The text part of the button (if available) and tooptip.
			label: 'Insert Resource',

			// The command to execute on click.
			command: 'resource',

			// The button placement in the toolbar (toolbar group name).
			toolbar: 'insert'
		});
	}
});

