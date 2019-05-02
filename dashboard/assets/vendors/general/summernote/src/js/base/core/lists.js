import $ from 'jquery';
import func from './func';

/**
 * returns the first item of an array.
 *
 * @param {Array} array
 */
function head(array) {
  return array[0];
}

/**
 * returns the last item of an array.
 *
 * @param {Array} array
 */
function last(array) {
  return array[array.length - 1];
}

/**
 * returns everything but the last entry of the array.
 *
 * @param {Array} array
 */
function initial(array) {
  return array.slice(0, array.length - 1);
}

/**
 * returns the rest of the items in an array.
 *
 * @param {Array} array
 */
function tail(array) {
  return array.slice(1);
}

/**
 * returns item of array
 */
function find(array, pred) {
  for (let idx = 0, len = array.length; idx < len; idx++) {
    const item = array[idx];
    if (pred(item)) {
      return item;
    }
  }
}

/**
 * returns true if all of the values in the array pass the predicate truth test.
 */
function all(array, pred) {
  for (let idx = 0, len = array.length; idx < len; idx++) {
    if (!pred(array[idx])) {
      return false;
    }
  }
  return true;
}

/**
 * returns index of item
 */
function indexOf(array, item) {
  return $.inArray(item, array);
}

/**
 * returns true if the value is present in the list.
 */
function contains(array, item) {
  return indexOf(array, item) !== -1;
}

/**
 * get sum from a list
 *
 * @param {Array} array - array
 * @param {Function} fn - iterator
 */
function sum(array, fn) {
  fn = fn || func.self;
  return array.reduce(function(memo, v) {
    return memo + fn(v);
  }, 0);
}

/**
 * returns a copy of the collection with array type.
 * @param {Collection} collection - collection eg) node.childNodes, ...
 */
function from(collection) {
  const result = [];
  const length = collection.length;
  let idx = -1;
  while (++idx < length) {
    result[idx] = collection[idx];
  }
  return result;
}

/**
 * returns whether list is empty or not
 */
function isEmpty(array) {
  return !array || !array.length;
}

/**
 * cluster elements by predicate function.
 *
 * @param {Array} array - array
 * @param {Function} fn - predicate function for cluster rule
 * @param {Array[]}
 */
function clusterBy(array, fn) {
  if (!array.length) { return []; }
  const aTail = tail(array);
  return aTail.reduce(function(memo, v) {
    const aLast = last(memo);
    if (fn(last(aLast), v)) {
      aLast[aLast.length] = v;
    } else {
      memo[memo.length] = [v];
    }
    return memo;
  }, [[head(array)]]);
}

/**
 * returns a copy of the array with all false values removed
 *
 * @param {Array} array - array
 * @param {Function} fn - predicate function for cluster rule
 */
function compact(array) {
  const aResult = [];
  for (let idx = 0, len = array.length; idx < len; idx++) {
    if (array[idx]) { aResult.push(array[idx]); }
  }
  return aResult;
}

/**
 * produces a duplicate-free version of the array
 *
 * @param {Array} array
 */
function unique(array) {
  const results = [];

  for (let idx = 0, len = array.length; idx < len; idx++) {
    if (!contains(results, array[idx])) {
      results.push(array[idx]);
    }
  }

  return results;
}

/**
 * returns next item.
 * @param {Array} array
 */
function next(array, item) {
  const idx = indexOf(array, item);
  if (idx === -1) { return null; }

  return array[idx + 1];
}

/**
 * returns prev item.
 * @param {Array} array
 */
function prev(array, item) {
  const idx = indexOf(array, item);
  if (idx === -1) { return null; }

  return array[idx - 1];
}

/**
 * @class core.list
 *
 * list utils
 *
 * @singleton
 * @alternateClassName list
 */
export default {
  head,
  last,
  initial,
  tail,
  prev,
  next,
  find,
  contains,
  all,
  sum,
  from,
  isEmpty,
  clusterBy,
  compact,
  unique
};
