<!DOCTYPE html>
<html lang="en">
  <head>

    <!-- head -->
    <meta charset="utf-8">
    <meta name="msapplication-tap-highlight" content="no" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Owl Carousel Documentation">
    <meta name="author" content="David Deutsch">
    <title>
      Events | Owl Carousel | 2.3.4
    </title>

    <!-- Stylesheets -->
    <link href='https://fonts.googleapis.com/css?family=Lato:300,400,700,400italic,300italic' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="../assets/css/docs.theme.min.css">

    <!-- Owl Stylesheets -->
    <link rel="stylesheet" href="../assets/owlcarousel/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="../assets/owlcarousel/assets/owl.theme.default.min.css">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->

    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <!-- Favicons -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/ico/apple-touch-icon-144-precomposed.png">
    <link rel="shortcut icon" href="../assets/ico/favicon.png">
    <link rel="shortcut icon" href="favicon.ico">

    <!-- Yeah i know js should not be in header. Its required for demos.-->

    <!-- javascript -->
    <script src="../assets/vendors/jquery.min.js"></script>
    <script src="../assets/owlcarousel/owl.carousel.js"></script>
  </head>
  <body>

    <!-- header -->
    <header class="header">
      <div class="row">
        <div class="large-12 columns">
          <div class="brand left">
            <h3>
              <a href="/OwlCarousel2/">owl.carousel.js</a> 
            </h3>
          </div>
          <a id="toggle-nav" class="right">
            <span></span> <span></span> <span></span> 
          </a> 
          <div class="nav-bar">
            <ul class="clearfix">
              <li> <a href="/OwlCarousel2/index.html">Home</a>  </li>
              <li> <a href="/OwlCarousel2/demos/demos.html">Demos</a>  </li>
              <li class="active">
                <a href="/OwlCarousel2/docs/started-welcome.html">Docs</a> 
              </li>
              <li>
                <a href="https://github.com/OwlCarousel2/OwlCarousel2/archive/2.3.4.zip">Download</a> 
                <span class="download"></span> 
              </li>
            </ul>
          </div>
        </div>
      </div>
    </header>

    <!-- title -->
    <section class="title">
      <div class="row">
        <div class="large-12 columns">
          <h1>API</h1>
        </div>
      </div>
    </section>
    <div id="docs">
      <div class="row">
        <div class="small-12 medium-3 large-3 columns">
          <ul class="side-nav">
            <li class="side-nav-head">Getting Started</li>
            <li> <a href="started-welcome.html">Welcome</a>  </li>
            <li> <a href="started-installation.html">Installation</a>  </li>
            <li> <a href="started-faq.html">FAQ</a>  </li>
          </ul>
          <ul class="side-nav">
            <li class="side-nav-head">API</li>
            <li> <a href="api-options.html">Options</a>  </li>
            <li> <a href="api-classes.html">Classes</a>  </li>
            <li> <a href="api-events.html">Events</a>  </li>
          </ul>
          <ul class="side-nav">
            <li class="side-nav-head">Development</li>
            <li> <a href="dev-buildin-plugins.html">Built-in Plugins</a>  </li>
            <li> <a href="dev-plugin-api.html">Plugin API</a>  </li>
            <li> <a href="dev-styles.html">Sass Styles</a>  </li>
            <li> <a href="dev-external.html">External Libs</a>  </li>
          </ul>
          <ul class="side-nav">
            <li class="side-nav-head">Support</li>
            <li> <a href="support-contributing.html">Contributing</a>  </li>
            <li> <a href="support-changelog.html">Changelog</a>  </li>
            <li> <a href="support-contact.html">Contact</a>  </li>
          </ul>
        </div>
        <div class="small-12 medium-9 large-9 columns">
          <article class="docs-content">
            <h2 id="events">Events</h2>
            <blockquote>
              <p>Events are provided by Owl Carousel in strategic code locations. This gives you the ability to listen for any changes and perform your own actions.</p>
            </blockquote>
            <pre><code>var owl = $(&#39;.owl-carousel&#39;);
owl.owlCarousel();
// Listen to owl events:
owl.on(&#39;changed.owl.carousel&#39;, function(event) {
    ...
})</code></pre>
            <p>You could also trigger events by yourself to control Owl Carousel:</p>
            <pre><code>var owl = $(&#39;.owl-carousel&#39;);
owl.owlCarousel();
// Go to the next item
$(&#39;.customNextBtn&#39;).click(function() {
    owl.trigger(&#39;next.owl.carousel&#39;);
})
// Go to the previous item
$(&#39;.customPrevBtn&#39;).click(function() {
    // With optional speed parameter
    // Parameters has to be in square bracket &#39;[]&#39;
    owl.trigger(&#39;prev.owl.carousel&#39;, [300]);
})</code></pre>
            <h3 id="callbacks">Callbacks</h3>
            <p>Instead of attaching an event handler you can also just add a callback to the options of Owl Carousel.</p>
            <pre><code>$(&#39;.owl-carousel&#39;).owlCarousel({
    onDragged: callback
});
function callback(event) {
    ...
}</code></pre>
            <h3 id="data">Data</h3>
            <p>Each event passes very useful information within the
              <a href="https://api.jquery.com/category/events/event-object/">event object</a> . Based on the example above:</p>
            <pre><code class="language-Javascript">function callback(event) {
    // Provided by the core
    var element   = event.target;         // DOM element, in this example .owl-carousel
    var name      = event.type;           // Name of the event, in this example dragged
    var namespace = event.namespace;      // Namespace of the event, in this example owl.carousel
    var items     = event.item.count;     // Number of items
    var item      = event.item.index;     // Position of the current item
    // Provided by the navigation plugin
    var pages     = event.page.count;     // Number of pages
    var page      = event.page.index;     // Position of the current page
    var size      = event.page.size;      // Number of items per page
}</code></pre>
            <h3 id="carousel">Carousel</h3>
            <h4 id="initialize-owl-carousel">initialize.owl.carousel</h4>
            <p>Type: <code>attachable</code>
              <br />Callback: <code>onInitialize</code>
              <br/>
            </p>
            <p>When the plugin initializes.</p>
            <hr>
            <h4 id="initialized-owl-carousel">initialized.owl.carousel</h4>
            <p>Type: <code>attachable</code>
              <br />Callback: <code>onInitialized</code>
              <br/>
            </p>
            <p>When the plugin has initialized.</p>
            <hr>
            <h4 id="resize-owl-carousel">resize.owl.carousel</h4>
            <p>Type: <code>attachable</code>
              <br />Callback: <code>onResize</code>
              <br/>
            </p>
            <p>When the plugin gets resized.</p>
            <hr>
            <h4 id="resized-owl-carousel">resized.owl.carousel</h4>
            <p>Type: <code>attachable</code>
              <br />Callback: <code>onResized</code>
              <br/>
            </p>
            <p>When the plugin has resized.</p>
            <hr>
            <h4 id="refresh-owl-carousel">refresh.owl.carousel</h4>
            <p>Type: <code>attachable, cancelable, triggerable</code>
              <br />Callback: <code>onRefresh</code>
              <br/>Parameter: <code>[event, speed]</code>
              <br/>
            </p>
            <p>When the internal state of the plugin needs update.</p>
            <hr>
            <h4 id="refreshed-owl-carousel">refreshed.owl.carousel</h4>
            <p>Type: <code>attachable</code>
              <br />Callback: <code>onRefreshed</code>
              <br/>
            </p>
            <p>When the internal state of the plugin has updated.</p>
            <hr>
            <h4 id="drag-owl-carousel">drag.owl.carousel</h4>
            <p>Type: <code>attachable</code>
              <br />Callback: <code>onDrag</code>
              <br/>
            </p>
            <p>When the dragging of an item is started.</p>
            <hr>
            <h4 id="dragged-owl-carousel">dragged.owl.carousel</h4>
            <p>Type: <code>attachable</code>
              <br />Callback: <code>onDragged</code>
              <br/>
            </p>
            <p>When the dragging of an item has finished.</p>
            <hr>
            <h4 id="translate-owl-carousel">translate.owl.carousel</h4>
            <p>Type: <code>attachable</code>
              <br />Callback: <code>onTranslate</code>
              <br/>
            </p>
            <p>When the translation of the stage starts.</p>
            <hr>
            <h4 id="translated-owl-carousel">translated.owl.carousel</h4>
            <p>Type: <code>attachable</code>
              <br />Callback: <code>onTranslated</code>
              <br/>
            </p>
            <p>When the translation of the stage has finished.</p>
            <hr>
            <h4 id="change-owl-carousel">change.owl.carousel</h4>
            <p>Type: <code>attachable</code>
              <br />Callback: <code>onChange</code>
              <br/>Parameter: <code>property</code>
              <br/>
            </p>
            <p>When a property is going to change its value.</p>
            <hr>
            <h4 id="changed-owl-carousel">changed.owl.carousel</h4>
            <p>Type: <code>attachable</code>
              <br />Callback: <code>onChanged</code>
              <br/>Parameter: <code>property</code>
              <br/>
            </p>
            <p>When a property has changed its value.</p>
            <hr>
            <h4 id="next-owl-carousel">next.owl.carousel</h4>
            <p>Type: <code>triggerable</code>
              <br />Parameter: <code>[speed]</code>
              <br/>
            </p>
            <p>Goes to next item.</p>
            <hr>
            <h4 id="prev-owl-carousel">prev.owl.carousel</h4>
            <p>Type: <code>triggerable</code>
              <br />Parameter: <code>[speed]</code>
              <br/>
            </p>
            <p>Goes to previous item.</p>
            <hr>
            <h4 id="to-owl-carousel">to.owl.carousel</h4>
            <p>Type: <code>triggerable</code>
              <br />Parameter: <code>[position, speed]</code>
              <br/>
            </p>
            <p>Goes to position.</p>
            <hr>
            <h4 id="destroy-owl-carousel">destroy.owl.carousel</h4>
            <p>Type: <code>triggerable</code>
              <br />
            </p>
            <p>Destroys carousel.</p>
            <hr>
            <h4 id="replace-owl-carousel">replace.owl.carousel</h4>
            <p>Type: <code>triggerable</code>
              <br />Parameter: <code>data</code>
              <br/>
            </p>
            <p>Removes current content and add a new one passed in the parameter.</p>
            <hr>
            <h4 id="add-owl-carousel">add.owl.carousel</h4>
            <p>Type: <code>triggerable</code>
              <br />Parameter: <code>[data, position]</code>
              <br/>
            </p>
            <p>Adds a new item on a given position.</p>
            <hr>
            <h4 id="remove-owl-carousel">remove.owl.carousel</h4>
            <p>Type: <code>triggerable</code>
              <br />Parameter: <code>position</code>
              <br/>
            </p>
            <p>Removes an item from a given position.</p>
            <hr>
            <h3 id="lazy">Lazy</h3>
            <h4 id="load-owl-lazy">load.owl.lazy</h4>
            <p>Type: <code>attachable</code>
              <br />Callback: <code>onLoadLazy</code>
              <br/>
            </p>
            <p>When lazy image loads.</p>
            <hr>
            <h4 id="loaded-owl-lazy">loaded.owl.lazy</h4>
            <p>Type: <code>attachable</code>
              <br />Callback: <code>onLoadedLazy</code>
              <br/>
            </p>
            <p>When lazy image has loaded.</p>
            <hr>
            <h3 id="autoplay">Autoplay</h3>
            <h4 id="play-owl-autoplay">play.owl.autoplay</h4>
            <p>Type: <code>triggerable</code>
              <br />Parameter: <code>[timeout, speed]</code>
              <br/>
            </p>
            <p>Runs autoplay.</p>
            <hr>
            <h4 id="stop-owl-autoplay">stop.owl.autoplay</h4>
            <p>Type: <code>triggerable</code>
              <br />
            </p>
            <p>Stops autoplay.</p>
            <hr>
            <h3 id="video">Video</h3>
            <h4 id="stop-owl-video">stop.owl.video</h4>
            <p>Type: <code>attachable</code>
              <br />Callback: <code>onStopVideo</code>
              <br/>
            </p>
            <p>When video has unloaded.</p>
            <hr>
            <h4 id="play-owl-video">play.owl.video</h4>
            <p>Type: <code>attachable</code>
              <br />Callback: <code>onPlayVideo</code>
              <br/>
            </p>
            <p>When video has loaded.</p>
            <hr>
          </article>
        </div>
      </div>
    </div>

    <!-- footer -->
    <footer class="footer">
      <div class="row">
        <div class="large-12 columns">
          <h5>
            <a href="/OwlCarousel2/docs/support-contact.html">David Deutsch</a> 
            <a id="custom-tweet-button" href="https://twitter.com/share?url=https://github.com/OwlCarousel2/OwlCarousel2&text=Owl Carousel - This is so awesome! " target="_blank"></a> 
          </h5>
        </div>
      </div>
    </footer>

    <!-- vendors -->
    <script src="../assets/vendors/highlight.js"></script>
    <script src="../assets/js/app.js"></script>
  </body>
</html>