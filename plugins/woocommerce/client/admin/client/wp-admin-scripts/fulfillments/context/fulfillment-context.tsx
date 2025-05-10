/**
 * External dependencies
 */
import React, { createContext, useEffect } from 'react';

/**
 * Internal dependencies
 */
import { Fulfillment } from '../data/types';
import { useShipmentFormContext } from './shipment-form-context';
import { ItemQuantity, unspreadItems } from '../utils/order-utils';

const WC_ORDER_CLASS = 'WC_Order';

interface FulfillmentContextProps {
	orderId: number;
	fulfillment: Fulfillment | null;
	setFulfillment: ( fulfillment: Fulfillment | null ) => void;
}

const defaultContextProps: FulfillmentContextProps = {
	orderId: 0,
	fulfillment: null,
	setFulfillment: () => {},
};

const FulfillmentContextValue =
	createContext< FulfillmentContextProps >( defaultContextProps );

export const useFulfillmentContext = () => {
	const context = React.useContext( FulfillmentContextValue );
	if ( ! context ) {
		throw new Error(
			'useFulfillmentContext must be used within a FulfillmentProvider'
		);
	}
	return context;
};

export const FulfillmentProvider = ( {
	orderId,
	fulfillment,
	children,
	selectedItems,
	notifyCustomer,
}: {
	orderId: number;
	fulfillment?: Fulfillment | null;
	selectedItems: Array< ItemQuantity >;
	notifyCustomer: boolean;
	children: React.ReactNode;
} ) => {
	const [ _fulfillment, _setFulfillment ] =
		React.useState< Fulfillment | null >( fulfillment ?? null );
	const { trackingNumber, trackingUrl, shipmentProvider } =
		useShipmentFormContext();

	useEffect( () => {
		if ( ! orderId || ! selectedItems.length ) {
			_setFulfillment( null );
			return;
		}
		_setFulfillment( {
			entity_id: String( orderId ),
			entity_type: WC_ORDER_CLASS,
			is_fulfilled: false,
			status: 'unfulfilled',
			meta_data: [
				{
					id: 0,
					key: 'tracking_number',
					value: trackingNumber,
				},
				{
					id: 0,
					key: 'tracking_url',
					value: trackingUrl,
				},
				{
					id: 0,
					key: 'shipment_provider',
					value: shipmentProvider,
				},
				{
					id: 0,
					key: '_items',
					value: unspreadItems( selectedItems ).map( ( item ) => {
						return {
							item_id: item.item_id,
							qty: item.qty,
						};
					} ),
				},
				{
					id: 0,
					key: '_notify',
					value: notifyCustomer,
				},
			],
		} as Fulfillment );
	}, [
		orderId,
		trackingNumber,
		trackingUrl,
		shipmentProvider,
		selectedItems,
		notifyCustomer,
	] );

	return (
		<FulfillmentContextValue.Provider
			value={ {
				orderId,
				fulfillment: _fulfillment,
				setFulfillment: _setFulfillment,
			} }
		>
			{ children }
		</FulfillmentContextValue.Provider>
	);
};
