'use strict';

var path = require('path');
var gulp = require('gulp');
var conf = require('./conf');
var exec = require('child_process').exec;

var browserSync = require('browser-sync');

var $ = require('gulp-load-plugins')();

var wiredep = require('wiredep').stream;
var _ = require('lodash');

gulp.task('styles-reload', ['styles'], function() {
  return buildStyles()
    .pipe(browserSync.stream());
});

gulp.task('styles', function() {
  return buildStyles();
});

var buildStyles = function() {
  var lessOptions = {
    options: [
      'bower_components',
      path.join(conf.paths.src, '/app')
    ]
  };

  //FIX BOOTSTRAP LESS
  exec("sed -i '36 d' bower_components/bootstrap/less/variables.less");
  exec("sed -i '35 a @link-hover-color:      darken(#337ab7, 15%);' bower_components/bootstrap/less/variables.less");

  var injectFiles = gulp.src([
    path.join(conf.paths.src, '/app/**/*.less'),
    path.join('!' + conf.paths.src, '/app/index.less')
  ], { read: false });

  var injectOptions = {
    transform: function(filePath) {
      filePath = filePath.replace(conf.paths.src + '/app/', '');
      return '@import "' + filePath + '";';
    },
    starttag: '// injector',
    endtag: '// endinjector',
    addRootSlash: false
  };


  return gulp.src([
    path.join(conf.paths.src, '/app/index.less')
  ])
    .pipe($.inject(injectFiles, injectOptions))
    .pipe(wiredep(_.extend({}, conf.wiredep)))
    .pipe($.sourcemaps.init())
    .pipe($.less(lessOptions)).on('error', conf.errorHandler('Less'))
    .pipe($.autoprefixer()).on('error', conf.errorHandler('Autoprefixer'))
    .pipe($.sourcemaps.write())
    .pipe(gulp.dest(path.join(conf.paths.tmp, '/serve/app/')));
};
