/**
 * External dependencies
 */
// import { useStoreCart } from '@woocommerce/base-context/hooks';
import CartLineItemsTable from '../../../../../base/components/cart-checkout/cart-line-items-table/index';
import clsx from 'clsx';

type MiniCartProductsTableBlockProps = {
	className: string;
};

const Block = ( {
	className,
}: MiniCartProductsTableBlockProps ): JSX.Element => {
	// console.log( 'hi', CartLineItemsTable );
	// const { cartItems, cartIsLoading } = useStoreCart();
	return (
		<div
			className={ clsx(
				className,
				'wc-block-mini-cart__products-table'
			) }
		>
			<p>Hello World</p>
			<CartLineItemsTable
				lineItems={ [] }
				isLoading={ false }
				className="wc-block-mini-cart-items"
			/>
		</div>
	);
};

export default Block;
