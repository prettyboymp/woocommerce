/**
 * Internal dependencies
 */
import { OfflineGatewayForm } from './components/offline-gateway-form';
import { useMemo } from 'react';

export const SettingsPaymentsCheque = () => {
	const customFields = useMemo(
		() => ( {
			render: () => null,
		} ),
		[]
	);

	return (
		<OfflineGatewayForm
			gatewayId="cheque"
			gatewayName="cheque"
			defaultTitle="Cheque payments"
			customFields={ customFields }
		/>
	);
};

export default SettingsPaymentsCheque;
