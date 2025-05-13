/**
 * External dependencies
 */
import { unregisterBlockStyle } from '@wordpress/blocks';

/**
 * Disables Logo-only style for social links
 * We can't use the style for .png icons
 */
function enhanceSocialLinksBlock() {
	unregisterBlockStyle( 'core/social-links', 'logos-only' );
}

export { enhanceSocialLinksBlock };
