<h1 align="center">bootstrap-select</h1>

<p align="center">
	<strong>The jQuery plugin that brings select elements into the 21st century with intuitive multiselection, searching, and much more. Now with Bootstrap 4 support.</strong>
</p>

<p align="center">
	<a href="https://github.com/snapappointments/bootstrap-select/releases/latest" target="_blank">
		<img src="https://img.shields.io/github/release/snapappointments/bootstrap-select.svg" alt="Latest release">
	</a>
	<a href="https://www.npmjs.com/package/bootstrap-select" target="_blank">
		<img src="https://img.shields.io/npm/v/bootstrap-select.svg" alt="npm">
	</a>
	<a href="https://www.nuget.org/packages/bootstrap-select" target="_blank">
		<img src="https://img.shields.io/nuget/v/bootstrap-select.svg" alt="NuGet">
	</a>
	<a href="https://cdnjs.com/libraries/bootstrap-select" target="_blank">
		<img src="https://img.shields.io/cdnjs/v/bootstrap-select.svg" alt="CDNJS">
	</a>
	<br>
	<a href="https://cdnjs.com/libraries/bootstrap-select" target="_blank">
		<img src="https://img.shields.io/badge/license-MIT-brightgreen.svg" alt="License">
	</a>
	<a href="https://david-dm.org/snapappointments/bootstrap-select?type=peer" target="_blank">
		<img src="https://img.shields.io/david/peer/snapappointments/bootstrap-select.svg" alt="peerDependencies Status">
	</a>
	<a href="https://david-dm.org/snapappointments/bootstrap-select#info=devDependencies" target="_blank">
		<img src="https://david-dm.org/snapappointments/bootstrap-select/dev-status.svg" alt="devDependency Status">
	</a>
</p>

<p align="center">
	<a href="https://developer.snapappointments.com/bootstrap-select"><img src="https://user-images.githubusercontent.com/2874325/38997831-97e12bbe-43ab-11e8-85f5-b8c05d91c7b1.gif" width="289" height="396" alt="bootstrap-select demo"></a>
</p>

## Demo

You can view a live demo and some examples of how to use the various options [here](https://developer.snapappointments.com/bootstrap-select/examples/).

## Quick start

Bootstrap-select requires jQuery v1.9.1+, Bootstrap’s dropdown.js component, and Bootstrap's CSS. If you're not already using Bootstrap in your project, a precompiled version of the Bootstrap v3.3.7 minimum requirements can be downloaded [here](https://getbootstrap.com/docs/3.3/customize/?id=7830063837006f6fc84f). If using bootstrap-select with Bootstrap v4+, you'll also need Popper.js. For all of Bootstrap v4's requirements, see [Getting started](https://getbootstrap.com/docs/4.1/getting-started/introduction/). A precompiled version of the requirements will be made available in an upcoming release of bootstrap-select.

Several quick start options are available:

- [Download the latest release.](https://github.com/snapappointments/bootstrap-select/archive/v1.13.5.zip)
- Clone the repo: `git clone https://github.com/snapappointments/bootstrap-select.git`
- Install with [npm](https://www.npmjs.com/package/bootstrap-select): `npm install bootstrap-select`
- Install with [yarn](https://yarn.pm/bootstrap-select): `yarn add bootstrap-select`
- Install with [Composer](https://getcomposer.org): `composer require snapappointments/bootstrap-select`
- Install with [NuGet](https://www.nuget.org/packages/bootstrap-select): `Install-Package bootstrap-select`
- Install with [Bower](https://bower.io): `bower install bootstrap-select`
- Install via CDN:

```html
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>

<!-- (Optional) Latest compiled and minified JavaScript translation files -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/i18n/defaults-*.min.js"></script>
```
> The CDN is updated after the release is made public, which means that there is a delay between the publishing of a release and its availability on the CDN.

## Usage

> Bootstrap 4 only works with bootstrap-select v1.13.0+. By default, bootstrap-select automatically detects the version of Bootstrap being used. However, there are some instances where the version detection won't work. See the [documentation](https://developer.snapappointments.com/bootstrap-select/options/#bootstrap-version) for more information.

### Via `selectpicker` class
Add the `selectpicker` class to your select element to use the data-api.
```html
<select class="selectpicker">
  <option>Mustard</option>
  <option>Ketchup</option>
  <option>Barbecue</option>
</select>
```

### Via JavaScript
```js
// To style only selects with the selectpicker class
$('.selectpicker').selectpicker();
```
or
```js
// To style all selects
$('select').selectpicker();
```

If calling bootstrap-select via JavaScript, you will need to wrap your code in a [`$(document).ready()`](https://api.jquery.com/ready/) block or place it at the bottom of the page (after the last instance of bootstrap-select).


Check out the [documentation](https://developer.snapappointments.com/bootstrap-select) for further information.

## Bugs and feature requests

Anyone and everyone is welcome to contribute. **Please take a moment to
review the [guidelines for contributing](CONTRIBUTING.md)**. Make sure you're using the latest version of bootstrap-select before submitting an issue.

* [Bug reports](CONTRIBUTING.md#bug-reports)
* [Feature requests](CONTRIBUTING.md#feature-requests)

## Documentation

Bootstrap-select's documentation, included in this repo in the root directory, is built with MkDocs and hosted at https://developer.snapappointments.com/bootstrap-select. The documentation may also be run locally.

### Running documentation locally

1. If necessary, [install MkDocs](https://www.mkdocs.org/#installation).
2. Install [mkdocs-bootstrap](https://mkdocs.github.io/mkdocs-bootstrap/) using `pip install mkdocs-bootstrap`.
3. From the `/bootstrap-select/docs` directory, run `mkdocs serve` in the command line.
4. Open `http://127.0.0.1:8000/` in your browser, and voilà.

Learn more about using MkDocs by reading its [documentation](https://www.mkdocs.org/).

## Copyright and license

Copyright (C) 2012-2018 [SnapAppointments, LLC](https://snapappointments.com)

Licensed under [the MIT license](LICENSE).
