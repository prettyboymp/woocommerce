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
	setItems: React.Dispatch< React.SetStateAction< LineItem[] > >;
	selectedItems: LineItem[];
	toggleItem: ( item: LineItem ) => void;
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

export const FulfillmentFormProvider = ( {
	children,
}: {
	children: React.ReactNode;
} ) => {
	const [ items, setItems ] = React.useState< LineItem[] >( [] );
	const [ selectedItems, setSelectedItems ] = React.useState< LineItem[] >(
		[]
	);

	const toggleItem = ( item: LineItem ) => {
		setSelectedItems( ( prevItems ) => {
			if ( prevItems.includes( item ) ) {
				return prevItems.filter( ( i ) => i !== item );
			}
			return [ ...prevItems, item ];
		} );
	};

	const clearSelectedItems = () => {
		setSelectedItems( [] );
	};

	const selectAllItems = () => {
		setSelectedItems( items );
	};

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
