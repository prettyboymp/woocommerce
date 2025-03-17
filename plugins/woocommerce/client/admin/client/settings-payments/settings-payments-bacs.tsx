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
import { useState } from 'react';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import './settings-payments-body.scss';
import './settings-payments-form.scss';
import { PaymentSettingsLayout } from '~/settings-payments/components/payment-settings-layout';
import { PaymentSettingsSection } from '~/settings-payments/components/payment-settings-section';

/**
 * Component for managing BACS (Direct Bank Transfer) payment gateway settings.
 */
export const SettingsPaymentsBacs = () => {
	const [ enabled, setEnabled ] = useState( true );
	const [ title, setTitle ] = useState( 'Check payments' );
	const [ description, setDescription ] = useState(
		'Make your payment directly into our bank account. Please use your Order ID as the payment reference. Your order will not be shipped until the funds have cleared in our account.'
	);
	const [ instructions, setInstructions ] = useState( '' );

	return (
		<PaymentSettingsLayout>
			<PaymentSettingsSection
				id={ 'bacs-settings' }
				title={ __( 'Enable and customise', 'woocommerce' ) }
				description={ __(
					'Choose how you want to present bank transfer to your customers during checkout.',
					'woocommerce'
				) }
			>
				<Card className="payment-settings-card__wrapper bacs-settings__wrapper">
					<CardBody className="bacs-settings__body">
						<div className={ 'form-field' }>
							<CheckboxControl
								onChange={ () => null }
								checked={ enabled }
								// @ts-expect-error The label prop can be a string, however, the final consumer of this prop accepts ReactNode.
								label={
									<span>
										{ __(
											'Enable direct bank transfers',
											'woocommerce'
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
								onChange={ () => null }
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
								onChange={ () => null }
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
								onChange={ () => null }
							/>
						</div>
					</CardBody>
				</Card>
			</PaymentSettingsSection>
			<PaymentSettingsSection
				id={ 'bacs-account-details' }
				title={ __( 'Account details', 'woocommerce' ) }
				description={ __(
					'Choose which bank account you would like to receive direct bank transfers into.',
					'woocommerce'
				) }
			>
				<Card className={ 'bacs-account-details__wrapper' }>
					<CardBody className={ 'bacs-account-details__body' }>
						<div>Hi</div>
					</CardBody>
				</Card>
			</PaymentSettingsSection>
			<Button isPrimary onClick={ () => null }>
				{ __( 'Save changes', 'woocommerce' ) }
			</Button>
		</PaymentSettingsLayout>
	);
};

export default SettingsPaymentsBacs;
