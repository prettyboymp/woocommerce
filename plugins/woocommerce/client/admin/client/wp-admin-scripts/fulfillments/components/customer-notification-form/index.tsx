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
import { useFulfillmentContext } from '../../context/fulfillment-context';

/**
 * Internal dependencies
 */

export default function CustomerNotificationBox( {
	isUpdate = false,
}: {
	isUpdate?: boolean;
} ) {
	const { notifyCustomer, setNotifyCustomer } = useFulfillmentContext();

	return (
		<FulfillmentCard
			size="small"
			isCollapsable={ false }
			initialState="expanded"
			header={
				<>
					<EnvelopeIcon />
					<h3>
						{ isUpdate
							? __( 'Update notification', 'woocommerce' )
							: __( 'Fulfillment notification', 'woocommerce' ) }
					</h3>
					<ToggleControl
						__nextHasNoMarginBottom
						checked={ notifyCustomer }
						label={ null }
						onChange={ ( checked ) => {
							setNotifyCustomer( checked );
						} }
					/>
				</>
			}
		>
			<p className="woocommerce-fulfillment-description">
				{ isUpdate
					? __(
							'Automatically send an email to the customer when the fulfillment is updated.',
							'woocommerce'
					  )
					: __(
							'Automatically send an email to the customer when the selected items are fulfilled.',
							'woocommerce'
					  ) }
			</p>
		</FulfillmentCard>
	);
}
