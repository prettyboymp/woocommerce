/**
 * External dependencies
 */
import { useSelect } from '@wordpress/data';
import moment from 'moment';
import { Link } from '@woocommerce/components';
import { CheckboxControl } from '@wordpress/components';
import { __, _n } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { FulfillmentStore } from '../data/store';
import FulfillmentItem from './FulfillmentItem';
import { LineItem } from '../data/types';
import ShipmentForm from './ShipmentForm';

interface FormProps {
	orderId: number;
	onClose: () => void;
}

const FulfillmentForm: React.FC< FormProps > = ( { orderId, onClose } ) => {
	const { order, isLoading } = useSelect(
		( select ) => {
			const store = select( FulfillmentStore );
			return {
				order: store.getOrder( orderId ),
				isLoading: store.isOrderLoading( orderId ),
			};
		},
		[ orderId ]
	);

	if ( isLoading ) {
		return <>Fetching data...</>;
	}

	if ( ! order ) {
		return <>No order found</>;
	}

	return (
		<>
			<div className="drawer-header">
				<div className="drawer-header-title">
					<h2>
						#{ orderId }{ ' ' }
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
			<div className="drawer-content">
				<h3>{ __( 'Order Items', 'woocommerce' ) }</h3>
				<div className="woocommerce-fulfillment-item-bulk-select">
					<CheckboxControl
						onChange={ () => {} }
						id="fulfillment-item-bulk-select"
						name="fulfillment-item-bulk-select"
						checked={ false }
						indeterminate={ true }
					/>
					<label
						htmlFor="fulfillment-item-bulk-select"
						className="woocommerce-fulfillment-item-bulk-select-label"
					>
						{
							/* translators: %d: number of selected items */
							_n( '%d selected', '%d selected', 3, 'woocommerce' )
						}
					</label>
					<Link
						href="#"
						className="woocommerce-fulfillment-item-bulk-select-link"
					>
						{ __( 'Clear selection', 'woocommerce' ) }
					</Link>
				</div>
				<ul className="woocommerce-fulfillment-item-list">
					{ order.line_items.map( ( item: LineItem ) => (
						<li key={ item.id }>
							<FulfillmentItem
								item={ item }
								currency={ order.currency }
							/>
						</li>
					) ) }
				</ul>
				<ShipmentForm />
			</div>
		</>
	);
};

export default FulfillmentForm;
