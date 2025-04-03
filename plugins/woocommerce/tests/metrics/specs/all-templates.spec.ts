/**
 * External dependencies
 */
import { Frame, FrameLocator, Locator, Page } from '@playwright/test';

/**
 * WordPress dependencies
 */
import { test, expect } from '@wordpress/e2e-test-utils-playwright';

/**
 * Internal dependencies
 */
import { median } from '../utils';
import { writeFileSync } from 'fs';

type FrameMetrics = {
	serverResponse: number;
	firstPaint: number;
	domContentLoaded: number;
	loaded: number;
	firstContentfulPaint: number;
	timeSinceResponseEnd: number;
};

type FrameMetricsCollection = {
	[ Property in keyof FrameMetrics ]: FrameMetrics[ Property ][];
};

type FrameMetricsById = Map< string, FrameMetricsCollection >;

async function getFrameMetrics( frame: Locator ) {
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
	const results: FrameMetricsById = new Map();

	test.afterAll( async ( {}, testInfo ) => {
		const medians = {};
		results.forEach( ( frameMetrics, frameId ) => {
			medians[ frameId ] = {};
			Object.keys( frameMetrics ).forEach( ( metric ) => {
				medians[ frameId ][ metric ] = median(
					results.get( frameId )?.[ metric ] || []
				);
			} );
		} );

		console.log( JSON.stringify( medians, null, Infinity ) );

		writeFileSync(
			'results.json',
			JSON.stringify( { 'all-templates': medians }, null, Infinity )
		);

		await testInfo.attach( 'results', {
			body: JSON.stringify(
				{ 'all-templates': medians },
				null,
				Infinity
			),
			contentType: 'application/json',
		} );
	} );

	const samples = 10;
	const throwaway = 1;
	const iterations = samples + throwaway;

	for ( let i = 0; i < iterations; i++ ) {
		test( `Run the test (${ i } of ${ iterations })`, async ( {
			page,
		} ) => {
			let requestCount = 0;
			page.on( 'request', () => {
				requestCount++;
			} );

			await page.goto(
				'/wp-admin/site-editor.php?postType=wp_template&activeView=WooCommerce'
			);

			const frames: Record< string, FrameLocator > = {};

			const cards = page.locator( '.dataviews-view-grid__card' );
			await expect( cards ).toHaveCount( 11 );

			for ( const card of await cards.all() ) {
				const frame = await card.locator( 'iframe' ).contentFrame();
				const title =
					( await card
						.locator( '.dataviews-view-grid__primary-field' )
						.textContent() ) ?? '';

				frames[ title ] = frame;
			}

			const frameMetricsById = new Map< string, FrameMetrics[] >();
			// Create new metrics object with merged arrays
			const updatedMetrics: FrameMetricsCollection = {
				serverResponse: [],
				firstPaint: [],
				domContentLoaded: [],
				loaded: [],
				firstContentfulPaint: [],
				timeSinceResponseEnd: [],
			};

			await Promise.all(
				Object.entries( frames ).map( async ( [ title, frame ] ) => {
					frame.locator( 'body' ).evaluate( () => {
						return document.readyState === 'interactive';
					} );

					const frameMetrics = await getFrameMetrics(
						frame.locator( 'body' )
					);
					if ( ! frameMetrics ) {
						throw new Error( 'Could not get frame metrics' );
					}

					const currentMetrics = frameMetricsById.get( title ) ?? [];

					frameMetricsById.set( title, [
						...currentMetrics,
						frameMetrics,
					] );
				} )
			);

			if ( i > throwaway ) {
				frameMetricsById.forEach( ( frameMetrics, frameId ) => {
					if ( ! frameMetrics ) return;
					const existingResults: FrameMetricsCollection =
						results.get( frameId ) ||
						( {} as FrameMetricsCollection );

					frameMetrics.forEach( ( metric, metricKey ) => {
						Object.entries( metric ).forEach(
							( [ key, value ] ) => {
								existingResults[ key ] = [
									...( existingResults[ key ] || [] ),
									value,
								];

								results.set( frameId, existingResults );
							}
						);
					} );
				} );

				if ( ! results[ 'requestCount' ] ) {
					results[ 'requestCount' ] = [];
				}

				results[ 'requestCount' ].push( requestCount );
			}
		} );
	}
} );
