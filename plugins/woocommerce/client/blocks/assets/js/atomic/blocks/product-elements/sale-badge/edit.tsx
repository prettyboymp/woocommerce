/**
 * External dependencies
 */
import { useBlockProps } from '@wordpress/block-editor';
import type { BlockEditProps } from '@wordpress/blocks';
import { ProductQueryContext as Context } from '@woocommerce/blocks/product-query/types';
import { useEffect } from '@wordpress/element';

/**
 * Internal dependencies
 */
import Block from './block';
import type { BlockAttributes } from './types';
import { useIsDescendentOfSingleProductTemplate } from '../shared/use-is-descendent-of-single-product-template';

const Edit = ( {
	attributes,
	setAttributes,
	context,
}: BlockEditProps< BlockAttributes > & { context: Context } ): JSX.Element => {
	const blockProps = useBlockProps();

	// Remove the `style` prop from the block props to avoid passing it to the wrapper div.
	const { style, ...wrapperProps } = blockProps;

	const blockAttrs = {
		...attributes,
		...context,
	};
	const isDescendentOfQueryLoop = Number.isFinite( context.queryId );

	const { isDescendentOfSingleProductTemplate } =
		useIsDescendentOfSingleProductTemplate();

	useEffect(
		() =>
			setAttributes( {
				isDescendentOfQueryLoop,
				isDescendentOfSingleProductTemplate,
			} ),
		[
			setAttributes,
			isDescendentOfQueryLoop,
			isDescendentOfSingleProductTemplate,
		]
	);

	return (
		<div { ...wrapperProps }>
			<Block { ...blockAttrs } />
		</div>
	);
};

export default Edit;
