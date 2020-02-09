# VAADIN GRID

version: 5.4.12

## Bundle process

Steps to create the vaadingrid.js file

- install npm, yarn and webpack: this is a lengthy combination of `npm install`s to get all the dependencies and scripts you need. A lot of fun.
- `npm i @vaadin/vaadin-grid --save`
- create an empty application that loads the grid (app.js)
- create webpack config file to bundle the application
- `webpack`

I copy in this directory the package.json and webpack.config.js files