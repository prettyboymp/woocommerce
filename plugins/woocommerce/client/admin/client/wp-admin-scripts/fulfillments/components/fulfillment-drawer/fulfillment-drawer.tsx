/**
 * External dependencies
 */
import React from 'react';
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import NewFulfillmentForm from '../fulfillments/new-fulfillment-form';
import { ErrorBoundary } from '~/error-boundary';
import { ShipmentFormProvider } from '../../context/shipment-form-context';
import FulfillmentsList from '../fulfillments/fulfillments-list';
import FulfillmentsDrawerHeader from './fulfillment-drawer-header';
import { store as FulfillmentsStore } from '../../data/store';

interface Props {
	isOpen: boolean;
	onClose: () => void;
	orderId: number | null;
}

const FulfillmentDrawer: React.FC< Props > = ( {
	isOpen,
	onClose,
	orderId,
} ) => {
	const { order, fulfillments, isLoading } = useSelect(
		( select ) => {
			if ( ! orderId ) {
				return {
					order: null,
					isLoading: false,
				};
			}
			const store = select( FulfillmentsStore );
			return {
				order: store.getOrder( orderId ),
				fulfillments: store.readFulfillments( orderId ),
				isLoading: store.isLoading( orderId ),
			};
		},
		[ orderId ]
	);

	if ( orderId === null ) {
		return null;
	}

	return (
		<div className="woocommerce-fulfillment-drawer">
			<div
				className={ [
					'drawer-panel',
					isOpen ? 'is-open' : 'is-closed',
				].join( ' ' ) }
			>
				{ ! isLoading && order && (
					<ErrorBoundary>
						<ShipmentFormProvider>
							<FulfillmentsDrawerHeader
								order={ order }
								onClose={ onClose }
							/>
							<NewFulfillmentForm
								order={ order }
								fulfillments={ fulfillments }
							/>
						</ShipmentFormProvider>
						<FulfillmentsList orderId={ orderId } />
					</ErrorBoundary>
				) }
			</div>
		</div>
	);
};

export default FulfillmentDrawer;
