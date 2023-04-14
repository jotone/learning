const mix = require('laravel-mix');
const {copyFile, exists, readFile, writeFile} = require('fs')
/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
  .js('resources/js/Dashboard/app.js', 'public/js/dashboard.js')
  .vue()
  .extract()
  .disableNotifications()

mix
  .sass('resources/css/reset.scss', 'public/css')
  .sass('resources/css/dashboard/main.scss', 'public/css/dashboard')
  .options({
    postCss: [
      require('postcss-discard-comments')({removeAll: true})
    ],
    terser: {
      terserOptions: {
        format: {
          comments: false
        }
      },
      extractComments: false,
    }
  })
  .version()
  .disableNotifications()

// Copying libs
// JSColor
// exists('./public/js/jscolor.min.js', res => !res && mix.copy('node_modules/@eastdesire/jscolor/jscolor.min.js', 'public/js'));