/**
 * External dependencies
 */
import * as settings from '@woocommerce/settings';

/**
 * Internal dependencies
 */
import {
	__experimentalRegisterAddressAutocompleteProvider,
	__experimentalGetAddressAutocompleteProviders,
	__experimentalRemoveAddressAutocompleteProvider,
} from '../registry';

jest.mock( '@woocommerce/settings', () => {
	const originalModule = jest.requireActual( '@woocommerce/settings' );

	return {
		...originalModule,
		getSetting: jest
			.fn()
			.mockImplementation( ( setting: string, ...rest: unknown[] ) => {
				if ( setting === 'addressAutocompleteProviders' ) {
					return [ 'test-provider', 'duplicate-provider' ];
				}
				return originalModule.getSetting( setting, ...rest );
			} ),
	};
} );

describe( 'Address Autocomplete Registry', () => {
	afterEach( () => {
		const providers = __experimentalGetAddressAutocompleteProviders();
		Object.keys( providers ).forEach( ( id ) => {
			__experimentalRemoveAddressAutocompleteProvider( id );
		} );
	} );

	it( 'should register a valid provider', () => {
		const provider = {
			id: 'test-provider',
			canSearch: () => true,
			search: () => ( { result: { text: 'Test', id: '1' } } ),
			select: () => ( { address1: '123 Test St' } ),
		};

		__experimentalRegisterAddressAutocompleteProvider( provider );

		const providers = __experimentalGetAddressAutocompleteProviders();
		expect( providers[ provider.id ] ).toBe( provider );
	} );

	it( 'should not register a provider with an empty ID', () => {
		const provider = {
			id: '',
			canSearch: () => true,
			search: () => ( { result: { text: 'Test', id: '1' } } ),
			select: () => ( { address1: '123 Test St' } ),
		};

		__experimentalRegisterAddressAutocompleteProvider( provider );

		const providers = __experimentalGetAddressAutocompleteProviders();
		expect( providers[ provider.id ] ).toBeUndefined();
		expect( console ).toHaveErroredWith(
			'The provider ID must be a non-empty string.'
		);
	} );

	it( 'should not register a provider without a canSearch function', () => {
		const provider = {
			id: 'invalid-provider',
			search: () => ( { result: { text: 'Test', id: '1' } } ),
			select: () => ( { address1: '123 Test St' } ),
		};

		__experimentalRegisterAddressAutocompleteProvider( provider );

		const providers = __experimentalGetAddressAutocompleteProviders();
		expect( providers[ provider.id ] ).toBeUndefined();
		expect( console ).toHaveErroredWith(
			`The "canSearch" method for provider "${ provider.id }" must be a function.`
		);
	} );

	it( 'should not register a provider if server-side provider is missing', () => {
		const provider = {
			id: 'missing-server-provider',
			canSearch: () => true,
			search: () => ( { result: { text: 'Test', id: '1' } } ),
			select: () => ( { address1: '123 Test St' } ),
		};

		__experimentalRegisterAddressAutocompleteProvider( provider );

		const providers = __experimentalGetAddressAutocompleteProviders();
		expect( providers[ provider.id ] ).toBeUndefined();
		expect( console ).toHaveErroredWith(
			`No server-side provider is registered with the ID "${ provider.id }".`
		);
	} );

	it( 'should not register a duplicate provider', () => {
		const provider = {
			id: 'duplicate-provider',
			canSearch: () => true,
			search: () => ( { result: { text: 'Test', id: '1' } } ),
			select: () => ( { address1: '123 Test St' } ),
		};

		__experimentalRegisterAddressAutocompleteProvider( provider );
		__experimentalRegisterAddressAutocompleteProvider( provider );

		const providers = __experimentalGetAddressAutocompleteProviders();
		expect( Object.keys( providers ).length ).toBe( 1 );
		expect( console ).toHaveErroredWith(
			`A provider with the ID "${ provider.id }" is already registered.`
		);
	} );

	it( 'should remove a registered provider', () => {
		const provider = {
			id: 'test-provider',
			canSearch: () => true,
			search: () => ( { result: { text: 'Test', id: '1' } } ),
			select: () => ( { address1: '123 Test St' } ),
		};

		__experimentalRegisterAddressAutocompleteProvider( provider );
		let providers = __experimentalGetAddressAutocompleteProviders();
		expect( providers[ provider.id ] ).toBe( provider );

		__experimentalRemoveAddressAutocompleteProvider( provider.id );
		providers = __experimentalGetAddressAutocompleteProviders();
		expect( providers[ provider.id ] ).toBeUndefined();
	} );

	it( 'should return all registered providers', () => {
		jest.mocked( settings.getSetting ).mockImplementation( ( setting ) => {
			if ( setting === 'addressAutocompleteProviders' ) {
				return [ 'provider-1', 'provider-2' ];
			}
			return settings.getSetting( setting );
		} );
		const provider1 = {
			id: 'provider-1',
			canSearch: () => true,
			search: () => ( { result: { text: 'Test 1', id: '1' } } ),
			select: () => ( { address1: '123 Test St' } ),
		};

		const provider2 = {
			id: 'provider-2',
			canSearch: () => true,
			search: () => ( { result: { text: 'Test 2', id: '2' } } ),
			select: () => ( { address1: '456 Test Ave' } ),
		};

		__experimentalRegisterAddressAutocompleteProvider( provider1 );
		__experimentalRegisterAddressAutocompleteProvider( provider2 );

		const providers = __experimentalGetAddressAutocompleteProviders();
		expect( Object.keys( providers ) ).toContain( provider1.id );
		expect( Object.keys( providers ) ).toContain( provider2.id );
		expect( providers[ provider1.id ] ).toBe( provider1 );
		expect( providers[ provider2.id ] ).toBe( provider2 );
	} );
} );
