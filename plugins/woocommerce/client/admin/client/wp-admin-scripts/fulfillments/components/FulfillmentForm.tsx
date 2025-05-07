/**
 * External dependencies
 */
import { useState } from 'react';
import { useSelect } from '@wordpress/data';
import moment from 'moment';
import { Link } from '@woocommerce/components';
import { CheckboxControl } from '@wordpress/components';
import { __, _n, sprintf } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { FulfillmentStore } from '../data/store';
import FulfillmentItem from './FulfillmentItem';
import { LineItem } from '../data/types';
import ShipmentForm from './ShipmentForm';
import { useFulfillmentFormContext } from '../context/FulfillmentFormContext';

interface FormProps {
	orderId: number;
	onClose: () => void;
}

const FulfillmentForm: React.FC< FormProps > = ( { orderId, onClose } ) => {
	const {
		items,
		setItems,
		selectedItems,
		clearSelectedItems,
		selectAllItems,
	} = useFulfillmentFormContext();
	const [ itemsCount, setItemsCount ] = useState( 0 );
	const { order, isLoading } = useSelect(
		( select ) => {
			const store = select( FulfillmentStore );
			const orderData = store.getOrder( orderId );
			if ( orderData ) {
				setItems( orderData.line_items );
				setItemsCount(
					orderData.line_items.reduce(
						( count: number, item: LineItem ) => {
							count = count + item.quantity;
							return count;
						},
						0
					)
				);
			}
			return {
				order: orderData,
				isLoading: store.isOrderLoading( orderId ),
			};
		},
		[ orderId, setItems ]
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
						onChange={ () => {
							if ( selectedItems.length === itemsCount ) {
								clearSelectedItems();
							} else {
								selectAllItems();
							}
						} }
						id="fulfillment-item-bulk-select"
						name="fulfillment-item-bulk-select"
						checked={ selectedItems.length === itemsCount }
						indeterminate={
							selectedItems.length > 0 &&
							selectedItems.length < itemsCount
						}
						__nextHasNoMarginBottom
					/>
					{ selectedItems.length > 0 && (
						<div className="woocommerce-fulfillment-item-bulk-select-label">
							{ sprintf(
								/* translators: %s: number of selected items */
								_n(
									'%s selected',
									'%s selected',
									selectedItems.length,
									'woocommerce'
								),
								selectedItems.length
							) }
						</div>
					) }
					{ itemsCount > selectedItems.length && (
						<Link
							href="#"
							className="woocommerce-fulfillment-item-bulk-select-link"
							onClick={ ( event ) => {
								event.preventDefault();
								selectAllItems();
							} }
						>
							{ sprintf(
								/* translators: %s: number of items in the order */
								__( 'Select all (%s)', 'woocommerce' ),
								itemsCount
							) }
						</Link>
					) }
					{ selectedItems.length > 0 && (
						<Link
							href="#"
							className="woocommerce-fulfillment-item-bulk-select-link"
							onClick={ ( event ) => {
								event.preventDefault();
								clearSelectedItems();
							} }
						>
							{ __( 'Clear selection', 'woocommerce' ) }
						</Link>
					) }
				</div>
				<ul className="woocommerce-fulfillment-item-list">
					{ items.map( ( item: LineItem ) => (
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
