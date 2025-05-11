/**
 * External dependencies
 */
import { Button, Icon } from '@wordpress/components';
import { useState } from 'react';

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
import ShipmentInfoEditor from '../shipment-form/editor';
import MetadataViewer from '../metadata-viewer';
import EditFulfillmentButton from '../buttons/edit-fulfillment-button';
import FulfillItemsButton from '../buttons/fulfill-items-button';
import CancelLink from '../buttons/cancel-link';
import RemoveButton from '../buttons/remove-button';
import UpdateButton from '../buttons/update-button';

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

	const [ formItems, setFormItems ] =
		useState< ItemQuantity[] >( itemsInFulfillment );

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
						icon={ expanded ? 'arrow-up-alt2' : 'arrow-down-alt2' }
						size={ 16 }
					/>
				</Button>
			</div>
			{ expanded && (
				<>
					<ItemSelector
						items={ formItems }
						setSelectedItems={ setSelectedItems }
						currency={ order.currency }
						editMode={ editMode }
					/>
					{ editMode && (
						<>
							<ShipmentForm />
						</>
					) }
					{ ! editMode && (
						<>
							<ShipmentInfoEditor />
							<MetadataViewer fulfillment={ fulfillment } />
						</>
					) }
					<CustomerNotificationBox
						value={ notifyCustomer }
						setValue={ setNotifyCustomer }
					/>
					<FulfillmentProvider
						orderId={ order.id }
						selectedItems={ selectedItems }
						notifyCustomer={ false }
					>
						<div className="woocommerce-fulfillment-item-actions">
							{ ! editMode ? (
								<>
									<EditFulfillmentButton
										onClick={ () => {
											setEditMode( true );
											setFormItems( selectableItems );
										} }
									/>
									<FulfillItemsButton />
								</>
							) : (
								<>
									<CancelLink
										onClick={ () => {
											setFormItems( itemsInFulfillment );
											setEditMode( false );
										} }
									/>
									<RemoveButton />
									<UpdateButton />
								</>
							) }
						</div>
					</FulfillmentProvider>
				</>
			) }
		</div>
	);
}
