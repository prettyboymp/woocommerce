/**
 * Internal dependencies
 */
import {
	registerAddressAutocompleteProvider,
	getAddressAutocompleteProviders,
	removeAddressAutocompleteProvider,
} from '../registry';

jest.mock( '@woocommerce/settings', () => {
	const originalModule = jest.requireActual( '@woocommerce/settings' );

	return {
		...originalModule,
		getSetting: ( setting: string, ...rest: unknown[] ) => {
			if ( setting === 'addressAutocompleteProviders' ) {
				return [ 'test-provider', 'duplicate-provider' ];
			}
			return originalModule.getSetting( setting, ...rest );
		},
	};
} );

describe( 'Address Autocomplete Registry', () => {
	afterEach( () => {
		const providers = getAddressAutocompleteProviders();
		Object.keys( providers ).forEach( ( id ) => {
			removeAddressAutocompleteProvider( id );
		} );
	} );

	it( 'should register a valid provider', () => {
		const provider = {
			id: 'test-provider',
			canSearch: () => true,
			search: () => ( { result: { text: 'Test', id: '1' } } ),
			select: () => ( { address1: '123 Test St' } ),
		};

		registerAddressAutocompleteProvider( provider );

		const providers = getAddressAutocompleteProviders();
		expect( providers[ provider.id ] ).toBe( provider );
	} );

	it( 'should not register a provider with an empty ID', () => {
		const provider = {
			id: '',
			canSearch: () => true,
			search: () => ( { result: { text: 'Test', id: '1' } } ),
			select: () => ( { address1: '123 Test St' } ),
		};

		registerAddressAutocompleteProvider( provider );

		const providers = getAddressAutocompleteProviders();
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

		registerAddressAutocompleteProvider( provider as any );

		const providers = getAddressAutocompleteProviders();
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

		registerAddressAutocompleteProvider( provider );

		const providers = getAddressAutocompleteProviders();
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

		registerAddressAutocompleteProvider( provider );
		registerAddressAutocompleteProvider( provider );

		const providers = getAddressAutocompleteProviders();
		expect( Object.keys( providers ).length ).toBe( 1 );
		expect( console ).toHaveErroredWith(
			`A provider with the ID "${ provider.id }" is already registered.`
		);
	} );
} );
