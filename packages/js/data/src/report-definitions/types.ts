export interface ReportQueryArgOption {
	value: string;
	label: string; // Localized label
}

export interface ReportQueryArg {
	required?: boolean;
	description?: string; // Localized description
	type: 'string' | 'array' | 'boolean' | 'number';
	format?: 'YYYY-MM-DD' | string;
	options?: ReportQueryArgOption[] | string[];
	defaultValue?:
		| string
		| number
		| boolean
		| string[]
		| number[]
		| boolean[]
		| null;
}

export interface ReportDefinition {
	report: string; // e.g., 'revenue', 'products'
	title: string; // Localized, e.g., __( 'Revenue', 'woocommerce' )
	path: string; // e.g., '/wp-admin/admin.php?page=wc-admin&path=%2Fanalytics%2Frevenue'
	queryArgs: {
		[ key: string ]: ReportQueryArg;
	};
}

export type AllReportDefinitions = ReportDefinition[];

export interface ReportSpecificConfig {
	title?: string;
	charts?: Array< {
		key: string;
		label: string;
		order?: string;
		orderby?: string;
		type?: string;
		[ key: string ]: unknown;
	} >;
	advancedFilters?: {
		filters: object;
		title: string;
		[ key: string ]: unknown;
	};
	filters?: Array< {
		label: string;
		param: string;
		[ key: string ]: unknown;
	} >;
}

export type ReportConfigs = {
	[ reportSlug: string ]: ReportSpecificConfig;
};
