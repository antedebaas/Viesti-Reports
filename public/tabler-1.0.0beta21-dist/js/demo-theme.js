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

	const themeStorageKey = "tablerTheme";
	const defaultTheme = "light";
	let selectedTheme;
	const params = new Proxy(new URLSearchParams(window.location.search), {
	  get: (searchParams, prop) => searchParams.get(prop)
	});
	if (!!params.theme) {
	  localStorage.setItem(themeStorageKey, params.theme);
	  selectedTheme = params.theme;
	} else {
	  const storedTheme = localStorage.getItem(themeStorageKey);
	  selectedTheme = storedTheme ? storedTheme : defaultTheme;
	}
	if (selectedTheme === 'dark') {
	  document.body.setAttribute("data-bs-theme", selectedTheme);
	} else {
	  document.body.removeAttribute("data-bs-theme");
	}

}));
