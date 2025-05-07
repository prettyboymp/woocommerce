/**
 * External dependencies
 */
import clsx from 'clsx';
import { createBlock } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import metadata from './block.json';
import { BlockAttributesV1 } from './types';

// In v2, we removed the `showSaleBadge` attribute and converted it to an inner block.
const v1 = {
	attributes: {
		...metadata.attributes,
		showSaleBadge: {
			type: 'boolean',
			default: true,
		},
		saleBadgeAlign: {
			type: 'string',
			default: 'right',
		},
	},
	save( { attributes }: { attributes: BlockAttributesV1 } ) {
		if (
			attributes.isDescendentOfQueryLoop ||
			attributes.isDescendentOfSingleProductBlock
		) {
			return null;
		}

		return <div className={ clsx( 'is-loading', attributes.className ) } />;
	},
	isEligible: ( attributes: BlockAttributesV1 ) =>
		attributes.showSaleBadge !== undefined,
	migrate: ( attributes: BlockAttributesV1 ) => {
		const { showSaleBadge, saleBadgeAlign, ...rest } = attributes;
		if ( ! showSaleBadge ) {
			return [ rest ];
		}

		let justifyContent = 'flex-end';
		if ( saleBadgeAlign === 'left' ) {
			justifyContent = 'flex-start';
		} else if ( saleBadgeAlign === 'center' ) {
			justifyContent = 'center';
		}

		const layoutProps = {
			justifyContent,
			type: 'flex',
		};

		const newAttributes = {
			...rest,
			layout: layoutProps,
		};

		return [
			newAttributes,
			[ createBlock( 'woocommerce/product-sale-badge' ) ],
		];
	},
};

export default [ v1 ];
