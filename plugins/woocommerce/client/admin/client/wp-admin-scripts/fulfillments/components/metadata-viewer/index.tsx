/**
 * External dependencies
 */
import { Button, Icon } from '@wordpress/components';
import { useState } from 'react';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import { Fulfillment } from '../../data/types';

interface MetadataViewerProps {
	fulfillment: Fulfillment;
}

const PostListIcon = () => (
	<svg
		width="16"
		height="16"
		viewBox="0 0 16 16"
		fill="none"
		xmlns="http://www.w3.org/2000/svg"
	>
		<path
			d="M14 1.5H2C1.86739 1.5 1.74021 1.55268 1.64645 1.64645C1.55268 1.74021 1.5 1.86739 1.5 2V14C1.5 14.1326 1.55268 14.2598 1.64645 14.3536C1.74021 14.4473 1.86739 14.5 2 14.5H14C14.1326 14.5 14.2598 14.4473 14.3536 14.3536C14.4473 14.2598 14.5 14.1326 14.5 14V2C14.5 1.86739 14.4473 1.74021 14.3536 1.64645C14.2598 1.55268 14.1326 1.5 14 1.5ZM2 0H14C14.5304 0 15.0391 0.210714 15.4142 0.585786C15.7893 0.960859 16 1.46957 16 2V14C16 14.5304 15.7893 15.0391 15.4142 15.4142C15.0391 15.7893 14.5304 16 14 16H2C1.46957 16 0.960859 15.7893 0.585786 15.4142C0.210714 15.0391 0 14.5304 0 14V2C0 1.46957 0.210714 0.960859 0.585786 0.585786C0.960859 0.210714 1.46957 0 2 0ZM3 5H4.5V6.5H3V5ZM4.5 9.5H3V11H4.5V9.5ZM6 5H13V6.5H6V5ZM13 9.5H6V11H13V9.5Z"
			fill="#1E1E1E"
		/>
	</svg>
);

export default function MetadataViewer( { fulfillment }: MetadataViewerProps ) {
	const [ expanded, setExpanded ] = useState( false );
	const publicMetadata = fulfillment.meta_data.filter(
		( meta ) => meta.key.startsWith( '_' ) === false
	);

	return (
		<div className="woocommerce-fulfillment-metadata-viewer">
			<div className="woocommerce-fulfillment-metadata-viewer__header">
				<PostListIcon />
				<h3>{ __( 'Fulfillment details', 'woocommerce' ) }</h3>
				<Button
					__next40pxDefaultSize
					size="small"
					onClick={ () => setExpanded( ! expanded ) }
				>
					<Icon
						icon={ expanded ? 'arrow-up-alt2' : 'arrow-down-alt2' }
						size={ 16 }
					/>
				</Button>
			</div>
			{ expanded && (
				<div className="woocommerce-fulfillment-metadata-viewer__content">
					{ publicMetadata.length === 0 && (
						<p>
							{ __( 'No information available.', 'woocommerce' ) }
						</p>
					) }
					{ publicMetadata.length > 0 && (
						<ul>
							{ publicMetadata.map( ( meta ) => (
								<li
									key={ meta.id }
									className="woocommerce-fulfillment-metadata-viewer__item"
								>
									<div className="woocommerce-fulfillment-metadata-viewer__item-key">
										{ meta.key }
									</div>
									<div className="woocommerce-fulfillment-metadata-viewer__item-value">
										{ String( meta.value ) ??
											__( '(empty)', 'woocommerce' ) }
									</div>
								</li>
							) ) }
						</ul>
					) }
				</div>
			) }
		</div>
	);
}
