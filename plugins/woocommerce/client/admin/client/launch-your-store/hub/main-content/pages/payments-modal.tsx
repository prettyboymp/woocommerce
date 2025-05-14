/**
 * External dependencies
 */
import { useEffect, useState } from '@wordpress/element';
import { useLocation } from 'react-router-dom';

/**
 * Internal dependencies
 */
import { OnboardingProvider } from '~/settings-payments/onboarding/providers/woopayments/data/onboarding-context';
import WooPaymentsOnboarding from '~/settings-payments/onboarding/providers/woopayments/components/onboarding';
import Modal from '~/settings-payments/onboarding/components/modal';

export const PaymentsModal = () => {
	const [ isOpen, setIsOpen ] = useState( true );
	const location = useLocation();

	// Reset isOpen to true only when the pathname includes "/woopayments/onboarding"
	useEffect( () => {
		if ( location.pathname.includes( '/woopayments/onboarding' ) ) {
			setIsOpen( true );
		}
	}, [ location.pathname ] );

	if ( ! isOpen ) {
		return null;
	}

	return (
		<Modal setIsOpen={ setIsOpen }>
			<OnboardingProvider
				closeModal={ () => setIsOpen( false ) }
				source="launch-your-store"
			>
				<WooPaymentsOnboarding />
			</OnboardingProvider>
		</Modal>
	);
};
