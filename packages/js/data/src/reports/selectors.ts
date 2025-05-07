/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { getResourceName } from '../utils';
import {
	ReportState,
	ReportItemsEndpoint,
	ReportStatEndpoint,
	ReportQueryParams,
	ReportStatQueryParams,
	ReportItemObjectInfer,
	ReportStatObjectInfer,
} from './types';
import {
	ReportDefinition,
	ReportSpecificConfig,
} from '../report-definitions/types';
import * as commonArgs from '../report-definitions/common-query-args';

const EMPTY_OBJECT = {} as const;

export const getReportItemsError = (
	state: ReportState,
	endpoint: ReportItemsEndpoint,
	query: ReportQueryParams
) => {
	const resourceName = getResourceName( endpoint, query );
	return state.itemErrors[ resourceName ] || false;
};

export const getReportItems = < T >(
	state: ReportState,
	endpoint: ReportItemsEndpoint,
	query: ReportQueryParams
): ReportItemObjectInfer< T > => {
	const resourceName = getResourceName( endpoint, query );
	return (
		( state.items[ resourceName ] as ReportItemObjectInfer< T > ) ||
		EMPTY_OBJECT
	);
};

export const getReportStats = < T >(
	state: ReportState,
	endpoint: ReportStatEndpoint,
	query: ReportStatQueryParams
): ReportStatObjectInfer< T > => {
	const resourceName = getResourceName( endpoint, query );
	return (
		( state.stats[ resourceName ] as ReportStatObjectInfer< T > ) ||
		EMPTY_OBJECT
	);
};

export const getReportStatsError = (
	state: ReportState,
	endpoint: ReportStatEndpoint,
	query: ReportStatQueryParams
) => {
	const resourceName = getResourceName( endpoint, query );
	return state.statErrors[ resourceName ] || false;
};

export const getReportConfig = (
	state: ReportState,
	reportSlug: string
): ReportSpecificConfig | undefined => {
	return state.config && state.config[ reportSlug ];
};

export const getReportDefinition = (
	state: ReportState,
	reportSlug: string
): ReportDefinition | undefined => {
	const staticConfig = getReportConfig( state, reportSlug );

	if ( ! staticConfig ) {
		return undefined;
	}

	const queryArgs: ReportDefinition[ 'queryArgs' ] = {
		period: commonArgs.periodArg,
		compare: commonArgs.compareArg,
		after: commonArgs.afterArg,
		before: commonArgs.beforeArg,
		interval: commonArgs.intervalArg,
		page: commonArgs.pageArg,
		per_page: commonArgs.perPageArg,
		order: commonArgs.orderArg,
		orderby: {
			...commonArgs.orderbyArg,
			options:
				staticConfig.charts?.map( ( c ) => ( {
					value: c.orderby || c.key,
					label: c.label,
				} ) ) || [],
			// defaultValue: could come from staticConfig or a convention
		},
		search: commonArgs.searchArg,
	};

	if ( staticConfig.charts ) {
		queryArgs.chart = {
			required: true, // Or based on behavior
			type: 'string',
			description: __(
				'Select the chart to display for this report.',
				'woocommerce'
			),
			options: staticConfig.charts.map( ( c ) => ( {
				value: c.key,
				label: c.label,
			} ) ),
			// defaultValue: could come from staticConfig or a convention
		};
	}

	// TODO: Incorporate advancedFilters and filters from staticConfig into queryArgs
	// This might involve defining how these filter structures map to ReportQueryArg definitions.

	const reportTitle =
		staticConfig.title ||
		reportSlug.charAt( 0 ).toUpperCase() + reportSlug.slice( 1 );

	return {
		report: reportSlug,
		title: reportTitle, // Ensure this is localized if not from staticConfig.title
		path: `/wp-admin/admin.php?page=wc-admin&path=%2Fanalytics%2F${ reportSlug }`,
		queryArgs,
	};
};

export const getAllReportDefinitions = (
	state: ReportState
): ReportDefinition[] => {
	if ( ! state.config ) {
		return [];
	}
	const reportSlugs = Object.keys( state.config );
	return reportSlugs
		.map( ( slug ) => getReportDefinition( state, slug ) )
		.filter( ( def ): def is ReportDefinition => def !== undefined );
};
