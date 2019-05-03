module.exports = function(config) {
  config.set({
    basePath: '',

    preprocessors: {
      'src/**/*.js': 'coverage'
    },

    reporters: ['progress', 'coverage'],

    browsers: ['Chrome'],

    frameworks: ['jasmine'],

    coverageReporter: {
      type: 'html',
      dir: 'test/coverage/'
    },

    files: [
      'bower_components/jquery/jquery.js',
      'src/common/utils.js',
      'src/bloodhound/version.js',
      'src/bloodhound/tokenizers.js',
      'src/bloodhound/lru_cache.js',
      'src/bloodhound/persistent_storage.js',
      'src/bloodhound/transport.js',
      'src/bloodhound/remote.js',
      'src/bloodhound/prefetch.js',
      'src/bloodhound/search_index.js',
      'src/bloodhound/options_parser.js',
      'src/bloodhound/bloodhound.js',
      'src/typeahead/www.js',
      'src/typeahead/event_bus.js',
      'src/typeahead/event_emitter.js',
      'src/typeahead/highlight.js',
      'src/typeahead/input.js',
      'src/typeahead/dataset.js',
      'src/typeahead/menu.js',
      'src/typeahead/default_menu.js',
      'src/typeahead/typeahead.js',
      'src/typeahead/plugin.js',
      'test/fixtures/**/*',
      'bower_components/jasmine-jquery/lib/jasmine-jquery.js',
      'bower_components/jasmine-ajax/lib/mock-ajax.js',
      'test/helpers/**/*',
      'test/**/*_spec.js'
    ]
  });
};
