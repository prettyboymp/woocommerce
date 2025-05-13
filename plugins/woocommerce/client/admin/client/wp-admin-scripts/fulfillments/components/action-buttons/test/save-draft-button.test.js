/**
 * External dependencies
 */
import { render, screen, fireEvent } from '@testing-library/react';
import { useDispatch } from '@wordpress/data';

/**
 * Internal dependencies
 */
import SaveAsDraftButton from '../save-draft-button';
import { useFulfillmentContext } from '../../../context/fulfillment-context';

// Mock dependencies
jest.mock( '@wordpress/data', () => {
	const originalModule = jest.requireActual( '@wordpress/data' );
	return {
		...originalModule,
		useDispatch: jest.fn( () => {} ),
	};
} );

jest.mock( '../../../context/fulfillment-context', () => ( {
	useFulfillmentContext: jest.fn(),
} ) );

describe( 'SaveAsDraftButton component', () => {
	beforeEach( () => {
		// Reset mocks
		jest.clearAllMocks();

		// Default mock implementations
		useDispatch.mockReturnValue( { saveFulfillment: jest.fn() } );
		useFulfillmentContext.mockReturnValue( {
			orderId: 123,
			fulfillment: { id: 456 },
		} );
	} );

	it( 'should render button with correct text', () => {
		render( <SaveAsDraftButton /> );
		expect( screen.getByText( 'Save as draft' ) ).toBeInTheDocument();
	} );

	it( 'should call saveFulfillment when button is clicked', () => {
		const mockSaveFulfillment = jest.fn();
		useDispatch.mockReturnValue( { saveFulfillment: mockSaveFulfillment } );

		const mockFulfillment = { id: 456 };
		useFulfillmentContext.mockReturnValue( {
			orderId: 123,
			fulfillment: mockFulfillment,
		} );

		render( <SaveAsDraftButton /> );
		fireEvent.click( screen.getByText( 'Save as draft' ) );

		expect( mockSaveFulfillment ).toHaveBeenCalledWith(
			123,
			mockFulfillment
		);
	} );

	it( 'should not call saveFulfillment when fulfillment is undefined', () => {
		const mockSaveFulfillment = jest.fn();
		useDispatch.mockReturnValue( { saveFulfillment: mockSaveFulfillment } );

		useFulfillmentContext.mockReturnValue( {
			orderId: 123,
			fulfillment: undefined,
		} );

		render( <SaveAsDraftButton /> );
		fireEvent.click( screen.getByText( 'Save as draft' ) );

		expect( mockSaveFulfillment ).not.toHaveBeenCalled();
	} );
} );
