/**
 * External dependencies
 */
import React from 'react';

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
				<div className="drawer-header">
					<div className="drawer-header-title">
						<h2>#{ orderId } Michael Jones</h2>
						<button className="close-button" onClick={ onClose }>
							×
						</button>
					</div>
					<p>February 19, 2020, 6:22pm</p>
				</div>
				<div className="drawer-content">
					{ /* TODO: Add content here */ }
				</div>
			</div>
		</div>
	);
};

export default FulfillmentDrawer;
