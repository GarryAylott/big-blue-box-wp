( function () {
	var addFilter = wp.hooks.addFilter;
	var createElement = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var createHigherOrderComponent = wp.compose.createHigherOrderComponent;

	var guidance = {
		1: 'Do not use — the post title is the H1.',
		2: 'Main section heading — use for primary content sections.',
		3: 'Large sub-header — use for subsections within an H2.',
		4: 'Small sub-header — use for further detail within an H3.',
		5: 'Minor heading — rarely needed, use sparingly.',
		6: 'Smallest heading — rarely needed, use sparingly.',
	};

	var withHeadingGuidance = createHigherOrderComponent( function ( BlockEdit ) {
		return function ( props ) {
			if ( props.name !== 'core/heading' ) {
				return createElement( BlockEdit, props );
			}

			var level = props.attributes.level || 2;
			var hint = guidance[ level ];

			return createElement(
				Fragment,
				null,
				createElement( BlockEdit, props ),
				hint
					? createElement(
							InspectorControls,
							null,
							createElement(
								PanelBody,
								{ title: 'Heading Guidance', initialOpen: true },
								createElement(
									'p',
									{ style: { margin: 0, fontSize: '13px', color: '#757575' } },
									'H' + level + ' — ' + hint
								)
							)
					  )
					: null
			);
		};
	}, 'withHeadingGuidance' );

	addFilter(
		'editor.BlockEdit',
		'bbb/heading-guidance',
		withHeadingGuidance
	);
} )();
