module.exports = {
	"env": {
		"browser": true,
		"jquery": true,
		"node": true,
		"amd": true
	},
	"parserOptions": {
		"ecmaVersion": 5,
		"sourceType": "script"
	},
	"extends": "eslint:recommended",
	"rules": {
		"array-bracket-spacing": [
			"warn",
			"never"
		],
		"array-callback-return": "warn",
		"arrow-body-style": "error",
		"arrow-parens": "error",
		"arrow-spacing": "error",
		"block-scoped-var": "warn",
		"block-spacing": [
			"warn",
			"always"
		],
		"brace-style":  [
			"error",
			"1tbs"
		],
		"callback-return": "error",
		"camelcase": [
			"warn",
			{ "properties": "always" }
		],
		"capitalized-comments": "off",
		"class-methods-use-this": "error",
		"comma-dangle": [
			"error",
			"never"
		],
		"comma-spacing": "error",
		"comma-style": "error",
		"complexity": [
			"warn",
			20
		],
		"computed-property-spacing": "error",
		"consistent-return": "error",
		"consistent-this": [
			"error",
			"that",
			"outerThis",
			"self"
		],
		"curly": "error",
		"default-case": "error",
		"dot-location": [
			"error",
			"property"
		],
		"dot-notation": "error",
		"eol-last": "error",
		"eqeqeq": "error",
		"func-call-spacing": "error",
		"func-name-matching": "error",
		"func-names": "off",
		"func-style": "off",
		"generator-star-spacing": "error",
		"global-require": "error",
		"guard-for-in": "warn",
		"handle-callback-err": "error",
		"id-blacklist": "error",
		"id-length": [
			"warn",
			{ "exceptions": ["$","e","i","j"] }
		],
		"id-match": "error",
		"indent": [
			"error",
			"tab"
		],
		"init-declarations": "off",
		"jsx-quotes": "error",
		"key-spacing": [
			"warn", {
				"singleLine": {
					"beforeColon": false,
					"afterColon": true
				},
				"multiLine": {
					"beforeColon": true,
					"afterColon": true,
					"align": "colon"
				}
			}
		],
		"keyword-spacing": "error",
		"line-comment-position": "off",
		"linebreak-style": [
			"warn",
			"unix"
		],
		"lines-around-comment": "off",
		"lines-around-directive": "off",
		"max-depth": "warn",
		"max-len": "off",
		"max-lines": "off",
		"max-nested-callbacks": "error",
		"max-params": [
			"warn",
			{ "max": 4 }
		],
		"max-statements": [
			"warn",
			{ "max": 20 }
		],
		"max-statements-per-line": [
			"error",
			{ "max": 2 }
		],
		"multiline-ternary": [
			"error",
			"never"
		],
		"new-parens": "error",
		"newline-after-var": "warn",
		"newline-before-return": "error",
		"newline-per-chained-call": [
			"warn",
			{ "ignoreChainWithDepth": 3 }
		],
		"no-alert": "error",
		"no-array-constructor": "error",
		"no-await-in-loop": "error",
		"no-bitwise": "error",
		"no-caller": "error",
		"no-catch-shadow": "warn",
		"no-compare-neg-zero": "error",
		"no-confusing-arrow": "error",
		"no-continue": "warn",
		"no-div-regex": "error",
		"no-duplicate-imports": "error",
		"no-else-return": "error",
		"no-empty": [
			"error",
			{ "allowEmptyCatch": true }
		],
		"no-empty-function": [
			"error",
			{ "allow": ["functions"] }
		],
		"no-eq-null": "error",
		"no-eval": [
			"error",
			{ "allowIndirect": true }
		],
		"no-extend-native": "error",
		"no-extra-bind": "error",
		"no-extra-label": "error",
		"no-extra-parens": [
			"warn",
			"all",
			{
				"returnAssign": false,
				"nestedBinaryExpressions": false
			}
		],
		"no-floating-decimal": "error",
		"no-global-assign": "error",
		"no-implicit-globals": "error",
		"no-implied-eval": "error",
		"no-inline-comments": "off",
		"no-inner-declarations": [
			"warn",
			"both"
		],
		"no-invalid-this": "off",
		"no-iterator": "error",
		"no-label-var": "error",
		"no-labels": "error",
		"no-lone-blocks": "error",
		"no-lonely-if": "error",
		"no-loop-func": "error",
		"no-magic-numbers": [
			"warn",
			{
				"ignore": [0,1],
				"ignoreArrayIndexes": true
			}
		],
		"no-mixed-operators": "error",
		"no-mixed-requires": "error",
		"no-multi-assign": "warn",
		"no-multi-spaces": "error",
		"no-multi-str": "error",
		"no-multiple-empty-lines": [
			"error",
			{
				max: 2,
				maxEOF: 1,
				maxBOF: 0
			}
		],
		"no-negated-condition": "warn",
		"no-nested-ternary": "error",
		"no-new": "error",
		"no-new-func": "error",
		"no-new-object": "error",
		"no-new-require": "error",
		"no-new-wrappers": "error",
		"no-octal-escape": "error",
		"no-param-reassign": "off",
		"no-path-concat": "error",
		"no-plusplus": "off",
		"no-process-env": "error",
		"no-process-exit": "error",
		"no-proto": "error",
		"no-prototype-builtins": "warn",
		"no-restricted-globals": "error",
		"no-restricted-imports": "error",
		"no-restricted-modules": "error",
		"no-restricted-properties": "error",
		"no-restricted-syntax": "error",
		"no-return-assign": "error",
		"no-return-await": "error",
		"no-script-url": "error",
		"no-self-compare": "error",
		"no-sequences": "error",
		"no-shadow": "warn",
		"no-shadow-restricted-names": "error",
		"no-spaced-func": "error",
		"no-sync": "warn",
		"no-tabs": "off",
		"no-template-curly-in-string": "error",
		"no-ternary": "warn",
		"no-throw-literal": "warn",
		"no-trailing-spaces": "error",
		"no-undef-init": "error",
		"no-undefined": "warn",
		"no-underscore-dangle": "error",
		"no-unmodified-loop-condition": "error",
		"no-unneeded-ternary": "error",
		"no-unsafe-negation": "error",
		"no-unused-expressions": "error",
		"no-unused-vars": [
			"warn",
			{
				"vars": "local",
				"args": "none"
			}
		],
		"no-use-before-define": [
			"error",
			{
				"functions": false
			}
		],
		"no-useless-call": "error",
		"no-useless-computed-key": "error",
		"no-useless-concat": "error",
		"no-useless-constructor": "error",
		"no-useless-escape": "error",
		"no-useless-rename": "error",
		"no-useless-return": "error",
		"no-var": "off",
		"no-void": "error",
		"no-warning-comments": "warn",
		"no-whitespace-before-property": "error",
		"no-with": "error",
		"nonblock-statement-body-position": "error",
		"object-curly-newline": "error",
		"object-curly-spacing": [
			"error",
			"never",
			{
				"arraysInObjects": true,
				"objectsInObjects": true
			}
		],
		"object-property-newline": [
			"error",
			{
				"allowMultiplePropertiesPerLine": true
			}
		],
		"object-shorthand": [
			"warn",
			"consistent"
		],
		"one-var": "off",
		"one-var-declaration-per-line": "off",
		"operator-assignment": [
			"error",
			"always"
		],
		"operator-linebreak": "error",
		"padded-blocks": "off",
		"prefer-arrow-callback": "off",
		"prefer-const": "error",
		"prefer-destructuring": "off",
		"prefer-numeric-literals": "off",
		"prefer-promise-reject-errors": "error",
		"prefer-rest-params": "off",
		"prefer-spread": "off",
		"prefer-template": "off",
		"quote-props": [
			"warn",
			"consistent-as-needed"
		],
		"quotes": [
			"error",
			"single",
			{
				"avoidEscape": true,
				"allowTemplateLiterals": true
			}
		],
		"radix": "error",
		"require-await": "error",
		"require-jsdoc": "off",
		"rest-spread-spacing": "error",
		"semi": "error",
		"semi-spacing": "error",
		"sort-imports": "error",
		"sort-keys": "warn",
		"sort-vars": "warn",
		"space-before-blocks": "warn",
		"space-before-function-paren": "off",
		"space-in-parens": "warn",
		"space-infix-ops": "error",
		"space-unary-ops": "error",
		"spaced-comment": ["error", "always", {
			"line": {
				"markers": ["/"],
				"exceptions": ["-", "+"]
			},
			"block": {
				"markers": ["!"],
				"exceptions": ["*"],
				"balanced": true
			}
		}],
		"strict": [
			"warn",
			"function"
		],
		"symbol-description": "off",
		"template-curly-spacing": "error",
		"template-tag-spacing": "error",
		"unicode-bom": "error",
		"valid-jsdoc": "warn",
		"vars-on-top": "warn",
		"wrap-iife": "error",
		"wrap-regex": "off",
		"yield-star-spacing": "error",
		"yoda": "error"
	}
};
