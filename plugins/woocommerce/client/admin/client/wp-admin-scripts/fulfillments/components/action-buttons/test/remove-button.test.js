/**
 * External dependencies
 */
import { render, screen, fireEvent } from '@testing-library/react';
import { useDispatch } from '@wordpress/data';

/**
 * Internal dependencies
 */
import RemoveButton from '../remove-button';
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

describe( 'RemoveButton component', () => {
	beforeEach( () => {
		// Reset mocks
		jest.clearAllMocks();

		// Default mock implementations
		useDispatch.mockReturnValue( { deleteFulfillment: jest.fn() } );
		useFulfillmentContext.mockReturnValue( {
			orderId: 123,
			fulfillment: { id: 456 },
		} );
	} );

	it( 'should render button with correct text', () => {
		render( <RemoveButton /> );
		expect( screen.getByText( 'Remove' ) ).toBeInTheDocument();
	} );

	it( 'should call deleteFulfillment when button is clicked', () => {
		const mockDeleteFulfillment = jest.fn();
		useDispatch.mockReturnValue( {
			deleteFulfillment: mockDeleteFulfillment,
		} );

		useFulfillmentContext.mockReturnValue( {
			orderId: 123,
			fulfillment: { id: 456 },
		} );

		render( <RemoveButton /> );
		fireEvent.click( screen.getByText( 'Remove' ) );

		expect( mockDeleteFulfillment ).toHaveBeenCalledWith( 123, 456 );
	} );

	it( 'should not call deleteFulfillment when fulfillment is undefined', () => {
		const mockDeleteFulfillment = jest.fn();
		useDispatch.mockReturnValue( {
			deleteFulfillment: mockDeleteFulfillment,
		} );

		useFulfillmentContext.mockReturnValue( {
			orderId: 123,
			fulfillment: undefined,
		} );

		render( <RemoveButton /> );
		fireEvent.click( screen.getByText( 'Remove' ) );

		expect( mockDeleteFulfillment ).not.toHaveBeenCalled();
	} );

	it( 'should not call deleteFulfillment when fulfillment has no id', () => {
		const mockDeleteFulfillment = jest.fn();
		useDispatch.mockReturnValue( {
			deleteFulfillment: mockDeleteFulfillment,
		} );

		useFulfillmentContext.mockReturnValue( {
			orderId: 123,
			fulfillment: {
				/* no id */
			},
		} );

		render( <RemoveButton /> );
		fireEvent.click( screen.getByText( 'Remove' ) );

		expect( mockDeleteFulfillment ).not.toHaveBeenCalled();
	} );
} );
