/**
 * External dependencies
 */
import { render } from '@testing-library/react';
import { useSelect, useDispatch } from '@wordpress/data';
import triggerFetch from '@wordpress/api-fetch';
import CheckoutProcessor from '../checkout-processor';

// Mock WordPress dependencies
jest.mock( '@wordpress/data', () => ( {
	useSelect: jest.fn(),
	useDispatch: jest.fn(),
	select: jest.fn(),
} ) );

jest.mock( '@wordpress/notices', () => ( {
	store: {
		createNotice: jest.fn(),
	},
} ) );

jest.mock( '@wordpress/api-fetch', () => jest.fn() );

jest.mock( '@woocommerce/base-utils', () => ( {
	emptyHiddenAddressFields: jest.fn( ( fields ) => fields ),
	removeAllNotices: jest.fn(),
} ) );

jest.mock( '@woocommerce/block-data', () => ( {
	checkoutStore: 'checkout',
	paymentStore: 'payment',
	validationStore: 'validation',
	processErrorResponse: jest.fn(),
	clearCheckoutPutRequests: jest.fn(),
} ) );

jest.mock( '@woocommerce/blocks-registry', () => ( {
	getPaymentMethods: jest.fn( () => ( {} ) ),
	getExpressPaymentMethods: jest.fn( () => ( {} ) ),
} ) );

jest.mock( '@woocommerce/blocks-checkout-events', () => ( {
	checkoutEvents: {
		onCheckoutValidation: jest.fn( () => () => {} ),
	},
} ) );

jest.mock( '../../../hooks/cart/use-store-cart', () => ( {
	useStoreCart: jest.fn( () => ( {
		cartNeedsPayment: false,
		cartNeedsShipping: false,
		receiveCartContents: jest.fn(),
	} ) ),
} ) );

jest.mock( '../../../hooks/use-checkout-address', () => ( {
	useCheckoutAddress: jest.fn( () => ( {
		shippingAddress: {},
		billingAddress: {},
		useBillingAsShipping: false,
	} ) ),
} ) );

jest.mock( '../utils', () => ( {
	preparePaymentData: jest.fn( () => ( {} ) ),
	processCheckoutResponseHeaders: jest.fn(),
} ) );

describe( 'CheckoutProcessor', () => {
	beforeEach( () => {
		// Reset all mocks before each test
		jest.clearAllMocks();

		// Setup default mock implementations
		( useSelect as jest.Mock ).mockImplementation( ( selector ) => {
			// The selector is a function that takes a select function as argument
			return selector( ( storeName: string ) => {
				if ( storeName === 'checkout' ) {
					return {
						getAdditionalFields: () => ( {} ),
						getCustomerId: () => 0,
						getCustomerPassword: () => '',
						getExtensionData: () => ( {} ),
						hasError: () => false,
						isBeforeProcessing: () => false,
						isComplete: () => false,
						isProcessing: () => false,
						getOrderNotes: () => '',
						getRedirectUrl: () => '',
						getShouldCreateAccount: () => false,
					};
				}
				if ( storeName === 'validation' ) {
					return {
						hasValidationErrors: () => false,
						getValidationError: () => undefined,
					};
				}
				if ( storeName === 'payment' ) {
					return {
						getActivePaymentMethod: () => '',
						getPaymentMethodData: () => ( {} ),
						isExpressPaymentMethodActive: () => false,
						hasPaymentError: () => false,
						isPaymentReady: () => true,
						getShouldSavePaymentMethod: () => false,
					};
				}
				return {};
			} );
		} );

		( useDispatch as jest.Mock ).mockReturnValue( {
			__internalSetHasError: jest.fn(),
			__internalProcessCheckoutResponse: jest.fn(),
		} );
	} );

	it( 'renders without crashing', () => {
		render( <CheckoutProcessor /> );
		// Since CheckoutProcessor returns null, we just verify it renders without errors
		expect( document.body ).toBeTruthy();
	} );

	it( 'should not process order when there are validation errors', () => {
		( useSelect as jest.Mock ).mockImplementation( ( selector ) => {
			return selector( ( storeName: string ) => {
				if ( storeName === 'validation' ) {
					return {
						hasValidationErrors: () => true,
						getValidationError: () => 'validation error',
					};
				}
				if ( storeName === 'checkout' ) {
					return {
						isProcessing: () => true,
						isBeforeProcessing: () => false,
						isComplete: () => false,
						hasError: () => false,
						getAdditionalFields: () => ( {} ),
						getCustomerId: () => 0,
						getCustomerPassword: () => '',
						getExtensionData: () => ( {} ),
						getOrderNotes: () => '',
						getRedirectUrl: () => '',
						getShouldCreateAccount: () => false,
					};
				}
				if ( storeName === 'payment' ) {
					return {
						getActivePaymentMethod: () => '',
						getPaymentMethodData: () => ( {} ),
						isExpressPaymentMethodActive: () => false,
						hasPaymentError: () => false,
						isPaymentReady: () => true,
						getShouldSavePaymentMethod: () => false,
					};
				}
				return {};
			} );
		} );

		render( <CheckoutProcessor /> );
		expect( triggerFetch ).not.toHaveBeenCalled();
	} );
} );
