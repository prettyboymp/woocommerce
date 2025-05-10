/**
 * External dependencies
 */
import { ComboboxControl, TextControl } from '@wordpress/components';
import { ComboboxControlOption } from '@wordpress/components/build-types/combobox-control/types';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { useShipmentFormContext } from '../../context/shipment-form-context';
import ShipmentProviders from '../../data/shipment-providers';

const SearchIcon = () => (
	<svg
		width="12"
		height="12"
		viewBox="0 0 12 12"
		fill="none"
		xmlns="http://www.w3.org/2000/svg"
	>
		<path
			d="M6.75 0.75C4.275 0.75 2.25 2.775 2.25 5.25C2.25 6.3 2.625 7.275 3.225 8.025L0.375 10.875L1.2 11.7L4.05 8.85C4.8 9.45 5.775 9.825 6.825 9.825C9.3 9.825 11.325 7.8 11.325 5.325C11.325 2.85 9.225 0.75 6.75 0.75ZM6.75 8.625C4.875 8.625 3.375 7.125 3.375 5.25C3.375 3.375 4.875 1.875 6.75 1.875C8.625 1.875 10.125 3.375 10.125 5.25C10.125 7.125 8.625 8.625 6.75 8.625Z"
			fill="#1E1E1E"
		/>
	</svg>
);

const ShippingProviderListItem = ( {
	item,
}: {
	item: ComboboxControlOption;
} ) => {
	return (
		<div
			className={ [
				'woocommerce-fulfillment-shipping-provider-list-item',
				'woocommerce-fulfillment-shipping-provider-list-item-' +
					item.value,
			].join( ' ' ) }
		>
			{ item.icon && (
				<div className="woocommerce-fulfillment-shipping-provider-list-item-icon">
					<img src={ item.icon } alt={ item.label } />
				</div>
			) }
			<div className="woocommerce-fulfillment-shipping-provider-list-item-label">
				{ item.label }
			</div>
		</div>
	);
};

export default function ShipmentManualEntryForm() {
	const {
		trackingNumber,
		setTrackingNumber,
		shipmentProvider,
		setShipmentProvider,
		trackingUrl,
		setTrackingUrl,
	} = useShipmentFormContext();
	return (
		<>
			<p className="woocommerce-fulfillment-description">
				{ __(
					'Provide the shipment information for this fulfillment.',
					'woocommerce'
				) }
			</p>
			<div className="woocommerce-fulfillment-input-container">
				<h4>{ __( 'Tracking Number', 'woocommerce' ) }</h4>
				<div className="woocommerce-fulfillment-input-group">
					<TextControl
						type="text"
						placeholder={ __(
							'Enter tracking number',
							'woocommerce'
						) }
						value={ trackingNumber }
						onChange={ ( value: string ) => {
							setTrackingNumber( value );
						} }
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</div>
			</div>
			<div className="woocommerce-fulfillment-input-container">
				<h4>{ __( 'Provider', 'woocommerce' ) }</h4>
				<div className="woocommerce-fulfillment-input-group">
					<ComboboxControl
						__experimentalRenderItem={ ( { item } ) => (
							<ShippingProviderListItem item={ item } />
						) }
						allowReset={ false }
						hideLabelFromVision
						__next40pxDefaultSize
						value={ shipmentProvider }
						options={ ShipmentProviders }
						onChange={ ( value ) => {
							setShipmentProvider( value as string );
						} }
						__nextHasNoMarginBottom
					/>
					<div className="woocommerce-fulfillment-shipment-provider-search-icon">
						<SearchIcon />
					</div>
				</div>
			</div>
			<div className="woocommerce-fulfillment-input-container">
				<h4>{ __( 'Tracking URL', 'woocommerce' ) }</h4>
				<div className="woocommerce-fulfillment-input-group">
					<TextControl
						type="text"
						placeholder={ __(
							'Enter tracking URL',
							'woocommerce'
						) }
						value={ trackingUrl }
						onChange={ ( value: string ) => {
							setTrackingUrl( value );
						} }
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</div>
			</div>
		</>
	);
}
