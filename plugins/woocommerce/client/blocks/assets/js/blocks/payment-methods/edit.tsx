/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
import { useEffect } from '@wordpress/element';
import { getPaymentMethods } from '@woocommerce/blocks-registry';
import type { PaymentMethodConfigInstance } from '@woocommerce/types';

/**
 * Internal dependencies
 */
import './style.scss';

export interface Attributes {
	showAsIcons: boolean;
	formattedPaymentMethods: Record< string, PaymentMethodConfigInstance >;
}

interface Props {
	attributes: Attributes;
	setAttributes: ( attributes: Record< string, unknown > ) => void;
}

const Edit = ( { attributes, setAttributes }: Props ) => {
	const { showAsIcons, formattedPaymentMethods } = attributes;
	const paymentMethods = getPaymentMethods();
	const blockProps = useBlockProps();

	useEffect( () => {
		setAttributes( {
			formattedPaymentMethods: paymentMethods,
		} );
	}, [ paymentMethods, setAttributes ] );

	return (
		<div { ...blockProps }>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'woocommerce' ) }>
					<ToggleControl
						label={ __( 'Show as icons', 'woocommerce' ) }
						checked={ showAsIcons }
						onChange={ ( value ) =>
							setAttributes( { showAsIcons: value } )
						}
					/>
				</PanelBody>
			</InspectorControls>

			<div className="wp-block-woocommerce-payment-methods">
				{ Object.keys( formattedPaymentMethods ).length === 0 ? (
					<p>
						<small>
							{ __(
								'No payment methods are currently active.',
								'woocommerce'
							) }
						</small>
					</p>
				) : (
					<div className="wc-block-payment-methods__content">
						<ul className="wc-block-payment-methods__list">
							{ Object.values( formattedPaymentMethods ).map(
								( method ) => {
									return (
										<li
											key={ method.name }
											className="wc-block-payment-methods__list-item"
										>
											{ showAsIcons &&
												method.icons &&
												method.icons[ 0 ] && (
													<img
														src={
															typeof method
																.icons[ 0 ] ===
															'string'
																? method
																		.icons[ 0 ]
																: method
																		.icons[ 0 ]
																		.src ||
																  ''
														}
														alt={ method.ariaLabel }
														className="wc-block-payment-methods__list-item-icon"
													/>
												) }
											{ ( ! showAsIcons ||
												! method.icons?.length ||
												! method.icons[ 0 ] ) && (
												<span className="wc-block-payment-methods__list-item-label">
													{ method.ariaLabel }
												</span>
											) }
										</li>
									);
								}
							) }
						</ul>
					</div>
				) }
			</div>
		</div>
	);
};

export default Edit;
