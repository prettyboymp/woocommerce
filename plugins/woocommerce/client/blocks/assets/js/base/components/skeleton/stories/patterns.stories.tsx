/**
 * External dependencies
 */
import { Meta, StoryObj } from '@storybook/react';

/**
 * Internal dependencies
 */
import { Skeleton, SkeletonProps } from '../';
import { ProductShortDescriptionSkeleton } from '../patterns/product-short-description';

export default {
	title: 'Base Components/Skeleton/Patterns',
	component: Skeleton,
	argTypes: {
		width: { control: 'text' },
		height: { control: 'text' },
		borderRadius: { control: 'text' },
		className: { control: 'text' },
		tag: {
			control: { type: 'select' },
			options: [ 'div' ],
		},
	},
	parameters: {
		docs: {
			description: {
				component:
					'Pattern skeletons are reusable structures built from base skeletons for common UI patterns.',
			},
		},
	},
} as Meta< SkeletonProps >;

export const ProductShortDescriptionSkeletonStory: StoryObj = {
	render: () => <ProductShortDescriptionSkeleton />,
	storyName: 'Product Short Description skeleton',
	parameters: {
		docs: {
			source: {
				code: '<ProductShortDescriptionSkeleton />',
			},
			description: {
				story: 'The skeleton pattern for the product short description.',
			},
		},
	},
};
