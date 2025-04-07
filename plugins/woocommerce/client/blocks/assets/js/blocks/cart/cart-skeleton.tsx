/**
 * External dependencies
 */
import { createRoot } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { CartSkeleton } from '../../base/components/skeleton/layouts/cart';

export const renderCartSkeleton = () => {
	const skeletonContainer = document.getElementById( 'cart-skeleton' );
	if ( skeletonContainer ) {
		createRoot( skeletonContainer ).render( <CartSkeleton /> );
	}
};
