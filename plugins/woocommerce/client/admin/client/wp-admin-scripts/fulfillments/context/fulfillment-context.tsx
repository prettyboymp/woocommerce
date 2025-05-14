/**
 * External dependencies
 */
import React, { createContext, useEffect } from 'react';

/**
 * Internal dependencies
 */
import { Fulfillment } from '../data/types';
import { useShipmentFormContext } from './shipment-form-context';
import { ItemQuantity } from '../utils/order-utils';
import {
	ITEMS_META_KEY,
	PROVIDER_NAME_META_KEY,
	SHIPMENT_OPTION_NO_INFO,
	SHIPMENT_PROVIDER_META_KEY,
	SHIPPING_OPTION_META_KEY,
	TRACKING_NUMBER_META_KEY,
	TRACKING_URL_META_KEY,
	WC_ORDER_CLASS,
} from '../data/constants';

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
}: {
	orderId: number;
	fulfillment?: Fulfillment | null;
	selectedItems: Array< ItemQuantity >;
	children: React.ReactNode;
} ) => {
	const [ _fulfillment, _setFulfillment ] =
		React.useState< Fulfillment | null >( fulfillment ?? null );
	const {
		selectedOption,
		trackingNumber,
		trackingUrl,
		shipmentProvider,
		providerName,
	} = useShipmentFormContext();

	useEffect( () => {
		if ( ! orderId ) {
			_setFulfillment( null );
			return;
		}
		_setFulfillment( {
			id: fulfillment?.id ?? undefined,
			fulfillment_id: fulfillment?.id ?? undefined,
			entity_id: String( orderId ),
			entity_type: WC_ORDER_CLASS,
			is_fulfilled: false,
			status: 'unfulfilled',
			meta_data: [
				{
					id: 0,
					key: SHIPPING_OPTION_META_KEY,
					value: selectedOption,
				},
				{
					id: 0,
					key: TRACKING_NUMBER_META_KEY,
					value:
						selectedOption === SHIPMENT_OPTION_NO_INFO
							? ''
							: trackingNumber,
				},
				{
					id: 0,
					key: TRACKING_URL_META_KEY,
					value:
						selectedOption === SHIPMENT_OPTION_NO_INFO
							? ''
							: trackingUrl,
				},
				{
					id: 0,
					key: SHIPMENT_PROVIDER_META_KEY,
					value:
						selectedOption === SHIPMENT_OPTION_NO_INFO
							? ''
							: shipmentProvider,
				},
				{
					id: 0,
					key: PROVIDER_NAME_META_KEY,
					value:
						selectedOption === SHIPMENT_OPTION_NO_INFO
							? ''
							: providerName,
				},
				{
					id: 0,
					key: ITEMS_META_KEY,
					value: selectedItems.map( ( item ) => {
						return {
							item_id: parseInt( item.item_id, 10 ),
							qty: item.qty,
						};
					} ),
				},
			],
		} as Fulfillment );
	}, [
		orderId,
		trackingNumber,
		trackingUrl,
		shipmentProvider,
		providerName,
		selectedOption,
		fulfillment?.id,
		selectedItems,
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
