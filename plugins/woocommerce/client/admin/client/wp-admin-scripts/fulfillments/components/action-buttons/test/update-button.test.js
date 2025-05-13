/**
 * External dependencies
 */
import { render, screen, fireEvent } from '@testing-library/react';
import { useDispatch } from '@wordpress/data';

/**
 * Internal dependencies
 */
import UpdateButton from '../update-button';
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

describe( 'UpdateButton component', () => {
	beforeEach( () => {
		// Reset mocks
		jest.clearAllMocks();

		// Default mock implementations
		useDispatch.mockReturnValue( { updateFulfillment: jest.fn() } );
		useFulfillmentContext.mockReturnValue( {
			orderId: 123,
			fulfillment: { id: 456 },
		} );
	} );

	it( 'should render button with correct text', () => {
		render( <UpdateButton /> );
		expect( screen.getByText( 'Update' ) ).toBeInTheDocument();
	} );

	it( 'should call updateFulfillment when button is clicked', () => {
		const mockUpdateFulfillment = jest.fn();
		useDispatch.mockReturnValue( {
			updateFulfillment: mockUpdateFulfillment,
		} );

		const mockFulfillment = { id: 456 };
		useFulfillmentContext.mockReturnValue( {
			orderId: 123,
			fulfillment: mockFulfillment,
		} );

		render( <UpdateButton /> );
		fireEvent.click( screen.getByText( 'Update' ) );

		expect( mockUpdateFulfillment ).toHaveBeenCalledWith(
			123,
			mockFulfillment
		);
	} );

	it( 'should not call updateFulfillment when fulfillment is undefined', () => {
		const mockUpdateFulfillment = jest.fn();
		useDispatch.mockReturnValue( {
			updateFulfillment: mockUpdateFulfillment,
		} );

		useFulfillmentContext.mockReturnValue( {
			orderId: 123,
			fulfillment: undefined,
		} );

		render( <UpdateButton /> );
		fireEvent.click( screen.getByText( 'Update' ) );

		expect( mockUpdateFulfillment ).not.toHaveBeenCalled();
	} );
} );
