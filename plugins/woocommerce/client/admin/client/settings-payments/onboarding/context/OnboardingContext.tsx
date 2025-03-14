/**
 * External dependencies
 */
import {
	createContext,
	useContext,
	useCallback,
	useMemo,
	useState,
	useEffect,
} from '@wordpress/element';
import { useDispatch, useSelect } from '@wordpress/data';
import { woopaymentsOnboardingStore } from '@woocommerce/data';
import { getHistory, getNewPath } from '@woocommerce/navigation';

/**
 * Internal dependencies
 */
import {
	frontEndOnlySteps,
	getStepContent,
	getStepOrder,
} from '../providers/woopayments/steps';
import { WooPaymentsProviderOnboardingStep } from '../types';

/**
 * Internal dependencies
 */

interface OnboardingStepsResponse {
	steps: Array< {
		id: string;
		label: string;
		path: string;
		order: number;
		status: 'completed' | 'incomplete';
		dependencies: string[];
	} >;
}

interface OnboardingContextType {
	steps: WooPaymentsProviderOnboardingStep[];
	isLoading: boolean;
	currentStep: WooPaymentsProviderOnboardingStep | undefined;
	navigateToStep: ( stepKey: string ) => void;
	navigateToNextStep: () => void;
	getStepByKey: (
		stepKey: string
	) => WooPaymentsProviderOnboardingStep | undefined;
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
	const history = getHistory();

	// Use React state to manage steps and loading state
	const [ stateStoreSteps, setStateStoreSteps ] = useState<
		OnboardingStepsResponse[ 'steps' ]
	>( [] );
	const [ isLocalLoading, setIsLocalLoading ] = useState( true );

	// Initial data fetch from store
	const { storeSteps, isStoreLoading } = useSelect(
		( select ) => ( {
			storeSteps: select(
				woopaymentsOnboardingStore
			).getOnboardingSteps(),
			isStoreLoading: select(
				woopaymentsOnboardingStore
			).isOnboardingStepsRequestPending(),
		} ),
		[]
	);

	// Update local state when store data changes
	useEffect( () => {
		if ( ! isStoreLoading && storeSteps.length > 0 ) {
			setStateStoreSteps( storeSteps );
			setIsLocalLoading( false );
		}
	}, [ storeSteps, isStoreLoading ] );

	// Make UI refresh when plugin is installed.
	const { getOnboardingStepsSuccess, invalidateResolutionForStoreSelector } =
		useDispatch( woopaymentsOnboardingStore );

	const allSteps = useMemo(
		() => [ ...stateStoreSteps, ...frontEndOnlySteps ],
		[ stateStoreSteps ]
	)
		.map( ( step ) => ( {
			...step,
			content: getStepContent( step.id ),
			order: getStepOrder( step.id ),
		} ) )
		.sort( ( a, b ) => a.order - b.order );

	// Helper function to get step by key
	// useCallback is used to avoid re-rendering the tree each time the component is rendered
	const getStepByKey = useCallback(
		( stepKey: string ) => {
			return allSteps.find( ( step ) => step.id === stepKey );
		},
		[ allSteps ]
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

	// Helper function to check if all dependencies of a step are completed
	const areStepDependenciesCompleted = useCallback(
		( step: WooPaymentsProviderOnboardingStep ) => {
			if ( ! step.dependencies || step.dependencies.length === 0 ) {
				return true;
			}

			return step.dependencies.every( ( dependencyId ) => {
				const dependencyStep = getStepByKey( dependencyId );
				return dependencyStep?.status === 'completed';
			} );
		},
		[ getStepByKey ]
	);

	// Find the first incomplete step with completed dependencies
	const currentStep = allSteps.find(
		( step ) =>
			step.status === 'incomplete' && areStepDependenciesCompleted( step )
	);

	const navigateToNextStep = useCallback( () => {
		const currentStepIndex = allSteps.findIndex(
			( step ) => step.id === currentStep?.id
		);
		if ( currentStepIndex !== -1 ) {
			// Mark current step as completed
			if ( currentStep?.status === 'incomplete' ) {
				// Is this a BE step?
				const isBackendStep = stateStoreSteps.find(
					( s ) => s.id === currentStep.id
				);

				if ( isBackendStep ) {
					const updatedSteps = stateStoreSteps.map( ( s ) =>
						s.id === currentStep.id
							? { ...s, status: 'completed' as const }
							: s
					);

					// Update both local state and the store
					setStateStoreSteps( updatedSteps );
					getOnboardingStepsSuccess( updatedSteps );
				}
			}

			const nextStep = allSteps[ currentStepIndex + 1 ];
			if ( nextStep ) {
				navigateToStep( nextStep.id );
			}
		}
	}, [
		currentStep,
		stateStoreSteps,
		allSteps,
		navigateToStep,
		getOnboardingStepsSuccess,
	] );

	const refreshOnboardingSteps = useCallback( () => {
		invalidateResolutionForStoreSelector( 'getOnboardingSteps' );
	}, [ invalidateResolutionForStoreSelector ] );

	return (
		<OnboardingContext.Provider
			value={ {
				steps: allSteps,
				isLoading: isLocalLoading,
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
