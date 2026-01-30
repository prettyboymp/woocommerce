/**
 * External dependencies
 */
import {
	getElement,
	store,
	getContext,
	getConfig,
} from '@wordpress/interactivity';
import '@woocommerce/stores/woocommerce/product-data';
import type { ProductDataStore } from '@woocommerce/stores/woocommerce/product-data';
import type {
	ProductData,
	WooCommerceConfig,
} from '@woocommerce/stores/woocommerce/cart';
import { sanitizeHTML } from '@woocommerce/sanitize';

// Stores are locked to prevent 3PD usage until the API is stable.
const universalLock =
	'I acknowledge that using a private store means my plugin will inevitably break on the next store release.';

const { state: productDataState } = store< ProductDataStore >(
	'woocommerce/product-data',
	{},
	{ lock: universalLock }
);

// Cache for fetched variation data (lazy loading)
const variationDataCache: Record< number, ProductData > = {};

// Track pending requests to prevent duplicate fetches
// Using window object to ensure singleton across multiple bundles
declare global {
	interface Window {
		__wcVariationPendingRequests?: Record< number, Promise< ProductData > >;
	}
}

if ( ! window.__wcVariationPendingRequests ) {
	window.__wcVariationPendingRequests = {};
}

/**
 * Fetch variation data from Store API.
 *
 * @param variationId The variation ID to fetch
 * @return Promise resolving to the variation data
 */
async function fetchVariationData(
	variationId: number
): Promise< ProductData > {
	// Check if already fetching this variation (deduplicate requests)
	if ( window.__wcVariationPendingRequests?.[ variationId ] ) {
		return window.__wcVariationPendingRequests[ variationId ];
	}

	// Check cache first
	if ( variationDataCache[ variationId ] ) {
		return variationDataCache[ variationId ];
	}

	// Get REST API root and nonce from WordPress globals
	const apiRoot =
		( window as unknown as { wpApiSettings?: { root?: string } } )
			?.wpApiSettings?.root || '/wp-json/';
	const nonce =
		( window as unknown as { wpApiSettings?: { nonce?: string } } )
			?.wpApiSettings?.nonce || '';

	// Fetch from API using native fetch
	const fetchPromise = fetch( `${ apiRoot }wc/store/v1/products/${ variationId }`, {
		method: 'GET',
		headers: {
			'Content-Type': 'application/json',
			'X-WP-Nonce': nonce,
		},
		credentials: 'same-origin',
	} )
		.then( ( response ) => {
			if ( ! response.ok ) {
				throw new Error( `HTTP error! status: ${ response.status }` );
			}
			return response.json() as Promise< {
				id: number;
				sku: string;
				price_html: string;
				availability_html: string;
				weight?: string;
				dimensions?: { length: string; width: string; height: string };
				image?: { id: number };
				add_to_cart?: {
					minimum?: number;
					maximum?: number;
					multiple_of?: number;
				};
				sold_individually?: boolean;
			} >;
		} )
		.then( ( data ) => {
			// Transform API response to ProductData format
			const variationData: ProductData = {
				sku: data.sku || '',
				price_html: data.price_html || '',
				availability: data.availability_html || '',
				weight: data.weight || '',
				dimensions: data.dimensions
					? `${ data.dimensions.length } × ${ data.dimensions.width } × ${ data.dimensions.height }`
					: '',
			};

			if ( data.image?.id ) {
				// @ts-expect-error - image_id is not in ProductData type but used in blocks
				variationData.image_id = data.image.id;
			}

			// Add quantity constraints from add_to_cart data
			if ( data.add_to_cart ) {
				if ( data.add_to_cart.minimum !== undefined ) {
					// @ts-expect-error - min is not in ProductData type but used by QuantitySelector
					variationData.min = data.add_to_cart.minimum;
				}
				if ( data.add_to_cart.maximum !== undefined ) {
					// @ts-expect-error - max is not in ProductData type but used by QuantitySelector
					variationData.max = data.add_to_cart.maximum;
				}
				if ( data.add_to_cart.multiple_of !== undefined ) {
					// @ts-expect-error - step is not in ProductData type but used by QuantitySelector
					variationData.step = data.add_to_cart.multiple_of;
				}
			}

			if ( data.sold_individually !== undefined ) {
				// @ts-expect-error - sold_individually is not in ProductData type but used by QuantitySelector
				variationData.sold_individually = data.sold_individually;
			}

			// Cache the result
			variationDataCache[ variationId ] = variationData;

			// Clean up pending request
			if ( window.__wcVariationPendingRequests ) {
				delete window.__wcVariationPendingRequests[ variationId ];
			}

			return variationData;
		} )
		.catch( ( error ) => {
			// Clean up on error
			if ( window.__wcVariationPendingRequests ) {
				delete window.__wcVariationPendingRequests[ variationId ];
			}
			console.error( 'Failed to fetch variation data:', error );
			throw error;
		} );

	// Store pending request
	if ( window.__wcVariationPendingRequests ) {
		window.__wcVariationPendingRequests[ variationId ] = fetchPromise;
	}

	return fetchPromise;
}

const ALLOWED_TAGS = [
	'a',
	'b',
	'em',
	'i',
	'strong',
	'p',
	'br',
	'span',
	'bdi',
	'del',
	'ins',
];
const ALLOWED_ATTR = [
	'class',
	'target',
	'href',
	'rel',
	'name',
	'download',
	'aria-hidden',
];

export type Context = {
	productElementKey:
		| 'price_html'
		| 'availability'
		| 'sku'
		| 'weight'
		| 'dimensions';
};

const productElementStore = store(
	'woocommerce/product-elements',
	{
		state: {
			fetchedVariationData: {} as Record< number, ProductData >,
			get productData(): ProductData | undefined {
				if ( ! productDataState?.productId ) {
					return undefined;
				}

				const { products } = getConfig(
					'woocommerce'
				) as WooCommerceConfig;

				if ( ! products ) {
					return undefined;
				}

				const variationId = productDataState?.variationId;
				const product = products[ productDataState.productId ];

				// No variation selected - return parent product data
				if ( ! variationId ) {
					return product;
				}

				// Check if we've already fetched this variation (lazy load scenario)
				const fetchedData =
					productElementStore.state.fetchedVariationData[
						variationId
					];
				if ( fetchedData ) {
					return fetchedData;
				}

				// Check if variation data exists in config (pre-loaded, under threshold)
				const configVariationData = product?.variations?.[ variationId ];

				// For products over threshold, variation data may only contain image_id
				// Check if we have complete data or need to fetch
				const hasCompleteData =
					configVariationData &&
					( 'sku' in configVariationData ||
						'price_html' in configVariationData );

				if ( hasCompleteData ) {
					// Merge with parent data for fields that might be missing
					return { ...product, ...configVariationData };
				}

				// Data is incomplete or missing - fetch it (lazy loading for products over threshold)
				// Trigger async fetch and update state when complete
				fetchVariationData( variationId )
					.then( ( data ) => {
						// Store in reactive state to trigger updates
						productElementStore.state.fetchedVariationData = {
							...productElementStore.state.fetchedVariationData,
							[ variationId ]: data,
						};
					} )
					.catch( ( error ) => {
						// Silently fail - will show parent data
						console.warn(
							'Failed to fetch variation data:',
							error
						);
					} );

				// Return merged data: parent product data with any partial variation data (like image_id)
				return configVariationData
					? { ...product, ...configVariationData }
					: product;
			},
		},
		callbacks: {
			updateValue: () => {
				const element = getElement();

				if ( ! element.ref || ! productDataState?.productId ) {
					return;
				}

				const { productElementKey } = getContext< Context >();

				const productElementHtml =
					productElementStore?.state?.productData?.[
						productElementKey
					];

				if ( typeof productElementHtml === 'string' ) {
					element.ref.innerHTML = sanitizeHTML( productElementHtml, {
						tags: ALLOWED_TAGS,
						attr: ALLOWED_ATTR,
					} );
				}
			},
		},
	},
	{ lock: true }
);

export type ProductElementStore = typeof productElementStore;
