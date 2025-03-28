/**
 * External dependencies
 */
import { Product } from '@woocommerce/data';
import { createContext, useContext } from '@wordpress/element';

/**
 * Default product shape matching API response.
 */
const defaultProductData: Product = {
	id: 0,
	name: '',
	type: 'simple',
	permalink: '',
	sku: '',
	slug: '',
	short_description: '',
	description: '',
	on_sale: false,
	price_html: '',
	average_rating: '0',
	categories: [],
	tags: [],
	attributes: [],
	variations: [],
	// Required Post fields
	date: '',
	date_gmt: '',
	guid: { rendered: '', raw: '' },
	link: '',
	modified: '',
	modified_gmt: '',
	title: { rendered: '', raw: '' },
	content: { rendered: '', raw: '', is_protected: false, block_version: '0' },
	excerpt: { rendered: '', raw: '', protected: false },
	featured_media: 0,
	comment_status: 'closed',
	ping_status: 'closed',
	template: '',
	meta: {},
	permalink_template: '',
	generated_slug: '',
	password: '',
	author: 0,
	format: 'standard',
	sticky: false,
	// Additional required Product fields
	price: '',
	regular_price: '',
	sale_price: '',
	date_created: '',
	date_created_gmt: '',
	date_modified: '',
	date_modified_gmt: '',
	featured: false,
	catalog_visibility: 'visible',
	virtual: false,
	downloadable: false,
	menu_order: 0,
	purchasable: true,
	total_sales: 0,
	backorders: 'no',
	backorders_allowed: false,
	backordered: false,
	stock_status: 'instock',
	stock_quantity: 0,
	low_stock_amount: 0,
	weight: '',
	dimensions: { length: '', width: '', height: '' },
	shipping_class: '',
	shipping_class_id: 0,
	reviews_allowed: true,
	tax_status: 'taxable',
	tax_class: 'standard',
	manage_stock: false,
	status: 'publish',
	button_text: '',
	date_on_sale_from_gmt: null,
	date_on_sale_to_gmt: null,
	default_attributes: [],
	downloads: [],
	external_url: '',
	related_ids: [],
	shipping_required: true,
	shipping_taxable: true,
	download_expiry: -1,
	download_limit: -1,
	meta_data: [],
	rating_count: 0,
};

/**
 * This context is used to pass product data down to all children blocks in a given tree.
 *
 * @member {Object} ProductDataContext A react context object
 */
const ProductDataContext = createContext( {
	product: defaultProductData,
	hasContext: false,
	isLoading: false,
} );

export const useProductDataContext = () => useContext( ProductDataContext );

interface ProductDataContextProviderProps {
	product: Product | null;
	children: JSX.Element | JSX.Element[];
	isLoading: boolean;
}

/**
 * This context is used to pass product data down to all children blocks in a given tree.
 *
 * @param {Object}   object           A react context object
 * @param {any|null} object.product   The product data to be passed down
 * @param {Object}   object.children  The product data to be passed down
 * @param {boolean}  object.isLoading The product data to be passed down
 */
export const ProductDataContextProvider = ( {
	product = null,
	children,
	isLoading,
}: ProductDataContextProviderProps ) => {
	const contextValue = {
		product: product || defaultProductData,
		isLoading,
		hasContext: true,
	};

	return (
		<ProductDataContext.Provider value={ contextValue }>
			{ isLoading ? (
				<div className="is-loading">{ children }</div>
			) : (
				children
			) }
		</ProductDataContext.Provider>
	);
};
