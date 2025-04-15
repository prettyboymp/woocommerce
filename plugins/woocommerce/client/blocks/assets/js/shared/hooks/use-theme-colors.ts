/**
 * External dependencies
 */
import { useEffect, useRef } from '@wordpress/element';
import { generateUniqueId } from '@woocommerce/utils';

export interface EditorColors {
	editorBackgroundColor: string;
	editorColor: string;
}

// Static map to track injected styles by their signature.
const injectedStyles = new Map< string, string >();

/**
 * Hook to inject a <style> element in the Site Editor using theme background and foreground colors.
 *
 * @param getStyleContent - Callback that receives editor colors and returns CSS to inject.
 */
export const useApplyEditorStyles = (
	getStyleContent: ( colors: EditorColors ) => string
): void => {
	// Generate a unique ID once when the hook is initialized.
	const styleIdRef = useRef< string >(
		`editor-style-${ generateUniqueId() }`
	);

	useEffect( () => {
		const styleId = styleIdRef.current;
		// Find the editor styles wrapper in the main document.
		let editorStylesWrapper = document.querySelector(
			'.editor-styles-wrapper'
		);

		// If not found in main document, try to find it in the site editor iframe.
		if ( ! editorStylesWrapper ) {
			const canvasEl = document.querySelector(
				'.edit-site-visual-editor__editor-canvas'
			) as HTMLIFrameElement | null;

			if ( ! canvasEl || ! ( canvasEl instanceof HTMLIFrameElement ) ) {
				return;
			}

			const canvasDoc =
				canvasEl.contentDocument || canvasEl.contentWindow?.document;
			if ( ! canvasDoc ) {
				return;
			}

			// Look for the editor styles wrapper inside the iframe.
			editorStylesWrapper = canvasDoc.querySelector(
				'.editor-styles-wrapper'
			);
		}

		if ( ! editorStylesWrapper ) {
			return;
		}

		// Get the computed background and text color of the editor.
		const computedStyles = window.getComputedStyle( editorStylesWrapper );
		const editorBackgroundColor = computedStyles?.backgroundColor;
		const editorColor = computedStyles?.color;

		if ( ! editorBackgroundColor || ! editorColor ) {
			return;
		}

		// Generate the content for this style.
		const styleContent = getStyleContent( {
			editorBackgroundColor,
			editorColor,
		} );

		// Create a signature for this style content.
		const styleSignature = `${ editorBackgroundColor }-${ editorColor }-${ styleContent }`;

		// Check if we've already injected a style with this signature.
		if ( injectedStyles.has( styleSignature ) ) {
			return;
		}

		// Create and inject the style element with the ref ID.
		const styleElement = document.createElement( 'style' );
		styleElement.id = styleId;
		styleElement.appendChild( document.createTextNode( styleContent ) );
		editorStylesWrapper.appendChild( styleElement );

		// Store this style in our map with signature as key and ID as value.
		injectedStyles.set( styleSignature, styleId );

		// Clean up function.
		return () => {
			if ( injectedStyles.get( styleSignature ) === styleId ) {
				injectedStyles.delete( styleSignature );
			}

			const existingStyleElement = editorStylesWrapper?.querySelector(
				`#${ styleId }`
			);
			if ( existingStyleElement ) {
				existingStyleElement.remove();
			}
		};
	}, [ getStyleContent ] );
};
