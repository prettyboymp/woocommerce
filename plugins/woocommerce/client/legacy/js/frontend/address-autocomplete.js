/**
 * Simple address provider registration for WooCommerce checkout
 */
var wooAddressProviders = {};

/**
 * State tracking for address autocomplete functionality
 */
var autocompleteState = {
	suggestionsShowing: false, // Whether suggestion dropdown is visible
	isSearching: false, // True after user enters at least 3 characters
	autocompleteEnabled: false, // Whether merchant has enabled autocomplete and canSearch returned true
	isLoadingSuggestions: false, // Whether request to load suggestions is in progress
};

/**
 * Register an address autocomplete provider
 *
 * @param {Object} provider The provider object
 * @return {boolean} Whether the registration was successful
 */
function registerAddressAutocompleteProvider( provider ) {
	try {
		// Check required properties
		if ( ! provider || typeof provider !== 'object' ) {
			throw new Error( 'Address provider must be a valid object' );
		}

		if ( ! provider.id || typeof provider.id !== 'string' ) {
			throw new Error( 'Address provider must have a valid ID' );
		}

		if ( typeof provider.canSearch !== 'function' ) {
			throw new Error(
				'Address provider must have a canSearch function'
			);
		}

		if ( typeof provider.search !== 'function' ) {
			throw new Error( 'Address provider must have a search function' );
		}

		if ( typeof provider.select !== 'function' ) {
			throw new Error( 'Address provider must have a select function' );
		}

		// Check if provider is registered on server
		var serverProviders = [];
		if (
			window &&
			window.wc_checkout_params &&
			window.wc_checkout_params.address_providers
		) {
			serverProviders = window.wc_checkout_params.address_providers;
		}

		if ( ! Array.isArray( serverProviders ) ) {
			throw new Error( 'Server providers configuration is invalid' );
		}

		var isRegistered = serverProviders.some( function ( serverProvider ) {
			return (
				serverProvider &&
				typeof serverProvider === 'object' &&
				typeof serverProvider.id === 'string' &&
				serverProvider.id === provider.id
			);
		} );
		if ( ! isRegistered ) {
			throw new Error(
				'Provider ' + provider.id + ' not registered on server'
			);
		}

		// Freeze and add provider to registry.
		Object.freeze( provider );
		wooAddressProviders[ provider.id ] = provider;

		// Initialize autocomplete UI when provider is registered
		initializeAddressAutocomplete( provider );

		return true;
	} catch ( error ) {
		console.error( 'Error registering address provider:', error.message );
		return false;
	}
}

/**
 * Initialize autocomplete UI elements and event listeners
 *
 * @param {Object} provider The address provider
 */
function initializeAddressAutocomplete( provider ) {
	// Wait for DOM to be ready
	jQuery( function ( $ ) {
		// Get the billing and shipping address fields
		var addressFields = [ '#billing_address_1', '#shipping_address_1' ];

		addressFields.forEach( function ( selector ) {
			var $addressField = $( selector );
			if ( $addressField.length === 0 ) return;

			// Check if autocomplete can be enabled with this provider
			var canSearch = false;
			try {
				canSearch = provider.canSearch();
			} catch ( error ) {
				console.error( 'Error calling canSearch:', error );
			}

			// Update state
			autocompleteState.autocompleteEnabled = canSearch;

			if ( canSearch ) {
				// Update placeholder to indicate search capability
				var originalPlaceholder =
					$addressField.attr( 'placeholder' ) || '';
				$addressField.attr( 'placeholder', 'Enter / Search Address' );

				console.log( originalPlaceholder, $addressField );

				// Add a search icon next to the field
				if (
					! $addressField.parent().find( '.address-search-icon' )
						.length
				) {
					$addressField.after(
						'<span class="address-search-icon dashicons dashicons-search"></span>'
					);
				}

				// Handle input for search
				$addressField.on( 'input', function () {
					var value = $( this ).val();

					// Update autocomplete attribute based on isSearching state
					if ( value.length >= 3 ) {
						autocompleteState.isSearching = true;
						$( this ).attr( 'autocomplete', 'off' );
					} else {
						autocompleteState.isSearching = false;
						$( this ).attr( 'autocomplete', 'address-line1' );
					}

					if ( value.length >= 3 ) {
						// Update loading state
						autocompleteState.isLoadingSuggestions = true;
						autocompleteState.suggestionsShowing = true;

						// Debounce search to avoid too many requests
						clearTimeout( $( this ).data( 'search-timeout' ) );
						$( this ).data(
							'search-timeout',
							setTimeout( function () {
								provider
									.search( value )
									.then( function ( results ) {
										autocompleteState.isLoadingSuggestions = false;

										// Handle empty results
										if ( ! results || ! results.length ) {
											return;
										}
									} )
									.catch( function ( error ) {
										autocompleteState.isLoadingSuggestions = false;
										console.error(
											'Error searching addresses:',
											error
										);
									} );
							}, 300 )
						);
					} else {
						autocompleteState.suggestionsShowing = false;
					}
				} );
			}
		} );
	} );
}

/**
 * Get current address autocomplete state
 *
 * @return {Object} Current state of address autocomplete
 */
function getAddressAutocompleteState() {
	return { ...autocompleteState };
}

// Make functions available globally
window.wc = window.wc || {};
window.wc.addressAutocomplete = {
	registerAddressAutocompleteProvider: registerAddressAutocompleteProvider,
	getState: getAddressAutocompleteState,
};
