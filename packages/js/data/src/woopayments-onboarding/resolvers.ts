/**
 * External dependencies
 */
import { apiFetch } from '@wordpress/data-controls';

/**
 * Internal dependencies
 */
import { WC_ADMIN_NAMESPACE } from '../constants';
import { OnboardingStepsResponse } from './types';

export function* getOnboardingSteps() {
	const response: OnboardingStepsResponse = yield apiFetch( {
		path: `${ WC_ADMIN_NAMESPACE }/settings/payments/onboarding-steps`,
	} );

	return response;
}
