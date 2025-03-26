/**
 * External dependencies
 */
import {
	Card,
	CardBody,
	CheckboxControl,
	TextControl,
	TextareaControl,
	Button,
} from '@wordpress/components';
import { useState, useEffect } from 'react';
import { __, sprintf } from '@wordpress/i18n';
import { useDispatch, useSelect } from '@wordpress/data';
import { paymentGatewaysStore } from '@woocommerce/data';

/**
 * Internal dependencies
 */
import '../../settings-payments-body.scss';
import { PaymentSettingsLayout } from '~/settings-payments/components/payment-settings-layout';
import { PaymentSettingsSection } from '~/settings-payments/components/payment-settings-section';

interface CustomFieldRenderProps {
	customData: Record< string, unknown > | null;
	setCustomData: ( data: Record< string, unknown > ) => void;
	gatewaySettings: any;
}

interface OfflineGatewayFormProps {
	gatewayId: string;
	gatewayName: string;
	defaultTitle: string;
	pageDescription: string;
	customFields?: {
		render: ( args: CustomFieldRenderProps ) => React.ReactNode;
		prepareSaveData?: ( baseSettings: any, customData: any ) => any;
		processSettings?: ( settings: any ) => Record<string, unknown>;
	};
	additionalSections?: React.ReactNode;
}

export const OfflineGatewayForm = ( {
	gatewayId,
	gatewayName,
	defaultTitle,
	customFields,
	additionalSections,
}: OfflineGatewayFormProps ) => {
	const { createSuccessNotice, createErrorNotice } =
		useDispatch( 'core/notices' );
	const { gatewaySettings, isLoading } = useSelect(
		( select ) => ( {
			gatewaySettings:
				select( paymentGatewaysStore ).getPaymentGateway( gatewayId ),
			isLoading: ! select( paymentGatewaysStore ).hasFinishedResolution(
				'getPaymentGateway',
				[ gatewayId ]
			),
		} ),
		[ gatewayId ]
	);

	const { updatePaymentGateway, invalidateResolutionForStoreSelector } =
		useDispatch( paymentGatewaysStore );

	// Common state
	const [ enabled, setEnabled ] = useState( false );
	const [ title, setTitle ] = useState( '' );
	const [ description, setDescription ] = useState( '' );
	const [ instructions, setInstructions ] = useState( '' );
	const [ customData, setCustomData ] = useState< Record<
		string,
		unknown
	> | null >( null );

	useEffect( () => {
		if ( gatewaySettings ) {
			setEnabled( gatewaySettings.enabled || false );
			setTitle( gatewaySettings.settings?.title?.value || defaultTitle );
			setDescription( gatewaySettings.description || '' );
			setInstructions(
				gatewaySettings.settings?.instructions?.value || ''
			);

			if ( customFields?.processSettings ) {
				setCustomData( customFields.processSettings( gatewaySettings ) );
			}
		}
	}, [ gatewaySettings, defaultTitle, customFields ] );

	const saveSettings = () => {
		const baseSettings = {
			enabled,
			description,
			settings: {
				title,
				instructions,
			},
		};

		const dataToSave = customFields?.prepareSaveData
			? customFields.prepareSaveData( baseSettings, customData )
			: baseSettings;

		console.log( dataToSave );

		updatePaymentGateway( gatewayId, dataToSave )
			.then( () => {
				invalidateResolutionForStoreSelector( 'getPaymentGateway' );
				createSuccessNotice(
					__( 'Settings updated successfully', 'woocommerce' )
				);
			} )
			.catch( () => {
				createErrorNotice(
					__( 'Failed to update settings', 'woocommerce' )
				);
			} );
	};

	if ( isLoading ) {
		return <p>Loading settings...</p>;
	}

	return (
		<PaymentSettingsLayout>
			<PaymentSettingsSection
				id={ `${ gatewayId }-settings` }
				title={ __( 'Enable and customise', 'woocommerce' ) }
				description={ sprintf(
					// translators: %s: Gateway name.
					__(
						'Choose how you want to present %s payments to your customers during checkout.',
						'woocommerce'
					),
					gatewayName
				) }
			>
				<Card
					className={ `payment-settings-card__wrapper ${ gatewayId }-settings__wrapper` }
				>
					<CardBody className={ `${ gatewayId }-settings__body` }>
						<div className={ 'form-field' }>
							<CheckboxControl
								onChange={ ( value ) => setEnabled( value ) }
								checked={ enabled }
								// @ts-expect-error The label prop can be a string, however, the final consumer of this prop accepts ReactNode.
								label={
									<span>
										{ sprintf(
											// translators: %s: Gateway name.
											__( 'Enable %s', 'woocommerce' ),
											gatewayName
										) }
									</span>
								}
							/>
						</div>

						<div className={ 'form-field' }>
							<TextControl
								label={ __( 'Title', 'woocommerce' ) }
								help={ __(
									'This controls the title which the user sees during checkout.',
									'woocommerce'
								) }
								value={ title }
								onChange={ ( value ) => setTitle( value ) }
							/>
						</div>

						<div className={ 'form-field' }>
							<TextareaControl
								label={ __( 'Description', 'woocommerce' ) }
								help={ __(
									'Payment method description that the customer will see on your checkout.',
									'woocommerce'
								) }
								value={ description }
								onChange={ ( value ) =>
									setDescription( value )
								}
							/>
						</div>

						<div className={ 'form-field' }>
							<TextareaControl
								label={ __( 'Instructions', 'woocommerce' ) }
								help={ __(
									'Instructions that will be added to the thank you page and emails.',
									'woocommerce'
								) }
								value={ instructions }
								onChange={ ( value ) =>
									setInstructions( value )
								}
							/>
						</div>

						{ customFields?.render?.( {
							customData,
							setCustomData,
							gatewaySettings,
						} ) }
					</CardBody>
				</Card>
			</PaymentSettingsSection>

			{ additionalSections }

			<Card className={ 'payment-settings-card__wrapper' }>
				<CardBody className={ 'form__actions' }>
					<Button variant={ 'primary' } onClick={ saveSettings }>
						{ __( 'Save changes', 'woocommerce' ) }
					</Button>
				</CardBody>
			</Card>
		</PaymentSettingsLayout>
	);
};
