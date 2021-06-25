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
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
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
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/assets/admin/jss/admin-lte.js":
/*!*************************************************!*\
  !*** ./resources/assets/admin/jss/admin-lte.js ***!
  \*************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

/**
 * AdminLTE Demo Menu
 * ------------------
 * You should not use this file in production.
 * This file is for demo purposes only.
 */
$(function () {
  'use strict';
  /**
   * Get access to plugins
   */

  $('[data-toggle="control-sidebar"]').controlSidebar();
  $('[data-toggle="push-menu"]').pushMenu();
  var $pushMenu = $('[data-toggle="push-menu"]').data('lte.pushmenu');
  var $controlSidebar = $('[data-toggle="control-sidebar"]').data('lte.controlsidebar');
  var $layout = $('body').data('lte.layout');
  /**
   * List of all the available skins
   *
   * @type Array
   */

  var mySkins = ['skin-blue', 'skin-black', 'skin-red', 'skin-yellow', 'skin-purple', 'skin-green', 'skin-blue-light', 'skin-black-light', 'skin-red-light', 'skin-yellow-light', 'skin-purple-light', 'skin-green-light'];
  /**
   * Get a prestored setting
   *
   * @param String name Name of of the setting
   * @returns String The value of the setting | null
   */

  function get(name) {
    if (typeof Storage !== 'undefined') {
      return localStorage.getItem(name);
    } else {
      window.alert('Please use a modern browser to properly view this template!');
    }
  }
  /**
   * Store a new settings in the browser
   *
   * @param String name Name of the setting
   * @param String val Value of the setting
   * @returns void
   */


  function store(name, val) {
    if (typeof Storage !== 'undefined') {
      localStorage.setItem(name, val);
    } else {
      window.alert('Please use a modern browser to properly view this template!');
    }
  }
  /**
   * Toggles layout classes
   *
   * @param String cls the layout class to toggle
   * @returns void
   */


  function changeLayout(cls) {
    $('body').toggleClass(cls);
    $layout.fixSidebar();

    if ($('body').hasClass('fixed') && cls == 'fixed') {
      $pushMenu.expandOnHover();
      $layout.activate();
    }

    $controlSidebar.fix();
  }
  /**
   * Replaces the old skin with the new skin
   * @param String cls the new skin class
   * @returns Boolean false to prevent link's default action
   */


  function changeSkin(cls) {
    $.each(mySkins, function (i) {
      $('body').removeClass(mySkins[i]);
    });
    $('body').addClass(cls); //store('skin', cls)

    return false;
  }
  /**
   * Retrieve default settings and apply them to the template
   *
   * @returns void
   */


  function setup() {
    /*
    var tmp = get('skin')
    if (tmp && $.inArray(tmp, mySkins))
        changeSkin(tmp)
    */
    // Add the change skin listener
    $('[data-skin]').on('click', function (e) {
      if ($(this).hasClass('knob')) return;
      e.preventDefault();
      changeSkin($(this).data('skin'));
    }); // Add the layout manager

    $('[data-layout]').on('click', function () {
      changeLayout($(this).data('layout'));
    });
    $('[data-controlsidebar]').on('click', function () {
      changeLayout($(this).data('controlsidebar'));
      var slide = !$controlSidebar.options.slide;
      $controlSidebar.options.slide = slide;
      if (!slide) $('.control-sidebar').removeClass('control-sidebar-open');
    });
    $('[data-sidebarskin="toggle"]').on('click', function () {
      var $sidebar = $('.control-sidebar');

      if ($sidebar.hasClass('control-sidebar-dark')) {
        $sidebar.removeClass('control-sidebar-dark');
        $sidebar.addClass('control-sidebar-light');
      } else {
        $sidebar.removeClass('control-sidebar-light');
        $sidebar.addClass('control-sidebar-dark');
      }
    });
    $('[data-enable="expandOnHover"]').on('click', function () {
      $(this).attr('disabled', true);
      $pushMenu.expandOnHover();
      if (!$('body').hasClass('sidebar-collapse')) $('[data-layout="sidebar-collapse"]').click();
    }); //  Reset options

    if ($('body').hasClass('fixed')) {
      $('[data-layout="fixed"]').attr('checked', 'checked');
    }

    if ($('body').hasClass('layout-boxed')) {
      $('[data-layout="layout-boxed"]').attr('checked', 'checked');
    }

    if ($('body').hasClass('sidebar-collapse')) {
      $('[data-layout="sidebar-collapse"]').attr('checked', 'checked');
    }
  } // Create the new tab


  var $tabPane = $('<div />', {
    'id': 'control-sidebar-theme-demo-options-tab',
    'class': 'tab-pane active'
  }); // Create the tab button

  var $tabButton = $('<li />', {
    'class': 'active'
  }).html('<a href=\'#control-sidebar-theme-demo-options-tab\' data-toggle=\'tab\'>' + '<i class="fa fa-wrench"></i>' + '</a>'); // Add the tab button to the right sidebar tabs

  $('#sidebar-right-tab').append($tabButton); // Create the menu

  var $demoSettings = $('<div />');
  var $skinsList = $('<ul />', {
    'class': 'list-unstyled clearfix'
  }); // Dark sidebar skins

  var $skinBlue = $('<li />', {
    style: 'float:left; width: 33.33333%; padding: 5px;'
  }).append('<a href="javascript:void(0)" data-skin="skin-blue" style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix full-opacity-hover">' + '<div><span style="display:block; width: 20%; float: left; height: 7px; background: #367fa9"></span><span class="bg-light-blue" style="display:block; width: 80%; float: left; height: 7px;"></span></div>' + '<div><span style="display:block; width: 20%; float: left; height: 20px; background: #222d32"></span><span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7"></span></div>' + '</a>' + '<p class="text-center no-margin">Blue</p>');
  $skinsList.append($skinBlue);
  var $skinBlack = $('<li />', {
    style: 'float:left; width: 33.33333%; padding: 5px;'
  }).append('<a href="javascript:void(0)" data-skin="skin-black" style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix full-opacity-hover">' + '<div style="box-shadow: 0 0 2px rgba(0,0,0,0.1)" class="clearfix"><span style="display:block; width: 20%; float: left; height: 7px; background: #fefefe"></span><span style="display:block; width: 80%; float: left; height: 7px; background: #fefefe"></span></div>' + '<div><span style="display:block; width: 20%; float: left; height: 20px; background: #222"></span><span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7"></span></div>' + '</a>' + '<p class="text-center no-margin">Black</p>');
  $skinsList.append($skinBlack);
  var $skinPurple = $('<li />', {
    style: 'float:left; width: 33.33333%; padding: 5px;'
  }).append('<a href="javascript:void(0)" data-skin="skin-purple" style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix full-opacity-hover">' + '<div><span style="display:block; width: 20%; float: left; height: 7px;" class="bg-purple-active"></span><span class="bg-purple" style="display:block; width: 80%; float: left; height: 7px;"></span></div>' + '<div><span style="display:block; width: 20%; float: left; height: 20px; background: #222d32"></span><span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7"></span></div>' + '</a>' + '<p class="text-center no-margin">Purple</p>');
  $skinsList.append($skinPurple);
  var $skinGreen = $('<li />', {
    style: 'float:left; width: 33.33333%; padding: 5px;'
  }).append('<a href="javascript:void(0)" data-skin="skin-green" style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix full-opacity-hover">' + '<div><span style="display:block; width: 20%; float: left; height: 7px;" class="bg-green-active"></span><span class="bg-green" style="display:block; width: 80%; float: left; height: 7px;"></span></div>' + '<div><span style="display:block; width: 20%; float: left; height: 20px; background: #222d32"></span><span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7"></span></div>' + '</a>' + '<p class="text-center no-margin">Green</p>');
  $skinsList.append($skinGreen);
  var $skinRed = $('<li />', {
    style: 'float:left; width: 33.33333%; padding: 5px;'
  }).append('<a href="javascript:void(0)" data-skin="skin-red" style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix full-opacity-hover">' + '<div><span style="display:block; width: 20%; float: left; height: 7px;" class="bg-red-active"></span><span class="bg-red" style="display:block; width: 80%; float: left; height: 7px;"></span></div>' + '<div><span style="display:block; width: 20%; float: left; height: 20px; background: #222d32"></span><span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7"></span></div>' + '</a>' + '<p class="text-center no-margin">Red</p>');
  $skinsList.append($skinRed);
  var $skinYellow = $('<li />', {
    style: 'float:left; width: 33.33333%; padding: 5px;'
  }).append('<a href="javascript:void(0)" data-skin="skin-yellow" style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix full-opacity-hover">' + '<div><span style="display:block; width: 20%; float: left; height: 7px;" class="bg-yellow-active"></span><span class="bg-yellow" style="display:block; width: 80%; float: left; height: 7px;"></span></div>' + '<div><span style="display:block; width: 20%; float: left; height: 20px; background: #222d32"></span><span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7"></span></div>' + '</a>' + '<p class="text-center no-margin">Yellow</p>');
  $skinsList.append($skinYellow); // Light sidebar skins

  var $skinBlueLight = $('<li />', {
    style: 'float:left; width: 33.33333%; padding: 5px;'
  }).append('<a href="javascript:void(0)" data-skin="skin-blue-light" style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix full-opacity-hover">' + '<div><span style="display:block; width: 20%; float: left; height: 7px; background: #367fa9"></span><span class="bg-light-blue" style="display:block; width: 80%; float: left; height: 7px;"></span></div>' + '<div><span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc"></span><span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7"></span></div>' + '</a>' + '<p class="text-center no-margin" style="font-size: 12px">Blue Light</p>');
  $skinsList.append($skinBlueLight);
  var $skinBlackLight = $('<li />', {
    style: 'float:left; width: 33.33333%; padding: 5px;'
  }).append('<a href="javascript:void(0)" data-skin="skin-black-light" style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix full-opacity-hover">' + '<div style="box-shadow: 0 0 2px rgba(0,0,0,0.1)" class="clearfix"><span style="display:block; width: 20%; float: left; height: 7px; background: #fefefe"></span><span style="display:block; width: 80%; float: left; height: 7px; background: #fefefe"></span></div>' + '<div><span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc"></span><span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7"></span></div>' + '</a>' + '<p class="text-center no-margin" style="font-size: 12px">Black Light</p>');
  $skinsList.append($skinBlackLight);
  var $skinPurpleLight = $('<li />', {
    style: 'float:left; width: 33.33333%; padding: 5px;'
  }).append('<a href="javascript:void(0)" data-skin="skin-purple-light" style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix full-opacity-hover">' + '<div><span style="display:block; width: 20%; float: left; height: 7px;" class="bg-purple-active"></span><span class="bg-purple" style="display:block; width: 80%; float: left; height: 7px;"></span></div>' + '<div><span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc"></span><span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7"></span></div>' + '</a>' + '<p class="text-center no-margin" style="font-size: 12px">Purple Light</p>');
  $skinsList.append($skinPurpleLight);
  var $skinGreenLight = $('<li />', {
    style: 'float:left; width: 33.33333%; padding: 5px;'
  }).append('<a href="javascript:void(0)" data-skin="skin-green-light" style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix full-opacity-hover">' + '<div><span style="display:block; width: 20%; float: left; height: 7px;" class="bg-green-active"></span><span class="bg-green" style="display:block; width: 80%; float: left; height: 7px;"></span></div>' + '<div><span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc"></span><span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7"></span></div>' + '</a>' + '<p class="text-center no-margin" style="font-size: 12px">Green Light</p>');
  $skinsList.append($skinGreenLight);
  var $skinRedLight = $('<li />', {
    style: 'float:left; width: 33.33333%; padding: 5px;'
  }).append('<a href="javascript:void(0)" data-skin="skin-red-light" style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix full-opacity-hover">' + '<div><span style="display:block; width: 20%; float: left; height: 7px;" class="bg-red-active"></span><span class="bg-red" style="display:block; width: 80%; float: left; height: 7px;"></span></div>' + '<div><span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc"></span><span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7"></span></div>' + '</a>' + '<p class="text-center no-margin" style="font-size: 12px">Red Light</p>');
  $skinsList.append($skinRedLight);
  var $skinYellowLight = $('<li />', {
    style: 'float:left; width: 33.33333%; padding: 5px;'
  }).append('<a href="javascript:void(0)" data-skin="skin-yellow-light" style="display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4)" class="clearfix full-opacity-hover">' + '<div><span style="display:block; width: 20%; float: left; height: 7px;" class="bg-yellow-active"></span><span class="bg-yellow" style="display:block; width: 80%; float: left; height: 7px;"></span></div>' + '<div><span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc"></span><span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7"></span></div>' + '</a>' + '<p class="text-center no-margin" style="font-size: 12px">Yellow Light</p>');
  $skinsList.append($skinYellowLight);
  $demoSettings.append('<h4 class="control-sidebar-heading">Skins</h4>');
  $demoSettings.append($skinsList);
  $tabPane.append($demoSettings);
  $('#sidebar-right-content').append($tabPane);
  setup();
  $('[data-toggle="tooltip"]').tooltip();
});

/***/ }),

/***/ "./resources/assets/admin/jss/app.js":
/*!*******************************************!*\
  !*** ./resources/assets/admin/jss/app.js ***!
  \*******************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! ./admin-lte.js */ "./resources/assets/admin/jss/admin-lte.js");

/***/ }),

/***/ "./resources/assets/admin/sass/app.scss":
/*!**********************************************!*\
  !*** ./resources/assets/admin/sass/app.scss ***!
  \**********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

// removed by extract-text-webpack-plugin

/***/ }),

/***/ 0:
/*!****************************************************************************************!*\
  !*** multi ./resources/assets/admin/jss/app.js ./resources/assets/admin/sass/app.scss ***!
  \****************************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

__webpack_require__(/*! /var/www/html/clavel-cms-2019/web/resources/assets/admin/jss/app.js */"./resources/assets/admin/jss/app.js");
module.exports = __webpack_require__(/*! /var/www/html/clavel-cms-2019/web/resources/assets/admin/sass/app.scss */"./resources/assets/admin/sass/app.scss");


/***/ })

/******/ });