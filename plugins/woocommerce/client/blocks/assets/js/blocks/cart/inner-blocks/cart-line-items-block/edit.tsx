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
	attributes: { className: string };
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
		<div { ...useBlockProps.save() }>
			<table className="wc-block-cart-items wp-block-woocommerce-cart-line-items-block">
				<thead>
					<tr className="wc-block-cart-items__header">
						<th className="wc-block-cart-items__header-image">
							<div
								className="wc-block-components-skeleton__element"
								style={ {
									height: '8px',
									width: '78px',
									display: 'inline-block',
								} }
							></div>
						</th>
						<th className="wc-block-cart-items__header-product"></th>
						<th className="wc-block-cart-items__header-total">
							<div
								className="wc-block-components-skeleton__element"
								style={ {
									height: '8px',
									width: '78px',
									display: 'inline-block',
								} }
							></div>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr className="wc-block-cart-items__row">
						<td className="wc-block-cart-item__image">
							<div
								className="wc-block-components-skeleton__element"
								style={ { width: '78px', height: '78px' } }
							></div>
						</td>
						<td className="wc-block-cart-item__product">
							<div className="wc-block-cart-item__wrap">
								<div
									className="wc-block-components-skeleton__element"
									style={ {
										width: '100%',
										height: '8px',
										maxWidth: '173px',
									} }
								></div>
								<div
									className="wc-block-components-skeleton__element"
									style={ { width: '78px', height: '8px' } }
								></div>
							</div>
						</td>
						<td className="wc-block-cart-item__total">
							<div
								className="wc-block-components-skeleton__element"
								style={ { width: '100%', height: '8px' } }
							></div>
						</td>
					</tr>
					<tr className="wc-block-cart-items__row">
						<td className="wc-block-cart-item__image">
							<div
								className="wc-block-components-skeleton__element"
								style={ { width: '78px', height: '78px' } }
							></div>
						</td>
						<td className="wc-block-cart-item__product">
							<div className="wc-block-cart-item__wrap">
								<div
									className="wc-block-components-skeleton__element"
									style={ {
										width: '100%',
										height: '8px',
										maxWidth: '173px',
									} }
								></div>
								<div
									className="wc-block-components-skeleton__element"
									style={ { width: '78px', height: '8px' } }
								></div>
							</div>
						</td>
						<td className="wc-block-cart-item__total">
							<div
								className="wc-block-components-skeleton__element"
								style={ { width: '100%', height: '8px' } }
							></div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	);
};
