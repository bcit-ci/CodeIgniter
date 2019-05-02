# OwlCarousel 2 Roadmap

You can discuss the roadmap here: [#1756](https://github.com/OwlCarousel2/OwlCarousel2/issues/1756).

## 2.3 - bugfixes, repo migration, minor features - current version

 - [x] clean up contributor guides
 - [x] Work through a bunch of PRs
    - [x] Various spelling and code style fixes: #1785, #1858, #1856, #1814, #1876, #1838, #1424
    - [x] [#1883](https://github.com/OwlCarousel2/OwlCarousel2/pull/1883) Fix infinite loop
    - [x] [#1900](https://github.com/OwlCarousel2/OwlCarousel2/pull/1900) Add repository info to package.json
    - [x] [#1770](https://github.com/OwlCarousel2/OwlCarousel2/pull/1770) Allow package file to expose CSS
    - [x] [#1077](https://github.com/OwlCarousel2/OwlCarousel2/pull/1077) .center class hangs around when responsive options
    - [x] [#1978](https://github.com/OwlCarousel2/OwlCarousel2/pull/1978) - Fix empty child auto width
    - [x] [#1942](https://github.com/OwlCarousel2/OwlCarousel2/pull/1942) - Improving autoplay behavior
    - [x] [#1915](https://github.com/OwlCarousel2/OwlCarousel2/pull/1915) - Fixing: [#1750](https://github.com/OwlCarousel2/OwlCarousel2/issues/1750) Passive event listeners, chrome and touch events
 - [x] [#1704](https://github.com/OwlCarousel2/OwlCarousel2/issues/1704) - viewport width detection
 - [ ] [#1717](https://github.com/OwlCarousel2/OwlCarousel2/issues/1717) - keyboard control
 - [ ] update progress in [#1538](https://github.com/OwlCarousel2/OwlCarousel2/issues/1538)

## 2.4 - finish up build pipeline, docs

 - [ ] move repo to company account (https://github.com/medienpark)
 - [ ] [#1330](https://github.com/OwlCarousel2/OwlCarousel2/issues/1330) - finish moving to gulp
 - [ ] ditto for moving to assemble for docs
 - [ ] [#1666](https://github.com/OwlCarousel2/OwlCarousel2/issues/1666) - RTL center mode
 - [ ] [#1613](https://github.com/OwlCarousel2/OwlCarousel2/issues/1613) - generic plugin integration
 - [ ] [#1602](https://github.com/OwlCarousel2/OwlCarousel2/issues/1602) - CSS transitions fail except for default

## 2.5 - cloning & worker cleanup

 - [ ] worker cleanup
 - [ ] clone computation fix (and provide consistent access to slides)
 - [ ] [#1575](https://github.com/OwlCarousel2/OwlCarousel2/issues/1575) & [#1621](https://github.com/OwlCarousel2/OwlCarousel2/issues/1621) - AutoHeight fixes
 - [ ] [#1511](https://github.com/OwlCarousel2/OwlCarousel2/issues/1511) - do not disable nav when center = true & length == items

## 2.6 - cleanup, code style, repo cleanup

 - [ ] clean up code ToDos
 - [ ] fix code style
 - [ ] check whether we want to support velocity.js (at least optionally)
 - [ ] close not-yet-tagged issues older than 8 month
 - [ ] [#1518](https://github.com/OwlCarousel2/OwlCarousel2/issues/1518) - slide change event issues (not cancelling events etc.)
 - [ ] [#1563](https://github.com/OwlCarousel2/OwlCarousel2/issues/1563) - slide offset on last if loop = false
 - [ ] [#1633](https://github.com/OwlCarousel2/OwlCarousel2/issues/1633) & [#1627](https://github.com/OwlCarousel2/OwlCarousel2/issues/1627) - (merged items) swipe/autoplay (mostly testing whether the worker/clone fixes in 2.5 solved this)

## 2.7 - bugfixes & final, "LTS" 2.x release

 - [ ] [#1647](https://github.com/OwlCarousel2/OwlCarousel2/issues/1647) - 1px from prev. slide on current
 - [ ] [#1523](https://github.com/OwlCarousel2/OwlCarousel2/issues/1523) - autoplay vs. video autoplay issue
 - [ ] [#1343](https://github.com/OwlCarousel2/OwlCarousel2/issues/1343) - timeout per slide

## 3.0 - Typescript, additional plugins, breaking changes

 - [ ] TypeScript refactoring
 - [ ] remove css-mimicking settings (such as margins) and use CSS instead
 - [ ] overlay plugin (to support overlay transitions etc.)
