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
	if ( ! isOpen ) return null;

	return (
		<div className="woocommerce-fulfillment-drawer">
			<div className="drawer-panel">
				<div className="drawer-header">
					<div className="drawer-header-title">
						<h2>#{ orderId } Michael Jones</h2>
						<button className="close-button" onClick={ onClose }>
							×
						</button>
					</div>
					<p>February 19, 2020, 6:22pm</p>
				</div>
				{ /* Custom content goes here */ }
			</div>
		</div>
	);
};

export default FulfillmentDrawer;
