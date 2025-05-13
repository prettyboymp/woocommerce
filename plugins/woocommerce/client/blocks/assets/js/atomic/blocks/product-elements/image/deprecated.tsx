/**
 * External dependencies
 */
import { createBlock } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import metadata from './block.json';
import { BlockAttributes } from './types';
import save from '../save';

// In v2, we're migrating the `showSaleBadge` attribute to an inner block.
const v1 = {
	save,
	attributes: metadata.attributes,
	isEligible: ( { showSaleBadge }: BlockAttributes ) => {
		// If the block is pristine, it doesn't have a showSaleBadge attribute
		// but it is `true` by default.
		const trueByDefault = showSaleBadge === undefined;

		// If the block was edited, it will have a showSaleBadge attribute
		// that we should respect.
		const trueAfterEdit = showSaleBadge === true;

		return trueByDefault || trueAfterEdit;
	},
	migrate: ( attributes: BlockAttributes ) => {
		const { showSaleBadge, saleBadgeAlign } = attributes;

		// If showSaleBadge is false, it means that the sale badge was explicitly set to false.
		if ( showSaleBadge === false ) {
			return [ attributes ];
		}
		// Otherwise, it's either:
		// - true explicitly or
		// - undefined (implicit true by default).

		return [
			{
				...attributes,
				showSaleBadge: false,
			},
			[
				createBlock( 'woocommerce/product-sale-badge', {
					align: saleBadgeAlign,
				} ),
			],
		];
	},
};

export default [ v1 ];
