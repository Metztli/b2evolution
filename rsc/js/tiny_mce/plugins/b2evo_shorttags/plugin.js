tinymce.PluginManager.add( 'b2evo_shorttags', function( editor ) {

	var win;
	var renderedTags = [];
	var selected;

	var self = this;
	self.selected = function() {
		return selected;
	};

	/**
	 * Add [image:] button
	 */
	editor.addButton( 'b2evo_image', {
		text: '[image:]',
		icon: false,
		tooltip: 'Insert Image',
		onclick: function() {
			if( editor.getParam( 'postID' ) ) 	{
				var attachments = editor.getParam( 'attachments' );
				var inlineImages = [];
				for( var i = 0; i < attachments.length; i++ )
				{
					if( attachments[i].type == 'image' && attachments[i].position == 'inline' ) {
						inlineImages.push( attachments[i] );
					}
				}
				if( ! jQuery.isEmptyObject( inlineImages ) ) {
					showDialog( inlineImages );
				}
				else
				{
					alert( 'You must have at least one attached image in the Inline position.' );
				}
			} else {
				alert( 'You must save the post (at least as a draft) before you can attach images.' );
			}
		},
		onPostRender: function()
		{
			var imageButton = this;
			editor.on( 'NodeChange', function( event ) {
				if( selected )
				{
					var data = getRenderedNodeData( selected );
					imageButton.active( data['type'] == 'image' );
				}
				else
				{
					imageButton.active( false );
				}
			});
		}
	});


	/**
	 *  Build the list of attachments
	 */
	function buildListItems( inputList, itemCallback, filetype) {
		function appendItems( values, output ) {
			output = output || [];

			tinymce.each( values, function( item ) {
				if( filetype && filetype != item.type )
				{
					// Do nothing
				}
				else
				{
					menuItem = { text: item.title || item.name, value: item.link_ID };
					output.push( menuItem );
				}
			} );

			return output;
		}

		return appendItems( inputList, [] );
	}


	/**
	 * Show dialog for inline images
	 *
	 * @param array
	 */
	function showDialog( attachmentList )
	{
		var selectedData = null;
		if( selected ) {
			selectedData = getRenderedNodeData( selected );
		}

		if( attachmentList ) {
			imageListCtrl = {
				type: 'listbox',
				name: 'link',
				label: 'Attached images',
				values: buildListItems(
						attachmentList,
						function( item ) {
							item.value = editor.convertURL( item.value || item.url, 'src' );
						},
						'image'	),
				value: selectedData ? selectedData.linkId : null
			};
		}

		win = editor.windowManager.open({
			title: 'Insert/Edit Image',
			body: [
				imageListCtrl,
				{
					type: 'container',
					label: 'Caption:',
					layout: 'flex',
					direction: 'row',
					align: 'center',
					spacing: 5,
					items: [
						{
							type: 'textbox',
							name: 'caption',
							value: ( selectedData && selectedData.caption != '-' ) ? selectedData.caption : null
						},
						{
							type: 'checkbox',
							name: 'nocaption',
							text: 'Do not show caption',
							checked: ( selectedData && selectedData.caption == '-' ),
							onclick: function() {
								var captionCtrl = win.find( '#caption' )[0];
								captionCtrl.disabled( this.checked() );
							}
						}
					]
				},
				{
					type: 'listbox',
					name: 'alignment',
					label: 'Alignment:',
					values: [
						{ text: 'none', value: '' },
						{ text: 'left', value: '.floatleft' },
						{ text: 'right', value: '.floatright' }
					],
					value: selectedData ? selectedData.alignment : ''
				},
				{
					type: 'textbox',
					name: 'imageClass',
					label: 'Additional class:',
					value: selectedData ? selectedData.extraClass : null
				}
			],
			buttons: [
				{
					text: 'Cancel',
					onclick: 'close'
				},
				{
					text: selected ? 'Update' : 'Insert',
					onclick: function() {
						var linkCtrl = win.find('#link')[0];
						var captionCtrl = win.find('#caption')[0];
						var noCaptionCtrl = win.find('#nocaption')[0];
						var alignCtrl = win.find('#alignment')[0];
						var classCtrl = win.find('#imageClass')[0];
						var tag = '[image:' + linkCtrl.value();

						if( noCaptionCtrl.checked() || captionCtrl.value() ||  alignCtrl.value() )
						{
							tag += ':';

							if( noCaptionCtrl.checked() )	{
								tag += '-';
							} else if( captionCtrl.value() ) {
								tag += captionCtrl.value();
							}

							if( alignCtrl.value() || classCtrl.value() )
							{
								var alignClass = alignCtrl.value();
								tag += ':' + alignClass + classCtrl.value();
							}
						}

						tag += ']';

						// Get rendered tag and output directly
						var renderedTag = getRenderedTag( tag );

						if( renderedTag === false )
						{
							getRenderedTags( [ tag ], function( rTags ) {
									for( var i = 0; i < renderedTags.length; i++ )
									{
										if( renderedTags[i].shortTag == tag )
										{
											renderedTag = renderedTags[i];
											break;
										}
									}

									if( renderedTag )
									{
										if( selected )
										{
											editor.dom.replace( renderedTag.node, selected, false );
										}
										else
										{
											editor.insertContent( renderedTag.html );
										}

										editor.windowManager.close();
									}
								} );
						}
						else
						{
							if( selected )
							{
								editor.dom.replace( renderedTag.node, selected, false );
							}
							else
							{
								editor.insertContent( renderedTag.html );
							}

							editor.windowManager.close();
						}
					}
				}
			]
		})
	}

	function _stop( event ) {
		event.stopPropagation();
		return false;
	}

	function select( node )
	{
		var dom = editor.dom;

		if ( node !== selected ) {
			// Make sure that the editor is focused.
			// It is possible that the editor is not focused when the mouse event fires
			// without focus, the selection will not work properly.
			editor.getBody().focus();

			deselect();
			selected = node;

			// Do not allow cut and paste operation within the selected node
			jQuery( selected ).bind( 'paste cut', _stop );

			// Necessary to prevent manipulating the selection/focus
			dom.bind( selected, 'beforedeactivate focusin focusout', _stop );
		}
	}

	function deselect()
	{
		jQuery( selected ).off( 'paste cut beforedeactive focusin focusout' );
		selected = null;
	}

	/**
	 * Fetches rendering data including  HTML fragments of submitted inline tags
	 *
	 * @param array List of inline tags to render
	 * @param function Callback function after fetching the HTML fragments
	 */
	function getRenderedTags( inlineTags, callback  ) {
		var tagsParam = [];
		for( var i = 0; i < inlineTags.length; i++ ) {
			var renderedTag = getRenderedTag( inlineTags[i] );
			if( renderedTag === false ) {
				renderedTags.push({
					shortTag: inlineTags[i],
					html: null,
					node: null,
					type: null,
					rendered: false
				});
			} else {
				continue;
			}
			tagsParam.push( 'tags[]=' + encodeURI( inlineTags[i] ) );
		}

		if( tagsParam.length )
		{
			tagsParam = tagsParam.join( '&' );

			tinymce.util.XHR.send({
				url: editor.getParam( 'async_url' ) + '?action=render_inlines&p=' + editor.getParam( 'postID' ),
				content_type : 'application/x-www-form-urlencoded',
				data: tagsParam,
				success: function( data ) {
					var returnedTags = tinymce.util.JSON.parse( data );

					for( tag in returnedTags ) {
						var wrapper = editor.dom.create( 'div' );
						var df = editor.dom.createFragment( returnedTags[tag] );
						var tagData = parseTag( tag );

						editor.dom.setAttrib( df.childNodes[0], 'data-b2evo-tag', window.encodeURIComponent( tag ) );
						editor.dom.setAttrib( df.childNodes[0], 'data-b2evo-type', tagData.type );
						editor.dom.addClass( df.childNodes[0], 'b2evo-tag' );
						wrapper.appendChild( df );

						var renderedTag = getRenderedTag( tag );
						if( renderedTag === false )
						{
							renderedTags.push({
								shortTag: tag,
								html: wrapper.innerHTML,
								node: wrapper.childNodes[0],
								type: tagdata.type,
								rendered: true
							});
						}
						else if( renderedTag.rendered === false )
						{
							renderedTag.html = wrapper.innerHTML;
							renderedTag.node = wrapper.childNodes[0];
							renderedTag.rendered = true;
						}
					}
					callback( returnedTags );
				}
			});
		}
		else
		{
			callback();
		}
	}


	/**
	 * Gets the relevant rendering of an inline tag
	 *
	 * @param string Inline tag
	 * @return mixed Array of rendering data, False otherwise
	 */
	function getRenderedTag( tag ) {
		var n = renderedTags.length;
		for( var i = 0; i < n; i++ )
		{
			if( renderedTags[i].shortTag == tag ) {
				return renderedTags[i];
			}
		}

		return false;
	}


	/**
	 * Render the inline tags
	 *
	 * @param string Content of the post
	 */
	function renderInlineTags( content )
	{
		var re = /(<span.*?data-b2evo-tag.*?>)?(\[(image|video|audio):(\d+):?([^\[\]]*)\])(<\/span>)?/ig;
		var m;
		var matches = [];
		var inlineTags = [];

		while ( ( m = re.exec( content ) ) !== null ) {
			if ( m.index === re.lastIndex ) {
					re.lastIndex++;
			}
			matches.push({
				shortTag: m[2],
				inlineType: m[3],
				linkId: parseInt( m[4] ),
				other: m[5],
				openTag: m[1],
				closeTag: m[6]
			});
			inlineTags.push( m[2] );
		}

		getRenderedTags( inlineTags, function( returnedTags ) {
			if( returnedTags )
			{
				update();
			}
		});

		var n = matches.length;
		for( var i = 0; i < n; i++ )
		{
			if( matches[i] && !matches[i].openTag && !matches[i].closeTag )
			{
				var tag = matches[i].shortTag;

				var renderedTag = getRenderedTag( tag );
				if( renderedTag !== false && renderedTag.rendered !== false )
				{
					switch( matches[i].inlineType ) {
						case 'image':
							content = content.replace( tag, renderedTag.html );
							break;

						default:
							content = content.replace( tag, '<span style="color: green;" data-b2evo-tag>' + tag + '</span>' );
					}
				}
			}
		}

		return content;
	}


	/**
	 * Restore rendering of inline tags to the original inline tag string
	 *
	 * @param string Content to cleanup
	 * @returen string Cleaned up content
	 */
	function restoreShortTags( content ) {
		// Cleanup errors
		content = content.replace( /(<span [^>]+data-b2evo-error[^>]+>(.*?)<\/span>)/ig,
			function( match, c, i )	{
				return i;
			});

		// Cleanup other shorttags
		var re = /(<span.*?data-b2evo-tag.*?>)?(\[(image|file|inline|video|audio|thumbnail):(\d+):?([^\[\]]*)\])(<\/span>)?/ig;
		while ( ( m = re.exec( content ) ) !== null ) {
			if ( m.index === re.lastIndex ) {
					re.lastIndex++;
			}
			if( m[1] && m[6] ) {
				content = content.replace( m[0], m[2] );
			}
		}

		// Cleanup [image:]
		var df = editor.dom.createFragment( content );
		var renderedNode;
		while( renderedNode = df.querySelector( '[data-b2evo-tag]' ) ) {
			var tag = window.decodeURIComponent( renderedNode.getAttributeNode( 'data-b2evo-tag' ).value );
			renderedNode.parentNode.replaceChild( document.createTextNode( tag ), renderedNode );
		}

		var tmpWrapper = editor.dom.create( 'div' );
		tmpWrapper.appendChild( df );

		return tmpWrapper.innerHTML;
	}


	/**
	 * Renders the inline tag and updates the post content
	 */
	function update() {
		var content = editor.getContent();
		editor.setContent( renderInlineTags( content ) );
	}


	/**
	 * Determines if a given node is part of a rendered node
	 *
	 * @param element Node to be determined
	 * @param string Element attribute used to identify root of rendered node
	 * @return mixed Root element of rendered node, False if give node is not part of a rendered node
	 */
	function getRenderedNode( node, nodeId ) {
		if( !nodeId ) nodeId = 'data-b2evo-tag';

		while( node, node.parentNode ) {

			if( node.nodeName != '#text' && node.getAttribute( nodeId ) ) {
				return node;
			}

			node = node.parentNode;
		}

		return false;
	}


	/**
	 * Retrieves tag from rendered node
	 *
	 * @param Element rendered node
	 * @return String shorttag
	 */
	function getRenderedNodeData( node )
	{
		var tag = node.getAttribute( 'data-b2evo-tag' );

		if( tag )	{
			return parseTag( tag );
		} else {
			return false;
		}
	}


	/**
	 * Parses data from shorttag
	 *
	 * @string Shorttag
	 * @return Array tag information
	 */
	function parseTag( tag )
	{
		if( tag ) {
			tag = window.decodeURIComponent( tag );
			var re = /\[(image|file|inline|video|audio|thumbnail):(\d+):?([^\[\]]*)\]/i;
			var m = re.exec( tag );
			var data = {
					tag: m[0],
					type: m[1],
					linkId: parseInt( m[2] )
				};

			switch( data.type ) {
				case 'image':
					var options = m[3]
					if( options ) {
						options = options.split(':');
						if( options[0] ) {
							data['caption'] = options[0];
						}
						if( options[1] ) {
							var extraClass = options[1];
							var alignClasses = [ '.floatleft', '.floatright' ];
							var maxIndex = -1, index;
							for( var i = 0; i < alignClasses.length; i++ ) {
								index = extraClass.indexOf( alignClasses[i] );
								if( index > maxIndex ) {
									maxIndex = index;
									data['alignment'] = alignClasses[i];
								}
							}

							var cls = extraClass.split('.');
							data['extraClass'] = '';
							for( var i = 0; i < cls.length; i++ ) {
								if( cls[i] == '' || alignClasses.indexOf( '.' + cls[i] ) !== -1 ) {
									continue;
								} else {
									data['extraClass'] += '.' + cls[i];
								}
							}
						}
					}
					else
					{
						data['caption'] = null;
						data['extraClass'] = null;
					}
					break;

				default:
					data['options'] = m[3];
			}

			return data;
		} else {
			return false;
		}
	};


	// Render shorttags into rendered nodes
	editor.on( 'BeforeSetContent', function( event )  {
		event.content = renderInlineTags( event.content );
	});


	// Restore rendered nodes into shorttags again
	editor.on( 'PostProcess', function( event )	{
		if( event.get )
		{
			event.content = restoreShortTags( event.content );
		}
	});


	// Update content and render inline tags when attachments are reloaded
	editor.on( 'attachmentsLoaded', function( event ) {
		update();
	});


	// Check if selected node is part of rendered node
	editor.on( 'mousedown mouseup click', function( event ) {
		var rNode = getRenderedNode( event.target );

		if( rNode )
		{
			select( rNode );
			//editor.selection.select( rNode );
		}
		else
		{
			deselect();
		}
	}, true);


	// Prevent editing if current selection part of rendered node
	editor.on( 'keypress keydown keyup', function( event ) {
		if( selected )
		{
			// TODO: move the following outside the function
			var allowedKeys = [];
			allowedKeys.push( 16, 17, 18, 19 ); // Ctrl, Alt, Shift, Pause/Break
			allowedKeys.push( 20, 27, 45 ); // Capslock, Esc, Insert
			allowedKeys.push( 33, 34, 35, 36 ); // PgUp, PgDn, End, Home
			allowedKeys.push( 37, 38, 39, 40 ); // Arrow keys
			allowedKeys.push( 91, 92, 93 ); // Left and right Windows Keys, select key
			allowedKeys.push( 112, 113, 114, 115, 116, 117, 118, 119 ,120, 121, 122, 123 ); // function keys
			allowedKeys.push( 144, 145 ); // NumLock, ScrollLock

			if( event.which == 8 || event.which == 46 ) // Backspace, Delete
			{
				editor.dom.remove( selected );
			}
			else if( allowedKeys.indexOf( event.which ) == -1 )
			{
				event.preventDefault();
				return false;
			}
		}
	}, true );


	// Select rendered node if selection has changed to child of rendered node
	editor.on( 'NodeChange', function( event ) {

		if( event.selectionChange )
		{
			var rNode = getRenderedNode( editor.selection.getNode() );
			if( rNode )
			{
				select( rNode );
				//editor.selection.select( rNode );
			}
			else
			{
				deselect();
			}
		}
	});


	// Set rendered node type
	editor.on( 'ResolveName', function( event ) {
		if ( editor.dom.hasClass( event.target, 'b2evo-tag' ) ) {
			var tagType = editor.dom.getAttrib( event.target, 'data-b2evo-type' );
			if( tagType ) {
				event.name = '[' + tagType + ':]';
			} else {
				event.name = 'shorttag';
			}
			event.stopPropagation();
		} else if ( getRenderedNode( event.target ) ) {
			event.preventDefault();
			event.stopPropagation();
		}
	});


	// Set dragged element data
	editor.on( 'dragstart', function( event ) {
		var rNode = getRenderedNode( event.target );

		if( rNode )
		{
			select( rNode );
			var tag = window.decodeURIComponent( rNode.getAttribute( 'data-b2evo-tag' ) );
			event.dataTransfer.setData( 'application/x-moz-node', event.target );
			event.dataTransfer.setData( 'text/plain', tag );
			event.dataTransfer.effectAllowed = 'move';
		}
		else
		{
			deselect();
		}
	});


	// Drop handler
	editor.on( 'drop', function( event ) {
		var target = event.target,
			tag = event.dataTransfer.getData( 'text/plain' );

		var rNode = getRenderedNode( target );
		if( rNode ) {
			target = rNode;
		}

		// Dragged element dropped on body itself and we are unable to determine
		// where we can insert the element so let's cancel the drop
		if( target.tagName == 'BODY' )
		{
			event.preventDefault();
			return false;
		}

		// An element belonging to a rendered node was dragged and dropped
		if( rNode && tag && selected ) {
			event.preventDefault();
			target.insertAdjacentHTML( 'beforebegin', tag );
			editor.dom.remove( selected );
			update();
			return false;
		}
	});


	editor.on( 'init', function() {
		var scrolled = false,
			selection = editor.selection;

		// When a renderedNode is selected, ensure content that is being pasted
		// or inserted is added to a text node (instead of the renderedNode).
		editor.on( 'BeforeSetContent', function() {
			var walker, target,
				rNode = getRenderedNode( selection.getNode() );

			// If the selection is not within a renderedNode, bail.
			if ( !rNode ) {
				return;
			}

			if ( !rNode.nextSibling || getRenderedNode( rNode.nextSibling ) ) {
				// If there are no additional nodes or the next node is a
				// renderedNode, create a text node after the current renderedNode.
				target = editor.getDoc().createTextNode('');
				editor.dom.insertAfter( target, rNode );
			} else {
				// Otherwise, find the next text node.
				walker = new tinymce.dom.TreeWalker( rNode.nextSibling, rNode.nextSibling );
				target = walker.next();
			}

			// Select the `target` text node.
			selection.select( target );
			selection.collapse( true );
		});
	});

});