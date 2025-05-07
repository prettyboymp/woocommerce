/**
 * External dependencies
 */
import { ComboboxControl, TextControl } from '@wordpress/components';
import { ComboboxControlOption } from '@wordpress/components/build-types/combobox-control/types';
import { __ } from '@wordpress/i18n';

const SearchIcon = () => (
	<svg
		width="12"
		height="12"
		viewBox="0 0 12 12"
		fill="none"
		xmlns="http://www.w3.org/2000/svg"
	>
		<path
			d="M6.75 0.75C4.275 0.75 2.25 2.775 2.25 5.25C2.25 6.3 2.625 7.275 3.225 8.025L0.375 10.875L1.2 11.7L4.05 8.85C4.8 9.45 5.775 9.825 6.825 9.825C9.3 9.825 11.325 7.8 11.325 5.325C11.325 2.85 9.225 0.75 6.75 0.75ZM6.75 8.625C4.875 8.625 3.375 7.125 3.375 5.25C3.375 3.375 4.875 1.875 6.75 1.875C8.625 1.875 10.125 3.375 10.125 5.25C10.125 7.125 8.625 8.625 6.75 8.625Z"
			fill="#1E1E1E"
		/>
	</svg>
);

const ShippingProviderListItem = ( {
	item,
}: {
	item: ComboboxControlOption;
} ) => {
	return (
		<div
			className={ [
				'woocommerce-fulfillment-shipping-provider-list-item',
				'woocommerce-fulfillment-shipping-provider-list-item-' +
					item.value,
			].join( ' ' ) }
		>
			{ item.icon && (
				<div className="woocommerce-fulfillment-shipping-provider-list-item-icon">
					<img src={ item.icon } alt={ item.label } />
				</div>
			) }
			<div className="woocommerce-fulfillment-shipping-provider-list-item-label">
				{ item.label }
			</div>
		</div>
	);
};

export default function ShipmentManualEntryForm( {
	trackingNumber,
	setTrackingNumber,
	trackingUrl,
	setTrackingUrl,
	shipmentProvider,
	setShipmentProvider,
}: {
	trackingNumber: string;
	setTrackingNumber: ( value: string ) => void;
	trackingUrl: string;
	setTrackingUrl: ( value: string ) => void;
	shipmentProvider: string;
	setShipmentProvider: ( value: string ) => void;
} ) {
	return (
		<>
			<p className="woocommerce-fulfillment-description">
				{ __(
					'Provide the shipment information for this fulfillment.',
					'woocommerce'
				) }
			</p>
			<div className="woocommerce-fulfillment-input-container">
				<h4>{ __( 'Tracking Number', 'woocommerce' ) }</h4>
				<div className="woocommerce-fulfillment-input-group">
					<TextControl
						type="text"
						placeholder={ __(
							'Enter tracking number',
							'woocommerce'
						) }
						value={ trackingNumber }
						onChange={ ( value: string ) => {
							setTrackingNumber( value );
						} }
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</div>
			</div>
			<div className="woocommerce-fulfillment-input-container">
				<h4>{ __( 'Provider', 'woocommerce' ) }</h4>
				<div className="woocommerce-fulfillment-input-group">
					<ComboboxControl
						__experimentalRenderItem={ ( { item } ) => (
							<ShippingProviderListItem item={ item } />
						) }
						allowReset={ false }
						hideLabelFromVision
						__next40pxDefaultSize
						value={ shipmentProvider }
						options={
							/* TODO: This will be moved into a server side endpoint, just hardcoded here for demo purposes. */
							[
								{
									label: __( 'UPS', 'woocommerce' ),
									icon: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABoAAAAgCAMAAAA7dZg3AAAAV1BMVEVHcEz/uRL8thP/uhL9txP/uRL3sxP+uBL9txL9txP/uRP+uBP/vRHmpRbqqRW+iByWaiB7ViRlRCZSNiiodx5GLClMMSjUmBk8JCmzfx3/uxL9txL/uxJ2tk2hAAAAHXRSTlMAHFiCstT8/0akpGb///L//////////////5MzDg1ZbnQAAAFKSURBVHgBdZIFgsQwCABTgxNysQKV/f87L6TrMlWYeOIaXT+ME2AFpnHoO3fm6xvQgPYc/HxV8XuJDCIE34C/X4cXRehDTDmXRs7k8Iyfc2GRIuWAL0qT1PQ9ciifuOVf1SxNvCpI/CqaoszWWn2ERVrDYl9Ti/qZk/c5eiKYuciKBJFNeVo5EWQlqDIsK6GCXx5VWCJi9hS5PNcKzECxxouIPCsLrMH8qthTkIAEid/VYolA12EsFyVoY+BIKKaUVC4jnBFzzHIoLPXroaYUwVNtAcAjaUE3JV6RCFZRRMJVLLR2JrcFZi6ysCi132Ihc9jcDuWgqXIFduemlV8Vr5OdQYz8rDhi184htG2W4GdpQmSGL9fYJ7XDIWfBSafdXRggmDxEgMHd0W2gSSpJYbNuHiVqSoomuv70KE8D4tByv1vvPtBvNsB/TAwswlG26LYAAAAASUVORK5CYII=',
									value: 'ups',
								},
								{
									label: __( 'FedEx', 'woocommerce' ),
									icon: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAYAAAByDd+UAAACAElEQVR4Ae2UA6wdURRFb+PUdhvU0Ytq27Zt27Zt27Zt27YRo93t6uTO+/4/+J6X7Jm5OuvoPlO+R5UolQf0gLEAWCvtpJRBFYGD7jm1McElJQ0VWDXVFAVT6ilFwgNWTDJFI3wDpG4JpHbGr+ZG/MIF1s4y43mFxJNX1Mo8fbMxHTNHBDjcNxCgo46JzqiDWa1GZo2khaEDgWWdoZ6l1yyyk5KaXzv6YvTeJTdG8JZU8dPrX4XfP/sxhrlXd771K20m+oF9s0iD09cODRIisFHuOf8jBLxi7Infw5psUrdyKzS+3Xad3nX/0fvn328x37/WOuYUCNglpT/CwdnXICLVht5r9PzCZs1p5oz3jdvpphQDCDhjDJ/b91Bt8y3mW3P6HvxDGkME2tohxs+vO9+DfdKcBmTA0c7+xd0IMQgAYxhmvlGeOa4jzDHuXW5t6RBreGCm9OA4El2q44utI64TzBtrmFRSu7XTTgMALFKIcIY9RDut814LDLWGGAbAGkCiJDrWXODQWps3MjG9y75VNuJ3r74KB/impjiCA2TCSWnwa4EwTjqpLW8Xeu9wFWOvQr/K6zsBvHXqdRsA1IqmIapFow6/pHGImjnWOPcPuIZGCSjbHLw1qTKN4zYRDRRi6z68/L4l7Y92Lbw2jiuBuCJ2zvvzjrlAD+gBPeBf8iRJMKLVRakAAAAASUVORK5CYII=',
									value: 'fedex',
								},
								{
									label: __( 'DHL', 'woocommerce' ),
									icon: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAMAAABF0y+mAAAAkFBMVEX/wQD/xgD/zAD/zQD/zgD/zwD/yQD/vQD/ywD/0AD/0gD9pwr6SSb4Lin3Nij3Wh/5exj3cRf2Xxz2WCP1Uib4PyP0GyfsFCruIyzpJirrLSn9oQ//vwDjICrqFS/tLjD/xwD/kQH/ThP/QSL1hxb4mBH3lRH0fxX0jBXuKyL+GCL/NBr/Vwz/1gD/2gD/1ADOPPtFAAABNElEQVR4AWWRBQLDIBAEL9xBIXV3d///78qSFCoTz6B7RJli0VoAR0SUySpkrZeBool+I2xJKcUeyOASThMrUGoQXe5ImOGShRdYSCn/c6Ts7xzpUv77KL9MOZHWUbIqCM6TZPi2gaRFCF1sBG28VdCiSYKS3DGHvfkrz5UHkoOr1uqNZqvV7nS73V6jnykML8TGGpUNhiMwHk/AcOqs9Z6UtXpWG47ni8ViNPcsl+NJs0IzWhky2XqzbQyGvd1+f9gdj6fDrnuuX663yioja/L7/fG4P57Pp1/K8w4e/ocYgzkZUUlRgZCq/8w15hRsDOaz5Phg0YQIitRS2d/hk3ffKIC7cBgWX7+EbDXD/dkiPsf8rWLpNHp6+90pSWxQRaIsVutQ7SgSyv8kwyGCNHN8sn0BuhAXVv8KvZsAAAAASUVORK5CYII=',
									value: 'dhl',
								},
								{
									label: __( 'USPS', 'woocommerce' ),
									icon: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAADZElEQVR4AcyWA5dYQQyF239U2zbXtm3bts0aa9u2bdu6fTO133pzzjPmm+Qmk0MADu/nRgGOHpOUPHZcKnQvNzLmN4AvN7HHW+jBAVBQ9gy9eUcXx05I7w/A+vp66MrKKsbGZ5CQXAFP7xgIiTrg8jV1nD4jt/sAAELxB1tdXUN1XTc+xBTC1CISnDyWuHFTEydPy+0FwO+2sbGBwaFJ5Bc2IiIqA8rqvuDmNceFSyo7D8DW5heW0dI6gNz8eji6fICKqhfu3NMjemIP0NraF8ps2OrW0zOM0dEpLC+vgNETRsdmUFHZDgPjMEZD8v8HOHNWLpTZsNXt7Dl5nDuvgIuXlPDosT7U1L0QE5OPxcVlZGTVIiAkFb4BSXD3+AAuHgv2dYD8VFPTB0ZGQVvYgpGYWIy8vFq6lZY2YXh4EjOzC+Dlt2YHQFKworIVbG15eZXRQx8d0McnBrdua+L8BQVcv6GGa9fVqHfGmVTX1g9hXwk9vT5Q9bOxmZl5RESmIig4ER+ZEBQW1aOhoQtdXUNoa+sHN48pJiZmICvvzh5AWMQG8/NL2KwRQQ4NTVCBErD+gTGYW4Sjt28M129qsgc4c1Ye7e0D+J+RSlrJhMvb5yOkZRxx46YGdbukpAP1BtFDZ+cgwiIzNr8YhYWn/BYGcj03t4iCgjqYmIbQwQgsibOdXTRy82ro8x/fHx6ZwoNHBpsHkJZxojMELc+rTDz74MVo49kzQ5w8JYNnz43g6vaWeIAI8TfQIUb90a9ywMVrsbXlmOR5f/8YFZWWli8uXVamKaqk5IaMjAoS4z+uI1U1nbC2fYnbd3W33w9wcBhTF1++ogJTs1BU17QTr/yeCbOLiI0vgbikM85dUNq5hoTM2M7+BU2ptbU1/GptHUNwdn2Pu/f1cJxFX8EagJRXG5soDA6O/yhEer6wsIzk1EpIy7nhFLu+gT3A5cvKRN00dX4cmMS2o3MIXj7xuPtAf+d7wgsXFaFvEEAr2I8DT88sIDO7Bqoavjh3UXnnm1KidC1tX1RVtTExXv/m5vaOIbqScXKb4/hJmd1pySytIkIrKlq+qZoAZGbXQk3DD1euqe1FU7oRymwYGZ1GcGgq7j8yxIlTMnvXFccmlIZq6wbi08wtc+jaLB/tGQ2ezulAYgAsWJdBb2lIHwAAAABJRU5ErkJggg==',
									value: 'usps',
								},
								{
									label: __( 'Royal Mail', 'woocommerce' ),
									icon: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAMAAABF0y+mAAAAkFBMVEX/wQD/xgD/zAD/zQD/zgD/zwD/yQD/vQD/ywD/0AD/0gD9pwr6SSb4Lin3Nij3Wh/5exj3cRf2Xxz2WCP1Uib4PyP0GyfsFCruIyzpJirrLSn9oQ//vwDjICrqFS/tLjD/xwD/kQH/ThP/QSL1hxb4mBH3lRH0fxX0jBXuKyL+GCL/NBr/Vwz/1gD/2gD/1ADOPPtFAAABNElEQVR4AWWRBQLDIBAEL9xBIXV3d///78qSFCoTz6B7RJli0VoAR0SUySpkrZeBool+I2xJKcUeyOASThMrUGoQXe5ImOGShRdYSCn/c6Ts7xzpUv77KL9MOZHWUbIqCM6TZPi2gaRFCF1sBG28VdCiSYKS3DGHvfkrz5UHkoOr1uqNZqvV7nS73V6jnykML8TGGpUNhiMwHk/AcOqs9Z6UtXpWG47ni8ViNPcsl+NJs0IzWhky2XqzbQyGvd1+f9gdj6fDrnuuX663yioja/L7/fG4P57Pp1/K8w4e/ocYgzkZUUlRgZCq/8w15hRsDOaz5Phg0YQIitRS2d/hk3ffKIC7cBgWX7+EbDXD/dkiPsf8rWLpNHp6+90pSWxQRaIsVutQ7SgSyv8kwyGCNHN8sn0BuhAXVv8KvZsAAAAASUVORK5CYII=',
									value: 'royal-mail',
								},
								{
									label: __( 'Hermes', 'woocommerce' ),
									icon: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAMAAABF0y+mAAAAkFBMVEX/wQD/xgD/zAD/zQD/zgD/zwD/yQD/vQD/ywD/0AD/0gD9pwr6SSb4Lin3Nij3Wh/5exj3cRf2Xxz2WCP1Uib4PyP0GyfsFCruIyzpJirrLSn9oQ//vwDjICrqFS/tLjD/xwD/kQH/ThP/QSL1hxb4mBH3lRH0fxX0jBXuKyL+GCL/NBr/Vwz/1gD/2gD/1ADOPPtFAAABNElEQVR4AWWRBQLDIBAEL9xBIXV3d///78qSFCoTz6B7RJli0VoAR0SUySpkrZeBool+I2xJKcUeyOASThMrUGoQXe5ImOGShRdYSCn/c6Ts7xzpUv77KL9MOZHWUbIqCM6TZPi2gaRFCF1sBG28VdCiSYKS3DGHvfkrz5UHkoOr1uqNZqvV7nS73V6jnykML8TGGpUNhiMwHk/AcOqs9Z6UtXpWG47ni8ViNPcsl+NJs0IzWhky2XqzbQyGvd1+f9gdj6fDrnuuX663yioja/L7/fG4P57Pp1/K8w4e/ocYgzkZUUlRgZCq/8w15hRsDOaz5Phg0YQIitRS2d/hk3ffKIC7cBgWX7+EbDXD/dkiPsf8rWLpNHp6+90pSWxQRaIsVutQ7SgSyv8kwyGCNHN8sn0BuhAXVv8KvZsAAAAASUVORK5CYII=',
									value: 'hermes',
								},
								{
									label: __( 'Yodel', 'woocommerce' ),
									icon: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAMAAABF0y+mAAAAkFBMVEX/wQD/xgD/zAD/zQD/zgD/zwD/yQD/vQD/ywD/0AD/0gD9pwr6SSb4Lin3Nij3Wh/5exj3cRf2Xxz2WCP1Uib4PyP0GyfsFCruIyzpJirrLSn9oQ//vwDjICrqFS/tLjD/xwD/kQH/ThP/QSL1hxb4mBH3lRH0fxX0jBXuKyL+GCL/NBr/Vwz/1gD/2gD/1ADOPPtFAAABNElEQVR4AWWRBQLDIBAEL9xBIXV3d///78qSFCoTz6B7RJli0VoAR0SUySpkrZeBool+I2xJKcUeyOASThMrUGoQXe5ImOGShRdYSCn/c6Ts7xzpUv77KL9MOZHWUbIqCM6TZPi2gaRFCF1sBG28VdCiSYKS3DGHvfkrz5UHkoOr1uqNZqvV7nS73V6jnykML8TGGpUNhiMwHk/AcOqs9Z6UtXpWG47ni8ViNPcsl+NJs0IzWhky2XqzbQyGvd1+f9gdj6fDrnuuX663yioja/L7/fG4P57Pp1/K8w4e/ocYgzkZUUlRgZCq/8w15hRsDOaz5Phg0YQIitRS2d/hk3ffKIC7cBgWX7+EbDXD/dkiPsf8rWLpNHp6+90pSWxQRaIsVutQ7SgSyv8kwyGCNHN8sn0BuhAXVv8KvZsAAAAASUVORK5CYII=',
									value: 'yodel',
								},
								{
									label: __( 'DPD', 'woocommerce' ),
									icon: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAMAAABF0y+mAAAAkFBMVEX/wQD/xgD/zAD/zQD/zgD/zwD/yQD/vQD/ywD/0AD/0gD9pwr6SSb4Lin3Nij3Wh/5exj3cRf2Xxz2WCP1Uib4PyP0GyfsFCruIyzpJirrLSn9oQ//vwDjICrqFS/tLjD/xwD/kQH/ThP/QSL1hxb4mBH3lRH0fxX0jBXuKyL+GCL/NBr/Vwz/1gD/2gD/1ADOPPtFAAABNElEQVR4AWWRBQLDIBAEL9xBIXV3d///78qSFCoTz6B7RJli0VoAR0SUySpkrZeBool+I2xJKcUeyOASThMrUGoQXe5ImOGShRdYSCn/c6Ts7xzpUv77KL9MOZHWUbIqCM6TZPi2gaRFCF1sBG28VdCiSYKS3DGHvfkrz5UHkoOr1uqNZqvV7nS73V6jnykML8TGGpUNhiMwHk/AcOqs9Z6UtXpWG47ni8ViNPcsl+NJs0IzWhky2XqzbQyGvd1+f9gdj6fDrnuuX663yioja/L7/fG4P57Pp1/K8w4e/ocYgzkZUUlRgZCq/8w15hRsDOaz5Phg0YQIitRS2d/hk3ffKIC7cBgWX7+EbDXD/dkiPsf8rWLpNHp6+90pSWxQRaIsVutQ7SgSyv8kwyGCNHN8sn0BuhAXVv8KvZsAAAAASUVORK5CYII=',
									value: 'dpd',
								},
								{
									label: __( 'Parcelforce', 'woocommerce' ),
									icon: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAMAAABF0y+mAAAAkFBMVEX/wQD/xgD/zAD/zQD/zgD/zwD/yQD/vQD/ywD/0AD/0gD9pwr6SSb4Lin3Nij3Wh/5exj3cRf2Xxz2WCP1Uib4PyP0GyfsFCruIyzpJirrLSn9oQ//vwDjICrqFS/tLjD/xwD/kQH/ThP/QSL1hxb4mBH3lRH0fxX0jBXuKyL+GCL/NBr/Vwz/1gD/2gD/1ADOPPtFAAABNElEQVR4AWWRBQLDIBAEL9xBIXV3d///78qSFCoTz6B7RJli0VoAR0SUySpkrZeBool+I2xJKcUeyOASThMrUGoQXe5ImOGShRdYSCn/c6Ts7xzpUv77KL9MOZHWUbIqCM6TZPi2gaRFCF1sBG28VdCiSYKS3DGHvfkrz5UHkoOr1uqNZqvV7nS73V6jnykML8TGGpUNhiMwHk/AcOqs9Z6UtXpWG47ni8ViNPcsl+NJs0IzWhky2XqzbQyGvd1+f9gdj6fDrnuuX663yioja/L7/fG4P57Pp1/K8w4e/ocYgzkZUUlRgZCq/8w15hRsDOaz5Phg0YQIitRS2d/hk3ffKIC7cBgWX7+EbDXD/dkiPsf8rWLpNHp6+90pSWxQRaIsVutQ7SgSyv8kwyGCNHN8sn0BuhAXVv8KvZsAAAAASUVORK5CYII=',
									value: 'parcelforce',
								},
								{
									label: __( 'Other', 'woocommerce' ),
									icon: null,
									value: 'other',
								},
							]
						}
						onChange={ ( value ) => {
							setShipmentProvider( value as string );
						} }
						__nextHasNoMarginBottom
					/>
					<div className="woocommerce-fulfillment-shipment-provider-search-icon">
						<SearchIcon />
					</div>
				</div>
			</div>
			<div className="woocommerce-fulfillment-input-container">
				<h4>{ __( 'Tracking URL', 'woocommerce' ) }</h4>
				<div className="woocommerce-fulfillment-input-group">
					<TextControl
						type="text"
						placeholder={ __(
							'Enter tracking URL',
							'woocommerce'
						) }
						value={ trackingUrl }
						onChange={ ( value: string ) => {
							setTrackingUrl( value );
						} }
						__next40pxDefaultSize
						__nextHasNoMarginBottom
					/>
				</div>
			</div>
		</>
	);
}
