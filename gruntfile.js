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
                { src: ['./app/**', './public/**', './*.php', './config.json']  },
                { src: ['./composer.*', './vendor/**']  },
                { src: ['LICENSE', './*.md', './docs/**']  },
            ]
        }
    }
    });
  
    // Load the plugin that provides the "build" task.
    grunt.loadNpmTasks('grunt-contrib-compress');
  
    // Default task(s).
    grunt.registerTask('default', ['compress:build']);
  
  };