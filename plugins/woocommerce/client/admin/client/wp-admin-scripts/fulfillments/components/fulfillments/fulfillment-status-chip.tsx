/**
 * Internal dependencies
 */
import { Fulfillment } from '../../data/types';

export default function FulfillmentStatusChip( {
	fulfillment,
}: {
	fulfillment: Fulfillment;
} ) {
	return (
		<div
			className={ `woocommerce-fulfillment-status-badge wocommerce-fulfillment-status-badge-${ fulfillment.status }` }
		>
			{ /* TODO: Find a way to convert this to a human readable string. */ }
			{ fulfillment.status }
		</div>
	);
}
