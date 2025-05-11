/**
 * Internal dependencies
 */
import FulfillmentEditor from './fulfillment-editor';
import { useFulfillmentDrawerContext } from '../../context/drawer-context';

export default function FulfillmentsList() {
	const { order, fulfillments, openSection, setOpenSection } =
		useFulfillmentDrawerContext();

	return (
		order && (
			<div className="woocommerce-fulfillment-stored-fulfillments-list">
				{ fulfillments.map( ( fulfillment, index ) => (
					<FulfillmentEditor
						index={ index }
						expanded={
							openSection === 'fulfillment-' + fulfillment.id
						}
						onExpand={ () =>
							setOpenSection( 'fulfillment-' + fulfillment.id )
						}
						onCollapse={ () => setOpenSection( 'order' ) }
						key={ fulfillment.id }
						order={ order }
						fulfillment={ fulfillment }
						fulfillments={ fulfillments }
					/>
				) ) }
			</div>
		)
	);
}
