/**
 * External dependencies
 */
import moment from 'moment';
import { LoadingPlaceholder } from '@automattic/components';

/**
 * Internal dependencies
 */
import { Order } from '../../data/types';

export default function FulfillmentsDrawerHeader( {
	order,
	onClose,
}: {
	order: Order;
	onClose: () => void;
} ) {
	if ( ! order ) {
		return <LoadingPlaceholder />;
	}

	return (
		order && (
			<div className="drawer-header">
				<div className="drawer-header-title">
					<h2>
						#{ order.id }{ ' ' }
						{ order.billing.first_name +
							' ' +
							order.billing.last_name }
					</h2>
					<button className="close-button" onClick={ onClose }>
						×
					</button>
				</div>
				<p>
					{ moment( order.date_created ).format(
						'MMMM DD, YYYY, H:mma'
					) }
				</p>
			</div>
		)
	);
}
