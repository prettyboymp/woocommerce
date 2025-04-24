/**
 * External dependencies
 */
import clsx from 'clsx';
// import { __ } from '@wordpress/i18n';
// import { CartResponseItem } from '@woocommerce/types';
import { createRef } from 'preact';
import { useEffect, useRef } from 'preact/hooks';
// import type { RefObject } from 'react';

/**
 * Internal dependencies
 */
// import CartLineItemRow from './cart-line-item-row';
// import './style.scss';

// const placeholderRows = [ ...Array( 3 ) ].map( ( _x, i ) => (
// 	<CartLineItemRow lineItem={ {} } key={ i } />
// ) );

const FakeCartLineItemRow = () => {
	return <div>FakeCartLineItemRow</div>;
};

interface CartLineItemsTableProps {
	lineItems: CartResponseItem[];
	isLoading: boolean;
	className?: string;
}

const setRefs = ( lineItems: CartResponseItem[] ) => {
	const refs = {} as Record< string, RefObject< HTMLTableRowElement > >;
	lineItems.forEach( ( { key } ) => {
		refs[ key ] = createRef();
	} );
	return refs;
};

const CartLineItemsTable = ( {
	lineItems = [],
	isLoading = false,
	className,
}: CartLineItemsTableProps ): JSX.Element => {
	// const tableRef = useRef< HTMLTableElement | null >( null );
	const rowRefs = useRef( setRefs( lineItems ) );
	useEffect( () => {
		rowRefs.current = setRefs( lineItems );
	}, [ lineItems ] );

	const onRemoveRow = ( nextItemKey: string | null ) => () => {
		if (
			rowRefs?.current &&
			nextItemKey &&
			rowRefs.current[ nextItemKey ].current instanceof HTMLElement
		) {
			( rowRefs.current[ nextItemKey ].current as HTMLElement ).focus();
		} else if ( tableRef.current instanceof HTMLElement ) {
			tableRef.current.focus();
		}
	};

	const products = lineItems.map( ( lineItem, i ) => {
		// const nextItemKey =
		// 	lineItems.length > i + 1 ? lineItems[ i + 1 ].key : null;
		return <FakeCartLineItemRow key={ lineItem.key } />;
		// return (
		// 	<CartLineItemRow
		// 		key={ lineItem.key }
		// 		lineItem={ lineItem }
		// 		onRemove={ onRemoveRow( nextItemKey ) }
		// 		ref={ rowRefs.current[ lineItem.key ] }
		// 		tabIndex={ -1 }
		// 	/>
		// );
	} );

	return (
		<table
			className={ clsx( 'wc-block-cart-items', className ) }
			tabIndex={ -1 }
		>
			<caption className="screen-reader-text">
				<h2>{ 'Products in cart' }</h2>
			</caption>
			<thead>
				<tr className="wc-block-cart-items__header">
					<th className="wc-block-cart-items__header-image">
						<span>{ 'Product' }</span>
					</th>
					<th className="wc-block-cart-items__header-product">
						<span>{ 'Details' }</span>
					</th>
					<th className="wc-block-cart-items__header-total">
						<span>{ 'Total' }</span>
					</th>
				</tr>
			</thead>
			<tbody>{ products }</tbody>
		</table>
	);
};

export default CartLineItemsTable;
