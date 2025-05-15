/**
 * External dependencies
 */
import { render } from '@testing-library/react';
import { useSelect, useDispatch } from '@wordpress/data';
import triggerFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
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
		// Mock implementation returns a no-op function as we don't need to test the actual event handling
		// eslint-disable-next-line @typescript-eslint/no-empty-function
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

type StoreSelectors = {
	checkout: {
		getAdditionalFields: () => Record< string, unknown >;
		getCustomerId: () => number;
		getCustomerPassword: () => string;
		getExtensionData: () => Record< string, unknown >;
		hasError: () => boolean;
		isBeforeProcessing: () => boolean;
		isComplete: () => boolean;
		isProcessing: () => boolean;
		getOrderNotes: () => string;
		getRedirectUrl: () => string;
		getShouldCreateAccount: () => boolean;
	};
	validation: {
		hasValidationErrors: () => boolean;
		getValidationError: () => string | undefined;
	};
	payment: {
		getActivePaymentMethod: () => string;
		getPaymentMethodData: () => Record< string, unknown >;
		isExpressPaymentMethodActive: () => boolean;
		hasPaymentError: () => boolean;
		isPaymentReady: () => boolean;
		getShouldSavePaymentMethod: () => boolean;
	};
};

describe( 'CheckoutProcessor', () => {
	const defaultStoreSelectors: StoreSelectors = {
		checkout: {
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
		},
		validation: {
			hasValidationErrors: () => false,
			getValidationError: () => undefined,
		},
		payment: {
			getActivePaymentMethod: () => '',
			getPaymentMethodData: () => ( {} ),
			isExpressPaymentMethodActive: () => false,
			hasPaymentError: () => false,
			isPaymentReady: () => true,
			getShouldSavePaymentMethod: () => false,
		},
	};

	beforeEach( () => {
		// Reset all mocks before each test
		jest.clearAllMocks();

		// Setup default mock implementations
		( useSelect as jest.Mock ).mockImplementation( ( selector ) => {
			return selector( ( storeName: keyof StoreSelectors ) => {
				return defaultStoreSelectors[ storeName ] || {};
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
			return selector( ( storeName: keyof StoreSelectors ) => {
				if ( storeName === 'validation' ) {
					return {
						...defaultStoreSelectors.validation,
						hasValidationErrors: () => true,
						getValidationError: () => 'validation error',
					};
				}
				if ( storeName === 'checkout' ) {
					return {
						...defaultStoreSelectors.checkout,
						isProcessing: () => true,
					};
				}
				return defaultStoreSelectors[ storeName ] || {};
			} );
		} );

		render( <CheckoutProcessor /> );
		expect( triggerFetch ).not.toHaveBeenCalled();
	} );
} );
