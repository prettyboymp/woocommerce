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

export default function UpdateButton() {
	const { orderId, fulfillment } = useFulfillmentContext();
	const { updateFulfillment } = useDispatch( FulfillmentStore );

	const handleUpdateFulfillment = () => {
		if ( ! fulfillment ) {
			return;
		}
		updateFulfillment( orderId, fulfillment );
	};

	return (
		<Button variant="primary" onClick={ handleUpdateFulfillment }>
			{ __( 'Update', 'woocommerce' ) }
		</Button>
	);
}
