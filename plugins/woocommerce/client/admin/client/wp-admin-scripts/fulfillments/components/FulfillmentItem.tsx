/**
 * External dependencies
 */
import { useContext, useState } from 'react';
import { CheckboxControl, Icon } from '@wordpress/components';
import CurrencyFactory, {
	CurrencyContext,
	SymbolPosition,
} from '@woocommerce/currency';
import { decodeEntities } from '@wordpress/html-entities';
import { range } from 'lodash';

/**
 * Internal dependencies
 */
import { LineItem } from '../data/types';
import { getAdminSetting } from '~/utils/admin-settings';

type FulfillmentItemProps = {
	item: LineItem;
	currency: string;
};

export default function FulfillmentItem( {
	item,
	currency,
}: FulfillmentItemProps ) {
	const [ checked, setChecked ] = useState( true );
	const [ itemExpanded, setItemExpanded ] = useState( false );

	const currencyContext = useContext( CurrencyContext );

	const storeCurrency = currencyContext.getCurrencyConfig();
	const { currencySymbols = {} } = getAdminSetting( 'onboarding', {} );

	const getFormattedItemTotal = (
		total: number | string,
		orderCurrencyCode: string
	) => {
		if ( ! orderCurrencyCode ) {
			return null;
		}

		// If the order currency is the same as the store currency, we show the formatted amount.
		if ( storeCurrency && storeCurrency.code === orderCurrencyCode ) {
			return currencyContext.formatAmount( total );
		}
		const symbol = currencySymbols[ orderCurrencyCode ];

		if ( ! symbol ) {
			// This should never happen, but if it does, we'll just show the currency code.
			return `${ orderCurrencyCode }${ total }`;
		}

		// If the order currency is different from the store currency, we show the currency code and amount in the order currency.
		return CurrencyFactory( {
			...storeCurrency,
			symbol: decodeEntities( symbol ),
			symbolPosition: storeCurrency.symbolPosition as
				| SymbolPosition
				| undefined,
			code: orderCurrencyCode,
		} ).formatAmount( total );
	};

	return (
		<>
			<div
				className={ [
					'woocommerce-fulfillment-item-container',
					itemExpanded ? 'woocommerce-fulfillment-item-expanded' : '',
				].join( ' ' ) }
			>
				<div className="woocommerce-fulfillment-item-checkbox">
					<CheckboxControl
						id={ `fulfillment-item-${ item.id }` }
						name={ `fulfillment-item-${ item.id }` }
						value={ item.id }
						checked={ checked }
						onChange={ ( value ) => {
							setChecked( value );
						} }
						__nextHasNoMarginBottom
					/>
				</div>
				{ item.quantity > 1 && (
					<Icon
						icon={
							itemExpanded ? 'arrow-up-alt2' : 'arrow-right-alt2'
						}
						onClick={ () => {
							setItemExpanded( ! itemExpanded );
						} }
						size={ 16 }
					/>
				) }
				<div className="woocommerce-fulfillment-item-title">
					<div className="woocommerce-fulfillment-item-image-container">
						<img
							src={ item.image.src }
							id={ item.image.id }
							alt={ item.name }
							width={ 32 }
							height={ 32 }
							className="woocommerce-fulfillment-item-image"
						/>
					</div>
					<div className="woocommerce-fulfillment-item-name-sku">
						<div className="woocommerce-fulfillment-item-name">
							{ item.name }
						</div>
						{ item.sku && (
							<span className="woocommerce-fulfillment-item-sku">
								{ item.sku }
							</span>
						) }
					</div>
				</div>
				{ item.quantity > 1 && (
					<div className="woocommerce-fulfillment-item-quantity">
						{ 'x' + item.quantity }
					</div>
				) }
				<div className="woocommerce-fulfillment-item-price">
					{ getFormattedItemTotal( item.total, currency ) }
				</div>
			</div>
			{ itemExpanded && (
				<div className="woocommerce-fulfillment-item-expansion">
					{ range( item.quantity ).map( ( index ) => (
						<div
							key={ 'fulfillment-item-expansion-' + index }
							className="woocommerce-fulfillment-item-expansion-row"
						>
							<div className="woocommerce-fulfillment-item-checkbox">
								<CheckboxControl
									id={ `fulfillment-item-${ item.id }` }
									name={ `fulfillment-item-${ item.id }` }
									value={ item.id }
									checked={ checked }
									onChange={ ( value ) => {
										setChecked( value );
									} }
									__nextHasNoMarginBottom
								/>
							</div>
							<div className="woocommerce-fulfillment-item-title">
								<div className="woocommerce-fulfillment-item-image-container">
									<img
										src={ item.image.src }
										id={ item.image.id }
										alt={ item.name }
										width={ 32 }
										height={ 32 }
										className="woocommerce-fulfillment-item-image"
									/>
								</div>
								<div className="woocommerce-fulfillment-item-name-sku">
									<div className="woocommerce-fulfillment-item-name">
										{ item.name }
									</div>
									{ item.sku && (
										<span className="woocommerce-fulfillment-item-sku">
											{ item.sku }
										</span>
									) }
								</div>
							</div>
							<div className="woocommerce-fulfillment-item-price">
								{ getFormattedItemTotal(
									parseInt( item.total, 10 ) / item.quantity,
									currency
								) }
							</div>
						</div>
					) ) }
				</div>
			) }
		</>
	);
}
