/**
 * External dependencies
 */
import { createContext, useContext, useCallback } from '@wordpress/element';
import { useDispatch, useSelect } from '@wordpress/data';
import {
	WooPaymentsOnboardingStepContent,
	woopaymentsOnboardingStore,
} from '@woocommerce/data';
import { useLocation } from 'react-router-dom';
import { getHistory, getNewPath } from '@woocommerce/navigation';

/**
 * Internal dependencies
 */

interface OnboardingContextType {
	steps: WooPaymentsOnboardingStepContent[];
	isLoading: boolean;
	currentStep: WooPaymentsOnboardingStepContent | undefined;
	navigateToStep: ( stepKey: string ) => void;
	navigateToNextStep: () => void;
	getStepByKey: (
		stepKey: string
	) => WooPaymentsOnboardingStepContent | undefined;
	refreshOnboardingSteps: () => void;
}

const OnboardingContext = createContext< OnboardingContextType >( {
	steps: [],
	isLoading: true,
	currentStep: undefined,
	navigateToStep: () => undefined,
	navigateToNextStep: () => undefined,
	getStepByKey: () => undefined,
	refreshOnboardingSteps: () => undefined,
} );

export const useOnboardingContext = () => useContext( OnboardingContext );

export const OnboardingProvider: React.FC< { children: React.ReactNode } > = ( {
	children,
} ) => {
	const location = useLocation();
	const history = getHistory();

	const { steps, isLoading } = useSelect(
		( select ) => ( {
			steps: select( woopaymentsOnboardingStore ).getOnboardingSteps(),
			isLoading: select(
				woopaymentsOnboardingStore
			).isOnboardingStepsRequestPending(),
		} ),
		[]
	);

	// Make UI refresh when plugin is installed.
	const { invalidateResolutionForStoreSelector } = useDispatch(
		woopaymentsOnboardingStore
	);

	// Extract step key from the path
	const pathParts = location.pathname.split( '/' );
	const stepFromPath = pathParts[ pathParts.length - 1 ];

	// Helper function to get step by key
	// useCallback is used to avoid re-rendering the tree each time the component is rendered
	const getStepByKey = useCallback(
		( stepKey: string ) => {
			return steps.find( ( step ) => step.key === stepKey );
		},
		[ steps ]
	);

	// Navigation helper
	// useCallback is used to avoid re-rendering the tree each time the component is rendered
	const navigateToStep = useCallback(
		( stepKey: string ) => {
			const step = getStepByKey( stepKey );
			if ( step?.path ) {
				const newPath = getNewPath( { path: step.path }, step.path, {
					page: 'wc-settings',
					tab: 'checkout',
				} );
				history.push( newPath );
			}
		},
		[ getStepByKey, history ]
	);

	const currentStep =
		getStepByKey( stepFromPath ) ||
		steps
			.sort( ( a, b ) => a.order - b.order )
			.find( ( step ) => step.status === 'incomplete' );

	const navigateToNextStep = useCallback( () => {
		const currentStepIndex = steps.findIndex(
			( step ) => step.key === currentStep?.key
		);
		if ( currentStepIndex !== -1 ) {
			const nextStep = steps[ currentStepIndex + 1 ];
			if ( nextStep ) {
				navigateToStep( nextStep.key );
			}
		}
	}, [ currentStep, steps, navigateToStep ] );

	const refreshOnboardingSteps = useCallback( () => {
		invalidateResolutionForStoreSelector( 'getOnboardingSteps' );
	}, [ invalidateResolutionForStoreSelector ] );

	return (
		<OnboardingContext.Provider
			value={ {
				steps,
				isLoading,
				currentStep,
				navigateToStep,
				navigateToNextStep,
				getStepByKey,
				refreshOnboardingSteps,
			} }
		>
			{ children }
		</OnboardingContext.Provider>
	);
};
