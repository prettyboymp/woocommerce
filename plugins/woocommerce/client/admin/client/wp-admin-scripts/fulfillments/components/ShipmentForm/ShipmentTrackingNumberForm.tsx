/**
 * External dependencies
 */

import { Button, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { useShipmentFormContext } from '../../context/ShipmentFormContext';

export default function ShipmentTrackingNumberForm() {
	const { trackingNumber, setTrackingNumber } = useShipmentFormContext();
	return (
		<>
			<p className="woocommerce-fulfillment-description">
				{ __(
					'Provide the shipment tracking number to find the shipment provider and tracking URL.',
					'woocommerce'
				) }
			</p>
			<div className="woocommerce-fulfillment-input-container">
				<h4>{ __( 'Tracking Number', 'woocommerce' ) }</h4>
				<div className="woocommerce-fulfillment-input-group">
					<TextControl
						type="text"
						placeholder={ __(
							'Enter tracking number',
							'woocommerce'
						) }
						value={ trackingNumber }
						onChange={ ( value ) => {
							setTrackingNumber( value );
						} }
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
					<Button variant="secondary" text="Find info" />
				</div>
			</div>
		</>
	);
}
