/**
 * External dependencies
 */
import { createElement, useMemo } from '@wordpress/element';
import { usePrevious } from '@wordpress/compose';
import { addQueryArgs } from '@wordpress/url';
import { __experimentalItemGroup as ItemGroup } from '@wordpress/components';
import * as IconPackage from '@wordpress/icons';
import {
	SidebarNavigationScreen,
	SidebarNavigationItem,
	SidebarContent,
} from '@automattic/site-admin';

const { Icon, ...icons } = IconPackage;

export const Sidebar = ( {
	pageTitle,
	sidebarItems,
	currentNestLevel,
	routeKey,
}: {
	pageTitle: string;
	sidebarItems: Array< {
		slug: string;
		label: string;
		to: string;
		isCurrent: boolean;
		withChevron: boolean;
		icon?: string;
		backPath?: string;
	} >;
	currentNestLevel: number;
	routeKey: string;
} ) => {
	const currentItem =
		sidebarItems.find( ( item ) => item.isCurrent ) || sidebarItems[ 0 ];
	const isRoot = ! currentItem?.backPath;
	const previousNestLevel = usePrevious( currentNestLevel );

	const shouldAnimate = useMemo( () => {
		return previousNestLevel !== currentNestLevel;
	}, [ currentItem ] );

	return (
		<SidebarContent shouldAnimate={ shouldAnimate } routeKey={ routeKey }>
			<SidebarNavigationScreen
				title={ pageTitle }
				isRoot={ isRoot }
				backPath={ currentItem?.backPath }
				exitLink={ addQueryArgs( 'admin.php', { page: 'wc-admin' } ) }
				content={
					<ItemGroup>
						{ sidebarItems.map( ( item ) => {
							const {
								label,
								icon,
								isCurrent,
								to,
								withChevron,
								slug,
							} = item;

							return (
								<SidebarNavigationItem
									icon={ icons[ icon as keyof typeof icons ] }
									aria-current={ isCurrent }
									uid={ slug }
									key={ slug }
									to={ to }
									suffix={
										withChevron ? 'CHEVRON' : undefined
									}
								>
									{ label }
								</SidebarNavigationItem>
							);
						} ) }
					</ItemGroup>
				}
			/>
		</SidebarContent>
	);
};
