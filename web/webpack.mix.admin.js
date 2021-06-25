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
    'resources': 'resources/assets/admin/',
    'public': 'public/assets/',
    'public_admin': 'public/assets/admin/vendor/',
    'blade': 'resources/views/',
    'root': ''
};

 // Recursos de administracion
mix.js(paths.resources + 'jss/app.js', paths.public + 'admin/js')
    .sass(paths.resources + 'sass/app.scss', paths.public + 'admin/css')
    .copy(paths.resources + 'img', paths.public + 'admin/img');


// adminlte
mix.copy(paths.node + 'admin-lte/dist/css/AdminLTE.min.css', paths.public_admin + 'adminlte/css/adminlte.min.css')
    .copy(paths.node + 'admin-lte/dist/css/skins/', paths.public_admin + 'adminlte/css/skins/')
    .copy(paths.node + 'admin-lte/dist/js/adminlte.min.js', paths.public_admin + 'adminlte/js/');


// Plugins adminlte
mix.copy(paths.node + 'admin-lte/plugins/', paths.public_admin );


// Bootstrap
// mix.copy(paths.node + 'bootstrap/dist/css/bootstrap.min.css', paths.public_admin + 'bootstrap/css/');
// mix.copy(paths.node + 'bootstrap/dist/css/bootstrap.min.css.map', paths.public_admin + 'bootstrap/css/');
// mix.copy(paths.node + 'bootstrap/dist/js/bootstrap.js', paths.public_admin + 'bootstrap/js/');
// mix.copy(paths.node + 'bootstrap/dist/fonts/', paths.public_admin + 'bootstrap/fonts/');


// Jquery
mix.copy(paths.node + 'admin-lte/bower_components/jquery/dist/jquery.min.js', paths.public_admin + 'jquery/js/');
mix.copy(paths.node + 'admin-lte/bower_components/jquery-ui/themes/base', paths.public_admin + 'jquery/css/');
mix.copy(paths.node + 'admin-lte/bower_components/jquery-ui/jquery-ui.min.js', paths.public_admin + 'jquery/js/');




// Font Awesome
mix.copy(paths.node + 'admin-lte/bower_components/font-awesome/css/', paths.public_admin + 'fontawesome/css/');
mix.copy(paths.node + 'admin-lte/bower_components/font-awesome/fonts/', paths.public_admin + 'fontawesome/fonts/');

// Ionicons
// gulp.src(paths.bower_base_path + "ionicons\dist\/css/")
//     .pipe(gulp.dest(paths.public_path + 'Ionicons/css/');

// mix.copy(paths.node + 'Ionicons/fonts/', paths.public_admin + 'Ionicons/fonts/');

// Select 2 plugin
// mix.copy(paths.node + 'select2/dist/js/select2.full.js', paths.public_admin + 'select2/js/');
// mix.copy(paths.node + 'select2/dist/css/select2.min.css', paths.public_admin + 'select2/css/');

// // // Select 2 para bootstrap 3
// mix.copy(paths.node + 'select2-bootstrap-theme/dist/', paths.public_admin + 'select2/css/');


// Aqui instalamos paquetes individuals
// iCheck
mix.copy(paths.node + 'admin-lte/plugins/iCheck/square/', paths.public_admin + 'iCheck/css/square/');
mix.copy(paths.node + 'admin-lte/plugins/iCheck/icheck.min.js', paths.public_admin + "iCheck/js/");

// // SlimScroll
// mix.copy(paths.node + 'jquery-slimscroll/jquery.slimscroll.min.js')
//     .pipe(gulp.dest(paths.public_path + "jquery-slimscroll/js/"));

// // FastClick
// mix.copy(paths.node + 'fastclick/lib/fastclick.js')
//     .pipe(gulp.dest(paths.public_path + "fastclick/js/"));

// Sparkline
// mix.copy(paths.node + 'jquery-sparkline/jquery.sparkline.min.js', paths.public_admin + 'jquery-sparkline/js/');

// // Chartjs
// mix.copy(paths.node + '/chart.js/Chart.min.js', paths.public_admin + 'chart.js/js/');

// jVectorMap
// mix.copy(paths.node + 'admin-lte/plugins/jvectormap/jquery-jvectormap-1.2.2.css', paths.public_admin + 'jvectormap/css/');

// mix.copy(paths.node + 'admin-lte/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js', paths.public_admin + 'jvectormap/js/');

// mix.copy(paths.node + 'admin-lte/plugins/jvectormap/jquery-jvectormap-world-mill-en.js', paths.public_admin + 'jvectormap/js/');

// Datatables
mix.copy(paths.node + 'admin-lte/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css', paths.public_admin + 'datatables/css/');
mix.copy(paths.node + 'admin-lte/bower_components/datatables.net/js/jquery.dataTables.min.js', paths.public_admin + 'datatables/js/');
mix.copy(paths.node + 'admin-lte/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js', paths.public_admin + 'datatables/js');

// // jquery-bonsai
mix.copy(paths.node + 'jquery-bonsai/jquery.bonsai.js', paths.public_admin + 'jquery-bonsai/js/');
mix.copy(paths.node + 'jquery-bonsai/jquery.bonsai.css', paths.public_admin + 'jquery-bonsai/css/');
mix.copy(paths.node + 'jquery-bonsai/assets/svg-icons.css', paths.public_admin + 'jquery-bonsai/css/assets/');

// jquery-qubit
mix.copy(paths.node + 'jquery-qubit/jquery.qubit.js', paths.public_admin + 'jquery-qubit/js/');

// dropzone
mix.copy(paths.node + 'dropzone/dist/min/', paths.public_admin + 'dropzone/');

//TinyMCE
mix.copy(paths.node + 'tinymce/tinymce.min.js', paths.public_admin + 'tinymce/');
mix.copy(paths.node + 'tinymce/jquery.tinymce.min.js', paths.public_admin + 'tinymce/');
mix.copy(paths.node + 'tinymce/skins/', paths.public_admin + 'tinymce/skins/');
mix.copy(paths.node + 'tinymce/plugins/', paths.public_admin + 'tinymce/plugins/');
mix.copy(paths.node + 'tinymce/themes/', paths.public_admin + 'tinymce/themes/');
mix.copy(paths.node + 'tinymce/icons/', paths.public_admin + 'tinymce/icons/');
mix.copy(paths.node + 'tinymce-i18n/langs5/', paths.public_admin + 'tinymce/langs5/');

// Ace Code Ace-builds
mix.copy(paths.node + 'ace-builds/src-min-noconflict/', paths.public_admin + 'ace-builds/');


// Jquery-sortable
mix.copy(paths.node + 'es-jquery-sortable/source/js/jquery-sortable-min.js', paths.public_admin + 'jquery-sortable/js');

// datepicker Plugin
// mix.copy(paths.node + 'bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js', paths.public_admin + 'datepicker/js/');

// mix.copy(paths.node + 'bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css', paths.public_admin + 'datepicker/css/');

// mix.copy(paths.node + 'bootstrap-datepicker/dist/locales/', paths.public_admin + 'datepicker/locales/');

// // Timepicker Plugin
// mix.copy(paths.node + 'admin-lte/plugins/timepicker/')
//     .pipe(gulp.dest(paths.public_path + "timepicker/"));

// daterangepicker Plugin
// mix.copy(paths.node + 'bootstrap-daterangepicker/daterangepicker.js', paths.public_admin + 'daterangepicker/js/');

// mix.copy(paths.node + 'bootstrap-daterangepicker/daterangepicker.css', paths.public_admin + 'daterangepicker/css/');


// // datetimepicker Plugin
// mix.copy(paths.node + 'eonasdan-bootstrap-datetimepicker/build/', paths.public_admin + 'datetimepicker/');


// // JS Validation
// gulp.src(paths.vendor_base_path + 'proengsoft/laravel-jsvalidation/public/js/')
//     .pipe(gulp.dest(paths.root_path + 'public/vendor/jsvalidation/js/');
// // JS Validation
// gulp.src(paths.bower_base_path + "jquery-validation/dist/")
//     .pipe(gulp.dest(paths.public_path + "jquery-validation"));

// Color Picker
// mix.copy(paths.node + 'bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js', paths.public_admin + 'colorpicker/js/');

// mix.copy(paths.node + 'bootstrap-colorpicker/dist/css/', paths.public_admin + 'colorpicker/css/');

// mix.copy(paths.node + 'bootstrap-colorpicker/dist/img/', paths.public_admin + 'colorpicker/img/');

// Moment js
mix.copy(paths.node + "moment/min/", paths.public_admin +  "moment");

// // eCharts
// gulp.src(paths.bower_base_path + "echarts/dist/")
//     .pipe(gulp.dest(paths.public_path + "echarts"));


// Font Awesome Iconpicker
mix.copy(paths.node + "fontawesome-iconpicker/dist/", paths.public_admin + 'fontawesome-iconpicker/');


// mix.copy(paths.node + 'fontawesome-iconpicker/dist/js/', paths.public_admin + 'fontawesome-iconpicker/js/');

// Vue
// mix.copy(paths.node + 'vue/dist/', paths.public_admin + 'vue/');

// // Axios
// mix.copy(paths.node + 'axios/dist/', paths.public_admin + 'axios/');

mix.browserSync({
    "proxy": "http://clavel2.test",
    "reloadDelay": 1000,
    "open": false
});
