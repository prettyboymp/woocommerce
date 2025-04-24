/**
 * External dependencies
 */
import { privateApis } from '@wordpress/interactivity';

const { h, VNode } = privateApis(
	'I acknowledge that using private APIs means my theme or plugin will inevitably break in the next version of WordPress.'
);

/**
 * Internal dependencies
 */
import MiniCartProductsTableBlock from '../mini-cart-products-table-block/block';

/**
 * Recursively convert DOM nodes to Preact elements
 */
const domToPreact = (
	node: Node | null,
	overrideChildren?: VNode[]
): VNode | null => {
	if ( ! node ) return null;

	// Handle text nodes
	if ( node.nodeType === Node.TEXT_NODE ) {
		return node.textContent as unknown as VNode;
	}

	// Handle element nodes
	if ( node.nodeType === Node.ELEMENT_NODE ) {
		const element = node as HTMLElement;
		const tagName = element.tagName.toLowerCase();

		if ( tagName === 'script' ) return null;

		// Convert attributes
		const props: Record< string, unknown > = {};
		Array.from( element.attributes ).forEach( ( attr ) => {
			props[ attr.name ] = attr.value;
		} );

		// Use override children if provided
		const children =
			overrideChildren !== undefined
				? overrideChildren
				: Array.from( element.childNodes )
						.map( ( child ) => domToPreact( child ) )
						.filter( Boolean );

		return h( tagName, props, ...children );
	}

	return null;
};

// Simple map of block names to lazy loaded Preact components
const blockMap = {
	'woocommerce/mini-cart-products-table-block': MiniCartProductsTableBlock,
};

type BlockRenderer = () => VNode | null;

const renderPreactBlock = ( element: HTMLElement ): BlockRenderer | null => {
	const blockName = element.dataset.blockName;

	// Recursively get child renderers
	const childRenderers = Array.from( element.children )
		.map( ( child ) => renderPreactBlock( child as HTMLElement ) )
		.filter( Boolean ) as BlockRenderer[];

	if ( blockName && blockMap[ blockName ] ) {
		try {
			const Component = blockMap[ blockName ];

			const props = Object.fromEntries(
				Object.entries( element.dataset )
					.filter( ( [ key ] ) => key !== 'blockName' )
					.map( ( [ key, value ] ) => [
						key,
						value?.startsWith( '{' ) || value?.startsWith( '[' )
							? JSON.parse( value )
							: value,
					] )
			);

			return () =>
				h( Component, props, ...childRenderers.map( ( r ) => r() ) );
		} catch ( error ) {
			console.error( `Error rendering block ${ blockName }:`, error );
			return null;
		}
	}

	// Unregistered element — render as plain element using domToPreact
	return () =>
		domToPreact(
			element,
			childRenderers.map( ( r ) => r() )
		);
};

export { renderPreactBlock };
