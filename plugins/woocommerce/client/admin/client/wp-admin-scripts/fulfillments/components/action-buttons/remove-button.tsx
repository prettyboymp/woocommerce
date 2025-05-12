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

export default function RemoveButton() {
	const { orderId, fulfillment } = useFulfillmentContext();
	const { deleteFulfillment } = useDispatch( FulfillmentStore );

	const handleFulfillItems = () => {
		if ( ! fulfillment || ! fulfillment.id ) {
			return;
		}
		deleteFulfillment( orderId, fulfillment.id );
	};

	return (
		<Button
			variant="secondary"
			onClick={ handleFulfillItems }
			__next40pxDefaultSize
		>
			{ __( 'Remove', 'woocommerce' ) }
		</Button>
	);
}
