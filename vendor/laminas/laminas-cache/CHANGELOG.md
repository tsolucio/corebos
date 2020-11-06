# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 2.9.0 - 2019-08-29

### Added

- [zendframework/zend-cache#178](https://github.com/zendframework/zend-cache/pull/178) adds support for PHP 7.3.

### Changed

- [zendframework/zend-cache#186](https://github.com/zendframework/zend-cache/pull/186) replaces
  deprecated `delete()` calls with `del()` in Redis adapter. `delete()`
  function is deprecated since version 5.0.0 and `del()` is available
  since version 2.1.0.

### Deprecated

- Nothing.

### Removed

- [zendframework/zend-cache#178](https://github.com/zendframework/zend-cache/pull/178) removes support for laminas-stdlib v2 releases.

### Fixed

- Nothing. 

## 2.8.3 - 2019-08-28

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-cache#184](https://github.com/zendframework/zend-cache/pull/184) fixes
  an issue with SimpleCacheDecorator where elements were deleted
  after creation. Wrong TTL was set instead of using default value
  from options.

- [zendframework/zend-cache#182](https://github.com/zendframework/zend-cache/pull/182) fixes
  a typo in variable name within the `ExtMongoDbResourceManager::getResource`
  method which prevented using custom db name when using that adapter.

## 2.8.2 - 2018-05-01

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-cache#168](https://github.com/zendframework/zend-cache/pull/168) fixes a typo in a variable name within the `Filesystem::setTags()` method which
  prevented clearing of tags when using that adapter.

## 2.8.1 - 2018-04-26

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-cache#165](https://github.com/zendframework/zend-cache/issues/165) fixes an issue
  with the memcached adapter ensuring that retrieval returns boolean false when
  unable to retrieve the requested item.

## 2.8.0 - 2018-04-24

### Added

- [zendframework/zend-cache#148](https://github.com/zendframework/zend-cache/pull/148) adds support for PHP 7.1 and 7.2.

- [zendframework/zend-cache#46](https://github.com/zendframework/zend-cache/issues/46), [zendframework/zend-cache#155](https://github.com/zendframework/zend-cache/issues/155), and [zendframework/zend-cache#161](https://github.com/zendframework/zend-cache/issues/161) add support for [PSR-6](https://www.php-fig.org/psr/psr-6/) (Caching Interface).
  They provides an implementation of `Psr\Cache\CacheItemPoolInterface` via
  `Laminas\Cache\Psr\CacheItemPool\CacheItemPoolDecorator`, which accepts a
  `Laminas\Cache\Storage\StorageInterface` instance to its constructor, and proxies
  the various PSR-6 methods to it. It also provides a
  `Psr\Cache\CacheItemInterface` implementation via `Laminas\Cache\Psr\CacheItemPool\CacheItem`,
  which provides a value object for both introspecting cache fetch results, as
  well as providing values to cache.

- [zendframework/zend-cache#152](https://github.com/zendframework/zend-cache/pull/152), [zendframework/zend-cache#155](https://github.com/zendframework/zend-cache/pull/155), [zendframework/zend-cache#159](https://github.com/zendframework/zend-cache/pull/159), and [zendframework/zend-cache#161](https://github.com/zendframework/zend-cache/issues/161)
  add an adapter providing [PSR-16](https://www.php-fig.org/psr/psr-16/) (Caching Library Interface) support.
  The new class, `Laminas\Cache\Psr\SimpleCache\SimpleCacheDecorator`, accepts a
  `Laminas\Cache\Storage\StorageInterface` instance to its constructor, and proxies
  the various PSR-16 methods to it.

- [zendframework/zend-cache#154](https://github.com/zendframework/zend-cache/pull/154) adds an ext-mongodb adapter, `Laminas\Cache\Storage\Adapter\ExtMongoDb`.
  You may use the `StorageFactory` to create an instance using either the fully qualified class
  name as the adapter name, or the strings `ext_mongo_db` or `ExtMongoDB` (or most variations
  on case of the latter string). The options it accepts are the same as for the existing
  `Laminas\Cache\Storage\Adapter\MongoDb`, and it provides the same capabilities. The adapter
  requires the mongodb/mongodb package to operate.

- [zendframework/zend-cache#120](https://github.com/zendframework/zend-cache/pull/120) adds the ability to configure alternate file suffixes for both
  cache and tag cache files within the Filesystem adapter. Use the `suffix` and `tag_suffix`
  options to set them; they will default to `dat` and `tag`, respectively.

- [zendframework/zend-cache#79](https://github.com/zendframework/zend-cache/issues/79)
  Add capability for the "lock-on-expire" feature (úsed by Zend Data Cache)

### Changed

- [zendframework/zend-cache#116](https://github.com/zendframework/zend-cache/pull/116) adds docblock method chaining consistency.

### Deprecated

- Nothing.

### Removed

- [zendframework/zend-cache#101](https://github.com/zendframework/zend-cache/pull/101) removes support for PHP 5.5.

- [zendframework/zend-cache#148](https://github.com/zendframework/zend-cache/pull/148) removes support for HHVM.

### Fixed

- [zendframework/zend-cache#151](https://github.com/zendframework/zend-cache/pull/151) adds logic to normalize options before creating the underlying Redis
  resource when using a Redis adapter, fixing issues when using an array with the server and port
  to use for connecting to the server.

- [zendframework/zend-cache#151](https://github.com/zendframework/zend-cache/pull/151) adds logic to prevent changing the underlying resource within Redis adapter instances.

- [zendframework/zend-cache#150](https://github.com/zendframework/zend-cache/pull/150) fixes an issue with how CAS tokens are handled when using the memcached adapter.

- [zendframework/zend-cache#61](https://github.com/zendframework/zend-cache/pull/61) sets the Zend Data Cache minTtl value to 1.

- [zendframework/zend-cache#147](https://github.com/zendframework/zend-cache/pull/147) fixes the Redis extension by ensuring it casts the results of `exists()` to a
  boolean when testing if the storage contains an item.

- [zendframework/zend-cache#146](https://github.com/zendframework/zend-cache/pull/146) fixes several methods to change `@return` annotations to `@throws` where applicable.

- [zendframework/zend-cache#134](https://github.com/zendframework/zend-cache/pull/134) adds a missing import statement for `Traversable` within the `AdapterOptions` class.

- [zendframework/zend-cache#128](https://github.com/zendframework/zend-cache/pull/128)
  Fixed incorrect variable usage in MongoDbResourceManager

## 2.7.2 - 2016-12-16

### Added

- [zendframework/zend-cache#124](https://github.com/zendframework/zend-cache/pull/124)
  New coding standard

### Deprecated

- [zendframework/zend-cache#123](https://github.com/zendframework/zend-cache/pull/123)
  Deprecate capability "expiredRead".
  It's basically providing the same information as staticTtl but from a wrong PoV

### Removed

- Nothing.

### Fixed

- [zendframework/zend-cache#122](https://github.com/zendframework/zend-cache/pull/122)
  Fixed redis doc for lib_options (not lib_option)
- [zendframework/zend-cache#118](https://github.com/zendframework/zend-cache/pull/118)
  fixed redis tests in case running with different server
- [zendframework/zend-cache#119](https://github.com/zendframework/zend-cache/pull/119)
  Redis: Don't call method Redis::info() every time
- [zendframework/zend-cache#113](https://github.com/zendframework/zend-cache/pull/113)
  Travis: Moved coverage reporting to latest env
- [zendframework/zend-cache#114](https://github.com/zendframework/zend-cache/pull/114)
  Travis: removed fast_finish flag
- [zendframework/zend-cache#107](https://github.com/zendframework/zend-cache/issues/107)
  fixed redis server version test in Redis::internalGetMetadata()
- [zendframework/zend-cache#111](https://github.com/zendframework/zend-cache/pull/111)
  Fixed typo in storage adapter doc
- [zendframework/zend-cache#102](https://github.com/zendframework/zend-cache/pull/102)
  filesystem: fixes a lot of possible race conditions

## 2.7.1 - 2016-05-12

### Added

- [zendframework/zend-cache#35](https://github.com/zendframework/zend-cache/pull/35)
  Added benchmarks using PHPBench

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-cache#76](https://github.com/zendframework/zend-cache/pull/76)
  LaminasServer: fixed return null on missing item
- [zendframework/zend-cache#88](https://github.com/zendframework/zend-cache/issues/88)
  Redis: fixed segfault on storing NULL and fixed supported datatypes capabilities
- [zendframework/zend-cache#95](https://github.com/zendframework/zend-cache/issues/95)
  don't try to unserialize missing items
- [zendframework/zend-cache#66](https://github.com/zendframework/zend-cache/issues/66)
  fixed Memcached::internalSetItems in PHP-7 by reducing variables by reference
- [zendframework/zend-cache#57](https://github.com/zendframework/zend-cache/pull/57)
  Memcached: HHVM compatibility and reduced duplicated code
- [zendframework/zend-cache#91](https://github.com/zendframework/zend-cache/pull/91)
  fixed that order of adapter options may cause exception
- [zendframework/zend-cache#98](https://github.com/zendframework/zend-cache/pull/98) updates the plugin
  manager alias list to ensure all adapter name permutations commonly used are
  accepted.

## 2.7.0 - 2016-04-12

### Added

- [zendframework/zend-cache#59](https://github.com/zendframework/zend-cache/pull/59)
  XCache >= 3.1.0 works in CLI mode
- [zendframework/zend-cache#23](https://github.com/zendframework/zend-cache/issues/23)
  [zendframework/zend-cache#47](https://github.com/zendframework/zend-cache/issues/47)
  Added an Apcu storage adapter as future replacement for Apc
- [zendframework/zend-cache#63](https://github.com/zendframework/zend-cache/pull/63)
  Implemented ClearByNamespaceInterface in Stoage\Adapter\Redis
- [zendframework/zend-cache#94](https://github.com/zendframework/zend-cache/pull/94) adds factories for
  each of the `PatternPluginManager`, `AdapterPluginManager`, and storage
  `PluginManager`.
- [zendframework/zend-cache#94](https://github.com/zendframework/zend-cache/pull/94) exposes the package
  as a standalone config-provider / Laminas component, by adding:
  - `Laminas\Cache\ConfigProvider`, which enables the
    `StorageCacheAbstractServiceFactory`, and maps factories for all plugin
    managers.
  - `Laminas\Cache\Module`, which does the same, for laminas-mvc contexts.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-cache#44](https://github.com/zendframework/zend-cache/issues/44)
  Filesystem: fixed race condition in method clearByTags
- [zendframework/zend-cache#59](https://github.com/zendframework/zend-cache/pull/59)
  XCache: fixed broken internalSetItem() with empty namespace
- [zendframework/zend-cache#58](https://github.com/zendframework/zend-cache/issues/58)
  XCache: Fatal error storing objects
- [zendframework/zend-cache#94](https://github.com/zendframework/zend-cache/pull/94) updates the
  `PatternPluginManager` to accept `$options` to `get()` and `build()`, cast
  them to a `PatternOptions` instance, and inject them into the generated plugin
  instance. This change allows better standalone usage of the plugin manager.
- [zendframework/zend-cache#94](https://github.com/zendframework/zend-cache/pull/94) updates the
  `StorageCacheFactory` and `StorageCacheAbstractServiceFactory` to seed the
  `StorageFactory` with the storage plugin manager and/or adapter plugin manager
  as pulled from the provided container, if present. This change enables re-use
  of pre-configured plugin managers (e.g., those seeded with custom plugins
  and/or adapters).

## 2.6.1 - 2016-02-12

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-cache#73](https://github.com/zendframework/zend-cache/pull/73) fixes how the
  `EventManager` instance is lazy-instantiated in
  `Laminas\Cache\Storage\Adapter\AbstractAdapter::getEventManager()`. In 2.6.0, it
  was using the v3-specific syntax; it now uses syntax compatible with both v2
  and v3.

## 2.6.0 - 2016-02-11

### Added

- [zendframework/zend-cache#70](https://github.com/zendframework/zend-cache/pull/70) adds, revises, and
  publishes the documentation to https://docs.laminas.dev/laminas-cache/

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-cache#22](https://github.com/zendframework/zend-cache/pull/22),
  [zendframework/zend-cache#64](https://github.com/zendframework/zend-cache/pull/64),
  [zendframework/zend-cache#68](https://github.com/zendframework/zend-cache/pull/68), and
  [zendframework/zend-cache#69](https://github.com/zendframework/zend-cache/pull/69) update the
  component to be forwards-compatible with laminas-eventmanager,
  laminas-servicemanager, and laminas-stdlib v3.
- [zendframework/zend-cache#31](https://github.com/zendframework/zend-cache/issues/31)
  Check Documentation Code Blocks
- [zendframework/zend-cache#53](https://github.com/zendframework/zend-cache/pull/53)
  fixed seg fault in redis adapter on PHP 7
- [zendframework/zend-cache#50](https://github.com/zendframework/zend-cache/issues/50)
  fixed APC tests not running on travis-ci since apcu-5 was released
- [zendframework/zend-cache#36](https://github.com/zendframework/zend-cache/pull/36)
  fixed AbstractAdapter::internalDecrementItems
- [zendframework/zend-cache#38](https://github.com/zendframework/zend-cache/pull/38)
  better test coverage of AbstractAdapter
- [zendframework/zend-cache#45](https://github.com/zendframework/zend-cache/pull/45)
  removed unused internal function Filesystem::readInfoFile
- [zendframework/zend-cache#25](https://github.com/zendframework/zend-cache/pull/25)
  MongoDd: fixed expiration support and removed duplicated tests
- [zendframework/zend-cache#40](https://github.com/zendframework/zend-cache/pull/40)
  Fixed TTL support of `Redis::addItem`
- [zendframework/zend-cache#18](https://github.com/zendframework/zend-cache/issues/18)
  Fixed `Redis::getCapabilities` and `RedisResourceManager::getMajorVersion`
  if resource wasn't initialized before

## 2.5.3 - 2015-09-15

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-cache#15](https://github.com/zendframework/zend-cache/pull/15) fixes an issue
  observed on HHVM when merging a list of memcached servers to add to the
  storage resource.
- [zendframework/zend-cache#17](https://github.com/zendframework/zend-cache/pull/17) Composer: moved
  `laminas/laminas-serializer` from `require` to `require-dev` as using the
  serializer is optional.
- A fix was provided for [ZF2015-07](https://getlaminas.org/security/advisory/ZF2015-07),
  ensuring that any directories or files created by the component use umask 0002
  in order to prevent arbitrary local execution and/or local privilege
  escalation.

## 2.5.2 - 2015-07-16

### Added

- [zendframework/zend-cache#10](https://github.com/zendframework/zend-cache/pull/10) adds TTL support
  for the Redis adapter.
- [zendframework/zend-cache#6](https://github.com/zendframework/zend-cache/pull/6) adds more suggestions
  to the `composer.json` for PHP extensions supported by storage adapters.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zendframework/zend-cache#9](https://github.com/zendframework/zend-cache/pull/9) fixes an issue when
  connecting to a Redis instance with the `persistent_id` option.
