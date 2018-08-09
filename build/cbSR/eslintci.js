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
        "no-undef": [
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