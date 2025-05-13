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

export default function UpdateButton( {
	setError,
}: {
	setError: ( message: string | null ) => void;
} ) {
	const { orderId, fulfillment } = useFulfillmentContext();
	const { updateFulfillment } = useDispatch( FulfillmentStore );
	const [ isExecuting, setIsExecuting ] = useState< boolean >( false );

	const handleUpdateFulfillment = () => {
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
		updateFulfillment( orderId, fulfillment )
			.catch( ( error ) => {
				setError( error );
			} )
			.finally( () => {
				setIsExecuting( false );
			} );
	};

	return (
		<Button
			variant="primary"
			onClick={ handleUpdateFulfillment }
			disabled={ isExecuting }
			__next40pxDefaultSize
		>
			{ __( 'Update', 'woocommerce' ) }
		</Button>
	);
}
