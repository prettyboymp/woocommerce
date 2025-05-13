/**
 * External dependencies
 */
import moment from 'moment';

/**
 * Internal dependencies
 */
import { useFulfillmentDrawerContext } from '../../../context/drawer-context';

export default function FulfillmentsDrawerHeader( {
	onClose,
}: {
	onClose: () => void;
} ) {
	const { order } = useFulfillmentDrawerContext();
	if ( ! order ) {
		return null;
	}

	return (
		order && (
			<div className="drawer-header">
				<div className="drawer-header__title">
					<h2>
						#{ order.id }{ ' ' }
						{ order.billing.first_name +
							' ' +
							order.billing.last_name }
					</h2>
					<button
						className="drawer-header__close-button"
						onClick={ onClose }
					>
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
