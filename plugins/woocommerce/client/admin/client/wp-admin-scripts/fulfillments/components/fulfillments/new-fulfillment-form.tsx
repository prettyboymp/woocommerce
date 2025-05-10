/**
 * External dependencies
 */
import { LoadingPlaceholder } from '@automattic/components';
import { useState } from 'react';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { Fulfillment, Order } from '../../data/types';
import ShipmentForm from '../shipment-form';
import CustomerNotificationBox from '../customer-notification-form';
import { FulfillmentProvider } from '../../context/fulfillment-context';
import SaveAsDraftButton from '../buttons/save-draft-button';
import FulfillItemsButton from '../buttons/fulfill-items-button';
import {
	ItemQuantity,
	getItemsNotInAnyFulfillment,
	spreadItems,
} from '../../utils/order-utils';
import ItemSelector from './item-selector';

type NewFulfillmentFormProps = {
	order: Order;
	fulfillments: Fulfillment[];
};

const NewFulfillmentForm: React.FC< NewFulfillmentFormProps > = ( {
	order,
	fulfillments,
} ) => {
	const remainingItems = getItemsNotInAnyFulfillment( fulfillments, order );
	const [ selectedItems, setSelectedItems ] = useState< ItemQuantity[] >(
		spreadItems( remainingItems )
	);
	const [ notifyCustomer, setNotifyCustomer ] = useState( false );

	if ( ! order ) {
		return <LoadingPlaceholder />;
	}

	return (
		<div className="woocommerce-fulfillment-new-fulfillment-form">
			<h3>{ __( 'Order Items', 'woocommerce' ) }</h3>
			<ItemSelector
				items={ remainingItems }
				setSelectedItems={ setSelectedItems }
				currency={ order.currency }
				editMode={ true }
			/>
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
		</div>
	);
};

export default NewFulfillmentForm;
