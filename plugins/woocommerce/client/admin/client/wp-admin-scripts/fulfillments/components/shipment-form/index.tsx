/**
 * External dependencies
 */
import { CheckboxControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useState } from 'react';

/**
 * Internal dependencies
 */
import ShipmentTrackingNumberForm from './shipment-tracking-number-form';
import ShipmentManualEntryForm from './shipment-manual-entry-form';
import { TruckIcon } from '../../utils/icons';
import FulfillmentCard from '../user-interface/fulfillments-card/card';

export default function ShipmentForm() {
	const [ selectedOption, setSelectedOption ] = useState( 'tracking-number' );
	const randomRadioGroupName = `woocommerce-fulfillment-shipment-form-${ Math.random() }`;

	return (
		<FulfillmentCard
			isCollapsable={ false }
			header={
				<>
					<TruckIcon />
					<h3>{ __( 'Shipment Information', 'woocommerce' ) }</h3>
				</>
			}
		>
			<div className="woocommerce-fulfillment-shipment-information-options">
				<div className="woocommerce-fulfillment-shipment-information-option-tracking-number">
					<CheckboxControl
						type="radio"
						name={ randomRadioGroupName }
						value={ 'tracking-number' }
						checked={ selectedOption === 'tracking-number' }
						onChange={ ( value ) =>
							value && setSelectedOption( 'tracking-number' )
						}
						label={ __( 'Tracking Number', 'woocommerce' ) }
						__nextHasNoMarginBottom
					/>
					{ selectedOption === 'tracking-number' && (
						<ShipmentTrackingNumberForm />
					) }
				</div>
				<div className="woocommerce-fulfillment-shipment-information-option-manual-entry">
					<CheckboxControl
						type="radio"
						name={ randomRadioGroupName }
						value={ 'manual-entry' }
						checked={ selectedOption === 'manual-entry' }
						onChange={ ( value ) =>
							value && setSelectedOption( 'manual-entry' )
						}
						label={ __( 'Enter manually', 'woocommerce' ) }
						__nextHasNoMarginBottom
					/>
					{ selectedOption === 'manual-entry' && (
						<ShipmentManualEntryForm />
					) }
				</div>
				<div className="woocommerce-fulfillment-shipment-information-option-no-info">
					<CheckboxControl
						type="radio"
						name={ randomRadioGroupName }
						value={ 'no-info' }
						checked={ selectedOption === 'no-info' }
						onChange={ ( value ) =>
							value && setSelectedOption( 'no-info' )
						}
						label={ __( 'No shipment information', 'woocommerce' ) }
						__nextHasNoMarginBottom
					/>
				</div>
			</div>
		</FulfillmentCard>
	);
}
