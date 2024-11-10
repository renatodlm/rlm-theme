const path = require('path');
const defaults = require('@wordpress/scripts/config/webpack.config.js');
const package = require('./package.json');

module.exports = {
   ...defaults,
   entry: {
      all: path.resolve(
         process.cwd(),
         'rlmwp',
         'wp-content',
         'themes',
         package.name,
         'assets',
         'scripts',
         'all',
         '_index.js',
      ),
      admin: path.resolve(
         process.cwd(),
         'rlmwp',
         'wp-content',
         'themes',
         package.name,
         'assets',
         'scripts',
         'admin',
         '_index.js',
      ),
      login: path.resolve(
         process.cwd(),
         'rlmwp',
         'wp-content',
         'themes',
         package.name,
         'assets',
         'scripts',
         'login',
         '_index.js',
      ),
   },
   output: {
      filename: '[name].min.js',
      path: path.resolve(process.cwd(), 'rlmwp', 'wp-content', 'themes', package.name, 'assets', 'js'),
   },
   module: {
      ...defaults.module,
      rules: [
         ...defaults.module.rules,
      ],
   },
   resolve: {
      extensions: [...(defaults.resolve ? defaults.resolve.extensions || ['.js', '.jsx'] : [])],
   },
};
