var gulp       = require('gulp')
var browserify = require('browserify')
var babelify   = require('babelify')
var uglify     = require('gulp-uglify')
var rename     = require('gulp-rename')
var sourcemaps = require('gulp-sourcemaps')
var source     = require('vinyl-source-stream')
var buffer     = require('vinyl-buffer')
var gutil      = require('gulp-util')
var chalk      = require('chalk')

var map_error = function(err){
  if(err.fileName){
    gutil.log(
      chalk.red(err.name) + ': ' +
      chalk.yellow(err.fileName.replace(__dirName + '/src/js', '')) + ': ' +
      chalk.magenta(err.lineNumber) + '& Column ' +
      chalk.magenta(err.columnNumber || err.column) + ': ' +
      chalk.blue(err.description)
    )
  } else {
    gutil.log(
      chalk.red(err.name) + ': ' +
      chalk.yellow(err.message)
    )
  }
}

gulp.task('scripts', function(){
  var bundler = browserify('./src/bubbles.js', {
    debug: true
  }).transform(babelify, {
    presets: ['es2015']
  })

  return bundler.bundle()
    .on('error', map_error)
    .pipe(source('./src/bubbles.js'))
    .pipe(buffer())
    .pipe(rename('bubbles.min.js'))
    .pipe(uglify())
    .pipe(gulp.dest('dist'))
})
