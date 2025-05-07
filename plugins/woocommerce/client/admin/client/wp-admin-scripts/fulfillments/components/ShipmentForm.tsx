/**
 * External dependencies
 */
import { Button, CheckboxControl, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useState } from 'react';

const TruckIcon = () => (
	<svg
		width="18"
		height="14"
		viewBox="0 0 18 14"
		fill="none"
		xmlns="http://www.w3.org/2000/svg"
	>
		<path
			fillRule="evenodd"
			clipRule="evenodd"
			d="M0.5 1.75C0.5 0.783502 1.2835 0 2.25 0L12.5 0V3H14.5607L18 6.43934V8.75C18 9.7165 17.2165 10.5 16.25 10.5H16.2377C16.2458 10.5822 16.25 10.6656 16.25 10.75C16.25 12.1307 15.1307 13.25 13.75 13.25C12.3693 13.25 11.25 12.1307 11.25 10.75C11.25 10.6656 11.2542 10.5822 11.2623 10.5H7.23766C7.24582 10.5822 7.25 10.6656 7.25 10.75C7.25 12.1307 6.13071 13.25 4.75 13.25C3.36929 13.25 2.25 12.1307 2.25 10.75C2.25 10.6656 2.25418 10.5822 2.26234 10.5H0.5V1.75ZM11 9V1.5H2.25C2.11193 1.5 2 1.61193 2 1.75V9H2.96464C3.41837 8.53716 4.05065 8.25 4.75 8.25C5.44935 8.25 6.08163 8.53716 6.53536 9H11ZM15.5354 9H16.25C16.3881 9 16.5 8.88807 16.5 8.75V7.06066L13.9393 4.5H12.5V8.58446C12.8677 8.37174 13.2946 8.25 13.75 8.25C14.4493 8.25 15.0816 8.53716 15.5354 9ZM3.7815 10.5C3.76094 10.5799 3.75 10.6637 3.75 10.75C3.75 11.3023 4.19772 11.75 4.75 11.75C5.30228 11.75 5.75 11.3023 5.75 10.75C5.75 10.6637 5.73906 10.5799 5.7185 10.5C5.60749 10.0687 5.21596 9.75 4.75 9.75C4.28404 9.75 3.89251 10.0687 3.7815 10.5ZM12.7815 10.5C12.7609 10.5799 12.75 10.6637 12.75 10.75C12.75 11.3023 13.1977 11.75 13.75 11.75C14.3023 11.75 14.75 11.3023 14.75 10.75C14.75 10.6637 14.7391 10.5799 14.7185 10.5C14.7144 10.4841 14.7099 10.4683 14.705 10.4526C14.5784 10.0456 14.1987 9.75 13.75 9.75C13.284 9.75 12.8925 10.0687 12.7815 10.5Z"
			fill="#1E1E1E"
		/>
	</svg>
);

export default function ShipmentForm() {
	const [ selectedOption, setSelectedOption ] = useState( 'tracking-number' );
	const [ trackingNumber, setTrackingNumber ] = useState( '' );
	return (
		<div className="woocommerce-fulfillment-shipment-form">
			<div className="woocommerce-fulfillment-shipment-form-header">
				<TruckIcon />
				<h3>{ __( 'Shipment Information', 'woocommerce' ) }</h3>
			</div>

			<div className="woocommerce-fulfillment-shipment-information-options">
				<div className="woocommerce-fulfillment-shipment-information-option-tracking-number">
					<CheckboxControl
						type="radio"
						name="tracking-number"
						checked={ selectedOption === 'tracking-number' }
						onChange={ ( value ) =>
							value && setSelectedOption( 'tracking-number' )
						}
						label={ __( 'Tracking Number', 'woocommerce' ) }
						__nextHasNoMarginBottom
					/>
					{ selectedOption === 'tracking-number' && (
						<>
							<p className="woocommerce-fulfillment-description">
								{ __(
									'Provide the shipment tracking number to find the shipment provider and tracking URL.',
									'woocommerce'
								) }
							</p>
							<div className="woocommerce-fulfillment-input-container">
								<h4>
									{ __( 'Tracking Number', 'woocommerce' ) }
								</h4>
								<div className="woocommerce-fulfillment-input-group">
									<TextControl
										type="text"
										placeholder={ __(
											'Enter tracking number',
											'woocommerce'
										) }
										value={ trackingNumber }
										onChange={ ( value ) => {
											setTrackingNumber( value );
										} }
									/>
									<Button
										variant="secondary"
										text="Find info"
									/>
								</div>
							</div>
						</>
					) }
				</div>
				<div className="woocommerce-fulfillment-shipment-information-option-manual-entry">
					<CheckboxControl
						type="radio"
						name="manual-entry"
						checked={ selectedOption === 'manual-entry' }
						onChange={ ( value ) =>
							value && setSelectedOption( 'manual-entry' )
						}
						label={ __( 'Enter manually', 'woocommerce' ) }
						__nextHasNoMarginBottom
					/>
				</div>
				<div className="woocommerce-fulfillment-shipment-information-option-no-info">
					<CheckboxControl
						type="radio"
						name="no-info"
						checked={ selectedOption === 'no-info' }
						onChange={ ( value ) =>
							value && setSelectedOption( 'no-info' )
						}
						label={ __( 'No shipment information', 'woocommerce' ) }
						__nextHasNoMarginBottom
					/>
				</div>
			</div>
		</div>
	);
}
