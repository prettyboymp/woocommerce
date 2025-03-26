/**
 * Internal dependencies
 */
import { Skeleton } from '../../';

export const ProductNoticeSkeleton = () => {
	return (
		<div className="wc-block-components-skeleton">
			<Skeleton height="16px" />
			<Skeleton height="16px" />
			<Skeleton height="16px" width="80%" />
		</div>
	);
};
