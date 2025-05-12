/**
 * External dependencies
 */
import { ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import FulfillmentCard from '../user-interface/fulfillments-card/card';
import { EnvelopeIcon } from '../../utils/icons';

/**
 * Internal dependencies
 */

export default function CustomerNotificationBox( {
	value,
	setValue,
}: {
	value: boolean;
	setValue: ( value: boolean ) => void;
} ) {
	return (
		<FulfillmentCard
			size="small"
			header={
				<>
					<EnvelopeIcon />
					<h3>{ __( 'Customer notification', 'woocommerce' ) }</h3>
					<ToggleControl
						__nextHasNoMarginBottom
						checked={ value }
						label={ null }
						onChange={ ( checked ) => {
							setValue( checked );
						} }
					/>
				</>
			}
		>
			<p className="woocommerce-fulfillment-description-sm">
				{ __(
					'Automatically send an email to the customer when the selected items are fulfilled.',
					'woocommerce'
				) }
			</p>
		</FulfillmentCard>
	);
}
