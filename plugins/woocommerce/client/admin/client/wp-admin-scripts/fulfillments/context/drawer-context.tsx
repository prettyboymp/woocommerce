/**
 * External dependencies
 */
import React, { createContext, useState } from 'react';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { Fulfillment, Order } from '../data/types';
import { store as FulfillmentsStore } from '../data/store';

interface FulfillmentDrawerContextProps {
	fulfillments: Fulfillment[];
	setFulfillments: ( fulfillments: Fulfillment[] ) => void;
	order: Order | null;
	setOrder: ( order: Order | null ) => void;
	openSection: string;
	setOpenSection: ( section: string ) => void;
}

const defaultContextProps: FulfillmentDrawerContextProps = {
	fulfillments: [],
	setFulfillments: () => {},
	order: null,
	setOrder: () => {},
	openSection: '',
	setOpenSection: () => {},
};

const FulfillmentDrawerContextValue =
	createContext< FulfillmentDrawerContextProps >( defaultContextProps );

export const useFulfillmentDrawerContext = () => {
	const context = React.useContext( FulfillmentDrawerContextValue );
	if ( ! context ) {
		throw new Error(
			'useFulfillmentDrawerContext must be used within a FulfillmentDrawerProvider'
		);
	}
	return context;
};

export const FulfillmentDrawerProvider = ( {
	orderId,
	children,
}: {
	orderId: number | null;
	children: React.ReactNode;
} ) => {
	const [ openSection, setOpenSection ] = useState( 'order' );
	const [ fulfillments, setFulfillments ] = useState< Fulfillment[] >();
	const [ order, setOrder ] = useState< Order | null >();

	const { isLoading } = useSelect(
		( select ) => {
			if ( ! orderId ) {
				return {
					isLoading: true,
				};
			}
			const store = select( FulfillmentsStore );
			const orderData = store.getOrder( orderId );
			const fulfillmentsData = store.readFulfillments( orderId );
			setOrder( orderData );
			setFulfillments( fulfillmentsData ?? [] );
			return {
				isLoading: store.isLoading( orderId ),
			};
		},
		[ orderId ]
	);

	if ( orderId === null ) {
		return null;
	}

	return (
		<FulfillmentDrawerContextValue.Provider
			value={ {
				fulfillments: fulfillments ?? [],
				setFulfillments,
				order: order ?? null,
				setOrder,
				openSection,
				setOpenSection,
			} }
		>
			{ isLoading ? 'Loading order...' : children }
		</FulfillmentDrawerContextValue.Provider>
	);
};
