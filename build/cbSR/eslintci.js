module.exports = {
    "env": {
        "browser": true,
        "es6": true
    },
    "parserOptions": {
        "sourceType": "module"
    },
    "extends": "eslint:recommended",
    "rules": {
        "indent": [
            "error",
            "tab"
        ],
        "linebreak-style": [
            "error",
            "unix"
        ],
        "quotes": [
            "error",
            "single"
        ],
        "semi": [
            "error",
            "always"
        ],
        "semi-spacing": [
            "error",
            {
                "before": false,
                "after": true
            }
        ],
        "brace-style": [
            "error",
            "1tbs"
        ],
        "no-fallthrough": [
            "off"
        ],
        "no-inner-declarations": [
            "off"
        ],
        "no-trailing-spaces": [
            2,
            {
                "skipBlankLines": false
            }
        ],
        "no-undef": [
            "off"
        ],
        "no-empty": [
            "off"
        ],
        "no-unreachable": [
            "off"
        ],
        "no-unused-vars": [
            "off"
        ],
        "no-useless-escape": [
            "off"
        ],
        "quotes": [
            "off"
        ],
        "no-redeclare": [
            "off"
        ],
        "no-cond-assign": [
            "off"
        ],
        "no-console": [
            "off"
        ],
       "space-before-function-paren": [
            "error",
            {
                "named": "never",
            }
        ],
        "keyword-spacing": [
            "error",
            {
                "before": true,
                "after": true
            }
        ],
        "comma-spacing": [
            "error",
            {
                "before": false,
                "after": true
            }
        ],
        "space-before-blocks": [
            "error",
            "always"
        ],
        "curly": [
            "error",
            "all"
        ]
    }
};
