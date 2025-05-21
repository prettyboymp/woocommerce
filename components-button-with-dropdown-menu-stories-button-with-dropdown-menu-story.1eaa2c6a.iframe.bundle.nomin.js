"use strict";
(self["webpackChunk_woocommerce_storybook"] = self["webpackChunk_woocommerce_storybook"] || []).push([[8431],{

/***/ "../../node_modules/.pnpm/@babel+runtime@7.25.7/node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js":
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {


// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  A: () => (/* binding */ _objectWithoutProperties)
});

;// ../../node_modules/.pnpm/@babel+runtime@7.25.7/node_modules/@babel/runtime/helpers/esm/objectWithoutPropertiesLoose.js
function _objectWithoutPropertiesLoose(r, e) {
  if (null == r) return {};
  var t = {};
  for (var n in r) if ({}.hasOwnProperty.call(r, n)) {
    if (e.includes(n)) continue;
    t[n] = r[n];
  }
  return t;
}

;// ../../node_modules/.pnpm/@babel+runtime@7.25.7/node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js

function _objectWithoutProperties(e, t) {
  if (null == e) return {};
  var o,
    r,
    i = _objectWithoutPropertiesLoose(e, t);
  if (Object.getOwnPropertySymbols) {
    var s = Object.getOwnPropertySymbols(e);
    for (r = 0; r < s.length; r++) o = s[r], t.includes(o) || {}.propertyIsEnumerable.call(e, o) && (i[o] = e[o]);
  }
  return i;
}


/***/ }),

/***/ "../../node_modules/.pnpm/@babel+runtime@7.25.7/node_modules/@babel/runtime/helpers/esm/toPropertyKey.js":
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {


// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  A: () => (/* binding */ toPropertyKey)
});

// EXTERNAL MODULE: ../../node_modules/.pnpm/@babel+runtime@7.25.7/node_modules/@babel/runtime/helpers/esm/typeof.js
var esm_typeof = __webpack_require__("../../node_modules/.pnpm/@babel+runtime@7.25.7/node_modules/@babel/runtime/helpers/esm/typeof.js");
;// ../../node_modules/.pnpm/@babel+runtime@7.25.7/node_modules/@babel/runtime/helpers/esm/toPrimitive.js

function toPrimitive(t, r) {
  if ("object" != (0,esm_typeof/* default */.A)(t) || !t) return t;
  var e = t[Symbol.toPrimitive];
  if (void 0 !== e) {
    var i = e.call(t, r || "default");
    if ("object" != (0,esm_typeof/* default */.A)(i)) return i;
    throw new TypeError("@@toPrimitive must return a primitive value.");
  }
  return ("string" === r ? String : Number)(t);
}

;// ../../node_modules/.pnpm/@babel+runtime@7.25.7/node_modules/@babel/runtime/helpers/esm/toPropertyKey.js


function toPropertyKey(t) {
  var i = toPrimitive(t, "string");
  return "symbol" == (0,esm_typeof/* default */.A)(i) ? i : i + "";
}


/***/ }),

/***/ "../../node_modules/.pnpm/@babel+runtime@7.25.7/node_modules/@babel/runtime/helpers/esm/typeof.js":
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   A: () => (/* binding */ _typeof)
/* harmony export */ });
function _typeof(o) {
  "@babel/helpers - typeof";

  return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) {
    return typeof o;
  } : function (o) {
    return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o;
  }, _typeof(o);
}


/***/ }),

/***/ "../../node_modules/.pnpm/@wordpress+components@28.0.3_@emotion+is-prop-valid@1.2.1_@types+react@18.3.16_react-dom@18.3_mbjd55jx3gsragjgwncwdigc7u/node_modules/@wordpress/components/build-module/dropdown-menu/index.js":
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {


// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  A: () => (/* binding */ dropdown_menu)
});

// UNUSED EXPORTS: DropdownMenu

// EXTERNAL MODULE: ../../node_modules/.pnpm/clsx@2.1.1/node_modules/clsx/dist/clsx.mjs
var clsx = __webpack_require__("../../node_modules/.pnpm/clsx@2.1.1/node_modules/clsx/dist/clsx.mjs");
// EXTERNAL MODULE: ../../node_modules/.pnpm/@wordpress+primitives@4.11.0_react@18.3.1/node_modules/@wordpress/primitives/build-module/svg/index.js
var svg = __webpack_require__("../../node_modules/.pnpm/@wordpress+primitives@4.11.0_react@18.3.1/node_modules/@wordpress/primitives/build-module/svg/index.js");
// EXTERNAL MODULE: ../../node_modules/.pnpm/react@18.3.1/node_modules/react/jsx-runtime.js
var jsx_runtime = __webpack_require__("../../node_modules/.pnpm/react@18.3.1/node_modules/react/jsx-runtime.js");
;// ../../node_modules/.pnpm/@wordpress+icons@10.0.2_react@18.3.1/node_modules/@wordpress/icons/build-module/library/menu.js
/**
 * WordPress dependencies
 */


const menu = /*#__PURE__*/(0,jsx_runtime.jsx)(svg/* SVG */.t4, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24",
  children: /*#__PURE__*/(0,jsx_runtime.jsx)(svg/* Path */.wA, {
    d: "M5 5v1.5h14V5H5zm0 7.8h14v-1.5H5v1.5zM5 19h14v-1.5H5V19z"
  })
});
/* harmony default export */ const library_menu = (menu);
//# sourceMappingURL=menu.js.map
// EXTERNAL MODULE: ../../node_modules/.pnpm/@wordpress+components@28.0.3_@emotion+is-prop-valid@1.2.1_@types+react@18.3.16_react-dom@18.3_mbjd55jx3gsragjgwncwdigc7u/node_modules/@wordpress/components/build-module/context/use-context-system.js + 1 modules
var use_context_system = __webpack_require__("../../node_modules/.pnpm/@wordpress+components@28.0.3_@emotion+is-prop-valid@1.2.1_@types+react@18.3.16_react-dom@18.3_mbjd55jx3gsragjgwncwdigc7u/node_modules/@wordpress/components/build-module/context/use-context-system.js");
// EXTERNAL MODULE: ../../node_modules/.pnpm/@wordpress+components@28.0.3_@emotion+is-prop-valid@1.2.1_@types+react@18.3.16_react-dom@18.3_mbjd55jx3gsragjgwncwdigc7u/node_modules/@wordpress/components/build-module/context/context-connect.js
var context_connect = __webpack_require__("../../node_modules/.pnpm/@wordpress+components@28.0.3_@emotion+is-prop-valid@1.2.1_@types+react@18.3.16_react-dom@18.3_mbjd55jx3gsragjgwncwdigc7u/node_modules/@wordpress/components/build-module/context/context-connect.js");
// EXTERNAL MODULE: ../../node_modules/.pnpm/@wordpress+components@28.0.3_@emotion+is-prop-valid@1.2.1_@types+react@18.3.16_react-dom@18.3_mbjd55jx3gsragjgwncwdigc7u/node_modules/@wordpress/components/build-module/button/index.js
var build_module_button = __webpack_require__("../../node_modules/.pnpm/@wordpress+components@28.0.3_@emotion+is-prop-valid@1.2.1_@types+react@18.3.16_react-dom@18.3_mbjd55jx3gsragjgwncwdigc7u/node_modules/@wordpress/components/build-module/button/index.js");
// EXTERNAL MODULE: ../../node_modules/.pnpm/@wordpress+components@28.0.3_@emotion+is-prop-valid@1.2.1_@types+react@18.3.16_react-dom@18.3_mbjd55jx3gsragjgwncwdigc7u/node_modules/@wordpress/components/build-module/dropdown/index.js
var dropdown = __webpack_require__("../../node_modules/.pnpm/@wordpress+components@28.0.3_@emotion+is-prop-valid@1.2.1_@types+react@18.3.16_react-dom@18.3_mbjd55jx3gsragjgwncwdigc7u/node_modules/@wordpress/components/build-module/dropdown/index.js");
// EXTERNAL MODULE: ../../node_modules/.pnpm/@wordpress+components@28.0.3_@emotion+is-prop-valid@1.2.1_@types+react@18.3.16_react-dom@18.3_mbjd55jx3gsragjgwncwdigc7u/node_modules/@wordpress/components/build-module/navigable-container/menu.js + 1 modules
var navigable_container_menu = __webpack_require__("../../node_modules/.pnpm/@wordpress+components@28.0.3_@emotion+is-prop-valid@1.2.1_@types+react@18.3.16_react-dom@18.3_mbjd55jx3gsragjgwncwdigc7u/node_modules/@wordpress/components/build-module/navigable-container/menu.js");
;// ../../node_modules/.pnpm/@wordpress+components@28.0.3_@emotion+is-prop-valid@1.2.1_@types+react@18.3.16_react-dom@18.3_mbjd55jx3gsragjgwncwdigc7u/node_modules/@wordpress/components/build-module/dropdown-menu/index.js
/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */






function mergeProps(defaultProps = {}, props = {}) {
  const mergedProps = {
    ...defaultProps,
    ...props
  };
  if (props.className && defaultProps.className) {
    mergedProps.className = (0,clsx/* default */.A)(props.className, defaultProps.className);
  }
  return mergedProps;
}
function isFunction(maybeFunc) {
  return typeof maybeFunc === 'function';
}
function UnconnectedDropdownMenu(dropdownMenuProps) {
  const {
    children,
    className,
    controls,
    icon = library_menu,
    label,
    popoverProps,
    toggleProps,
    menuProps,
    disableOpenOnArrowDown = false,
    text,
    noIcons,
    open,
    defaultOpen,
    onToggle: onToggleProp,
    // Context
    variant
  } = (0,use_context_system/* useContextSystem */.A)(dropdownMenuProps, 'DropdownMenu');
  if (!controls?.length && !isFunction(children)) {
    return null;
  }

  // Normalize controls to nested array of objects (sets of controls)
  let controlSets;
  if (controls?.length) {
    // @ts-expect-error The check below is needed because `DropdownMenus`
    // rendered by `ToolBarGroup` receive controls as a nested array.
    controlSets = controls;
    if (!Array.isArray(controlSets[0])) {
      // This is not ideal, but at this point we know that `controls` is
      // not a nested array, even if TypeScript doesn't.
      controlSets = [controls];
    }
  }
  const mergedPopoverProps = mergeProps({
    className: 'components-dropdown-menu__popover',
    variant
  }, popoverProps);
  return /*#__PURE__*/(0,jsx_runtime.jsx)(dropdown/* default */.A, {
    className: className,
    popoverProps: mergedPopoverProps,
    renderToggle: ({
      isOpen,
      onToggle
    }) => {
      var _toggleProps$showTool;
      const openOnArrowDown = event => {
        if (disableOpenOnArrowDown) {
          return;
        }
        if (!isOpen && event.code === 'ArrowDown') {
          event.preventDefault();
          onToggle();
        }
      };
      const {
        as: Toggle = build_module_button/* default */.Ay,
        ...restToggleProps
      } = toggleProps !== null && toggleProps !== void 0 ? toggleProps : {};
      const mergedToggleProps = mergeProps({
        className: (0,clsx/* default */.A)('components-dropdown-menu__toggle', {
          'is-opened': isOpen
        })
      }, restToggleProps);
      return /*#__PURE__*/(0,jsx_runtime.jsx)(Toggle, {
        ...mergedToggleProps,
        icon: icon,
        onClick: event => {
          onToggle();
          if (mergedToggleProps.onClick) {
            mergedToggleProps.onClick(event);
          }
        },
        onKeyDown: event => {
          openOnArrowDown(event);
          if (mergedToggleProps.onKeyDown) {
            mergedToggleProps.onKeyDown(event);
          }
        },
        "aria-haspopup": "true",
        "aria-expanded": isOpen,
        label: label,
        text: text,
        showTooltip: (_toggleProps$showTool = toggleProps?.showTooltip) !== null && _toggleProps$showTool !== void 0 ? _toggleProps$showTool : true,
        children: mergedToggleProps.children
      });
    },
    renderContent: props => {
      const mergedMenuProps = mergeProps({
        'aria-label': label,
        className: (0,clsx/* default */.A)('components-dropdown-menu__menu', {
          'no-icons': noIcons
        })
      }, menuProps);
      return /*#__PURE__*/(0,jsx_runtime.jsxs)(navigable_container_menu/* default */.Ay, {
        ...mergedMenuProps,
        role: "menu",
        children: [isFunction(children) ? children(props) : null, controlSets?.flatMap((controlSet, indexOfSet) => controlSet.map((control, indexOfControl) => /*#__PURE__*/(0,jsx_runtime.jsx)(build_module_button/* default */.Ay, {
          onClick: event => {
            event.stopPropagation();
            props.onClose();
            if (control.onClick) {
              control.onClick();
            }
          },
          className: (0,clsx/* default */.A)('components-dropdown-menu__menu-item', {
            'has-separator': indexOfSet > 0 && indexOfControl === 0,
            'is-active': control.isActive,
            'is-icon-only': !control.title
          }),
          icon: control.icon,
          label: control.label,
          "aria-checked": control.role === 'menuitemcheckbox' || control.role === 'menuitemradio' ? control.isActive : undefined,
          role: control.role === 'menuitemcheckbox' || control.role === 'menuitemradio' ? control.role : 'menuitem',
          disabled: control.isDisabled,
          children: control.title
        }, [indexOfSet, indexOfControl].join())))]
      });
    },
    open: open,
    defaultOpen: defaultOpen,
    onToggle: onToggleProp
  });
}

/**
 *
 * The DropdownMenu displays a list of actions (each contained in a MenuItem,
 * MenuItemsChoice, or MenuGroup) in a compact way. It appears in a Popover
 * after the user has interacted with an element (a button or icon) or when
 * they perform a specific action.
 *
 * Render a Dropdown Menu with a set of controls:
 *
 * ```jsx
 * import { DropdownMenu } from '@wordpress/components';
 * import {
 * 	more,
 * 	arrowLeft,
 * 	arrowRight,
 * 	arrowUp,
 * 	arrowDown,
 * } from '@wordpress/icons';
 *
 * const MyDropdownMenu = () => (
 * 	<DropdownMenu
 * 		icon={ more }
 * 		label="Select a direction"
 * 		controls={ [
 * 			{
 * 				title: 'Up',
 * 				icon: arrowUp,
 * 				onClick: () => console.log( 'up' ),
 * 			},
 * 			{
 * 				title: 'Right',
 * 				icon: arrowRight,
 * 				onClick: () => console.log( 'right' ),
 * 			},
 * 			{
 * 				title: 'Down',
 * 				icon: arrowDown,
 * 				onClick: () => console.log( 'down' ),
 * 			},
 * 			{
 * 				title: 'Left',
 * 				icon: arrowLeft,
 * 				onClick: () => console.log( 'left' ),
 * 			},
 * 		] }
 * 	/>
 * );
 * ```
 *
 * Alternatively, specify a `children` function which returns elements valid for
 * use in a DropdownMenu: `MenuItem`, `MenuItemsChoice`, or `MenuGroup`.
 *
 * ```jsx
 * import { DropdownMenu, MenuGroup, MenuItem } from '@wordpress/components';
 * import { more, arrowUp, arrowDown, trash } from '@wordpress/icons';
 *
 * const MyDropdownMenu = () => (
 * 	<DropdownMenu icon={ more } label="Select a direction">
 * 		{ ( { onClose } ) => (
 * 			<>
 * 				<MenuGroup>
 * 					<MenuItem icon={ arrowUp } onClick={ onClose }>
 * 						Move Up
 * 					</MenuItem>
 * 					<MenuItem icon={ arrowDown } onClick={ onClose }>
 * 						Move Down
 * 					</MenuItem>
 * 				</MenuGroup>
 * 				<MenuGroup>
 * 					<MenuItem icon={ trash } onClick={ onClose }>
 * 						Remove
 * 					</MenuItem>
 * 				</MenuGroup>
 * 			</>
 * 		) }
 * 	</DropdownMenu>
 * );
 * ```
 *
 */
const DropdownMenu = (0,context_connect/* contextConnectWithoutRef */.zS)(UnconnectedDropdownMenu, 'DropdownMenu');
/* harmony default export */ const dropdown_menu = (DropdownMenu);
//# sourceMappingURL=index.js.map

/***/ }),

/***/ "../../node_modules/.pnpm/@wordpress+components@28.0.3_@emotion+is-prop-valid@1.2.1_@types+react@18.3.16_react-dom@18.3_mbjd55jx3gsragjgwncwdigc7u/node_modules/@wordpress/components/build-module/dropdown/index.js":
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   A: () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* unused harmony export Dropdown */
/* harmony import */ var clsx__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__("../../node_modules/.pnpm/clsx@2.1.1/node_modules/clsx/dist/clsx.mjs");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__("../../node_modules/.pnpm/react@18.3.1/node_modules/react/index.js");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__("../../node_modules/.pnpm/@wordpress+compose@7.0.1_react@18.3.1/node_modules/@wordpress/compose/build-module/hooks/use-merge-refs/index.js");
/* harmony import */ var _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__("../../node_modules/.pnpm/@wordpress+deprecated@4.0.1/node_modules/@wordpress/deprecated/build-module/index.js");
/* harmony import */ var _context__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("../../node_modules/.pnpm/@wordpress+components@28.0.3_@emotion+is-prop-valid@1.2.1_@types+react@18.3.16_react-dom@18.3_mbjd55jx3gsragjgwncwdigc7u/node_modules/@wordpress/components/build-module/context/use-context-system.js");
/* harmony import */ var _context__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__("../../node_modules/.pnpm/@wordpress+components@28.0.3_@emotion+is-prop-valid@1.2.1_@types+react@18.3.16_react-dom@18.3_mbjd55jx3gsragjgwncwdigc7u/node_modules/@wordpress/components/build-module/context/context-connect.js");
/* harmony import */ var _utils_hooks__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__("../../node_modules/.pnpm/@wordpress+components@28.0.3_@emotion+is-prop-valid@1.2.1_@types+react@18.3.16_react-dom@18.3_mbjd55jx3gsragjgwncwdigc7u/node_modules/@wordpress/components/build-module/utils/hooks/use-controlled-value.js");
/* harmony import */ var _popover__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__("../../node_modules/.pnpm/@wordpress+components@28.0.3_@emotion+is-prop-valid@1.2.1_@types+react@18.3.16_react-dom@18.3_mbjd55jx3gsragjgwncwdigc7u/node_modules/@wordpress/components/build-module/popover/index.js");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("../../node_modules/.pnpm/react@18.3.1/node_modules/react/jsx-runtime.js");
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */





const UnconnectedDropdown = (props, forwardedRef) => {
  const {
    renderContent,
    renderToggle,
    className,
    contentClassName,
    expandOnMobile,
    headerTitle,
    focusOnMount,
    popoverProps,
    onClose,
    onToggle,
    style,
    open,
    defaultOpen,
    // Deprecated props
    position,
    // From context system
    variant
  } = (0,_context__WEBPACK_IMPORTED_MODULE_1__/* .useContextSystem */ .A)(props, 'Dropdown');
  if (position !== undefined) {
    (0,_wordpress_deprecated__WEBPACK_IMPORTED_MODULE_2__/* ["default"] */ .A)('`position` prop in wp.components.Dropdown', {
      since: '6.2',
      alternative: '`popoverProps.placement` prop',
      hint: 'Note that the `position` prop will override any values passed through the `popoverProps.placement` prop.'
    });
  }

  // Use internal state instead of a ref to make sure that the component
  // re-renders when the popover's anchor updates.
  const [fallbackPopoverAnchor, setFallbackPopoverAnchor] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_3__.useState)(null);
  const containerRef = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_3__.useRef)();
  const [isOpen, setIsOpen] = (0,_utils_hooks__WEBPACK_IMPORTED_MODULE_4__/* .useControlledValue */ .j)({
    defaultValue: defaultOpen,
    value: open,
    onChange: onToggle
  });

  /**
   * Closes the popover when focus leaves it unless the toggle was pressed or
   * focus has moved to a separate dialog. The former is to let the toggle
   * handle closing the popover and the latter is to preserve presence in
   * case a dialog has opened, allowing focus to return when it's dismissed.
   */
  function closeIfFocusOutside() {
    if (!containerRef.current) {
      return;
    }
    const {
      ownerDocument
    } = containerRef.current;
    const dialog = ownerDocument?.activeElement?.closest('[role="dialog"]');
    if (!containerRef.current.contains(ownerDocument.activeElement) && (!dialog || dialog.contains(containerRef.current))) {
      close();
    }
  }
  function close() {
    onClose?.();
    setIsOpen(false);
  }
  const args = {
    isOpen: !!isOpen,
    onToggle: () => setIsOpen(!isOpen),
    onClose: close
  };
  const popoverPropsHaveAnchor = !!popoverProps?.anchor ||
  // Note: `anchorRef`, `getAnchorRect` and `anchorRect` are deprecated and
  // be removed from `Popover` from WordPress 6.3
  !!popoverProps?.anchorRef || !!popoverProps?.getAnchorRect || !!popoverProps?.anchorRect;
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsxs)("div", {
    className: className,
    ref: (0,_wordpress_compose__WEBPACK_IMPORTED_MODULE_5__/* ["default"] */ .A)([containerRef, forwardedRef, setFallbackPopoverAnchor])
    // Some UAs focus the closest focusable parent when the toggle is
    // clicked. Making this div focusable ensures such UAs will focus
    // it and `closeIfFocusOutside` can tell if the toggle was clicked.
    ,
    tabIndex: -1,
    style: style,
    children: [renderToggle(args), isOpen && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsx)(_popover__WEBPACK_IMPORTED_MODULE_6__/* ["default"] */ .Ay, {
      position: position,
      onClose: close,
      onFocusOutside: closeIfFocusOutside,
      expandOnMobile: expandOnMobile,
      headerTitle: headerTitle,
      focusOnMount: focusOnMount
      // This value is used to ensure that the dropdowns
      // align with the editor header by default.
      ,
      offset: 13,
      anchor: !popoverPropsHaveAnchor ? fallbackPopoverAnchor : undefined,
      variant: variant,
      ...popoverProps,
      className: (0,clsx__WEBPACK_IMPORTED_MODULE_7__/* ["default"] */ .A)('components-dropdown__content', popoverProps?.className, contentClassName),
      children: renderContent(args)
    })]
  });
};

/**
 * Renders a button that opens a floating content modal when clicked.
 *
 * ```jsx
 * import { Button, Dropdown } from '@wordpress/components';
 *
 * const MyDropdown = () => (
 *   <Dropdown
 *     className="my-container-class-name"
 *     contentClassName="my-dropdown-content-classname"
 *     popoverProps={ { placement: 'bottom-start' } }
 *     renderToggle={ ( { isOpen, onToggle } ) => (
 *       <Button
 *         variant="primary"
 *         onClick={ onToggle }
 *         aria-expanded={ isOpen }
 *       >
 *         Toggle Dropdown!
 *       </Button>
 *     ) }
 *     renderContent={ () => <div>This is the content of the dropdown.</div> }
 *   />
 * );
 * ```
 */
const Dropdown = (0,_context__WEBPACK_IMPORTED_MODULE_8__/* .contextConnect */ .KZ)(UnconnectedDropdown, 'Dropdown');
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Dropdown);
//# sourceMappingURL=index.js.map

/***/ }),

/***/ "../../node_modules/.pnpm/@wordpress+components@28.0.3_@emotion+is-prop-valid@1.2.1_@types+react@18.3.16_react-dom@18.3_mbjd55jx3gsragjgwncwdigc7u/node_modules/@wordpress/components/build-module/flex/flex/component.js":
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   A: () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* unused harmony export Flex */
/* harmony import */ var _context__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__("../../node_modules/.pnpm/@wordpress+components@28.0.3_@emotion+is-prop-valid@1.2.1_@types+react@18.3.16_react-dom@18.3_mbjd55jx3gsragjgwncwdigc7u/node_modules/@wordpress/components/build-module/context/context-connect.js");
/* harmony import */ var _hook__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("../../node_modules/.pnpm/@wordpress+components@28.0.3_@emotion+is-prop-valid@1.2.1_@types+react@18.3.16_react-dom@18.3_mbjd55jx3gsragjgwncwdigc7u/node_modules/@wordpress/components/build-module/flex/flex/hook.js");
/* harmony import */ var _context__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__("../../node_modules/.pnpm/@wordpress+components@28.0.3_@emotion+is-prop-valid@1.2.1_@types+react@18.3.16_react-dom@18.3_mbjd55jx3gsragjgwncwdigc7u/node_modules/@wordpress/components/build-module/flex/context.js");
/* harmony import */ var _view__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__("../../node_modules/.pnpm/@wordpress+components@28.0.3_@emotion+is-prop-valid@1.2.1_@types+react@18.3.16_react-dom@18.3_mbjd55jx3gsragjgwncwdigc7u/node_modules/@wordpress/components/build-module/view/component.js");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("../../node_modules/.pnpm/react@18.3.1/node_modules/react/jsx-runtime.js");
/**
 * External dependencies
 */

/**
 * Internal dependencies
 */






function UnconnectedFlex(props, forwardedRef) {
  const {
    children,
    isColumn,
    ...otherProps
  } = (0,_hook__WEBPACK_IMPORTED_MODULE_1__/* .useFlex */ .v)(props);
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsx)(_context__WEBPACK_IMPORTED_MODULE_2__/* .FlexContext */ .R.Provider, {
    value: {
      flexItemDisplay: isColumn ? 'block' : undefined
    },
    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_0__.jsx)(_view__WEBPACK_IMPORTED_MODULE_3__/* ["default"] */ .A, {
      ...otherProps,
      ref: forwardedRef,
      children: children
    })
  });
}

/**
 * `Flex` is a primitive layout component that adaptively aligns child content
 * horizontally or vertically. `Flex` powers components like `HStack` and
 * `VStack`.
 *
 * `Flex` is used with any of its two sub-components, `FlexItem` and
 * `FlexBlock`.
 *
 * ```jsx
 * import { Flex, FlexBlock, FlexItem } from '@wordpress/components';
 *
 * function Example() {
 *   return (
 *     <Flex>
 *       <FlexItem>
 *         <p>Code</p>
 *       </FlexItem>
 *       <FlexBlock>
 *         <p>Poetry</p>
 *       </FlexBlock>
 *     </Flex>
 *   );
 * }
 * ```
 */
const Flex = (0,_context__WEBPACK_IMPORTED_MODULE_4__/* .contextConnect */ .KZ)(UnconnectedFlex, 'Flex');
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Flex);
//# sourceMappingURL=component.js.map

/***/ }),

/***/ "../../node_modules/.pnpm/@wordpress+components@28.0.3_@emotion+is-prop-valid@1.2.1_@types+react@18.3.16_react-dom@18.3_mbjd55jx3gsragjgwncwdigc7u/node_modules/@wordpress/components/build-module/navigable-container/menu.js":
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {


// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  Ay: () => (/* binding */ menu)
});

// UNUSED EXPORTS: NavigableMenu, UnforwardedNavigableMenu

// EXTERNAL MODULE: ../../node_modules/.pnpm/react@18.3.1/node_modules/react/index.js
var react = __webpack_require__("../../node_modules/.pnpm/react@18.3.1/node_modules/react/index.js");
// EXTERNAL MODULE: ../../node_modules/.pnpm/@wordpress+dom@4.0.1/node_modules/@wordpress/dom/build-module/index.js + 2 modules
var build_module = __webpack_require__("../../node_modules/.pnpm/@wordpress+dom@4.0.1/node_modules/@wordpress/dom/build-module/index.js");
// EXTERNAL MODULE: ../../node_modules/.pnpm/react@18.3.1/node_modules/react/jsx-runtime.js
var jsx_runtime = __webpack_require__("../../node_modules/.pnpm/react@18.3.1/node_modules/react/jsx-runtime.js");
;// ../../node_modules/.pnpm/@wordpress+components@28.0.3_@emotion+is-prop-valid@1.2.1_@types+react@18.3.16_react-dom@18.3_mbjd55jx3gsragjgwncwdigc7u/node_modules/@wordpress/components/build-module/navigable-container/container.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */

const noop = () => {};
const MENU_ITEM_ROLES = ['menuitem', 'menuitemradio', 'menuitemcheckbox'];
function cycleValue(value, total, offset) {
  const nextValue = value + offset;
  if (nextValue < 0) {
    return total + nextValue;
  } else if (nextValue >= total) {
    return nextValue - total;
  }
  return nextValue;
}
class NavigableContainer extends react.Component {
  constructor(args) {
    super(args);
    this.onKeyDown = this.onKeyDown.bind(this);
    this.bindContainer = this.bindContainer.bind(this);
    this.getFocusableContext = this.getFocusableContext.bind(this);
    this.getFocusableIndex = this.getFocusableIndex.bind(this);
  }
  componentDidMount() {
    if (!this.container) {
      return;
    }

    // We use DOM event listeners instead of React event listeners
    // because we want to catch events from the underlying DOM tree
    // The React Tree can be different from the DOM tree when using
    // portals. Block Toolbars for instance are rendered in a separate
    // React Trees.
    this.container.addEventListener('keydown', this.onKeyDown);
  }
  componentWillUnmount() {
    if (!this.container) {
      return;
    }
    this.container.removeEventListener('keydown', this.onKeyDown);
  }
  bindContainer(ref) {
    const {
      forwardedRef
    } = this.props;
    this.container = ref;
    if (typeof forwardedRef === 'function') {
      forwardedRef(ref);
    } else if (forwardedRef && 'current' in forwardedRef) {
      forwardedRef.current = ref;
    }
  }
  getFocusableContext(target) {
    if (!this.container) {
      return null;
    }
    const {
      onlyBrowserTabstops
    } = this.props;
    const finder = onlyBrowserTabstops ? build_module/* focus */.XC.tabbable : build_module/* focus */.XC.focusable;
    const focusables = finder.find(this.container);
    const index = this.getFocusableIndex(focusables, target);
    if (index > -1 && target) {
      return {
        index,
        target,
        focusables
      };
    }
    return null;
  }
  getFocusableIndex(focusables, target) {
    return focusables.indexOf(target);
  }
  onKeyDown(event) {
    if (this.props.onKeyDown) {
      this.props.onKeyDown(event);
    }
    const {
      getFocusableContext
    } = this;
    const {
      cycle = true,
      eventToOffset,
      onNavigate = noop,
      stopNavigationEvents
    } = this.props;
    const offset = eventToOffset(event);

    // eventToOffset returns undefined if the event is not handled by the component.
    if (offset !== undefined && stopNavigationEvents) {
      // Prevents arrow key handlers bound to the document directly interfering.
      event.stopImmediatePropagation();

      // When navigating a collection of items, prevent scroll containers
      // from scrolling. The preventDefault also prevents Voiceover from
      // 'handling' the event, as voiceover will try to use arrow keys
      // for highlighting text.
      const targetRole = event.target?.getAttribute('role');
      const targetHasMenuItemRole = !!targetRole && MENU_ITEM_ROLES.includes(targetRole);
      if (targetHasMenuItemRole) {
        event.preventDefault();
      }
    }
    if (!offset) {
      return;
    }
    const activeElement = event.target?.ownerDocument?.activeElement;
    if (!activeElement) {
      return;
    }
    const context = getFocusableContext(activeElement);
    if (!context) {
      return;
    }
    const {
      index,
      focusables
    } = context;
    const nextIndex = cycle ? cycleValue(index, focusables.length, offset) : index + offset;
    if (nextIndex >= 0 && nextIndex < focusables.length) {
      focusables[nextIndex].focus();
      onNavigate(nextIndex, focusables[nextIndex]);

      // `preventDefault()` on tab to avoid having the browser move the focus
      // after this component has already moved it.
      if (event.code === 'Tab') {
        event.preventDefault();
      }
    }
  }
  render() {
    const {
      children,
      stopNavigationEvents,
      eventToOffset,
      onNavigate,
      onKeyDown,
      cycle,
      onlyBrowserTabstops,
      forwardedRef,
      ...restProps
    } = this.props;
    return /*#__PURE__*/(0,jsx_runtime.jsx)("div", {
      ref: this.bindContainer,
      ...restProps,
      children: children
    });
  }
}
const forwardedNavigableContainer = (props, ref) => {
  return /*#__PURE__*/(0,jsx_runtime.jsx)(NavigableContainer, {
    ...props,
    forwardedRef: ref
  });
};
forwardedNavigableContainer.displayName = 'NavigableContainer';
/* harmony default export */ const container = ((0,react.forwardRef)(forwardedNavigableContainer));
//# sourceMappingURL=container.js.map
;// ../../node_modules/.pnpm/@wordpress+components@28.0.3_@emotion+is-prop-valid@1.2.1_@types+react@18.3.16_react-dom@18.3_mbjd55jx3gsragjgwncwdigc7u/node_modules/@wordpress/components/build-module/navigable-container/menu.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


function UnforwardedNavigableMenu({
  role = 'menu',
  orientation = 'vertical',
  ...rest
}, ref) {
  const eventToOffset = evt => {
    const {
      code
    } = evt;
    let next = ['ArrowDown'];
    let previous = ['ArrowUp'];
    if (orientation === 'horizontal') {
      next = ['ArrowRight'];
      previous = ['ArrowLeft'];
    }
    if (orientation === 'both') {
      next = ['ArrowRight', 'ArrowDown'];
      previous = ['ArrowLeft', 'ArrowUp'];
    }
    if (next.includes(code)) {
      return 1;
    } else if (previous.includes(code)) {
      return -1;
    } else if (['ArrowDown', 'ArrowUp', 'ArrowLeft', 'ArrowRight'].includes(code)) {
      // Key press should be handled, e.g. have event propagation and
      // default behavior handled by NavigableContainer but not result
      // in an offset.
      return 0;
    }
    return undefined;
  };
  return /*#__PURE__*/(0,jsx_runtime.jsx)(container, {
    ref: ref,
    stopNavigationEvents: true,
    onlyBrowserTabstops: false,
    role: role,
    "aria-orientation": role !== 'presentation' && (orientation === 'vertical' || orientation === 'horizontal') ? orientation : undefined,
    eventToOffset: eventToOffset,
    ...rest
  });
}

/**
 * A container for a navigable menu.
 *
 *  ```jsx
 *  import {
 *    NavigableMenu,
 *    Button,
 *  } from '@wordpress/components';
 *
 *  function onNavigate( index, target ) {
 *    console.log( `Navigates to ${ index }`, target );
 *  }
 *
 *  const MyNavigableContainer = () => (
 *    <div>
 *      <span>Navigable Menu:</span>
 *      <NavigableMenu onNavigate={ onNavigate } orientation="horizontal">
 *        <Button variant="secondary">Item 1</Button>
 *        <Button variant="secondary">Item 2</Button>
 *        <Button variant="secondary">Item 3</Button>
 *      </NavigableMenu>
 *    </div>
 *  );
 *  ```
 */
const NavigableMenu = (0,react.forwardRef)(UnforwardedNavigableMenu);
/* harmony default export */ const menu = (NavigableMenu);
//# sourceMappingURL=menu.js.map

/***/ }),

/***/ "../../node_modules/.pnpm/@wordpress+components@28.0.3_@emotion+is-prop-valid@1.2.1_@types+react@18.3.16_react-dom@18.3_mbjd55jx3gsragjgwncwdigc7u/node_modules/@wordpress/components/build-module/utils/hooks/use-controlled-value.js":
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   j: () => (/* binding */ useControlledValue)
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("../../node_modules/.pnpm/react@18.3.1/node_modules/react/index.js");
/**
 * WordPress dependencies
 */

/**
 * Simplified and improved implementation of useControlledState.
 *
 * @param props
 * @param props.defaultValue
 * @param props.value
 * @param props.onChange
 * @return The controlled value and the value setter.
 */
function useControlledValue({
  defaultValue,
  onChange,
  value: valueProp
}) {
  const hasValue = typeof valueProp !== 'undefined';
  const initialValue = hasValue ? valueProp : defaultValue;
  const [state, setState] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(initialValue);
  const value = hasValue ? valueProp : state;
  let setValue;
  if (hasValue && typeof onChange === 'function') {
    setValue = onChange;
  } else if (!hasValue && typeof onChange === 'function') {
    setValue = nextValue => {
      onChange(nextValue);
      setState(nextValue);
    };
  } else {
    setValue = setState;
  }
  return [value, setValue];
}
//# sourceMappingURL=use-controlled-value.js.map

/***/ }),

/***/ "../../packages/js/product-editor/src/components/button-with-dropdown-menu/stories/button-with-dropdown-menu.story.tsx":
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  Default: () => (/* binding */ Default),
  "default": () => (/* binding */ button_with_dropdown_menu_story)
});

// EXTERNAL MODULE: ../../node_modules/.pnpm/core-js@3.34.0/node_modules/core-js/modules/es.symbol.js
var es_symbol = __webpack_require__("../../node_modules/.pnpm/core-js@3.34.0/node_modules/core-js/modules/es.symbol.js");
// EXTERNAL MODULE: ../../node_modules/.pnpm/core-js@3.34.0/node_modules/core-js/modules/es.array.filter.js
var es_array_filter = __webpack_require__("../../node_modules/.pnpm/core-js@3.34.0/node_modules/core-js/modules/es.array.filter.js");
// EXTERNAL MODULE: ../../node_modules/.pnpm/core-js@3.34.0/node_modules/core-js/modules/es.array.for-each.js
var es_array_for_each = __webpack_require__("../../node_modules/.pnpm/core-js@3.34.0/node_modules/core-js/modules/es.array.for-each.js");
// EXTERNAL MODULE: ../../node_modules/.pnpm/core-js@3.34.0/node_modules/core-js/modules/es.object.define-properties.js
var es_object_define_properties = __webpack_require__("../../node_modules/.pnpm/core-js@3.34.0/node_modules/core-js/modules/es.object.define-properties.js");
// EXTERNAL MODULE: ../../node_modules/.pnpm/core-js@3.34.0/node_modules/core-js/modules/es.object.define-property.js
var es_object_define_property = __webpack_require__("../../node_modules/.pnpm/core-js@3.34.0/node_modules/core-js/modules/es.object.define-property.js");
// EXTERNAL MODULE: ../../node_modules/.pnpm/core-js@3.34.0/node_modules/core-js/modules/es.object.get-own-property-descriptor.js
var es_object_get_own_property_descriptor = __webpack_require__("../../node_modules/.pnpm/core-js@3.34.0/node_modules/core-js/modules/es.object.get-own-property-descriptor.js");
// EXTERNAL MODULE: ../../node_modules/.pnpm/core-js@3.34.0/node_modules/core-js/modules/es.object.get-own-property-descriptors.js
var es_object_get_own_property_descriptors = __webpack_require__("../../node_modules/.pnpm/core-js@3.34.0/node_modules/core-js/modules/es.object.get-own-property-descriptors.js");
// EXTERNAL MODULE: ../../node_modules/.pnpm/core-js@3.34.0/node_modules/core-js/modules/es.object.keys.js
var es_object_keys = __webpack_require__("../../node_modules/.pnpm/core-js@3.34.0/node_modules/core-js/modules/es.object.keys.js");
// EXTERNAL MODULE: ../../node_modules/.pnpm/core-js@3.34.0/node_modules/core-js/modules/es.object.to-string.js
var es_object_to_string = __webpack_require__("../../node_modules/.pnpm/core-js@3.34.0/node_modules/core-js/modules/es.object.to-string.js");
// EXTERNAL MODULE: ../../node_modules/.pnpm/core-js@3.34.0/node_modules/core-js/modules/web.dom-collections.for-each.js
var web_dom_collections_for_each = __webpack_require__("../../node_modules/.pnpm/core-js@3.34.0/node_modules/core-js/modules/web.dom-collections.for-each.js");
// EXTERNAL MODULE: ../../node_modules/.pnpm/@babel+runtime@7.25.7/node_modules/@babel/runtime/helpers/esm/defineProperty.js
var defineProperty = __webpack_require__("../../node_modules/.pnpm/@babel+runtime@7.25.7/node_modules/@babel/runtime/helpers/esm/defineProperty.js");
// EXTERNAL MODULE: ../../node_modules/.pnpm/@babel+runtime@7.25.7/node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js + 1 modules
var objectWithoutProperties = __webpack_require__("../../node_modules/.pnpm/@babel+runtime@7.25.7/node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js");
// EXTERNAL MODULE: ../../node_modules/.pnpm/@wordpress+primitives@4.11.0_react@18.3.1/node_modules/@wordpress/primitives/build-module/svg/index.js
var svg = __webpack_require__("../../node_modules/.pnpm/@wordpress+primitives@4.11.0_react@18.3.1/node_modules/@wordpress/primitives/build-module/svg/index.js");
// EXTERNAL MODULE: ../../node_modules/.pnpm/react@18.3.1/node_modules/react/jsx-runtime.js
var jsx_runtime = __webpack_require__("../../node_modules/.pnpm/react@18.3.1/node_modules/react/jsx-runtime.js");
;// ../../node_modules/.pnpm/@wordpress+icons@10.6.0_react@18.3.1/node_modules/@wordpress/icons/build-module/library/chevron-down.js
/**
 * WordPress dependencies
 */


const chevronDown = /*#__PURE__*/(0,jsx_runtime.jsx)(svg/* SVG */.t4, {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg",
  children: /*#__PURE__*/(0,jsx_runtime.jsx)(svg/* Path */.wA, {
    d: "M17.5 11.6L12 16l-5.5-4.4.9-1.2L12 14l4.5-3.6 1 1.2z"
  })
});
/* harmony default export */ const chevron_down = (chevronDown);
//# sourceMappingURL=chevron-down.js.map
// EXTERNAL MODULE: ../../node_modules/.pnpm/@wordpress+components@28.0.3_@emotion+is-prop-valid@1.2.1_@types+react@18.3.16_react-dom@18.3_mbjd55jx3gsragjgwncwdigc7u/node_modules/@wordpress/components/build-module/flex/flex/component.js
var component = __webpack_require__("../../node_modules/.pnpm/@wordpress+components@28.0.3_@emotion+is-prop-valid@1.2.1_@types+react@18.3.16_react-dom@18.3_mbjd55jx3gsragjgwncwdigc7u/node_modules/@wordpress/components/build-module/flex/flex/component.js");
// EXTERNAL MODULE: ../../node_modules/.pnpm/@wordpress+components@28.0.3_@emotion+is-prop-valid@1.2.1_@types+react@18.3.16_react-dom@18.3_mbjd55jx3gsragjgwncwdigc7u/node_modules/@wordpress/components/build-module/flex/flex-item/component.js
var flex_item_component = __webpack_require__("../../node_modules/.pnpm/@wordpress+components@28.0.3_@emotion+is-prop-valid@1.2.1_@types+react@18.3.16_react-dom@18.3_mbjd55jx3gsragjgwncwdigc7u/node_modules/@wordpress/components/build-module/flex/flex-item/component.js");
// EXTERNAL MODULE: ../../node_modules/.pnpm/@wordpress+components@28.0.3_@emotion+is-prop-valid@1.2.1_@types+react@18.3.16_react-dom@18.3_mbjd55jx3gsragjgwncwdigc7u/node_modules/@wordpress/components/build-module/button/index.js
var build_module_button = __webpack_require__("../../node_modules/.pnpm/@wordpress+components@28.0.3_@emotion+is-prop-valid@1.2.1_@types+react@18.3.16_react-dom@18.3_mbjd55jx3gsragjgwncwdigc7u/node_modules/@wordpress/components/build-module/button/index.js");
// EXTERNAL MODULE: ../../node_modules/.pnpm/@wordpress+components@28.0.3_@emotion+is-prop-valid@1.2.1_@types+react@18.3.16_react-dom@18.3_mbjd55jx3gsragjgwncwdigc7u/node_modules/@wordpress/components/build-module/dropdown-menu/index.js + 1 modules
var dropdown_menu = __webpack_require__("../../node_modules/.pnpm/@wordpress+components@28.0.3_@emotion+is-prop-valid@1.2.1_@types+react@18.3.16_react-dom@18.3_mbjd55jx3gsragjgwncwdigc7u/node_modules/@wordpress/components/build-module/dropdown-menu/index.js");
// EXTERNAL MODULE: ../../node_modules/.pnpm/@wordpress+i18n@5.0.1/node_modules/@wordpress/i18n/build-module/index.js + 3 modules
var build_module = __webpack_require__("../../node_modules/.pnpm/@wordpress+i18n@5.0.1/node_modules/@wordpress/i18n/build-module/index.js");
;// ../../packages/js/product-editor/src/components/button-with-dropdown-menu/index.tsx












var _excluded = ["dropdownButtonLabel", "controls", "defaultOpen", "popoverProps", "className", "renderMenu", "onToggle"];
function ownKeys(e, r) {
  var t = Object.keys(e);
  if (Object.getOwnPropertySymbols) {
    var o = Object.getOwnPropertySymbols(e);
    r && (o = o.filter(function (r) {
      return Object.getOwnPropertyDescriptor(e, r).enumerable;
    })), t.push.apply(t, o);
  }
  return t;
}
function _objectSpread(e) {
  for (var r = 1; r < arguments.length; r++) {
    var t = null != arguments[r] ? arguments[r] : {};
    r % 2 ? ownKeys(Object(t), !0).forEach(function (r) {
      (0,defineProperty/* default */.A)(e, r, t[r]);
    }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) {
      Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r));
    });
  }
  return e;
}
/**
 * External dependencies
 */





/**
 * Internal dependencies
 */


function ButtonWithDropdownMenu(_ref) {
  var _ref$dropdownButtonLa = _ref.dropdownButtonLabel,
    dropdownButtonLabel = _ref$dropdownButtonLa === void 0 ? (0,build_module.__)('More options', 'woocommerce') : _ref$dropdownButtonLa,
    controls = _ref.controls,
    _ref$defaultOpen = _ref.defaultOpen,
    defaultOpen = _ref$defaultOpen === void 0 ? false : _ref$defaultOpen,
    _ref$popoverProps = _ref.popoverProps,
    _ref$popoverProps2 = _ref$popoverProps === void 0 ? {
      placement: 'bottom-end',
      position: 'bottom left left',
      offset: 0
    } : _ref$popoverProps,
    _ref$popoverProps2$pl = _ref$popoverProps2.placement,
    placement = _ref$popoverProps2$pl === void 0 ? 'bottom-end' : _ref$popoverProps2$pl,
    _ref$popoverProps2$po = _ref$popoverProps2.position,
    position = _ref$popoverProps2$po === void 0 ? 'bottom left left' : _ref$popoverProps2$po,
    _ref$popoverProps2$of = _ref$popoverProps2.offset,
    offset = _ref$popoverProps2$of === void 0 ? 0 : _ref$popoverProps2$of,
    className = _ref.className,
    renderMenu = _ref.renderMenu,
    onToggle = _ref.onToggle,
    props = (0,objectWithoutProperties/* default */.A)(_ref, _excluded);
  return /*#__PURE__*/(0,jsx_runtime.jsxs)(component/* default */.A, {
    className: "woocommerce-button-with-dropdown-menu".concat(className !== null && className !== void 0 && className.length ? ' ' + className : ''),
    justify: "left",
    gap: 0,
    expanded: false,
    role: "group",
    children: [/*#__PURE__*/(0,jsx_runtime.jsx)(flex_item_component/* default */.A, {
      role: "none",
      children: /*#__PURE__*/(0,jsx_runtime.jsx)(build_module_button/* default */.Ay, _objectSpread(_objectSpread({}, props), {}, {
        className: "woocommerce-button-with-dropdown-menu__main-button"
      }))
    }), /*#__PURE__*/(0,jsx_runtime.jsx)(flex_item_component/* default */.A, {
      role: "none",
      children: /*#__PURE__*/(0,jsx_runtime.jsx)(dropdown_menu/* default */.A, {
        toggleProps: {
          className: 'woocommerce-button-with-dropdown-menu__dropdown-button',
          variant: props.variant
        },
        controls: controls,
        icon: chevron_down,
        label: dropdownButtonLabel,
        popoverProps: {
          placement: placement,
          position: position,
          offset: offset
        },
        defaultOpen: defaultOpen,
        onToggle: onToggle,
        children: renderMenu
      })
    })]
  });
}
try {
    // @ts-ignore
    ButtonWithDropdownMenu.displayName = "ButtonWithDropdownMenu";
    // @ts-ignore
    ButtonWithDropdownMenu.__docgenInfo = { "description": "", "displayName": "ButtonWithDropdownMenu", "props": { "className": { "defaultValue": null, "description": "", "name": "className", "required": false, "type": { "name": "string" } }, "href": { "defaultValue": null, "description": "If provided, renders `a` instead of `button`.", "name": "href", "required": false, "type": { "name": "string" } }, "dropdownButtonLabel": { "defaultValue": { value: "__( 'More options', 'woocommerce' )" }, "description": "", "name": "dropdownButtonLabel", "required": false, "type": { "name": "string" } }, "defaultOpen": { "defaultValue": { value: "false" }, "description": "", "name": "defaultOpen", "required": false, "type": { "name": "boolean" } }, "controls": { "defaultValue": null, "description": "", "name": "controls", "required": false, "type": { "name": "DropdownOption[]" } }, "popoverProps": { "defaultValue": null, "description": "", "name": "popoverProps", "required": false, "type": { "name": "PopoverProps" } }, "renderMenu": { "defaultValue": null, "description": "", "name": "renderMenu", "required": false, "type": { "name": "((props: { onClose: () => void; }) => ReactNode)" } }, "onToggle": { "defaultValue": null, "description": "", "name": "onToggle", "required": false, "type": { "name": "((isOpen: boolean) => void)" } }, "__next40pxDefaultSize": { "defaultValue": { value: "false" }, "description": "Start opting into the larger default height that will become the\ndefault size in a future version.", "name": "__next40pxDefaultSize", "required": false, "type": { "name": "boolean" } }, "children": { "defaultValue": null, "description": "The button's children.", "name": "children", "required": false, "type": { "name": "ReactNode" } }, "describedBy": { "defaultValue": null, "description": "An accessible description for the button.", "name": "describedBy", "required": false, "type": { "name": "string" } }, "icon": { "defaultValue": null, "description": "If provided, renders an Icon component inside the button.", "name": "icon", "required": false, "type": { "name": "IconType | null" } }, "iconPosition": { "defaultValue": { value: "'left'" }, "description": "If provided with `icon`, sets the position of icon relative to the `text`.", "name": "iconPosition", "required": false, "type": { "name": "enum", "value": [{ "value": "\"left\"" }, { "value": "\"right\"" }] } }, "iconSize": { "defaultValue": null, "description": "If provided with `icon`, sets the icon size.\nPlease refer to the Icon component for more details regarding\nthe default value of its `size` prop.", "name": "iconSize", "required": false, "type": { "name": "number" } }, "isBusy": { "defaultValue": null, "description": "Indicates activity while a action is being performed.", "name": "isBusy", "required": false, "type": { "name": "boolean" } }, "isDestructive": { "defaultValue": null, "description": "Renders a red text-based button style to indicate destructive behavior.", "name": "isDestructive", "required": false, "type": { "name": "boolean" } }, "isPressed": { "defaultValue": null, "description": "Renders a pressed button style.", "name": "isPressed", "required": false, "type": { "name": "boolean" } }, "label": { "defaultValue": null, "description": "Sets the `aria-label` of the component, if none is provided.\nSets the Tooltip content if `showTooltip` is provided.", "name": "label", "required": false, "type": { "name": "string" } }, "shortcut": { "defaultValue": null, "description": "If provided with `showTooltip`, appends the Shortcut label to the tooltip content.\nIf an object is provided, it should contain `display` and `ariaLabel` keys.", "name": "shortcut", "required": false, "type": { "name": "string | { display: string; ariaLabel: string; }" } }, "showTooltip": { "defaultValue": null, "description": "If provided, renders a Tooltip component for the button.", "name": "showTooltip", "required": false, "type": { "name": "boolean" } }, "size": { "defaultValue": { value: "'default'" }, "description": "The size of the button.\n\n- `'default'`: For normal text-label buttons, unless it is a toggle button.\n- `'compact'`: For toggle buttons, icon buttons, and buttons when used in context of either.\n- `'small'`: For icon buttons associated with more advanced or auxiliary features.\n\nIf the deprecated `isSmall` prop is also defined, this prop will take precedence.", "name": "size", "required": false, "type": { "name": "enum", "value": [{ "value": "\"small\"" }, { "value": "\"default\"" }, { "value": "\"compact\"" }] } }, "text": { "defaultValue": null, "description": "If provided, displays the given text inside the button. If the button contains children elements, the text is displayed before them.", "name": "text", "required": false, "type": { "name": "string" } }, "tooltipPosition": { "defaultValue": null, "description": "If provided with `showTooltip`, sets the position of the tooltip.\nPlease refer to the Tooltip component for more details regarding the defaults.", "name": "tooltipPosition", "required": false, "type": { "name": "enum", "value": [{ "value": "\"top left\"" }, { "value": "\"top right\"" }, { "value": "\"top center\"" }, { "value": "\"middle left\"" }, { "value": "\"middle right\"" }, { "value": "\"middle center\"" }, { "value": "\"bottom left\"" }, { "value": "\"bottom right\"" }, { "value": "\"bottom center\"" }, { "value": "\"top\"" }, { "value": "\"bottom\"" }, { "value": "\"bottom left left\"" }, { "value": "\"middle\"" }, { "value": "\"top center top\"" }, { "value": "\"top center bottom\"" }, { "value": "\"top center left\"" }, { "value": "\"top center right\"" }, { "value": "\"top left top\"" }, { "value": "\"top left bottom\"" }, { "value": "\"top left left\"" }, { "value": "\"top left right\"" }, { "value": "\"top right top\"" }, { "value": "\"top right bottom\"" }, { "value": "\"top right left\"" }, { "value": "\"top right right\"" }, { "value": "\"bottom center top\"" }, { "value": "\"bottom center bottom\"" }, { "value": "\"bottom center left\"" }, { "value": "\"bottom center right\"" }, { "value": "\"bottom left top\"" }, { "value": "\"bottom left bottom\"" }, { "value": "\"bottom left right\"" }, { "value": "\"bottom right top\"" }, { "value": "\"bottom right bottom\"" }, { "value": "\"bottom right left\"" }, { "value": "\"bottom right right\"" }, { "value": "\"middle center top\"" }, { "value": "\"middle center bottom\"" }, { "value": "\"middle center left\"" }, { "value": "\"middle center right\"" }, { "value": "\"middle left top\"" }, { "value": "\"middle left bottom\"" }, { "value": "\"middle left left\"" }, { "value": "\"middle left right\"" }, { "value": "\"middle right top\"" }, { "value": "\"middle right bottom\"" }, { "value": "\"middle right left\"" }, { "value": "\"middle right right\"" }] } }, "variant": { "defaultValue": null, "description": "Specifies the button's style.\nThe accepted values are:\n'primary' (the primary button styles)\n'secondary' (the default button styles)\n'tertiary' (the text-based button styles)\n'link' (the link button styles)", "name": "variant", "required": false, "type": { "name": "enum", "value": [{ "value": "\"link\"" }, { "value": "\"secondary\"" }, { "value": "\"primary\"" }, { "value": "\"tertiary\"" }] } }, "__experimentalIsFocusable": { "defaultValue": { value: "false" }, "description": "Whether to keep the button focusable when disabled.", "name": "__experimentalIsFocusable", "required": false, "type": { "name": "boolean" } }, "disabled": { "defaultValue": null, "description": "Whether the button is disabled.\n\nIf `true`, this will force a `button` element to be rendered, even when an `href` is given.", "name": "disabled", "required": false, "type": { "name": "boolean" } }, "isDefault": { "defaultValue": null, "description": "Gives the button a default style.\n@deprecated Use the `'secondary'` value on the `variant` prop instead.\n@ignore", "name": "isDefault", "required": false, "type": { "name": "boolean" } }, "isLink": { "defaultValue": null, "description": "Gives the button a link style.\n@deprecated Use the `'link'` value on the `variant` prop instead.\n@ignore", "name": "isLink", "required": false, "type": { "name": "boolean" } }, "isPrimary": { "defaultValue": null, "description": "Gives the button a primary style.\n@deprecated Use the `'primary'` value on the `variant` prop instead.\n@ignore", "name": "isPrimary", "required": false, "type": { "name": "boolean" } }, "isSecondary": { "defaultValue": null, "description": "Gives the button a default style.\n@deprecated Use the `'secondary'` value on the `variant` prop instead.\n@ignore", "name": "isSecondary", "required": false, "type": { "name": "boolean" } }, "isTertiary": { "defaultValue": null, "description": "Gives the button a text-based style.\n@deprecated Use the `'tertiary'` value on the `variant` prop instead.\n@ignore", "name": "isTertiary", "required": false, "type": { "name": "boolean" } }, "isSmall": { "defaultValue": null, "description": "Decreases the size of the button.\n@deprecated Use the `'small'` value on the `size` prop instead.\n@ignore", "name": "isSmall", "required": false, "type": { "name": "boolean" } }, "target": { "defaultValue": null, "description": "If provided with `href`, sets the `target` attribute to the `a`.", "name": "target", "required": false, "type": { "name": "string" } } } };
    // @ts-ignore
    if (typeof STORYBOOK_REACT_CLASSES !== "undefined")
        // @ts-ignore
        STORYBOOK_REACT_CLASSES["../../packages/js/product-editor/src/components/button-with-dropdown-menu/index.tsx#ButtonWithDropdownMenu"] = { docgenInfo: ButtonWithDropdownMenu.__docgenInfo, name: "ButtonWithDropdownMenu", path: "../../packages/js/product-editor/src/components/button-with-dropdown-menu/index.tsx#ButtonWithDropdownMenu" };
}
catch (__react_docgen_typescript_loader_error) { }
;// ../../packages/js/product-editor/src/components/button-with-dropdown-menu/stories/button-with-dropdown-menu.story.tsx











function button_with_dropdown_menu_story_ownKeys(e, r) {
  var t = Object.keys(e);
  if (Object.getOwnPropertySymbols) {
    var o = Object.getOwnPropertySymbols(e);
    r && (o = o.filter(function (r) {
      return Object.getOwnPropertyDescriptor(e, r).enumerable;
    })), t.push.apply(t, o);
  }
  return t;
}
function button_with_dropdown_menu_story_objectSpread(e) {
  for (var r = 1; r < arguments.length; r++) {
    var t = null != arguments[r] ? arguments[r] : {};
    r % 2 ? button_with_dropdown_menu_story_ownKeys(Object(t), !0).forEach(function (r) {
      (0,defineProperty/* default */.A)(e, r, t[r]);
    }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : button_with_dropdown_menu_story_ownKeys(Object(t)).forEach(function (r) {
      Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r));
    });
  }
  return e;
}
/**
 * Internal dependencies
 */


/* harmony default export */ const button_with_dropdown_menu_story = ({
  title: 'Product Editor/components/ButtonWithDropdownMenu',
  component: ButtonWithDropdownMenu
});
var Default = function Default(args) {
  return /*#__PURE__*/(0,jsx_runtime.jsx)("div", {
    style: {
      display: 'flex',
      justifyContent: 'center',
      minHeight: '300px'
    },
    children: /*#__PURE__*/(0,jsx_runtime.jsx)(ButtonWithDropdownMenu, button_with_dropdown_menu_story_objectSpread({}, args))
  });
};
Default.args = {
  children: 'Add to store',
  dropdownButtonLabel: 'More options',
  variant: 'secondary',
  defaultOpen: false,
  popoverProps: {
    placement: 'bottom-end',
    position: 'bottom left left',
    offset: 0
  },
  controls: [{
    title: 'First Menu Item Label',
    onClick: function noRefCheck() {}
  }, {
    onClick: function noRefCheck() {},
    title: 'Second Menu Item Label'
  }],
  className: 'my-custom-classname',
  onClick: console.log // eslint-disable-line no-console
};
Default.parameters = {
  ...Default.parameters,
  docs: {
    ...Default.parameters?.docs,
    source: {
      originalSource: "(args: ButtonWithDropdownMenuProps) => {\n  return <div style={{\n    display: 'flex',\n    justifyContent: 'center',\n    minHeight: '300px'\n  }}>\n            <ButtonWithDropdownMenu {...args} />\n        </div>;\n}",
      ...Default.parameters?.docs?.source
    }
  }
};
try {
    // @ts-ignore
    Default.displayName = "Default";
    // @ts-ignore
    Default.__docgenInfo = { "description": "", "displayName": "Default", "props": { "className": { "defaultValue": null, "description": "", "name": "className", "required": false, "type": { "name": "string" } }, "href": { "defaultValue": null, "description": "If provided, renders `a` instead of `button`.", "name": "href", "required": false, "type": { "name": "string" } }, "dropdownButtonLabel": { "defaultValue": null, "description": "", "name": "dropdownButtonLabel", "required": false, "type": { "name": "string" } }, "defaultOpen": { "defaultValue": null, "description": "", "name": "defaultOpen", "required": false, "type": { "name": "boolean" } }, "controls": { "defaultValue": null, "description": "", "name": "controls", "required": false, "type": { "name": "DropdownOption[]" } }, "popoverProps": { "defaultValue": null, "description": "", "name": "popoverProps", "required": false, "type": { "name": "PopoverProps" } }, "renderMenu": { "defaultValue": null, "description": "", "name": "renderMenu", "required": false, "type": { "name": "((props: { onClose: () => void; }) => ReactNode)" } }, "onToggle": { "defaultValue": null, "description": "", "name": "onToggle", "required": false, "type": { "name": "((isOpen: boolean) => void)" } }, "__next40pxDefaultSize": { "defaultValue": { value: "false" }, "description": "Start opting into the larger default height that will become the\ndefault size in a future version.", "name": "__next40pxDefaultSize", "required": false, "type": { "name": "boolean" } }, "children": { "defaultValue": null, "description": "The button's children.", "name": "children", "required": false, "type": { "name": "ReactNode" } }, "describedBy": { "defaultValue": null, "description": "An accessible description for the button.", "name": "describedBy", "required": false, "type": { "name": "string" } }, "icon": { "defaultValue": null, "description": "If provided, renders an Icon component inside the button.", "name": "icon", "required": false, "type": { "name": "IconType | null" } }, "iconPosition": { "defaultValue": { value: "'left'" }, "description": "If provided with `icon`, sets the position of icon relative to the `text`.", "name": "iconPosition", "required": false, "type": { "name": "enum", "value": [{ "value": "\"left\"" }, { "value": "\"right\"" }] } }, "iconSize": { "defaultValue": null, "description": "If provided with `icon`, sets the icon size.\nPlease refer to the Icon component for more details regarding\nthe default value of its `size` prop.", "name": "iconSize", "required": false, "type": { "name": "number" } }, "isBusy": { "defaultValue": null, "description": "Indicates activity while a action is being performed.", "name": "isBusy", "required": false, "type": { "name": "boolean" } }, "isDestructive": { "defaultValue": null, "description": "Renders a red text-based button style to indicate destructive behavior.", "name": "isDestructive", "required": false, "type": { "name": "boolean" } }, "isPressed": { "defaultValue": null, "description": "Renders a pressed button style.", "name": "isPressed", "required": false, "type": { "name": "boolean" } }, "label": { "defaultValue": null, "description": "Sets the `aria-label` of the component, if none is provided.\nSets the Tooltip content if `showTooltip` is provided.", "name": "label", "required": false, "type": { "name": "string" } }, "shortcut": { "defaultValue": null, "description": "If provided with `showTooltip`, appends the Shortcut label to the tooltip content.\nIf an object is provided, it should contain `display` and `ariaLabel` keys.", "name": "shortcut", "required": false, "type": { "name": "string | { display: string; ariaLabel: string; }" } }, "showTooltip": { "defaultValue": null, "description": "If provided, renders a Tooltip component for the button.", "name": "showTooltip", "required": false, "type": { "name": "boolean" } }, "size": { "defaultValue": { value: "'default'" }, "description": "The size of the button.\n\n- `'default'`: For normal text-label buttons, unless it is a toggle button.\n- `'compact'`: For toggle buttons, icon buttons, and buttons when used in context of either.\n- `'small'`: For icon buttons associated with more advanced or auxiliary features.\n\nIf the deprecated `isSmall` prop is also defined, this prop will take precedence.", "name": "size", "required": false, "type": { "name": "enum", "value": [{ "value": "\"small\"" }, { "value": "\"default\"" }, { "value": "\"compact\"" }] } }, "text": { "defaultValue": null, "description": "If provided, displays the given text inside the button. If the button contains children elements, the text is displayed before them.", "name": "text", "required": false, "type": { "name": "string" } }, "tooltipPosition": { "defaultValue": null, "description": "If provided with `showTooltip`, sets the position of the tooltip.\nPlease refer to the Tooltip component for more details regarding the defaults.", "name": "tooltipPosition", "required": false, "type": { "name": "enum", "value": [{ "value": "\"top left\"" }, { "value": "\"top right\"" }, { "value": "\"top center\"" }, { "value": "\"middle left\"" }, { "value": "\"middle right\"" }, { "value": "\"middle center\"" }, { "value": "\"bottom left\"" }, { "value": "\"bottom right\"" }, { "value": "\"bottom center\"" }, { "value": "\"top\"" }, { "value": "\"bottom\"" }, { "value": "\"bottom left left\"" }, { "value": "\"middle\"" }, { "value": "\"top center top\"" }, { "value": "\"top center bottom\"" }, { "value": "\"top center left\"" }, { "value": "\"top center right\"" }, { "value": "\"top left top\"" }, { "value": "\"top left bottom\"" }, { "value": "\"top left left\"" }, { "value": "\"top left right\"" }, { "value": "\"top right top\"" }, { "value": "\"top right bottom\"" }, { "value": "\"top right left\"" }, { "value": "\"top right right\"" }, { "value": "\"bottom center top\"" }, { "value": "\"bottom center bottom\"" }, { "value": "\"bottom center left\"" }, { "value": "\"bottom center right\"" }, { "value": "\"bottom left top\"" }, { "value": "\"bottom left bottom\"" }, { "value": "\"bottom left right\"" }, { "value": "\"bottom right top\"" }, { "value": "\"bottom right bottom\"" }, { "value": "\"bottom right left\"" }, { "value": "\"bottom right right\"" }, { "value": "\"middle center top\"" }, { "value": "\"middle center bottom\"" }, { "value": "\"middle center left\"" }, { "value": "\"middle center right\"" }, { "value": "\"middle left top\"" }, { "value": "\"middle left bottom\"" }, { "value": "\"middle left left\"" }, { "value": "\"middle left right\"" }, { "value": "\"middle right top\"" }, { "value": "\"middle right bottom\"" }, { "value": "\"middle right left\"" }, { "value": "\"middle right right\"" }] } }, "variant": { "defaultValue": null, "description": "Specifies the button's style.\nThe accepted values are:\n'primary' (the primary button styles)\n'secondary' (the default button styles)\n'tertiary' (the text-based button styles)\n'link' (the link button styles)", "name": "variant", "required": false, "type": { "name": "enum", "value": [{ "value": "\"link\"" }, { "value": "\"secondary\"" }, { "value": "\"primary\"" }, { "value": "\"tertiary\"" }] } }, "__experimentalIsFocusable": { "defaultValue": { value: "false" }, "description": "Whether to keep the button focusable when disabled.", "name": "__experimentalIsFocusable", "required": false, "type": { "name": "boolean" } }, "disabled": { "defaultValue": null, "description": "Whether the button is disabled.\n\nIf `true`, this will force a `button` element to be rendered, even when an `href` is given.", "name": "disabled", "required": false, "type": { "name": "boolean" } }, "isDefault": { "defaultValue": null, "description": "Gives the button a default style.\n@deprecated Use the `'secondary'` value on the `variant` prop instead.\n@ignore", "name": "isDefault", "required": false, "type": { "name": "boolean" } }, "isLink": { "defaultValue": null, "description": "Gives the button a link style.\n@deprecated Use the `'link'` value on the `variant` prop instead.\n@ignore", "name": "isLink", "required": false, "type": { "name": "boolean" } }, "isPrimary": { "defaultValue": null, "description": "Gives the button a primary style.\n@deprecated Use the `'primary'` value on the `variant` prop instead.\n@ignore", "name": "isPrimary", "required": false, "type": { "name": "boolean" } }, "isSecondary": { "defaultValue": null, "description": "Gives the button a default style.\n@deprecated Use the `'secondary'` value on the `variant` prop instead.\n@ignore", "name": "isSecondary", "required": false, "type": { "name": "boolean" } }, "isTertiary": { "defaultValue": null, "description": "Gives the button a text-based style.\n@deprecated Use the `'tertiary'` value on the `variant` prop instead.\n@ignore", "name": "isTertiary", "required": false, "type": { "name": "boolean" } }, "isSmall": { "defaultValue": null, "description": "Decreases the size of the button.\n@deprecated Use the `'small'` value on the `size` prop instead.\n@ignore", "name": "isSmall", "required": false, "type": { "name": "boolean" } }, "target": { "defaultValue": null, "description": "If provided with `href`, sets the `target` attribute to the `a`.", "name": "target", "required": false, "type": { "name": "string" } } } };
    // @ts-ignore
    if (typeof STORYBOOK_REACT_CLASSES !== "undefined")
        // @ts-ignore
        STORYBOOK_REACT_CLASSES["../../packages/js/product-editor/src/components/button-with-dropdown-menu/stories/button-with-dropdown-menu.story.tsx#Default"] = { docgenInfo: Default.__docgenInfo, name: "Default", path: "../../packages/js/product-editor/src/components/button-with-dropdown-menu/stories/button-with-dropdown-menu.story.tsx#Default" };
}
catch (__react_docgen_typescript_loader_error) { }

/***/ })

}]);