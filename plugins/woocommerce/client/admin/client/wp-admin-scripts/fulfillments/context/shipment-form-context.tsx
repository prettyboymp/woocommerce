/**
 * External dependencies
 */
import React, { createContext } from 'react';

interface ShipmentFormContextProps {
	trackingNumber: string;
	setTrackingNumber: ( trackingNumber: string ) => void;
	shipmentProvider: string;
	setShipmentProvider: ( shipmentProvider: string ) => void;
	trackingUrl: string;
	setTrackingUrl: ( trackingUrl: string ) => void;
}

const defaultContextProps: ShipmentFormContextProps = {
	trackingNumber: '',
	setTrackingNumber: () => {},
	shipmentProvider: '',
	setShipmentProvider: () => {},
	trackingUrl: '',
	setTrackingUrl: () => {},
};

const ShipmentFormContextValue =
	createContext< ShipmentFormContextProps >( defaultContextProps );

export const useShipmentFormContext = () => {
	const context = React.useContext( ShipmentFormContextValue );
	if ( ! context ) {
		throw new Error(
			'useShipmentFormContext must be used within a ShipmentFormProvider'
		);
	}
	return context;
};

export const ShipmentFormProvider = ( {
	children,
}: {
	children: React.ReactNode;
} ) => {
	const [ trackingNumber, setTrackingNumber ] = React.useState( '' );
	const [ shipmentProvider, setShipmentProvider ] = React.useState( '' );
	const [ trackingUrl, setTrackingUrl ] = React.useState( '' );

	return (
		<ShipmentFormContextValue.Provider
			value={ {
				trackingNumber,
				setTrackingNumber,
				shipmentProvider,
				setShipmentProvider,
				trackingUrl,
				setTrackingUrl,
			} }
		>
			{ children }
		</ShipmentFormContextValue.Provider>
	);
};
