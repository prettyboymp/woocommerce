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

export default function RemoveButton( {
	setError,
}: {
	setError: ( message: string | null ) => void;
} ) {
	const { orderId, fulfillment } = useFulfillmentContext();
	const [ isExecuting, setIsExecuting ] = useState< boolean >( false );
	const { deleteFulfillment } = useDispatch( FulfillmentStore );

	const handleFulfillItems = () => {
		setError( null );
		setIsExecuting( true );
		if ( ! fulfillment || ! fulfillment.id ) {
			setIsExecuting( false );
			return;
		}
		deleteFulfillment( orderId, fulfillment.id )
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
			disabled={ isExecuting }
			__next40pxDefaultSize
		>
			{ __( 'Remove', 'woocommerce' ) }
		</Button>
	);
}
