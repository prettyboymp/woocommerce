/**
 * External dependencies
 */

import { Button, TextControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useEffect, useState } from 'react';
import { isEmpty } from 'lodash';

/**
 * Internal dependencies
 */
import { useShipmentFormContext } from '../../context/shipment-form-context';
import ErrorLabel from '../user-interface/error-label';
import { EditIcon } from '../../utils/icons';
import ShipmentProviders from '../../data/shipment-providers';

export default function ShipmentTrackingNumberForm() {
	const [ trackingNumberTemp, setTrackingNumberTemp ] = useState( '' );
	const [ error, setError ] = useState< string | null >( null );
	const [ editMode, setEditMode ] = useState( false );
	const {
		trackingNumber,
		setTrackingNumber,
		shipmentProvider,
		setShipmentProvider,
		trackingUrl,
		setTrackingUrl,
	} = useShipmentFormContext();

	const handleTrackingNumberLookup = () => {
		setError( null );
		// TODO: For testing purposes, remove this before production.
		if ( trackingNumberTemp === '12345678' ) {
			setTrackingNumber( trackingNumberTemp );
			setShipmentProvider( 'ups' );
			setTrackingUrl( 'https://www.ups.com/track?tracknum=12345678' );
			setEditMode( false );
		} else {
			setError(
				__(
					'No information found for this tracking number. Check the number or enter the details manually.',
					'woocommerce'
				)
			);
		}
	};

	useEffect( () => {
		if ( isEmpty( trackingNumber ) ) {
			setEditMode( true );
		}
	}, [ trackingNumber ] );

	return (
		<>
			<p className="woocommerce-fulfillment-description">
				{ __(
					'Provide the shipment tracking number to find the shipment provider and tracking URL. Enter "12345678" to test the tracking number lookup.',
					'woocommerce'
				) }
			</p>
			{ editMode ? (
				<div className="woocommerce-fulfillment-input-container">
					<h4>{ __( 'Tracking Number', 'woocommerce' ) }</h4>
					<div className="woocommerce-fulfillment-input-group">
						<TextControl
							type="text"
							placeholder={ __(
								'Enter tracking number',
								'woocommerce'
							) }
							value={ trackingNumberTemp }
							onChange={ ( value ) => {
								setTrackingNumberTemp( value );
							} }
							__next40pxDefaultSize
							__nextHasNoMarginBottom
						/>
						<Button
							variant="secondary"
							text="Find info"
							onClick={ handleTrackingNumberLookup }
							__next40pxDefaultSize
						/>
					</div>
				</div>
			) : (
				<>
					<div className="woocommerce-fulfillment-input-container">
						<h4>{ __( 'Tracking Number', 'woocommerce' ) }</h4>
						<div className="woocommerce-fulfillment-input-group space-between">
							<span>{ trackingNumber }</span>
							<Button
								size="small"
								onClick={ () => {
									setEditMode( true );
								} }
							>
								<EditIcon />
							</Button>
						</div>
					</div>
					<div className="woocommerce-fulfillment-input-container">
						<h4>{ __( 'Provider', 'woocommerce' ) }</h4>
						<div className="woocommerce-fulfillment-input-group">
							<TextControl
								type="text"
								value={
									ShipmentProviders.find(
										( p ) => p.value === shipmentProvider
									)?.label ?? shipmentProvider
								}
								disabled
								onChange={ () => {} }
								__nextHasNoMarginBottom
								__next40pxDefaultSize
							/>
						</div>
					</div>
					<div className="woocommerce-fulfillment-input-container">
						<h4>{ __( 'Tracking URL', 'woocommerce' ) }</h4>
						<div className="woocommerce-fulfillment-input-group">
							<TextControl
								disabled
								type="text"
								value={ trackingUrl }
								onChange={ () => {} }
								__next40pxDefaultSize
								__nextHasNoMarginBottom
							/>
						</div>
					</div>
				</>
			) }
			{ error && <ErrorLabel error={ error } /> }
		</>
	);
}
