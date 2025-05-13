/**
 * External dependencies
 */
import { Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useDispatch } from '@wordpress/data';
import { useState } from 'react';

/**
 * Internal dependencies
 */
import { useFulfillmentContext } from '../../context/fulfillment-context';
import { store as FulfillmentStore } from '../../data/store';
import { getFulfillmentItems } from '../../utils/fulfillment-utils';

export default function SaveAsDraftButton( {
	setError,
}: {
	setError: ( message: string | null ) => void;
} ) {
	const { orderId, fulfillment } = useFulfillmentContext();
	const [ isExecuting, setIsExecuting ] = useState( false );
	const { saveFulfillment } = useDispatch( FulfillmentStore );

	const handleFulfillItems = () => {
		setError( null );
		setIsExecuting( true );

		if ( ! fulfillment ) {
			setIsExecuting( false );
			return;
		}
		if ( getFulfillmentItems( fulfillment ).length === 0 ) {
			setIsExecuting( false );
			setError( 'Select items to be fulfilled.' );
			return;
		}
		saveFulfillment( orderId, fulfillment )
			.catch( ( error ) => {
				setError( error );
			} )
			.finally( () => {
				setIsExecuting( false );
			} );
	};

	return (
		<Button
			variant="secondary"
			onClick={ handleFulfillItems }
			__next40pxDefaultSize
			disabled={ isExecuting }
		>
			{ __( 'Save as draft', 'woocommerce' ) }
		</Button>
	);
}
