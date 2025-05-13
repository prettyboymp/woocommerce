/**
 * External dependencies
 */
import { createBlock } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import metadata from './block.json';
import { BlockAttributesV1 } from './types';
import save from '../save';

const mapAlignToJustifyContent = {
	left: 'flex-start',
	center: 'center',
	right: 'flex-end',
};

// In v2, we removed the `showSaleBadge` attribute and converted it to an inner block.
const v1 = {
	attributes: {
		...metadata.attributes,
	},
	save,
	isEligible: ( { showSaleBadge }: BlockAttributesV1 ) => {
		// If the block is pristine, it doesn't have a showSaleBadge attribute
		// but it is `true` by default.
		const trueByDefault = showSaleBadge === undefined;

		// If the block was edited, it will have a showSaleBadge attribute
		// that we should respect.
		const trueAfterEdit = showSaleBadge === true;

		return trueByDefault || trueAfterEdit;
	},
	migrate: ( attributes: BlockAttributesV1 ) => {
		const { showSaleBadge, saleBadgeAlign } = attributes;
		console.log( 'migration starts', attributes );

		// If showSaleBadge is false, it means that the sale badge was explicitly set to false.
		if ( showSaleBadge === false ) {
			return [ attributes ];
		}
		// Otherwise, it's either:
		// - explicitly true or
		// - undefined (which means the default true value should be used).

		const justifyContent =
			mapAlignToJustifyContent[ saleBadgeAlign ] || 'flex-end';

		const layoutProps = {
			justifyContent,
			type: 'flex',
		};

		const newAttributes = {
			...attributes,
			showSaleBadge: false,
			layout: layoutProps,
		};

		console.log( 'migration ends', newAttributes );
		return [
			newAttributes,
			[ createBlock( 'woocommerce/product-sale-badge' ) ],
		];
	},
};

export default [ v1 ];
