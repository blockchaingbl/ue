module.exports = function (grunt) {
  'use strict';

  var requirejs = require('requirejs');
  var path = require('path');

  var rDefineStart = /define\([^{]*?{/;
  var rDefineEndWithReturn = /\s*return\s+[^\}]+(\}\);[^\w\}]*)$/;
  var rDefineEnd = /\}\);[^}\w]*$/;

  grunt.registerMultiTask('build', 'concatenate source: summernote.js', function () {
    var self = this;

    var done = this.async();
    requirejs.optimize({
      name: 'summernote/summernote',
      baseUrl: this.data.baseUrl,
      /**
       * Handle final output from the optimizer
       */
      out: function (compiled) {
        // 01. Embed version
        var version = grunt.config('pkg.version');
        compiled = compiled.replace(/@VERSION/g, version);

        // 02.  Embed Date
        var date = (new Date()).toISOString().replace(/:\d+\.\d+Z$/, 'Z');
        compiled = compiled.replace(/@DATE/g, date);

        grunt.file.write(self.data.outFile, compiled);
      },
      optimize: 'none',
      wrap: {
        startFile: path.join(this.data.baseUrl, this.data.startFile),
        endFile: path.join(this.data.baseUrl, this.data.endFile)
      },
      findNestedDependencies: true,
      skipSemiColonInsertion: true,
      /**
       * Strip all definitions generated by requirejs
       *
       * @param {String} name
       * @param {String} path
       * @param {String} contents The contents to be written (including their AMD wrappers)
       */
      onBuildWrite: function (name, path, contents) {
        contents = contents.replace(rDefineStart, '');

        if (rDefineEndWithReturn.test(contents)) {
          contents = contents.replace(rDefineEndWithReturn, '');
        } else {
          contents = contents.replace(rDefineEnd, '');
        }
        return contents;
      },
      excludeShallow: ['jquery', 'codemirror', 'app'],
      include: ['summernote/bs3/settings'],
      paths: {
        jquery: 'empty:',
        codemirror: 'empty:'
      },
      packages: [{
        name: 'summernote',
        location: './'
      }]
    }, function () {
      done();
    }, function (err) {
      done(err);
    });
  });
};
