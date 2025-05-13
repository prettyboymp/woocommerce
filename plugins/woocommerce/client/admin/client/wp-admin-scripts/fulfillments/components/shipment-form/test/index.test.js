/**
 * External dependencies
 */
import { fireEvent, render, screen } from '@testing-library/react';

/**
 * Internal dependencies
 */
import ShipmentForm from '../index';

// Mock subcomponents
jest.mock( '../shipment-tracking-number-form', () => () => (
	<div data-testid="tracking-number-form">Tracking Number Form</div>
) );
jest.mock( '../shipment-manual-entry-form', () => () => (
	<div data-testid="manual-entry-form">Manual Entry Form</div>
) );
jest.mock( '../../user-interface/fulfillments-card/card', () => ( {
	__esModule: true,
	default: ( { header, children } ) => (
		<div data-testid="fulfillment-card">
			<div data-testid="card-header">{ header }</div>
			<div data-testid="card-body">{ children }</div>
		</div>
	),
} ) );
jest.mock( '../../../utils/icons', () => ( {
	TruckIcon: () => <span data-testid="truck-icon" />,
} ) );
jest.mock( '@wordpress/components', () => ( {
	...jest.requireActual( '@wordpress/components' ),
	CheckboxControl: ( { label, checked, onChange } ) => {
		const randomId = Math.random();
		return (
			<div data-testid="checkbox-control">
				<label htmlFor={ 'checkbox-' + randomId }>
					<input
						id={ 'checkbox-' + randomId }
						type="radio"
						checked={ checked }
						onChange={ () => onChange( ! checked ) }
					/>
					{ label }
				</label>
			</div>
		);
	},
} ) );

describe( 'ShipmentForm', () => {
	it( 'renders the header and icon', () => {
		render( <ShipmentForm /> );
		expect( screen.getByTestId( 'card-header' ) ).toHaveTextContent(
			'Shipment Information'
		);
		expect( screen.getByTestId( 'truck-icon' ) ).toBeInTheDocument();
	} );

	it( 'renders the tracking number form by default', () => {
		render( <ShipmentForm /> );
		expect(
			screen.getByTestId( 'tracking-number-form' )
		).toBeInTheDocument();
	} );

	it( 'switches to manual entry form when the corresponding radio is selected', () => {
		render( <ShipmentForm /> );
		fireEvent.click( screen.getByLabelText( 'Enter manually' ) );
		expect( screen.getByTestId( 'manual-entry-form' ) ).toBeInTheDocument();
		expect(
			screen.queryByTestId( 'tracking-number-form' )
		).not.toBeInTheDocument();
	} );

	it( 'switches to no shipment information when the corresponding radio is selected', () => {
		render( <ShipmentForm /> );
		fireEvent.click( screen.getByLabelText( 'No shipment information' ) );
		expect(
			screen.queryByTestId( 'tracking-number-form' )
		).not.toBeInTheDocument();
		expect(
			screen.queryByTestId( 'manual-entry-form' )
		).not.toBeInTheDocument();
	} );
} );
