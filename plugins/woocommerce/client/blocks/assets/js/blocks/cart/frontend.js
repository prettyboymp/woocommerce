/**
 * External dependencies
 */
import { getValidBlockAttributes } from '@woocommerce/base-utils';
import {
	Children,
	cloneElement,
	isValidElement,
	useEffect,
} from '@wordpress/element';
import { useStoreCart } from '@woocommerce/base-context';
import { getRegisteredBlockComponents } from '@woocommerce/blocks-registry';
import { renderParentBlock } from '@woocommerce/atomic-utils';

/**
 * Internal dependencies
 */
import './inner-blocks/register-components';
import Block from './block';
import { blockName, blockAttributes } from './attributes';
import { renderCartSkeleton } from './cart-skeleton';

const getProps = ( el ) => {
	return {
		attributes: getValidBlockAttributes(
			blockAttributes,
			!! el ? el.dataset : {}
		),
	};
};

const Wrapper = ( { children } ) => {
	// we need to pluck out receiveCart.
	// eslint-disable-next-line no-unused-vars
	const { extensions, receiveCart, cartIsLoading, ...cart } = useStoreCart();

	useEffect( () => {
		// Only remove skeleton when cart is done loading and cart block is rendered
		if ( ! cartIsLoading ) {
			const cartElement = document.querySelector(
				'.wp-block-woocommerce-cart'
			);
			const skeletonElement = document.getElementById( 'cart-skeleton' );

			if ( cartElement && skeletonElement ) {
				skeletonElement.remove();
			}
		}
	}, [ cartIsLoading ] );

	return Children.map( children, ( child ) => {
		if ( isValidElement( child ) ) {
			const componentProps = {
				extensions,
				cart,
			};
			return cloneElement( child, componentProps );
		}
		return child;
	} );
};

renderCartSkeleton();

renderParentBlock( {
	Block,
	blockName,
	selector: '.wp-block-woocommerce-cart',
	getProps,
	blockMap: getRegisteredBlockComponents( blockName ),
	blockWrapper: Wrapper,
} );
