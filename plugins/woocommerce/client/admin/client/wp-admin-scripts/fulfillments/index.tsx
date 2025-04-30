/**
 * External dependencies
 */
import React, { useLayoutEffect, useState } from 'react';
import { createRoot } from '@wordpress/element';

/**
 * Internal dependencies
 */
import FulfillmentDrawer from './components/FullfillmentDrawer';
import './style.scss'; // optional styling

function FulfillmentsController() {
	const [ openOrderId, setOpenOrderId ] = useState< number | null >( null );
	const [ activeRow, setActiveRow ] = useState< HTMLElement | null >( null );

	useLayoutEffect( () => {
		const triggers = document.querySelectorAll(
			'.fulfillments-trigger'
		) as NodeListOf< HTMLElement >;
		triggers.forEach( ( trigger ) => {
			const id = parseInt( trigger.dataset.orderId || '', 10 );
			if ( id ) {
				trigger.addEventListener( 'click', ( e ) => {
					e.preventDefault();
					e.stopPropagation();
					const targetRow = trigger.closest( 'tr' );
					document
						.querySelectorAll( '.type-shop_order' )
						.forEach( ( row ) => {
							row.classList.remove( 'is-selected' );
						} );
					targetRow?.classList.add( 'is-selected' );
					setOpenOrderId( id );
				} );
			}
		} );
	}, [] ); // eslint-disable-line react-hooks/exhaustive-deps

	return (
		<FulfillmentDrawer
			isOpen={ openOrderId !== null }
			orderId={ openOrderId }
			onClose={ () => {
				document
					.querySelectorAll( '.type-shop_order' )
					.forEach( ( row ) => {
						row.classList.remove( 'is-selected' );
					} );
				setOpenOrderId( null );
			} }
		/>
	);
}

export default FulfillmentsController;

const container = document.querySelector(
	'#wc_order_fulfillments_panel_container'
) as HTMLElement;

createRoot( container ).render( <FulfillmentsController /> );
