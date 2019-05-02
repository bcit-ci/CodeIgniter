describe('DefaultMenu', function() {
  var www = WWW();

  beforeEach(function() {
    var $fixture;

    jasmine.Dataset.useMock();

    setFixtures('<div id="menu-fixture"></div>');

    $fixture = $('#jasmine-fixtures');
    this.$node = $fixture.find('#menu-fixture');
    this.$node.html(fixtures.html.dataset);

    this.view = new DefaultMenu({ node: this.$node, datasets: [{}] }, www).bind();
    this.dataset = this.view.datasets[0];
  });

  describe('when rendered is triggered on a dataset', function() {
    it('should hide menu if empty', function() {
      this.dataset.isEmpty.andReturn(true);

      this.view._show();
      this.dataset.trigger('rendered');

      expect(this.$node).not.toBeVisible();
    });

    it('should not show menu if not open', function() {
      this.dataset.isEmpty.andReturn(false);

      this.view._hide();
      this.dataset.trigger('rendered');

      expect(this.$node).not.toBeVisible();
    });

    it('should show menu if not empty and open', function() {
      this.dataset.isEmpty.andReturn(false);

      this.view._hide();
      this.view.open();
      this.dataset.trigger('rendered');

      expect(this.$node).toBeVisible();
    });
  });

  describe('when cleared is triggered on a dataset', function() {
    it('should hide menu if empty', function() {
      this.dataset.isEmpty.andReturn(true);

      this.view._show();
      this.dataset.trigger('cleared');

      expect(this.$node).not.toBeVisible();
    });

    it('should not show menu if not open', function() {
      this.dataset.isEmpty.andReturn(false);

      this.view._hide();
      this.dataset.trigger('cleared');

      expect(this.$node).not.toBeVisible();
    });

    it('should show menu if not empty and open', function() {
      this.dataset.isEmpty.andReturn(false);

      this.view._hide();
      this.view.open();
      this.dataset.trigger('cleared');

      expect(this.$node).toBeVisible();
    });
  });

  describe('#open', function() {
    it('should show menu if not empty', function() {
      spyOn(this.view, '_allDatasetsEmpty').andReturn(false);
      this.view.open();

      expect(this.$node[0].getAttribute('style')).toMatch(/display: block/);
    });

    it('should not show menu if empty', function() {
      spyOn(this.view, '_allDatasetsEmpty').andReturn(true);
      this.view.open();

      expect(this.$node).not.toHaveAttr('style', 'display: block;');
    });
  });

  describe('#close', function() {
    it('should hide menu', function() {
      this.view._show();
      this.view.close();

      expect(this.$node).not.toBeVisible();
    });
  });
});
