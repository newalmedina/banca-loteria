const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

let paths = {
    'vendor': 'vendor/',
    'node': 'node_modules/',
    'resources': 'resources/assets/front/',
    'public': 'public/assets/',
    'public_front': 'public/assets/front/vendor/',
    'blade': 'resources/views/',
    'root': ''
};

// Recursos de frontend
mix.js(paths.resources + 'jss/app.js', paths.public + 'front/js')
.sass(paths.resources + 'sass/app.scss', paths.public + 'front/css')
// //.copy('node_modules/@fortawesome/fontawesome-free/webfonts', 'public/admin/fonts')
// .copy(paths.resources + 'front/css', paths.public + 'front/css')
// .copy(paths.resources + 'front/js', paths.public + 'front/js')
.copy(paths.resources + 'vendor', paths.public + 'front/vendor')
.copy(paths.resources + 'img', paths.public + 'front/img')
.copy(paths.resources + 'css', paths.public + 'front/css')
.copy(paths.resources + 'fonts', paths.public + 'front/fonts')
.copy(paths.resources + 'js', paths.public + 'front/js');

// // Componentes
// mix.copy(paths.node + 'datatables.net/js', paths.public_front + 'datatables.net/js/');
// mix.copy(paths.node + 'datatables.net-bs4/js', paths.public_front + 'datatables.net/js/');
// mix.copy(paths.node + 'datatables.net-bs4/css', paths.public_front + 'datatables.net/css/');


mix.browserSync({
    "proxy": "http://ecografia.test",
    "reloadDelay": 1000,
    "open": false
});
