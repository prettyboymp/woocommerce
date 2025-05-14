/**
 * External dependencies
 */
import React, { createContext } from 'react';

/**
 * Internal dependencies
 */
import { Fulfillment } from '../data/types';
import { getFulfillmentMeta } from '../utils/fulfillment-utils';

interface ShipmentFormContextProps {
	trackingNumber: string;
	setTrackingNumber: ( trackingNumber: string ) => void;
	shipmentProvider: string;
	setShipmentProvider: ( shipmentProvider: string ) => void;
	trackingUrl: string;
	setTrackingUrl: ( trackingUrl: string ) => void;
	providerName: string;
	setProviderName: ( providerName: string ) => void;
}

const defaultContextProps: ShipmentFormContextProps = {
	trackingNumber: '',
	setTrackingNumber: () => {},
	shipmentProvider: '',
	setShipmentProvider: () => {},
	trackingUrl: '',
	setTrackingUrl: () => {},
	providerName: '',
	setProviderName: () => {},
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
	fulfillment = null,
	children,
}: {
	fulfillment?: Fulfillment | null;
	children: React.ReactNode;
} ) => {
	const [ trackingNumber, setTrackingNumber ] = React.useState(
		getFulfillmentMeta( fulfillment, '_tracking_number', '' )
	);
	const [ shipmentProvider, setShipmentProvider ] = React.useState(
		getFulfillmentMeta( fulfillment, '_shipment_provider', '' )
	);
	const [ trackingUrl, setTrackingUrl ] = React.useState(
		getFulfillmentMeta( fulfillment, '_tracking_url', '' )
	);

	const [ providerName, setProviderName ] = React.useState(
		getFulfillmentMeta( fulfillment, '_provider_name', '' )
	);

	return (
		<ShipmentFormContextValue.Provider
			value={ {
				trackingNumber,
				setTrackingNumber,
				shipmentProvider,
				setShipmentProvider,
				trackingUrl,
				setTrackingUrl,
				providerName,
				setProviderName,
			} }
		>
			{ children }
		</ShipmentFormContextValue.Provider>
	);
};
