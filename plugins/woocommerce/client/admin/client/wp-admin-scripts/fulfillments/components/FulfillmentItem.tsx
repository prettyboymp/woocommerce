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
import { useFulfillmentFormContext } from '../context/FulfillmentFormContext';

type FulfillmentItemProps = {
	item: LineItem;
	currency: string;
};

export default function FulfillmentItem( {
	item,
	currency,
}: FulfillmentItemProps ) {
	const { toggleItem, selectedItems } = useFulfillmentFormContext();
	const [ itemExpanded, setItemExpanded ] = useState( false );

	const currencyContext = useContext( CurrencyContext );

	const storeCurrency = currencyContext.getCurrencyConfig();

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

		// TODO: Find a way to get the currency symbols from the store.
		const symbol = false;

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

	const calculateCheckedState = ( id: string, quantity: number ): boolean => {
		if ( id.includes( '-' ) ) {
			return selectedItems.some(
				( itemToCheck ) => itemToCheck.id === id && itemToCheck.checked
			);
		}
		if ( quantity > 1 ) {
			const itemCount = selectedItems.filter( ( itemToCheck ) =>
				itemToCheck.id.startsWith( id + '-' )
			).length;
			return quantity === itemCount;
		}

		return selectedItems.some(
			( itemToCheck ) => itemToCheck.id === id && itemToCheck.checked
		);
	};

	const calculateDeterminateState = ( id: string ): boolean => {
		const itemCount = selectedItems.filter( ( itemToCheck ) =>
			itemToCheck.id.startsWith( id + '-' )
		).length;
		const itemQuantity = item.quantity;
		return itemCount > 0 && itemCount < itemQuantity;
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
						checked={ calculateCheckedState(
							String( item.id ),
							item.quantity
						) }
						onChange={ ( value ) => {
							toggleItem( String( item.id ), value );
						} }
						indeterminate={ calculateDeterminateState(
							String( item.id )
						) }
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
									checked={ calculateCheckedState(
										String( item.id ) + '-' + index,
										item.quantity
									) }
									onChange={ ( value ) => {
										toggleItem(
											String( item.id ) + '-' + index,
											value
										);
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
