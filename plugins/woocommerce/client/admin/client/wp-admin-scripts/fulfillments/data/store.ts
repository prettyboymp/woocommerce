/**
 * External dependencies
 */

import apiFetch from '@wordpress/api-fetch';
import { createReduxStore, register } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { Order } from './types';

export const STORE_NAME = 'order/fulfillments';

// --- Default State
const DEFAULT_STATE: {
	orderMap: Record< number, Order >;
	loadingMap: Record< number, boolean >;
	errorMap: Record< number, string | null >;
} = {
	orderMap: {},
	loadingMap: {},
	errorMap: {},
};

// --- Actions
const actions = {
	setOrder( orderId: number, order: Order ) {
		return {
			type: 'SET_ORDER' as const,
			orderId,
			order,
		};
	},

	setLoading( orderId: number, isLoading: boolean ) {
		return {
			type: 'SET_LOADING' as const,
			orderId,
			isLoading,
		};
	},

	setError( orderId: number, error: string | null ) {
		return {
			type: 'SET_ERROR' as const,
			orderId,
			error,
		};
	},
};

type Action = ReturnType<
	| typeof actions.setOrder
	| typeof actions.setLoading
	| typeof actions.setError
>;

// --- Reducer
function reducer( state = DEFAULT_STATE, action: Action ) {
	switch ( action.type ) {
		case 'SET_ORDER':
			return {
				...state,
				orderMap: {
					...state.orderMap,
					[ action.orderId ]: action.order,
				},
			};

		case 'SET_LOADING':
			return {
				...state,
				loadingMap: {
					...state.loadingMap,
					[ action.orderId ]: action.isLoading,
				},
			};

		case 'SET_ERROR':
			return {
				...state,
				errorMap: {
					...state.errorMap,
					[ action.orderId ]: action.error,
				},
			};

		default:
			return state;
	}
}

// --- Selectors
const selectors = {
	getOrder( state: typeof DEFAULT_STATE, orderId: number ) {
		return state.orderMap[ orderId ] || null;
	},

	isOrderLoading( state: typeof DEFAULT_STATE, orderId: number ) {
		return !! state.loadingMap[ orderId ];
	},

	getOrderError( state: typeof DEFAULT_STATE, orderId: number ) {
		return state.errorMap[ orderId ] || null;
	},
};

// --- Resolvers
const resolvers = {
	getOrder:
		( orderId: number ) =>
		async ( { dispatch }: { dispatch: typeof actions } ) => {
			dispatch.setLoading( orderId, true );
			dispatch.setError( orderId, null );

			try {
				const order: Order = await apiFetch( {
					path: `/wc/v3/orders/${ orderId }`,
					method: 'GET',
				} );
				dispatch.setOrder( orderId, order );
			} catch ( error ) {
				dispatch.setError(
					orderId,
					( error as any ).message || 'Failed to load order' // eslint-disable-line @typescript-eslint/no-explicit-any
				);
			} finally {
				dispatch.setLoading( orderId, false );
			}
		},
};

// --- Store Registration

const store = createReduxStore( STORE_NAME, {
	reducer,
	actions,
	selectors,
	resolvers,
} );

register( store );

export const FulfillmentStore = store;
