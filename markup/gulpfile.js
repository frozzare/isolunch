// ## Globals
var gulp = require('gulp');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var autoprefixer = require('gulp-autoprefixer');

var path = '.';
var config = {
  sass: {
    entries: [
      'assets/styles/main.scss'
    ],
    dest: 'assets/css/',
    watch: 'assets/styles/**/*.scss'
  }
};


/**
 * Sass development task.
 */
gulp.task('sass', function() {
  return gulp.src(config.sass.entries)
    .pipe(sourcemaps.init())
    .pipe(sass({ errLogToConsole: true, style: 'expanded' }))
    .pipe(autoprefixer('last 2 version', '> 5%'))
    .pipe(sourcemaps.write('.', {
      includeContent: false
    }))
    .pipe(gulp.dest(config.sass.dest));
});

/**
 * Watch task that watch changes in sass files.
 */
gulp.task('watch', function() {
  gulp.watch(config.sass.watch, ['sass']);
});

/**
 * The default task that builds sass.
 */
gulp.task('default', ['sass', 'watch']);