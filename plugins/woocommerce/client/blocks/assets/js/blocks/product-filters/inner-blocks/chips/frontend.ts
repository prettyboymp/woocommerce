/**
 * External dependencies
 */
import { getContext, store } from '@wordpress/interactivity';

export type ChipsContext = {
	showAll: boolean;
};

// Stores are locked to prevent 3PD usage until the API is stable.
const universalLock =
	'I acknowledge that using a private store means my plugin will inevitably break on the next store release.';

store(
	'woocommerce/product-filters',
	{
		actions: {
			showAllChips: () => {
				const context = getContext< ChipsContext >();
				context.showAll = true;
			},
		},
	},
	{ lock: universalLock }
);
