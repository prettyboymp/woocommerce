/**
 * External dependencies
 */
import { useBlockProps } from '@wordpress/block-editor';
import Noninteractive from '@woocommerce/base-components/noninteractive';

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
			<Noninteractive>
				<Block className={ className } />
			</Noninteractive>
		</div>
	);
};

export const Save = (): JSX.Element => {
	return (
		<div
			{ ...useBlockProps.save( {
				className:
					'wc-block-components-skeleton wc-block-components-totals-wrapper',
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
