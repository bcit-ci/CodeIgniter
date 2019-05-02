/**
 * Owl Carousel
 *
 * Bartosz Wojciechowski
 *
 * Copyright (c) 2014
 * Licensed under the MIT license.
 */
module.exports = function(grunt) {

	require('load-grunt-tasks')(grunt);

	grunt
		.initConfig({
			pkg: grunt.file.readJSON('package.json'),
			app: grunt.file.readJSON('_config.json'),
			banner: '/**\n' + ' * Owl Carousel v<%= pkg.version %>\n'
				+ ' * Copyright 2013-<%= grunt.template.today("yyyy") %> <%= pkg.author.name %>\n'
				+ ' * Licensed under: <%= pkg.license %>\n' + ' */\n',

			// assemble
			assemble: {
				options: {
					flatten: false,
					expand: true,
					production: false,
					assets: '<%= app.docs.dest %>/assets',
					postprocess: require('pretty'),

					// metadata
					pkg: '<%= pkg %>',
					app: '<%= app %>',
					data: [ '<%= app.docs.src %>/data/*.{json,yml}' ],

					// templates
					partials: '<%= app.docs.templates %>/partials/*.hbs',
					layoutdir: '<%= app.docs.layouts %>/',

					// extensions
					helpers: '<%= app.docs.src %>/helpers/*.js'
				},
				index: {
					options: {
						layout: 'home.hbs'
					},
					files: [ {
						expand: true,
						cwd: '<%= app.docs.pages %>/',
						src: '*.hbs',
						dest: '<%= app.docs.dest %>/'
					} ]
				},
				demos: {
					options: {
						layout: 'demos.hbs'
					},
					files: [ {
						expand: true,
						cwd: '<%= app.docs.pages %>/demos/',
						src: '*.hbs',
						dest: '<%= app.docs.dest %>/demos'
					} ]
				},
				docs: {
					options: {
						layout: 'docs.hbs'
					},
					files: [ {
						expand: true,
						cwd: '<%= app.docs.pages %>/docs/',
						src: '*.hbs',
						dest: '<%= app.docs.dest %>/docs'
					} ]
				}
			},

			// clean
			clean: {
				docs: [ '<%= app.docs.dest %>' ],
				dist: [ 'dist' ]
			},

			// sass
			sass: {
				docs: {
					options: {
						outputStyle: 'compressed',
						includePaths: [ '<%= app.docs.src %>/assets/scss/', 'node_modules/foundation-sites/scss' ]
					},
					files: {
						'<%= app.docs.dest %>/assets/css/docs.theme.min.css': '<%= app.docs.src %>/assets/scss/docs.theme.scss'
					}
				},
				dist: {
					options: {
						outputStyle: 'nested'
					},
					files: {
						'dist/assets/<%= pkg.name %>.css': 'src/scss/<%= pkg.name %>.scss',
						'dist/assets/owl.theme.default.css': 'src/scss/owl.theme.default.scss',
						'dist/assets/owl.theme.green.css': 'src/scss/owl.theme.green.scss'
					}
				}
			},

			autoprefixer: {
				options: {
					browsers: [ 'last 2 versions', 'ie 7', 'ie 8', 'ie 9', 'ie 10', 'ie 11' ]
				},
				dist: {
					files: {
						'dist/assets/<%= pkg.name %>.css': 'dist/assets/<%= pkg.name %>.css',
						'dist/assets/owl.theme.default.css': 'dist/assets/owl.theme.default.css',
						'dist/assets/owl.theme.green.css': 'dist/assets/owl.theme.green.css'
					}
				}
			},

			concat: {
				dist: {
					files: {
						'dist/<%= pkg.name %>.js': '<%= app.src.scripts %>'
					}
				}
			},

			cssmin: {
				dist: {
					files: {
						'dist/assets/<%= pkg.name %>.min.css': 'dist/assets/<%= pkg.name %>.css',
						'dist/assets/owl.theme.default.min.css': 'dist/assets/owl.theme.default.css',
						'dist/assets/owl.theme.green.min.css': 'dist/assets/owl.theme.green.css'
					}
				}
			},

			jshint: {
				options: {
					jshintrc: 'src/js/.jshintrc'
				},
				dist: {
					src: [ '<%= app.src.scripts %>', 'Gruntfile.js' ]
				}
			},

			qunit: {
				options: {
					timeout: 10000
				},
				dist: [ 'test/index.html' ]
			},

			jscs: {
				options: {
					config: 'src/js/.jscsrc',
					reporter: 'text.js',
					reporterOutput: 'jscs.report.txt'
				},
				dist: {
					src: [ '<%= app.src.scripts %>', 'Gruntfile.js' ]
				}
			},

			usebanner: {
				dist: {
					options: {
						banner: '<%= banner %>',
						linebreak: false
					},
					files: {
						src: [
							'dist/<%= pkg.name %>.js',
							'dist/assets/*.css'
						]
					}
				}
			},

			uglify: {
				options: {
					banner: '<%= banner %>'
				},
				dist: {
					files: {
						'dist/<%= pkg.name %>.min.js': 'dist/<%= pkg.name %>.js'
					}
				}
			},

			// copy
			copy: {
				distImages: {
					expand: true,
					flatten: true,
					cwd: 'src/',
					src: [ 'img/*.*' ],
					dest: 'dist/assets'
				},

				distToDocs: {
					expand: true,
					cwd: 'dist/',
					src: [ '**/*.*' ],
					dest: '<%= app.docs.dest %>/assets/owlcarousel'
				},

				srcToDocs: {
					expand: true,
					cwd: 'src/js',
					src: [ '**/*.js' ],
					dest: '<%= app.docs.dest %>/assets/owlcarousel/src'
				},

				docsAssets: {
					expand: true,
					cwd: '<%= app.docs.src %>/assets/',
					src: [ 'css/*.css', 'vendors/*.js', 'vendors/*.map', 'img/*.*', 'js/*.*' ],
					dest: '<%= app.docs.dest %>/assets/'
				},

				readme: {
					files: [ {
						'dist/LICENSE': 'LICENSE',
						'dist/README.md': 'README.md'
					} ]
				}
			},

			// connect
			connect: {
				options: {
					port: 9600,
					open: true,
					livereload: true,
					hostname: 'localhost'
				},
				docs: {
					options: {
						base: "<%= app.docs.dest %>"
					}
				}
			},

			// watch
			watch: {
				options: {
					livereload: true
				},
				templatesDocs: {
					files: [ '<%= app.docs.templates %>/**/*.hbs' ],
					tasks: [ 'assemble' ]
				},
				sassDocs: {
					files: [ '<%= app.docs.src %>/assets/**/*.scss' ],
					tasks: [ 'sass:docs' ]
				},
				sass: {
					files: [ 'src/**/*.scss' ],
					tasks: [ 'sass:dist', 'cssmin:dist', 'usebanner:dist', 'copy:distToDocs' ]
				},
				jsDocs: {
					files: [ '<%= app.docs.src %>/assets/**/*.js' ],
					tasks: [ 'copy:docsAssets' ]
				},
				js: {
					files: [ 'src/**/*.js' ],
					tasks: [ 'jscs:dist', 'jshint:dist', 'qunit:dist', 'concat:dist', 'uglify:dist', 'usebanner:dist', 'copy:distToDocs', 'copy:srcToDocs' ]
				},
				helpersDocs: {
					files: [ '<%= app.docs.src %>/helpers/*.js' ],
					tasks: [ 'assemble' ]
				},
				test: {
					files: [ 'test/*.html', 'test/unit/*.js' ],
					tasks: [ 'qunit:dist' ]
				}
			},

			// compress zip
			compress: {
				zip: {
					options: {
						archive: 'docs/download/owl.carousel.<%= pkg.version %>.zip'
					},
					files: [ {
						expand: true,
						cwd: 'dist/',
						src: [ '**' ],
						dest: 'owl.carousel.<%= pkg.version %>'
					} ]
				}
			},

			// publish to github pages
			'gh-pages': {
				options: {
					base: 'docs'
				},
				src: '**/*'
			}
		});

	grunt.loadNpmTasks('assemble');

	// tasks
	grunt.registerTask('dist', [ 'clean:dist', 'sass:dist', 'autoprefixer', 'concat:dist', 'cssmin:dist', 'copy:distImages', 'usebanner:dist', 'uglify:dist', 'copy:readme' ]);

	grunt.registerTask('docs', [ 'dist', 'clean:docs', 'assemble', 'sass:docs', 'copy:docsAssets', 'copy:distToDocs', 'zip' ]);

	grunt.registerTask('test', [ 'jshint:dist', 'qunit:dist', 'jscs:dist' ]);

	grunt.registerTask('default', [ 'dist', 'docs', 'test' ]);

	grunt.registerTask('serve', [ 'connect:docs', 'watch' ]);

	grunt.registerTask('zip', [ 'compress' ]);

	grunt.registerTask('deploy', [ 'docs', 'gh-pages' ]);

};
