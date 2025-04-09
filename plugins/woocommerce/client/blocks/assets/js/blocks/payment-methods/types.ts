export interface PaymentMethod {
	id: string;
	title: string;
	description: string;
	enabled: boolean;
	method_title: string;
	method_description: string;
	settings: Record< string, unknown >;
	icon?: string;
}
