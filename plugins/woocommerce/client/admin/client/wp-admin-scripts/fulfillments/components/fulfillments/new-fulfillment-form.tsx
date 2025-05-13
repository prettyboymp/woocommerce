/**
 * External dependencies
 */
import { useState } from 'react';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { LineItem, Order } from '../../data/types';
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

const NewFulfillmentForm: React.FC = () => {
	const { order, fulfillments, openSection } = useFulfillmentDrawerContext();
	const remainingItems = getItemsNotInAnyFulfillment(
		fulfillments,
		order ?? ( { line_items: [] as LineItem[] } as Order )
	);
	const [ selectedItems, setSelectedItems ] = useState< ItemQuantity[] >(
		spreadItems( remainingItems )
	);

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
					<FulfillmentProvider
						orderId={ order.id }
						selectedItems={ selectedItems }
						fulfillment={ null }
					>
						<div className="woocommerce-fulfillment-item-actions">
							<SaveAsDraftButton />
							<FulfillItemsButton />
						</div>
					</FulfillmentProvider>
				</div>
			) }
		</div>
	);
};

export default NewFulfillmentForm;
