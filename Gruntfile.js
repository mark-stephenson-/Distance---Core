module.exports = function(grunt) {

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        watch: {
            options: {
              livereload: true
            },
            less: {
                files: ['app/less/*'],
                tasks: ['less:dev']
            },
            js: {
                files: ['public/js/*.js', '!public/js/*.min.js'],
                tasks: ['uglify:dev']
            },
        },
        less: {
            dev: {
                files: {
                    "public/css/app.min.css": "app/less/app.less"
                }
            },
            prod: {
                options: {
                    yuicompress: true
                },
                files: {
                    "public/css/app.min.css": "app/less/app.less"
                }
            }
        },
        uglify: {
            options: {
                mangle: false
            },
            dev: {
                options: {
                    beautify: true
                },
                files: {
                    'public/js/app.min.js': [
                        'public/js/bootstrap/bootstrap-dropdown.js',
                        'public/js/bootstrap/bootstrap-modal.js',
                        'public/js/jquery.nestable.js',
                        'public/js/select2.js',
                        'public/js/app.js'
                    ]
                }
            },
            prod: {
                options: {
                    report: 'gzip'
                },
                files: {
                    'public/js/app.min.js': [
                        'public/js/bootstrap/bootstrap-dropdown.js',
                        'public/js/bootstrap/bootstrap-modal.js',
                        'public/js/jquery.nestable.js',
                        'public/js/select2.js',
                        'public/js/app.js'
                    ]
                }
            }
        }
    });

    // Load plugins
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-uglify');

    // Default task(s).
    grunt.registerTask('default', ['watch']);
    grunt.registerTask('prod', ['less:prod', 'uglify:prod']);

};