const composer          = require('./composer.json');
const [vendor, package] = composer.name.split("/");
const version           = composer.version;

module.exports = function(grunt) {

    // Project configuration.
    grunt.initConfig({
      compress: {
        build: {
            options: {
                archive: `./dist/${package}_v${version}.zip`,
                mode: 'zip'
            },
            files: [
                { src: ['./app/**', './public/**', './*.php', './config.json'], dest: `/${package}/` },
                { src: ['./composer.*', './vendor/**'], dest: `/${package}/`  },
                { src: ['LICENSE', './*.md', './docs/**'], dest: `/${package}/`  },
            ]
        }
    }
    });
  
    // Load the plugin that provides the "build" task.
    grunt.loadNpmTasks('grunt-contrib-compress');
  
    // Default task(s).
    grunt.registerTask('default', ['build']);
    grunt.registerTask('build', ['compress:build']);
  
  };