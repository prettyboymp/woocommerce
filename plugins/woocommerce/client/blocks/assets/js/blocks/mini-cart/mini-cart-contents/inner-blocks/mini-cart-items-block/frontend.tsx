/**
 * External dependencies
 */
import { privateApis, store } from '@wordpress/interactivity';
import { Suspense } from 'preact/compat';

/**
 * Internal dependencies
 */
import Block from './component';
import { renderPreactBlock } from './render-blocks';

export const { directive } = privateApis(
	'I acknowledge that using private APIs means my theme or plugin will inevitably break in the next version of WordPress.'
);

// data-wp-client-only-component
directive(
	'client-only-component',
	( {
		directives: { [ 'client-only-component' ]: comp },
		element,
		evaluate,
	} ) => {
		const entry = comp.find( ( { suffix } ) => suffix === null );
		return evaluate( entry )( element.props );
	}
);

store( 'woocommerce/mini-cart-items-block', {
	components: {
		Comp: () => {
			const element = document.querySelector(
				'.wp-block-woocommerce-mini-cart-items'
			);

			if ( ! element ) {
				return () => {};
			}

			return renderPreactBlock( element );
		},
	},
	state: {
		timesClosed: -1,
	},
	actions: {
		// toggle() {
		// 	const context = getContext();
		// 	context.isOpen = ! context.isOpen;
		// },
		// hideMyself() {
		// 	const context = getContext();
		// 	context.isOpen = false;
		// },
	},
	callbacks: {
		// logIsOpen() {
		// 	const { isOpen } = getContext();
		// 	console.log( `Is open: ${ isOpen }` );
		// },
		// trackTimesClosed() {
		// 	const { isOpen } = getContext();
		// 	if ( ! isOpen ) {
		// 		state.timesClosed++;
		// 		console.log( `Times closed: ${ state.timesClosed }` );
		// 	}
		// },
	},
} );

// import clsx from 'clsx';

// type MiniCartItemsBlockProps = {
// 	children: JSX.Element;
// 	className: string;
// };

// const Block = ( {
// 	children,
// 	className,
// }: MiniCartItemsBlockProps ): JSX.Element => {
// 	return (
// 		<div
// 			className={ clsx( className, 'wc-block-mini-cart__items' ) }
// 			tabIndex={ -1 }
// 		>
// 			{ children }
// 		</div>
// 	);
// };

export default Block;
