const mix = require('laravel-mix');
const fs = require('fs')
const {minify, combine} = require("laravel-mix");
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
  .sass('resources/css/reset.scss', 'public/css')
  .sass('resources/css/dashboard/preview.scss', 'public/css/dashboard')
  .sass('resources/css/dashboard/main.scss', 'public/css/dashboard')
  .sass('resources/css/dashboard/permissions.scss', 'public/css/dashboard')
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

mix
  .js('resources/js/Dashboard/app.js', 'public/js/dashboard.js')
  .vue()
  .extract()
  .disableNotifications()

// Copying libs
// CKEditor 4
try {
  fs.accessSync('./public/js/ckeditor/ckeditor.js')
} catch (e) {
  const dest = 'public/js/ckeditor/';
  const pluginsPath = 'node_modules/ckeditor4/plugins/';
  const skinPath = 'node_modules/ckeditor4/skins/moono-lisa/';
  mix
    .copy('node_modules/ckeditor4/ckeditor.js', dest + 'ckeditor.js')
    .combine(['node_modules/ckeditor4/config.js'], dest + 'config.js')
    .combine(['node_modules/ckeditor4/styles.js'], dest + 'styles.js')
    .styles('node_modules/ckeditor4/contents.css', dest + 'contents.css')
    .combine(['node_modules/ckeditor4/lang/en.js'], dest + 'lang/en.js')
    /* Copy plugins */
    // Dialog plugin
    .combine([pluginsPath + 'dialog/dialogDefinition.js'], dest + 'plugins/dialog/dialogDefinition.js')
    .styles([pluginsPath + 'dialog/styles/dialog.css'], dest + 'plugins/dialog/styles/dialog.css')
    // Font plugin
    .copy(pluginsPath + 'font/lang/en.js', dest + 'plugins/font/lang/en.js')
    .combine([pluginsPath + 'font/plugin.js'], dest + 'plugins/font/plugin.js')
    // Justify plugin
    .copyDirectory(pluginsPath + 'justify/icons/', dest + 'plugins/justify/icons/')
    .combine([pluginsPath + 'justify/plugin.js'], dest + 'plugins/justify/plugin.js')
    // Link plugin
    .copyDirectory(pluginsPath + 'link/images', dest + 'plugins/link/images')
    .combine([pluginsPath + 'link/dialogs/link.js'], dest + 'plugins/link/dialogs/link.js')
    // Scayt plugin
    .combine([pluginsPath + 'scayt/dialogs/options.js'], dest + 'plugins/scayt/dialogs/options.js')
    .styles([pluginsPath + 'scayt/dialogs/dialog.css'], dest + 'plugins/scayt/dialogs/dialog.css')
    .styles([pluginsPath + 'scayt/dialogs/toolbar.css'], dest + 'plugins/scayt/dialogs/toolbar.css')
    .styles([pluginsPath + 'scayt/skins/moono-lisa/scayt.css'], dest + 'plugins/scayt/skins/moono-lisa/scayt.css')
    // Tableselection plugin
    .styles([pluginsPath + 'tableselection/styles/tableselection.css'], dest + 'plugins/tableselection/styles/tableselection.css')
    /* Copy Moono-lisa skin */
    .copyDirectory(skinPath + 'images', dest + 'skins/moono-lisa/images')
    .copy(skinPath + 'icons.png', dest + 'skins/moono-lisa/icons.png')
    .copy(skinPath + 'icons_hidpi.png', dest + 'skins/moono-lisa/icons_hidpi.png')

  // Minify skin css files
  fs.readdir(skinPath, (err, files) => {
    for (let i = 0, n = files.length; i < n; i++) {
      if ('css' === files[i].split('.').pop()) {
        mix.styles(skinPath + files[i], dest + 'skins/moono-lisa/' + files[i])
      }
    }
  })
}

// Copy JQuery
try {
  fs.accessSync('./public/js/jquery.min.js')
} catch (e) {
  mix.combine(
    ['node_modules/jquery/dist/jquery.min.js', 'node_modules/axios/dist/axios.min.js'],
    'public/js/jquery.min.js'
  )
}