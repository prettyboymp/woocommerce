/**
 * External dependencies
 */
import React, { createContext, useEffect } from 'react';

/**
 * Internal dependencies
 */
import { Fulfillment } from '../data/types';
import { ItemQuantity } from '../utils/order-utils';

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
}: {
	orderId: number;
	fulfillment?: Fulfillment | null;
	selectedItems: Array< ItemQuantity >;
	children: React.ReactNode;
} ) => {
	const [ _fulfillment, _setFulfillment ] =
		React.useState< Fulfillment | null >( fulfillment ?? null );

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
					key: '_items',
					value: selectedItems.map( ( item ) => {
						return {
							item_id: parseInt( item.item_id, 10 ),
							qty: item.qty,
						};
					} ),
				},
			],
		} as Fulfillment );
	}, [ orderId, selectedItems, fulfillment?.id ] );

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
