/**
 * External dependencies
 */
import { Frame, Page } from '@playwright/test';

/**
 * WordPress dependencies
 */
import { test, expect } from '@wordpress/e2e-test-utils-playwright';

async function getFrameMetrics( frame: Frame ) {
	return await frame.evaluate( () => {
		const navigationEntries =
			window.performance.getEntriesByType( 'navigation' );
		const paintTimings = window.performance.getEntriesByType( 'paint' );

		const [
			{
				requestStart,
				responseStart,
				responseEnd,
				domContentLoadedEventEnd,
				loadEventEnd,
			},
		] = navigationEntries as PerformanceNavigationTiming[];

		const firstPaintStartTime = paintTimings.find(
			( { name } ) => name === 'first-paint'
		)?.startTime;

		const firstContentfulPaintStartTime = paintTimings.find(
			( { name } ) => name === 'first-contentful-paint'
		)?.startTime;

		if ( ! firstPaintStartTime || ! firstContentfulPaintStartTime ) {
			return null; // Signal that paint metrics are not complete
		}

		return {
			frameId: window.location.href,
			// Server side metric.
			serverResponse: responseStart - requestStart,
			// For client side metrics, consider the end of the response (the
			// browser receives the HTML) as the start time (0).
			firstPaint: firstPaintStartTime - responseEnd,
			domContentLoaded: domContentLoadedEventEnd - responseEnd,
			loaded: loadEventEnd - responseEnd,
			firstContentfulPaint: firstContentfulPaintStartTime - responseEnd,
			timeSinceResponseEnd: window.performance.now() - responseEnd,
		};
	} );
}

test.describe( 'All templates performance', () => {
	test( 'Loading', async ( { page }, testInfo ) => {
		const results: {
			frameId: string;
			serverResponse: number;
			firstPaint: number;
			domContentLoaded: number;
			loaded: number;
			requestCount: number;
		}[] = [];
		const samples = 3;
		const throwaway = 1;
		const iterations = samples + throwaway;

		for ( let i = 0; i < iterations; i++ ) {
			let requestCount = 0;
			page.on( 'request', () => {
				requestCount++;
			} );

			await page.goto(
				'/wp-admin/site-editor.php?postType=wp_template&activeView=WooCommerce'
			);

			const mainFrame = page.mainFrame();
			let frames: Frame[] = [];

			await expect
				.poll( async () => {
					frames = mainFrame.childFrames();
					return frames.length;
				} )
				.toBe( 11 );

			const loadTimes: ( {
				frameId: string;
				serverResponse: number;
				firstPaint: number;
				domContentLoaded: number;
				loaded: number;
				firstContentfulPaint: number;
				timeSinceResponseEnd: number;
			} | null )[] = [];

			await Promise.all(
				frames.map( async ( frame ) => {
					await frame.waitForLoadState( 'domcontentloaded' );
					const frameMetrics = await getFrameMetrics( frame );
					if ( ! frameMetrics ) {
						throw new Error( 'Could not get frame metrics' );
					}
					loadTimes.push( frameMetrics );
				} )
			);

			await page.pause();

			if ( i > throwaway ) {
				// console.log( loadTimes, 'loadTimes' );

				const fixedLoadTimes = loadTimes.map( ( loadTime ) => {
					return {
						...loadTime,
						requestCount,
					};
				} );

				results.push( ...fixedLoadTimes );
			}

			// console.log( results );
		}
		const valuesByKeys = results.reduce( ( acc, curr ) => {
			const frameId = curr.frameId;
			if ( ! frameId ) {
				return acc;
			}
			acc[ frameId ] = {
				...acc[ frameId ],
				serverResponse: [
					...( acc[ frameId ]?.serverResponse || [] ),
					curr.serverResponse,
				],
				firstPaint: [
					...( acc[ frameId ]?.firstPaint || [] ),
					curr.firstPaint,
				],
				domContentLoaded: [
					...( acc[ frameId ]?.domContentLoaded || [] ),
					curr.domContentLoaded,
				],
				loaded: [ ...( acc[ frameId ]?.loaded || [] ), curr.loaded ],
				requestCount: [
					...( acc[ frameId ]?.requestCount || [] ),
					curr.requestCount,
				],
			};
			return acc;
		}, {} );

		const median = {};
		for ( const title in valuesByKeys ) {
			const values = valuesByKeys[ title ];
			median[ title ] = {}; // Initialize an object for this title

			for ( const key in values ) {
				values[ key ].sort( ( a, b ) => a - b );
				median[ title ][ key ] =
					values[ key ][ Math.floor( values[ key ].length / 2 ) ];
			}
		}

		console.log( median, 'median' );

		await testInfo.attach( 'all-templates.json', {
			body: JSON.stringify( median, null, 2 ),
			contentType: 'application/json',
		} );

		expect( true ).toBe( true );
	} );
} );
