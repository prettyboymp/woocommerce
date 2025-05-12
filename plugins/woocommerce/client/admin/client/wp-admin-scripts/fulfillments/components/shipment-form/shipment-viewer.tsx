/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { isEmpty } from 'lodash';

/**
 * Internal dependencies
 */
import { useShipmentFormContext } from '../../context/shipment-form-context';
import ShipmentProviders from '../../data/shipment-providers';
import { CopyIcon, TruckIcon } from '../../utils/icons';
import FulfillmentCard from '../user-interface/fulfillments-card/card';
import MetaList from '../user-interface/meta-list/meta-list';

export default function ShipmentViewer() {
	const { shipmentProvider, trackingNumber, trackingUrl } =
		useShipmentFormContext();
	const isShipmentInformationProvided =
		! isEmpty( shipmentProvider ) &&
		! isEmpty( trackingNumber ) &&
		! isEmpty( trackingUrl );
	const shipmentProviderObject = ShipmentProviders.find(
		( p ) => p.value === shipmentProvider
	);

	return (
		<FulfillmentCard
			isCollapsable={ isShipmentInformationProvided }
			header={
				isShipmentInformationProvided ? (
					<>
						<img
							src={ shipmentProviderObject?.icon as string }
							alt={ shipmentProviderObject?.label as string }
						/>
						<h3>
							{ trackingNumber }{ ' ' }
							<CopyIcon copyText={ trackingNumber } />
						</h3>
					</>
				) : (
					<>
						<TruckIcon />
						<h3>
							{ __( 'No shipment information', 'woocommerce' ) }
						</h3>
					</>
				)
			}
		>
			{ isShipmentInformationProvided && (
				<MetaList
					metaList={ [
						{
							label: __( 'Tracking number', 'woocommerce' ),
							value: trackingNumber,
						},
						{
							label: __( 'Provider name', 'woocommerce' ),
							value:
								shipmentProviderObject?.label ??
								shipmentProvider,
						},
						{
							label: __( 'Tracking URL', 'woocommerce' ),
							value: trackingUrl,
						},
					] }
				/>
			) }
		</FulfillmentCard>
	);
}
