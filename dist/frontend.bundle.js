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
/******/ 	// identity function for calling harmony imports with the correct context
/******/ 	__webpack_require__.i = function(value) { return value; };
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
/******/ 	return __webpack_require__(__webpack_require__.s = 6);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
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

/***/ }),
/* 1 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
  value: true
});

exports.default = function (Events) {
  var formButtons = document.querySelectorAll('.omg-form-submit-btn');
  var processForm = processFormRequest(Events);
  window.processFormRequest = processForm;

  if (!formButtons || 0 === formButtons.length) {
    return false;
  }

  [].forEach.call(formButtons, function (button) {
    var form = button.closest('form');
    var formWrapper = button.closest('.omg-form-wrapper');

    form.addEventListener('submit', function (e) {
      e.preventDefault();

      processForm(form, formWrapper);
    });
  });
};

var _formErrors = __webpack_require__(4);

var _formErrors2 = _interopRequireDefault(_formErrors);

var _formSuccess = __webpack_require__(5);

var _formSuccess2 = _interopRequireDefault(_formSuccess);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

function processFormRequest(Events) {
  return function (form, formWrapper) {
    var data = new FormData(form);
    var formError = document.getElementById('omg-form-level-error');
    var button = form.querySelector('.omg-form-submit-btn');

    button.setAttribute('disabled', 'disabled');

    if (formError.classList.contains('error')) {
      formError.classList.remove('error');
    }

    data.append('formId', form.getAttribute('id'));

    if (parseInt(formWrapper.dataset.rest)) {
      submitForm(data).then(function (response) {
        button.removeAttribute('disabled');
        handleFormSuccess(response, formWrapper, form, Events);
      }).catch(function (error) {
        button.removeAttribute('disabled');
        handleFormErrors(error, formWrapper, form, Events);
        // console.warn( error );
      });
    } else {
      Events.emit('omg-form-submit', {
        data: data,
        formWrapper: formWrapper,
        form: form,
        formType: JSON.parse(formWrapper.dataset.formtype)
      });
    }
  };
}

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

var handleFormErrors = function handleFormErrors(error, formWrapper, form, Events) {
  if ('omg-form-field-error' === error.code) {
    (0, _formErrors2.default)(error.data.fields);
    Events.emit('omg-form-field-errors', {
      fields: error.data.fields,
      formWrapper: formWrapper,
      form: form
    });
  }

  if ('omg-form-submission-error' === error.code) {
    var formError = document.getElementById('omg-form-level-error');
    formError.innerHTML = error.message;
    formError.classList.add('error');
    Events.emit('omg-form-submission-error', {
      error: error,
      formWrapper: formWrapper,
      form: form
    });
  }
};

var handleFormSuccess = function handleFormSuccess(response, formWrapper, form, Events) {
  if (response) {
    (0, _formSuccess2.default)(formWrapper, form);
    Events.emit('omg-form-success', {
      formWrapper: formWrapper,
      form: form
    });
  }
};

var submitForm = function submitForm(data) {
  var endpoint = OMGForms.baseURL + '/wp-json/omg/v1/forms';
  return new Promise(function (resolve, reject) {
    var xhr = new XMLHttpRequest();

    xhr.addEventListener('load', function (evt) {
      if (xhr.readyState === 4 && xhr.status === 200) {
        return resolve(JSON.parse(xhr.response));
      }

      if (xhr.readyState === 4 && xhr.status <= 400) {
        return reject(JSON.parse(xhr.response));
      }
    }, false);

    xhr.addEventListener('error', function (error) {
      return reject(JSON.parse(error));
    }, false);

    xhr.addEventListener('abort', function (error) {
      return reject(JSON.parse(error));
    }, false);

    xhr.open('POST', endpoint, true);

    xhr.setRequestHeader('X-WP-NONCE', OMGForms.nonce);
    xhr.send(data);
  });
};

/***/ }),
/* 2 */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),
/* 3 */,
/* 4 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


Object.defineProperty(exports, "__esModule", {
    value: true
});

exports.default = function (errors) {
    var fields = errors.map(function (error) {
        return document.getElementById(error);
    });

    if (!fields || 0 === fields.length) {
        return false;
    }

    fields.forEach(function (field) {
        var input = field.querySelector('input');
        var select = field.querySelector('select');
        var textarea = field.querySelector('textarea');

        field.classList.add('error');

        if (input) {
            input.addEventListener('keyup', function (e) {
                if (e.target.value) {
                    field.classList.remove('error');
                }
            });
        }

        if (select) {
            select.addEventListener('select', function (e) {
                field.classList.remove('error');
            });
        }

        if (textarea) {
            textarea.addEventListener('keyup', function (e) {
                if (e.target.value) {
                    field.classList.remove('error');
                }
            });
        }
    });
};

/***/ }),
/* 5 */
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
    return true;
  }

  var redirect = wrapper.dataset.redirect;

  if (redirect) {
    window.location = redirect;
  }
};

/***/ }),
/* 6 */
/***/ (function(module, exports, __webpack_require__) {

"use strict";


var _formSubmit = __webpack_require__(1);

var _formSubmit2 = _interopRequireDefault(_formSubmit);

var _index = __webpack_require__(2);

var _index2 = _interopRequireDefault(_index);

var _formEvents = __webpack_require__(0);

var _formEvents2 = _interopRequireDefault(_formEvents);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

document.addEventListener('DOMContentLoaded', function () {
  window.omg_events = _formEvents2.default;
  (0, _formSubmit2.default)(window.omg_events);
});

/***/ })
/******/ ]);
//# sourceMappingURL=frontend.bundle.js.map