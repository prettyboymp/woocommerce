/**
 * External dependencies
 */
import {
	unregisterBlockVariation,
	getBlockVariations,
	registerBlockVariation,
} from '@wordpress/blocks';

// Add support for top social networks
const supportedVariations = [
	// 'behance',
	// 'bluesky',
	// 'chain', // Link
	// 'discord',
	'facebook',
	'feed',
	'github',
	// 'gravatar',
	'instagram',
	'linkedin',
	'mail',
	'mastodon',
	// 'medium',
	// 'patreon',
	// 'pinterest',
	// 'reddit',
	// 'snapchat',
	// 'soundcloud',
	// 'spotify',
	// 'telegram',
	// 'threads',
	// 'tiktok',
	// 'tumblr',
	// 'twitch',
	'twitter',
	// 'vimeo',
	// 'vk',
	'wordpress',
	// 'whatsapp',
	'x',
	// 'youtube',
	// 'yelp',
];

function unregisterBlockVariations() {
	// Remove unsupported social links
	const variations = getBlockVariations( 'core/social-link' );
	variations.forEach( ( variation ) => {
		if ( ! supportedVariations.includes( variation.name ) ) {
			unregisterBlockVariation( 'core/social-link', variation.name );
		}
	} );
}

function registerCustomSocialLinksBlockVariation() {
	// Register a custom variation for the social links block
	// This variation is used to display the social links in the email editor by automatically adding some preset social links
	registerBlockVariation( 'core/social-links', {
		name: 'social-links-default',
		title: 'Social Icons',
		attributes: {
			openInNewTab: true,
			showLabels: false,
			size: false,
			iconColor: undefined,
			customIconColor: undefined,
			iconColorValue: undefined,
		},
		isDefault: true, // set this as the default variation
		innerBlocks: [
			{
				name: 'core/social-link',
				attributes: {
					service: 'wordpress',
					url: 'https://wordpress.org',
				},
			},
			{
				name: 'core/social-link',
				attributes: {
					service: 'facebook',
					url: 'https://www.facebook.com/WordPress/',
				},
			},
			{
				name: 'core/social-link',
				attributes: {
					service: 'twitter',
					url: 'https://twitter.com/WordPress',
				},
			},
		],
	} );
}

/**
 * Enhances the social links and social link blocks
 */
function enhanceSocialLinksBlock() {
	unregisterBlockVariations();
	registerCustomSocialLinksBlockVariation();
}

export { enhanceSocialLinksBlock };
