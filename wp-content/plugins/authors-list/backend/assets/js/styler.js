'use strict';

/**
 *  Global vars.
 */
let authorsListStylerVar = {};
/* global authorsListFonts */
const authorsListStylerFontsRegular = authorsListFonts.regular;
const authorsListStylerFontsGoogle = authorsListFonts.google;
const authorsListStylerFontsAll = authorsListStylerFontsRegular.concat( authorsListStylerFontsGoogle );

/**
 * Initialize.
 */
function authorsListStylerInit() {
	/* global authorsListStylerData */
	jQuery.each( authorsListStylerData, function( key, value ) {
		let selector = value.selector;
		const element = value.element;
		let label = value.label;
		const noSupport = value.nosupport;

		// If selector not defined use element.
		if ( selector === undefined ) {
			selector = element;
		}

		// If label not defined generate from element.
		if ( label === undefined ) {
			label = element;
		}

		jQuery( element ).attr( 'data-authors-list-styler-selector', selector );
		jQuery( element ).attr( 'data-authors-list-styler-label', label );
		jQuery( element ).attr( 'data-authors-list-styler-no-support', noSupport );
	} );

	if ( jQuery( '#authors-list-styler-panel-data' ).val() ) {
		authorsListStylerVar = JSON.parse( jQuery( '#authors-list-styler-panel-data' ).val() );
	}

	// Initialize options.
	authorsListStylerOptionSliderInit();
	authorsListStylerOptionColorpickerInit();
	authorsListStylerOptionFontFamilyInit();
	authorsListStylerMediaImageSelect();
	authorsListStylerMediaImageRemove();

	// Apply class to all editable elements and popular var.
	jQuery( '[data-authors-list-styler-selector]' ).addClass( 'authors-list-styler-element-editable' ).each( function() {
		const selector = jQuery( this ).data( 'authors-list-styler-selector' );
		if ( authorsListStylerVar[ selector ] === undefined ) {
			authorsListStylerVar[ selector ] = {};
		}
	} );
}

/**
 * Set option values.
 */
function authorsListStylerOptionsSetValues() {
	// Vars.
	const element = jQuery( '.authors-list-styler-element-editable.authors-list-styler-active' );
	const selector = element.data( 'authors-list-styler-selector' );
	const options = jQuery( '.authors-list-styler-panel-option' );
	let optionRule;
	let optionValue;
	let option;

	// Go through every option and set values of the element.
	options.each( function() {
		option = jQuery( this );
		optionRule = option.data( 'authors-list-styler-affect' );
		optionValue = jQuery( selector ).css( optionRule );

		// Fix font weight value.
		if ( optionRule === 'font-weight' ) {
			if ( optionValue === 'normal' ) {
				optionValue = 400;
			} else if ( optionValue === 'bold' ) {
				optionValue = 700;
			}
		}

		// Fix background position value.
		if ( optionRule === 'background-position' ) {
			if ( optionValue === '0% 0%' ) {
				optionValue = 'left top';
			} else if ( optionValue === '100% 0%' ) {
				optionValue = 'right top';
			} else if ( optionValue === '50% 0%' ) {
				optionValue = 'center top';
			} else if ( optionValue === '0% 50%' ) {
				optionValue = 'left center';
			} else if ( optionValue === '100% 50%' ) {
				optionValue = 'right center';
			} else if ( optionValue === '50% 50%' ) {
				optionValue = 'center center';
			} else if ( optionValue === '0% 100%' ) {
				optionValue = 'left bottom';
			} else if ( optionValue === '100% 100%' ) {
				optionValue = 'right bottom';
			} else if ( optionValue === '50% 100%' ) {
				optionValue = 'center bottom';
			}
		}

		option.find( '.authors-list-styler-panel-option-value' ).val( optionValue );

		// Update slider.
		if ( option.hasClass( 'authors-list-styler-panel-option-type-slider' ) ) {
			option.find( '.authors-list-styler-panel-option-slider' ).slider( 'value', parseInt( optionValue ) );
			option.find( '.authors-list-styler-panel-option-extra' ).text( optionValue );
		}

		// Update colorpicker.
		if ( option.hasClass( 'authors-list-styler-panel-option-type-colorpicker' ) ) {
			option.find( '.authors-list-styler-panel-option-value' ).spectrum( 'set', optionValue );
		}

		// Update image.
		if ( option.hasClass( 'authors-list-styler-panel-option-type-image' ) ) {
			const hiddenElem = option.find( '.authors-list-styler-panel-option-value' );
			const hiddenElemValue = hiddenElem.val().replaceAll( 'url("', '' ).replaceAll( '")', '' );
			if ( 'none' !== hiddenElemValue ) {
				jQuery( hiddenElem ).siblings( '.authors-list-styler-panel-image' ).show().find( 'img' ).attr( 'src', hiddenElemValue );
			} else {
				jQuery( hiddenElem ).siblings( '.authors-list-styler-panel-image' ).hide().find( 'img' ).removeAttr( 'src' );
			}
		}
	} );
}

/**
 * Show Options.
 */
function authorsListStylerOptionsShow() {
	// Hide/Show.
	authorsListStylerPanelShowSection( 'options' );
	authorsListStylerOptionsSupport();

	// Currently editing.
	let currentlyEditing = jQuery( '.authors-list-styler-element-editable.authors-list-styler-active' ).data( 'authors-list-styler-selector' );

	if ( jQuery( '.authors-list-styler-element-editable.authors-list-styler-active' ).data( 'authors-list-styler-label' ) ) {
		currentlyEditing = jQuery( '.authors-list-styler-element-editable.authors-list-styler-active' ).data( 'authors-list-styler-label' );
	}
	jQuery( '#authors-list-styler-panel-header-secondary' ).text( currentlyEditing );
}

/**
 * Options group hide/show based on support.
 */
function authorsListStylerOptionsSupport() {
	const selector = jQuery( '.authors-list-styler-element-editable.authors-list-styler-active' );
	const groups = jQuery( '.authors-list-styler-panel-options-group' );
	let group;
	let groupID;
	let supportsData;

	// Show all groups.
	groups.show();

	if ( selector.data( 'authors-list-styler-no-support' ) ) {
		// Split into array.
		supportsData = selector.data( 'authors-list-styler-no-support' ).split( ',' );

		// Hide unsupported groups.
		groups.each( function() {
			group = jQuery( this );
			groupID = group.data( 'authors-list-styler-id' );

			if ( supportsData.indexOf( groupID ) !== -1 ) {
				group.hide();
			}
		} );
	}
}

/**
 * Show options group.
 *
 * @param {*} group Group name passed via the data attribute.
 */
function authorsListStylerOptionsGroupShow( group ) {
	const newGroup = group;
	const prevGroup = jQuery( '.authors-list-styler-panel-options-group.authors-list-styler-active' );

	if ( newGroup.hasClass( 'authors-list-styler-active' ) ) {
		newGroup.removeClass( 'authors-list-styler-active' );
	} else if ( prevGroup.length ) {
		prevGroup.removeClass( 'authors-list-styler-active' );
		newGroup.addClass( 'authors-list-styler-active' );
	} else {
		newGroup.addClass( 'authors-list-styler-active' );
	}
}

/**
 * Initiate Slider Options.
 */
function authorsListStylerOptionSliderInit() {
	const sliders = jQuery( '.authors-list-styler-panel-option-slider' );
	let slider;
	let option;

	sliders.each( function() {
		let attrMin = 0;
		let attrMax = 150;
		let attrInc = 1;

		slider = jQuery( this );
		option = slider.closest( '.authors-list-styler-panel-option' );

		if ( option.data( 'authors-list-styler-min' ) ) {
			attrMin = option.data( 'authors-list-styler-min' );
		}

		if ( option.data( 'authors-list-styler-max' ) ) {
			attrMax = option.data( 'authors-list-styler-max' );
		}

		if ( option.data( 'authors-list-styler-inc' ) ) {
			attrInc = option.data( 'authors-list-styler-inc' );
		}

		slider.slider( {
			min: attrMin,
			max: attrMax,
			step: attrInc,
			slide: function( event, ui ) {
				const handle = jQuery( ui.handle );
				option = handle.closest( '.authors-list-styler-panel-option' );
				const optionField = option.find( '.authors-list-styler-panel-option-value' );
				let ext = '';

				if ( option.data( 'authors-list-styler-ext' ) ) {
					ext = option.data( 'authors-list-styler-ext' );
				}

				const optionValue = ui.value + ext;

				optionField.val( optionValue ).trigger( 'change' );
				option.find( '.authors-list-styler-panel-option-extra' ).text( optionValue );
			},
		} );
	} );
}

/**
 * Initiate Colorpicker Options.
 */
function authorsListStylerOptionColorpickerInit() {
	jQuery( '.authors-list-styler-panel-option-type-colorpicker' ).each( function() {
		// Vars.
		const option = jQuery( this );
		const optionField = option.find( '.authors-list-styler-panel-option-value' );
		const currValue = optionField.val();
		let field;
		let value;

		// Initiate.
		optionField.spectrum( {
			color: currValue,
			showInput: true,
			allowEmpty: true,
			showAlpha: true,
			clickoutFiresChange: true,
			preferredFormat: 'rgb',
			move: function( color ) {
				field = jQuery( this );

				if ( color === null ) {
					value = 'transparent';
				} else {
					value = color.toRgbString();
				}

				field.val( value ).trigger( 'change' );
			},
		} );
	} );
}

/**
 * Initialize font family option.
 */
function authorsListStylerOptionFontFamilyInit() {
	jQuery( document ).on( 'keyup', '.authors-list-styler-panel-option-type-font-family .authors-list-styler-panel-option-value', function( e ) {
		if ( e.which !== 13 && e.which !== 38 && e.which !== 40 ) {
			// Vars.
			const field = jQuery( this );
			const option = field.closest( '.authors-list-styler-panel-option' );
			const fieldVal = field.val();
			const regex = new RegExp( '^' + fieldVal, 'i' );
			const fontsAmount = authorsListStylerFontsAll.length;
			let i = 0;
			const fontMatch = [];

			// Do.
			do {
				// Check if font meets requirements.
				if ( regex.test( authorsListStylerFontsAll[ i ] ) ) {
					if ( fontMatch.length < 10 ) {
						fontMatch.push( authorsListStylerFontsAll[ i ] );
					}
				}

				// Increment count.
				i++;
			// While there are fonts.
			} while ( i < fontsAmount );

			// Clear suggestions.
			jQuery( '.authors-list-styler-panel-option-type-font-family-suggest', option ).html( '' );

			// If a match found.
			if ( fontMatch ) {
				// Show suggestion box.
				jQuery( '.authors-list-styler-panel-option-type-font-family-suggest', option ).show();

				jQuery.each( fontMatch, function( key, font ) {
					jQuery( '.authors-list-styler-panel-option-type-font-family-suggest', option ).append( '<span>' + font + '</span>' );
				} );
			// If a match is not found.
			} else {
				// Hide suggestion box.
				jQuery( '.authors-list-styler-panel-option-type-font-family-suggest', option ).hide();
			}
		}
	} );

	jQuery( document ).on( 'click', '.authors-list-styler-panel-option-type-font-family-suggest span', function() {
		const font = jQuery( this ).text();
		const option = jQuery( this ).closest( '.authors-list-styler-panel-option' );
		const field = option.find( '.authors-list-styler-panel-option-value' );

		field.val( font ).trigger( 'change' );
		jQuery( '.authors-list-styler-panel-option-type-font-family-suggest', option ).hide();
	} );
}

/**
 * Load a font family.
 *
 * @param {*} font Font family.
 */
function authorsListStylerOptionFontFamilyLoad( font ) {
	// If it's a Google font.
	if ( font.length && authorsListStylerFontsGoogle.indexOf( font ) !== -1 ) {
		// Font to load.
		font = font + ':400,100,200,300,500,600,700,800,900';

		/* global WebFont */
		WebFont.load( {
			google: {
				families: [ font ],
			},
		} );
	}
}

/**
 * Generate CSS.
 */
function authorsListStylerGenerateCss() {
	let cssCode = '';
	const prefix = '.authors-list-item-' + jQuery( '.authors-list-dashboard-preview' ).data( 'item-id' );
	let spacer = '';

	// Go through each selector.
	jQuery.each( authorsListStylerVar, function( selector, values ) {
		// If selector has rules.
		if ( ! jQuery.isEmptyObject( values ) ) {
			if ( selector !== '.authors-list-items' ) {
				spacer = ' ';
			} else {
				spacer = '';
			}

			// Open up the selector rules section.
			cssCode += prefix + spacer + selector + ' { ';

			// Go through each rule/value.
			jQuery.each( values, function( rule, value ) {
				// Add the rule value.
				cssCode += rule + ': ' + value + '; ';
			} );

			// Close the selector rules section.
			cssCode += ' } ';
		}
	} );

	jQuery( '#authors-list-styler-panel-code' ).val( cssCode );
}

/**
 * Generate styler data.
 */
function authorsListStylerGenerateData() {
	jQuery( '#authors-list-styler-panel-data' ).val( JSON.stringify( authorsListStylerVar ) );
}

/**
 * Show Section.
 *
 * @param {*} section Section name passed via the data attribute.
 */
function authorsListStylerPanelShowSection( section ) {
	const currSection = jQuery( '.authors-list-styler-panel-section.authors-list-styler-active' );
	const newSection = jQuery( '.authors-list-styler-panel-section[data-authors-list-styler-id="' + section + '"]' );

	currSection.removeClass( 'authors-list-styler-active' );
	newSection.addClass( 'authors-list-styler-active' );
}

jQuery( document ).ready( function( $ ) {
	authorsListStylerInit();

	// Activate.
	jQuery( 'body' ).addClass( 'authors-list-styler-active' );
	jQuery( '#authors-list-styler-panel' ).addClass( 'authors-list-styler-active' );

	// Disable clicking propagation.
	jQuery( document ).on( 'click', 'body.authors-list-styler-active .authors-list-styler-element-editable', function( e ) {
		e.preventDefault();
		e.stopPropagation();
	} );

	// Editable element click.
	jQuery( document ).on( 'click', 'body.authors-list-styler-active .authors-list-styler-element-editable', function() {
		// Active classes.
		jQuery( '.authors-list-styler-element-editable.authors-list-styler-active' ).removeClass( 'authors-list-styler-active' );
		jQuery( this ).addClass( 'authors-list-styler-active' );

		// Show options.
		authorsListStylerOptionsSetValues();
		authorsListStylerOptionsShow();
	} );

	// Default active settings nav group.
	authorsListStylerOptionsGroupShow( jQuery( '.authors-list-styler-panel-options-group[data-authors-list-styler-id="typography"]' ) );

	// Change active settings nav group.
	jQuery( document ).on( 'click', '.authors-list-styler-nav a', function( e ) {
		e.preventDefault();
		jQuery( this ).addClass( 'nav-tab-active' ).siblings( '.nav-tab-active' ).removeClass( 'nav-tab-active' );
		const group = jQuery( this ).data( 'authors-list-styler-id' );
		authorsListStylerOptionsGroupShow( jQuery( '.authors-list-styler-panel-options-group[data-authors-list-styler-id="' + group + '"]' ) );
	} );

	// Option value changed.
	$( document ).on( 'change', '.authors-list-styler-panel-option-value', function() {
		// Vars.
		const option = jQuery( this ).closest( '.authors-list-styler-panel-option' );
		const rule = option.data( 'authors-list-styler-affect' );
		const element = jQuery( '.authors-list-styler-element-editable.authors-list-styler-active' );
		const selector = element.data( 'authors-list-styler-selector' );
		const value = jQuery( this ).val();

		// If font family.
		if ( rule === 'font-family' ) {
			authorsListStylerOptionFontFamilyLoad( value );
		}

		// Apply CSS to element.
		jQuery( selector ).css( rule, value );

		// Apply new rule to var.
		authorsListStylerVar[ selector ][ rule ] = value;

		// Generate data.
		authorsListStylerGenerateData();

		// Generate the CSS.
		authorsListStylerGenerateCss();
	} );
} );

/**
 * Media image upload.
 */
function authorsListStylerMediaImageSelect() {
	let fileFrame;

	jQuery( document ).on( 'click', '.authors-list-styler-panel-image-upload', function( event ) {
		event.preventDefault();

		const element = jQuery( this );
		/* global wp */
		fileFrame = wp.media.frames.media_file = wp.media( {
			states: [ new wp.media.controller.Library( {
				library: wp.media.query( {
					type: 'image',
				} ),
			} ) ],
		} );

		fileFrame.on( 'select', function() {
			const attachment = fileFrame.state().get( 'selection' ).first().toJSON();

			// Add the attachment url to the hidden field.
			const hiddenElem = element.siblings( '.authors-list-styler-panel-option-value' ).val( 'url(' + attachment.url + ')' );
			hiddenElem.trigger( 'change' );

			// Display the background thumbnail image.
			const imageURL = attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.sizes.full.url;
			element.siblings( '.authors-list-styler-panel-image' ).show().find( 'img' ).attr( 'src', imageURL );
		} );

		fileFrame.open();
	} );
}

/**
 * Media image remove.
 */
function authorsListStylerMediaImageRemove() {
	jQuery( document ).on( 'click', '.authors-list-styler-panel-image-delete', function( event ) {
		event.preventDefault();

		const element = jQuery( this );
		const parentElem = element.closest( '.authors-list-styler-panel-image' );
		const imgElem = element.siblings( '.authors-list-styler-panel-image-url' );
		const inputValue = parentElem.siblings( '.authors-list-styler-panel-option-value' );

		// Remove the datas.
		inputValue.val( 'none' );
		inputValue.trigger( 'change' );
		imgElem.attr( 'src', '' );
		parentElem.hide();
	} );
}
