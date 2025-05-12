/**
 * External dependencies
 */
import { Button, Icon } from '@wordpress/components';
import { useState } from 'react';
import { __, sprintf } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { Fulfillment, Order } from '../../data/types';
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
import MetadataViewer from '../metadata-viewer';
import EditFulfillmentButton from '../action-buttons/edit-fulfillment-button';
import FulfillItemsButton from '../action-buttons/fulfill-items-button';
import CancelLink from '../action-buttons/cancel-link';
import RemoveButton from '../action-buttons/remove-button';
import UpdateButton from '../action-buttons/update-button';
import ShipmentViewer from '../shipment-form/shipment-viewer';
import { ShipmentFormProvider } from '../../context/shipment-form-context';
import FulfillmentStatusBadge from './fulfillment-status-badge';

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
	const [ notifyCustomer, setNotifyCustomer ] = useState( true );
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

	const handleChevronClick = () => {
		if ( editMode ) return;
		if ( ! expanded ) {
			onExpand();
		} else {
			onCollapse();
		}
	};

	return (
		<div className="woocommerce-fulfillment-stored-fulfillment-list-item">
			<div
				className="woocommerce-fulfillment-stored-fulfillment-list-item-header"
				onClick={ handleChevronClick }
				onKeyUp={ ( event ) => {
					if ( event.key === 'Enter' ) {
						handleChevronClick();
					}
				} }
				role="button"
				tabIndex={ -1 }
			>
				<h3>
					{
						// eslint-disable-next-line @wordpress/valid-sprintf
						sprintf(
							editMode
								? /* translators: %s: Fulfillment ID */
								  __( 'Editing fulfillment #%s', 'woocommerce' )
								: /* translators: %s: Fulfillment ID */
								  __( 'Fulfillment #%s', 'woocommerce' ),
							index + 1
						)
					}
				</h3>
				<FulfillmentStatusBadge fulfillment={ fulfillment } />
				<Button __next40pxDefaultSize size="small">
					<Icon
						icon={ expanded ? 'arrow-up-alt2' : 'arrow-down-alt2' }
						size={ 16 }
						color={ editMode ? '#dddddd' : undefined }
					/>
				</Button>
			</div>
			{ expanded && (
				<div className="woocommerce-fulfillment-stored-fulfillment-list-item-content">
					<ItemSelector
						items={ formItems }
						setSelectedItems={ setSelectedItems }
						currency={ order.currency }
						editMode={ editMode }
					/>
					<ShipmentFormProvider fulfillment={ fulfillment }>
						{ editMode && (
							<>
								<ShipmentForm />
							</>
						) }
						{ ! editMode && (
							<>
								<ShipmentViewer />
								<MetadataViewer fulfillment={ fulfillment } />
							</>
						) }
						<CustomerNotificationBox
							value={ notifyCustomer }
							setValue={ setNotifyCustomer }
						/>
						<FulfillmentProvider
							fulfillment={ fulfillment }
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
												setFormItems(
													itemsInFulfillment
												);
												setEditMode( false );
											} }
										/>
										<RemoveButton />
										<UpdateButton />
									</>
								) }
							</div>
						</FulfillmentProvider>
					</ShipmentFormProvider>
				</div>
			) }
		</div>
	);
}
