/**
 * External dependencies
 */
import React, { createContext } from 'react';

/**
 * Internal dependencies
 */
import { LineItem } from '../data/types';

interface FulfillmentFormContextProps {
	items: LineItem[];
	setItems: ( items: LineItem[] ) => void;
	selectedItems: ItemState[];
	toggleItem: ( key: string, checked: boolean ) => void;
	clearSelectedItems: () => void;
	selectAllItems: () => void;
}

const defaultContextProps: FulfillmentFormContextProps = {
	items: [],
	setItems: () => {},
	selectedItems: [],
	toggleItem: () => {},
	clearSelectedItems: () => {},
	selectAllItems: () => {},
};

const FulfillmentFormContextValue =
	createContext< FulfillmentFormContextProps >( defaultContextProps );

export const useFulfillmentFormContext = () => {
	const context = React.useContext( FulfillmentFormContextValue );
	if ( ! context ) {
		throw new Error(
			'useFulfillmentFormContext must be used within a FulfillmentFormProvider'
		);
	}
	return context;
};

type ItemState = {
	id: string;
	item: LineItem;
	checked: boolean;
};

export const FulfillmentFormProvider = ( {
	children,
}: {
	children: React.ReactNode;
} ) => {
	const [ itemsBuffer, setItemsBuffer ] = React.useState< LineItem[] >( [] );
	const [ itemsMap, setItemsMap ] = React.useState<
		Record< string, ItemState >
	>( {} );

	const items = React.useMemo< LineItem[] >( () => {
		return itemsBuffer;
	}, [ itemsBuffer ] );

	const setItems = React.useCallback( ( newItems: LineItem[] ) => {
		setItemsBuffer( newItems );
		newItems.map( ( item ) => {
			if ( item.quantity > 1 ) {
				for ( let i = 0; i < item.quantity; i++ ) {
					const itemState: ItemState = {
						id: `${ item.id }-${ i }`,
						item,
						checked: true,
					};
					setItemsMap( ( prev ) => ( {
						...prev,
						[ itemState.id ]: itemState,
					} ) );
				}
			} else {
				const itemState: ItemState = {
					id: String( item.id ),
					item,
					checked: true,
				};
				setItemsMap( ( prev ) => ( {
					...prev,
					[ item.id ]: itemState,
				} ) );
			}
			return item;
		} );
	}, [] );

	const toggleItem = ( key: string, checked: boolean ) => {
		const newItemsMap = { ...itemsMap };
		if ( newItemsMap[ key ] ) {
			newItemsMap[ key ].checked = checked;
		} else {
			const matchingKeys = Object.keys( itemsMap ).filter( ( mapKey ) =>
				itemsMap[ mapKey ].id.startsWith( key + '-' )
			);
			if ( matchingKeys.length > 0 ) {
				for ( let i = 0; i < matchingKeys.length; i++ ) {
					newItemsMap[ matchingKeys[ i ] ] = {
						id: matchingKeys[ i ],
						item: itemsMap[ matchingKeys[ i ] ].item,
						checked,
					};
				}
			}
		}
		setItemsMap( newItemsMap );
	};

	const clearSelectedItems = () => {
		const newItemsMap = { ...itemsMap };
		Object.keys( newItemsMap ).forEach( ( key ) => {
			newItemsMap[ key ].checked = false;
		} );
		setItemsMap( newItemsMap );
	};

	const selectAllItems = () => {
		const newItemsMap = { ...itemsMap };
		Object.keys( newItemsMap ).forEach( ( key ) => {
			newItemsMap[ key ].checked = true;
		} );
		setItemsMap( newItemsMap );
	};

	const selectedItems = React.useMemo( () => {
		return Object.values( itemsMap ).filter( ( item ) => item.checked );
	}, [ itemsMap ] );

	return (
		<FulfillmentFormContextValue.Provider
			value={ {
				items,
				setItems,
				selectedItems,
				toggleItem,
				clearSelectedItems,
				selectAllItems,
			} }
		>
			{ children }
		</FulfillmentFormContextValue.Provider>
	);
};
