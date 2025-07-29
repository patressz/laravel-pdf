# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## v0.6.0 - 2025-07-29

* feat: add `fromUrl()` method to `PdfBuilder` (d9928c43093dad748cd44bb19ff795fd07d69e6c)
* docs: add docs about `fromUrl()` method (6e75ab06b66ef6c144748542a158684b86f6b38d)

**Full Changelog**: https://github.com/patressz/laravel-pdf/compare/v0.5.1...v0.6.0

## v0.5.1 - 2025-07-28

* refactor: add `PLAYWRIGHT_BROWSERS_PATH` env variable to `Process` to search for browsers in that env when is defined (d7eb6151c5e358c1df5b6f10f26e87b11d685af2)

**Full Changelog**: https://github.com/patressz/laravel-pdf/compare/v0.5.0...v0.5.1

## v0.5.0 - 2025-07-25

* fix: ensure executable path is used when launching Chromium browser (8776db205cb706011c4a3ccea637b5791de514e7)

**Full Changelog**: https://github.com/patressz/laravel-pdf/compare/v0.4.0...v0.5.0

## v0.4.0 - 2025-07-24

* refactor: extends `FakePdfBuilder` from `PdfBuilder` and remove duplicates method keep only necessary (6d6315ce7cfc72e4117220f4d9fc1b4f07d72f38)
* feat: add `Macroable` trait to `PdfBuilder` + update docs (a306b2a79b10e07cf7a981ced92999f69e664e59)

**Full Changelog**: https://github.com/patressz/laravel-pdf/compare/v0.3.0...v0.4.0

## 0.3.0 - 2025-07-24

* refactor: simplify assertion methods in `Pdf` facade and `FakePdfBuilder` and fix `assertView()` method to check if the view was set (bffb904b1bb3ff8bfca5c0105107ca5d1962d462)

**Full Changelog**: https://github.com/patressz/laravel-pdf/compare/v.0.2.0...v0.3.0

## v0.2.0 - 2025-07-24

* feat: add `Pdf` facade (f8f1517f3f4a452c1951b2e227ddfb071ba213e6)
* feat: add `FakePdfBuilder` and `FakePdfBuilderTest` to test PDFs (49f8a35eaaaba6916549350e5d263895bbb74584)(c6f00b47c65b38425bce010e692a355de1455b7c)
* dosc: add section about testing to `REAMDE.md` (3ed0f15d6e2f3c932f0b6a3edf5521131b30df79)

**Full Changelog**: https://github.com/patressz/laravel-pdf/compare/v0.1.0...v.0.2.0

## Initial release - v.0.1.0 - 2025-07-23

**Initial release**

- Initial release supporting generating an PDF using playwright with chromium
