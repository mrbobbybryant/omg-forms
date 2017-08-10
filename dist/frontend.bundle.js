/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 1);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */,
/* 1 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _formSubmit = __webpack_require__(2);

var _formSubmit2 = _interopRequireDefault(_formSubmit);

var _index = __webpack_require__(5);

var _index2 = _interopRequireDefault(_index);

var _formEvents = __webpack_require__(6);

var _formEvents2 = _interopRequireDefault(_formEvents);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

document.addEventListener('DOMContentLoaded', function () {
  window.omg_events = _formEvents2.default;
  (0, _formSubmit2.default)(window.omg_events);
});

/***/ }),
/* 2 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

exports.default = function (Events) {
  var formButtons = document.querySelectorAll('.omg-form-submit-btn');

  if (!formButtons || 0 === formButtons.length) {
    return false;
  }

  [].forEach.call(formButtons, function (button) {
    var form = button.closest('form');
    var formWrapper = button.closest('.omg-form-wrapper');

    form.addEventListener('submit', function (e) {
      e.preventDefault();

      var data = new FormData(form);
      data.append('formId', form.getAttribute('id'));

      submitForm(data).then(function (response) {
        if ('omg_form_validation_fail' === response.code) {
          (0, _formErrors2.default)(response.data.fields);
          Events.emit('omg-form-field-errors', {
            fields: response.data.fields,
            formWrapper: formWrapper,
            form: form
          });
        }

        if (true === response) {
          (0, _formSuccess2.default)(formWrapper, form);
          Events.emit('omg-form-success', {
            formWrapper: formWrapper,
            form: form
          });
        }
      }).catch(function (error) {
        console.warn(error);
      });
    });
  });
};

var _formErrors = __webpack_require__(3);

var _formErrors2 = _interopRequireDefault(_formErrors);

var _formSuccess = __webpack_require__(4);

var _formSuccess2 = _interopRequireDefault(_formSuccess);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

var getFormElements = function getFormElements(button) {
  var form = button.closest('form');
  return form.querySelectorAll("[name^='omg-forms-']");
};

var getFormData = function getFormData(button) {
  var info = {};
  var elements = getFormElements(button);
  var form = button.closest('form');

  info.form = form.getAttribute('id');

  info.fields = [].map.call(elements, function (element) {
    return {
      value: element.value,
      type: getFieldType(element),
      id: element.getAttribute('name'),
      required: element.dataset.required
    };
  });

  return info;
};

var getFieldType = function getFieldType(field) {
  var type = field.getAttribute('type');

  if (type) {
    return type;
  }
  type = field.tagName.toLowerCase();

  if (type) {
    return type;
  }
};

var submitForm = function submitForm(data) {
  var endpoint = OMGForms.baseURL + '/wp-json/wp/v2/entries';
  return new Promise(function (resolve, reject) {

    var xhr = new XMLHttpRequest();

    xhr.addEventListener("load", function (evt) {
      return resolve(JSON.parse(xhr.response));
    }, false);

    xhr.addEventListener("error", function (error) {
      return reject(JSON.parse(error));
    }, false);

    xhr.addEventListener("abort", function (error) {
      return reject(JSON.parse(error));
    }, false);

    xhr.open('POST', endpoint, true);

    xhr.setRequestHeader('X-WP-NONCE', OMGForms.nonce);
    xhr.send(data);
  });
};

/***/ }),
/* 3 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

exports.default = function (errors) {
  var fields = errors.map(function (error) {
    return document.getElementById(error);
  });

  if (0 === fields.length) {
    return false;
  }

  fields.forEach(function (field) {
    return field.classList.add('show');
  });
};

/***/ }),
/* 4 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

exports.default = function (wrapper, form) {
  var successMessage = wrapper.querySelector('.omg-success');

  if (successMessage) {
    form.classList.add('show');
    successMessage.classList.add('show');
    return;
  }

  var redirect = wrapper.dataset.redirect;

  if (redirect) {
    window.location = redirect;
  }
};

/***/ }),
/* 5 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 6 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});
exports.default = {
  events: {},
  subscribe: function subscribe(eventName, fn) {
    this.events[eventName] = this.events[eventName] || [];
    this.events[eventName].push(fn);
  },
  unsubscribe: function unsubscribe(eventName, fn) {
    if (this.events[eventName]) {
      this.events[eventName] = this.events[eventName].filter(function (eventFn) {
        return eventFn !== fn;
      });
    }
  },
  emit: function emit(eventName, data) {
    if (this.events[eventName]) {
      this.events[eventName].forEach(function (fn) {
        fn(data);
      });
    }
  }
};

/***/ })
/******/ ]);
//# sourceMappingURL=frontend.bundle.js.map