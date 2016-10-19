'use strict';
module.exports = function(grunt) {
    // подгружаем необходимые плагины
    require('load-grunt-tasks')(grunt);

    require('time-grunt')(grunt);
    // конфигурация
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        concurrent: {
            stream1: ['jshint', 'copy', 'bower_concat'],
            stream2: ['concat', 'dev_prod_switch'],
            stream3: ['removelogging', 'sass'],
            stream4: ['uglify', 'cssmin']
        },
        jshint: {
            options: {
                'browser': true,
                'esnext': true,
                'globals': {
                    '$': true,
                    'console': true,
                    'jQuery': true
                },
                'strict': true,
                'globalstrict': false,
                'curly': true,
                'quotmark': true,
                'eqeqeq': true,
                'eqnull': true,
                'expr': true,
                'latedef': true,
                'onevar': true,
                'noarg': true,
                'node': true,
                'trailing': true,
                'undef': true,
                'unused': true
            },
            '<%== pkg.name %>': {
                src: ['views/js/**/*.js']
            }
        },
        bower_concat: {
            build: {
                dest: {
                    'js': 'tmp/bower.js',
                    'css': 'tmp/bower.css'
                },
                dependencies: {
                    'bootstrap': 'jquery',
                },
                bowerOptions: {
                    relative: false
                }
            }
        },
        concat: {
            options: {
                stripBanners: true,
                banner: '/*! <%= pkg.name %> - v<%= pkg.version %> - <%= grunt.template.today("yyyy-mm-dd") %> */\n',
            },
            build: {
                files: {
                    'tmp/terminal.js': ['tmp/bower.js', 'views/js/terminal.js', 'views/js/cashcode.js'],
                },
            }
        },
        removelogging: {
            build: {
                files: {
                    'tmp/terminal.min.js': 'tmp/terminal.js',
                },

                options: {
                    // see below for options. this is optional. 
                }
            }
        },
        uglify: {
            options: {
                stripBanners: true,
                banner: '/*! <%= pkg.name %> - v<%= pkg.version %> - <%= grunt.template.today("yyyy-mm-dd") %> */\n',
            },
            build: {
                files: [{
                    expand: true,
                    cwd: 'tmp',
                    src: '*.min.js',
                    dest: 'release/views/js'
                }]
            }
        },
        sass: {
            build: {
                options: {
                    style: 'expanded'
                },
                files: {
                    'views/css/style-sass.css': 'views/css/style.scss',
                    'views/css/login-sass.css': 'views/css/login.scss',
                }
            }
        },
        cssmin: {
            build: {
                options: {
                    banner: '/*! <%= pkg.name %> - v<%= pkg.version %> - <%= grunt.template.today("yyyy-mm-dd") %> */\n'
                },
                files: {
                    'release/views/css/login.min.css' : [
                        'bower_components/bootstrap/dist/css/bootstrap.css',
                        'views/css/login*.css',
                    ],
                    'release/views/css/style.min.css' : [
                        'bower_components/bootstrap/dist/css/bootstrap.css',
                        'views/css/style*.css',
                    ]
                }
            }
        },
        watch: {
            sass: {
                files: ['views/css/*.scss', '!views/css/_*.scss'],
                tasks: ['newer:sass'],
            },
            sass2: {
                files: 'views/css/_*.scss',
                tasks: ['sass'],
            },
            js: {
                files: 'views/js/*.js',
                tasks: ['newer:jshint']
            }
        },
        dev_prod_switch: {
            options: {
                // Can be ran as `grunt --env=dev` or ``grunt --env=prod``
                // environment: grunt.option('env') || 'dev', // 'prod' or 'dev'
                environment: 'prod', // 'prod' or 'dev'
                env_char: '#',
                env_block_dev: 'env:dev',
                env_block_prod: 'env:prod'
            },
            build: {
                files: [
                    {
                        expand: true,
                        cwd: '',
                        src: ['*.html', '*.php'],
                        dest: 'release/'
                    },
                    {
                        expand: true,
                        cwd: 'views/',
                        src: ['*.html', '*.php'],
                        dest: 'release/views/'
                    },
                ]
            }
        },
        copy: {
            build: {
                files: [
                {expand: true, flatten: true, src: ['views/img/*'], dest: 'release/views/img/', filter: 'isFile'},
                {expand: true, flatten: true, src: ['views/include/*'], dest: 'release/views/include/', filter: 'isFile'},
                {expand: true, flatten: true, src: ['bower_components/bootstrap/fonts/*'], dest: 'release/fonts/', filter: 'isFile'},
                {expand: true, flatten: true, src: ['components/*'], dest: 'release/components/', filter: 'isFile'},
                {expand: true, flatten: true, src: ['controllers/*'], dest: 'release/controllers/', filter: 'isFile'},
                {expand: true, flatten: true, src: ['config/*'], dest: 'release/config/', filter: 'isFile'},
                {expand: true, flatten: true, src: ['models/*'], dest: 'release/models/', filter: 'isFile'},
                {expand: true, flatten: true, src: ['controllers/'], dest: 'release/controllers/', filter: 'isFile'},
                {expand: true, flatten: true, src: ['.htaccess'], dest: 'release/', filter: 'isFile'},
                ]
            },
        },
        clean: {
            build: ['tmp/'],
            release: ['release/'],
            css: ['views/css/*.map', 'views/css/*.css']
        },
    });

    // регистрируем задачи
    grunt.registerTask('default', ['watch']);
    grunt.registerTask('prod', ['clean', 'bower_concat', 'concurrent:stream1', 'concurrent:stream2', 'concurrent:stream3', 'concurrent:stream4']);
};
