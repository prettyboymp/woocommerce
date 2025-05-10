/**
 * External dependencies
 */
import { useState } from 'react';
import { select, subscribe } from '@wordpress/data';
import { isEqual } from 'lodash';

/**
 * Internal dependencies
 */
import { store as FulfillmentStore } from '../../data/store';
import { Fulfillment, Order } from '../../data/types';
import FulfillmentEditor from './fulfillment-editor';

export default function FulfillmentsList( { orderId }: { orderId: number } ) {
	const [ fulfillments, setFulfillments ] = useState< Fulfillment[] >( [] );
	const [ order, setOrder ] = useState< Order | null >( null );
	const [ selectedFulfillmentId, setSelectedFulfillmentId ] = useState<
		number | null
	>( null );

	// Fetch fulfillments from the store
	subscribe( () => {
		if ( ! orderId ) {
			return;
		}
		if ( ! order ) {
			const newOrder = select( FulfillmentStore ).getOrder( orderId );
			if ( newOrder ) {
				setOrder( newOrder );
			}
		}
		const newFulfillments =
			select( FulfillmentStore ).readFulfillments( orderId );
		if ( ! isEqual( newFulfillments, fulfillments ) ) {
			setFulfillments( newFulfillments );
		}
	} );

	return (
		order && (
			<div className="woocommerce-fulfillment-stored-fulfillments-list">
				{ fulfillments.map( ( fulfillment, index ) => (
					<FulfillmentEditor
						index={ index }
						expanded={ selectedFulfillmentId === fulfillment.id }
						onExpand={ () =>
							setSelectedFulfillmentId( fulfillment.id ?? null )
						}
						onCollapse={ () => setSelectedFulfillmentId( null ) }
						key={ fulfillment.id }
						order={ order }
						fulfillment={ fulfillment }
						fulfillments={ fulfillments }
					/>
				) ) }
			</div>
		)
	);
}
