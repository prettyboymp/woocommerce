/**
 * External dependencies
 */
import { createContext, useContext } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import {
	WooPaymentsOnboardingStepContent,
	woopaymentsOnboardingStore,
} from '@woocommerce/data';

/**
 * Internal dependencies
 */

interface OnboardingContextType {
	steps: WooPaymentsOnboardingStepContent[];
	isLoading: boolean;
}

const OnboardingContext = createContext< OnboardingContextType >( {
	steps: [],
	isLoading: true,
} );

export const useOnboardingContext = () => useContext( OnboardingContext );

export const OnboardingProvider: React.FC< { children: React.ReactNode } > = ( {
	children,
} ) => {
	const { steps, isLoading } = useSelect(
		( select ) => ( {
			steps: select( woopaymentsOnboardingStore ).getOnboardingSteps(),
			isLoading: select(
				woopaymentsOnboardingStore
			).isOnboardingStepsRequestPending(),
		} ),
		[]
	);

	return (
		<OnboardingContext.Provider value={ { steps, isLoading } }>
			{ children }
		</OnboardingContext.Provider>
	);
};
