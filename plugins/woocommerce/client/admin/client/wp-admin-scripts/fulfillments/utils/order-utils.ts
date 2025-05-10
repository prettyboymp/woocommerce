/**
 * External dependencies
 */

/**
 * Internal dependencies
 */
import { Fulfillment, LineItem, Order } from '../data/types';

/**
 * ItemQuantity interface represents an item with its ID, quantity, and checked status.
 * It is used to manage the items in the order and fulfillments.
 */
export interface ItemQuantity {
	item_id: string;
	item: LineItem;
	qty: number;
	checked: boolean;
}

/**
 * Get the items from the order, with the quantity and checked status.
 *
 * @param order   The order received from the API
 * @param checked Whether the items should be checked or not
 * @return Array<ItemQuantity> The items in the order
 */
export const getItemsFromOrder = (
	order: Order,
	checked = false
): ItemQuantity[] => {
	const items: Array< ItemQuantity > = [];
	order.line_items.forEach( ( item: LineItem ) => {
		items.push( {
			item_id: String( item.id ),
			item,
			qty: item.quantity,
			checked,
		} as ItemQuantity );
	} );
	return items;
};

/**
 * Get the items from the fulfillment, with the quantity and checked status.
 *
 * @param order       The order received from the API
 * @param fulfillment The fulfillment received from the API
 * @param checked     Whether the items should be checked or not
 * @return Array<ItemQuantity> The items in the fulfillment
 */
export const getItemsFromFulfillment = (
	order: Order,
	fulfillment: Fulfillment,
	checked = false
): ItemQuantity[] => {
	const fulfillmentItems = fulfillment.meta_data.find(
		( meta ) => meta.key === '_items'
	)?.value as Array< { item_id: string; qty: number } >;
	return fulfillmentItems.map( ( item ) => {
		const orderItem = order.line_items.find(
			( lineItem ) => String( lineItem.id ) === String( item.item_id )
		);
		return {
			item_id: String( item.item_id ),
			item: orderItem ? orderItem : ( {} as LineItem ),
			qty: item.qty,
			checked,
		} as ItemQuantity;
	} );
};

export const getOrderItemsCount = ( order: Order ): number => {
	return order.line_items.reduce( ( acc, item ) => {
		return acc + item.quantity;
	}, 0 );
};

/**
 * Combine two arrays of items, summing the quantities of items with the same ID.
 *
 * @param items1 The first array of items
 * @param items2 The second array of items
 * @return Array<ItemQuantity> The combined array of items
 */
export const combineItems = (
	items1: ItemQuantity[],
	items2: ItemQuantity[]
): ItemQuantity[] => {
	const itemMap: Record< string, ItemQuantity > = {};
	items1.forEach( ( item ) => {
		itemMap[ item.item_id ] = { ...item } as ItemQuantity;
	} );
	items2.forEach( ( item ) => {
		if ( itemMap[ item.item_id ] ) {
			itemMap[ item.item_id ].qty += item.qty;
		} else {
			itemMap[ item.item_id ] = { ...item } as ItemQuantity;
		}
	} );

	return Object.values( itemMap );
};

/**
 * Reduce the quantities of items in the first array by the quantities of items in the second array.
 * If the quantity of an item in the first array is less than or equal to 0, it is removed from the array.
 *
 * @param items         The first array of items
 * @param itemsToReduce The second array of items
 * @return Array<ItemQuantity> The reduced array of items
 */
export const reduceItems = (
	items: ItemQuantity[],
	itemsToReduce: ItemQuantity[]
): ItemQuantity[] => {
	const itemMap: Record< string, ItemQuantity > = {};
	items.forEach( ( item ) => {
		itemMap[ item.item_id ] = { ...item } as ItemQuantity;
	} );
	itemsToReduce.forEach( ( item ) => {
		if ( itemMap[ item.item_id ] ) {
			itemMap[ item.item_id ].qty -= item.qty;
			if ( itemMap[ item.item_id ].qty <= 0 ) {
				delete itemMap[ item.item_id ];
			}
		}
	} );

	return Object.values( itemMap );
};

/**
 * Check if all items in the array have a quantity of 1.
 *
 * @param items The array of items to check
 * @return boolean True if all items have a quantity of 1, false otherwise
 */
export const areItemsSpread = ( items: ItemQuantity[] ): boolean => {
	return ! items.some( ( item ) => {
		return item.qty > 1;
	} );
};

/**
 * Spread the quantities of items in the array, creating a new item for each quantity.
 * For example, if an item has a quantity of 3, it will be split into 3 items with a quantity of 1.
 *
 * @param items The array of items to spread
 * @return Array<ItemQuantity> The spread array of items
 */
export const spreadItems = ( items: ItemQuantity[] ): ItemQuantity[] => {
	const itemMap: Record< string, ItemQuantity > = {};
	items.forEach( ( item ) => {
		if ( item.qty > 1 ) {
			for ( let i = 0; i < item.qty; i++ ) {
				itemMap[ item.item_id + '-' + i ] = {
					...item,
					item_id: item.item_id + '-' + i,
					qty: 1,
				} as ItemQuantity;
			}
		} else {
			itemMap[ item.item_id ] = {
				...item,
				item_id: item.item_id,
				qty: 1,
			} as ItemQuantity;
		}
	} );

	return Object.values( itemMap );
};

/**
 * Unspread the quantities of items in the array, combining items with the same ID.
 * For example, if an item has a quantity of 3, it will be combined into 1 item with a quantity of 3.
 *
 * @param items The array of items to unspread
 * @return Array<ItemQuantity> The unspread array of items
 */
export const unspreadItems = ( items: ItemQuantity[] ): ItemQuantity[] => {
	const itemMap: Record< string, ItemQuantity > = {};
	items.forEach( ( item ) => {
		const itemId = item.item_id.split( '-' )[ 0 ];
		if ( itemMap[ itemId ] ) {
			itemMap[ itemId ].qty += 1;
		} else {
			itemMap[ itemId ] = {
				...item,
				item_id: itemId,
				qty: 1,
			} as ItemQuantity;
		}
	} );

	return Object.values( itemMap );
};

/**
 * Get the items that are not in any fulfillment.
 * If there are no fulfillments, return all items from the order.
 *
 * @param fulfillments The array of fulfillments
 * @param order        The order received from the API
 * @param checked      Whether the items should be checked or not
 * @return Array<ItemQuantity> The items not in any fulfillment
 */
export const getItemsNotInAnyFulfillment = (
	fulfillments: Fulfillment[],
	order: Order,
	checked = false
): ItemQuantity[] => {
	if ( fulfillments.length === 0 ) {
		return getItemsFromOrder( order );
	}
	const itemsFromFulfillments = fulfillments.reduce( ( acc, fulfillment ) => {
		const items = getItemsFromFulfillment( order, fulfillment, checked );
		return combineItems( acc, items );
	}, [] as ItemQuantity[] );
	const itemsFromOrder = getItemsFromOrder( order, checked );

	return reduceItems( itemsFromOrder, itemsFromFulfillments );
};
