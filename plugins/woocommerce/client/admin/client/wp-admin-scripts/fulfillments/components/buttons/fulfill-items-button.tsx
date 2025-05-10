/**
 * External dependencies
 */
import { Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useDispatch } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { useFulfillmentContext } from '../../context/fulfillment-context';
import { store as FulfillmentStore } from '../../data/store';

export default function FulfillItemsButton() {
	const { orderId, fulfillment } = useFulfillmentContext();
	const { saveFulfillment } = useDispatch( FulfillmentStore );

	const handleFulfillItems = () => {
		if ( ! fulfillment ) {
			return;
		}
		fulfillment.is_fulfilled = true;
		saveFulfillment( orderId, fulfillment );
	};

	return (
		<Button variant="primary" onClick={ handleFulfillItems }>
			{ __( 'Fulfill items', 'woocommerce' ) }
		</Button>
	);
}
