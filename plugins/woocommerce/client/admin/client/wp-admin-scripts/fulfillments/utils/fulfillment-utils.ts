/**
 * Internal dependencies
 */
import { Fulfillment } from '../data/types';

export function getFulfillmentMeta(
	fulfillment: Fulfillment | null,
	metaKey: string,
	defaultValue = ''
) {
	if ( ! fulfillment ) {
		return defaultValue;
	}
	const meta = fulfillment.meta_data.find(
		( _meta ) => _meta.key === metaKey
	)?.value;
	return meta ? ( meta as string ) : defaultValue;
}
