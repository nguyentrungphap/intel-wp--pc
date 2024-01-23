'use strict';

jQuery( document ).ready( function( $ ) {
	/**
	 * Settings tab navigation.
	 */
	function AuthorsListinitSettingsNav() {
		if ( $( '.authors-list-settings' ).length ) {
			const nav = $( '.authors-list-settings-nav' );
			const main = $( '.authors-list-settings-main' );

			// Change section on nav click.
			$( document ).on( 'click', '.authors-list-settings-nav a', function( e ) {
				e.preventDefault();

				const sectionID = $( this ).data( 'of-id' );

				$( this ).addClass( 'nav-tab-active' ).siblings( '.nav-tab-active' ).removeClass( 'nav-tab-active' );
				main.find( '.authors-list-settings-section[data-of-id="' + sectionID + '"]' ).addClass( 'authors-list-active' ).siblings( '.authors-list-active' ).removeClass( 'authors-list-active' );

				const selectedSectionElem = $( '.authors-list-selected-section' ).val( sectionID );
			} );
		}
	}

	// Initiate nav.
	AuthorsListinitSettingsNav();

	/**
	 * Load item preview.
	 */
	 function authorsListloadItemPreview() {
		// Element.
		const holder = $( '.authors-list-dashboard-preview' );
		const inner = holder.find( '.authors-list-dashboard-preview-inner' );

		// Set ajax data.
		const data = {
			'action': 'authors_list_display_edit_item_preview_ajax',
			'item_id': holder.data( 'item-id' ),
			'settings': $( '.authors-list-settings-main form' ).serialize(),
		};

		$( '.authors-list-dashboard-preview-loader' ).show();

		$.post( ajaxurl, data, function( response ) {
			if ( response.success ) {
				inner.html( response.data.disable + response.data.output );
			}
			$( '.authors-list-dashboard-preview-loader' ).hide();
			authorsListTransparentPreviewSection();
			authorsListStylerInit();
		} );
	}

	$( '.authors-list-settings-field-reload-on-change input, .authors-list-settings-field-reload-on-change select, .authors-list-settings-field-reload-on-change textarea' ).on( 'change', function() {
		authorsListloadItemPreview();
	} );

	// For radio select image selected option.
	$( '.authors-list-radio-image' ).each( function() {
		const radioOptions = $( this ).find( '.authors-list-radio-image-item' );

		radioOptions.on( 'click', function() {
			const $this = $( this );
			const selectorParentClass = $this.closest( '.authors-list-radio-image' );
			$this.find( 'input[type="radio"]' ).prop( 'checked', true ).trigger( 'change' );
			const selected = selectorParentClass.find( 'input[type="radio"]:checked' ).closest( '.authors-list-radio-image-item' );
			// Add and remove the class.
			selectorParentClass.find( '.authors-list-radio-image-item' ).removeClass( 'authors-list-radio-image-selected' );
			selected.addClass( 'authors-list-radio-image-selected' );
		} );
	} );

	// Stop the default behaviour of <label> click inside of the radio image selection,
	// if is is not stopped, it causes the `change` event on the radio to be triggered twice.
	$( document ).on( 'click', '.authors-list-radio-image label', function( event ) {
		event.preventDefault();
	} );

	// Remove saved settings information above the item settings.
	const authorsListRemoveSavedMsgElem = $( '.authors-list-above-item-settings .authors-list-settings-notice-success' );
	if ( authorsListRemoveSavedMsgElem ) {
		setTimeout( function() {
			authorsListRemoveSavedMsgElem.fadeOut();
		}, 3000 );
	}

	// Confirm dialog display for the Trash dashboard action link.
	if ( authorsListDashboardActions !== 'undefined' ) {
		const trashElem = $( '.authors-list-trash-link' );
		trashElem.on( 'click', function( event ) {
			const confirmation = confirm( authorsListDashboardActions.trash );
			return confirmation;
		} );
	}

	/**
	 * Remove and add the transparent preview div according to the current section tab selected.
	 */
	function authorsListTransparentPreviewSection() {
		const parentElem = jQuery( '.authors-list-settings-nav' );
		const activeTabElem = parentElem.find( '.nav-tab-active' );
		const transparentPreviewElem = jQuery( '.authors-list-dashboard-preview-styler-disabled' );
		transparentPreviewElem.show();

		// If the current tab enabled is `styling`, remove the transparent preview div.
		if ( 'styling' === activeTabElem.data( 'of-id' ) ) {
			transparentPreviewElem.hide();
		}
	}

	// Remove and add the transparent preview div according to the current section tab selected on page load.
	authorsListTransparentPreviewSection();

	// Remove and add the transparent preview div according to the current section tab selected on click event.
	jQuery( document ).on( 'click', '.authors-list-settings-nav', function() {
		authorsListTransparentPreviewSection();
	} );

	/* global authorsListOptionsDependency */
	if ( typeof authorsListOptionsDependency !== 'undefined' ) {
		// Dependency for the item options.
		function AuthorsListOptionsDependency() {
			const dependencies = authorsListOptionsDependency;
			jQuery.each( dependencies, function( index, value ) {
				let tempValue;
				const objectLength = Object.keys( value ).length;

				jQuery.each( value, function( i, val ) {
					const mainElem = jQuery( '.authors-list-settings-field-main' );
					const currentOptionElem = mainElem.find( 'input[name*="[' + index + ']"], select[name*="[' + index + ']"], textarea[name*="[' + index + ']"]' );
					const options = mainElem.find( 'input[name*="[' + i + ']"], select[name*="[' + i + ']"]' );

					options.each( function( id, selector ) {
						if ( ( 'checkbox' === jQuery( selector ).attr( 'type' ) ) || ( jQuery( selector ).val() ) ) {
							const checkboxValue = jQuery( selector ).prop( 'checked' );
							const inputValue = jQuery( selector ).val();

							if ( objectLength <= 1 ) {
								if ( ( checkboxValue ) || ( inputValue === val ) ) {
									currentOptionElem.closest( '.authors-list-settings-field' ).show();
								} else {
									currentOptionElem.closest( '.authors-list-settings-field' ).hide();
								}
							} else {
								if ( tempValue && inputValue === val ) {
									currentOptionElem.closest( '.authors-list-settings-field' ).show();
								} else {
									currentOptionElem.closest( '.authors-list-settings-field' ).hide();
								}
							}
							tempValue = checkboxValue;
						}
					} );
				} );
			} );
		}

		// Initiate.
		AuthorsListOptionsDependency();

		// Change the dependency options display according to the events taken.
		const dependencies = authorsListOptionsDependency;
		jQuery.each( dependencies, function( index, value ) {
			jQuery.each( value, function( i, val ) {
				const mainElem = 'input[name*="[' + i + ']"], select[name*="[' + i + ']"]';
				jQuery( document ).on( 'click change', mainElem, function() {
					AuthorsListOptionsDependency();
				} );
			} );
		} );
	}

	/* global authorsListSearchFilters */
	if ( typeof authorsListSearchFilters !== 'undefined' ) {
		/**
		 * Search filters.
		 */
		function AuthorsListSearchFilters() {
			const buttonClick = $( '.authors-list-filters-add' );
			buttonClick.on( 'click', function( event ) {
				event.preventDefault();
				const parentElem = $( this ).closest( '.authors-list-settings-field-main' );
				const appendElem = parentElem.find( '.authors-list-search-filter-options' );
				let selectOptions = '<option disabled selected>Filter Type</option>';
				$.each( authorsListSearchFilters.select, function( index, value ) {
					selectOptions += '<option value="' + index + '">' + value + '</option>';
				} );
				const elements = '<div class="authors-list-search-filters">' +
				'<input type="text" class="authors-list-meta-key" placeholder="Meta Key" />' +
				'<input type="text" class="authors-list-label" placeholder="Label" />' +
				'<select class="authors-list-type">' +
				selectOptions +
				'</select>' +
				'<input type="button" class="authors-list-filters-remove button button-secondary" value="' + authorsListSearchFilters.remove + '" />' +
				'</div>';

				// Append the elements.
				appendElem.append( elements );
			} );
		}

		// Initiate search filters.
		AuthorsListSearchFilters();

		/**
		 * Binding click events for data updates on the search filters options.
		 */
		$( document ).on( 'click', '.authors-list-filters-remove', function() {
			const mainElem = $( this ).closest( '.authors-list-search-filters' );
			mainElem.remove();
		} );

		/**
		 * Binding keyup/change/click events for data updates on the search filters options update.
		 */
		$( document ).on( 'keyup change click', '.authors-list-search-filters, .authors-list-search-filters > input, .authors-list-search-filters > select', function() {
			const searchFiltersValues = $( '.authors-list-search-filters-values' );
			const searchFilterElems = $( '.authors-list-search-filters .authors-list-meta-key, .authors-list-search-filters .authors-list-label, .authors-list-search-filters .authors-list-type' );

			const elemValues = $.map( searchFilterElems, function( value ) {
				return $( value ).val();
			} );
			const hiddenInputValue = elemValues.join( ',' ).replaceAll( 'select,', 'select|' ).replaceAll( 'checkboxes,', 'checkboxes|' ).replaceAll( 'radio,', 'radio|' ).replaceAll( 'number_range,', 'number_range|' );

			// Update the value of the main hidden input type/field.
			searchFiltersValues.val( hiddenInputValue );
		} );
	}
} );
