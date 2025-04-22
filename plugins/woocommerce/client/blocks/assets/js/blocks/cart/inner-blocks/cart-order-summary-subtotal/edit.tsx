/**
 * External dependencies
 */
import { useBlockProps } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import Block from './block';

export const Edit = ( {
	attributes,
}: {
	attributes: {
		className: string;
	};
	setAttributes: ( attributes: Record< string, unknown > ) => void;
} ): JSX.Element => {
	const { className } = attributes;
	const blockProps = useBlockProps();
	return (
		<div { ...blockProps }>
			<Block className={ className } />
		</div>
	);
};

export const Save = (): JSX.Element => {
	return (
		<div
			{ ...useBlockProps.save( {
				className: 'wc-block-components-skeleton',
			} ) }
		>
			<div className="wc-block-components-skeleton__row">
				<div
					className="wc-block-components-skeleton__element"
					style={ { width: '173px', height: '8px' } }
				></div>
				<div
					className="wc-block-components-skeleton__element"
					style={ { width: '45px', height: '8px' } }
				></div>
			</div>
		</div>
	);
};
