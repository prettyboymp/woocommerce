/**
 * External dependencies
 */
import { createHigherOrderComponent } from '@wordpress/compose';
import { Component } from '@wordpress/element';
import { productsStore } from '@woocommerce/data';
import { resolveSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */

const adaptV3ProductToV1 = ( product ) => {
	const price = product?.price;
	const images = product?.images.map( ( image ) => {
		return {
			...image,
			thumbnail: image.src,
			srcset: image.src,
		};
	} );

	return {
		...product,
		prices: {
			price,
		},
		images,
	};
};

/**
 * HOC that queries a product for a component.
 *
 * @param {Function} OriginalComponent Component being wrapped.
 */
const withProduct = createHigherOrderComponent( ( OriginalComponent ) => {
	return class WrappedComponent extends Component {
		state = {
			error: null,
			loading: false,
			product:
				this.props.attributes.productId === 'preview'
					? this.props.attributes.previewProduct
					: null,
		};

		componentDidMount() {
			this.loadProduct();
		}

		componentDidUpdate( prevProps ) {
			if (
				prevProps.attributes.productId !==
				this.props.attributes.productId
			) {
				this.loadProduct();
			}
		}

		loadProduct = () => {
			const { productId } = this.props.attributes;

			if ( productId === 'preview' ) {
				return;
			}

			if ( ! productId ) {
				this.setState( { product: null, loading: false, error: null } );
				return;
			}

			this.setState( { loading: true } );

			resolveSelect( productsStore )
				.getProduct( productId )
				.then( ( product ) => {
					this.setState( {
						product: adaptV3ProductToV1( product ),
						loading: false,
						error: null,
					} );
				} );
		};

		render() {
			const { error, loading, product } = this.state;

			return (
				<OriginalComponent
					{ ...this.props }
					error={ error }
					getProduct={ this.loadProduct }
					isLoading={ loading }
					product={ product }
				/>
			);
		}
	};
}, 'withProduct' );

export default withProduct;
