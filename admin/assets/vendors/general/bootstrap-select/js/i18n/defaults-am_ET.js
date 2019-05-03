/*
 * Translated default messages for bootstrap-select.
 * Locale: AM (Amharic)
 * Region: ET (Ethiopia)
 */
(function ($) {
  $.fn.selectpicker.defaults = {
    noneSelectedText: 'ምንም አልተመረጠም',
    noneResultsText: 'ከ{0} ጋር ተመሳሳይ ውጤት የለም',
    countSelectedText: function (numSelected, numTotal) {
      return (numSelected == 1) ? '{0} ምርጫ ተመርጧል' : '{0} ምርጫዎች ተመርጠዋል';
    },
    maxOptionsText: function (numAll, numGroup) {
      return [
        (numAll == 1) ? 'ገደብ ላይ ተደርሷል  (ቢበዛ {n} ምርጫ)' : 'ገደብ ላይ ተደርሷል  (ቢበዛ {n} ምርጫዎች)',
        (numGroup == 1) ? 'የቡድን ገደብ ላይ ተደርሷል (ቢበዛ {n} ምርጫ)' : 'የቡድን ገደብ ላይ ተደርሷል (ቢበዛ {n} ምርጫዎች)'
      ];
    },
    selectAllText: 'ሁሉም ይመረጥ',
    deselectAllText: 'ሁሉም አይመረጥ',
    multipleSeparator: ' ፣ '
  };
})(jQuery);
