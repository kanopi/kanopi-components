const {
	npm_lifecycle_event: currentEnvironment = "production",
	VIRTUAL_HOST: sockHostDomain = "",
	WP_THEME_ASSETS_HOST_SUBDOMAIN: sockHostSubdomain = ""
} = process.env;

module.exports = {
	"assets": {
		"inlineFileTypes": 'svg'
	},
	"devServer": {
		"sockHost": sockHostSubdomain + "." + sockHostDomain,
		"sockPort": 443,
		"watchOptions": {
			"poll": true
		},
		"useSslProxy": true,
		"useProxy": true
	},
	"environment": {
		"dotenvEnable": false
	},
	"externals": [
		function ({ request }, callback) {
			let externalRequest = defaultRequestToExternal(request);

			return externalRequest ? callback(null, externalRequest) : callback();
		}
	],
	"filePatterns": {
		cssOutputPath: "css/[name].[contenthash].css",
		"entryPoints": {
			"accordion-standard": "./assets/src/css/blocks/standard/accordion.css",
			"accordion-theme": "./assets/src/css/blocks/acf/accordion.css",
			"block-editor": "./assets/src/ts/block-editor.ts",
			editor: "./assets/src/css/editor/editor.css",
			"logo-grid-theme": "./assets/src/css/blocks/acf/logo-grid.css",
			"hero-theme": "./assets/src/css/blocks/acf/hero.css",
			"tabs-theme": "./assets/src/css/blocks/acf/tabs.css",
			struts: "./assets/src/css/template/struts.css",
			"cards-repeater-theme": "./assets/src/css/blocks/acf/cards.css",
			"struts-site": "./assets/src/js/struts-site.js",
			"carousel-block-style": "./blocks/acf/carousel/index.css",
			"carousel-block-script": "./blocks/acf/carousel/script.js"
		},
		"jsOutputPath": "js/[name].[contenthash].js"
	},
	"styles": {
		"devHeadSelectorInsertBefore": "#global-styles-inline-css",
		"styleLintConfigFile": "./assets/configuration/tools/.stylelintrc.js",
		"styleLintIgnorePath": "./assets/configuration/tools/.stylelintignore",
		"useSass": false
	},
	"scripts": {
		"esLintFileTypes": "js,jsx,ts,tsx",
		"useJsxSyntax": true
	}
};

/**
 * External handlers pulled from @wordpress/dependency-extraction-webpack-plugin
 * 	- The project only needs to manage the external inclusion or WordPress externals, not the whole plugin
 */

const WORDPRESS_NAMESPACE = '@wordpress/';
const BUNDLED_PACKAGES = ['@wordpress/icons', '@wordpress/interface'];

/**
 * Default request to global transformation
 *
 * Transform @wordpress dependencies:
 * - request `@wordpress/api-fetch` becomes `[ 'wp', 'apiFetch' ]`
 * - request `@wordpress/i18n` becomes `[ 'wp', 'i18n' ]`
 *
 * @param {string} request Module request (the module name in `import from`) to be transformed
 * @return {string|string[]|undefined} The resulting external definition. Return `undefined`
 *   to ignore the request. Return `string|string[]` to map the request to an external.
 */
function defaultRequestToExternal(request) {
	switch (request) {
		case 'moment':
			return request;

		case '@babel/runtime/regenerator':
			return 'regeneratorRuntime';

		case 'jquery':
			return 'jQuery';

		case 'react':
			return "development" === currentEnvironment ? undefined : 'React';

		case 'react-dom':
			return "development" === currentEnvironment ? undefined : 'ReactDOM';
	}

	if (BUNDLED_PACKAGES.includes(request)) {
		return undefined;
	}

	if (request.startsWith(WORDPRESS_NAMESPACE)) {
		return [
			'wp',
			camelCaseDash(request.substring(WORDPRESS_NAMESPACE.length)),
		];
	}
}

/**
 * Given a string, returns a new string with dash separators converted to
 * camelCase equivalent. This is not as aggressive as `_.camelCase` in
 * converting to uppercase, where Lodash will also capitalize letters
 * following numbers.
 *
 * @param {string} string Input dash-delimited string.
 * @return {string} Camel-cased string.
 */
function camelCaseDash(string) {
	return string.replace(/-([a-z])/g, (_, letter) => letter.toUpperCase());
}

