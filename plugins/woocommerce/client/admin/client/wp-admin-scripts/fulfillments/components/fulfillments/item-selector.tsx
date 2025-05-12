/**
 * External dependencies
 */
import { CheckboxControl } from '@wordpress/components';
import { __, _n, sprintf } from '@wordpress/i18n';
import { Link } from '@woocommerce/components';
import { useEffect, useState } from 'react';
import { isEqual } from 'lodash';

/**
 * Internal dependencies
 */
import {
	ItemQuantity,
	areItemsSpread,
	spreadItems,
	unspreadItems,
} from '../../utils/order-utils';
import FulfillmentLineItem from './fulfillment-line-item';

type ItemSelectorProps = {
	items: ItemQuantity[];
	setSelectedItems: ( items: ItemQuantity[] ) => void;
	currency: string;
	editMode: boolean;
};

export default function ItemSelector( {
	items,
	setSelectedItems,
	editMode,
	currency,
}: ItemSelectorProps ) {
	const [ itemsBuffer, setItemsBuffer ] = useState< ItemQuantity[] >( [] );
	const [ prevItems, setPrevItems ] = useState< ItemQuantity[] >( [] );

	// Update the items buffer when the items prop change.
	if ( ! isEqual( items, prevItems ) ) {
		setItemsBuffer(
			areItemsSpread( items ) ? items : spreadItems( items )
		);
		setPrevItems( items );
	}

	const itemsCount = itemsBuffer.length;
	const selectedItemsCount = itemsBuffer.filter(
		( item ) => item.checked
	).length;

	const clearSelectedItems = () => {
		setItemsBuffer(
			itemsBuffer.map( ( item ) => ( {
				...item,
				checked: false,
			} ) )
		);
	};

	const selectAllItems = () => {
		setItemsBuffer(
			itemsBuffer.map( ( item ) => ( {
				...item,
				checked: true,
			} ) )
		);
	};

	const handleToggleItem = ( id: string, checked: boolean ) => {
		const newItemsBuffer = [ ...itemsBuffer ];
		newItemsBuffer.forEach( ( item ) => {
			if ( item.item_id === id || item.item_id.startsWith( id + '-' ) ) {
				item.checked = checked;
			}
		} );

		setItemsBuffer( newItemsBuffer );
	};

	const isChecked = ( id: string ) => {
		// If the item id is not a split item, we check if all the sub items are checked if any.
		// If not, we check if the item is checked.
		const checkedItems = itemsBuffer.filter(
			( item ) => item.item_id.startsWith( id + '-' ) && item.checked
		);
		if ( checkedItems.length > 0 ) {
			return checkedItems.every( ( item ) => item.checked );
		}
		return (
			itemsBuffer.find( ( item ) => item.item_id === id )?.checked ??
			false
		);
	};

	const isIndeterminate = ( id: string ) => {
		// Sub items can't be indeterminate.
		if ( id.includes( '-' ) ) {
			return false;
		}
		// If the item doesn't have any sub items, we return false.
		const subItems = itemsBuffer.filter( ( item ) =>
			item.item_id.startsWith( id + '-' )
		);
		if ( subItems.length === 0 ) {
			return false;
		}
		const mainItem = items.find( ( __item ) => __item.item_id === id );
		if ( mainItem ) {
			const totalQuantity = mainItem.qty;
			const checkedItemsQuantity = subItems.filter(
				( item ) => item.checked
			).length;
			return (
				checkedItemsQuantity > 0 && checkedItemsQuantity < totalQuantity
			);
		}
		return false;
	};

	// Update the selected items with a callback prop (output) when the items buffer change.
	useEffect( () => {
		setSelectedItems(
			unspreadItems( itemsBuffer.filter( ( item ) => item.checked ) )
		);
	}, [ setSelectedItems, itemsBuffer ] );

	return (
		<ul className="woocommerce-fulfillment-item-list">
			<li>
				<div className="woocommerce-fulfillment-item-bulk-select">
					{ editMode && (
						<CheckboxControl
							onChange={ () => {
								if ( selectedItemsCount === itemsCount ) {
									clearSelectedItems();
								} else {
									selectAllItems();
								}
							} }
							checked={ selectedItemsCount === itemsCount }
							indeterminate={
								selectedItemsCount > 0 &&
								selectedItemsCount < itemsCount
							}
							__nextHasNoMarginBottom
						/>
					) }
					{ selectedItemsCount > 0 && (
						<div className="woocommerce-fulfillment-item-bulk-select__label">
							{ sprintf(
								/* translators: %s: number of selected items */
								_n(
									'%s selected',
									'%s selected',
									selectedItemsCount,
									'woocommerce'
								),
								selectedItemsCount
							) }
						</div>
					) }
					{ editMode && itemsCount > selectedItemsCount && (
						<Link
							href="#"
							className="woocommerce-fulfillment-item-bulk-select__link"
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
					{ editMode && selectedItemsCount > 0 && (
						<Link
							href="#"
							className="woocommerce-fulfillment-item-bulk-select__link"
							onClick={ ( event ) => {
								event.preventDefault();
								clearSelectedItems();
							} }
						>
							{ __( 'Clear selection', 'woocommerce' ) }
						</Link>
					) }
				</div>
			</li>
			{ items.map( ( item: ItemQuantity ) => (
				<li key={ item.item_id }>
					<FulfillmentLineItem
						item={ item.item }
						quantity={ item.qty }
						editMode={ editMode }
						currency={ currency }
						toggleItem={ handleToggleItem }
						isChecked={ isChecked }
						isIndeterminate={ isIndeterminate }
					/>
				</li>
			) ) }
		</ul>
	);
}
