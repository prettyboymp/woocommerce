/**
 * External dependencies
 */
import { Button, Icon } from '@wordpress/components';
import { useState } from 'react';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { Fulfillment, Order } from '../../data/types';
import FulfillmentStatusChip from './fulfillment-status-chip';
import {
	ItemQuantity,
	combineItems,
	getItemsFromFulfillment,
	getItemsNotInAnyFulfillment,
} from '../../utils/order-utils';
import ShipmentForm from '../shipment-form';
import CustomerNotificationBox from '../customer-notification-form';
import { FulfillmentProvider } from '../../context/fulfillment-context';
import ItemSelector from './item-selector';

interface FulfillmentEditorProps {
	index: number;
	expanded: boolean;
	onExpand: () => void;
	onCollapse: () => void;
	fulfillment: Fulfillment;
	fulfillments: Fulfillment[];
	order: Order;
}
export default function FulfillmentEditor( {
	index,
	expanded,
	onExpand,
	onCollapse,
	fulfillment,
	fulfillments,
	order,
}: FulfillmentEditorProps ) {
	const [ editMode, setEditMode ] = useState( false );
	const [ notifyCustomer, setNotifyCustomer ] = useState( false );
	const [ selectedItems, setSelectedItems ] = useState< ItemQuantity[] >(
		[]
	);
	const itemsInFulfillment = getItemsFromFulfillment(
		order,
		fulfillment,
		true
	);
	const itemsNotInAnyFulfillment = getItemsNotInAnyFulfillment(
		fulfillments,
		order,
		false
	);
	const selectableItems = combineItems(
		itemsInFulfillment,
		itemsNotInAnyFulfillment
	);

	return (
		<div className="woocommerce-fulfillment-stored-fulfillment-list-item">
			<div className="woocommerce-fulfillment-stored-fulfillment-list-item-header">
				<h3>{ `Fulfillment #${ index + 1 }` }</h3>
				<FulfillmentStatusChip fulfillment={ fulfillment } />
				<Button
					__next40pxDefaultSize
					size="small"
					onClick={ ! expanded ? onExpand : onCollapse }
				>
					<Icon
						icon={ expanded ? 'arrow-down-alt2' : 'arrow-up-alt2' }
						size={ 16 }
					/>
				</Button>
			</div>
			{ expanded && (
				<>
					<ItemSelector
						items={
							editMode ? selectableItems : itemsInFulfillment
						}
						setSelectedItems={ setSelectedItems }
						currency={ order.currency }
						editMode={ editMode }
					/>
					{ editMode && (
						<>
							<ShipmentForm />
							<CustomerNotificationBox
								value={ notifyCustomer }
								setValue={ setNotifyCustomer }
							/>
						</>
					) }
					{ ! editMode && <div> Taha Paksu was here </div> }
					<FulfillmentProvider
						orderId={ order.id }
						selectedItems={ selectedItems }
						notifyCustomer={ false }
					>
						<div className="woocommerce-fulfillment-item-actions">
							{ editMode ? (
								<Button
									__next40pxDefaultSize
									size="small"
									onClick={ () => {
										setEditMode( false );
									} }
									className="woocommerce-fulfillment-item-actions-cancel"
								>
									{ __( 'Cancel', 'woocommerce' ) }
								</Button>
							) : (
								<Button
									__next40pxDefaultSize
									size="small"
									onClick={ () => {
										setEditMode( true );
									} }
									className="woocommerce-fulfillment-item-actions-edit"
								>
									{ __( 'Edit', 'woocommerce' ) }
								</Button>
							) }
							{ editMode && (
								<Button
									__next40pxDefaultSize
									size="small"
									onClick={ () => {
										setEditMode( false );
									} }
									className="woocommerce-fulfillment-item-actions-save"
								>
									{ __( 'Save', 'woocommerce' ) }
								</Button>
							) }
						</div>
					</FulfillmentProvider>
				</>
			) }
		</div>
	);
}
