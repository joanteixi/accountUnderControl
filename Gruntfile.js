'use strict';


module.exports = function (grunt) {

    // Time how long tasks take. Can help when optimizing build times
    require('time-grunt')(grunt);

    // Load grunt tasks automatically
    require('load-grunt-tasks')(grunt);

    // Configurable paths
    var config = {
        app: 'frontend',
        dist: 'dist'
    };

    grunt.initConfig({

        config: config,
        watch: {
            bower: {
                files: ['bower.json'],
                tasks: ['wiredep']
            },

            js: {
                files: ['<%= config.app %>/scripts/{,*/}*.js'],
                tasks: ["jshing"],
                options: {
                    livereload: false
                }
            },

            gruntfile: {
                files: ['Gruntfile.js']
            },

            styles: {
                files: ['<%= config.app %>/css/{,*/}*.css'],
                tasks: ['newer:copy:styles', 'autoprefixer']
            }
        },

        // Empties folders to start fresh
        clear: {
            dist: {
                files: [{
                    dot: true,
                    src: [
                        '.tmp',
                        '<%= config.dist %>/*',
                        '!<%= config.dist %>/.git*'
                    ]
                }]
            },
            server: '.tmp'
        },

        // Make sure code styles are up to par and there are no obvious mistakes
        jshint: {
            options: {
                jshintrc: '.jshintrc',
                reporter: require('jshint-stylish')
            },
            all: [
                'Gruntfile.js',
                '<%= config.app %>/scripts/{,*/}*.js',
                '!<%= config.app %>/scripts/vendor/*',
                'test/spec/{,*/}*.js'
            ]
        },

        // Add vendor prefixed styles
        autoprefixer: {
            options: {
                browsers: ['> 1%', 'last 2 versions', 'Firefox ESR', 'Opera 12.1']
            },
            dist: {
                files: [{
                    expand: true,
                    cwd: '.tmp/styles/',
                    src: '{,*/}*.css',
                    dest: '.tmp/styles/'
                }]
            }
        },

        // Renames files for browser caching purposes
        rev: {
            dist: {
                files: {
                    src: [
                        '<%= config.dist %>/scripts/{,*/}*.js',
                        '<%= config.dist %>/styles/{,*/}*.css',
                        '<%= config.dist %>/images/{,*/}*.*',
                        '<%= config.dist %>/styles/fonts/{,*/}*.*',
                        '<%= config.dist %>/*.{ico,png}'
                    ]
                }
            }
        },

        bowercopy: {
			options: {
				srcPrefix: 'bower_components',
				destPrefix: 'web/assets'
			 },
            myCss: {
                options: {
                    srcPrefix: 'frontend'
                },
                files: {
                    'css/sb-admin.css': 'css/sb-admin.css'
                }
            },
			scripts: {
				files: {
					'js/jquery.js': 'jquery/dist/jquery.js',
					'js/bootstrap.js': 'bootstrap/dist/js/bootstrap.js'
				}
		 	},
			stylesheets: {
				files: {
					'css/bootstrap.css': 'bootstrap/dist/css/bootstrap.css',
					'css/font-awesome.css': 'font-awesome/css/font-awesome.css'

				}
		 	},
			fonts: {
				files: {
					'fonts': 'font-awesome/fonts'
				}
	       		}
		}
	});

	grunt.loadNpmTasks('grunt-bowercopy');
	grunt.registerTask('default', ['bowercopy']);
    grunt.registerTask('build', [
        'clean:dist',
        'wiredep',
        'useminPrepare',
        'concurrent:dist',
        'autoprefixer',
        'concat',
        'cssmin',
        'uglify',
        'copy:dist',
        'rev',
        'usemin',
        'htmlmin'
    ]);
};
