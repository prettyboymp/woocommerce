/**
 * External dependencies
 */
import React from 'react';

/**
 * Internal dependencies
 */
import { ErrorBoundary } from '~/error-boundary';
import FulfillmentsDrawerHeader from './fulfillment-drawer-header';

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
				<ErrorBoundary>
					<FulfillmentsDrawerHeader
						orderId={ orderId }
						onClose={ onClose }
					/>
					<div className="drawer-content">
						{ /* TODO: Add content here */ }
					</div>
				</ErrorBoundary>
			</div>
		</div>
	);
};

export default FulfillmentDrawer;
