/**
 * External dependencies
 */
import { useEffect, useRef } from 'react';
import { __ } from '@wordpress/i18n';
import { BaseControl } from '@wordpress/components';

/**
 * Internal dependencies
 */
import './shipping-methods-select.scss';

declare global {
	interface Window {
		// eslint-disable-next-line @typescript-eslint/no-explicit-any
		jQuery?: any;
	}
}

interface ShippingMethodsSelectProps {
	value: string[];
	onChange: ( value: string[] ) => void;
	options: Array< { label: string; value: string } >;
	label?: string;
	help?: string;
	className?: string;
	style?: React.CSSProperties;
	isDisabled?: boolean;
}

export const ShippingMethodsSelect = ( {
	value = [],
	onChange,
	options = [],
	label = __( 'Enable for shipping methods', 'woocommerce' ),
	help = __(
		'Select which shipping methods this payment method is available for. Leave blank to enable for all methods.',
		'woocommerce'
	),
	isDisabled = false,
}: ShippingMethodsSelectProps ) => {
	const selectRef = useRef< HTMLSelectElement | null >( null );

	useEffect( () => {
		// Initialize enhanced select if needed
		const select = selectRef.current;
		if ( select && window.jQuery && window.jQuery.fn.selectWoo ) {
			const $select = window.jQuery( select );

			// Only initialize if not already initialized
			if ( ! $select.hasClass( 'enhanced' ) ) {
				$select
					.selectWoo( {
						allowClear: true,
						placeholder: __(
							'Select shipping methods',
							'woocommerce'
						),
					} )
					.addClass( 'enhanced' );
			}
		}

		return () => {
			// Cleanup if needed
			if ( selectRef.current && window.jQuery ) {
				const $select = window.jQuery( selectRef.current );
				if ( $select.data( 'selectWoo' ) ) {
					$select.selectWoo( 'destroy' );
				}
			}
		};
	}, [] );

	return (
		<div className="shipping-methods-select-wrapper">
			<BaseControl
				id={ 'shipping-methods-control' }
				label={ label }
				help={ help }
			>
				<select
					ref={ selectRef }
					className={ `wc-enhanced-select shipping-methods-select` }
					disabled={ isDisabled }
					multiple
					value={ value }
					onChange={ ( event ) =>
						onChange(
							Array.from( event.target.selectedOptions ).map(
								( option ) => option.value
							)
						)
					}
				>
					{ options.map( ( option ) => (
						<option key={ option.value } value={ option.value }>
							{ option.label }
						</option>
					) ) }
				</select>
			</BaseControl>
		</div>
	);
};
