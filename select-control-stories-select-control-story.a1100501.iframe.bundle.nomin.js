"use strict";
(self["webpackChunk_woocommerce_storybook"] = self["webpackChunk_woocommerce_storybook"] || []).push([[2752],{

/***/ "../../packages/js/components/src/select-control/stories/select-control.story.tsx":
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Basic: () => (/* binding */ Basic),
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("../../node_modules/.pnpm/@babel+runtime@7.25.7/node_modules/@babel/runtime/helpers/esm/defineProperty.js");
/* harmony import */ var _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__("../../node_modules/.pnpm/@babel+runtime@7.25.7/node_modules/@babel/runtime/helpers/esm/slicedToArray.js");
/* harmony import */ var core_js_modules_es_array_from_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("../../node_modules/.pnpm/core-js@3.34.0/node_modules/core-js/modules/es.array.from.js");
/* harmony import */ var core_js_modules_es_array_from_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_from_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var core_js_modules_es_symbol_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__("../../node_modules/.pnpm/core-js@3.34.0/node_modules/core-js/modules/es.symbol.js");
/* harmony import */ var core_js_modules_es_symbol_js__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_symbol_js__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var core_js_modules_es_array_filter_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__("../../node_modules/.pnpm/core-js@3.34.0/node_modules/core-js/modules/es.array.filter.js");
/* harmony import */ var core_js_modules_es_array_filter_js__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_filter_js__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var core_js_modules_es_array_for_each_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__("../../node_modules/.pnpm/core-js@3.34.0/node_modules/core-js/modules/es.array.for-each.js");
/* harmony import */ var core_js_modules_es_array_for_each_js__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_for_each_js__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var core_js_modules_es_object_define_properties_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__("../../node_modules/.pnpm/core-js@3.34.0/node_modules/core-js/modules/es.object.define-properties.js");
/* harmony import */ var core_js_modules_es_object_define_properties_js__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_define_properties_js__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var core_js_modules_es_object_define_property_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__("../../node_modules/.pnpm/core-js@3.34.0/node_modules/core-js/modules/es.object.define-property.js");
/* harmony import */ var core_js_modules_es_object_define_property_js__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_define_property_js__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var core_js_modules_es_object_get_own_property_descriptor_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__("../../node_modules/.pnpm/core-js@3.34.0/node_modules/core-js/modules/es.object.get-own-property-descriptor.js");
/* harmony import */ var core_js_modules_es_object_get_own_property_descriptor_js__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_get_own_property_descriptor_js__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var core_js_modules_es_object_get_own_property_descriptors_js__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__("../../node_modules/.pnpm/core-js@3.34.0/node_modules/core-js/modules/es.object.get-own-property-descriptors.js");
/* harmony import */ var core_js_modules_es_object_get_own_property_descriptors_js__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_get_own_property_descriptors_js__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var core_js_modules_es_object_keys_js__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__("../../node_modules/.pnpm/core-js@3.34.0/node_modules/core-js/modules/es.object.keys.js");
/* harmony import */ var core_js_modules_es_object_keys_js__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_keys_js__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__("../../node_modules/.pnpm/core-js@3.34.0/node_modules/core-js/modules/es.object.to-string.js");
/* harmony import */ var core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_object_to_string_js__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var core_js_modules_es_string_iterator_js__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__("../../node_modules/.pnpm/core-js@3.34.0/node_modules/core-js/modules/es.string.iterator.js");
/* harmony import */ var core_js_modules_es_string_iterator_js__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_iterator_js__WEBPACK_IMPORTED_MODULE_11__);
/* harmony import */ var core_js_modules_web_dom_collections_for_each_js__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__("../../node_modules/.pnpm/core-js@3.34.0/node_modules/core-js/modules/web.dom-collections.for-each.js");
/* harmony import */ var core_js_modules_web_dom_collections_for_each_js__WEBPACK_IMPORTED_MODULE_12___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_web_dom_collections_for_each_js__WEBPACK_IMPORTED_MODULE_12__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__("../../node_modules/.pnpm/react@18.3.1/node_modules/react/index.js");
/* harmony import */ var ___WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__("../../packages/js/components/src/select-control/index.tsx");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__("../../node_modules/.pnpm/react@18.3.1/node_modules/react/jsx-runtime.js");


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
      (0,_babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__/* ["default"] */ .A)(e, r, t[r]);
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


var options = [{
  key: 'apple',
  label: 'Apple',
  value: {
    id: 'apple'
  }
}, {
  key: 'apricot',
  label: 'Apricot',
  value: {
    id: 'apricot'
  }
}, {
  key: 'banana',
  label: 'Banana',
  keywords: ['best', 'fruit'],
  value: {
    id: 'banana'
  }
}, {
  key: 'blueberry',
  label: 'Blueberry',
  value: {
    id: 'blueberry'
  }
}, {
  key: 'cherry',
  label: 'Cherry',
  value: {
    id: 'cherry'
  }
}, {
  key: 'cantaloupe',
  label: 'Cantaloupe',
  value: {
    id: 'cantaloupe'
  }
}, {
  key: 'dragonfruit',
  label: 'Dragon Fruit',
  value: {
    id: 'dragonfruit'
  }
}, {
  key: 'elderberry',
  label: 'Elderberry',
  value: {
    id: 'elderberry'
  }
}];

// Create a larger list of options for virtual scrolling example
var manyOptions = Array.from({
  length: 2000
}, function (_, index) {
  var key = "option-".concat(index + 1);
  return {
    key: key,
    label: "Option ".concat(index + 1),
    value: {
      id: key
    }
  };
});
var SelectControlExample = function SelectControlExample() {
  var _useState = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_14__.useState)({
      simpleSelected: [],
      simpleMultipleSelected: [],
      singleSelected: [],
      singleSelectedShowAll: [],
      multipleSelected: [],
      inlineSelected: [],
      allOptionsIncludingSelected: options[options.length - 1].key,
      virtualScrollSelected: [],
      disabledSelected: [{
        key: 'apple',
        label: 'Apple',
        value: {
          id: 'apple'
        }
      }, {
        key: 'banana',
        label: 'Banana',
        value: {
          id: 'banana'
        }
      }],
      disabledInlineSelected: [{
        key: 'apple',
        label: 'Apple',
        value: {
          id: 'apple'
        }
      }, {
        key: 'banana',
        label: 'Banana',
        value: {
          id: 'banana'
        }
      }]
    }),
    _useState2 = (0,_babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_15__/* ["default"] */ .A)(_useState, 2),
    state = _useState2[0],
    setState = _useState2[1];
  var simpleSelected = state.simpleSelected,
    simpleMultipleSelected = state.simpleMultipleSelected,
    singleSelected = state.singleSelected,
    singleSelectedShowAll = state.singleSelectedShowAll,
    multipleSelected = state.multipleSelected,
    inlineSelected = state.inlineSelected,
    allOptionsIncludingSelected = state.allOptionsIncludingSelected,
    virtualScrollSelected = state.virtualScrollSelected,
    disabledSelected = state.disabledSelected,
    disabledInlineSelected = state.disabledInlineSelected;
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsxs)("div", {
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsx)(___WEBPACK_IMPORTED_MODULE_16__/* ["default"] */ .A, {
      label: "Simple single value",
      onChange: function onChange(selected) {
        return setState(_objectSpread(_objectSpread({}, state), {}, {
          simpleSelected: selected
        }));
      },
      options: options,
      placeholder: "Start typing to filter options...",
      selected: simpleSelected
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsx)("br", {}), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsx)(___WEBPACK_IMPORTED_MODULE_16__/* ["default"] */ .A, {
      label: "Multiple values",
      multiple: true,
      onChange: function onChange(selected) {
        return setState(_objectSpread(_objectSpread({}, state), {}, {
          simpleMultipleSelected: selected
        }));
      },
      options: options,
      placeholder: "Start typing to filter options...",
      selected: simpleMultipleSelected
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsx)("br", {}), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsx)(___WEBPACK_IMPORTED_MODULE_16__/* ["default"] */ .A, {
      label: "Show all options with default selected",
      onChange: function onChange(selected) {
        return setState(_objectSpread(_objectSpread({}, state), {}, {
          allOptionsIncludingSelected: selected
        }));
      },
      options: options,
      placeholder: "Start typing to filter options...",
      selected: allOptionsIncludingSelected,
      showAllOnFocus: true,
      isSearchable: true,
      excludeSelectedOptions: false
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsx)("br", {}), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsx)(___WEBPACK_IMPORTED_MODULE_16__/* ["default"] */ .A, {
      label: "Single value searchable",
      isSearchable: true,
      onChange: function onChange(selected) {
        return setState(_objectSpread(_objectSpread({}, state), {}, {
          singleSelected: selected
        }));
      },
      options: options,
      placeholder: "Start typing to filter options...",
      selected: singleSelected
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsx)("br", {}), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsx)(___WEBPACK_IMPORTED_MODULE_16__/* ["default"] */ .A, {
      label: "Single value searchable with options on refocus",
      isSearchable: true,
      onChange: function onChange(selected) {
        return setState(_objectSpread(_objectSpread({}, state), {}, {
          singleSelectedShowAll: selected
        }));
      },
      options: options,
      placeholder: "Start typing to filter options...",
      selected: singleSelectedShowAll,
      showAllOnFocus: true
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsx)("br", {}), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsx)(___WEBPACK_IMPORTED_MODULE_16__/* ["default"] */ .A, {
      label: "Inline tags searchable",
      isSearchable: true,
      multiple: true,
      inlineTags: true,
      onChange: function onChange(selected) {
        return setState(_objectSpread(_objectSpread({}, state), {}, {
          inlineSelected: selected
        }));
      },
      options: options,
      placeholder: "Start typing to filter options...",
      selected: inlineSelected
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsx)("br", {}), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsx)(___WEBPACK_IMPORTED_MODULE_16__/* ["default"] */ .A, {
      hideBeforeSearch: true,
      isSearchable: true,
      label: "Hidden options before search",
      multiple: true,
      onChange: function onChange(selected) {
        return setState(_objectSpread(_objectSpread({}, state), {}, {
          multipleSelected: selected
        }));
      },
      options: options,
      placeholder: "Start typing to filter options...",
      selected: multipleSelected,
      showClearButton: true
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsx)("br", {}), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsx)(___WEBPACK_IMPORTED_MODULE_16__/* ["default"] */ .A, {
      label: "Virtual scrolling with many options",
      isSearchable: true,
      onChange: function onChange(selected) {
        return setState(_objectSpread(_objectSpread({}, state), {}, {
          virtualScrollSelected: selected
        }));
      },
      options: manyOptions,
      placeholder: "Start typing to filter options...",
      selected: virtualScrollSelected,
      showAllOnFocus: true,
      virtualScroll: true,
      virtualItemHeight: 56,
      virtualListHeight: 56 * 6
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsx)("br", {}), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsx)(___WEBPACK_IMPORTED_MODULE_16__/* ["default"] */ .A, {
      label: "Disabled select control",
      isSearchable: true,
      multiple: true,
      disabled: true,
      onChange: function onChange(selected) {
        return setState(_objectSpread(_objectSpread({}, state), {}, {
          disabledSelected: selected
        }));
      },
      options: options,
      placeholder: "Start typing to filter options...",
      selected: disabledSelected,
      showClearButton: true
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsx)("br", {}), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsx)(___WEBPACK_IMPORTED_MODULE_16__/* ["default"] */ .A, {
      label: "Disabled select control with inline tags",
      isSearchable: true,
      multiple: true,
      disabled: true,
      inlineTags: true,
      onChange: function onChange(selected) {
        return setState(_objectSpread(_objectSpread({}, state), {}, {
          disabledInlineSelected: selected
        }));
      },
      options: options,
      placeholder: "Start typing to filter options...",
      selected: disabledInlineSelected,
      showClearButton: true
    })]
  });
};
var Basic = function Basic() {
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_13__.jsx)(SelectControlExample, {});
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  title: 'Components/SelectControl',
  component: ___WEBPACK_IMPORTED_MODULE_16__/* ["default"] */ .A
});
Basic.parameters = {
  ...Basic.parameters,
  docs: {
    ...Basic.parameters?.docs,
    source: {
      originalSource: "() => <SelectControlExample />",
      ...Basic.parameters?.docs?.source
    }
  }
};

/***/ })

}]);