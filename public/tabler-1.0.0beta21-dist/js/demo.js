/*!
* Tabler v1.0.0-beta21 (https://tabler.io)
* @version 1.0.0-beta21
* @link https://tabler.io
* Copyright 2018-2024 The Tabler Authors
* Copyright 2018-2024 codecalm.net Paweł Kuna
* Licensed under MIT (https://github.com/tabler/tabler/blob/master/LICENSE)
*/
(function (factory) {
	typeof define === 'function' && define.amd ? define(factory) :
	factory();
})((function () { 'use strict';

	const items = {
	  "menu-position": {
	    localStorage: "tablerMenuPosition",
	    default: "top"
	  },
	  "menu-behavior": {
	    localStorage: "tablerMenuBehavior",
	    default: "sticky"
	  },
	  "container-layout": {
	    localStorage: "tablerContainerLayout",
	    default: "boxed"
	  }
	};
	const config = {};
	for (const [key, params] of Object.entries(items)) {
	  const lsParams = localStorage.getItem(params.localStorage);
	  config[key] = lsParams ? lsParams : params.default;
	}
	const parseUrl = () => {
	  const search = window.location.search.substring(1);
	  const params = search.split("&");
	  for (let i = 0; i < params.length; i++) {
	    const arr = params[i].split("=");
	    const key = arr[0];
	    const value = arr[1];
	    if (!!items[key]) {
	      localStorage.setItem(items[key].localStorage, value);
	      config[key] = value;
	    }
	  }
	};
	const toggleFormControls = form => {
	  for (const [key, params] of Object.entries(items)) {
	    const elem = form.querySelector(`[name="settings-${key}"][value="${config[key]}"]`);
	    if (elem) {
	      elem.checked = true;
	    }
	  }
	};
	const submitForm = form => {
	  for (const [key, params] of Object.entries(items)) {
	    const value = form.querySelector(`[name="settings-${key}"]:checked`).value;
	    localStorage.setItem(params.localStorage, value);
	    config[key] = value;
	  }
	  window.dispatchEvent(new Event("resize"));
	  new bootstrap.Offcanvas(form).hide();
	};
	parseUrl();
	const form = document.querySelector("#offcanvasSettings");
	if (form) {
	  form.addEventListener("submit", function (e) {
	    e.preventDefault();
	    submitForm(form);
	  });
	  toggleFormControls(form);
	}

}));
