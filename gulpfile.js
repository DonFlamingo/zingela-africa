var gulp = require('gulp'),
    sass = require('gulp-sass'),
    sourcemaps = require('gulp-sourcemaps'),
    cleanCSS = require('gulp-clean-css'),
    browserSync = require('browser-sync').create(),
    concat = require('gulp-concat'),
    group = require('gulp-group-files'),
    minify = require('gulp-minify');

var scripts = {
    'core': [
        'resources/assets/js/lib/jquery-3.2.1.min.js',
        'resources/assets/js/lib/jquery-ui.js',
        'resources/assets/js/lib/jquery.ui.touch-punch.min.js',
        'resources/assets/js/lib/bootstrap.min.js',
        'resources/assets/js/lib/bootstrap-select.js',
        'resources/assets/js/lib/bootstrap-datepicker.min.js',
        'resources/assets/js/lib/bootstrap-datetimepicker.js',
        'resources/assets/js/lib/bootstrap-colorpicker.min.js',
        'resources/assets/js/lib/bootstrap-modal.js',
        'resources/assets/js/lib/bootstrap-modalmanager.js',
        'resources/assets/js/lib/bootstrap-toastr.js',
        'resources/assets/js/lib/jquery.ba-throttle-debounce.js',

        'resources/assets/js/lib/flot/jquery.flot.js',
        'resources/assets/js/lib/flot/jquery.flot.canvas.js',
        'resources/assets/js/lib/flot/jquery.flot.crosshair.js',
        'resources/assets/js/lib/flot/jquery.flot.navigate.js',
        'resources/assets/js/lib/flot/jquery.flot.resize.js',
        'resources/assets/js/lib/flot/jquery.flot.selection.js',
        'resources/assets/js/lib/flot/jquery.flot.time.js',

        'resources/assets/js/helpers/helper.js',

        'resources/assets/js/plugins/outer-html.js',
        'resources/assets/js/plugins/jquery.databox.js',
        'resources/assets/js/plugins/loader.js',
        'resources/assets/js/plugins/modals.js',
        'resources/assets/js/plugins/tables.js',
        'resources/assets/js/plugins/multi-checkbox.js'
    ],
    'app':[
        'resources/assets/js/lib/moment.js',
        'resources/assets/js/lib/moment-timezone.js',

        'resources/assets/js/lib/leaflet/leaflet.1.0.3.js',
        'resources/assets/js/lib/leaflet/leaflet.polylineDecorator.js',
        'resources/assets/js/lib/leaflet/leaflet.markerCluster.js',
        'resources/assets/js/lib/leaflet/leaflet.draw.js',
        'resources/assets/js/lib/leaflet/leaflet.editable.js',
    	'resources/assets/js/lib/leaflet/leaflet.ruler.js',
        'resources/assets/js/lib/leaflet/marker.rotate.js',
        'resources/assets/js/lib/leaflet/Leaflet.GoogleMutant.js',
        'resources/assets/js/lib/leaflet/Yandex.js',

        'resources/assets/js/controller/listview.js',
        'resources/assets/js/controller/historyGraph.js',
        'resources/assets/js/controller/historyPlayer.js',
        'resources/assets/js/controller/history.js',
        'resources/assets/js/controller/devices.js',
        'resources/assets/js/controller/mapIcons.js',
        'resources/assets/js/controller/geofences.js',
        'resources/assets/js/controller/routes.js',
        'resources/assets/js/controller/alerts.js',
        'resources/assets/js/controller/events.js',
        'resources/assets/js/controller/sensors.js',
        'resources/assets/js/controller/app.js',
		
    	'resources/assets/js/model/notification.js',
        'resources/assets/js/model/device.js',
        'resources/assets/js/model/mapIcon.js',
        'resources/assets/js/model/geofence.js',
        'resources/assets/js/model/route.js',
        'resources/assets/js/model/event.js',
        'resources/assets/js/model/MapTiles.js'
    ]
};

gulp.task('browserSync', function() {
    browserSync.init({
        proxy: "http://46.101.121.251"
    });
});

gulp.task('compress', function() {
    gulp.src('public/assets/js/*.js')
        .pipe(minify({
            ext:{
                src:'.js',
                min:'.min.js'
            },
            exclude: ['tasks'],
            ignoreFiles: ['.min.js']
        }))
        .pipe(gulp.dest('public/assets/js/'))
});

gulp.task('scripts',group(scripts, function(name,files){
    return gulp.src(files)
        .pipe(concat(name + ".js"))
        .pipe(gulp.dest("public/assets/js/"));
}));

gulp.task('sass', function(){
    //return gulp.src('resources/assets/scss/app.scss')
    return gulp.src('resources/assets/scss/templates/light-blue.scss')
        .pipe(sourcemaps.init())
        .pipe(sass())
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('public/assets/css'))
        .pipe(browserSync.reload({
            stream: true
        }))

});

gulp.task('sass-all', function(){
    return gulp.src('resources/assets/scss/templates/*.scss')
        .pipe(sourcemaps.init())
        .pipe(sass())
        .pipe(sourcemaps.write())
        .pipe(gulp.dest('public/assets/css'))
});

gulp.task('minify-css', function() {
    return gulp.src('public/assets/css/*.css')
        .pipe(cleanCSS({debug: true}, function(details) {
            console.log(details.name + ': ' + details.stats.originalSize);
            console.log(details.name + ': ' + details.stats.minifiedSize);
        }))
        .pipe(gulp.dest('public/assets/css'));
});

gulp.task('watch', ['browserSync','sass', 'scripts'], function(){
    gulp.watch('resources/assets/scss/**/*.scss', ['sass']);
    gulp.watch('resources/assets/js/**/*.js', ['scripts']);
});

gulp.task('default', ['sass', 'scripts', 'minify-css', 'watch']);
gulp.task('templates', ['sass-all', 'minify-css', 'scripts']);