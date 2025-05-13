/**
 * External dependencies
 */
import { store, getContext } from '@wordpress/interactivity';

store( 'woocommerce/mini-cart', {
	state: {
		get derivedClassName() {
			const { isOpen } = getContext< { isOpen: boolean } >();

			return ! isOpen
				? 'wc-block-components-drawer__screen-overlay wc-block-components-drawer__screen-overlay--is-hidden'
				: 'wc-block-components-drawer__screen-overlay wc-block-components-drawer__screen-overlay';
		},
	},

	callbacks: {
		toggleIsOpen() {
			const ctx = getContext< { isOpen: boolean } >();
			ctx.isOpen = ! ctx.isOpen;
		},
	},
} );
