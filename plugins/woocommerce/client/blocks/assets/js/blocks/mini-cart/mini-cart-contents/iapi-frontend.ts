/**
 * External dependencies
 */
import { store, getContext } from '@wordpress/interactivity';

store( 'woocommerce/mini-cart-contents', {
	callbacks: {
		closeDrawer() {
			const ctx = getContext< { isOpen: boolean } >(
				'woocommerce/mini-cart'
			);
			ctx.isOpen = false;
		},
	},
} );
