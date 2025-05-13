/**
 * External dependencies
 */
import { useState } from 'react';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { LineItem, Order } from '../../data/types';
import ShipmentForm from '../shipment-form';
import CustomerNotificationBox from '../customer-notification-form';
import { FulfillmentProvider } from '../../context/fulfillment-context';
import SaveAsDraftButton from '../action-buttons/save-draft-button';
import FulfillItemsButton from '../action-buttons/fulfill-items-button';
import {
	ItemQuantity,
	getItemsNotInAnyFulfillment,
	spreadItems,
} from '../../utils/order-utils';
import ItemSelector from './item-selector';
import { useFulfillmentDrawerContext } from '../../context/drawer-context';
import { ShipmentFormProvider } from '../../context/shipment-form-context';

const NewFulfillmentForm: React.FC = () => {
	const { order, fulfillments, openSection } = useFulfillmentDrawerContext();
	const remainingItems = getItemsNotInAnyFulfillment(
		fulfillments,
		order ?? ( { line_items: [] as LineItem[] } as Order )
	);
	const [ selectedItems, setSelectedItems ] = useState< ItemQuantity[] >(
		spreadItems( remainingItems )
	);
	const [ notifyCustomer, setNotifyCustomer ] = useState( true );

	if ( ! order ) {
		return null;
	}

	if ( remainingItems.length === 0 ) {
		return null;
	}

	return (
		<div className="woocommerce-fulfillment-new-fulfillment-form">
			<div className="woocommerce-fulfillment-new-fulfillment-form__header">
				<h3>
					{ fulfillments.length === 0
						? __( 'Order Items', 'woocommerce' )
						: __( 'Pending Items', 'woocommerce' ) }
				</h3>
			</div>
			{ openSection && (
				<div className="woocommerce-fulfillment-new-fulfillment-form__content">
					<ItemSelector
						items={ remainingItems }
						setSelectedItems={ setSelectedItems }
						currency={ order.currency }
						editMode={ true }
					/>
					<ShipmentFormProvider>
						<ShipmentForm />
						<CustomerNotificationBox
							value={ notifyCustomer }
							setValue={ setNotifyCustomer }
						/>
						<FulfillmentProvider
							orderId={ order.id }
							notifyCustomer={ notifyCustomer }
							selectedItems={ selectedItems }
							fulfillment={ null }
						>
							<div className="woocommerce-fulfillment-item-actions">
								<SaveAsDraftButton />
								<FulfillItemsButton />
							</div>
						</FulfillmentProvider>
					</ShipmentFormProvider>
				</div>
			) }
		</div>
	);
};

export default NewFulfillmentForm;
