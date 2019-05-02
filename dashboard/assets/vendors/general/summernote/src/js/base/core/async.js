import $ from 'jquery';

/**
 * @method readFileAsDataURL
 *
 * read contents of file as representing URL
 *
 * @param {File} file
 * @return {Promise} - then: dataUrl
 */
export function readFileAsDataURL(file) {
  return $.Deferred((deferred) => {
    $.extend(new FileReader(), {
      onload: (e) => {
        const dataURL = e.target.result;
        deferred.resolve(dataURL);
      },
      onerror: (err) => {
        deferred.reject(err);
      }
    }).readAsDataURL(file);
  }).promise();
}

/**
 * @method createImage
 *
 * create `<image>` from url string
 *
 * @param {String} url
 * @return {Promise} - then: $image
 */
export function createImage(url) {
  return $.Deferred((deferred) => {
    const $img = $('<img>');

    $img.one('load', () => {
      $img.off('error abort');
      deferred.resolve($img);
    }).one('error abort', () => {
      $img.off('load').detach();
      deferred.reject($img);
    }).css({
      display: 'none'
    }).appendTo(document.body).attr('src', url);
  }).promise();
}
