'use strict';

module.exports = function (grunt) {
  // Automatically load grunt modules
  require('load-grunt-tasks')(grunt);

  // Time how long tasks take. Can help when optimizing build times
  require('time-grunt')(grunt);

  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    concat: {
      options: {
        separator: ';'
      },
      dist: {
        src: ['src/**/*.js'],
        dest: '<%= pkg.name %>.js'
      }
    },
    uglify: {
      options: {
        banner: '/* ========================================================== \n' +
          ' * \n' +
          ' * <%= pkg.name %>.js v <%= pkg.version %> \n' +
          ' * Copyright <%= grunt.template.today("yyyy") %> <%= pkg.author %>\n' +
          ' * Licensed under <%= pkg.licenses[0]["type"] %>\n' +
          ' * URL: <%= pkg.licenses[0]["url"] %>\n' +
          ' *\n' +
          ' * ========================================================== */\n\n'
      },
      dist: {
        files: {
          '<%= pkg.name %>.min.js': ['<%= concat.dist.dest %>']
        }
      }
    },
    qunit: {
      files: ['test/**/*.html']
    },
    jshint: {
      files: ['Gruntfile.js', 'src/**/*.js', 'test/**/*.js'],
      options: {
        jshintrc: '.jshintrc'
      }
    },
    jscs: {
      options: {
        config: '.jscsrc'
      },
      src: {
        src: ['Gruntfile.js', 'src/**/*.js', 'test/**/*.js']
      }
    },
    watch: {
      files: ['<%= jshint.files %>'],
      tasks: ['jshint', 'qunit']
    }
  });

  grunt.registerTask('test', ['jshint','jscs','qunit']);

  grunt.registerTask('default', ['jshint','qunit','concat','uglify']);
};
