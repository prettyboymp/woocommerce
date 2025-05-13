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
import ErrorLabel from '../user-interface/error-label';

const NewFulfillmentForm: React.FC = () => {
	const { order, fulfillments, openSection, isEditing } =
		useFulfillmentDrawerContext();
	const [ error, setError ] = useState< string | null >( null );
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
		<div
			className={ [
				'woocommerce-fulfillment-new-fulfillment-form',
				isEditing &&
					'woocommerce-fulfillment-new-fulfillment-form__disabled',
			].join( ' ' ) }
		>
			<div className="woocommerce-fulfillment-new-fulfillment-form__header">
				<h3>
					{ fulfillments.length === 0
						? __( 'Order Items', 'woocommerce' )
						: __( 'Pending Items', 'woocommerce' ) }
				</h3>
			</div>
			{ openSection && (
				<div className="woocommerce-fulfillment-new-fulfillment-form__content">
					{ error && <ErrorLabel error={ error } /> }
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
							<SaveAsDraftButton setError={ setError } />
							<FulfillItemsButton setError={ setError } />
						</div>
					</FulfillmentProvider>
				</div>
			) }
		</div>
	);
};

export default NewFulfillmentForm;
