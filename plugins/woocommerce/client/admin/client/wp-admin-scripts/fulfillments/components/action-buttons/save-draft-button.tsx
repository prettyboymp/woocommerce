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

export default function SaveAsDraftButton() {
	const { orderId, fulfillment } = useFulfillmentContext();
	const { saveFulfillment } = useDispatch( FulfillmentStore );

	const handleFulfillItems = () => {
		if ( ! fulfillment ) {
			return;
		}
		saveFulfillment( orderId, fulfillment );
	};

	return (
		<Button
			variant="secondary"
			onClick={ handleFulfillItems }
			__next40pxDefaultSize
		>
			{ __( 'Save as draft', 'woocommerce' ) }
		</Button>
	);
}
