/**
 * Copyright (c) 2014-2016, CKSource - Frederico Knabben. All rights reserved.
 * Licensed under the terms of the MIT License (see LICENSE.md).
 *
 * The abbr plugin dialog window definition.
 *
 * Created out of the CKEditor Plugin SDK:
 * http://docs.ckeditor.com/#!/guide/plugin_sdk_sample_1
 */

// Our dialog definition.
CKEDITOR.dialog.add( 'wikipageDialog', function( editor ) {
	return {

		// Basic properties of the dialog window: title, minimum size.
		title: 'New Wiki Page',
		minWidth: 400,
		minHeight: 200,

		// Dialog window content definition.
		contents: [
			{
				// Definition of the Basic Settings dialog tab (page).
				id: 'tab-basic',
				label: 'Settings',

				// The tab content.
				elements: [
					{
						// Text input field for the abbreviation text.
						type: 'text',
						id: 'title',
						label: 'Title',

						// Validation checking whether the field is not empty.
						validate: CKEDITOR.dialog.validate.notEmpty( "Title field cannot be empty." )
					}
				]
			}
		],

		// This method is invoked once a user clicks the OK button, confirming the dialog.
		onOk: function() {

			// The context of this function is the dialog object itself.
			// http://docs.ckeditor.com/#!/api/CKEDITOR.dialog
			var dialog = this;
			var wikiEditForm=document.querySelector('#edit .form');
			alert(wikiEditForm);
			if (wikiEditForm)  {
				alert(wikiEditForm);
				var wikiUrl=wikiEditForm.getAttribute('action');
				//console.log([wikiEditForm,wikiUrl]);
				alert(wikiUrl);
				if (wikiUrl.length>0) {
					// Create a new <abbr> element.
					var link = editor.document.createElement( 'a' );

					// Set element attribute and text by getting the defined field values.
					alert(window.href);
					link.setAttribute( 'href', dialog.getValueOf( 'tab-basic', 'title' ) );
					link.setText( dialog.getValueOf( 'tab-basic', 'title' ) );

					
					// Finally, insert the element into the editor at the caret position.
					editor.insertElement( link );
				}
			}
		}
	};
});
