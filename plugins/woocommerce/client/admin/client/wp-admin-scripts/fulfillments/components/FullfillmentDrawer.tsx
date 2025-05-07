/**
 * External dependencies
 */
import React from 'react';

/**
 * Internal dependencies
 */
import { FulfillmentFormProvider } from '../context/FulfillmentFormContext';
import FulfillmentForm from './FulfillmentForm';

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
	return (
		<div className="woocommerce-fulfillment-drawer">
			<div
				className={ [
					'drawer-panel',
					isOpen ? 'is-open' : 'is-closed',
				].join( ' ' ) }
			>
				{ orderId && (
					<FulfillmentFormProvider>
						<FulfillmentForm
							orderId={ orderId }
							onClose={ onClose }
						/>
					</FulfillmentFormProvider>
				) }
			</div>
		</div>
	);
};

export default FulfillmentDrawer;
