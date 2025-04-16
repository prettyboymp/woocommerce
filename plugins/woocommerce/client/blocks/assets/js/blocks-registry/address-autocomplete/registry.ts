/**
 * External dependencies
 */
import { getSetting } from '@woocommerce/settings';
import type { BaseAddress } from '@woocommerce/type-defs/cart';
import {
	isObject,
	objectHasProp,
	isFunction,
	isString,
} from '@woocommerce/types';

export interface SearchResult {
	highlightPositions?: { offset: number; length: number }[];
	text: string;
	id: string;
}

export interface AddressAutocompleteProvider {
	id: string;
	canSearch: ( currentAddress: Partial< BaseAddress > ) => boolean;
	search: (
		value: string,
		currentAddress: Partial< BaseAddress >
	) => Record< string, SearchResult >;
	select: ( id: string ) => BaseAddress;
}

const addressAutocompleteProviders: Record<
	string,
	AddressAutocompleteProvider
> = {};

/**
 * Type guard to validate AddressAutocompleteProvider.
 *
 * @param provider The provider to validate.
 * @return Whether the provider is valid.
 */
const isValidAddressAutocompleteProvider = (
	provider: unknown
): provider is AddressAutocompleteProvider => {
	if ( ! isObject( provider ) ) {
		// eslint-disable-next-line no-console -- developers should be able to see this error to debug their providers.
		console.error( 'The provider must be an object.' );
		return false;
	}

	if (
		! objectHasProp( provider, 'id' ) ||
		! isString( provider.id ) ||
		provider.id.trim() === ''
	) {
		// eslint-disable-next-line no-console -- developers should be able to see this error to debug their providers.
		console.error( 'The provider ID must be a non-empty string.' );
		return false;
	}

	const requiredMethods = [ 'canSearch', 'search', 'select' ] as const;
	for ( const method of requiredMethods ) {
		if (
			! objectHasProp( provider, method ) ||
			! isFunction( provider[ method ] )
		) {
			// eslint-disable-next-line no-console -- developers should be able to see this error to debug their providers.
			console.error(
				`The "${ method }" method for provider "${ provider.id }" must be a function.`
			);
			return false;
		}
	}

	return true;
};

/**
 * Register an address autocomplete provider.
 *
 * @param provider The provider configuration.
 */
export const __experimentalRegisterAddressAutocompleteProvider = (
	provider: unknown
): void => {
	if ( ! isValidAddressAutocompleteProvider( provider ) ) {
		return;
	}

	// Use getSetting to check if the server-side provider exists.
	const registeredProviders = getSetting< string[] >(
		'addressAutocompleteProviders',
		[]
	);

	if ( ! registeredProviders.includes( provider.id ) ) {
		// eslint-disable-next-line no-console -- developers should be able to see this error to debug their providers.
		console.error(
			`No server-side provider is registered with the ID "${ provider.id }".`
		);
		return;
	}

	if ( addressAutocompleteProviders[ provider.id ] ) {
		// eslint-disable-next-line no-console -- developers should be able to see this error to debug their providers.
		console.error(
			`A provider with the ID "${ provider.id }" is already registered.`
		);
		return;
	}

	addressAutocompleteProviders[ provider.id ] = provider;
};

/**
 * Remove an address autocomplete provider by ID.
 *
 * @param id The ID of the provider to remove.
 */
export const __experimentalRemoveAddressAutocompleteProvider = (
	id: string
): void => {
	if ( addressAutocompleteProviders[ id ] ) {
		delete addressAutocompleteProviders[ id ];
	}
};

/**
 * Get all registered address autocomplete providers.
 *
 * @return A record of registered providers.
 */
export const __experimentalGetAddressAutocompleteProviders = (): Record<
	string,
	AddressAutocompleteProvider
> => {
	return addressAutocompleteProviders;
};
