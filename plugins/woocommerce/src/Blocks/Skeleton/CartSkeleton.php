<?php
namespace Automattic\WooCommerce\Blocks\Skeleton;

/**
 * CartSkeleton class.
 *
 * @internal
 */
class CartSkeleton {
	/**
	 * Get the skeleton HTML for the cart.
	 *
	 * @return string The skeleton HTML.
	 */
	public static function get_html(): string {
		return '
		<div id="cart-skeleton" class="wc-block-components-sidebar-layout">
			<div class="wc-block-components-main">
				<div class="wc-block-components-skeleton wc-block-components-skeleton--cart-line-items wc-block-cart is-large">
				<table class="wc-block-cart-items wp-block-woocommerce-cart-line-items-block">
					<thead>
					<tr class="wc-block-cart-items__header">
						<th class="wc-block-cart-items__header-image"></th>
						<th class="wc-block-cart-items__header-product"></th>
						<th class="wc-block-cart-items__header-total"></th>
					</tr>
					</thead>
					<tbody>
					<tr class="wc-block-cart-items__row">
						<td class="wc-block-cart-item__image">
						<div class="wc-block-components-skeleton__element" aria-hidden="true" style="width: 78px; height: 78px;"></div>
						</td>
						<td class="wc-block-cart-item__product">
						<div class="wc-block-cart-item__wrap">
							<div class="wc-block-components-skeleton__element" aria-hidden="true" style="width: 100%; height: 8px; max-width: 173px;"></div>
							<div class="wc-block-components-skeleton__element" aria-hidden="true" style="width: 78px; height: 8px;"></div>
						</div>
						</td>
						<td class="wc-block-cart-item__total">
						<div class="wc-block-components-skeleton__element" aria-hidden="true" style="width: 100%; height: 8px;"></div>
						</td>
					</tr>
					<tr class="wc-block-cart-items__row">
						<td class="wc-block-cart-item__image">
						<div class="wc-block-components-skeleton__element" aria-hidden="true" style="width: 78px; height: 78px;"></div>
						</td>
						<td class="wc-block-cart-item__product">
						<div class="wc-block-cart-item__wrap">
							<div class="wc-block-components-skeleton__element" aria-hidden="true" style="width: 100%; height: 8px; max-width: 173px;"></div>
							<div class="wc-block-components-skeleton__element" aria-hidden="true" style="width: 78px; height: 8px;"></div>
						</div>
						</td>
						<td class="wc-block-cart-item__total">
						<div class="wc-block-components-skeleton__element" aria-hidden="true" style="width: 100%; height: 8px;"></div>
						</td>
					</tr>
					</tbody>
				</table>
				</div>
			</div>
			<div class="wc-block-components-sidebar">
				<div class="wc-block-components-skeleton wc-block-components-skeleton--cart-order-summary">
				<div class="wc-block-components-skeleton__row">
					<div class="wc-block-components-skeleton__element" aria-hidden="true" style="width: 173px; height: 8px;"></div>
					<div class="wc-block-components-skeleton__element" aria-hidden="true" style="width: 45px; height: 8px;"></div>
				</div>
				<div class="wc-block-components-skeleton__row">
					<div class="wc-block-components-skeleton__element" aria-hidden="true" style="width: 173px; height: 8px;"></div>
					<div class="wc-block-components-skeleton__element" aria-hidden="true" style="width: 45px; height: 8px;"></div>
				</div>
				<div class="wc-block-components-skeleton__row">
					<div class="wc-block-components-skeleton__element" aria-hidden="true" style="width: 112px; height: 18px;"></div>
					<div class="wc-block-components-skeleton__element" aria-hidden="true" style="width: 52px; height: 18px;"></div>
				</div>
				</div>
			</div>
		</div>
		';
	}
}
