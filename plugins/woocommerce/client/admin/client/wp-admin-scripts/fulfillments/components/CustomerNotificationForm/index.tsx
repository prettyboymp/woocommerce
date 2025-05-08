/**
 * External dependencies
 */
import { ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useState } from 'react';

/**
 * Internal dependencies
 */

const EnvelopeIcon = () => (
	<svg
		width="18"
		height="14"
		viewBox="0 0 18 14"
		fill="none"
		xmlns="http://www.w3.org/2000/svg"
	>
		<path
			fillRule="evenodd"
			clipRule="evenodd"
			d="M0 2C0 0.89543 0.895431 0 2 0H16C17.1046 0 18 0.895431 18 2V12C18 13.1046 17.1046 14 16 14H2C0.89543 14 0 13.1046 0 12V2ZM2 1.5H16C16.2761 1.5 16.5 1.72386 16.5 2V2.93754L9.00005 8.5625L1.5 2.93746V2C1.5 1.72386 1.72386 1.5 2 1.5ZM1.5 4.81246V12C1.5 12.2761 1.72386 12.5 2 12.5H16C16.2761 12.5 16.5 12.2761 16.5 12V4.81254L9.00005 10.4375L1.5 4.81246Z"
			fill="#1E1E1E"
		/>
	</svg>
);

export default function CustomerNotificationBox() {
	const [ isChecked, setChecked ] = useState( true );
	return (
		<div className="woocommerce-fulfillment-notification-form">
			<div className="woocommerce-fulfillment-notification-form-header">
				<EnvelopeIcon />
				<h3>{ __( 'Customer notification', 'woocommerce' ) }</h3>
				<ToggleControl
					__nextHasNoMarginBottom
					checked={ isChecked }
					label={ null }
					onChange={ ( value ) => {
						setChecked( value );
					} }
				/>
			</div>
			<div className="woocommerce-fulfillment-notification-form-content">
				{ __(
					'Automatically send an email to the customer when the selected items are fulfilled.',
					'woocommerce'
				) }
			</div>
		</div>
	);
}
