/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { CheckboxControl } from '@wordpress/components';
import { useMemo } from 'react';

/**
 * Internal dependencies
 */
import { OfflineGatewayForm } from './components/offline-gateway-form';
import { ShippingMethodsSelect } from '~/settings-payments/components/shipping-methods-select';

const CodCustomFields = ( {
	customData,
	setCustomData,
	gatewaySettings,
}: {
	customData: {
		enableForMethods: string[];
		enableForVirtual: boolean;
	};
	setCustomData: ( data: {
		enableForMethods: string[];
		enableForVirtual: boolean;
	} ) => void;
	gatewaySettings: {
		settings?: {
			enable_for_methods?: {
				options?: Record< string, Record< string, string > >;
			};
		};
	};
} ) => {
	const { enableForMethods, enableForVirtual } = customData;

	const methodOptions = Object.entries(
		gatewaySettings?.settings?.enable_for_methods?.options || {}
	).flatMap( ( [ groupLabel, groupOptions ]: [ string, any ] ) =>
		Object.entries( groupOptions ).map(
			( [ value, label ]: [ string, any ] ) => ( {
				label: label.replace( /&quot;/g, '"' ),
				value,
			} )
		)
	);

	return (
		<>
			<div className={ 'form-field' }>
				<ShippingMethodsSelect
					value={ enableForMethods }
					onChange={ ( selected ) =>
						setCustomData( {
							...customData,
							enableForMethods: selected,
						} )
					}
					options={ methodOptions }
				/>
			</div>

			<div className={ 'form-field' }>
				<CheckboxControl
					onChange={ ( value ) =>
						setCustomData( {
							...customData,
							enableForVirtual: value,
						} )
					}
					checked={ enableForVirtual }
					help={ __(
						'Accept cash on delivery if the order is virtual',
						'woocommerce'
					) }
					// @ts-expect-error The label prop can be a string, however, the final consumer of this prop accepts ReactNode.
					label={
						<span>
							{ __( 'Accept for virtual orders', 'woocommerce' ) }
						</span>
					}
				/>
			</div>
		</>
	);
};

interface GatewaySettings {
	settings?: {
		enable_for_methods?: {
			value?: string;
			options?: Record<string, Record<string, string>>;
		};
		enable_for_virtual?: {
			value?: string;
		};
	};
}

export const SettingsPaymentsCod = () => {
	const customFields = useMemo(
		() => ( {
			render: CodCustomFields,
			processSettings: ( settings: GatewaySettings ) => {
				return {
					enableForMethods: settings.settings?.enable_for_methods
						?.value
						? settings.settings.enable_for_methods.value.split(
								','
						  )
						: [],
					enableForVirtual:
						settings.settings?.enable_for_virtual?.value === 'yes',
				};
			},
			prepareSaveData: ( baseSettings, customData ) => ( {
				...baseSettings,
				settings: {
					...baseSettings.settings,
					enable_for_methods: customData.enableForMethods.join( ',' ),
					enable_for_virtual: customData.enableForVirtual
						? 'yes'
						: 'no',
				},
			} ),
		} ),
		[]
	);

	return (
		<OfflineGatewayForm
			gatewayId="cod"
			gatewayName="cash on delivery"
			defaultTitle="Cash on delivery"
			customFields={ customFields }
		/>
	);
};

export default SettingsPaymentsCod;
