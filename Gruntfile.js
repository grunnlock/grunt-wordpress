'use strict';

module.exports = function( grunt ) {

	var packageJSON = grunt.file.readJSON('package.json');

	var config  	= {
		port: 35729,
		dev: packageJSON.name + '_dev',
		dist: packageJSON.name,
		folders: {
			js:   'assets/js',
			less: 'assets/less',
			css:  'assets/css',
			img:  'assets/img'
		}
	};

	// Load grunt tasks automatically
	require('load-grunt-tasks')( grunt );

	// Time how long tasks take
	require('time-grunt')( grunt );

	grunt.initConfig({

		config: config,

		// Watch files and reload page if they're edited
		watch: {
			options: {
            	livereload: {port: '<%= config.port %>'}
            },
            less: {
            	files: ['<%= config.dev %>/<%= config.folders.less %>/**/*.less'],
       			tasks: ['less']
            },
            all: {
            	files: ['<%= config.dev %>/**/*'],
            }
        },

		// Check JS syntax to avoid errors
		jshint: {
			all: ['<%= config.dev %>/<%= config.folders.js %>/**/*.js']
		},

        // Delete files
        clean: {
        	build: {
				src: ['<%= config.dist %>']
        	}
        },

        // Copy/paste files
		copy: {
			options: {
                noProcess: ['<%= config.dev %>/**/*.{png,gif,jpg,ico,psd,svg}']
			},
			build: {
                files: [{
                    expand: true,
                    dot: true,
                    cwd: '<%= config.dev %>',
                    src: [
                    	'**/*',
                    	'!**/less/**'
                    ],
                    dest: '<%= config.dist %>'
                }]
            }
        },

  		// Update header.php to delete livereload link
  		targethtml: {
			build: {
				files: {'<%= config.dist %>/header.php': '<%= config.dist %>/header.php'}
			}
		},

		// Run some tasks in parallel to speed up the process
	    concurrent: {
	    	build: ['less']
	    },

	    // Less (CSS pre-processor)
		less: {
			all: {
				files: [{
					expand: true,
					cwd: '<%= config.dev %>/<%= config.folders.less %>',
					src: ['*.less'],
					dest: '<%= config.dev %>/<%= config.folders.css %>',
					ext: '.css'
				}]
			}
		},

		// CAUSES A BUG WITH SINGLE.PHP FILE
		// // Minify HTML (and preserve PHP scripts)
		// htmlclean: {
		// 	options: {
		// 		edit: function(html) {
		// 			return html.replace(/\begg(s?)\b/ig, 'omelet$1');
		// 		}
		// 	},
		// 	build: {
		// 		expand: true,
		// 		cwd: '<%= config.dist %>/',
		// 		src: '**/*.php',
		// 		dest: '<%= config.dist %>/'
		// 	}
		// },

		// Update the package.json version
		bump: {
			options: {
				files: ['package.json'],
				commit: false,
				createTag: false,
				push: false
			}
		},

		// Replace global variables by their values in all files regardless of their type
		replace: {
			build: {
				overwrite: true,
				src: '<%= config.dist %>/**/*.{php,html,css,js,json,xml,less,sass,scss}',
				replacements: [{
					from: '@@PROJECT_NAME',
					to: packageJSON.name
				}, {
					from: '@@PROJECT_VERSION',
					to: packageJSON.version
				}, {
					from: '@@PROJECT_ENVIRONMENT',
					to: 'Release'
				}]
			}
		},

		// Execute shell commands, in our case "grunt replace"
		shell: {
	        replace: {
	            command: 'grunt replace'
	        }
	    },

		// Delete empty folders and files
		cleanempty: {
			options: {
				noJunk: true
			},
			build: {
				files: {'<%= config.dist %>/**/*': '<%= config.dist %>/**/*'}
			}
		},

	    // Minify JS files
		uglify: {
			build: {
				files: [{
					expand: true,
					cwd: '<%= config.dist %>',
					src: '**/*.js',
					dest: '<%= config.dist %>'
				}]
			}
		},

		// Minify CSS files
		cssmin: {
			build: {
				files: [{
					expand: true,
					cwd: '<%= config.dist %>',
					src: '**/*.css',
					dest: '<%= config.dist %>'
				}]
			}
		},

	    // Compress images
		imagemin: {
			build: {
				files: [{
					expand: true,
					cwd: '<%= config.dist %>',
					src: '**/*.{png,jpg,jpeg,gif}',
					dest: '<%= config.dist %>'
				}]
			}
	    },

		// Upload files on different environments (FTP servers)
		dploy: {
			demo: {
				host: 'ftp.your-server.com',
				user: 'user',
				pass: 'secret-password',
				path: {
				    local: packageJSON.name,
				    remote: 'public_html/demo/'
				}
			},
			live: {
				host: 'ftp.your-server.com',
				user: 'user',
				pass: 'secret-password',
				path: {
				    local: packageJSON.name,
				    remote: 'public_html/dev/'
				}
			}
		}

	});

	grunt.registerTask('serve', [
		'jshint',
		'concurrent',
		'watch'
	]);

	grunt.registerTask('default', [
		'jshint',
		'build'
	]);

	// Compile files and bump the version of package.json and across all files
	grunt.registerTask('build', function ( releaseType ) {

		// Run the building process
		grunt.task.run([
			'clean:build',
			'concurrent',
			'copy',
		    'uglify',
		    'cssmin',
		    'targethtml',
		    'cleanempty'
		]);

		// Bump the package.json version depending of the release type
		if( !releaseType ) {
			// Not a release, simply a development update

			// Get current project version
			var currentVersion = packageJSON.version;

			// Split the current version into an array
			currentVersion = currentVersion.split('.');

			// Do a minor bump or a patch bump depending of the current version
			if( currentVersion[ currentVersion.length-1 ] > 8 ) {
				grunt.task.run(['bump:minor']);
			} else {
				grunt.task.run(['bump:patch']);
			}

		} else {

			// Release
			if( releaseType === 'release' ) {
				// Main release
				grunt.task.run(['bump:major']);
			} else {
				// Incorrect release type
				grunt.fail.warn('You specified an incorrect release type. Only "release" is supported as a release type for the moment.');
			}

		}

		// Update the version of all files (using shell command as we need to wait for package.json to be updated before editing the version across all files)
		grunt.task.run(['shell:replace']);

	});

	// Task to deploy the projects on all environments
	grunt.registerTask('deploy', function ( target ) {
		if( !target ) {
			grunt.fail.warn('You need to specify the environment (FTP server) where you want to upload the files.');
		} else {
			grunt.task.run([ 'dploy:' + target ]);
		}
	});

	grunt.registerTask('getVersion', function() {
		grunt.log.ok( 'Package.json version: ' + packageJSON.version );
	});

};