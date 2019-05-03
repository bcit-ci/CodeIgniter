/*
 * Translated default messages for bootstrap-select.
 * Locale: LV (Latvian)
 * Region: LV (Latvia)
 */
(function ($) {
  $.fn.selectpicker.defaults = {
    noneSelectedText: 'Nekas nav atzīmēts',
    noneResultsText: 'Nav neviena rezultāta {0}',
    countSelectedText: function (numSelected, numTotal) {
      return (numSelected == 1) ? '{0} ieraksts atzīmēts' : '{0} ieraksti atzīmēts';
    },
    maxOptionsText: function (numAll, numGroup) {
      return [
        (numAll == 1) ? 'Sasniegts limits ({n} ieraksts maksimums)' : 'Sasniegts limits ({n} ieraksti maksimums)',
        (numGroup == 1) ? 'Sasniegts grupas limits ({n} ieraksts maksimums)' : 'Sasniegts grupas limits ({n} ieraksti maksimums)'
      ];
    },
    selectAllText: 'Atzīmēt visu',
    deselectAllText: 'Neatzīmēt nevienu',
    multipleSeparator: ', '
  };
})(jQuery);
