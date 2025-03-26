/**
 * External dependencies
 */
import { useState, useMemo } from 'react';
import { __ } from '@wordpress/i18n';
import { Card, CardBody } from '@wordpress/components';

/**
 * Internal dependencies
 */
import { OfflineGatewayForm } from './components/offline-gateway-form';
import { PaymentSettingsSection } from './components/payment-settings-section';
import { BacsAccountDetailsTable } from './components/bacs-account-details-table';

interface BacsAccountDetails {
	account_name: string;
	account_number: string;
	bank_name: string;
	sort_code: string;
	iban: string;
	bic: string;
}

export const SettingsPaymentsBacs = () => {
	const [ accountDetails, setAccountDetails ] = useState<
		BacsAccountDetails[]
	>( [] );

	const customFields = useMemo(() => ({
		render: () => null,
		processSettings: ( settings: any ) => {
			if ( Array.isArray( settings.account_details ) ) {
				setAccountDetails( settings.account_details );
			} else {
				setAccountDetails( [] );
			}
			return {};
		},
		prepareSaveData: ( baseSettings: any ) => {
			return {
				...baseSettings,
				accountDetails,
			};
		},
	}), []);

	const accountDetailsSection = (
		<PaymentSettingsSection
			id={ 'bacs-account-details' }
			title={ __( 'Account details', 'woocommerce' ) }
			description={ __(
				'Choose which bank account you would like to receive direct bank transfers into.',
				'woocommerce'
			) }
		>
			<Card
				className={
					'payment-settings-card__wrapper bacs-account-details__wrapper'
				}
			>
				<CardBody className={ 'bacs-account-details__body' }>
					<div className={ 'form-field' }>
						<BacsAccountDetailsTable
							accounts={ accountDetails }
							setAccounts={ setAccountDetails }
						/>
					</div>
				</CardBody>
			</Card>
		</PaymentSettingsSection>
	);

	return (
		<OfflineGatewayForm
			gatewayId="bacs"
			gatewayName="bank transfer"
			defaultTitle="Direct bank transfer"
			customFields={ customFields }
			additionalSections={ accountDetailsSection }
		/>
	);
};

export default SettingsPaymentsBacs;
