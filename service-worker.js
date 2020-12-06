try {
  self['workbox:core:5.0.0'] && _();
} catch (e) {}

/*
  Copyright 2019 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
/**
 * Force a service worker to activate immediately, instead of
 * [waiting](https://developers.google.com/web/fundamentals/primers/service-workers/lifecycle#waiting)
 * for existing clients to close.
 *
 * @memberof module:workbox-core
 */

function skipWaiting() {
  // We need to explicitly call `self.skipWaiting()` here because we're
  // shadowing `skipWaiting` with this local function.
  self.addEventListener('install', () => self.skipWaiting());
}

/*
  Copyright 2019 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
/**
 * Claim any currently available clients once the service worker
 * becomes active. This is normally used in conjunction with `skipWaiting()`.
 *
 * @memberof module:workbox-core
 */

function clientsClaim() {
  self.addEventListener('activate', () => self.clients.claim());
}

/*
  Copyright 2018 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
const _cacheNameDetails = {
  googleAnalytics: 'googleAnalytics',
  precache: 'precache-v2',
  prefix: 'workbox',
  runtime: 'runtime',
  suffix: typeof registration !== 'undefined' ? registration.scope : ''
};

const _createCacheName = cacheName => {
  return [_cacheNameDetails.prefix, cacheName, _cacheNameDetails.suffix].filter(value => value && value.length > 0).join('-');
};

const eachCacheNameDetail = fn => {
  for (const key of Object.keys(_cacheNameDetails)) {
    fn(key);
  }
};

const cacheNames = {
  updateDetails: details => {
    eachCacheNameDetail(key => {
      if (typeof details[key] === 'string') {
        _cacheNameDetails[key] = details[key];
      }
    });
  },
  getGoogleAnalyticsName: userCacheName => {
    return userCacheName || _createCacheName(_cacheNameDetails.googleAnalytics);
  },
  getPrecacheName: userCacheName => {
    return userCacheName || _createCacheName(_cacheNameDetails.precache);
  },
  getPrefix: () => {
    return _cacheNameDetails.prefix;
  },
  getRuntimeName: userCacheName => {
    return userCacheName || _createCacheName(_cacheNameDetails.runtime);
  },
  getSuffix: () => {
    return _cacheNameDetails.suffix;
  }
};

/*
  Copyright 2018 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/

const getFriendlyURL = url => {
  const urlObj = new URL(String(url), location.href);

  if (urlObj.origin === location.origin) {
    return urlObj.pathname;
  }

  return urlObj.href;
};

/*
  Copyright 2019 Google LLC
  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
const logger =  (() => {
  // Don't overwrite this value if it's already set.
  // See https://github.com/GoogleChrome/workbox/pull/2284#issuecomment-560470923
  if (!('__WB_DISABLE_DEV_LOGS' in self)) {
    self.__WB_DISABLE_DEV_LOGS = false;
  }

  let inGroup = false;
  const methodToColorMap = {
    debug: `#7f8c8d`,
    log: `#2ecc71`,
    warn: `#f39c12`,
    error: `#c0392b`,
    groupCollapsed: `#3498db`,
    groupEnd: null
  };

  const print = function (method, args) {
    if (self.__WB_DISABLE_DEV_LOGS) {
      return;
    }

    if (method === 'groupCollapsed') {
      // Safari doesn't print all console.groupCollapsed() arguments:
      // https://bugs.webkit.org/show_bug.cgi?id=182754
      if (/^((?!chrome|android).)*safari/i.test(navigator.userAgent)) {
        console[method](...args);
        return;
      }
    }

    const styles = [`background: ${methodToColorMap[method]}`, `border-radius: 0.5em`, `color: white`, `font-weight: bold`, `padding: 2px 0.5em`]; // When in a group, the workbox prefix is not displayed.

    const logPrefix = inGroup ? [] : ['%cworkbox', styles.join(';')];
    console[method](...logPrefix, ...args);

    if (method === 'groupCollapsed') {
      inGroup = true;
    }

    if (method === 'groupEnd') {
      inGroup = false;
    }
  };

  const api = {};
  const loggerMethods = Object.keys(methodToColorMap);

  for (const key of loggerMethods) {
    const method = key;

    api[method] = (...args) => {
      print(method, args);
    };
  }

  return api;
})();

/*
  Copyright 2018 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
const messages = {
  'invalid-value': ({
    paramName,
    validValueDescription,
    value
  }) => {
    if (!paramName || !validValueDescription) {
      throw new Error(`Unexpected input to 'invalid-value' error.`);
    }

    return `The '${paramName}' parameter was given a value with an ` + `unexpected value. ${validValueDescription} Received a value of ` + `${JSON.stringify(value)}.`;
  },
  'not-in-sw': ({
    moduleName
  }) => {
    if (!moduleName) {
      throw new Error(`Unexpected input to 'not-in-sw' error.`);
    }

    return `The '${moduleName}' must be used in a service worker.`;
  },
  'not-an-array': ({
    moduleName,
    className,
    funcName,
    paramName
  }) => {
    if (!moduleName || !className || !funcName || !paramName) {
      throw new Error(`Unexpected input to 'not-an-array' error.`);
    }

    return `The parameter '${paramName}' passed into ` + `'${moduleName}.${className}.${funcName}()' must be an array.`;
  },
  'incorrect-type': ({
    expectedType,
    paramName,
    moduleName,
    className,
    funcName
  }) => {
    if (!expectedType || !paramName || !moduleName || !funcName) {
      throw new Error(`Unexpected input to 'incorrect-type' error.`);
    }

    return `The parameter '${paramName}' passed into ` + `'${moduleName}.${className ? className + '.' : ''}` + `${funcName}()' must be of type ${expectedType}.`;
  },
  'incorrect-class': ({
    expectedClass,
    paramName,
    moduleName,
    className,
    funcName,
    isReturnValueProblem
  }) => {
    if (!expectedClass || !moduleName || !funcName) {
      throw new Error(`Unexpected input to 'incorrect-class' error.`);
    }

    if (isReturnValueProblem) {
      return `The return value from ` + `'${moduleName}.${className ? className + '.' : ''}${funcName}()' ` + `must be an instance of class ${expectedClass.name}.`;
    }

    return `The parameter '${paramName}' passed into ` + `'${moduleName}.${className ? className + '.' : ''}${funcName}()' ` + `must be an instance of class ${expectedClass.name}.`;
  },
  'missing-a-method': ({
    expectedMethod,
    paramName,
    moduleName,
    className,
    funcName
  }) => {
    if (!expectedMethod || !paramName || !moduleName || !className || !funcName) {
      throw new Error(`Unexpected input to 'missing-a-method' error.`);
    }

    return `${moduleName}.${className}.${funcName}() expected the ` + `'${paramName}' parameter to expose a '${expectedMethod}' method.`;
  },
  'add-to-cache-list-unexpected-type': ({
    entry
  }) => {
    return `An unexpected entry was passed to ` + `'workbox-precaching.PrecacheController.addToCacheList()' The entry ` + `'${JSON.stringify(entry)}' isn't supported. You must supply an array of ` + `strings with one or more characters, objects with a url property or ` + `Request objects.`;
  },
  'add-to-cache-list-conflicting-entries': ({
    firstEntry,
    secondEntry
  }) => {
    if (!firstEntry || !secondEntry) {
      throw new Error(`Unexpected input to ` + `'add-to-cache-list-duplicate-entries' error.`);
    }

    return `Two of the entries passed to ` + `'workbox-precaching.PrecacheController.addToCacheList()' had the URL ` + `${firstEntry._entryId} but different revision details. Workbox is ` + `is unable to cache and version the asset correctly. Please remove one ` + `of the entries.`;
  },
  'plugin-error-request-will-fetch': ({
    thrownError
  }) => {
    if (!thrownError) {
      throw new Error(`Unexpected input to ` + `'plugin-error-request-will-fetch', error.`);
    }

    return `An error was thrown by a plugins 'requestWillFetch()' method. ` + `The thrown error message was: '${thrownError.message}'.`;
  },
  'invalid-cache-name': ({
    cacheNameId,
    value
  }) => {
    if (!cacheNameId) {
      throw new Error(`Expected a 'cacheNameId' for error 'invalid-cache-name'`);
    }

    return `You must provide a name containing at least one character for ` + `setCacheDetails({${cacheNameId}: '...'}). Received a value of ` + `'${JSON.stringify(value)}'`;
  },
  'unregister-route-but-not-found-with-method': ({
    method
  }) => {
    if (!method) {
      throw new Error(`Unexpected input to ` + `'unregister-route-but-not-found-with-method' error.`);
    }

    return `The route you're trying to unregister was not  previously ` + `registered for the method type '${method}'.`;
  },
  'unregister-route-route-not-registered': () => {
    return `The route you're trying to unregister was not previously ` + `registered.`;
  },
  'queue-replay-failed': ({
    name
  }) => {
    return `Replaying the background sync queue '${name}' failed.`;
  },
  'duplicate-queue-name': ({
    name
  }) => {
    return `The Queue name '${name}' is already being used. ` + `All instances of backgroundSync.Queue must be given unique names.`;
  },
  'expired-test-without-max-age': ({
    methodName,
    paramName
  }) => {
    return `The '${methodName}()' method can only be used when the ` + `'${paramName}' is used in the constructor.`;
  },
  'unsupported-route-type': ({
    moduleName,
    className,
    funcName,
    paramName
  }) => {
    return `The supplied '${paramName}' parameter was an unsupported type. ` + `Please check the docs for ${moduleName}.${className}.${funcName} for ` + `valid input types.`;
  },
  'not-array-of-class': ({
    value,
    expectedClass,
    moduleName,
    className,
    funcName,
    paramName
  }) => {
    return `The supplied '${paramName}' parameter must be an array of ` + `'${expectedClass}' objects. Received '${JSON.stringify(value)},'. ` + `Please check the call to ${moduleName}.${className}.${funcName}() ` + `to fix the issue.`;
  },
  'max-entries-or-age-required': ({
    moduleName,
    className,
    funcName
  }) => {
    return `You must define either config.maxEntries or config.maxAgeSeconds` + `in ${moduleName}.${className}.${funcName}`;
  },
  'statuses-or-headers-required': ({
    moduleName,
    className,
    funcName
  }) => {
    return `You must define either config.statuses or config.headers` + `in ${moduleName}.${className}.${funcName}`;
  },
  'invalid-string': ({
    moduleName,
    funcName,
    paramName
  }) => {
    if (!paramName || !moduleName || !funcName) {
      throw new Error(`Unexpected input to 'invalid-string' error.`);
    }

    return `When using strings, the '${paramName}' parameter must start with ` + `'http' (for cross-origin matches) or '/' (for same-origin matches). ` + `Please see the docs for ${moduleName}.${funcName}() for ` + `more info.`;
  },
  'channel-name-required': () => {
    return `You must provide a channelName to construct a ` + `BroadcastCacheUpdate instance.`;
  },
  'invalid-responses-are-same-args': () => {
    return `The arguments passed into responsesAreSame() appear to be ` + `invalid. Please ensure valid Responses are used.`;
  },
  'expire-custom-caches-only': () => {
    return `You must provide a 'cacheName' property when using the ` + `expiration plugin with a runtime caching strategy.`;
  },
  'unit-must-be-bytes': ({
    normalizedRangeHeader
  }) => {
    if (!normalizedRangeHeader) {
      throw new Error(`Unexpected input to 'unit-must-be-bytes' error.`);
    }

    return `The 'unit' portion of the Range header must be set to 'bytes'. ` + `The Range header provided was "${normalizedRangeHeader}"`;
  },
  'single-range-only': ({
    normalizedRangeHeader
  }) => {
    if (!normalizedRangeHeader) {
      throw new Error(`Unexpected input to 'single-range-only' error.`);
    }

    return `Multiple ranges are not supported. Please use a  single start ` + `value, and optional end value. The Range header provided was ` + `"${normalizedRangeHeader}"`;
  },
  'invalid-range-values': ({
    normalizedRangeHeader
  }) => {
    if (!normalizedRangeHeader) {
      throw new Error(`Unexpected input to 'invalid-range-values' error.`);
    }

    return `The Range header is missing both start and end values. At least ` + `one of those values is needed. The Range header provided was ` + `"${normalizedRangeHeader}"`;
  },
  'no-range-header': () => {
    return `No Range header was found in the Request provided.`;
  },
  'range-not-satisfiable': ({
    size,
    start,
    end
  }) => {
    return `The start (${start}) and end (${end}) values in the Range are ` + `not satisfiable by the cached response, which is ${size} bytes.`;
  },
  'attempt-to-cache-non-get-request': ({
    url,
    method
  }) => {
    return `Unable to cache '${url}' because it is a '${method}' request and ` + `only 'GET' requests can be cached.`;
  },
  'cache-put-with-no-response': ({
    url
  }) => {
    return `There was an attempt to cache '${url}' but the response was not ` + `defined.`;
  },
  'no-response': ({
    url,
    error
  }) => {
    let message = `The strategy could not generate a response for '${url}'.`;

    if (error) {
      message += ` The underlying error is ${error}.`;
    }

    return message;
  },
  'bad-precaching-response': ({
    url,
    status
  }) => {
    return `The precaching request for '${url}' failed with an HTTP ` + `status of ${status}.`;
  },
  'non-precached-url': ({
    url
  }) => {
    return `createHandlerBoundToURL('${url}') was called, but that URL is ` + `not precached. Please pass in a URL that is precached instead.`;
  },
  'add-to-cache-list-conflicting-integrities': ({
    url
  }) => {
    return `Two of the entries passed to ` + `'workbox-precaching.PrecacheController.addToCacheList()' had the URL ` + `${url} with different integrity values. Please remove one of them.`;
  },
  'missing-precache-entry': ({
    cacheName,
    url
  }) => {
    return `Unable to find a precached response in ${cacheName} for ${url}.`;
  }
};

/*
  Copyright 2018 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/

const generatorFunction = (code, details = {}) => {
  const message = messages[code];

  if (!message) {
    throw new Error(`Unable to find message for code '${code}'.`);
  }

  return message(details);
};

const messageGenerator =  generatorFunction;

/*
  Copyright 2018 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
/**
 * Workbox errors should be thrown with this class.
 * This allows use to ensure the type easily in tests,
 * helps developers identify errors from workbox
 * easily and allows use to optimise error
 * messages correctly.
 *
 * @private
 */

class WorkboxError extends Error {
  /**
   *
   * @param {string} errorCode The error code that
   * identifies this particular error.
   * @param {Object=} details Any relevant arguments
   * that will help developers identify issues should
   * be added as a key on the context object.
   */
  constructor(errorCode, details) {
    let message = messageGenerator(errorCode, details);
    super(message);
    this.name = errorCode;
    this.details = details;
  }

}

/*
  Copyright 2018 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
/*
 * This method returns true if the current context is a service worker.
 */

const isSWEnv = moduleName => {
  if (!('ServiceWorkerGlobalScope' in self)) {
    throw new WorkboxError('not-in-sw', {
      moduleName
    });
  }
};
/*
 * This method throws if the supplied value is not an array.
 * The destructed values are required to produce a meaningful error for users.
 * The destructed and restructured object is so it's clear what is
 * needed.
 */


const isArray = (value, details) => {
  if (!Array.isArray(value)) {
    throw new WorkboxError('not-an-array', details);
  }
};

const hasMethod = (object, expectedMethod, details) => {
  const type = typeof object[expectedMethod];

  if (type !== 'function') {
    details['expectedMethod'] = expectedMethod;
    throw new WorkboxError('missing-a-method', details);
  }
};

const isType = (object, expectedType, details) => {
  if (typeof object !== expectedType) {
    details['expectedType'] = expectedType;
    throw new WorkboxError('incorrect-type', details);
  }
};

const isInstance = (object, expectedClass, details) => {
  if (!(object instanceof expectedClass)) {
    details['expectedClass'] = expectedClass;
    throw new WorkboxError('incorrect-class', details);
  }
};

const isOneOf = (value, validValues, details) => {
  if (!validValues.includes(value)) {
    details['validValueDescription'] = `Valid values are ${JSON.stringify(validValues)}.`;
    throw new WorkboxError('invalid-value', details);
  }
};

const isArrayOfClass = (value, expectedClass, details) => {
  const error = new WorkboxError('not-array-of-class', details);

  if (!Array.isArray(value)) {
    throw error;
  }

  for (let item of value) {
    if (!(item instanceof expectedClass)) {
      throw error;
    }
  }
};

const finalAssertExports =  {
  hasMethod,
  isArray,
  isInstance,
  isOneOf,
  isSWEnv,
  isType,
  isArrayOfClass
};

/*
  Copyright 2018 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/

const quotaErrorCallbacks = new Set();

/*
  Copyright 2018 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
/**
 * Runs all of the callback functions, one at a time sequentially, in the order
 * in which they were registered.
 *
 * @memberof module:workbox-core
 * @private
 */

async function executeQuotaErrorCallbacks() {
  {
    logger.log(`About to run ${quotaErrorCallbacks.size} ` + `callbacks to clean up caches.`);
  }

  for (const callback of quotaErrorCallbacks) {
    await callback();

    {
      logger.log(callback, 'is complete.');
    }
  }

  {
    logger.log('Finished running callbacks.');
  }
}

/*
  Copyright 2018 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
const pluginUtils = {
  filter: (plugins, callbackName) => {
    return plugins.filter(plugin => callbackName in plugin);
  }
};

/*
  Copyright 2018 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
/**
 * Wrapper around cache.put().
 *
 * Will call `cacheDidUpdate` on plugins if the cache was updated, using
 * `matchOptions` when determining what the old entry is.
 *
 * @param {Object} options
 * @param {string} options.cacheName
 * @param {Request} options.request
 * @param {Response} options.response
 * @param {Event} [options.event]
 * @param {Array<Object>} [options.plugins=[]]
 * @param {Object} [options.matchOptions]
 *
 * @private
 * @memberof module:workbox-core
 */

const putWrapper = async ({
  cacheName,
  request,
  response,
  event,
  plugins = [],
  matchOptions
}) => {
  {
    if (request.method && request.method !== 'GET') {
      throw new WorkboxError('attempt-to-cache-non-get-request', {
        url: getFriendlyURL(request.url),
        method: request.method
      });
    }
  }

  const effectiveRequest = await _getEffectiveRequest({
    plugins,
    request,
    mode: 'write'
  });

  if (!response) {
    {
      logger.error(`Cannot cache non-existent response for ` + `'${getFriendlyURL(effectiveRequest.url)}'.`);
    }

    throw new WorkboxError('cache-put-with-no-response', {
      url: getFriendlyURL(effectiveRequest.url)
    });
  }

  let responseToCache = await _isResponseSafeToCache({
    event,
    plugins,
    response,
    request: effectiveRequest
  });

  if (!responseToCache) {
    {
      logger.debug(`Response '${getFriendlyURL(effectiveRequest.url)}' will ` + `not be cached.`, responseToCache);
    }

    return;
  }

  const cache = await self.caches.open(cacheName);
  const updatePlugins = pluginUtils.filter(plugins, "cacheDidUpdate"
  /* CACHE_DID_UPDATE */
  );
  let oldResponse = updatePlugins.length > 0 ? await matchWrapper({
    cacheName,
    matchOptions,
    request: effectiveRequest
  }) : null;

  {
    logger.debug(`Updating the '${cacheName}' cache with a new Response for ` + `${getFriendlyURL(effectiveRequest.url)}.`);
  }

  try {
    await cache.put(effectiveRequest, responseToCache);
  } catch (error) {
    // See https://developer.mozilla.org/en-US/docs/Web/API/DOMException#exception-QuotaExceededError
    if (error.name === 'QuotaExceededError') {
      await executeQuotaErrorCallbacks();
    }

    throw error;
  }

  for (let plugin of updatePlugins) {
    await plugin["cacheDidUpdate"
    /* CACHE_DID_UPDATE */
    ].call(plugin, {
      cacheName,
      event,
      oldResponse,
      newResponse: responseToCache,
      request: effectiveRequest
    });
  }
};
/**
 * This is a wrapper around cache.match().
 *
 * @param {Object} options
 * @param {string} options.cacheName Name of the cache to match against.
 * @param {Request} options.request The Request that will be used to look up
 *     cache entries.
 * @param {Event} [options.event] The event that prompted the action.
 * @param {Object} [options.matchOptions] Options passed to cache.match().
 * @param {Array<Object>} [options.plugins=[]] Array of plugins.
 * @return {Response} A cached response if available.
 *
 * @private
 * @memberof module:workbox-core
 */


const matchWrapper = async ({
  cacheName,
  request,
  event,
  matchOptions,
  plugins = []
}) => {
  const cache = await self.caches.open(cacheName);
  const effectiveRequest = await _getEffectiveRequest({
    plugins,
    request,
    mode: 'read'
  });
  let cachedResponse = await cache.match(effectiveRequest, matchOptions);

  {
    if (cachedResponse) {
      logger.debug(`Found a cached response in '${cacheName}'.`);
    } else {
      logger.debug(`No cached response found in '${cacheName}'.`);
    }
  }

  for (const plugin of plugins) {
    if ("cachedResponseWillBeUsed"
    /* CACHED_RESPONSE_WILL_BE_USED */
    in plugin) {
      const pluginMethod = plugin["cachedResponseWillBeUsed"
      /* CACHED_RESPONSE_WILL_BE_USED */
      ];
      cachedResponse = await pluginMethod.call(plugin, {
        cacheName,
        event,
        matchOptions,
        cachedResponse,
        request: effectiveRequest
      });

      {
        if (cachedResponse) {
          finalAssertExports.isInstance(cachedResponse, Response, {
            moduleName: 'Plugin',
            funcName: "cachedResponseWillBeUsed"
            /* CACHED_RESPONSE_WILL_BE_USED */
            ,
            isReturnValueProblem: true
          });
        }
      }
    }
  }

  return cachedResponse;
};
/**
 * This method will call cacheWillUpdate on the available plugins (or use
 * status === 200) to determine if the Response is safe and valid to cache.
 *
 * @param {Object} options
 * @param {Request} options.request
 * @param {Response} options.response
 * @param {Event} [options.event]
 * @param {Array<Object>} [options.plugins=[]]
 * @return {Promise<Response>}
 *
 * @private
 * @memberof module:workbox-core
 */


const _isResponseSafeToCache = async ({
  request,
  response,
  event,
  plugins = []
}) => {
  let responseToCache = response;
  let pluginsUsed = false;

  for (let plugin of plugins) {
    if ("cacheWillUpdate"
    /* CACHE_WILL_UPDATE */
    in plugin) {
      pluginsUsed = true;
      const pluginMethod = plugin["cacheWillUpdate"
      /* CACHE_WILL_UPDATE */
      ];
      responseToCache = await pluginMethod.call(plugin, {
        request,
        response: responseToCache,
        event
      });

      {
        if (responseToCache) {
          finalAssertExports.isInstance(responseToCache, Response, {
            moduleName: 'Plugin',
            funcName: "cacheWillUpdate"
            /* CACHE_WILL_UPDATE */
            ,
            isReturnValueProblem: true
          });
        }
      }

      if (!responseToCache) {
        break;
      }
    }
  }

  if (!pluginsUsed) {
    {
      if (responseToCache) {
        if (responseToCache.status !== 200) {
          if (responseToCache.status === 0) {
            logger.warn(`The response for '${request.url}' is an opaque ` + `response. The caching strategy that you're using will not ` + `cache opaque responses by default.`);
          } else {
            logger.debug(`The response for '${request.url}' returned ` + `a status code of '${response.status}' and won't be cached as a ` + `result.`);
          }
        }
      }
    }

    responseToCache = responseToCache && responseToCache.status === 200 ? responseToCache : undefined;
  }

  return responseToCache ? responseToCache : null;
};
/**
 * Checks the list of plugins for the cacheKeyWillBeUsed callback, and
 * executes any of those callbacks found in sequence. The final `Request` object
 * returned by the last plugin is treated as the cache key for cache reads
 * and/or writes.
 *
 * @param {Object} options
 * @param {Request} options.request
 * @param {string} options.mode
 * @param {Array<Object>} [options.plugins=[]]
 * @return {Promise<Request>}
 *
 * @private
 * @memberof module:workbox-core
 */


const _getEffectiveRequest = async ({
  request,
  mode,
  plugins = []
}) => {
  const cacheKeyWillBeUsedPlugins = pluginUtils.filter(plugins, "cacheKeyWillBeUsed"
  /* CACHE_KEY_WILL_BE_USED */
  );
  let effectiveRequest = request;

  for (const plugin of cacheKeyWillBeUsedPlugins) {
    effectiveRequest = await plugin["cacheKeyWillBeUsed"
    /* CACHE_KEY_WILL_BE_USED */
    ].call(plugin, {
      mode,
      request: effectiveRequest
    });

    if (typeof effectiveRequest === 'string') {
      effectiveRequest = new Request(effectiveRequest);
    }

    {
      finalAssertExports.isInstance(effectiveRequest, Request, {
        moduleName: 'Plugin',
        funcName: "cacheKeyWillBeUsed"
        /* CACHE_KEY_WILL_BE_USED */
        ,
        isReturnValueProblem: true
      });
    }
  }

  return effectiveRequest;
};

const cacheWrapper = {
  put: putWrapper,
  match: matchWrapper
};

/*
  Copyright 2018 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
/**
 * Wrapper around the fetch API.
 *
 * Will call requestWillFetch on available plugins.
 *
 * @param {Object} options
 * @param {Request|string} options.request
 * @param {Object} [options.fetchOptions]
 * @param {ExtendableEvent} [options.event]
 * @param {Array<Object>} [options.plugins=[]]
 * @return {Promise<Response>}
 *
 * @private
 * @memberof module:workbox-core
 */

const wrappedFetch = async ({
  request,
  fetchOptions,
  event,
  plugins = []
}) => {
  if (typeof request === 'string') {
    request = new Request(request);
  } // We *should* be able to call `await event.preloadResponse` even if it's
  // undefined, but for some reason, doing so leads to errors in our Node unit
  // tests. To work around that, explicitly check preloadResponse's value first.


  if (event instanceof FetchEvent && event.preloadResponse) {
    const possiblePreloadResponse = await event.preloadResponse;

    if (possiblePreloadResponse) {
      {
        logger.log(`Using a preloaded navigation response for ` + `'${getFriendlyURL(request.url)}'`);
      }

      return possiblePreloadResponse;
    }
  }

  {
    finalAssertExports.isInstance(request, Request, {
      paramName: 'request',
      expectedClass: Request,
      moduleName: 'workbox-core',
      className: 'fetchWrapper',
      funcName: 'wrappedFetch'
    });
  }

  const failedFetchPlugins = pluginUtils.filter(plugins, "fetchDidFail"
  /* FETCH_DID_FAIL */
  ); // If there is a fetchDidFail plugin, we need to save a clone of the
  // original request before it's either modified by a requestWillFetch
  // plugin or before the original request's body is consumed via fetch().

  const originalRequest = failedFetchPlugins.length > 0 ? request.clone() : null;

  try {
    for (let plugin of plugins) {
      if ("requestWillFetch"
      /* REQUEST_WILL_FETCH */
      in plugin) {
        const pluginMethod = plugin["requestWillFetch"
        /* REQUEST_WILL_FETCH */
        ];
        const requestClone = request.clone();
        request = await pluginMethod.call(plugin, {
          request: requestClone,
          event
        });

        if ("development" !== 'production') {
          if (request) {
            finalAssertExports.isInstance(request, Request, {
              moduleName: 'Plugin',
              funcName: "cachedResponseWillBeUsed"
              /* CACHED_RESPONSE_WILL_BE_USED */
              ,
              isReturnValueProblem: true
            });
          }
        }
      }
    }
  } catch (err) {
    throw new WorkboxError('plugin-error-request-will-fetch', {
      thrownError: err
    });
  } // The request can be altered by plugins with `requestWillFetch` making
  // the original request (Most likely from a `fetch` event) to be different
  // to the Request we make. Pass both to `fetchDidFail` to aid debugging.


  let pluginFilteredRequest = request.clone();

  try {
    let fetchResponse; // See https://github.com/GoogleChrome/workbox/issues/1796

    if (request.mode === 'navigate') {
      fetchResponse = await fetch(request);
    } else {
      fetchResponse = await fetch(request, fetchOptions);
    }

    if ("development" !== 'production') {
      logger.debug(`Network request for ` + `'${getFriendlyURL(request.url)}' returned a response with ` + `status '${fetchResponse.status}'.`);
    }

    for (const plugin of plugins) {
      if ("fetchDidSucceed"
      /* FETCH_DID_SUCCEED */
      in plugin) {
        fetchResponse = await plugin["fetchDidSucceed"
        /* FETCH_DID_SUCCEED */
        ].call(plugin, {
          event,
          request: pluginFilteredRequest,
          response: fetchResponse
        });

        if ("development" !== 'production') {
          if (fetchResponse) {
            finalAssertExports.isInstance(fetchResponse, Response, {
              moduleName: 'Plugin',
              funcName: "fetchDidSucceed"
              /* FETCH_DID_SUCCEED */
              ,
              isReturnValueProblem: true
            });
          }
        }
      }
    }

    return fetchResponse;
  } catch (error) {
    {
      logger.error(`Network request for ` + `'${getFriendlyURL(request.url)}' threw an error.`, error);
    }

    for (const plugin of failedFetchPlugins) {
      await plugin["fetchDidFail"
      /* FETCH_DID_FAIL */
      ].call(plugin, {
        error,
        event,
        originalRequest: originalRequest.clone(),
        request: pluginFilteredRequest.clone()
      });
    }

    throw error;
  }
};

const fetchWrapper = {
  fetch: wrappedFetch
};

/*
  Copyright 2019 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
let supportStatus;
/**
 * A utility function that determines whether the current browser supports
 * constructing a new `Response` from a `response.body` stream.
 *
 * @return {boolean} `true`, if the current browser can successfully
 *     construct a `Response` from a `response.body` stream, `false` otherwise.
 *
 * @private
 */

function canConstructResponseFromBodyStream() {
  if (supportStatus === undefined) {
    const testResponse = new Response('');

    if ('body' in testResponse) {
      try {
        new Response(testResponse.body);
        supportStatus = true;
      } catch (error) {
        supportStatus = false;
      }
    }

    supportStatus = false;
  }

  return supportStatus;
}

/*
  Copyright 2019 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
/**
 * Allows developers to copy a response and modify its `headers`, `status`,
 * or `statusText` values (the values settable via a
 * [`ResponseInit`]{@link https://developer.mozilla.org/en-US/docs/Web/API/Response/Response#Syntax}
 * object in the constructor).
 * To modify these values, pass a function as the second argument. That
 * function will be invoked with a single object with the response properties
 * `{headers, status, statusText}`. The return value of this function will
 * be used as the `ResponseInit` for the new `Response`. To change the values
 * either modify the passed parameter(s) and return it, or return a totally
 * new object.
 *
 * @param {Response} response
 * @param {Function} modifier
 * @memberof module:workbox-core
 */

async function copyResponse(response, modifier) {
  const clonedResponse = response.clone(); // Create a fresh `ResponseInit` object by cloning the headers.

  const responseInit = {
    headers: new Headers(clonedResponse.headers),
    status: clonedResponse.status,
    statusText: clonedResponse.statusText
  }; // Apply any user modifications.

  const modifiedResponseInit = modifier ? modifier(responseInit) : responseInit; // Create the new response from the body stream and `ResponseInit`
  // modifications. Note: not all browsers support the Response.body stream,
  // so fall back to reading the entire body into memory as a blob.

  const body = canConstructResponseFromBodyStream() ? clonedResponse.body : await clonedResponse.blob();
  return new Response(body, modifiedResponseInit);
}

try {
  self['workbox:precaching:5.0.0'] && _();
} catch (e) {}

/*
  Copyright 2018 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/

const REVISION_SEARCH_PARAM = '__WB_REVISION__';
/**
 * Converts a manifest entry into a versioned URL suitable for precaching.
 *
 * @param {Object|string} entry
 * @return {string} A URL with versioning info.
 *
 * @private
 * @memberof module:workbox-precaching
 */

function createCacheKey(entry) {
  if (!entry) {
    throw new WorkboxError('add-to-cache-list-unexpected-type', {
      entry
    });
  } // If a precache manifest entry is a string, it's assumed to be a versioned
  // URL, like '/app.abcd1234.js'. Return as-is.


  if (typeof entry === 'string') {
    const urlObject = new URL(entry, location.href);
    return {
      cacheKey: urlObject.href,
      url: urlObject.href
    };
  }

  const {
    revision,
    url
  } = entry;

  if (!url) {
    throw new WorkboxError('add-to-cache-list-unexpected-type', {
      entry
    });
  } // If there's just a URL and no revision, then it's also assumed to be a
  // versioned URL.


  if (!revision) {
    const urlObject = new URL(url, location.href);
    return {
      cacheKey: urlObject.href,
      url: urlObject.href
    };
  } // Otherwise, construct a properly versioned URL using the custom Workbox
  // search parameter along with the revision info.


  const cacheKeyURL = new URL(url, location.href);
  const originalURL = new URL(url, location.href);
  cacheKeyURL.searchParams.set(REVISION_SEARCH_PARAM, revision);
  return {
    cacheKey: cacheKeyURL.href,
    url: originalURL.href
  };
}

/*
  Copyright 2018 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
/**
 * @param {string} groupTitle
 * @param {Array<string>} deletedURLs
 *
 * @private
 */

const logGroup = (groupTitle, deletedURLs) => {
  logger.groupCollapsed(groupTitle);

  for (const url of deletedURLs) {
    logger.log(url);
  }

  logger.groupEnd();
};
/**
 * @param {Array<string>} deletedURLs
 *
 * @private
 * @memberof module:workbox-precaching
 */


function printCleanupDetails(deletedURLs) {
  const deletionCount = deletedURLs.length;

  if (deletionCount > 0) {
    logger.groupCollapsed(`During precaching cleanup, ` + `${deletionCount} cached ` + `request${deletionCount === 1 ? ' was' : 's were'} deleted.`);
    logGroup('Deleted Cache Requests', deletedURLs);
    logger.groupEnd();
  }
}

/*
  Copyright 2018 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
/**
 * @param {string} groupTitle
 * @param {Array<string>} urls
 *
 * @private
 */

function _nestedGroup(groupTitle, urls) {
  if (urls.length === 0) {
    return;
  }

  logger.groupCollapsed(groupTitle);

  for (const url of urls) {
    logger.log(url);
  }

  logger.groupEnd();
}
/**
 * @param {Array<string>} urlsToPrecache
 * @param {Array<string>} urlsAlreadyPrecached
 *
 * @private
 * @memberof module:workbox-precaching
 */


function printInstallDetails(urlsToPrecache, urlsAlreadyPrecached) {
  const precachedCount = urlsToPrecache.length;
  const alreadyPrecachedCount = urlsAlreadyPrecached.length;

  if (precachedCount || alreadyPrecachedCount) {
    let message = `Precaching ${precachedCount} file${precachedCount === 1 ? '' : 's'}.`;

    if (alreadyPrecachedCount > 0) {
      message += ` ${alreadyPrecachedCount} ` + `file${alreadyPrecachedCount === 1 ? ' is' : 's are'} already cached.`;
    }

    logger.groupCollapsed(message);

    _nestedGroup(`View newly precached URLs.`, urlsToPrecache);

    _nestedGroup(`View previously precached URLs.`, urlsAlreadyPrecached);

    logger.groupEnd();
  }
}

/*
  Copyright 2019 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
/**
 * Performs efficient precaching of assets.
 *
 * @memberof module:workbox-precaching
 */

class PrecacheController {
  /**
   * Create a new PrecacheController.
   *
   * @param {string} [cacheName] An optional name for the cache, to override
   * the default precache name.
   */
  constructor(cacheName) {
    this._cacheName = cacheNames.getPrecacheName(cacheName);
    this._urlsToCacheKeys = new Map();
    this._urlsToCacheModes = new Map();
    this._cacheKeysToIntegrities = new Map();
  }
  /**
   * This method will add items to the precache list, removing duplicates
   * and ensuring the information is valid.
   *
   * @param {
   * Array<module:workbox-precaching.PrecacheController.PrecacheEntry|string>
   * } entries Array of entries to precache.
   */


  addToCacheList(entries) {
    {
      finalAssertExports.isArray(entries, {
        moduleName: 'workbox-precaching',
        className: 'PrecacheController',
        funcName: 'addToCacheList',
        paramName: 'entries'
      });
    }

    const urlsToWarnAbout = [];

    for (const entry of entries) {
      // See https://github.com/GoogleChrome/workbox/issues/2259
      if (typeof entry === 'string') {
        urlsToWarnAbout.push(entry);
      } else if (entry && entry.revision === undefined) {
        urlsToWarnAbout.push(entry.url);
      }

      const {
        cacheKey,
        url
      } = createCacheKey(entry);
      const cacheMode = typeof entry !== 'string' && entry.revision ? 'reload' : 'default';

      if (this._urlsToCacheKeys.has(url) && this._urlsToCacheKeys.get(url) !== cacheKey) {
        throw new WorkboxError('add-to-cache-list-conflicting-entries', {
          firstEntry: this._urlsToCacheKeys.get(url),
          secondEntry: cacheKey
        });
      }

      if (typeof entry !== 'string' && entry.integrity) {
        if (this._cacheKeysToIntegrities.has(cacheKey) && this._cacheKeysToIntegrities.get(cacheKey) !== entry.integrity) {
          throw new WorkboxError('add-to-cache-list-conflicting-integrities', {
            url
          });
        }

        this._cacheKeysToIntegrities.set(cacheKey, entry.integrity);
      }

      this._urlsToCacheKeys.set(url, cacheKey);

      this._urlsToCacheModes.set(url, cacheMode);

      if (urlsToWarnAbout.length > 0) {
        const warningMessage = `Workbox is precaching URLs without revision ` + `info: ${urlsToWarnAbout.join(', ')}\nThis is generally NOT safe. ` + `Learn more at https://bit.ly/wb-precache`;

        {
          logger.warn(warningMessage);
        }
      }
    }
  }
  /**
   * Precaches new and updated assets. Call this method from the service worker
   * install event.
   *
   * @param {Object} options
   * @param {Event} [options.event] The install event (if needed).
   * @param {Array<Object>} [options.plugins] Plugins to be used for fetching
   * and caching during install.
   * @return {Promise<module:workbox-precaching.InstallResult>}
   */


  async install({
    event,
    plugins
  } = {}) {
    {
      if (plugins) {
        finalAssertExports.isArray(plugins, {
          moduleName: 'workbox-precaching',
          className: 'PrecacheController',
          funcName: 'install',
          paramName: 'plugins'
        });
      }
    }

    const toBePrecached = [];
    const alreadyPrecached = [];
    const cache = await self.caches.open(this._cacheName);
    const alreadyCachedRequests = await cache.keys();
    const existingCacheKeys = new Set(alreadyCachedRequests.map(request => request.url));

    for (const [url, cacheKey] of this._urlsToCacheKeys) {
      if (existingCacheKeys.has(cacheKey)) {
        alreadyPrecached.push(url);
      } else {
        toBePrecached.push({
          cacheKey,
          url
        });
      }
    }

    const precacheRequests = toBePrecached.map(({
      cacheKey,
      url
    }) => {
      const integrity = this._cacheKeysToIntegrities.get(cacheKey);

      const cacheMode = this._urlsToCacheModes.get(url);

      return this._addURLToCache({
        cacheKey,
        cacheMode,
        event,
        integrity,
        plugins,
        url
      });
    });
    await Promise.all(precacheRequests);
    const updatedURLs = toBePrecached.map(item => item.url);

    {
      printInstallDetails(updatedURLs, alreadyPrecached);
    }

    return {
      updatedURLs,
      notUpdatedURLs: alreadyPrecached
    };
  }
  /**
   * Deletes assets that are no longer present in the current precache manifest.
   * Call this method from the service worker activate event.
   *
   * @return {Promise<module:workbox-precaching.CleanupResult>}
   */


  async activate() {
    const cache = await self.caches.open(this._cacheName);
    const currentlyCachedRequests = await cache.keys();
    const expectedCacheKeys = new Set(this._urlsToCacheKeys.values());
    const deletedURLs = [];

    for (const request of currentlyCachedRequests) {
      if (!expectedCacheKeys.has(request.url)) {
        await cache.delete(request);
        deletedURLs.push(request.url);
      }
    }

    {
      printCleanupDetails(deletedURLs);
    }

    return {
      deletedURLs
    };
  }
  /**
   * Requests the entry and saves it to the cache if the response is valid.
   * By default, any response with a status code of less than 400 (including
   * opaque responses) is considered valid.
   *
   * If you need to use custom criteria to determine what's valid and what
   * isn't, then pass in an item in `options.plugins` that implements the
   * `cacheWillUpdate()` lifecycle event.
   *
   * @private
   * @param {Object} options
   * @param {string} options.cacheKey The string to use a cache key.
   * @param {string} options.url The URL to fetch and cache.
   * @param {string} [options.cacheMode] The cache mode for the network request.
   * @param {Event} [options.event] The install event (if passed).
   * @param {Array<Object>} [options.plugins] An array of plugins to apply to
   * fetch and caching.
   * @param {string} [options.integrity] The value to use for the `integrity`
   * field when making the request.
   */


  async _addURLToCache({
    cacheKey,
    url,
    cacheMode,
    event,
    plugins,
    integrity
  }) {
    const request = new Request(url, {
      integrity,
      cache: cacheMode,
      credentials: 'same-origin'
    });
    let response = await fetchWrapper.fetch({
      event,
      plugins,
      request
    }); // Allow developers to override the default logic about what is and isn't
    // valid by passing in a plugin implementing cacheWillUpdate(), e.g.
    // a `CacheableResponsePlugin` instance.

    let cacheWillUpdatePlugin;

    for (const plugin of plugins || []) {
      if ('cacheWillUpdate' in plugin) {
        cacheWillUpdatePlugin = plugin;
      }
    }

    const isValidResponse = cacheWillUpdatePlugin ? // Use a callback if provided. It returns a truthy value if valid.
    // NOTE: invoke the method on the plugin instance so the `this` context
    // is correct.
    await cacheWillUpdatePlugin.cacheWillUpdate({
      event,
      request,
      response
    }) : // Otherwise, default to considering any response status under 400 valid.
    // This includes, by default, considering opaque responses valid.
    response.status < 400; // Consider this a failure, leading to the `install` handler failing, if
    // we get back an invalid response.

    if (!isValidResponse) {
      throw new WorkboxError('bad-precaching-response', {
        url,
        status: response.status
      });
    } // Redirected responses cannot be used to satisfy a navigation request, so
    // any redirected response must be "copied" rather than cloned, so the new
    // response doesn't contain the `redirected` flag. See:
    // https://bugs.chromium.org/p/chromium/issues/detail?id=669363&desc=2#c1


    if (response.redirected) {
      response = await copyResponse(response);
    }

    await cacheWrapper.put({
      event,
      plugins,
      response,
      // `request` already uses `url`. We may be able to reuse it.
      request: cacheKey === url ? request : new Request(cacheKey),
      cacheName: this._cacheName,
      matchOptions: {
        ignoreSearch: true
      }
    });
  }
  /**
   * Returns a mapping of a precached URL to the corresponding cache key, taking
   * into account the revision information for the URL.
   *
   * @return {Map<string, string>} A URL to cache key mapping.
   */


  getURLsToCacheKeys() {
    return this._urlsToCacheKeys;
  }
  /**
   * Returns a list of all the URLs that have been precached by the current
   * service worker.
   *
   * @return {Array<string>} The precached URLs.
   */


  getCachedURLs() {
    return [...this._urlsToCacheKeys.keys()];
  }
  /**
   * Returns the cache key used for storing a given URL. If that URL is
   * unversioned, like `/index.html', then the cache key will be the original
   * URL with a search parameter appended to it.
   *
   * @param {string} url A URL whose cache key you want to look up.
   * @return {string} The versioned URL that corresponds to a cache key
   * for the original URL, or undefined if that URL isn't precached.
   */


  getCacheKeyForURL(url) {
    const urlObject = new URL(url, location.href);
    return this._urlsToCacheKeys.get(urlObject.href);
  }
  /**
   * This acts as a drop-in replacement for [`cache.match()`](https://developer.mozilla.org/en-US/docs/Web/API/Cache/match)
   * with the following differences:
   *
   * - It knows what the name of the precache is, and only checks in that cache.
   * - It allows you to pass in an "original" URL without versioning parameters,
   * and it will automatically look up the correct cache key for the currently
   * active revision of that URL.
   *
   * E.g., `matchPrecache('index.html')` will find the correct precached
   * response for the currently active service worker, even if the actual cache
   * key is `'/index.html?__WB_REVISION__=1234abcd'`.
   *
   * @param {string|Request} request The key (without revisioning parameters)
   * to look up in the precache.
   * @return {Promise<Response|undefined>}
   */


  async matchPrecache(request) {
    const url = request instanceof Request ? request.url : request;
    const cacheKey = this.getCacheKeyForURL(url);

    if (cacheKey) {
      const cache = await self.caches.open(this._cacheName);
      return cache.match(cacheKey);
    }

    return undefined;
  }
  /**
   * Returns a function that can be used within a
   * {@link module:workbox-routing.Route} that will find a response for the
   * incoming request against the precache.
   *
   * If for an unexpected reason there is a cache miss for the request,
   * this will fall back to retrieving the `Response` via `fetch()` when
   * `fallbackToNetwork` is `true`.
   *
   * @param {boolean} [fallbackToNetwork=true] Whether to attempt to get the
   * response from the network if there's a precache miss.
   * @return {module:workbox-routing~handlerCallback}
   */


  createHandler(fallbackToNetwork = true) {
    return async ({
      request
    }) => {
      try {
        const response = await this.matchPrecache(request);

        if (response) {
          return response;
        } // This shouldn't normally happen, but there are edge cases:
        // https://github.com/GoogleChrome/workbox/issues/1441


        throw new WorkboxError('missing-precache-entry', {
          cacheName: this._cacheName,
          url: request instanceof Request ? request.url : request
        });
      } catch (error) {
        if (fallbackToNetwork) {
          {
            logger.debug(`Unable to respond with precached response. ` + `Falling back to network.`, error);
          }

          return fetch(request);
        }

        throw error;
      }
    };
  }
  /**
   * Returns a function that looks up `url` in the precache (taking into
   * account revision information), and returns the corresponding `Response`.
   *
   * If for an unexpected reason there is a cache miss when looking up `url`,
   * this will fall back to retrieving the `Response` via `fetch()` when
   * `fallbackToNetwork` is `true`.
   *
   * @param {string} url The precached URL which will be used to lookup the
   * `Response`.
   * @param {boolean} [fallbackToNetwork=true] Whether to attempt to get the
   * response from the network if there's a precache miss.
   * @return {module:workbox-routing~handlerCallback}
   */


  createHandlerBoundToURL(url, fallbackToNetwork = true) {
    const cacheKey = this.getCacheKeyForURL(url);

    if (!cacheKey) {
      throw new WorkboxError('non-precached-url', {
        url
      });
    }

    const handler = this.createHandler(fallbackToNetwork);
    const request = new Request(url);
    return () => handler({
      request
    });
  }

}

/*
  Copyright 2019 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
let precacheController;
/**
 * @return {PrecacheController}
 * @private
 */

const getOrCreatePrecacheController = () => {
  if (!precacheController) {
    precacheController = new PrecacheController();
  }

  return precacheController;
};

/*
  Copyright 2018 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
/**
 * Removes any URL search parameters that should be ignored.
 *
 * @param {URL} urlObject The original URL.
 * @param {Array<RegExp>} ignoreURLParametersMatching RegExps to test against
 * each search parameter name. Matches mean that the search parameter should be
 * ignored.
 * @return {URL} The URL with any ignored search parameters removed.
 *
 * @private
 * @memberof module:workbox-precaching
 */

function removeIgnoredSearchParams(urlObject, ignoreURLParametersMatching = []) {
  // Convert the iterable into an array at the start of the loop to make sure
  // deletion doesn't mess up iteration.
  for (const paramName of [...urlObject.searchParams.keys()]) {
    if (ignoreURLParametersMatching.some(regExp => regExp.test(paramName))) {
      urlObject.searchParams.delete(paramName);
    }
  }

  return urlObject;
}

/*
  Copyright 2019 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
/**
 * Generator function that yields possible variations on the original URL to
 * check, one at a time.
 *
 * @param {string} url
 * @param {Object} options
 *
 * @private
 * @memberof module:workbox-precaching
 */

function* generateURLVariations(url, {
  ignoreURLParametersMatching,
  directoryIndex,
  cleanURLs,
  urlManipulation
} = {}) {
  const urlObject = new URL(url, location.href);
  urlObject.hash = '';
  yield urlObject.href;
  const urlWithoutIgnoredParams = removeIgnoredSearchParams(urlObject, ignoreURLParametersMatching);
  yield urlWithoutIgnoredParams.href;

  if (directoryIndex && urlWithoutIgnoredParams.pathname.endsWith('/')) {
    const directoryURL = new URL(urlWithoutIgnoredParams.href);
    directoryURL.pathname += directoryIndex;
    yield directoryURL.href;
  }

  if (cleanURLs) {
    const cleanURL = new URL(urlWithoutIgnoredParams.href);
    cleanURL.pathname += '.html';
    yield cleanURL.href;
  }

  if (urlManipulation) {
    const additionalURLs = urlManipulation({
      url: urlObject
    });

    for (const urlToAttempt of additionalURLs) {
      yield urlToAttempt.href;
    }
  }
}

/*
  Copyright 2019 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
/**
 * This function will take the request URL and manipulate it based on the
 * configuration options.
 *
 * @param {string} url
 * @param {Object} options
 * @return {string} Returns the URL in the cache that matches the request,
 * if possible.
 *
 * @private
 */

const getCacheKeyForURL = (url, options) => {
  const precacheController = getOrCreatePrecacheController();
  const urlsToCacheKeys = precacheController.getURLsToCacheKeys();

  for (const possibleURL of generateURLVariations(url, options)) {
    const possibleCacheKey = urlsToCacheKeys.get(possibleURL);

    if (possibleCacheKey) {
      return possibleCacheKey;
    }
  }
};

/*
  Copyright 2019 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
/**
 * Adds a `fetch` listener to the service worker that will
 * respond to
 * [network requests]{@link https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API/Using_Service_Workers#Custom_responses_to_requests}
 * with precached assets.
 *
 * Requests for assets that aren't precached, the `FetchEvent` will not be
 * responded to, allowing the event to fall through to other `fetch` event
 * listeners.
 *
 * NOTE: when called more than once this method will replace the previously set
 * configuration options. Calling it more than once is not recommended outside
 * of tests.
 *
 * @private
 * @param {Object} [options]
 * @param {string} [options.directoryIndex=index.html] The `directoryIndex` will
 * check cache entries for a URLs ending with '/' to see if there is a hit when
 * appending the `directoryIndex` value.
 * @param {Array<RegExp>} [options.ignoreURLParametersMatching=[/^utm_/]] An
 * array of regex's to remove search params when looking for a cache match.
 * @param {boolean} [options.cleanURLs=true] The `cleanURLs` option will
 * check the cache for the URL with a `.html` added to the end of the end.
 * @param {workbox.precaching~urlManipulation} [options.urlManipulation]
 * This is a function that should take a URL and return an array of
 * alternative URLs that should be checked for precache matches.
 */

const addFetchListener = ({
  ignoreURLParametersMatching = [/^utm_/],
  directoryIndex = 'index.html',
  cleanURLs = true,
  urlManipulation
} = {}) => {
  const cacheName = cacheNames.getPrecacheName(); // See https://github.com/Microsoft/TypeScript/issues/28357#issuecomment-436484705

  self.addEventListener('fetch', event => {
    const precachedURL = getCacheKeyForURL(event.request.url, {
      cleanURLs,
      directoryIndex,
      ignoreURLParametersMatching,
      urlManipulation
    });

    if (!precachedURL) {
      {
        logger.debug(`Precaching did not find a match for ` + getFriendlyURL(event.request.url));
      }

      return;
    }

    let responsePromise = self.caches.open(cacheName).then(cache => {
      return cache.match(precachedURL);
    }).then(cachedResponse => {
      if (cachedResponse) {
        return cachedResponse;
      } // Fall back to the network if we don't have a cached response
      // (perhaps due to manual cache cleanup).


      {
        logger.warn(`The precached response for ` + `${getFriendlyURL(precachedURL)} in ${cacheName} was not found. ` + `Falling back to the network instead.`);
      }

      return fetch(precachedURL);
    });

    {
      responsePromise = responsePromise.then(response => {
        // Workbox is going to handle the route.
        // print the routing details to the console.
        logger.groupCollapsed(`Precaching is responding to: ` + getFriendlyURL(event.request.url));
        logger.log(`Serving the precached url: ${precachedURL}`);
        logger.groupCollapsed(`View request details here.`);
        logger.log(event.request);
        logger.groupEnd();
        logger.groupCollapsed(`View response details here.`);
        logger.log(response);
        logger.groupEnd();
        logger.groupEnd();
        return response;
      });
    }

    event.respondWith(responsePromise);
  });
};

/*
  Copyright 2019 Google LLC
  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
let listenerAdded = false;
/**
 * Add a `fetch` listener to the service worker that will
 * respond to
 * [network requests]{@link https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API/Using_Service_Workers#Custom_responses_to_requests}
 * with precached assets.
 *
 * Requests for assets that aren't precached, the `FetchEvent` will not be
 * responded to, allowing the event to fall through to other `fetch` event
 * listeners.
 *
 * @param {Object} [options]
 * @param {string} [options.directoryIndex=index.html] The `directoryIndex` will
 * check cache entries for a URLs ending with '/' to see if there is a hit when
 * appending the `directoryIndex` value.
 * @param {Array<RegExp>} [options.ignoreURLParametersMatching=[/^utm_/]] An
 * array of regex's to remove search params when looking for a cache match.
 * @param {boolean} [options.cleanURLs=true] The `cleanURLs` option will
 * check the cache for the URL with a `.html` added to the end of the end.
 * @param {module:workbox-precaching~urlManipulation} [options.urlManipulation]
 * This is a function that should take a URL and return an array of
 * alternative URLs that should be checked for precache matches.
 *
 * @memberof module:workbox-precaching
 */

function addRoute(options) {
  if (!listenerAdded) {
    addFetchListener(options);
    listenerAdded = true;
  }
}

/*
  Copyright 2019 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
const plugins = [];
const precachePlugins = {
  /*
   * @return {Array}
   * @private
   */
  get() {
    return plugins;
  },

  /*
   * @param {Array} newPlugins
   * @private
   */
  add(newPlugins) {
    plugins.push(...newPlugins);
  }

};

/*
  Copyright 2019 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/

const installListener = event => {
  const precacheController = getOrCreatePrecacheController();
  const plugins = precachePlugins.get();
  event.waitUntil(precacheController.install({
    event,
    plugins
  }).catch(error => {
    {
      logger.error(`Service worker installation failed. It will ` + `be retried automatically during the next navigation.`);
    } // Re-throw the error to ensure installation fails.


    throw error;
  }));
};

const activateListener = event => {
  const precacheController = getOrCreatePrecacheController();
  event.waitUntil(precacheController.activate());
};
/**
 * Adds items to the precache list, removing any duplicates and
 * stores the files in the
 * ["precache cache"]{@link module:workbox-core.cacheNames} when the service
 * worker installs.
 *
 * This method can be called multiple times.
 *
 * Please note: This method **will not** serve any of the cached files for you.
 * It only precaches files. To respond to a network request you call
 * [addRoute()]{@link module:workbox-precaching.addRoute}.
 *
 * If you have a single array of files to precache, you can just call
 * [precacheAndRoute()]{@link module:workbox-precaching.precacheAndRoute}.
 *
 * @param {Array<Object|string>} [entries=[]] Array of entries to precache.
 *
 * @memberof module:workbox-precaching
 */


function precache(entries) {
  const precacheController = getOrCreatePrecacheController();
  precacheController.addToCacheList(entries);

  if (entries.length > 0) {
    // NOTE: these listeners will only be added once (even if the `precache()`
    // method is called multiple times) because event listeners are implemented
    // as a set, where each listener must be unique.
    // See https://github.com/Microsoft/TypeScript/issues/28357#issuecomment-436484705
    self.addEventListener('install', installListener);
    self.addEventListener('activate', activateListener);
  }
}

/*
  Copyright 2019 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
/**
 * This method will add entries to the precache list and add a route to
 * respond to fetch events.
 *
 * This is a convenience method that will call
 * [precache()]{@link module:workbox-precaching.precache} and
 * [addRoute()]{@link module:workbox-precaching.addRoute} in a single call.
 *
 * @param {Array<Object|string>} entries Array of entries to precache.
 * @param {Object} [options] See
 * [addRoute() options]{@link module:workbox-precaching.addRoute}.
 *
 * @memberof module:workbox-precaching
 */

function precacheAndRoute(entries, options) {
  precache(entries);
  addRoute(options);
}

/**
* Welcome to your Workbox-powered service worker!
*
* You'll need to register this file in your web app.
* See https://goo.gl/nhQhGp
*
* The rest of the code is auto-generated. Please don't update this file
* directly; instead, make changes to your Workbox build configuration
* and re-run your build process.
* See https://goo.gl/2aRDsh
*/

skipWaiting();
clientsClaim();
/**
 * The precacheAndRoute() method efficiently caches and responds to
 * requests for URLs in the manifest.
 * See https://goo.gl/S9QRab
 */

precacheAndRoute([{
  "url": "jscalendar/calendar-setup.js",
  "revision": "1d6c5bf60a74c0700cc5a98b345ea68a"
}, {
  "url": "jscalendar/calendar-win2k-cold-1.css",
  "revision": "6abca10115d091d0ed4b5dfbc05945d2"
}, {
  "url": "jscalendar/calendar.js",
  "revision": "0f5478de5833a3865c255b072c5635d9"
}, {
  "url": "jscalendar/img.gif",
  "revision": "c1e5255bd358fcd5a0779a0cc310a2fe"
}, {
  "url": "jscalendar/lang/calendar-af.js",
  "revision": "65fc5963bf1f044c7f0d89be381cb87a"
}, {
  "url": "jscalendar/lang/calendar-br.js",
  "revision": "65fb4100d35f121f0b45981e61158fa0"
}, {
  "url": "jscalendar/lang/calendar-ca.js",
  "revision": "00e83a2121db4d21c059d3335f67eb42"
}, {
  "url": "jscalendar/lang/calendar-cs-win.js",
  "revision": "3556760402191331e9ebdc868992cf78"
}, {
  "url": "jscalendar/lang/calendar-da.js",
  "revision": "14ba236068dd2666d30e1a34055e9e7e"
}, {
  "url": "jscalendar/lang/calendar-de.js",
  "revision": "cce502a97461d1732c8932c66d2592f8"
}, {
  "url": "jscalendar/lang/calendar-du.js",
  "revision": "82ab1eabcc24cba821b950d12aaba8b3"
}, {
  "url": "jscalendar/lang/calendar-el.js",
  "revision": "8eb26742d8354ca3833e9be4d72e3bec"
}, {
  "url": "jscalendar/lang/calendar-en.js",
  "revision": "7ff36b7944c535271983566ac10a041b"
}, {
  "url": "jscalendar/lang/calendar-es.js",
  "revision": "26d7a6308d9dc6088978710726abaae2"
}, {
  "url": "jscalendar/lang/calendar-fi.js",
  "revision": "8d0194ed53abae22c8e98d633aee4626"
}, {
  "url": "jscalendar/lang/calendar-fr.js",
  "revision": "fadd88e8ac9444357a1426007927b3f2"
}, {
  "url": "jscalendar/lang/calendar-hr-utf8.js",
  "revision": "8d3bc284f3c2bfad26b38c6224fbc1ca"
}, {
  "url": "jscalendar/lang/calendar-hr.js",
  "revision": "921e18e1c60ba3425fbc6eb2f12984f6"
}, {
  "url": "jscalendar/lang/calendar-hu.js",
  "revision": "10409c671140a39b91e9f96dcedbe1b1"
}, {
  "url": "jscalendar/lang/calendar-it.js",
  "revision": "a947e189174be4d737ac034abe9db957"
}, {
  "url": "jscalendar/lang/calendar-jp.js",
  "revision": "b47ddea3200306ace5d9493ef98eca8e"
}, {
  "url": "jscalendar/lang/calendar-ko-utf8.js",
  "revision": "a9868c0d85ce1cec22cadf007c3f0661"
}, {
  "url": "jscalendar/lang/calendar-ko.js",
  "revision": "22bce81d786dfdbcc91654b23cb89afe"
}, {
  "url": "jscalendar/lang/calendar-lt-utf8.js",
  "revision": "ff0c26d46b71feeb3e536144ac566690"
}, {
  "url": "jscalendar/lang/calendar-lt.js",
  "revision": "06b3f5ddc2465af49ac476efdfead122"
}, {
  "url": "jscalendar/lang/calendar-nl.js",
  "revision": "6e570069acfffe65ba50608436221bd6"
}, {
  "url": "jscalendar/lang/calendar-no.js",
  "revision": "4ac0b8870fa93c6f49c183f71cd4baf9"
}, {
  "url": "jscalendar/lang/calendar-pl-utf8.js",
  "revision": "4df2bd769a1ebf7bb9ff4e634a80fd1c"
}, {
  "url": "jscalendar/lang/calendar-pl.js",
  "revision": "34c066e72e565b3a09af07c85a56de7a"
}, {
  "url": "jscalendar/lang/calendar-pt.js",
  "revision": "177f8bcab8ce4300dfffc1edbfa25e2c"
}, {
  "url": "jscalendar/lang/calendar-ro.js",
  "revision": "b2e4098f50eb62ad3fd4d44c1202c0b7"
}, {
  "url": "jscalendar/lang/calendar-ru.js",
  "revision": "b38f5c1915147fbcd4d7e47c9205b478"
}, {
  "url": "jscalendar/lang/calendar-si.js",
  "revision": "b66bac0654b4309f577ed89392e80132"
}, {
  "url": "jscalendar/lang/calendar-sk.js",
  "revision": "782c204921fac0922f297d2418b05756"
}, {
  "url": "jscalendar/lang/calendar-sp.js",
  "revision": "b8fd8524ae91fe7a4d27742fb30eb9bc"
}, {
  "url": "jscalendar/lang/calendar-sv.js",
  "revision": "76d3d0e80ee3f36e96875442567682cd"
}, {
  "url": "jscalendar/lang/calendar-tr.js",
  "revision": "bbbb0d304d90b89fd7336538088f2ba0"
}, {
  "url": "jscalendar/lang/calendar-zh.js",
  "revision": "304eef7561e91ed20df0ceddb155314b"
}, {
  "url": "jscalendar/menuarrow.gif",
  "revision": "b5a91d7a2755198b2eb729541ad3288c"
}, {
  "url": "jscalendar/menuarrow2.gif",
  "revision": "1f8c673c8f76832febaeeac88a5f4353"
}, {
  "url": "modules/Calendar4You/Calendar4You.js",
  "revision": "50d3abdbb3764da1689ad0de87c66cf4"
}, {
  "url": "modules/Calendar4You/Calendar4You.png",
  "revision": "af2cca5b0d51fbfc4248fee073bb75eb"
}, {
  "url": "modules/Calendar4You/fullcalendar/fullcalendar.css",
  "revision": "47f6229cce431d267654ed013368eeb5"
}, {
  "url": "modules/Calendar4You/fullcalendar/fullcalendar.js",
  "revision": "0068d4028e1e83203a153d09d0fcfc6a"
}, {
  "url": "modules/Calendar4You/fullcalendar/fullcalendar.min.css",
  "revision": "7359f6ebc56c4ba9309895ed0ff48f45"
}, {
  "url": "modules/Calendar4You/fullcalendar/fullcalendar.min.js",
  "revision": "846152635d7e89526179ac13fbd1483a"
}, {
  "url": "modules/Calendar4You/fullcalendar/fullcalendar.print.css",
  "revision": "e26ff154c0948181dac143a4b361662d"
}, {
  "url": "modules/Calendar4You/fullcalendar/fullcalendar.print.min.css",
  "revision": "1712640f1c08b5e9412e06caf3bfebfe"
}, {
  "url": "modules/Calendar4You/fullcalendar/gcal.js",
  "revision": "5155e8f8435d60d5e9a3dc3ad4b6076e"
}, {
  "url": "modules/Calendar4You/fullcalendar/gcal.min.js",
  "revision": "0a8420923a7aaa5fa5bdfb83884452ae"
}, {
  "url": "modules/Calendar4You/fullcalendar/lib/jquery-ui.min.js",
  "revision": "c15b1008dec3c8967ea657a7bb4baaec"
}, {
  "url": "modules/Calendar4You/fullcalendar/lib/jquery.min.js",
  "revision": "a09e13ee94d51c524b7e2a728c7d4039"
}, {
  "url": "modules/Calendar4You/fullcalendar/lib/moment.min.js",
  "revision": "de82f2f2bd52ead2e0dbe58983236395"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale-all.js",
  "revision": "c61d63888b913a98ea1c292d5e9fae94"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/af.js",
  "revision": "4cc8fff4e0eae2beceb28852407d8ee1"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/ar-dz.js",
  "revision": "bdebe3cc6c859fe19a39c4dcaeb8f46f"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/ar-kw.js",
  "revision": "c99dc3d59530da6680fd7ab4f4a59764"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/ar-ly.js",
  "revision": "56ead35e6aacb17c5b2348bb4393346b"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/ar-ma.js",
  "revision": "e71fc413afe12229dd32c450f31b924c"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/ar-sa.js",
  "revision": "37b2642147cf798072f41e6db0ff9e24"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/ar-tn.js",
  "revision": "6688881ac22f904e20d475b4f7d8e502"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/ar.js",
  "revision": "318ee308a3338e650e21eef9bfec4454"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/be.js",
  "revision": "ee704412608520b4a15461067bf10e80"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/bg.js",
  "revision": "611a2797fc737fdd91f4ff1475841375"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/bs.js",
  "revision": "ccbff7000925c53198d4994d2b020920"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/ca.js",
  "revision": "9f4daf05cbd58dac565e862548362e50"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/cs.js",
  "revision": "c6425a5c310fa6e4f8daee83b2dbd642"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/da.js",
  "revision": "c5e64b64407b8711626d0b341dbe6aa7"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/de-at.js",
  "revision": "f58ebbf82fd65ff7a547e47ecffa9a87"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/de-ch.js",
  "revision": "1ff636a1d3f423fc532bf18a3ca77657"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/de.js",
  "revision": "15d5d4e179318560629fa0eea56a8510"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/el.js",
  "revision": "0cc55851e13048a37c3218ebdeb94307"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/en-au.js",
  "revision": "87eb7d91bfd405f2b1fa748dd944ec2e"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/en-ca.js",
  "revision": "a21ff12d18dd973274e8d83726abfb46"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/en-gb.js",
  "revision": "e957c4fa13bca1f4a29528b8729eff9c"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/en-ie.js",
  "revision": "38239bb17bb2795159fb869af42f3ed6"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/en-nz.js",
  "revision": "536427f5c3395b42415533f189aba586"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/es-do.js",
  "revision": "3f973a3cc32a8dfd98ba2cc768543b41"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/es-us.js",
  "revision": "22226a5ec23b1305c566e7604af18e75"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/es.js",
  "revision": "96dea70e8dad2e4403103a88629794b6"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/et.js",
  "revision": "e968806ddef3a40ac8ee3beb4630b876"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/eu.js",
  "revision": "e7a94e494c802877f4563f38d92f6d0e"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/fa.js",
  "revision": "283ef4df61fb25f14077a8cbae5becbf"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/fi.js",
  "revision": "8da8595594a3664a85e471e21d203a82"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/fr-ca.js",
  "revision": "e8153da9dc2ca4ad6a0b4dd29f217f72"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/fr-ch.js",
  "revision": "10d15457f413e7e46a68cd79658f30d6"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/fr.js",
  "revision": "140a7e017212fcfcb7969f2adadc499a"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/gl.js",
  "revision": "40672764b9b4e830fa5231a8c33d6d71"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/he.js",
  "revision": "e340e0ad6d7324a4a01b1329ff5e8e8c"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/hi.js",
  "revision": "3be62d0ccf23cdc983f81fcffebf8fad"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/hr.js",
  "revision": "ec038ba8c412e64a752f28082f977697"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/hu.js",
  "revision": "c69c977c4698337ef02bc6d680f87171"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/id.js",
  "revision": "3ad379acdd2a9c87636116db9c31a374"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/is.js",
  "revision": "6756a2b553dfe63771134dd621836150"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/it.js",
  "revision": "fadc0b1ec8f55f7cdcbc3b2cf8089162"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/ja.js",
  "revision": "65064401b0da655f340018ee20d79e22"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/ka.js",
  "revision": "68373ec74e30e3f90b67df1aa56e4f05"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/kk.js",
  "revision": "a1a408d0934f8efee6b2e251d981aff9"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/ko.js",
  "revision": "baa216ba89b4cd77221c2bdf4cd7a51b"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/lb.js",
  "revision": "c383c75a5e9b54fa9d23afa1c33d81f6"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/lt.js",
  "revision": "2973703128bcb2ebada18ff239407825"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/lv.js",
  "revision": "316fba27f32475d6a2deef6c3932cba9"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/mk.js",
  "revision": "0392f05fd92c81754ac5001f6c8f03a6"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/ms-my.js",
  "revision": "9b11dd8ab04790934e73026d0be5c105"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/ms.js",
  "revision": "6775cf3c9ec6a867a01f63865ea2b02e"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/nb.js",
  "revision": "eafaeca16ab9f245a2a85c469ee0ffe0"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/nl-be.js",
  "revision": "a290d39d6d9956e66f33592c91f03db5"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/nl.js",
  "revision": "0104eea0e4be0f98dc258ad3e2331eb4"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/nn.js",
  "revision": "bfb236dc35f50e798205e652c9d568e2"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/pl.js",
  "revision": "5c5178c680aa3391867239eaa12ca85c"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/pt-br.js",
  "revision": "9e99177e1aff4f252f331e21b5345312"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/pt.js",
  "revision": "d0d6e93cca316749b60f842603f624b8"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/ro.js",
  "revision": "ea3eac43b6f7094e136bf274477e1063"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/ru.js",
  "revision": "d870d9459ac48f242fd78769a558711e"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/sk.js",
  "revision": "47ac02a362d43c11bedb68c9a26c8ca7"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/sl.js",
  "revision": "bacd5fdefce08e1291eec50fdd1bf5f3"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/sq.js",
  "revision": "9b1c029280b5e0a71c89c5f7180386ef"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/sr-cyrl.js",
  "revision": "3dbdeb11141d10d9c9e822a82ec3c5bd"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/sr.js",
  "revision": "e1717b43e0b03035a76803733c0502be"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/sv.js",
  "revision": "420404cb45137c7bc8e05cb2fadc8c2e"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/th.js",
  "revision": "6f333da064dee1aae8ed587329324c29"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/tr.js",
  "revision": "759160f2f78b84f51820fc3a4f866889"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/uk.js",
  "revision": "97cf63b777614f0ccb6e05c083cf2b16"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/vi.js",
  "revision": "72dad7af17539b670e390ede1b8e3df6"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/zh-cn.js",
  "revision": "7b45b1072b1d5ba71a8f5d3e7ca350fd"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/zh-hk.js",
  "revision": "2291c08b7e8bad263e9d0f566c385a47"
}, {
  "url": "modules/Calendar4You/fullcalendar/locale/zh-tw.js",
  "revision": "300ef3323eea3f21cdce972384dab937"
}, {
  "url": "modules/Calendar4You/fullcalendar/themes/cupertino/images/animated-overlay.gif",
  "revision": "2b912f7c0653008ca28ebacda49025e7"
}, {
  "url": "modules/Calendar4You/fullcalendar/themes/cupertino/images/ui-bg_diagonals-thick_90_eeeeee_40x40.png",
  "revision": "254973041f2f3ff094034cf79e1dd669"
}, {
  "url": "modules/Calendar4You/fullcalendar/themes/cupertino/images/ui-bg_flat_15_cd0a0a_40x100.png",
  "revision": "4dd12b2014e983dcd555358219ce81d7"
}, {
  "url": "modules/Calendar4You/fullcalendar/themes/cupertino/images/ui-bg_glass_100_e4f1fb_1x400.png",
  "revision": "cec0b018d99ce30327d69d3c4faaca11"
}, {
  "url": "modules/Calendar4You/fullcalendar/themes/cupertino/images/ui-bg_glass_50_3baae3_1x400.png",
  "revision": "bb06e6a2f7440ca2a11050057fe7926d"
}, {
  "url": "modules/Calendar4You/fullcalendar/themes/cupertino/images/ui-bg_glass_80_d7ebf9_1x400.png",
  "revision": "286b528907dfbc11dc44a4cc89681635"
}, {
  "url": "modules/Calendar4You/fullcalendar/themes/cupertino/images/ui-bg_highlight-hard_100_f2f5f7_1x100.png",
  "revision": "dc0a7dd2653b260113c92c1936198b21"
}, {
  "url": "modules/Calendar4You/fullcalendar/themes/cupertino/images/ui-bg_highlight-hard_70_000000_1x100.png",
  "revision": "3ad15b0b6700a003dabeb7052e80be64"
}, {
  "url": "modules/Calendar4You/fullcalendar/themes/cupertino/images/ui-bg_highlight-soft_100_deedf7_1x100.png",
  "revision": "c04e9ad1e86ced01d2295fa5df2d7b56"
}, {
  "url": "modules/Calendar4You/fullcalendar/themes/cupertino/images/ui-bg_highlight-soft_25_ffef8f_1x100.png",
  "revision": "870e4da769784845bf381570ac584621"
}, {
  "url": "modules/Calendar4You/fullcalendar/themes/cupertino/images/ui-icons_2694e8_256x240.png",
  "revision": "a1ce3cc448b059968bb35b71a5c91874"
}, {
  "url": "modules/Calendar4You/fullcalendar/themes/cupertino/images/ui-icons_2e83ff_256x240.png",
  "revision": "db3b908bd060c6f278fde9e11b3b94e3"
}, {
  "url": "modules/Calendar4You/fullcalendar/themes/cupertino/images/ui-icons_3d80b3_256x240.png",
  "revision": "8370749a3e90577fdb876d72e6935f8e"
}, {
  "url": "modules/Calendar4You/fullcalendar/themes/cupertino/images/ui-icons_72a7cf_256x240.png",
  "revision": "505f6857cba9aad738957a7d3bb226a9"
}, {
  "url": "modules/Calendar4You/fullcalendar/themes/cupertino/images/ui-icons_ffffff_256x240.png",
  "revision": "2e2a588883eebc04ad50854a6ecfbac1"
}, {
  "url": "modules/Calendar4You/fullcalendar/themes/cupertino/jquery-ui.min.css",
  "revision": "c5518025b115c73bbeb9b8518ec0499b"
}, {
  "url": "modules/Calendar4You/fullcalendar/themes/cupertino/theme.css",
  "revision": "5e885f2a9f6890b2c8e5a041654d66cc"
}, {
  "url": "modules/Calendar4You/images/color_picker.gif",
  "revision": "765a90c24ad2f3969b53e542bdf2e32f"
}, {
  "url": "modules/Calendar4You/images/icon-left.png",
  "revision": "adee43d7f579110baaf8f9a67b51e995"
}, {
  "url": "modules/Calendar4You/images/icon-right.png",
  "revision": "c95c5cf609a39a56758307cd99b1f989"
}, {
  "url": "modules/Calendar4You/images/sync_icon_small.png",
  "revision": "ef6abcdd3e3b265354af8667d77c66e8"
}, {
  "url": "modules/Calendar4You/images/sync_icon_small2.png",
  "revision": "b583b2cbc0c3c0017a981611efc88c2e"
}, {
  "url": "modules/Calendar4You/images/sync_icon.png",
  "revision": "95a2902cd634118555aa10b92ee387f2"
}, {
  "url": "modules/Mobile/apple-touch-icon-precomposed.png",
  "revision": "89744856c32a6fa01ec83cf86b78f27f"
}, {
  "url": "modules/Mobile/apple-touch-icon.png",
  "revision": "89744856c32a6fa01ec83cf86b78f27f"
}, {
  "url": "modules/Mobile/resources/crmtogo.js",
  "revision": "ae01304427dd8dab540c2eec0aa80c11"
}, {
  "url": "modules/Mobile/resources/css/images/ui-bg_diagonals-thick_18_b81900_40x40.png",
  "revision": "e9c44fa4ccdc5d2d4c5e2bf65fc166aa"
}, {
  "url": "modules/Mobile/resources/css/images/ui-bg_diagonals-thick_20_666666_40x40.png",
  "revision": "9ae6fe136a5be5e41944408c245e4496"
}, {
  "url": "modules/Mobile/resources/css/images/ui-bg_flat_10_000000_40x100.png",
  "revision": "d37c642004b462889ab774e3fa9d1b82"
}, {
  "url": "modules/Mobile/resources/css/images/ui-bg_glass_100_f6f6f6_1x400.png",
  "revision": "becd0bda9ac1f3bdc1caae0d1e25469f"
}, {
  "url": "modules/Mobile/resources/css/images/ui-bg_glass_100_fdf5ce_1x400.png",
  "revision": "b9e2e007ea2f8410bd56d6caaf5920b1"
}, {
  "url": "modules/Mobile/resources/css/images/ui-bg_glass_65_ffffff_1x400.png",
  "revision": "04b555466770e66a3ed8c6fc2c7bf897"
}, {
  "url": "modules/Mobile/resources/css/images/ui-bg_gloss-wave_35_f6a828_500x100.png",
  "revision": "2d40290132e66b322ff28f9197af0a71"
}, {
  "url": "modules/Mobile/resources/css/images/ui-bg_highlight-soft_100_eeeeee_1x100.png",
  "revision": "67ce8280f78b7043b7c096432450afbd"
}, {
  "url": "modules/Mobile/resources/css/images/ui-bg_highlight-soft_75_ffe45c_1x100.png",
  "revision": "f81da2f75a8967177352a62872e53bcd"
}, {
  "url": "modules/Mobile/resources/css/images/ui-icons_222222_256x240.png",
  "revision": "b8f844f3f13060d832e089c783435683"
}, {
  "url": "modules/Mobile/resources/css/images/ui-icons_228ef1_256x240.png",
  "revision": "464fab5837b02773a19e770bd2670932"
}, {
  "url": "modules/Mobile/resources/css/images/ui-icons_ef8c08_256x240.png",
  "revision": "f5912e64e68ca6c6d6be3f8186a62607"
}, {
  "url": "modules/Mobile/resources/css/images/ui-icons_ffd27a_256x240.png",
  "revision": "b81d996790dc58a0a5477f756a9dff04"
}, {
  "url": "modules/Mobile/resources/css/images/ui-icons_ffffff_256x240.png",
  "revision": "89c7f81db0a270cbc7cf8a9fb5749595"
}, {
  "url": "modules/Mobile/resources/css/jquery-ui.min.css",
  "revision": "e56443e3a7143d279057f675dc25d115"
}, {
  "url": "modules/Mobile/resources/css/jquery.mobile-1.4.5.min.css",
  "revision": "b835b04bbff5a8020c31ce21714e389b"
}, {
  "url": "modules/Mobile/resources/css/jquery.mobile.icons.min.css",
  "revision": "1299bcc0c86b9c76f6c8730d3ea5a8ae"
}, {
  "url": "modules/Mobile/resources/css/jquery.mobile.structure-1.4.5.min.css",
  "revision": "1544ca9f803edff31be0577b9f985853"
}, {
  "url": "modules/Mobile/resources/css/jw-jqm-cal.css",
  "revision": "7546978657cc41eac63743013463b106"
}, {
  "url": "modules/Mobile/resources/css/jw-jqm-cal.ios7.css",
  "revision": "bca9e7f6e3bdbf2179ef949e6cbac603"
}, {
  "url": "modules/Mobile/resources/css/mobiscroll.custom-2.6.2.min.css",
  "revision": "29c9b4f741888f6cf71e27c434e4c362"
}, {
  "url": "modules/Mobile/resources/css/signature-pad.css",
  "revision": "53beeb9c71b1a21aa389919c8c32b7b9"
}, {
  "url": "modules/Mobile/resources/css/style.css",
  "revision": "0f03bf37b48e916eedc374fc7181dd7a"
}, {
  "url": "modules/Mobile/resources/css/theme.css",
  "revision": "3ad2cc4a1e7c3b6c566726883e414b15"
}, {
  "url": "modules/Mobile/resources/css/tinyselect.css",
  "revision": "df87e74765c600edb73159d1dfd5b9ab"
}, {
  "url": "modules/Mobile/resources/documents.js",
  "revision": "351e44a8f5939b2fa6d5c498a65bcdc9"
}, {
  "url": "modules/Mobile/resources/getScrollcontent.js",
  "revision": "6ac55c0e399455103faaa0704252b03b"
}, {
  "url": "modules/Mobile/resources/images/ajax-loader.gif",
  "revision": "8fd7e719b06cd3f701c791adb62bd7a6"
}, {
  "url": "modules/Mobile/resources/images/ajax-loader.png",
  "revision": "d66e82db53d9d7e63b00fb02a271dedd"
}, {
  "url": "modules/Mobile/resources/images/icons-18-black.png",
  "revision": "951fbc0ce7edbbee9137073b63a0e77b"
}, {
  "url": "modules/Mobile/resources/images/icons-18-white.png",
  "revision": "1c58818bbee0d727686b0995aecbde84"
}, {
  "url": "modules/Mobile/resources/images/icons-36-black.png",
  "revision": "149e75b7045d6c873b0408e91c2d3e5c"
}, {
  "url": "modules/Mobile/resources/images/icons-36-white.png",
  "revision": "8ad3678f02e860c055be0953d8e4bffe"
}, {
  "url": "modules/Mobile/resources/images/icons-png/action-black.png",
  "revision": "9a19edc87343cefa0ea5fbfc38c45b92"
}, {
  "url": "modules/Mobile/resources/images/icons-png/action-white.png",
  "revision": "13d2742979c0abdff486ffc0c2765efb"
}, {
  "url": "modules/Mobile/resources/images/icons-png/alert-black.png",
  "revision": "09364128a6be0cc59f1fc6e9fade366f"
}, {
  "url": "modules/Mobile/resources/images/icons-png/alert-white.png",
  "revision": "86373cf5fcb815be2adc0c06a87eb6f1"
}, {
  "url": "modules/Mobile/resources/images/icons-png/arrow-d-black.png",
  "revision": "f85e79a0dcf3d65491e6bb99b40c0fda"
}, {
  "url": "modules/Mobile/resources/images/icons-png/arrow-d-l-black.png",
  "revision": "27790e799f740daee527b1ca3c9971f9"
}, {
  "url": "modules/Mobile/resources/images/icons-png/arrow-d-l-white.png",
  "revision": "14b3bcde3ed10d0be18d5fcc90fe8ce0"
}, {
  "url": "modules/Mobile/resources/images/icons-png/arrow-d-r-black.png",
  "revision": "5bad1e7e859eb120f4d136af29084460"
}, {
  "url": "modules/Mobile/resources/images/icons-png/arrow-d-r-white.png",
  "revision": "fec8ef05dd2b57134a284515eb5ecabf"
}, {
  "url": "modules/Mobile/resources/images/icons-png/arrow-d-white.png",
  "revision": "a7ed65414584a456e4608c2bc3d85065"
}, {
  "url": "modules/Mobile/resources/images/icons-png/arrow-l-black.png",
  "revision": "ee7f9b8e2abb96a61fe8d4cf11ca7697"
}, {
  "url": "modules/Mobile/resources/images/icons-png/arrow-l-white.png",
  "revision": "434675e67d80715862db88c75a7df577"
}, {
  "url": "modules/Mobile/resources/images/icons-png/arrow-r-black.png",
  "revision": "d96c7bba4b98ec14e62790584b139a61"
}, {
  "url": "modules/Mobile/resources/images/icons-png/arrow-r-white.png",
  "revision": "34350abeb7bd36e979c0aa4d6e038d2d"
}, {
  "url": "modules/Mobile/resources/images/icons-png/arrow-u-black.png",
  "revision": "5e086bd389bca6a7793a8741a6c6fad3"
}, {
  "url": "modules/Mobile/resources/images/icons-png/arrow-u-l-black.png",
  "revision": "9891529976aef3fa1c23308dbbbe0485"
}, {
  "url": "modules/Mobile/resources/images/icons-png/arrow-u-l-white.png",
  "revision": "eb17742486f621a31bfb1aaabdc30d5c"
}, {
  "url": "modules/Mobile/resources/images/icons-png/arrow-u-r-black.png",
  "revision": "25267137bba768f6f2b595398c6a2b92"
}, {
  "url": "modules/Mobile/resources/images/icons-png/arrow-u-r-white.png",
  "revision": "ce2d1de04f61355443949d6061f4ea37"
}, {
  "url": "modules/Mobile/resources/images/icons-png/arrow-u-white.png",
  "revision": "9f6cd65e48648b4823e236b0da1e54b0"
}, {
  "url": "modules/Mobile/resources/images/icons-png/audio-black.png",
  "revision": "d3cfac47faf40513c646c1f16b087e88"
}, {
  "url": "modules/Mobile/resources/images/icons-png/audio-white.png",
  "revision": "7c90c384a65cbfef572bbdd02b9d8edb"
}, {
  "url": "modules/Mobile/resources/images/icons-png/back-black.png",
  "revision": "0759505d2298fdee60b52b5126dfcfc6"
}, {
  "url": "modules/Mobile/resources/images/icons-png/back-white.png",
  "revision": "e78ad3c61a492b120a7ba0a789d4b2e0"
}, {
  "url": "modules/Mobile/resources/images/icons-png/bars-black.png",
  "revision": "d638db196907b36c3e0bfefb8a698cc8"
}, {
  "url": "modules/Mobile/resources/images/icons-png/bars-white.png",
  "revision": "f4d15b9a0fdcf961fe8d749703b20f20"
}, {
  "url": "modules/Mobile/resources/images/icons-png/bullets-black.png",
  "revision": "63e8e96e2deb4d60b69a68d6d9765df8"
}, {
  "url": "modules/Mobile/resources/images/icons-png/bullets-white.png",
  "revision": "b74986306e8ee76bd1f2a4293d56c3c4"
}, {
  "url": "modules/Mobile/resources/images/icons-png/calendar-black.png",
  "revision": "9116cef9549b26ddc4d9e96bec5dfd41"
}, {
  "url": "modules/Mobile/resources/images/icons-png/calendar-white.png",
  "revision": "215a42df136361f8b54b056a0ca6ae15"
}, {
  "url": "modules/Mobile/resources/images/icons-png/camera-black.png",
  "revision": "434dcb1c736b2da8247a1e59372bc64b"
}, {
  "url": "modules/Mobile/resources/images/icons-png/camera-white.png",
  "revision": "054a64f6a2886570ed734a26a804a66a"
}, {
  "url": "modules/Mobile/resources/images/icons-png/carat-d-black.png",
  "revision": "9708c21592cabb6f7fe4272e6daa7853"
}, {
  "url": "modules/Mobile/resources/images/icons-png/carat-d-white.png",
  "revision": "52f8e9ceafe00b0360bce803f5236a0c"
}, {
  "url": "modules/Mobile/resources/images/icons-png/carat-l-black.png",
  "revision": "01df9e30c853da8996684cd08c3a7917"
}, {
  "url": "modules/Mobile/resources/images/icons-png/carat-l-white.png",
  "revision": "32a1036e056d5a5831f6e8d40d4d1faf"
}, {
  "url": "modules/Mobile/resources/images/icons-png/carat-r-black.png",
  "revision": "01945aeb9182966e0e02cd0cd2f74abd"
}, {
  "url": "modules/Mobile/resources/images/icons-png/carat-r-white.png",
  "revision": "41c4ab4735f66dd007c2689a87695863"
}, {
  "url": "modules/Mobile/resources/images/icons-png/carat-u-black.png",
  "revision": "76904bfc235fd12caacfc2858f8e1eef"
}, {
  "url": "modules/Mobile/resources/images/icons-png/carat-u-white.png",
  "revision": "3bde6d2e6ab2936a25b69767de4ac7c3"
}, {
  "url": "modules/Mobile/resources/images/icons-png/check-black.png",
  "revision": "358363d39df6c2d2e0afbad366b14231"
}, {
  "url": "modules/Mobile/resources/images/icons-png/check-white.png",
  "revision": "0bc57ed512131d2e4b507055552f7277"
}, {
  "url": "modules/Mobile/resources/images/icons-png/clock-black.png",
  "revision": "c92ab62b3c0ca2ca1ae11bcc940c20a6"
}, {
  "url": "modules/Mobile/resources/images/icons-png/clock-white.png",
  "revision": "44cffb967f09ddf5fb8d13380745f273"
}, {
  "url": "modules/Mobile/resources/images/icons-png/cloud-black.png",
  "revision": "c0c7bf5a98f76f252d14f1af232a0ee1"
}, {
  "url": "modules/Mobile/resources/images/icons-png/cloud-white.png",
  "revision": "c71b429d726c0b8c94fc6dd33f885574"
}, {
  "url": "modules/Mobile/resources/images/icons-png/comment-black.png",
  "revision": "81a45d4b2b64e4321667542b75eb6754"
}, {
  "url": "modules/Mobile/resources/images/icons-png/comment-white.png",
  "revision": "0917e96ac998c0d191d7b81d880927a9"
}, {
  "url": "modules/Mobile/resources/images/icons-png/delete-black.png",
  "revision": "fb456b3f7f0b805ac7be21d97b443f9a"
}, {
  "url": "modules/Mobile/resources/images/icons-png/delete-white.png",
  "revision": "478fa064c1e2234032e7a3de1884f4ed"
}, {
  "url": "modules/Mobile/resources/images/icons-png/edit-black.png",
  "revision": "3bed8f0eeea77c23adcce4870c391286"
}, {
  "url": "modules/Mobile/resources/images/icons-png/edit-white.png",
  "revision": "a41a9a4e6b71ae9829dd8fa24e695be9"
}, {
  "url": "modules/Mobile/resources/images/icons-png/eye-black.png",
  "revision": "03ce5e4016e1f8ab8d7b02a72d45e600"
}, {
  "url": "modules/Mobile/resources/images/icons-png/eye-white.png",
  "revision": "0bf7b7e9cb0aee2da885a86629744cf2"
}, {
  "url": "modules/Mobile/resources/images/icons-png/forbidden-black.png",
  "revision": "fcf54d3cda520f292d34592d4ae6d9ae"
}, {
  "url": "modules/Mobile/resources/images/icons-png/forbidden-white.png",
  "revision": "d124846cb27f0a6e07764e114895e335"
}, {
  "url": "modules/Mobile/resources/images/icons-png/forward-black.png",
  "revision": "54bf8c0856e1a1b2b18fbf8161d8dadf"
}, {
  "url": "modules/Mobile/resources/images/icons-png/forward-white.png",
  "revision": "486c47d6f12f7872c04a16a28f7ae6c3"
}, {
  "url": "modules/Mobile/resources/images/icons-png/gear-black.png",
  "revision": "957fed4d5d46498e93cb74af8384f4b2"
}, {
  "url": "modules/Mobile/resources/images/icons-png/gear-white.png",
  "revision": "592af245bec551ffa61392b9e363c8ee"
}, {
  "url": "modules/Mobile/resources/images/icons-png/grid-black.png",
  "revision": "536fe23332253922017d3145f06258a6"
}, {
  "url": "modules/Mobile/resources/images/icons-png/grid-white.png",
  "revision": "ab90c7666595f04b9374518ece4fd0e2"
}, {
  "url": "modules/Mobile/resources/images/icons-png/heart-black.png",
  "revision": "7e2aaea1b42b5d462a7d098d5814433a"
}, {
  "url": "modules/Mobile/resources/images/icons-png/heart-white.png",
  "revision": "86b007f62248a968255c50b3d5c0e696"
}, {
  "url": "modules/Mobile/resources/images/icons-png/home-black.png",
  "revision": "3ef58401159dce7cdb4ca66bd1e1c475"
}, {
  "url": "modules/Mobile/resources/images/icons-png/home-white.png",
  "revision": "1c80eb5b00855d8494116db68af3242c"
}, {
  "url": "modules/Mobile/resources/images/icons-png/info-black.png",
  "revision": "ecc9460bc8b0a3de72a6638c8fc39d36"
}, {
  "url": "modules/Mobile/resources/images/icons-png/info-white.png",
  "revision": "a776b029342f4ef75889d2b9853a0e59"
}, {
  "url": "modules/Mobile/resources/images/icons-png/location-black.png",
  "revision": "173cf9e0989ea6f0bb6254a1fc0334ab"
}, {
  "url": "modules/Mobile/resources/images/icons-png/location-white.png",
  "revision": "359f3b2435bb7ea11c9b62f46d712b2f"
}, {
  "url": "modules/Mobile/resources/images/icons-png/lock-black.png",
  "revision": "28a57a211fa4c6a69055a91cd3e2b688"
}, {
  "url": "modules/Mobile/resources/images/icons-png/lock-white.png",
  "revision": "827adbd30b32a8089a39bd5a40d956ca"
}, {
  "url": "modules/Mobile/resources/images/icons-png/mail-black.png",
  "revision": "13106c83b8c7a9e78e5d0fbcf275f027"
}, {
  "url": "modules/Mobile/resources/images/icons-png/mail-white.png",
  "revision": "006089860dcf971fe6f65ec3ad289e3a"
}, {
  "url": "modules/Mobile/resources/images/icons-png/minus-black.png",
  "revision": "92cc19063926bda68541c1c6213e0637"
}, {
  "url": "modules/Mobile/resources/images/icons-png/minus-white.png",
  "revision": "9e2ff829356531c31e954eb48d69b1c5"
}, {
  "url": "modules/Mobile/resources/images/icons-png/navigation-black.png",
  "revision": "f36cc2c09123d935278e9fdbe1722769"
}, {
  "url": "modules/Mobile/resources/images/icons-png/navigation-white.png",
  "revision": "59bdad3cbad70b98a5580f59f4b0f89d"
}, {
  "url": "modules/Mobile/resources/images/icons-png/phone-black.png",
  "revision": "c78bd6ae6d2074f201518d5e504120d9"
}, {
  "url": "modules/Mobile/resources/images/icons-png/phone-white.png",
  "revision": "3f351a2cf1b17acf767294695eb9a825"
}, {
  "url": "modules/Mobile/resources/images/icons-png/plus-black.png",
  "revision": "96410e386e61459b3bf045ae72449b72"
}, {
  "url": "modules/Mobile/resources/images/icons-png/plus-white.png",
  "revision": "d8256afa69d9ed42bdbeb1232acddc0e"
}, {
  "url": "modules/Mobile/resources/images/icons-png/power-black.png",
  "revision": "d9a9cd79c18b61953483b15e78b7b6b6"
}, {
  "url": "modules/Mobile/resources/images/icons-png/power-white.png",
  "revision": "4e785618f27780944e6d8a13fee251b0"
}, {
  "url": "modules/Mobile/resources/images/icons-png/recycle-black.png",
  "revision": "04ae75ab4410ec64093da3b298fef31e"
}, {
  "url": "modules/Mobile/resources/images/icons-png/recycle-white.png",
  "revision": "8a46b6ed030cee2db774928b81d1e6e3"
}, {
  "url": "modules/Mobile/resources/images/icons-png/refresh-black.png",
  "revision": "1da2deb97177b5676c80be327ddc82e3"
}, {
  "url": "modules/Mobile/resources/images/icons-png/refresh-white.png",
  "revision": "705e7dd6e46b24381e9d123be4721787"
}, {
  "url": "modules/Mobile/resources/images/icons-png/search-black.png",
  "revision": "8fdc32864a50e0359972f7caaa6a4fac"
}, {
  "url": "modules/Mobile/resources/images/icons-png/search-white.png",
  "revision": "615d54abf8ffe2159c6418996e73b86f"
}, {
  "url": "modules/Mobile/resources/images/icons-png/shop-black.png",
  "revision": "34776eb5710390641a48b2ef933b42d8"
}, {
  "url": "modules/Mobile/resources/images/icons-png/shop-white.png",
  "revision": "bed77b8b0aa66b98bb2c53d5ace2d736"
}, {
  "url": "modules/Mobile/resources/images/icons-png/star-black.png",
  "revision": "741986dbcdfb3f8e4b86a58a5de62b4e"
}, {
  "url": "modules/Mobile/resources/images/icons-png/star-white.png",
  "revision": "f62c7807aed9d236a22b8672290f845d"
}, {
  "url": "modules/Mobile/resources/images/icons-png/tag-black.png",
  "revision": "d5fc58dc0ecabd4e37cb41e2a8c6f871"
}, {
  "url": "modules/Mobile/resources/images/icons-png/tag-white.png",
  "revision": "63d500360386f0352234ea160a235650"
}, {
  "url": "modules/Mobile/resources/images/icons-png/user-black.png",
  "revision": "72109232660715674c269a748b6d3b94"
}, {
  "url": "modules/Mobile/resources/images/icons-png/user-white.png",
  "revision": "291b0ebdb48850f539026ccd24ade8ff"
}, {
  "url": "modules/Mobile/resources/images/icons-png/video-black.png",
  "revision": "3e9650ab48d52565ff42b9f67e1ea617"
}, {
  "url": "modules/Mobile/resources/images/icons-png/video-white.png",
  "revision": "d180c9f44b809cd008ea4c32a4450bd2"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/action-black.svg",
  "revision": "67275db7ead5c13b17248764737e1941"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/action-white.svg",
  "revision": "797865c23a9a7fd58f879c06ea5f3373"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/alert-black.svg",
  "revision": "36f5ec26786ba00eb754c08e54482a45"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/alert-white.svg",
  "revision": "62b7c0f9ef04d0e883874cd17beb67f9"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/arrow-d-black.svg",
  "revision": "8f16783cae829210adb04701c7c56e8a"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/arrow-d-l-black.svg",
  "revision": "ee21b9e7833a9e2b379860f3a618b1e3"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/arrow-d-l-white.svg",
  "revision": "01a113d82aba147674d5c3f02a964d62"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/arrow-d-r-black.svg",
  "revision": "97a158a3980bc8a4ec4fb17d2f73a350"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/arrow-d-r-white.svg",
  "revision": "0a090129d1927e101076a432d621a6d7"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/arrow-d-white.svg",
  "revision": "6edf191c7866180081b7eefedb0dba70"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/arrow-l-black.svg",
  "revision": "c8dbdfc79c8e80d1ea94b87aa3912b8e"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/arrow-l-white.svg",
  "revision": "359f958a031112b9520608597719d379"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/arrow-r-black.svg",
  "revision": "00df078716e101e98d97f3716cc08ada"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/arrow-r-white.svg",
  "revision": "8185b003c91161784c2f459cd1841653"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/arrow-u-black.svg",
  "revision": "892fff5096355d1d137d604b2cd59a60"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/arrow-u-l-black.svg",
  "revision": "1db1daead125330f1a7bf3e7fe72f275"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/arrow-u-l-white.svg",
  "revision": "fad90887ad3b93dd743c3fe55ab10d17"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/arrow-u-r-black.svg",
  "revision": "0eac866257c85e1d8b09b315337a2b07"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/arrow-u-r-white.svg",
  "revision": "1a8f30eea2937a267e65d0074db54f15"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/arrow-u-white.svg",
  "revision": "ec482d18e3e5eca475988f736385f83b"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/audio-black.svg",
  "revision": "bf7400a0a0aeb0bef6a40d17cf542c1e"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/audio-white.svg",
  "revision": "79fe8db54c1bf84120a2d83a3321809f"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/back-black.svg",
  "revision": "299d8fddcf7aefe01fcda1e3d36c539e"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/back-white.svg",
  "revision": "28b170be258eb94dd59a5aebede55ca0"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/bars-black.svg",
  "revision": "30b70e23bcdb1582a62d0ea37518c218"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/bars-white.svg",
  "revision": "b28b198349a23f1bb70adfd3c3928bec"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/bullets-black.svg",
  "revision": "8592b97a7ff92d49c4a4500825c38a24"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/bullets-white.svg",
  "revision": "28e926450152e985c4373e1573a62011"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/calendar-black.svg",
  "revision": "afb432ae012246d38cf48b40b75b4661"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/calendar-white.svg",
  "revision": "683b7091302672384f03633fae4cf8e0"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/camera-black.svg",
  "revision": "bc31967f8a33cfbcbb1283910848f3d2"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/camera-white.svg",
  "revision": "07c295ce297a2c08d81b85982bb7f5ce"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/carat-d-black.svg",
  "revision": "ca571e71da5ea3b3aa366f4565c101b4"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/carat-d-white.svg",
  "revision": "44fe6b4ec96e6f324e23fc6d6906fc92"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/carat-l-black.svg",
  "revision": "0fd1f5d1dd111f9a39d2b12d626a9538"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/carat-l-white.svg",
  "revision": "c7f967d6d7d2cd246d3313737fb609a5"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/carat-r-black.svg",
  "revision": "bad5e7f56576d11e67fb476dfc16a413"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/carat-r-white.svg",
  "revision": "3afee5c3be598fd31a861956308085df"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/carat-u-black.svg",
  "revision": "5c696a49dfe8cdc1d944ff23c4ce45fa"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/carat-u-white.svg",
  "revision": "2be4a9d2a9193bcdbcd53fa75130589b"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/check-black.svg",
  "revision": "821dc8547ced9cc0698d0dd4ed9d06e3"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/check-white.svg",
  "revision": "88eb12a3c79959eeb743b48b16d511ab"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/clock-black.svg",
  "revision": "009a91daa40bc2d5f8814d653f8434ad"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/clock-white.svg",
  "revision": "56cf9aa2dc1c162096903ac41dff13fa"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/cloud-black.svg",
  "revision": "b5fa9f83217d9c7d8a9b260f8702aece"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/cloud-white.svg",
  "revision": "97cb950227497b3ce36f0d342e6dab86"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/comment-black.svg",
  "revision": "1bebdcbea0e1aa92b8a003f25dae320c"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/comment-white.svg",
  "revision": "1c88d821a8088bf13e11c42f51a2cb79"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/delete-black.svg",
  "revision": "a47df270172eb9e4aa2f53a8d49a747c"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/delete-white.svg",
  "revision": "1545461b20b7e130257e9d8a3fbbe9fe"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/edit-black.svg",
  "revision": "aa68f894234d3877204b56a1f29f56df"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/edit-white.svg",
  "revision": "6ef800ecc6836e5572f29941e2c94883"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/eye-black.svg",
  "revision": "4b5188538a87aff30ca0a8ae073448bf"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/eye-white.svg",
  "revision": "815468019d233b42706fe5accb6d4634"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/forbidden-black.svg",
  "revision": "0f1f6127b30576bef03089ed4a54963c"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/forbidden-white.svg",
  "revision": "bf4eb7e1a4f5a7f2eb92afa3c0caa5cf"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/forward-black.svg",
  "revision": "93d89c376ee9fc61eaddc8acc88f79bf"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/forward-white.svg",
  "revision": "7dce88ed7e26c973e49d6b5d63c69438"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/gear-black.svg",
  "revision": "83717679bfeec2bac44f61bf2c2fdb07"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/gear-white.svg",
  "revision": "d973b195dd62c5f30900ec2ddbeeaf15"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/grid-black.svg",
  "revision": "729b4b585cd8008e420b9c7288d473f4"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/grid-white.svg",
  "revision": "631da228c4a3f8e9cee2b8bdb16f99c7"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/heart-black.svg",
  "revision": "8946674e6081a099c5003e12d014b727"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/heart-white.svg",
  "revision": "7d8835566b2c5181f98cc3619317d2d2"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/home-black.svg",
  "revision": "c0f3930a0ba1e46c3aedb4d422dc674d"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/home-white.svg",
  "revision": "315a466d71a7357747511dec557827b4"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/info-black.svg",
  "revision": "293e7f6124a6f6c8e57d94523d9b3aaa"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/info-white.svg",
  "revision": "31af5be157e2fa395cd31cdf2cfb2d19"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/location-black.svg",
  "revision": "f9f9c4a0b82be059f8ea20f3e5324a84"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/location-white.svg",
  "revision": "5e8eefb85b6d22fe477b32bd82a8376a"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/lock-black.svg",
  "revision": "dcc507ffc396bc2a851fe4d10cffe207"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/lock-white.svg",
  "revision": "7b91f4c76a72fa47314a629de5afd2d9"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/mail-black.svg",
  "revision": "72a01ba4327cf1f0b14738d9d6950da6"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/mail-white.svg",
  "revision": "4e89f20bc80df577d2b306afbe3da21f"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/minus-black.svg",
  "revision": "c8a533902d2d8346b16ec3056a24215d"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/minus-white.svg",
  "revision": "51ad8ba2fffae02ba35ea6d732fdf49c"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/navigation-black.svg",
  "revision": "65a0b299b9ea5401717848c88520ea26"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/navigation-white.svg",
  "revision": "37eb89d6300e305be01a560fcecc551f"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/phone-black.svg",
  "revision": "f62c567527b2b5e0a346b831a8731de6"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/phone-white.svg",
  "revision": "bee327d659d00c409047ca9004978844"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/plus-black.svg",
  "revision": "242deb48861062f78e2cc4c19f9cfe75"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/plus-white.svg",
  "revision": "472f41b23eed8edfcd9524a02f6fd3b1"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/power-black.svg",
  "revision": "01f5e002762fbd375ca25f096b0a459f"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/power-white.svg",
  "revision": "2efec04180b4d9b8933bbe692b1abdf8"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/recycle-black.svg",
  "revision": "42d586529aff8f677f93c2bf75f9f10c"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/recycle-white.svg",
  "revision": "bcf1c2ef6444eff7e84138ba066c6419"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/refresh-black.svg",
  "revision": "111f67f8800111970f0684f37de60d28"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/refresh-white.svg",
  "revision": "d17451ae1ab9ce9b60b10cae760c5de1"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/search-black.svg",
  "revision": "a33a2d282639e95496f7b0ba743b8dd3"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/search-white.svg",
  "revision": "67ec6b224fd71699bad087b88fa3884b"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/shop-black.svg",
  "revision": "dd3498094e4587df4d2f93f46fbfb56d"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/shop-white.svg",
  "revision": "35c5498ca57890e0763d000147e39dca"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/star-black.svg",
  "revision": "028a29b556652a25dc9f6713c8ead3a7"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/star-white.svg",
  "revision": "cc0a9ecae004d7ae84945a1417cdc356"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/tag-black.svg",
  "revision": "2f00ae8c1629d9b78e8b0a3a6e524aa5"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/tag-white.svg",
  "revision": "c1bd8cf3fa1f28b8b12aafa9ebcc41ec"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/user-black.svg",
  "revision": "b1450949596cf32dbd0677dea1501b85"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/user-white.svg",
  "revision": "7dab217ef0edb74466854f49a5e7aebd"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/video-black.svg",
  "revision": "406a4bc63094ceb5e3655c8f28318c88"
}, {
  "url": "modules/Mobile/resources/images/icons-svg/video-white.svg",
  "revision": "322d59e56994f993a42bcede51344a16"
}, {
  "url": "modules/Mobile/resources/images/images.png",
  "revision": "d4b4025f9b8d1d31c3ad36b363e475af"
}, {
  "url": "modules/Mobile/resources/jquery-1.11.2.min.js",
  "revision": "5790ead7ad3ba27397aedfa3d263b867"
}, {
  "url": "modules/Mobile/resources/jquery-ui.js",
  "revision": "b5f3656496ccb995aacdccc0e91437c2"
}, {
  "url": "modules/Mobile/resources/jquery-ui.min.js",
  "revision": "870b75c273a97501e7d1fb27776bafd0"
}, {
  "url": "modules/Mobile/resources/jquery.blockUI.js",
  "revision": "5c98c0cbfacee6dab0783112cb0e233d"
}, {
  "url": "modules/Mobile/resources/jquery.easing-1.3.min.js",
  "revision": "07e36bf95f7c90e4b47c66b2d4311be3"
}, {
  "url": "modules/Mobile/resources/jquery.mobile-1.4.5.min.js",
  "revision": "fccb67b52239d374598b00ad388015c9"
}, {
  "url": "modules/Mobile/resources/jw-jqm-cal.js",
  "revision": "d44637254943525f26e61bf555301c81"
}, {
  "url": "modules/Mobile/resources/lang/de_de.lang.js",
  "revision": "ae79d7d7a78e70cbb6045300b9b69047"
}, {
  "url": "modules/Mobile/resources/lang/en_gb.lang.js",
  "revision": "ae989aa845cd40bce24cadefa3363b52"
}, {
  "url": "modules/Mobile/resources/lang/en_us.lang.js",
  "revision": "ae989aa845cd40bce24cadefa3363b52"
}, {
  "url": "modules/Mobile/resources/lang/es_es.lang.js",
  "revision": "f657034c4a672e24b6f1d61ae7c04bbc"
}, {
  "url": "modules/Mobile/resources/lang/es_mx.lang.js",
  "revision": "f657034c4a672e24b6f1d61ae7c04bbc"
}, {
  "url": "modules/Mobile/resources/lang/fr_fr.lang.js",
  "revision": "6b620d239f6e3a12a9484b33213ee4f1"
}, {
  "url": "modules/Mobile/resources/lang/nl_nl.lang.js",
  "revision": "113dffdaddb2cf254d0879d0402349aa"
}, {
  "url": "modules/Mobile/resources/lang/pt_br.lang.js",
  "revision": "84613dd324df8bf134a0f41941757cd7"
}, {
  "url": "modules/Mobile/resources/lang/ro_ro.lang.js",
  "revision": "ae989aa845cd40bce24cadefa3363b52"
}, {
  "url": "modules/Mobile/resources/settings.js",
  "revision": "7fab3007e1698eeb90371ff591b7f77c"
}, {
  "url": "modules/Mobile/resources/signature_pad.js",
  "revision": "7314c126d7a2316ca8e00a1d405d0771"
}, {
  "url": "modules/Mobile/resources/xdate.js",
  "revision": "12342c1101cdbfa8a77a9d022b50da24"
}, {
  "url": "kcfinder/adapters/jquery-min.js",
  "revision": "7d737fc770c43a38d2b9026fe03c8608"
}, {
  "url": "kcfinder/adapters/jquery.js",
  "revision": "f246c9d6f4b280dc6bef646b6662c379"
}, {
  "url": "kcfinder/css/000.base.css",
  "revision": "7a3956ddf686d2f7051de54b2c887374"
}, {
  "url": "kcfinder/css/001.transForm.css",
  "revision": "d8768f59df1cc93059944053645ea348"
}, {
  "url": "kcfinder/css/999.agent.css",
  "revision": "443a85f2f65df5dea35f3e01d727d99e"
}, {
  "url": "kcfinder/js/000._jquery.js",
  "revision": "52d16e147b5346147d0f3269cd4d0f80"
}, {
  "url": "kcfinder/js/002._jqueryui.js",
  "revision": "4549e5882cbcd79619a49e9b1bf3ae8d"
}, {
  "url": "kcfinder/js/006.jquery.transForm.js",
  "revision": "50ba6cfcd46c88ad381778dac4d331a2"
}, {
  "url": "kcfinder/js/010.jquery.fixes.js",
  "revision": "f7459e62cdff631529c5dca3d663cdf0"
}, {
  "url": "kcfinder/js/020.jquery.rightClick.js",
  "revision": "31d0d6b98ae067f2a363568f6b043982"
}, {
  "url": "kcfinder/js/021.jquery.taphold.js",
  "revision": "b3274f0bcf212a23db9f36bd0b64bd3d"
}, {
  "url": "kcfinder/js/022.jquery.shDropUpload.js",
  "revision": "3e420181dcb76e2a862a39652cb2a049"
}, {
  "url": "kcfinder/js/029.jquery.agent.js",
  "revision": "d7998efe21a6669340eefdbef2883db6"
}, {
  "url": "kcfinder/js/030.jquery.helper.js",
  "revision": "e4bb3b90303fd6e48bf4ae178b6f1166"
}, {
  "url": "kcfinder/js/031.jquery.md5.js",
  "revision": "b4c1ed5bfbb1f378ed384e5a3d687e26"
}, {
  "url": "kcfinder/js/040.object.js",
  "revision": "61728f2ca17836a38769a43d3510306a"
}, {
  "url": "kcfinder/js/041.dialogs.js",
  "revision": "2fbfe8a703357d196bf4fdbcc94f087b"
}, {
  "url": "kcfinder/js/050.init.js",
  "revision": "44c944bcfaf558a81a0eeeb0320a21f8"
}, {
  "url": "kcfinder/js/060.toolbar.js",
  "revision": "ea2835310ebd4d9a2f99feb7faae96a2"
}, {
  "url": "kcfinder/js/070.settings.js",
  "revision": "6cd7eb37f5e99b73e7fb218c47e7fbbe"
}, {
  "url": "kcfinder/js/080.files.js",
  "revision": "7bbaebf73318804843e1bc3f6e8183a5"
}, {
  "url": "kcfinder/js/090.folders.js",
  "revision": "b099f2ce3c59837d5ede55aefcc5234b"
}, {
  "url": "kcfinder/js/091.menus.js",
  "revision": "076c69969f3ac063403dd46ea351d355"
}, {
  "url": "kcfinder/js/091.viewImage.js",
  "revision": "dada4c8cf4b96f73e93a52e30c78cc6e"
}, {
  "url": "kcfinder/js/100.clipboard.js",
  "revision": "928e545e384b71fcd7f9437a23cdbd3e"
}, {
  "url": "kcfinder/js/110.dropUpload.js",
  "revision": "53c4b90e35b921772c0f8897bf2b01f2"
}, {
  "url": "kcfinder/js/120.misc.js",
  "revision": "6c691ef6219b5162535378fbfa3f0adb"
}, {
  "url": "kcfinder/themes/dark/01.ui.css",
  "revision": "5525d1c384994f07d3cc92b3d12c5c4a"
}, {
  "url": "kcfinder/themes/dark/02.transForm.css",
  "revision": "731b4889bbba5d4bc6d728e42a6ca7a8"
}, {
  "url": "kcfinder/themes/dark/03.misc.css",
  "revision": "2ebf2f61c4e968870f5cadced53ba487"
}, {
  "url": "kcfinder/themes/dark/img/bg_transparent.png",
  "revision": "7c85f000c77022603d72e8ef976756ac"
}, {
  "url": "kcfinder/themes/dark/img/files/big/avi.png",
  "revision": "b796cb8587ce723d48ea5d24d9dd0d51"
}, {
  "url": "kcfinder/themes/dark/img/files/big/bat.png",
  "revision": "3db21e1a732089979b26986402d49cd0"
}, {
  "url": "kcfinder/themes/dark/img/files/big/bmp.png",
  "revision": "bfa41aea0226a49465cf488651de4366"
}, {
  "url": "kcfinder/themes/dark/img/files/big/bz2.png",
  "revision": "a0554ca9d3746f639122d7d4999b2046"
}, {
  "url": "kcfinder/themes/dark/img/files/big/ccd.png",
  "revision": "61611e0e4071e8a80e44114d19a59cfa"
}, {
  "url": "kcfinder/themes/dark/img/files/big/cgi.png",
  "revision": "3db21e1a732089979b26986402d49cd0"
}, {
  "url": "kcfinder/themes/dark/img/files/big/com.png",
  "revision": "ef7cb6752a845c2038ae752f54713883"
}, {
  "url": "kcfinder/themes/dark/img/files/big/csh.png",
  "revision": "3db21e1a732089979b26986402d49cd0"
}, {
  "url": "kcfinder/themes/dark/img/files/big/cue.png",
  "revision": "61611e0e4071e8a80e44114d19a59cfa"
}, {
  "url": "kcfinder/themes/dark/img/files/big/deb.png",
  "revision": "0027b959f2b82f90e75d25cf5b2bf6a5"
}, {
  "url": "kcfinder/themes/dark/img/files/big/dll.png",
  "revision": "7a71e998c9348fa02e636a8adf58cde7"
}, {
  "url": "kcfinder/themes/dark/img/files/big/doc.png",
  "revision": "26a06a2ff6098b8437685bc283c2041c"
}, {
  "url": "kcfinder/themes/dark/img/files/big/docx.png",
  "revision": "26a06a2ff6098b8437685bc283c2041c"
}, {
  "url": "kcfinder/themes/dark/img/files/big/exe.png",
  "revision": "ef7cb6752a845c2038ae752f54713883"
}, {
  "url": "kcfinder/themes/dark/img/files/big/fla.png",
  "revision": "f353e9fa95f7d30af29f136dadc5d94e"
}, {
  "url": "kcfinder/themes/dark/img/files/big/flv.png",
  "revision": "b796cb8587ce723d48ea5d24d9dd0d51"
}, {
  "url": "kcfinder/themes/dark/img/files/big/fon.png",
  "revision": "2d4004d2af5eb5fd3a4801c41a1a2a8d"
}, {
  "url": "kcfinder/themes/dark/img/files/big/gif.png",
  "revision": "bfa41aea0226a49465cf488651de4366"
}, {
  "url": "kcfinder/themes/dark/img/files/big/gz.png",
  "revision": "a0554ca9d3746f639122d7d4999b2046"
}, {
  "url": "kcfinder/themes/dark/img/files/big/htm.png",
  "revision": "54d3b4a0d20897276af9b467d2c014fe"
}, {
  "url": "kcfinder/themes/dark/img/files/big/html.png",
  "revision": "54d3b4a0d20897276af9b467d2c014fe"
}, {
  "url": "kcfinder/themes/dark/img/files/big/ini.png",
  "revision": "34f6ef1f537af2513b90c5b98d0ace79"
}, {
  "url": "kcfinder/themes/dark/img/files/big/iso.png",
  "revision": "61611e0e4071e8a80e44114d19a59cfa"
}, {
  "url": "kcfinder/themes/dark/img/files/big/jar.png",
  "revision": "d1e7813a2e5b85e7d5a86df2998e7771"
}, {
  "url": "kcfinder/themes/dark/img/files/big/java.png",
  "revision": "e4de49aa3f9d254d1e8a149611444016"
}, {
  "url": "kcfinder/themes/dark/img/files/big/jpeg.png",
  "revision": "bfa41aea0226a49465cf488651de4366"
}, {
  "url": "kcfinder/themes/dark/img/files/big/jpg.png",
  "revision": "bfa41aea0226a49465cf488651de4366"
}, {
  "url": "kcfinder/themes/dark/img/files/big/js.png",
  "revision": "e2d486ea7e98654aad5cc4a69e9be65d"
}, {
  "url": "kcfinder/themes/dark/img/files/big/mds.png",
  "revision": "61611e0e4071e8a80e44114d19a59cfa"
}, {
  "url": "kcfinder/themes/dark/img/files/big/mdx.png",
  "revision": "61611e0e4071e8a80e44114d19a59cfa"
}, {
  "url": "kcfinder/themes/dark/img/files/big/mid.png",
  "revision": "8b63cd34121a5aa0810ab290274b19a8"
}, {
  "url": "kcfinder/themes/dark/img/files/big/midi.png",
  "revision": "8b63cd34121a5aa0810ab290274b19a8"
}, {
  "url": "kcfinder/themes/dark/img/files/big/mkv.png",
  "revision": "b796cb8587ce723d48ea5d24d9dd0d51"
}, {
  "url": "kcfinder/themes/dark/img/files/big/mov.png",
  "revision": "b796cb8587ce723d48ea5d24d9dd0d51"
}, {
  "url": "kcfinder/themes/dark/img/files/big/mp3.png",
  "revision": "0b301a786c0f3978fe05927f24928395"
}, {
  "url": "kcfinder/themes/dark/img/files/big/mp4.png",
  "revision": "b796cb8587ce723d48ea5d24d9dd0d51"
}, {
  "url": "kcfinder/themes/dark/img/files/big/mpeg.png",
  "revision": "b796cb8587ce723d48ea5d24d9dd0d51"
}, {
  "url": "kcfinder/themes/dark/img/files/big/mpg.png",
  "revision": "b796cb8587ce723d48ea5d24d9dd0d51"
}, {
  "url": "kcfinder/themes/dark/img/files/big/nfo.png",
  "revision": "34f6ef1f537af2513b90c5b98d0ace79"
}, {
  "url": "kcfinder/themes/dark/img/files/big/nrg.png",
  "revision": "61611e0e4071e8a80e44114d19a59cfa"
}, {
  "url": "kcfinder/themes/dark/img/files/big/ogg.png",
  "revision": "0b301a786c0f3978fe05927f24928395"
}, {
  "url": "kcfinder/themes/dark/img/files/big/pdf.png",
  "revision": "dbd20bfa045d965ad9ac071fb20def86"
}, {
  "url": "kcfinder/themes/dark/img/files/big/php.png",
  "revision": "4512f251e567191e1c6677901b2703ad"
}, {
  "url": "kcfinder/themes/dark/img/files/big/phps.png",
  "revision": "4512f251e567191e1c6677901b2703ad"
}, {
  "url": "kcfinder/themes/dark/img/files/big/pl.png",
  "revision": "9ad71d8ee41be29ab64b31c5e7096ea9"
}, {
  "url": "kcfinder/themes/dark/img/files/big/pm.png",
  "revision": "9ad71d8ee41be29ab64b31c5e7096ea9"
}, {
  "url": "kcfinder/themes/dark/img/files/big/png.png",
  "revision": "bfa41aea0226a49465cf488651de4366"
}, {
  "url": "kcfinder/themes/dark/img/files/big/ppt.png",
  "revision": "e638989e04132df68a63003fc835da6e"
}, {
  "url": "kcfinder/themes/dark/img/files/big/pptx.png",
  "revision": "e638989e04132df68a63003fc835da6e"
}, {
  "url": "kcfinder/themes/dark/img/files/big/psd.png",
  "revision": "bfa41aea0226a49465cf488651de4366"
}, {
  "url": "kcfinder/themes/dark/img/files/big/qt.png",
  "revision": "b796cb8587ce723d48ea5d24d9dd0d51"
}, {
  "url": "kcfinder/themes/dark/img/files/big/rar.png",
  "revision": "a0554ca9d3746f639122d7d4999b2046"
}, {
  "url": "kcfinder/themes/dark/img/files/big/rpm.png",
  "revision": "ff4d91ee2466c503fb51213d6dacee54"
}, {
  "url": "kcfinder/themes/dark/img/files/big/rtf.png",
  "revision": "34f6ef1f537af2513b90c5b98d0ace79"
}, {
  "url": "kcfinder/themes/dark/img/files/big/sh.png",
  "revision": "3db21e1a732089979b26986402d49cd0"
}, {
  "url": "kcfinder/themes/dark/img/files/big/sql.png",
  "revision": "9b458ee9f254709811c24e77291e2664"
}, {
  "url": "kcfinder/themes/dark/img/files/big/srt.png",
  "revision": "34f6ef1f537af2513b90c5b98d0ace79"
}, {
  "url": "kcfinder/themes/dark/img/files/big/sub.png",
  "revision": "34f6ef1f537af2513b90c5b98d0ace79"
}, {
  "url": "kcfinder/themes/dark/img/files/big/swf.png",
  "revision": "872bf413980a37c17eda80a1875fc860"
}, {
  "url": "kcfinder/themes/dark/img/files/big/tgz.png",
  "revision": "e7c32280bfe4992b469313584ec62610"
}, {
  "url": "kcfinder/themes/dark/img/files/big/tif.png",
  "revision": "bfa41aea0226a49465cf488651de4366"
}, {
  "url": "kcfinder/themes/dark/img/files/big/tiff.png",
  "revision": "bfa41aea0226a49465cf488651de4366"
}, {
  "url": "kcfinder/themes/dark/img/files/big/torrent.png",
  "revision": "de99ae3506fb97ac67e7e0893270a049"
}, {
  "url": "kcfinder/themes/dark/img/files/big/ttf.png",
  "revision": "640982ca75b713b4583aa58502f8ca8c"
}, {
  "url": "kcfinder/themes/dark/img/files/big/txt.png",
  "revision": "34f6ef1f537af2513b90c5b98d0ace79"
}, {
  "url": "kcfinder/themes/dark/img/files/big/wav.png",
  "revision": "0b301a786c0f3978fe05927f24928395"
}, {
  "url": "kcfinder/themes/dark/img/files/big/wma.png",
  "revision": "0b301a786c0f3978fe05927f24928395"
}, {
  "url": "kcfinder/themes/dark/img/files/big/xls.png",
  "revision": "513a56c3b24b93c29cb02c8f9a075cbd"
}, {
  "url": "kcfinder/themes/dark/img/files/big/xlsx.png",
  "revision": "513a56c3b24b93c29cb02c8f9a075cbd"
}, {
  "url": "kcfinder/themes/dark/img/files/big/zip.png",
  "revision": "a0554ca9d3746f639122d7d4999b2046"
}, {
  "url": "kcfinder/themes/dark/img/files/small/avi.png",
  "revision": "669214cffe7b913296246e90c372818e"
}, {
  "url": "kcfinder/themes/dark/img/files/small/bat.png",
  "revision": "f438e488078834934cd62c8c616c8bc7"
}, {
  "url": "kcfinder/themes/dark/img/files/small/bmp.png",
  "revision": "02709989af27569d20280bd017651195"
}, {
  "url": "kcfinder/themes/dark/img/files/small/bz2.png",
  "revision": "621703fa3710ba61b6eb05cf0f3c59b2"
}, {
  "url": "kcfinder/themes/dark/img/files/small/ccd.png",
  "revision": "11e31a8ab5d57e70cd1789cf1a13fd2c"
}, {
  "url": "kcfinder/themes/dark/img/files/small/cgi.png",
  "revision": "f438e488078834934cd62c8c616c8bc7"
}, {
  "url": "kcfinder/themes/dark/img/files/small/com.png",
  "revision": "86729340ae0d76d4fe823009991839a7"
}, {
  "url": "kcfinder/themes/dark/img/files/small/csh.png",
  "revision": "f438e488078834934cd62c8c616c8bc7"
}, {
  "url": "kcfinder/themes/dark/img/files/small/cue.png",
  "revision": "11e31a8ab5d57e70cd1789cf1a13fd2c"
}, {
  "url": "kcfinder/themes/dark/img/files/small/deb.png",
  "revision": "621703fa3710ba61b6eb05cf0f3c59b2"
}, {
  "url": "kcfinder/themes/dark/img/files/small/dll.png",
  "revision": "db49f2c0f68155c38201228411f2b87f"
}, {
  "url": "kcfinder/themes/dark/img/files/small/doc.png",
  "revision": "a441226ae0288bdfa96a1828cdabe8ab"
}, {
  "url": "kcfinder/themes/dark/img/files/small/docx.png",
  "revision": "a441226ae0288bdfa96a1828cdabe8ab"
}, {
  "url": "kcfinder/themes/dark/img/files/small/exe.png",
  "revision": "86729340ae0d76d4fe823009991839a7"
}, {
  "url": "kcfinder/themes/dark/img/files/small/fla.png",
  "revision": "600bd9e16bfa3083c2ff3db3f50615d5"
}, {
  "url": "kcfinder/themes/dark/img/files/small/flv.png",
  "revision": "669214cffe7b913296246e90c372818e"
}, {
  "url": "kcfinder/themes/dark/img/files/small/fon.png",
  "revision": "7078c74fee4a70390417c0e95962f3db"
}, {
  "url": "kcfinder/themes/dark/img/files/small/gif.png",
  "revision": "02709989af27569d20280bd017651195"
}, {
  "url": "kcfinder/themes/dark/img/files/small/gz.png",
  "revision": "621703fa3710ba61b6eb05cf0f3c59b2"
}, {
  "url": "kcfinder/themes/dark/img/files/small/htm.png",
  "revision": "8df519faea5f1a9b414325ac20a6ab39"
}, {
  "url": "kcfinder/themes/dark/img/files/small/html.png",
  "revision": "8df519faea5f1a9b414325ac20a6ab39"
}, {
  "url": "kcfinder/themes/dark/img/files/small/ini.png",
  "revision": "24e6b797e0a778e78b7ce2c6a0229baa"
}, {
  "url": "kcfinder/themes/dark/img/files/small/iso.png",
  "revision": "11e31a8ab5d57e70cd1789cf1a13fd2c"
}, {
  "url": "kcfinder/themes/dark/img/files/small/jar.png",
  "revision": "621703fa3710ba61b6eb05cf0f3c59b2"
}, {
  "url": "kcfinder/themes/dark/img/files/small/java.png",
  "revision": "cf43f436c57342e281b9edb8e1387424"
}, {
  "url": "kcfinder/themes/dark/img/files/small/jpeg.png",
  "revision": "02709989af27569d20280bd017651195"
}, {
  "url": "kcfinder/themes/dark/img/files/small/jpg.png",
  "revision": "02709989af27569d20280bd017651195"
}, {
  "url": "kcfinder/themes/dark/img/files/small/js.png",
  "revision": "7109810830148823eacaa7951904474e"
}, {
  "url": "kcfinder/themes/dark/img/files/small/mds.png",
  "revision": "11e31a8ab5d57e70cd1789cf1a13fd2c"
}, {
  "url": "kcfinder/themes/dark/img/files/small/mdx.png",
  "revision": "11e31a8ab5d57e70cd1789cf1a13fd2c"
}, {
  "url": "kcfinder/themes/dark/img/files/small/mid.png",
  "revision": "433e55560fceaf20721671bc16bac4d6"
}, {
  "url": "kcfinder/themes/dark/img/files/small/midi.png",
  "revision": "433e55560fceaf20721671bc16bac4d6"
}, {
  "url": "kcfinder/themes/dark/img/files/small/mkv.png",
  "revision": "669214cffe7b913296246e90c372818e"
}, {
  "url": "kcfinder/themes/dark/img/files/small/mov.png",
  "revision": "669214cffe7b913296246e90c372818e"
}, {
  "url": "kcfinder/themes/dark/img/files/small/mp3.png",
  "revision": "e2eb2abc9786a1f32cd9f3b93021d994"
}, {
  "url": "kcfinder/themes/dark/img/files/small/mp4.png",
  "revision": "669214cffe7b913296246e90c372818e"
}, {
  "url": "kcfinder/themes/dark/img/files/small/mpeg.png",
  "revision": "669214cffe7b913296246e90c372818e"
}, {
  "url": "kcfinder/themes/dark/img/files/small/mpg.png",
  "revision": "669214cffe7b913296246e90c372818e"
}, {
  "url": "kcfinder/themes/dark/img/files/small/nfo.png",
  "revision": "24e6b797e0a778e78b7ce2c6a0229baa"
}, {
  "url": "kcfinder/themes/dark/img/files/small/nrg.png",
  "revision": "11e31a8ab5d57e70cd1789cf1a13fd2c"
}, {
  "url": "kcfinder/themes/dark/img/files/small/ogg.png",
  "revision": "e2eb2abc9786a1f32cd9f3b93021d994"
}, {
  "url": "kcfinder/themes/dark/img/files/small/pdf.png",
  "revision": "e686949b4c31d52da8bbc964a9b5ee30"
}, {
  "url": "kcfinder/themes/dark/img/files/small/php.png",
  "revision": "fbd20eba7ecdab05a2395873cdc7c4f3"
}, {
  "url": "kcfinder/themes/dark/img/files/small/phps.png",
  "revision": "fbd20eba7ecdab05a2395873cdc7c4f3"
}, {
  "url": "kcfinder/themes/dark/img/files/small/pl.png",
  "revision": "24e6b797e0a778e78b7ce2c6a0229baa"
}, {
  "url": "kcfinder/themes/dark/img/files/small/pm.png",
  "revision": "24e6b797e0a778e78b7ce2c6a0229baa"
}, {
  "url": "kcfinder/themes/dark/img/files/small/png.png",
  "revision": "02709989af27569d20280bd017651195"
}, {
  "url": "kcfinder/themes/dark/img/files/small/ppt.png",
  "revision": "01bcdf5f17f28121d8a068b7d7569908"
}, {
  "url": "kcfinder/themes/dark/img/files/small/pptx.png",
  "revision": "01bcdf5f17f28121d8a068b7d7569908"
}, {
  "url": "kcfinder/themes/dark/img/files/small/psd.png",
  "revision": "02709989af27569d20280bd017651195"
}, {
  "url": "kcfinder/themes/dark/img/files/small/qt.png",
  "revision": "669214cffe7b913296246e90c372818e"
}, {
  "url": "kcfinder/themes/dark/img/files/small/rar.png",
  "revision": "621703fa3710ba61b6eb05cf0f3c59b2"
}, {
  "url": "kcfinder/themes/dark/img/files/small/rpm.png",
  "revision": "621703fa3710ba61b6eb05cf0f3c59b2"
}, {
  "url": "kcfinder/themes/dark/img/files/small/rtf.png",
  "revision": "24e6b797e0a778e78b7ce2c6a0229baa"
}, {
  "url": "kcfinder/themes/dark/img/files/small/sh.png",
  "revision": "f438e488078834934cd62c8c616c8bc7"
}, {
  "url": "kcfinder/themes/dark/img/files/small/sql.png",
  "revision": "b539cb41069d13b28c548843b756555e"
}, {
  "url": "kcfinder/themes/dark/img/files/small/srt.png",
  "revision": "24e6b797e0a778e78b7ce2c6a0229baa"
}, {
  "url": "kcfinder/themes/dark/img/files/small/sub.png",
  "revision": "24e6b797e0a778e78b7ce2c6a0229baa"
}, {
  "url": "kcfinder/themes/dark/img/files/small/swf.png",
  "revision": "910c7aa2cbc755d8822e65eeb62e1d8c"
}, {
  "url": "kcfinder/themes/dark/img/files/small/tgz.png",
  "revision": "621703fa3710ba61b6eb05cf0f3c59b2"
}, {
  "url": "kcfinder/themes/dark/img/files/small/tif.png",
  "revision": "02709989af27569d20280bd017651195"
}, {
  "url": "kcfinder/themes/dark/img/files/small/tiff.png",
  "revision": "02709989af27569d20280bd017651195"
}, {
  "url": "kcfinder/themes/dark/img/files/small/torrent.png",
  "revision": "11708f7a86cdb8c4f98d7d935314752c"
}, {
  "url": "kcfinder/themes/dark/img/files/small/ttf.png",
  "revision": "14b20b653866d0d5fb98d7497e8595e0"
}, {
  "url": "kcfinder/themes/dark/img/files/small/txt.png",
  "revision": "24e6b797e0a778e78b7ce2c6a0229baa"
}, {
  "url": "kcfinder/themes/dark/img/files/small/wav.png",
  "revision": "e2eb2abc9786a1f32cd9f3b93021d994"
}, {
  "url": "kcfinder/themes/dark/img/files/small/wma.png",
  "revision": "e2eb2abc9786a1f32cd9f3b93021d994"
}, {
  "url": "kcfinder/themes/dark/img/files/small/xls.png",
  "revision": "ac70ce6ad43c38b2d641747e86f9ffde"
}, {
  "url": "kcfinder/themes/dark/img/files/small/xlsx.png",
  "revision": "ac70ce6ad43c38b2d641747e86f9ffde"
}, {
  "url": "kcfinder/themes/dark/img/files/small/zip.png",
  "revision": "621703fa3710ba61b6eb05cf0f3c59b2"
}, {
  "url": "kcfinder/themes/dark/img/icons/about.png",
  "revision": "3750c701d2ec35a45d289b9b9c1a0667"
}, {
  "url": "kcfinder/themes/dark/img/icons/clipboard-add.png",
  "revision": "479446e25607f16529f7744d7b636b05"
}, {
  "url": "kcfinder/themes/dark/img/icons/clipboard-clear.png",
  "revision": "8a3e6c20ba362842e66ad828495b9ce0"
}, {
  "url": "kcfinder/themes/dark/img/icons/clipboard.png",
  "revision": "0deaad6ffb62dc35f74b2fd5daa74130"
}, {
  "url": "kcfinder/themes/dark/img/icons/copy.png",
  "revision": "38de59d96ecaa147d8b5f440b4c4b0e6"
}, {
  "url": "kcfinder/themes/dark/img/icons/delete.png",
  "revision": "42492684e24356a4081134894eabeb9e"
}, {
  "url": "kcfinder/themes/dark/img/icons/download.png",
  "revision": "86d3d6909693e4e5e0d850be316911bc"
}, {
  "url": "kcfinder/themes/dark/img/icons/folder-new.png",
  "revision": "4fd0ba998b55abd333f81f9318f89748"
}, {
  "url": "kcfinder/themes/dark/img/icons/maximize.png",
  "revision": "3f4f61a6af60e51cff3e67f466033e40"
}, {
  "url": "kcfinder/themes/dark/img/icons/move.png",
  "revision": "08cbb971307a4420839b409ea2eccf3d"
}, {
  "url": "kcfinder/themes/dark/img/icons/refresh.png",
  "revision": "5d4d84cf2a3b2e9be202bf9eb6173107"
}, {
  "url": "kcfinder/themes/dark/img/icons/rename.png",
  "revision": "4a0b6e47d66ed0f302f76a7b55fd77a1"
}, {
  "url": "kcfinder/themes/dark/img/icons/select.png",
  "revision": "c9b528b9541e127967eda62f79118ef0"
}, {
  "url": "kcfinder/themes/dark/img/icons/settings.png",
  "revision": "a137eb4441860564ce1655357af26de8"
}, {
  "url": "kcfinder/themes/dark/img/icons/upload.png",
  "revision": "41e3781d96abfaf1541d6af5454fb426"
}, {
  "url": "kcfinder/themes/dark/img/icons/view.png",
  "revision": "530887306aa20d3aaf2b5c6191bdaf72"
}, {
  "url": "kcfinder/themes/dark/img/kcf_logo.png",
  "revision": "644ade7d0b564d0c77c0e161d3398751"
}, {
  "url": "kcfinder/themes/dark/img/loading.gif",
  "revision": "856c387a1073192db1e249ebdd3d51f4"
}, {
  "url": "kcfinder/themes/dark/img/tree/denied.png",
  "revision": "551bbaae9998f1b2f33e3de33ea8b915"
}, {
  "url": "kcfinder/themes/dark/img/tree/folder.png",
  "revision": "fbd3929a893b147ae0940d48d533e023"
}, {
  "url": "kcfinder/themes/dark/img/tree/minus.png",
  "revision": "0e3d94086367ea0bc61513c2ccf0119c"
}, {
  "url": "kcfinder/themes/dark/img/tree/plus.png",
  "revision": "4c1dbdc7e1933b101743fd2b8199b9a5"
}, {
  "url": "kcfinder/themes/dark/img/ui-icons_black.png",
  "revision": "c6736cdb254390c71a9ea2584d7f92d7"
}, {
  "url": "kcfinder/themes/dark/img/ui-icons_grey.png",
  "revision": "0cc0e6dd865a3da7ccb04f4fcd7072db"
}, {
  "url": "kcfinder/themes/dark/img/ui-icons_white.png",
  "revision": "07d238be34eb3c035f8fa31e1792a178"
}, {
  "url": "kcfinder/themes/dark/init.js",
  "revision": "60f55be6372ad389e83812e9a5835624"
}, {
  "url": "kcfinder/themes/default/01.ui.css",
  "revision": "4188233c7094cc86bccafb985b05dec3"
}, {
  "url": "kcfinder/themes/default/02.transForm.css",
  "revision": "64edb73fa8284c315e904d96a9e8327a"
}, {
  "url": "kcfinder/themes/default/03.misc.css",
  "revision": "85e05b6bc52f83fe4b11f2a9d3465762"
}, {
  "url": "kcfinder/themes/default/img/bg_transparent.png",
  "revision": "2eee3b487cf0a09681eee137c125d7ff"
}, {
  "url": "kcfinder/themes/default/img/files/big/avi.png",
  "revision": "b796cb8587ce723d48ea5d24d9dd0d51"
}, {
  "url": "kcfinder/themes/default/img/files/big/bat.png",
  "revision": "3db21e1a732089979b26986402d49cd0"
}, {
  "url": "kcfinder/themes/default/img/files/big/bmp.png",
  "revision": "bfa41aea0226a49465cf488651de4366"
}, {
  "url": "kcfinder/themes/default/img/files/big/bz2.png",
  "revision": "a0554ca9d3746f639122d7d4999b2046"
}, {
  "url": "kcfinder/themes/default/img/files/big/ccd.png",
  "revision": "61611e0e4071e8a80e44114d19a59cfa"
}, {
  "url": "kcfinder/themes/default/img/files/big/cgi.png",
  "revision": "3db21e1a732089979b26986402d49cd0"
}, {
  "url": "kcfinder/themes/default/img/files/big/com.png",
  "revision": "ef7cb6752a845c2038ae752f54713883"
}, {
  "url": "kcfinder/themes/default/img/files/big/csh.png",
  "revision": "3db21e1a732089979b26986402d49cd0"
}, {
  "url": "kcfinder/themes/default/img/files/big/cue.png",
  "revision": "61611e0e4071e8a80e44114d19a59cfa"
}, {
  "url": "kcfinder/themes/default/img/files/big/deb.png",
  "revision": "0027b959f2b82f90e75d25cf5b2bf6a5"
}, {
  "url": "kcfinder/themes/default/img/files/big/dll.png",
  "revision": "7a71e998c9348fa02e636a8adf58cde7"
}, {
  "url": "kcfinder/themes/default/img/files/big/doc.png",
  "revision": "26a06a2ff6098b8437685bc283c2041c"
}, {
  "url": "kcfinder/themes/default/img/files/big/docx.png",
  "revision": "26a06a2ff6098b8437685bc283c2041c"
}, {
  "url": "kcfinder/themes/default/img/files/big/exe.png",
  "revision": "ef7cb6752a845c2038ae752f54713883"
}, {
  "url": "kcfinder/themes/default/img/files/big/fla.png",
  "revision": "f353e9fa95f7d30af29f136dadc5d94e"
}, {
  "url": "kcfinder/themes/default/img/files/big/flv.png",
  "revision": "b796cb8587ce723d48ea5d24d9dd0d51"
}, {
  "url": "kcfinder/themes/default/img/files/big/fon.png",
  "revision": "2d4004d2af5eb5fd3a4801c41a1a2a8d"
}, {
  "url": "kcfinder/themes/default/img/files/big/gif.png",
  "revision": "bfa41aea0226a49465cf488651de4366"
}, {
  "url": "kcfinder/themes/default/img/files/big/gz.png",
  "revision": "a0554ca9d3746f639122d7d4999b2046"
}, {
  "url": "kcfinder/themes/default/img/files/big/htm.png",
  "revision": "54d3b4a0d20897276af9b467d2c014fe"
}, {
  "url": "kcfinder/themes/default/img/files/big/html.png",
  "revision": "54d3b4a0d20897276af9b467d2c014fe"
}, {
  "url": "kcfinder/themes/default/img/files/big/ini.png",
  "revision": "34f6ef1f537af2513b90c5b98d0ace79"
}, {
  "url": "kcfinder/themes/default/img/files/big/iso.png",
  "revision": "61611e0e4071e8a80e44114d19a59cfa"
}, {
  "url": "kcfinder/themes/default/img/files/big/jar.png",
  "revision": "d1e7813a2e5b85e7d5a86df2998e7771"
}, {
  "url": "kcfinder/themes/default/img/files/big/java.png",
  "revision": "e4de49aa3f9d254d1e8a149611444016"
}, {
  "url": "kcfinder/themes/default/img/files/big/jpeg.png",
  "revision": "bfa41aea0226a49465cf488651de4366"
}, {
  "url": "kcfinder/themes/default/img/files/big/jpg.png",
  "revision": "bfa41aea0226a49465cf488651de4366"
}, {
  "url": "kcfinder/themes/default/img/files/big/js.png",
  "revision": "e2d486ea7e98654aad5cc4a69e9be65d"
}, {
  "url": "kcfinder/themes/default/img/files/big/mds.png",
  "revision": "61611e0e4071e8a80e44114d19a59cfa"
}, {
  "url": "kcfinder/themes/default/img/files/big/mdx.png",
  "revision": "61611e0e4071e8a80e44114d19a59cfa"
}, {
  "url": "kcfinder/themes/default/img/files/big/mid.png",
  "revision": "8b63cd34121a5aa0810ab290274b19a8"
}, {
  "url": "kcfinder/themes/default/img/files/big/midi.png",
  "revision": "8b63cd34121a5aa0810ab290274b19a8"
}, {
  "url": "kcfinder/themes/default/img/files/big/mkv.png",
  "revision": "b796cb8587ce723d48ea5d24d9dd0d51"
}, {
  "url": "kcfinder/themes/default/img/files/big/mov.png",
  "revision": "b796cb8587ce723d48ea5d24d9dd0d51"
}, {
  "url": "kcfinder/themes/default/img/files/big/mp3.png",
  "revision": "0b301a786c0f3978fe05927f24928395"
}, {
  "url": "kcfinder/themes/default/img/files/big/mp4.png",
  "revision": "b796cb8587ce723d48ea5d24d9dd0d51"
}, {
  "url": "kcfinder/themes/default/img/files/big/mpeg.png",
  "revision": "b796cb8587ce723d48ea5d24d9dd0d51"
}, {
  "url": "kcfinder/themes/default/img/files/big/mpg.png",
  "revision": "b796cb8587ce723d48ea5d24d9dd0d51"
}, {
  "url": "kcfinder/themes/default/img/files/big/nfo.png",
  "revision": "34f6ef1f537af2513b90c5b98d0ace79"
}, {
  "url": "kcfinder/themes/default/img/files/big/nrg.png",
  "revision": "61611e0e4071e8a80e44114d19a59cfa"
}, {
  "url": "kcfinder/themes/default/img/files/big/ogg.png",
  "revision": "0b301a786c0f3978fe05927f24928395"
}, {
  "url": "kcfinder/themes/default/img/files/big/pdf.png",
  "revision": "dbd20bfa045d965ad9ac071fb20def86"
}, {
  "url": "kcfinder/themes/default/img/files/big/php.png",
  "revision": "4512f251e567191e1c6677901b2703ad"
}, {
  "url": "kcfinder/themes/default/img/files/big/phps.png",
  "revision": "4512f251e567191e1c6677901b2703ad"
}, {
  "url": "kcfinder/themes/default/img/files/big/pl.png",
  "revision": "9ad71d8ee41be29ab64b31c5e7096ea9"
}, {
  "url": "kcfinder/themes/default/img/files/big/pm.png",
  "revision": "9ad71d8ee41be29ab64b31c5e7096ea9"
}, {
  "url": "kcfinder/themes/default/img/files/big/png.png",
  "revision": "bfa41aea0226a49465cf488651de4366"
}, {
  "url": "kcfinder/themes/default/img/files/big/ppt.png",
  "revision": "e638989e04132df68a63003fc835da6e"
}, {
  "url": "kcfinder/themes/default/img/files/big/pptx.png",
  "revision": "e638989e04132df68a63003fc835da6e"
}, {
  "url": "kcfinder/themes/default/img/files/big/psd.png",
  "revision": "bfa41aea0226a49465cf488651de4366"
}, {
  "url": "kcfinder/themes/default/img/files/big/qt.png",
  "revision": "b796cb8587ce723d48ea5d24d9dd0d51"
}, {
  "url": "kcfinder/themes/default/img/files/big/rar.png",
  "revision": "a0554ca9d3746f639122d7d4999b2046"
}, {
  "url": "kcfinder/themes/default/img/files/big/rpm.png",
  "revision": "ff4d91ee2466c503fb51213d6dacee54"
}, {
  "url": "kcfinder/themes/default/img/files/big/rtf.png",
  "revision": "34f6ef1f537af2513b90c5b98d0ace79"
}, {
  "url": "kcfinder/themes/default/img/files/big/sh.png",
  "revision": "3db21e1a732089979b26986402d49cd0"
}, {
  "url": "kcfinder/themes/default/img/files/big/sql.png",
  "revision": "9b458ee9f254709811c24e77291e2664"
}, {
  "url": "kcfinder/themes/default/img/files/big/srt.png",
  "revision": "34f6ef1f537af2513b90c5b98d0ace79"
}, {
  "url": "kcfinder/themes/default/img/files/big/sub.png",
  "revision": "34f6ef1f537af2513b90c5b98d0ace79"
}, {
  "url": "kcfinder/themes/default/img/files/big/swf.png",
  "revision": "872bf413980a37c17eda80a1875fc860"
}, {
  "url": "kcfinder/themes/default/img/files/big/tgz.png",
  "revision": "e7c32280bfe4992b469313584ec62610"
}, {
  "url": "kcfinder/themes/default/img/files/big/tif.png",
  "revision": "bfa41aea0226a49465cf488651de4366"
}, {
  "url": "kcfinder/themes/default/img/files/big/tiff.png",
  "revision": "bfa41aea0226a49465cf488651de4366"
}, {
  "url": "kcfinder/themes/default/img/files/big/torrent.png",
  "revision": "de99ae3506fb97ac67e7e0893270a049"
}, {
  "url": "kcfinder/themes/default/img/files/big/ttf.png",
  "revision": "640982ca75b713b4583aa58502f8ca8c"
}, {
  "url": "kcfinder/themes/default/img/files/big/txt.png",
  "revision": "34f6ef1f537af2513b90c5b98d0ace79"
}, {
  "url": "kcfinder/themes/default/img/files/big/wav.png",
  "revision": "0b301a786c0f3978fe05927f24928395"
}, {
  "url": "kcfinder/themes/default/img/files/big/wma.png",
  "revision": "0b301a786c0f3978fe05927f24928395"
}, {
  "url": "kcfinder/themes/default/img/files/big/xls.png",
  "revision": "513a56c3b24b93c29cb02c8f9a075cbd"
}, {
  "url": "kcfinder/themes/default/img/files/big/xlsx.png",
  "revision": "513a56c3b24b93c29cb02c8f9a075cbd"
}, {
  "url": "kcfinder/themes/default/img/files/big/zip.png",
  "revision": "a0554ca9d3746f639122d7d4999b2046"
}, {
  "url": "kcfinder/themes/default/img/files/small/avi.png",
  "revision": "669214cffe7b913296246e90c372818e"
}, {
  "url": "kcfinder/themes/default/img/files/small/bat.png",
  "revision": "f438e488078834934cd62c8c616c8bc7"
}, {
  "url": "kcfinder/themes/default/img/files/small/bmp.png",
  "revision": "02709989af27569d20280bd017651195"
}, {
  "url": "kcfinder/themes/default/img/files/small/bz2.png",
  "revision": "621703fa3710ba61b6eb05cf0f3c59b2"
}, {
  "url": "kcfinder/themes/default/img/files/small/ccd.png",
  "revision": "11e31a8ab5d57e70cd1789cf1a13fd2c"
}, {
  "url": "kcfinder/themes/default/img/files/small/cgi.png",
  "revision": "f438e488078834934cd62c8c616c8bc7"
}, {
  "url": "kcfinder/themes/default/img/files/small/com.png",
  "revision": "86729340ae0d76d4fe823009991839a7"
}, {
  "url": "kcfinder/themes/default/img/files/small/csh.png",
  "revision": "f438e488078834934cd62c8c616c8bc7"
}, {
  "url": "kcfinder/themes/default/img/files/small/cue.png",
  "revision": "11e31a8ab5d57e70cd1789cf1a13fd2c"
}, {
  "url": "kcfinder/themes/default/img/files/small/deb.png",
  "revision": "621703fa3710ba61b6eb05cf0f3c59b2"
}, {
  "url": "kcfinder/themes/default/img/files/small/dll.png",
  "revision": "db49f2c0f68155c38201228411f2b87f"
}, {
  "url": "kcfinder/themes/default/img/files/small/doc.png",
  "revision": "a441226ae0288bdfa96a1828cdabe8ab"
}, {
  "url": "kcfinder/themes/default/img/files/small/docx.png",
  "revision": "a441226ae0288bdfa96a1828cdabe8ab"
}, {
  "url": "kcfinder/themes/default/img/files/small/exe.png",
  "revision": "86729340ae0d76d4fe823009991839a7"
}, {
  "url": "kcfinder/themes/default/img/files/small/fla.png",
  "revision": "600bd9e16bfa3083c2ff3db3f50615d5"
}, {
  "url": "kcfinder/themes/default/img/files/small/flv.png",
  "revision": "669214cffe7b913296246e90c372818e"
}, {
  "url": "kcfinder/themes/default/img/files/small/fon.png",
  "revision": "7078c74fee4a70390417c0e95962f3db"
}, {
  "url": "kcfinder/themes/default/img/files/small/gif.png",
  "revision": "02709989af27569d20280bd017651195"
}, {
  "url": "kcfinder/themes/default/img/files/small/gz.png",
  "revision": "621703fa3710ba61b6eb05cf0f3c59b2"
}, {
  "url": "kcfinder/themes/default/img/files/small/htm.png",
  "revision": "8df519faea5f1a9b414325ac20a6ab39"
}, {
  "url": "kcfinder/themes/default/img/files/small/html.png",
  "revision": "8df519faea5f1a9b414325ac20a6ab39"
}, {
  "url": "kcfinder/themes/default/img/files/small/ini.png",
  "revision": "24e6b797e0a778e78b7ce2c6a0229baa"
}, {
  "url": "kcfinder/themes/default/img/files/small/iso.png",
  "revision": "11e31a8ab5d57e70cd1789cf1a13fd2c"
}, {
  "url": "kcfinder/themes/default/img/files/small/jar.png",
  "revision": "621703fa3710ba61b6eb05cf0f3c59b2"
}, {
  "url": "kcfinder/themes/default/img/files/small/java.png",
  "revision": "cf43f436c57342e281b9edb8e1387424"
}, {
  "url": "kcfinder/themes/default/img/files/small/jpeg.png",
  "revision": "02709989af27569d20280bd017651195"
}, {
  "url": "kcfinder/themes/default/img/files/small/jpg.png",
  "revision": "02709989af27569d20280bd017651195"
}, {
  "url": "kcfinder/themes/default/img/files/small/js.png",
  "revision": "7109810830148823eacaa7951904474e"
}, {
  "url": "kcfinder/themes/default/img/files/small/mds.png",
  "revision": "11e31a8ab5d57e70cd1789cf1a13fd2c"
}, {
  "url": "kcfinder/themes/default/img/files/small/mdx.png",
  "revision": "11e31a8ab5d57e70cd1789cf1a13fd2c"
}, {
  "url": "kcfinder/themes/default/img/files/small/mid.png",
  "revision": "433e55560fceaf20721671bc16bac4d6"
}, {
  "url": "kcfinder/themes/default/img/files/small/midi.png",
  "revision": "433e55560fceaf20721671bc16bac4d6"
}, {
  "url": "kcfinder/themes/default/img/files/small/mkv.png",
  "revision": "669214cffe7b913296246e90c372818e"
}, {
  "url": "kcfinder/themes/default/img/files/small/mov.png",
  "revision": "669214cffe7b913296246e90c372818e"
}, {
  "url": "kcfinder/themes/default/img/files/small/mp3.png",
  "revision": "e2eb2abc9786a1f32cd9f3b93021d994"
}, {
  "url": "kcfinder/themes/default/img/files/small/mp4.png",
  "revision": "669214cffe7b913296246e90c372818e"
}, {
  "url": "kcfinder/themes/default/img/files/small/mpeg.png",
  "revision": "669214cffe7b913296246e90c372818e"
}, {
  "url": "kcfinder/themes/default/img/files/small/mpg.png",
  "revision": "669214cffe7b913296246e90c372818e"
}, {
  "url": "kcfinder/themes/default/img/files/small/nfo.png",
  "revision": "24e6b797e0a778e78b7ce2c6a0229baa"
}, {
  "url": "kcfinder/themes/default/img/files/small/nrg.png",
  "revision": "11e31a8ab5d57e70cd1789cf1a13fd2c"
}, {
  "url": "kcfinder/themes/default/img/files/small/ogg.png",
  "revision": "e2eb2abc9786a1f32cd9f3b93021d994"
}, {
  "url": "kcfinder/themes/default/img/files/small/pdf.png",
  "revision": "e686949b4c31d52da8bbc964a9b5ee30"
}, {
  "url": "kcfinder/themes/default/img/files/small/php.png",
  "revision": "fbd20eba7ecdab05a2395873cdc7c4f3"
}, {
  "url": "kcfinder/themes/default/img/files/small/phps.png",
  "revision": "fbd20eba7ecdab05a2395873cdc7c4f3"
}, {
  "url": "kcfinder/themes/default/img/files/small/pl.png",
  "revision": "24e6b797e0a778e78b7ce2c6a0229baa"
}, {
  "url": "kcfinder/themes/default/img/files/small/pm.png",
  "revision": "24e6b797e0a778e78b7ce2c6a0229baa"
}, {
  "url": "kcfinder/themes/default/img/files/small/png.png",
  "revision": "02709989af27569d20280bd017651195"
}, {
  "url": "kcfinder/themes/default/img/files/small/ppt.png",
  "revision": "01bcdf5f17f28121d8a068b7d7569908"
}, {
  "url": "kcfinder/themes/default/img/files/small/pptx.png",
  "revision": "01bcdf5f17f28121d8a068b7d7569908"
}, {
  "url": "kcfinder/themes/default/img/files/small/psd.png",
  "revision": "02709989af27569d20280bd017651195"
}, {
  "url": "kcfinder/themes/default/img/files/small/qt.png",
  "revision": "669214cffe7b913296246e90c372818e"
}, {
  "url": "kcfinder/themes/default/img/files/small/rar.png",
  "revision": "621703fa3710ba61b6eb05cf0f3c59b2"
}, {
  "url": "kcfinder/themes/default/img/files/small/rpm.png",
  "revision": "621703fa3710ba61b6eb05cf0f3c59b2"
}, {
  "url": "kcfinder/themes/default/img/files/small/rtf.png",
  "revision": "24e6b797e0a778e78b7ce2c6a0229baa"
}, {
  "url": "kcfinder/themes/default/img/files/small/sh.png",
  "revision": "f438e488078834934cd62c8c616c8bc7"
}, {
  "url": "kcfinder/themes/default/img/files/small/sql.png",
  "revision": "b539cb41069d13b28c548843b756555e"
}, {
  "url": "kcfinder/themes/default/img/files/small/srt.png",
  "revision": "24e6b797e0a778e78b7ce2c6a0229baa"
}, {
  "url": "kcfinder/themes/default/img/files/small/sub.png",
  "revision": "24e6b797e0a778e78b7ce2c6a0229baa"
}, {
  "url": "kcfinder/themes/default/img/files/small/swf.png",
  "revision": "910c7aa2cbc755d8822e65eeb62e1d8c"
}, {
  "url": "kcfinder/themes/default/img/files/small/tgz.png",
  "revision": "621703fa3710ba61b6eb05cf0f3c59b2"
}, {
  "url": "kcfinder/themes/default/img/files/small/tif.png",
  "revision": "02709989af27569d20280bd017651195"
}, {
  "url": "kcfinder/themes/default/img/files/small/tiff.png",
  "revision": "02709989af27569d20280bd017651195"
}, {
  "url": "kcfinder/themes/default/img/files/small/torrent.png",
  "revision": "11708f7a86cdb8c4f98d7d935314752c"
}, {
  "url": "kcfinder/themes/default/img/files/small/ttf.png",
  "revision": "14b20b653866d0d5fb98d7497e8595e0"
}, {
  "url": "kcfinder/themes/default/img/files/small/txt.png",
  "revision": "24e6b797e0a778e78b7ce2c6a0229baa"
}, {
  "url": "kcfinder/themes/default/img/files/small/wav.png",
  "revision": "e2eb2abc9786a1f32cd9f3b93021d994"
}, {
  "url": "kcfinder/themes/default/img/files/small/wma.png",
  "revision": "e2eb2abc9786a1f32cd9f3b93021d994"
}, {
  "url": "kcfinder/themes/default/img/files/small/xls.png",
  "revision": "ac70ce6ad43c38b2d641747e86f9ffde"
}, {
  "url": "kcfinder/themes/default/img/files/small/xlsx.png",
  "revision": "ac70ce6ad43c38b2d641747e86f9ffde"
}, {
  "url": "kcfinder/themes/default/img/files/small/zip.png",
  "revision": "621703fa3710ba61b6eb05cf0f3c59b2"
}, {
  "url": "kcfinder/themes/default/img/icons/about.png",
  "revision": "3750c701d2ec35a45d289b9b9c1a0667"
}, {
  "url": "kcfinder/themes/default/img/icons/clipboard-add.png",
  "revision": "479446e25607f16529f7744d7b636b05"
}, {
  "url": "kcfinder/themes/default/img/icons/clipboard-clear.png",
  "revision": "8a3e6c20ba362842e66ad828495b9ce0"
}, {
  "url": "kcfinder/themes/default/img/icons/clipboard.png",
  "revision": "0deaad6ffb62dc35f74b2fd5daa74130"
}, {
  "url": "kcfinder/themes/default/img/icons/copy.png",
  "revision": "38de59d96ecaa147d8b5f440b4c4b0e6"
}, {
  "url": "kcfinder/themes/default/img/icons/delete.png",
  "revision": "42492684e24356a4081134894eabeb9e"
}, {
  "url": "kcfinder/themes/default/img/icons/download.png",
  "revision": "86d3d6909693e4e5e0d850be316911bc"
}, {
  "url": "kcfinder/themes/default/img/icons/folder-new.png",
  "revision": "4fd0ba998b55abd333f81f9318f89748"
}, {
  "url": "kcfinder/themes/default/img/icons/maximize.png",
  "revision": "3f4f61a6af60e51cff3e67f466033e40"
}, {
  "url": "kcfinder/themes/default/img/icons/move.png",
  "revision": "08cbb971307a4420839b409ea2eccf3d"
}, {
  "url": "kcfinder/themes/default/img/icons/refresh.png",
  "revision": "5d4d84cf2a3b2e9be202bf9eb6173107"
}, {
  "url": "kcfinder/themes/default/img/icons/rename.png",
  "revision": "4a0b6e47d66ed0f302f76a7b55fd77a1"
}, {
  "url": "kcfinder/themes/default/img/icons/select.png",
  "revision": "c9b528b9541e127967eda62f79118ef0"
}, {
  "url": "kcfinder/themes/default/img/icons/settings.png",
  "revision": "a137eb4441860564ce1655357af26de8"
}, {
  "url": "kcfinder/themes/default/img/icons/upload.png",
  "revision": "41e3781d96abfaf1541d6af5454fb426"
}, {
  "url": "kcfinder/themes/default/img/icons/view.png",
  "revision": "530887306aa20d3aaf2b5c6191bdaf72"
}, {
  "url": "kcfinder/themes/default/img/kcf_logo.png",
  "revision": "644ade7d0b564d0c77c0e161d3398751"
}, {
  "url": "kcfinder/themes/default/img/loading.gif",
  "revision": "4817dab8b118ae548890b9fb15a5b647"
}, {
  "url": "kcfinder/themes/default/img/tree/denied.png",
  "revision": "551bbaae9998f1b2f33e3de33ea8b915"
}, {
  "url": "kcfinder/themes/default/img/tree/folder.png",
  "revision": "fbd3929a893b147ae0940d48d533e023"
}, {
  "url": "kcfinder/themes/default/img/tree/minus.png",
  "revision": "0e3d94086367ea0bc61513c2ccf0119c"
}, {
  "url": "kcfinder/themes/default/img/tree/plus.png",
  "revision": "4c1dbdc7e1933b101743fd2b8199b9a5"
}, {
  "url": "kcfinder/themes/default/img/ui-icons_black.png",
  "revision": "a3f7b7d66f301cff7d43b2f13c0b8368"
}, {
  "url": "kcfinder/themes/default/img/ui-icons_blue.png",
  "revision": "864f6bb3e7aeecb673415250cdbc696d"
}, {
  "url": "kcfinder/themes/default/img/ui-icons_white.png",
  "revision": "e340015df9b7d83d3db0e4fc6e2ce5c6"
}, {
  "url": "kcfinder/themes/default/init.js",
  "revision": "dd6ecb973c7d558767cbbfa2a354682e"
}, {
  "url": "include/LD/assets/fonts/SalesforceSans-Bold.ttf",
  "revision": "bab6f1ee9617b3f4375b4b4ccb818b01"
}, {
  "url": "include/LD/assets/fonts/SalesforceSans-BoldItalic.ttf",
  "revision": "ff2f4aa51976e26f3f356a3e16a4a1f8"
}, {
  "url": "include/LD/assets/fonts/SalesforceSans-Book.ttf",
  "revision": "23b8e087ecb40a02e3a2bf50b2da72a3"
}, {
  "url": "include/LD/assets/fonts/SalesforceSans-Italic.ttf",
  "revision": "d0c0958fee01a679b1dad7db640e1835"
}, {
  "url": "include/LD/assets/fonts/SalesforceSans-Light.ttf",
  "revision": "76d3c8425c3ee7c56dd3dad04b9016ca"
}, {
  "url": "include/LD/assets/fonts/SalesforceSans-LightItalic.ttf",
  "revision": "30b63ac5063e7500299c0a9895332691"
}, {
  "url": "include/LD/assets/fonts/SalesforceSans-Regular.ttf",
  "revision": "6c9ddaa8a8cfa8df9d612612753d00b2"
}, {
  "url": "include/LD/assets/fonts/SalesforceSans-Semibold.ttf",
  "revision": "c3506dbc4b756695f9faf15eb9caebd1"
}, {
  "url": "include/LD/assets/fonts/SalesforceSans-Thin.ttf",
  "revision": "b4b52fea9d4b0e87eb7a302197295cad"
}, {
  "url": "include/LD/assets/fonts/SalesforceSans-ThinItalic.ttf",
  "revision": "388a242283418317e04f2a7105bb0835"
}, {
  "url": "include/LD/assets/fonts/webfonts/SalesforceSans-Bold.eot",
  "revision": "7126a8f76526e1d11b7e96e590524af6"
}, {
  "url": "include/LD/assets/fonts/webfonts/SalesforceSans-Bold.svg",
  "revision": "8aecdcc4aa68e15aefcb0d1d0e590d81"
}, {
  "url": "include/LD/assets/fonts/webfonts/SalesforceSans-Bold.woff",
  "revision": "034baa2c90687fad501b83e225f3728f"
}, {
  "url": "include/LD/assets/fonts/webfonts/SalesforceSans-BoldItalic.eot",
  "revision": "430e3065b81b4b2abec9687be9dcecfe"
}, {
  "url": "include/LD/assets/fonts/webfonts/SalesforceSans-BoldItalic.svg",
  "revision": "8152c6eb3b7a34653208166c7cb60c2b"
}, {
  "url": "include/LD/assets/fonts/webfonts/SalesforceSans-BoldItalic.woff",
  "revision": "7b6ae28b116debe909b3fec84b310468"
}, {
  "url": "include/LD/assets/fonts/webfonts/SalesforceSans-Italic.eot",
  "revision": "60d1fa1975c08d8c67abd5ea6ee5417d"
}, {
  "url": "include/LD/assets/fonts/webfonts/SalesforceSans-Italic.svg",
  "revision": "c944a148692ed1743f976c168fcf1629"
}, {
  "url": "include/LD/assets/fonts/webfonts/SalesforceSans-Italic.woff",
  "revision": "490fd008e87efd93f09b27cc298402d3"
}, {
  "url": "include/LD/assets/fonts/webfonts/SalesforceSans-Light.eot",
  "revision": "db0b0031bf4eeb01aabdb6830a918912"
}, {
  "url": "include/LD/assets/fonts/webfonts/SalesforceSans-Light.svg",
  "revision": "d5a0b582f31fe3e2cb49f85480e9d823"
}, {
  "url": "include/LD/assets/fonts/webfonts/SalesforceSans-Light.woff",
  "revision": "49c5f0d3823d5417274ec49fe9d702d7"
}, {
  "url": "include/LD/assets/fonts/webfonts/SalesforceSans-LightItalic.eot",
  "revision": "545e3e4d690f6d7d4a89e0783929a0c0"
}, {
  "url": "include/LD/assets/fonts/webfonts/SalesforceSans-LightItalic.svg",
  "revision": "8575578fa11d310d1dd4295ec7e495fa"
}, {
  "url": "include/LD/assets/fonts/webfonts/SalesforceSans-LightItalic.woff",
  "revision": "13eed833d3d7ff2ae1b85ac45b13a3d9"
}, {
  "url": "include/LD/assets/fonts/webfonts/SalesforceSans-Regular.eot",
  "revision": "e49dcb01a490f0e54b047e69cc5ba537"
}, {
  "url": "include/LD/assets/fonts/webfonts/SalesforceSans-Regular.svg",
  "revision": "ada994ed9b2acac21ceedf8e5d97484a"
}, {
  "url": "include/LD/assets/fonts/webfonts/SalesforceSans-Regular.woff",
  "revision": "8621cf5a8eb1acfacd002232c95d85ed"
}, {
  "url": "include/LD/assets/fonts/webfonts/SalesforceSans-Thin.eot",
  "revision": "0f5f0d78694ea287e7f43ff54a0056ad"
}, {
  "url": "include/LD/assets/fonts/webfonts/SalesforceSans-Thin.svg",
  "revision": "a564e763588be50117b9624867e4cf8c"
}, {
  "url": "include/LD/assets/fonts/webfonts/SalesforceSans-Thin.woff",
  "revision": "4a20519f44ff88ce58982cdba77baccf"
}, {
  "url": "include/LD/assets/fonts/webfonts/SalesforceSans-ThinItalic.eot",
  "revision": "78b1ce67f1f64198543dc1b92bd28d47"
}, {
  "url": "include/LD/assets/fonts/webfonts/SalesforceSans-ThinItalic.svg",
  "revision": "ee6131f0b7cd6021b2de99f5c7a0dc83"
}, {
  "url": "include/LD/assets/fonts/webfonts/SalesforceSans-ThinItalic.woff",
  "revision": "e9d217aa6d675425569b853e6c63fee9"
}, {
  "url": "include/LD/assets/icons/action-sprite/svg/symbols-rtl.svg",
  "revision": "d21e4d3fad60f6a89cb2a7ca6e50b67d"
}, {
  "url": "include/LD/assets/icons/action-sprite/svg/symbols.svg",
  "revision": "3ab367e03b9d59c947370cc92c1c9e90"
}, {
  "url": "include/LD/assets/icons/action/add_contact.svg",
  "revision": "8213f8c1af6fc1a5f45b9e68d3370883"
}, {
  "url": "include/LD/assets/icons/action/add_file.svg",
  "revision": "1d087065bc495e0b2b2cd3a7ea51c1da"
}, {
  "url": "include/LD/assets/icons/action/add_photo_video.svg",
  "revision": "005cf618ccbd7626e4f0dd5cfcafb8bd"
}, {
  "url": "include/LD/assets/icons/action/add_relationship.svg",
  "revision": "0a095c9731c059b328d3d1b238c9130f"
}, {
  "url": "include/LD/assets/icons/action/announcement.svg",
  "revision": "3363818115295fbc44b75ac74bc87c04"
}, {
  "url": "include/LD/assets/icons/action/apex.svg",
  "revision": "10f87560dd726ad9da9b8bb606c72db1"
}, {
  "url": "include/LD/assets/icons/action/approval.svg",
  "revision": "42f167b8fedebe4bcf08fcc92c791c97"
}, {
  "url": "include/LD/assets/icons/action/back.svg",
  "revision": "4480dec54ab9729244271d09f9c45d41"
}, {
  "url": "include/LD/assets/icons/action/bug.svg",
  "revision": "61f848c3e3abde9e33f959312319681a"
}, {
  "url": "include/LD/assets/icons/action/call.svg",
  "revision": "eb966d0df9365dc826b5aa706820bcc9"
}, {
  "url": "include/LD/assets/icons/action/canvas.svg",
  "revision": "92294a386cfeb597239bebde2ff9ff3e"
}, {
  "url": "include/LD/assets/icons/action/change_owner.svg",
  "revision": "92d871f9cf8e07a4923874bb3c8f63f2"
}, {
  "url": "include/LD/assets/icons/action/change_record_type.svg",
  "revision": "6bb2a79175bba89d782d70de18b112c2"
}, {
  "url": "include/LD/assets/icons/action/check.svg",
  "revision": "a7fb6b1d178bab3a40731120babe0dd5"
}, {
  "url": "include/LD/assets/icons/action/clone.svg",
  "revision": "5eb96dff92abe1d34aa01fdb8ad1e2f7"
}, {
  "url": "include/LD/assets/icons/action/close.svg",
  "revision": "8df3fa2eefea162616b7720dbc211489"
}, {
  "url": "include/LD/assets/icons/action/defer.svg",
  "revision": "f990fd06d32b73e8f0a3a3e62e5599d6"
}, {
  "url": "include/LD/assets/icons/action/delete.svg",
  "revision": "5a727b715b796a063ea365f41a060666"
}, {
  "url": "include/LD/assets/icons/action/description.svg",
  "revision": "1784efb133e92dcf6dd4d4f3ff0dbad8"
}, {
  "url": "include/LD/assets/icons/action/dial_in.svg",
  "revision": "9c5c820a4e1b61269875de44db7b072d"
}, {
  "url": "include/LD/assets/icons/action/download.svg",
  "revision": "bebdb605afbb5eceb453afd7126bac69"
}, {
  "url": "include/LD/assets/icons/action/edit_groups.svg",
  "revision": "868c58df1a39725661d4410de1029f28"
}, {
  "url": "include/LD/assets/icons/action/edit_relationship.svg",
  "revision": "3784a087b713bd14c6e06bb4a3df900a"
}, {
  "url": "include/LD/assets/icons/action/edit.svg",
  "revision": "a6528100cb184b81f8ec282a2878dcd4"
}, {
  "url": "include/LD/assets/icons/action/email.svg",
  "revision": "7458bf7f5ef5feaf561526e8291d1c27"
}, {
  "url": "include/LD/assets/icons/action/fallback.svg",
  "revision": "b746999da8e9f9f77ee4936760673edf"
}, {
  "url": "include/LD/assets/icons/action/filter.svg",
  "revision": "d53d43eb22125dc69823a1f1e46d3cde"
}, {
  "url": "include/LD/assets/icons/action/flow.svg",
  "revision": "5a8e73c3b53a5ec132bc178bf8c3ab55"
}, {
  "url": "include/LD/assets/icons/action/follow.svg",
  "revision": "c23e77a1f1cb06b9fdce3756ad11a69a"
}, {
  "url": "include/LD/assets/icons/action/following.svg",
  "revision": "70299b31268b128788ce678b35a86d06"
}, {
  "url": "include/LD/assets/icons/action/freeze_user.svg",
  "revision": "af1167e1ee824a67baac4ce633e64f5b"
}, {
  "url": "include/LD/assets/icons/action/goal.svg",
  "revision": "0b44a8ec035f7a6d1db4d3dcbd774ce8"
}, {
  "url": "include/LD/assets/icons/action/google_news.svg",
  "revision": "e28746a9097b0f97b0051f7a056728cd"
}, {
  "url": "include/LD/assets/icons/action/info.svg",
  "revision": "eba3730c5c02615a98f8a5935d7172df"
}, {
  "url": "include/LD/assets/icons/action/join_group.svg",
  "revision": "9a515a486e9990fb5e88351103a83c23"
}, {
  "url": "include/LD/assets/icons/action/lead_convert.svg",
  "revision": "128e3311634f40be931b3508c7c16027"
}, {
  "url": "include/LD/assets/icons/action/leave_group.svg",
  "revision": "a39eb4d9bf4af4a43024cf96b76ac1dd"
}, {
  "url": "include/LD/assets/icons/action/log_a_call.svg",
  "revision": "1c67cbb49ddd9a67fed194337c91fe1f"
}, {
  "url": "include/LD/assets/icons/action/log_event.svg",
  "revision": "b77da229b8a26dc3aa5d1f7cf0c58ac9"
}, {
  "url": "include/LD/assets/icons/action/manage_perm_sets.svg",
  "revision": "f4403b5973e94731a8f98ed87fcb0e5f"
}, {
  "url": "include/LD/assets/icons/action/map.svg",
  "revision": "321dce93fa2e442bb440f4de9cbe6355"
}, {
  "url": "include/LD/assets/icons/action/more.svg",
  "revision": "6446b98ddcaaf5ad04298f9bb6a92cc5"
}, {
  "url": "include/LD/assets/icons/action/new_account.svg",
  "revision": "0bac65edac20af13332525ce4697f2d8"
}, {
  "url": "include/LD/assets/icons/action/new_campaign.svg",
  "revision": "cbe5f8daac8ea26277f043295eaec0ce"
}, {
  "url": "include/LD/assets/icons/action/new_case.svg",
  "revision": "44ac8e486e3689366efd3cbe602e3ab5"
}, {
  "url": "include/LD/assets/icons/action/new_child_case.svg",
  "revision": "6e4a7883a4127d37f4c23d57eb8ee39b"
}, {
  "url": "include/LD/assets/icons/action/new_contact.svg",
  "revision": "8213f8c1af6fc1a5f45b9e68d3370883"
}, {
  "url": "include/LD/assets/icons/action/new_custom1.svg",
  "revision": "94034e1718801b7f8575b922ffddf20a"
}, {
  "url": "include/LD/assets/icons/action/new_custom10.svg",
  "revision": "debd6911cb454838ff05ee616b97c473"
}, {
  "url": "include/LD/assets/icons/action/new_custom100.svg",
  "revision": "a48c07ae28c22b533351947f2f0ca69f"
}, {
  "url": "include/LD/assets/icons/action/new_custom11.svg",
  "revision": "d8ec488ee9730cb9927cd42fb542b0d5"
}, {
  "url": "include/LD/assets/icons/action/new_custom12.svg",
  "revision": "ae0710fe3fb29c9d51d27e095d8219d6"
}, {
  "url": "include/LD/assets/icons/action/new_custom13.svg",
  "revision": "70df76cbd28208fb826553c86ea591cd"
}, {
  "url": "include/LD/assets/icons/action/new_custom14.svg",
  "revision": "0e65a0bd3601d889a3845f907f6482e2"
}, {
  "url": "include/LD/assets/icons/action/new_custom15.svg",
  "revision": "42fff31ae41f0903e32da38aef3c0f03"
}, {
  "url": "include/LD/assets/icons/action/new_custom16.svg",
  "revision": "335c2efd4903fed6d72a56e067be704f"
}, {
  "url": "include/LD/assets/icons/action/new_custom17.svg",
  "revision": "25d89035764bf7cc074a435b280101ec"
}, {
  "url": "include/LD/assets/icons/action/new_custom18.svg",
  "revision": "6ecc943f3260d31f99156e5a688acba8"
}, {
  "url": "include/LD/assets/icons/action/new_custom19.svg",
  "revision": "4c28d8c88c0e1dbb597c03d227a6de16"
}, {
  "url": "include/LD/assets/icons/action/new_custom2.svg",
  "revision": "bc525a017169359d34705dd4bc8aa117"
}, {
  "url": "include/LD/assets/icons/action/new_custom20.svg",
  "revision": "233eb341b4c7d3057cdfd90570cbeb3d"
}, {
  "url": "include/LD/assets/icons/action/new_custom21.svg",
  "revision": "d7d817f06ef1bb7c60cbe9bd45c18fa7"
}, {
  "url": "include/LD/assets/icons/action/new_custom22.svg",
  "revision": "02830bc9ecb11449a88725df5d393dd6"
}, {
  "url": "include/LD/assets/icons/action/new_custom23.svg",
  "revision": "ca7b264b9005923adf67338b1d5e1d71"
}, {
  "url": "include/LD/assets/icons/action/new_custom24.svg",
  "revision": "3f953a6750378757d691489697a170dc"
}, {
  "url": "include/LD/assets/icons/action/new_custom25.svg",
  "revision": "54d648cf67b4cb919f745507c379e6c1"
}, {
  "url": "include/LD/assets/icons/action/new_custom26.svg",
  "revision": "af06832d04d12c140e7e4db738efb97f"
}, {
  "url": "include/LD/assets/icons/action/new_custom27.svg",
  "revision": "a85a54ddde660f2e86d7a5b5adb89cc7"
}, {
  "url": "include/LD/assets/icons/action/new_custom28.svg",
  "revision": "f5d6c6e7cdecbac0cf3ebf114fb46265"
}, {
  "url": "include/LD/assets/icons/action/new_custom29.svg",
  "revision": "dae8ff75b9b72319d33ad24e1123c81e"
}, {
  "url": "include/LD/assets/icons/action/new_custom3.svg",
  "revision": "06cebf29445bb133d3feb52aee044ce2"
}, {
  "url": "include/LD/assets/icons/action/new_custom30.svg",
  "revision": "0528aafbc2a47d177841af50bc38c17e"
}, {
  "url": "include/LD/assets/icons/action/new_custom31.svg",
  "revision": "c15f827e3a38474b4b62fc55bf0b1d29"
}, {
  "url": "include/LD/assets/icons/action/new_custom32.svg",
  "revision": "b6e0301b4229cecd657c962473137fa1"
}, {
  "url": "include/LD/assets/icons/action/new_custom33.svg",
  "revision": "9ee8ec948d097e9db179311a4b8884d2"
}, {
  "url": "include/LD/assets/icons/action/new_custom34.svg",
  "revision": "bd84f8d9dedd972e96a6dac465009c88"
}, {
  "url": "include/LD/assets/icons/action/new_custom35.svg",
  "revision": "d0814c569026e27a40d544d4beacd406"
}, {
  "url": "include/LD/assets/icons/action/new_custom36.svg",
  "revision": "203d3db78a413e71a372485739ff501c"
}, {
  "url": "include/LD/assets/icons/action/new_custom37.svg",
  "revision": "f73c0011aa5bbdf23dc82804c769dd51"
}, {
  "url": "include/LD/assets/icons/action/new_custom38.svg",
  "revision": "449df704dd6a110f239c4ea144745bb0"
}, {
  "url": "include/LD/assets/icons/action/new_custom39.svg",
  "revision": "66ac702d271a722ccf4255a86c198711"
}, {
  "url": "include/LD/assets/icons/action/new_custom4.svg",
  "revision": "7d36627c26583586ff3227028f883e2c"
}, {
  "url": "include/LD/assets/icons/action/new_custom40.svg",
  "revision": "8566f4d2e31464d0c6cac3329365c9a9"
}, {
  "url": "include/LD/assets/icons/action/new_custom41.svg",
  "revision": "fa9828cd6c11fd4652976da434ae1391"
}, {
  "url": "include/LD/assets/icons/action/new_custom42.svg",
  "revision": "70df76cbd28208fb826553c86ea591cd"
}, {
  "url": "include/LD/assets/icons/action/new_custom43.svg",
  "revision": "175c884afda6de8f41df75c1a7035d7a"
}, {
  "url": "include/LD/assets/icons/action/new_custom44.svg",
  "revision": "c121efe045dd6605e9c616ee723e54db"
}, {
  "url": "include/LD/assets/icons/action/new_custom45.svg",
  "revision": "7ca903d2dc4f5f633f0a073ce2ff22dd"
}, {
  "url": "include/LD/assets/icons/action/new_custom46.svg",
  "revision": "4c42c3dae1c826c7a6844e2f214d5d0c"
}, {
  "url": "include/LD/assets/icons/action/new_custom47.svg",
  "revision": "d039579af28f0293c36353c4341889d8"
}, {
  "url": "include/LD/assets/icons/action/new_custom48.svg",
  "revision": "f328de125540ea0b60abb05d300d8882"
}, {
  "url": "include/LD/assets/icons/action/new_custom49.svg",
  "revision": "380779215c86ac2f5f606d95699671d6"
}, {
  "url": "include/LD/assets/icons/action/new_custom5.svg",
  "revision": "a23b5d43462ccd831f6be4b8bdc42c40"
}, {
  "url": "include/LD/assets/icons/action/new_custom50.svg",
  "revision": "80acb568a42a7b3e334482e50936b8e8"
}, {
  "url": "include/LD/assets/icons/action/new_custom51.svg",
  "revision": "fddc26f50a5353bdb0c0da8a87487448"
}, {
  "url": "include/LD/assets/icons/action/new_custom52.svg",
  "revision": "0ce684e1dd04b5b67e6c012c911db01b"
}, {
  "url": "include/LD/assets/icons/action/new_custom53.svg",
  "revision": "03f771df9678d079100c0ebe6cdcc723"
}, {
  "url": "include/LD/assets/icons/action/new_custom54.svg",
  "revision": "4716099024e2e4d7032a9ccb7ad9441a"
}, {
  "url": "include/LD/assets/icons/action/new_custom55.svg",
  "revision": "754762d16de6b98f7e7d0d5faff73889"
}, {
  "url": "include/LD/assets/icons/action/new_custom56.svg",
  "revision": "6149f3111ab6f2413cae8146f8c78f88"
}, {
  "url": "include/LD/assets/icons/action/new_custom57.svg",
  "revision": "43bc8edc2ef6193f96291b58d3058274"
}, {
  "url": "include/LD/assets/icons/action/new_custom58.svg",
  "revision": "94d63ab4b7ec5bc59425b9b8d929e738"
}, {
  "url": "include/LD/assets/icons/action/new_custom59.svg",
  "revision": "9ea0543c9400930ff17ae80b06e086dd"
}, {
  "url": "include/LD/assets/icons/action/new_custom6.svg",
  "revision": "4818335bf07a480aedc7c641c647243d"
}, {
  "url": "include/LD/assets/icons/action/new_custom60.svg",
  "revision": "a2b1c608e6e638ab0f05359705f4ed45"
}, {
  "url": "include/LD/assets/icons/action/new_custom61.svg",
  "revision": "cf550a625493e32bf456f7bbde5fba83"
}, {
  "url": "include/LD/assets/icons/action/new_custom62.svg",
  "revision": "7de743e53f8fe4aab054d91b4ca2a333"
}, {
  "url": "include/LD/assets/icons/action/new_custom63.svg",
  "revision": "1ca3171b90418044dbbb055926870d88"
}, {
  "url": "include/LD/assets/icons/action/new_custom64.svg",
  "revision": "53c6652a9583a10f7297c351cc4246cc"
}, {
  "url": "include/LD/assets/icons/action/new_custom65.svg",
  "revision": "0c8549fe2969aa48c83c5cd9842c2c28"
}, {
  "url": "include/LD/assets/icons/action/new_custom66.svg",
  "revision": "19b0278b151f20bef559eccd05708320"
}, {
  "url": "include/LD/assets/icons/action/new_custom67.svg",
  "revision": "fb3224656890cba8c3c5eb63dc193774"
}, {
  "url": "include/LD/assets/icons/action/new_custom68.svg",
  "revision": "cbfd33a3db75189e5e7d456d3ba25690"
}, {
  "url": "include/LD/assets/icons/action/new_custom69.svg",
  "revision": "68d1c5bea12c98117a89cdabc63403eb"
}, {
  "url": "include/LD/assets/icons/action/new_custom7.svg",
  "revision": "66c06e52d7121203f0eb076d0cb6ebb0"
}, {
  "url": "include/LD/assets/icons/action/new_custom70.svg",
  "revision": "07e1f6363e85608b653bb4b77dcd4459"
}, {
  "url": "include/LD/assets/icons/action/new_custom71.svg",
  "revision": "b1a2c50f43e282d9dec52d310a3ce57e"
}, {
  "url": "include/LD/assets/icons/action/new_custom72.svg",
  "revision": "507213244199bba0a3adc8208e4ca42e"
}, {
  "url": "include/LD/assets/icons/action/new_custom73.svg",
  "revision": "cc00d18fa10d9953577ceb5a9708f081"
}, {
  "url": "include/LD/assets/icons/action/new_custom74.svg",
  "revision": "9ed8be8f4901b198bae01d1a1fe270eb"
}, {
  "url": "include/LD/assets/icons/action/new_custom75.svg",
  "revision": "0e383b3be78d4f9d94dd6c37c7061b84"
}, {
  "url": "include/LD/assets/icons/action/new_custom76.svg",
  "revision": "335717d2559fb34c6150b4e761ef9e64"
}, {
  "url": "include/LD/assets/icons/action/new_custom77.svg",
  "revision": "5bbb0a7c9189b53ccb1d0e23fe2aef70"
}, {
  "url": "include/LD/assets/icons/action/new_custom78.svg",
  "revision": "a80e3d9985c6748a5f3a3c71f627443f"
}, {
  "url": "include/LD/assets/icons/action/new_custom79.svg",
  "revision": "62ac20813583ec4d758a9ed2b261f40c"
}, {
  "url": "include/LD/assets/icons/action/new_custom8.svg",
  "revision": "73eb2ce4939a5b403d7aa7ef7d653019"
}, {
  "url": "include/LD/assets/icons/action/new_custom80.svg",
  "revision": "c660ade0d317bb8e56ce4e9357ccd8b1"
}, {
  "url": "include/LD/assets/icons/action/new_custom81.svg",
  "revision": "1953121bce9f323689d0039cc0633084"
}, {
  "url": "include/LD/assets/icons/action/new_custom82.svg",
  "revision": "3604b82d3b82cf1de7d168dc66d5d923"
}, {
  "url": "include/LD/assets/icons/action/new_custom83.svg",
  "revision": "5de8649aa5a9b27041d84e93c26c3823"
}, {
  "url": "include/LD/assets/icons/action/new_custom84.svg",
  "revision": "83dce573b4fd91fdb75594b1ebe49890"
}, {
  "url": "include/LD/assets/icons/action/new_custom85.svg",
  "revision": "01aa16b05176d25fe25e2ed6c22b9f32"
}, {
  "url": "include/LD/assets/icons/action/new_custom86.svg",
  "revision": "a2d5b498669ca89907407ead0b0739dd"
}, {
  "url": "include/LD/assets/icons/action/new_custom87.svg",
  "revision": "bc2d0aaf946d8f5366b1e61e307eac4f"
}, {
  "url": "include/LD/assets/icons/action/new_custom88.svg",
  "revision": "bdb668efa8d14958c2e455d5a566c1db"
}, {
  "url": "include/LD/assets/icons/action/new_custom89.svg",
  "revision": "ce6dcd2dda5a77b2df4f79d4a50fa1bf"
}, {
  "url": "include/LD/assets/icons/action/new_custom9.svg",
  "revision": "01ec6e7a2a321b3439b143d11782f7ae"
}, {
  "url": "include/LD/assets/icons/action/new_custom90.svg",
  "revision": "84d37c3a4faba123c6a13554707b2002"
}, {
  "url": "include/LD/assets/icons/action/new_custom91.svg",
  "revision": "07bc0253e0ddebae3e507dcfe89eb241"
}, {
  "url": "include/LD/assets/icons/action/new_custom92.svg",
  "revision": "2d758a0e547efbf264c5851d7c777932"
}, {
  "url": "include/LD/assets/icons/action/new_custom93.svg",
  "revision": "e340238e8cf5cd94cb6b8f07ca09d2ca"
}, {
  "url": "include/LD/assets/icons/action/new_custom94.svg",
  "revision": "e42d087c0a17617482516e2b5e65953e"
}, {
  "url": "include/LD/assets/icons/action/new_custom95.svg",
  "revision": "3d701979b64fd7ee4ea8714bf527c1cf"
}, {
  "url": "include/LD/assets/icons/action/new_custom96.svg",
  "revision": "b3020cc1af210f4052d15d5deb6e0940"
}, {
  "url": "include/LD/assets/icons/action/new_custom97.svg",
  "revision": "f389e55ebae20cb52b6c2bf198a0bfb2"
}, {
  "url": "include/LD/assets/icons/action/new_custom98.svg",
  "revision": "e465521420765865fdbf9b43e281bd24"
}, {
  "url": "include/LD/assets/icons/action/new_custom99.svg",
  "revision": "a45a0b9363139d4a001eedc82554dbec"
}, {
  "url": "include/LD/assets/icons/action/new_event.svg",
  "revision": "feeedb395459c47a6f4fd6b51bc0691b"
}, {
  "url": "include/LD/assets/icons/action/new_group.svg",
  "revision": "df3fc1d8963a3eaadf898eb42fb0cda3"
}, {
  "url": "include/LD/assets/icons/action/new_lead.svg",
  "revision": "d7330315e0a2c124145bb9606de29a70"
}, {
  "url": "include/LD/assets/icons/action/new_note.svg",
  "revision": "71352fc56fc16ea7a4662766ed8b0b6f"
}, {
  "url": "include/LD/assets/icons/action/new_notebook.svg",
  "revision": "8afe66ff0cb9a81776e7c4ba4c05bb1d"
}, {
  "url": "include/LD/assets/icons/action/new_opportunity.svg",
  "revision": "babc67970bcf099f01513d1826add76b"
}, {
  "url": "include/LD/assets/icons/action/new_person_account.svg",
  "revision": "520fa00271cc9abef0f7b706be9bef4b"
}, {
  "url": "include/LD/assets/icons/action/new_task.svg",
  "revision": "3cd52d70366ec9f7a97a9867100e3a52"
}, {
  "url": "include/LD/assets/icons/action/new.svg",
  "revision": "1867ec504cd6bcdf955b7fffa8d51b4a"
}, {
  "url": "include/LD/assets/icons/action/password_unlock.svg",
  "revision": "959116234fc751cbe27b3db489e3d708"
}, {
  "url": "include/LD/assets/icons/action/preview.svg",
  "revision": "12ad8cbeb2b4486adb7fec8abf8397b3"
}, {
  "url": "include/LD/assets/icons/action/priority.svg",
  "revision": "037437751a74cb8103477d45a7274cfc"
}, {
  "url": "include/LD/assets/icons/action/question_post_action.svg",
  "revision": "0f762422b4e72ffb4e5c236df19d36e1"
}, {
  "url": "include/LD/assets/icons/action/quote.svg",
  "revision": "f642b96cf42e48663073e5b34f5bef3b"
}, {
  "url": "include/LD/assets/icons/action/recall.svg",
  "revision": "0fc40f0167d1f532c5e874f02f2a1a76"
}, {
  "url": "include/LD/assets/icons/action/record.svg",
  "revision": "adfcac8e9f67a8205565a91cc70e5b37"
}, {
  "url": "include/LD/assets/icons/action/refresh.svg",
  "revision": "3cf7d2a5ea45a0abe3066b257146cbda"
}, {
  "url": "include/LD/assets/icons/action/reject.svg",
  "revision": "8df3fa2eefea162616b7720dbc211489"
}, {
  "url": "include/LD/assets/icons/action/remove_relationship.svg",
  "revision": "c1c8fc526196ef796c20696ad5b39d72"
}, {
  "url": "include/LD/assets/icons/action/remove.svg",
  "revision": "8df3fa2eefea162616b7720dbc211489"
}, {
  "url": "include/LD/assets/icons/action/reset_password.svg",
  "revision": "3cf7d2a5ea45a0abe3066b257146cbda"
}, {
  "url": "include/LD/assets/icons/action/script.svg",
  "revision": "94161c56d723f2c2160cac4b612ec877"
}, {
  "url": "include/LD/assets/icons/action/share_file.svg",
  "revision": "ee87e138f87e45132f2fb616e702de6e"
}, {
  "url": "include/LD/assets/icons/action/share_link.svg",
  "revision": "d876ec47e93af49da332d469408be53f"
}, {
  "url": "include/LD/assets/icons/action/share_poll.svg",
  "revision": "858d9d0b09b5fb75b96579468623c340"
}, {
  "url": "include/LD/assets/icons/action/share_post.svg",
  "revision": "ee77077bbd46815bbb752eb3f9d07b9e"
}, {
  "url": "include/LD/assets/icons/action/share_thanks.svg",
  "revision": "723cc95e1da63ac807408dbc7da2153a"
}, {
  "url": "include/LD/assets/icons/action/share.svg",
  "revision": "d9c46349e91dc62065659d66438156f9"
}, {
  "url": "include/LD/assets/icons/action/sort.svg",
  "revision": "beca2649fcb6cd20d9efa6394248b00c"
}, {
  "url": "include/LD/assets/icons/action/submit_for_approval.svg",
  "revision": "973ada77057d4ea4346ce34d0b53340e"
}, {
  "url": "include/LD/assets/icons/action/update_status.svg",
  "revision": "09e57bc164441064f5fa366be5b667a7"
}, {
  "url": "include/LD/assets/icons/action/update.svg",
  "revision": "ce68e09bdef560e600515dacf7372081"
}, {
  "url": "include/LD/assets/icons/action/upload.svg",
  "revision": "c7a865ee55d392c0146e79737d4f9acf"
}, {
  "url": "include/LD/assets/icons/action/user_activation.svg",
  "revision": "e0c8b7224a6bd287f05f99d4c5c489a7"
}, {
  "url": "include/LD/assets/icons/action/user.svg",
  "revision": "eaf556fe598cd7822ef415c390df15dd"
}, {
  "url": "include/LD/assets/icons/action/view_relationship.svg",
  "revision": "a2f76d085e93967443b2594a8ef7bb96"
}, {
  "url": "include/LD/assets/icons/action/web_link.svg",
  "revision": "eb9a74852adcbb338206cd3d9174a5b4"
}, {
  "url": "include/LD/assets/icons/custom-sprite/svg/symbols-rtl.svg",
  "revision": "ec7295690c85ce97b330d4c91c237288"
}, {
  "url": "include/LD/assets/icons/custom-sprite/svg/symbols.svg",
  "revision": "3eeb1bd682daedabcdc71243ae64099f"
}, {
  "url": "include/LD/assets/icons/custom/custom1.svg",
  "revision": "e11274a2928044a549a6d85d09a31cc0"
}, {
  "url": "include/LD/assets/icons/custom/custom10.svg",
  "revision": "c95a1f566398a48a50628120a404e657"
}, {
  "url": "include/LD/assets/icons/custom/custom100.svg",
  "revision": "81b56bdd2353de6877cbf027f4a9709e"
}, {
  "url": "include/LD/assets/icons/custom/custom101.svg",
  "revision": "7449bf8cd0adfb5c9e890ef67f029a24"
}, {
  "url": "include/LD/assets/icons/custom/custom102.svg",
  "revision": "24a009db677f96fedee954679dc010d7"
}, {
  "url": "include/LD/assets/icons/custom/custom103.svg",
  "revision": "a055997724607e657f2870e4c562b747"
}, {
  "url": "include/LD/assets/icons/custom/custom104.svg",
  "revision": "2b1f235f7dbdae67637035b554936984"
}, {
  "url": "include/LD/assets/icons/custom/custom105.svg",
  "revision": "b5405030c8c5a07e31a19001f23898e4"
}, {
  "url": "include/LD/assets/icons/custom/custom106.svg",
  "revision": "63722f24533d0ac5324a7e4bb16b998a"
}, {
  "url": "include/LD/assets/icons/custom/custom107.svg",
  "revision": "7d206516860458fad3c942d81eab5960"
}, {
  "url": "include/LD/assets/icons/custom/custom108.svg",
  "revision": "58188d896775a194673d620dc2c5e53f"
}, {
  "url": "include/LD/assets/icons/custom/custom109.svg",
  "revision": "bce2e755f0700241715cc7253dca5e34"
}, {
  "url": "include/LD/assets/icons/custom/custom11.svg",
  "revision": "6fa0e806eceb83ac7f20152c1a77f7b2"
}, {
  "url": "include/LD/assets/icons/custom/custom110.svg",
  "revision": "d97d5851cad11b2ccb6e313fce374ef0"
}, {
  "url": "include/LD/assets/icons/custom/custom111.svg",
  "revision": "356f59548ddff1cbf5deb3e8ef06ebab"
}, {
  "url": "include/LD/assets/icons/custom/custom112.svg",
  "revision": "38ebfe25b3d5dddc507f36cdfe90a76e"
}, {
  "url": "include/LD/assets/icons/custom/custom113.svg",
  "revision": "448aa7a81990975ae4b5e6e65e818dd6"
}, {
  "url": "include/LD/assets/icons/custom/custom12.svg",
  "revision": "18ec8646ee4704c0244750012d3058a8"
}, {
  "url": "include/LD/assets/icons/custom/custom13.svg",
  "revision": "c41dfa6aa178f08a1db08484ca5be041"
}, {
  "url": "include/LD/assets/icons/custom/custom14.svg",
  "revision": "1ed0852d71605209fe2dbea9dfd9c19b"
}, {
  "url": "include/LD/assets/icons/custom/custom15.svg",
  "revision": "947db169f6fd625040bc0f50c50c9821"
}, {
  "url": "include/LD/assets/icons/custom/custom16.svg",
  "revision": "62254f356889c2c0097d92c22f1431bf"
}, {
  "url": "include/LD/assets/icons/custom/custom17.svg",
  "revision": "0e8f591addc0d5810a8e11d1918c7630"
}, {
  "url": "include/LD/assets/icons/custom/custom18.svg",
  "revision": "d47df4201afd9cb4bada172f8ec41fe1"
}, {
  "url": "include/LD/assets/icons/custom/custom19.svg",
  "revision": "0ed1283494abbe20657992596738a0e0"
}, {
  "url": "include/LD/assets/icons/custom/custom2.svg",
  "revision": "e7fe09ff64f5116129ff1414e7ae335f"
}, {
  "url": "include/LD/assets/icons/custom/custom20.svg",
  "revision": "6e0ab5eb2a927ea142872d16a0291f01"
}, {
  "url": "include/LD/assets/icons/custom/custom21.svg",
  "revision": "1da066b454326f9bef91654144f0d0db"
}, {
  "url": "include/LD/assets/icons/custom/custom22.svg",
  "revision": "80cbae2b313517d2955bd8a0cee82b64"
}, {
  "url": "include/LD/assets/icons/custom/custom23.svg",
  "revision": "3001d76a01c82b0b162a442c1dcd3fde"
}, {
  "url": "include/LD/assets/icons/custom/custom24.svg",
  "revision": "03d4fde3a3b8cff13f3370796dc112f6"
}, {
  "url": "include/LD/assets/icons/custom/custom25.svg",
  "revision": "7959a16dcac05a2d4db416c6aae32427"
}, {
  "url": "include/LD/assets/icons/custom/custom26.svg",
  "revision": "7b4f24ff4958187ec0fd05aede08a74a"
}, {
  "url": "include/LD/assets/icons/custom/custom27.svg",
  "revision": "e8d949e39ab1a7fff29427b492bb57c7"
}, {
  "url": "include/LD/assets/icons/custom/custom28.svg",
  "revision": "c4c4d15c8fe95804c94ca45bdb86827c"
}, {
  "url": "include/LD/assets/icons/custom/custom29.svg",
  "revision": "6b3c9eff081fe5e3674bfbbd4e218115"
}, {
  "url": "include/LD/assets/icons/custom/custom3.svg",
  "revision": "d7d6fe86f3d0b488bb9443d241faa21e"
}, {
  "url": "include/LD/assets/icons/custom/custom30.svg",
  "revision": "9a3ce0d7bcd0a3546ebf7c3a9bd7ca2b"
}, {
  "url": "include/LD/assets/icons/custom/custom31.svg",
  "revision": "d0fd986ace4166106690f99c825ccfdd"
}, {
  "url": "include/LD/assets/icons/custom/custom32.svg",
  "revision": "cac789c58726b586c59de4c3b90d2351"
}, {
  "url": "include/LD/assets/icons/custom/custom33.svg",
  "revision": "52d193840d70a3c39e15e63c8f0e0699"
}, {
  "url": "include/LD/assets/icons/custom/custom34.svg",
  "revision": "ab565efa322435a68f207cd0e9009123"
}, {
  "url": "include/LD/assets/icons/custom/custom35.svg",
  "revision": "0c72c28466d9facc87d3f09504ae60f1"
}, {
  "url": "include/LD/assets/icons/custom/custom36.svg",
  "revision": "7a555c167a8d2f2ba2f2bd30b9e58149"
}, {
  "url": "include/LD/assets/icons/custom/custom37.svg",
  "revision": "08c2228d108f678b944e262bf2efd93d"
}, {
  "url": "include/LD/assets/icons/custom/custom38.svg",
  "revision": "e3a49fb6664ba414130b68e7df44d2c3"
}, {
  "url": "include/LD/assets/icons/custom/custom39.svg",
  "revision": "8cab6f4768cbe78ca5a2665eab8fa228"
}, {
  "url": "include/LD/assets/icons/custom/custom4.svg",
  "revision": "6c5b6ad594e4a1472f5ed559e539ce1b"
}, {
  "url": "include/LD/assets/icons/custom/custom40.svg",
  "revision": "5176082c68d5f5809c861eaaa0174e56"
}, {
  "url": "include/LD/assets/icons/custom/custom41.svg",
  "revision": "72f3d2b8086cb78774fcc9bb8c8c7fab"
}, {
  "url": "include/LD/assets/icons/custom/custom42.svg",
  "revision": "c41dfa6aa178f08a1db08484ca5be041"
}, {
  "url": "include/LD/assets/icons/custom/custom43.svg",
  "revision": "7aa3899b8049eeaf38e7654c70fa176b"
}, {
  "url": "include/LD/assets/icons/custom/custom44.svg",
  "revision": "c43361dd8718bb47a02fe1c693bbb99b"
}, {
  "url": "include/LD/assets/icons/custom/custom45.svg",
  "revision": "c3631ed88c131abaac26dd9bc269fffa"
}, {
  "url": "include/LD/assets/icons/custom/custom46.svg",
  "revision": "c399dc7a985b9b265dd65f2aafdd45b3"
}, {
  "url": "include/LD/assets/icons/custom/custom47.svg",
  "revision": "6e0f46b6b15c782876d8c9014a92e0af"
}, {
  "url": "include/LD/assets/icons/custom/custom48.svg",
  "revision": "f48c9a7c24d290a97c9e1ab2841ce4f9"
}, {
  "url": "include/LD/assets/icons/custom/custom49.svg",
  "revision": "f9b9fb019aeae0fccd8ad54c0a6654c2"
}, {
  "url": "include/LD/assets/icons/custom/custom5.svg",
  "revision": "39db62b6a1f6925ccc2563537976ea68"
}, {
  "url": "include/LD/assets/icons/custom/custom50.svg",
  "revision": "84ce04eb35de5ee9fb911a00ed44d1b5"
}, {
  "url": "include/LD/assets/icons/custom/custom51.svg",
  "revision": "ab53bac46166923a6b20e5a721c04adc"
}, {
  "url": "include/LD/assets/icons/custom/custom52.svg",
  "revision": "cf858c5056b52c1a4ac34f4000b439c8"
}, {
  "url": "include/LD/assets/icons/custom/custom53.svg",
  "revision": "6349525282d6b10c4ea049d560abeffe"
}, {
  "url": "include/LD/assets/icons/custom/custom54.svg",
  "revision": "c764d2679564d539c2e1caebeec0fde7"
}, {
  "url": "include/LD/assets/icons/custom/custom55.svg",
  "revision": "88b3a6d4ac2b69ac138a450f79d40772"
}, {
  "url": "include/LD/assets/icons/custom/custom56.svg",
  "revision": "daceb8f126edd695b3a90e8fe6375206"
}, {
  "url": "include/LD/assets/icons/custom/custom57.svg",
  "revision": "44fc31759b44b61fede0fab6d6751bac"
}, {
  "url": "include/LD/assets/icons/custom/custom58.svg",
  "revision": "a37384ac6e525cee771ab189725aa1dc"
}, {
  "url": "include/LD/assets/icons/custom/custom59.svg",
  "revision": "96482fddfaf95e6ecb88d8f3c573370a"
}, {
  "url": "include/LD/assets/icons/custom/custom6.svg",
  "revision": "eb9aade4edb9df7299b6870a842edcf0"
}, {
  "url": "include/LD/assets/icons/custom/custom60.svg",
  "revision": "fd409af584d5f897c25811c58b3c7a46"
}, {
  "url": "include/LD/assets/icons/custom/custom61.svg",
  "revision": "0093854f2f818a8ded61f3d60f730d7a"
}, {
  "url": "include/LD/assets/icons/custom/custom62.svg",
  "revision": "25a2801293b8c4796c317531463b8e56"
}, {
  "url": "include/LD/assets/icons/custom/custom63.svg",
  "revision": "bcef9a5b18fdc34ca4be1ef38851e3d9"
}, {
  "url": "include/LD/assets/icons/custom/custom64.svg",
  "revision": "2dca3819aa6fbae9d98107e7f18ae398"
}, {
  "url": "include/LD/assets/icons/custom/custom65.svg",
  "revision": "75627fad4de18881bfa97b9b02a2d680"
}, {
  "url": "include/LD/assets/icons/custom/custom66.svg",
  "revision": "50a238d52f972af7851403c558d58c1f"
}, {
  "url": "include/LD/assets/icons/custom/custom67.svg",
  "revision": "c5402e37292cec38b38df04456ff1cb1"
}, {
  "url": "include/LD/assets/icons/custom/custom68.svg",
  "revision": "d41769ced7274fafb8eb4b593929b564"
}, {
  "url": "include/LD/assets/icons/custom/custom69.svg",
  "revision": "2780edafbd505e23366484ffa2e1f847"
}, {
  "url": "include/LD/assets/icons/custom/custom7.svg",
  "revision": "981bb9d27c82d2aac23f6fc135a648ac"
}, {
  "url": "include/LD/assets/icons/custom/custom70.svg",
  "revision": "53704ff16df494965a544789c5c70400"
}, {
  "url": "include/LD/assets/icons/custom/custom71.svg",
  "revision": "e918b562bb78d79feaef6f4181c0e120"
}, {
  "url": "include/LD/assets/icons/custom/custom72.svg",
  "revision": "750daecd2741000a4f0a981eb7e86755"
}, {
  "url": "include/LD/assets/icons/custom/custom73.svg",
  "revision": "c68681e5cc0e981c05d86fda604b8385"
}, {
  "url": "include/LD/assets/icons/custom/custom74.svg",
  "revision": "c3659d9e177968cf42457c80b3582dfd"
}, {
  "url": "include/LD/assets/icons/custom/custom75.svg",
  "revision": "a7181505c76fcf8f58ff0db6ee313637"
}, {
  "url": "include/LD/assets/icons/custom/custom76.svg",
  "revision": "5519d4ce220ceeaf72e4d567fe7e19d7"
}, {
  "url": "include/LD/assets/icons/custom/custom77.svg",
  "revision": "c2782b7c2339c2bc889d90e33eb21edf"
}, {
  "url": "include/LD/assets/icons/custom/custom78.svg",
  "revision": "bbabab20c17265cfb5220cfff6484441"
}, {
  "url": "include/LD/assets/icons/custom/custom79.svg",
  "revision": "775b59d75d122e077b0dca71d170212a"
}, {
  "url": "include/LD/assets/icons/custom/custom8.svg",
  "revision": "ad82e04c5f61e7c4743703e23c95ae60"
}, {
  "url": "include/LD/assets/icons/custom/custom80.svg",
  "revision": "aeff6280e6379eea2feef98199b46ae0"
}, {
  "url": "include/LD/assets/icons/custom/custom81.svg",
  "revision": "fe021220f6cb6c65ecf8464f4a1e53b1"
}, {
  "url": "include/LD/assets/icons/custom/custom82.svg",
  "revision": "49ac97858cf072278d42b1490f2fb950"
}, {
  "url": "include/LD/assets/icons/custom/custom83.svg",
  "revision": "138188e0dbaf1addb9563347663da228"
}, {
  "url": "include/LD/assets/icons/custom/custom84.svg",
  "revision": "2c3c2beca11788b7ca4053ab92160176"
}, {
  "url": "include/LD/assets/icons/custom/custom85.svg",
  "revision": "ea30eb530d9e052dee7fb191a938611a"
}, {
  "url": "include/LD/assets/icons/custom/custom86.svg",
  "revision": "0edaf3e2e56738e085e8c2be93cc4903"
}, {
  "url": "include/LD/assets/icons/custom/custom87.svg",
  "revision": "21ee279f8fbbbcfdc878ac1633283ea6"
}, {
  "url": "include/LD/assets/icons/custom/custom88.svg",
  "revision": "b01d575af1b19fa1b8f5d4ab6f9dff65"
}, {
  "url": "include/LD/assets/icons/custom/custom89.svg",
  "revision": "ecf7c7896c0411c278bf48f0adc773fe"
}, {
  "url": "include/LD/assets/icons/custom/custom9.svg",
  "revision": "e159aa4bc079cb55c62d1b3d7d3f57af"
}, {
  "url": "include/LD/assets/icons/custom/custom90.svg",
  "revision": "10664f230c672aa301ff36be24cbc800"
}, {
  "url": "include/LD/assets/icons/custom/custom91.svg",
  "revision": "8360e1e365daba0b269cd4be968e19f5"
}, {
  "url": "include/LD/assets/icons/custom/custom92.svg",
  "revision": "3c08ddf594bce9465d7d23814e4b29c5"
}, {
  "url": "include/LD/assets/icons/custom/custom93.svg",
  "revision": "c9c363e1601496a68590ab1eb475863d"
}, {
  "url": "include/LD/assets/icons/custom/custom94.svg",
  "revision": "e6f880c34b999db1a673406da61e0d81"
}, {
  "url": "include/LD/assets/icons/custom/custom95.svg",
  "revision": "d300412c4a8b078511e0a146f13b3351"
}, {
  "url": "include/LD/assets/icons/custom/custom96.svg",
  "revision": "9c45624634cf2537d91fd254c03e8ec1"
}, {
  "url": "include/LD/assets/icons/custom/custom97.svg",
  "revision": "18c22ae46d6010e0492c2ea3ae80a960"
}, {
  "url": "include/LD/assets/icons/custom/custom98.svg",
  "revision": "edcf327dbe2de7f133bf713d92aee9d9"
}, {
  "url": "include/LD/assets/icons/custom/custom99.svg",
  "revision": "b0363502d3a25ad3f9f0a07a716a65a0"
}, {
  "url": "include/LD/assets/icons/doctype-sprite/svg/symbols-rtl.svg",
  "revision": "13dabdc82fc9d693f512de42d7c13c8c"
}, {
  "url": "include/LD/assets/icons/doctype-sprite/svg/symbols.svg",
  "revision": "b2a21821ff68628a87fa121cadfcc983"
}, {
  "url": "include/LD/assets/icons/doctype/ai.svg",
  "revision": "0ecd8226c7fef2b3ff45be98628a4881"
}, {
  "url": "include/LD/assets/icons/doctype/attachment.svg",
  "revision": "54574525b11121669ac899db0ba65dc5"
}, {
  "url": "include/LD/assets/icons/doctype/audio.svg",
  "revision": "831bf5ccf2032da86ccaa606a8397cdb"
}, {
  "url": "include/LD/assets/icons/doctype/box_notes.svg",
  "revision": "12f3f1ee189a2afb6ad6548d465301be"
}, {
  "url": "include/LD/assets/icons/doctype/csv.svg",
  "revision": "bf6fb5ca1ef8e9909ea56d55501bac2f"
}, {
  "url": "include/LD/assets/icons/doctype/eps.svg",
  "revision": "d2b0ce82758e0e19a7a19f326f8de7fa"
}, {
  "url": "include/LD/assets/icons/doctype/excel.svg",
  "revision": "b40d03d841485a5312e921e7c71a1765"
}, {
  "url": "include/LD/assets/icons/doctype/exe.svg",
  "revision": "d5b641ce11a1462359f2627a9b05e64a"
}, {
  "url": "include/LD/assets/icons/doctype/flash.svg",
  "revision": "a62d5a699c2afdc90932e4bfb81bf727"
}, {
  "url": "include/LD/assets/icons/doctype/folder.svg",
  "revision": "5e0543f198ea5454272edc6d06b21ba1"
}, {
  "url": "include/LD/assets/icons/doctype/gdoc.svg",
  "revision": "d9a596444da8709070ab8352a69ca4c3"
}, {
  "url": "include/LD/assets/icons/doctype/gdocs.svg",
  "revision": "7d7ced055e19230c64e56a096cef3f7d"
}, {
  "url": "include/LD/assets/icons/doctype/gform.svg",
  "revision": "7c0fdad722e0e80824d5c6c611576cd7"
}, {
  "url": "include/LD/assets/icons/doctype/gpres.svg",
  "revision": "575faaeb144f107fc4893862d8c94edc"
}, {
  "url": "include/LD/assets/icons/doctype/gsheet.svg",
  "revision": "b1ea4a583e41207664510b34e9b0499d"
}, {
  "url": "include/LD/assets/icons/doctype/html.svg",
  "revision": "302a22ecebbd0b27f0a957ef725208a9"
}, {
  "url": "include/LD/assets/icons/doctype/image.svg",
  "revision": "5295b645acd4143f11788ebf0d571cb8"
}, {
  "url": "include/LD/assets/icons/doctype/keynote.svg",
  "revision": "4f8a1176e0d3d3b2d405dbfb88278f98"
}, {
  "url": "include/LD/assets/icons/doctype/library_folder.svg",
  "revision": "c28ce78251c04f3acf648948a28950fd"
}, {
  "url": "include/LD/assets/icons/doctype/link.svg",
  "revision": "29c725f37f51c09dcba36902d9df2a35"
}, {
  "url": "include/LD/assets/icons/doctype/mp4.svg",
  "revision": "107541bd124636d925d63a777e9c2388"
}, {
  "url": "include/LD/assets/icons/doctype/overlay.svg",
  "revision": "3158671ed83aac4807bf85e87547aaad"
}, {
  "url": "include/LD/assets/icons/doctype/pack.svg",
  "revision": "e7bdd3c1ee2c9202df7e785682277b3d"
}, {
  "url": "include/LD/assets/icons/doctype/pages.svg",
  "revision": "659986515cf433c3942cb2f9aa74eb09"
}, {
  "url": "include/LD/assets/icons/doctype/pdf.svg",
  "revision": "8c426522b596af0a71d2afef67a17e0a"
}, {
  "url": "include/LD/assets/icons/doctype/ppt.svg",
  "revision": "6326298a23be79a57ad974ef17dd84b3"
}, {
  "url": "include/LD/assets/icons/doctype/psd.svg",
  "revision": "49594ef4509c9e90a85163f134b0ea60"
}, {
  "url": "include/LD/assets/icons/doctype/quip_doc.svg",
  "revision": "04a0b126d0ddbc91a88b603deaebebf4"
}, {
  "url": "include/LD/assets/icons/doctype/quip_sheet.svg",
  "revision": "47ba836f21922f9bef5b793825ea46bc"
}, {
  "url": "include/LD/assets/icons/doctype/quip_slide.svg",
  "revision": "af912428f73acb363a6d026ad5760048"
}, {
  "url": "include/LD/assets/icons/doctype/rtf.svg",
  "revision": "77a07e3925b631176ab5e97c916faba9"
}, {
  "url": "include/LD/assets/icons/doctype/slide.svg",
  "revision": "375973828e5893d3711a2058967e1eea"
}, {
  "url": "include/LD/assets/icons/doctype/stypi.svg",
  "revision": "802f99c81ccfb00627849f91a9c38126"
}, {
  "url": "include/LD/assets/icons/doctype/txt.svg",
  "revision": "b34e4ed5790537fe35c19e2934c171da"
}, {
  "url": "include/LD/assets/icons/doctype/unknown.svg",
  "revision": "99b509624d232eb06c423d07c6e51e84"
}, {
  "url": "include/LD/assets/icons/doctype/video.svg",
  "revision": "b6ef22ec70bf35c1eb77f1346fdf3a18"
}, {
  "url": "include/LD/assets/icons/doctype/visio.svg",
  "revision": "82debd6620682c33cd78d060f4e0888a"
}, {
  "url": "include/LD/assets/icons/doctype/webex.svg",
  "revision": "1fbd69f4c64a27e02155e6b30cc81414"
}, {
  "url": "include/LD/assets/icons/doctype/word.svg",
  "revision": "52c76dfa5fa71448c5d954455f71fd06"
}, {
  "url": "include/LD/assets/icons/doctype/xml.svg",
  "revision": "7524dace22c7de84fe82675b40b2ed5d"
}, {
  "url": "include/LD/assets/icons/doctype/zip.svg",
  "revision": "5c8edbb90bf5f9300e6a04192ca00332"
}, {
  "url": "include/LD/assets/icons/standard-sprite/svg/symbols-rtl.svg",
  "revision": "d95140204e963d2321a3af68efaa8044"
}, {
  "url": "include/LD/assets/icons/standard-sprite/svg/symbols.svg",
  "revision": "54107b52411070ffba28acd41448bb27"
}, {
  "url": "include/LD/assets/icons/standard/account.svg",
  "revision": "abe646477566bf7244dc34d041686f6d"
}, {
  "url": "include/LD/assets/icons/standard/action_list_component.svg",
  "revision": "4f29def4c8044062fc1249322252e9c2"
}, {
  "url": "include/LD/assets/icons/standard/actions_and_buttons.svg",
  "revision": "2393245eac36ef617b02fa2ce75e4f2d"
}, {
  "url": "include/LD/assets/icons/standard/address.svg",
  "revision": "d500b97e3eb64aac8ea82f78b1be2574"
}, {
  "url": "include/LD/assets/icons/standard/agent_session.svg",
  "revision": "2e47aa4a3c94aa0a721590b581e5876c"
}, {
  "url": "include/LD/assets/icons/standard/all.svg",
  "revision": "9297f763202c64b3bd79e2971fcb1cfc"
}, {
  "url": "include/LD/assets/icons/standard/announcement.svg",
  "revision": "1f3ee691e8ff9194d97e1e006b367dd4"
}, {
  "url": "include/LD/assets/icons/standard/answer_best.svg",
  "revision": "d402b8c490498588141cd5842928a505"
}, {
  "url": "include/LD/assets/icons/standard/answer_private.svg",
  "revision": "55dd82f0bc774fe864b4cb50229e232a"
}, {
  "url": "include/LD/assets/icons/standard/answer_public.svg",
  "revision": "7c5e61a8560f40253debf53afb1e0ddb"
}, {
  "url": "include/LD/assets/icons/standard/apex_plugin.svg",
  "revision": "f2d5fd1d60b2356796a8b947e6ba331f"
}, {
  "url": "include/LD/assets/icons/standard/apex.svg",
  "revision": "0c2fabda3aacb7482854c84c0d6e9366"
}, {
  "url": "include/LD/assets/icons/standard/app.svg",
  "revision": "73b8799dfcd266bb05d9e8db68ac9b03"
}, {
  "url": "include/LD/assets/icons/standard/approval.svg",
  "revision": "a06057773e58d10308af47d059fa9d24"
}, {
  "url": "include/LD/assets/icons/standard/apps_admin.svg",
  "revision": "d744ef3ecaeda679ee3dc0918941b85d"
}, {
  "url": "include/LD/assets/icons/standard/apps.svg",
  "revision": "fec5e4e7e92bf0fdaa8f0a389ad286e6"
}, {
  "url": "include/LD/assets/icons/standard/article.svg",
  "revision": "cbc881a1c9224c09d6edda3b704ed46a"
}, {
  "url": "include/LD/assets/icons/standard/asset_relationship.svg",
  "revision": "c157cb67952c8ef9cb090f9c5b1c0ce1"
}, {
  "url": "include/LD/assets/icons/standard/assigned_resource.svg",
  "revision": "c93f6407cd8f2933d7f692856bf777e5"
}, {
  "url": "include/LD/assets/icons/standard/assignment.svg",
  "revision": "e271261db7c260e120b2a75f7cfa2b74"
}, {
  "url": "include/LD/assets/icons/standard/avatar_loading.svg",
  "revision": "da372795dd0e9f3f7d0b44096ac83e11"
}, {
  "url": "include/LD/assets/icons/standard/avatar.svg",
  "revision": "55c51356065facdfcda2a6064b08a9cb"
}, {
  "url": "include/LD/assets/icons/standard/bot_training.svg",
  "revision": "9a3056dfe6d4e3da284bd3f7aff105b8"
}, {
  "url": "include/LD/assets/icons/standard/bot.svg",
  "revision": "85d6216a5f9fd15603088848f02482cb"
}, {
  "url": "include/LD/assets/icons/standard/branch_merge.svg",
  "revision": "93f8f9796544daff3e9de5943bddb674"
}, {
  "url": "include/LD/assets/icons/standard/brand.svg",
  "revision": "9e2659363bec74ed23e871dbb3e2dc91"
}, {
  "url": "include/LD/assets/icons/standard/business_hours.svg",
  "revision": "e49e93413d3017df3e84710218bca1cc"
}, {
  "url": "include/LD/assets/icons/standard/buyer_account.svg",
  "revision": "089a9d97b02137421fb0cd408ce1b98f"
}, {
  "url": "include/LD/assets/icons/standard/buyer_group.svg",
  "revision": "97958863e1c2cc6472cabfffd35b2a42"
}, {
  "url": "include/LD/assets/icons/standard/calibration.svg",
  "revision": "20f6a8d90b7e279a086872e0d43ce1e0"
}, {
  "url": "include/LD/assets/icons/standard/call_history.svg",
  "revision": "1c74918374fb15af0dc2ee09860c2c4f"
}, {
  "url": "include/LD/assets/icons/standard/call.svg",
  "revision": "80cbae2b313517d2955bd8a0cee82b64"
}, {
  "url": "include/LD/assets/icons/standard/campaign_members.svg",
  "revision": "c36dd61941ac983052e481a0a70d2363"
}, {
  "url": "include/LD/assets/icons/standard/campaign.svg",
  "revision": "3563d73e8f3d97f2619550b1de01cc9a"
}, {
  "url": "include/LD/assets/icons/standard/canvas.svg",
  "revision": "9710f5c14c8953089b0c03b2cb188d4c"
}, {
  "url": "include/LD/assets/icons/standard/carousel.svg",
  "revision": "9521133fc942d732f04ff98a24722e89"
}, {
  "url": "include/LD/assets/icons/standard/case_change_status.svg",
  "revision": "24d420dabb338c7296636e482189ffef"
}, {
  "url": "include/LD/assets/icons/standard/case_comment.svg",
  "revision": "14235379d3f9dee48e77287b182fa60b"
}, {
  "url": "include/LD/assets/icons/standard/case_email.svg",
  "revision": "f3cfaa7db60cc108db66e66d865f2796"
}, {
  "url": "include/LD/assets/icons/standard/case_log_a_call.svg",
  "revision": "945f69182f84d15e3d4f7ec960c46be4"
}, {
  "url": "include/LD/assets/icons/standard/case_milestone.svg",
  "revision": "3befdef263cfaa06bfb648708448bebe"
}, {
  "url": "include/LD/assets/icons/standard/case_transcript.svg",
  "revision": "9f9ce13316540ba785a044d8e8a643d5"
}, {
  "url": "include/LD/assets/icons/standard/case.svg",
  "revision": "ee400a23a97646a54caab05336c08b30"
}, {
  "url": "include/LD/assets/icons/standard/catalog.svg",
  "revision": "661a25daccf5b5609063c47ae411b05b"
}, {
  "url": "include/LD/assets/icons/standard/category.svg",
  "revision": "27c611420d4d913ad6a9e4e97ed98031"
}, {
  "url": "include/LD/assets/icons/standard/channel_program_history.svg",
  "revision": "c2e4013086e659784940c288111e45cb"
}, {
  "url": "include/LD/assets/icons/standard/channel_program_levels.svg",
  "revision": "4d0e1e8e1dd570c1b124845fd511f488"
}, {
  "url": "include/LD/assets/icons/standard/channel_program_members.svg",
  "revision": "8a019d3c0b80a85b6dcdbe7b56eef644"
}, {
  "url": "include/LD/assets/icons/standard/channel_programs.svg",
  "revision": "b778b3199c5a54a38e924961cb7584bf"
}, {
  "url": "include/LD/assets/icons/standard/chart.svg",
  "revision": "9bdcdad73ec527592f286594de8538d3"
}, {
  "url": "include/LD/assets/icons/standard/choice.svg",
  "revision": "84eb74de4e8b2e6b452b6609fe0ccbbd"
}, {
  "url": "include/LD/assets/icons/standard/client.svg",
  "revision": "2fd7e3cd53af1da3ccacec4fff577e50"
}, {
  "url": "include/LD/assets/icons/standard/cms.svg",
  "revision": "8c0ff3364502cd61fb0c296a472fdd49"
}, {
  "url": "include/LD/assets/icons/standard/coaching.svg",
  "revision": "af220e45b95dfa536a452a4b90a16cd4"
}, {
  "url": "include/LD/assets/icons/standard/code_playground.svg",
  "revision": "20306bfdcd1a4cce98fbfbc280da86d9"
}, {
  "url": "include/LD/assets/icons/standard/collection_variable.svg",
  "revision": "e18a1b477084c092e9fe8abfbb66aa5e"
}, {
  "url": "include/LD/assets/icons/standard/connected_apps.svg",
  "revision": "ea6114f2ba4c6b46dd5eced0730199ec"
}, {
  "url": "include/LD/assets/icons/standard/constant.svg",
  "revision": "36eb2113204cdaea5433d3f75b74fb41"
}, {
  "url": "include/LD/assets/icons/standard/contact_list.svg",
  "revision": "3fe73cdc28ac16beb051f05468368cb6"
}, {
  "url": "include/LD/assets/icons/standard/contact_request.svg",
  "revision": "0358863754d6bb6ba7ecc9033a866c2c"
}, {
  "url": "include/LD/assets/icons/standard/contact.svg",
  "revision": "93760c429c87b0c2893723a807014a6c"
}, {
  "url": "include/LD/assets/icons/standard/contract_line_item.svg",
  "revision": "0afdaf70816ea4b8fa31d50e33ba5fb3"
}, {
  "url": "include/LD/assets/icons/standard/contract.svg",
  "revision": "a1b6cc1b68335bb3a22577339f265883"
}, {
  "url": "include/LD/assets/icons/standard/currency_input.svg",
  "revision": "6f3998ed2479c79169e794215a28632c"
}, {
  "url": "include/LD/assets/icons/standard/currency.svg",
  "revision": "51928d573a5c93fa61ebf0e4ff06fc58"
}, {
  "url": "include/LD/assets/icons/standard/custom_notification.svg",
  "revision": "885225a68fd72e2897771921a460e166"
}, {
  "url": "include/LD/assets/icons/standard/custom.svg",
  "revision": "0ed1283494abbe20657992596738a0e0"
}, {
  "url": "include/LD/assets/icons/standard/customer_360.svg",
  "revision": "ca6c5351ccfe7bdcbb151e17ce4cc03e"
}, {
  "url": "include/LD/assets/icons/standard/customer_portal_users.svg",
  "revision": "3f2c6df261b8a1d70c3ab91f73f6d4df"
}, {
  "url": "include/LD/assets/icons/standard/customers.svg",
  "revision": "195c842f04310fde226cd83b270f7c73"
}, {
  "url": "include/LD/assets/icons/standard/dashboard_ea.svg",
  "revision": "bb2c6f97196fb659721d3d82f2a8f5ad"
}, {
  "url": "include/LD/assets/icons/standard/dashboard.svg",
  "revision": "17ca577a6ac8c41d14d3514b6637f1ac"
}, {
  "url": "include/LD/assets/icons/standard/data_integration_hub.svg",
  "revision": "bc420fa730d8570a7a55b4ae36fef15b"
}, {
  "url": "include/LD/assets/icons/standard/datadotcom.svg",
  "revision": "818268da2835b1ad46dca1771e04b0f2"
}, {
  "url": "include/LD/assets/icons/standard/dataset.svg",
  "revision": "32620ef3f406c7e4a45e3918f3aa667c"
}, {
  "url": "include/LD/assets/icons/standard/date_input.svg",
  "revision": "4b35fcbea1b8f0d2622de81b69b38a6d"
}, {
  "url": "include/LD/assets/icons/standard/date_time.svg",
  "revision": "1a5cae468291cbd96c2f27979b124ed8"
}, {
  "url": "include/LD/assets/icons/standard/decision.svg",
  "revision": "71e2c9cbbf5c58ee9e06360d26eee1ef"
}, {
  "url": "include/LD/assets/icons/standard/default.svg",
  "revision": "776fa46dafcc71bb1c59702212525dc3"
}, {
  "url": "include/LD/assets/icons/standard/delegated_account.svg",
  "revision": "165a7b5d687d6fed3fb094581b312315"
}, {
  "url": "include/LD/assets/icons/standard/display_rich_text.svg",
  "revision": "e42df585f83028f9d4f756a9757a1f8f"
}, {
  "url": "include/LD/assets/icons/standard/display_text.svg",
  "revision": "a20b029a4dcc526b4d4eeaf40218011d"
}, {
  "url": "include/LD/assets/icons/standard/document.svg",
  "revision": "4818ca8248c4c24666fcae7e24b77305"
}, {
  "url": "include/LD/assets/icons/standard/drafts.svg",
  "revision": "38b015c87555fa6adba2b6f724a1b943"
}, {
  "url": "include/LD/assets/icons/standard/dynamic_record_choice.svg",
  "revision": "546d57e1cfba081035b078ae422acf8c"
}, {
  "url": "include/LD/assets/icons/standard/education.svg",
  "revision": "97540a829967845e0664da0d7c3b6da1"
}, {
  "url": "include/LD/assets/icons/standard/email_chatter.svg",
  "revision": "1823ba0303260e4c3752a3281774087a"
}, {
  "url": "include/LD/assets/icons/standard/email.svg",
  "revision": "1823ba0303260e4c3752a3281774087a"
}, {
  "url": "include/LD/assets/icons/standard/empty.svg",
  "revision": "620dbcd993a5c760ced27e2ec365d57d"
}, {
  "url": "include/LD/assets/icons/standard/endorsement.svg",
  "revision": "75119b280368598425a8dd3c17248ee5"
}, {
  "url": "include/LD/assets/icons/standard/entitlement_policy.svg",
  "revision": "8e33911cf1c6b4fa804092a05e43ae34"
}, {
  "url": "include/LD/assets/icons/standard/entitlement_process.svg",
  "revision": "4c92a9f48361810752fb27af62a477b1"
}, {
  "url": "include/LD/assets/icons/standard/entitlement_template.svg",
  "revision": "eb36eded65bed19a5ac873dfb2370dc5"
}, {
  "url": "include/LD/assets/icons/standard/entitlement.svg",
  "revision": "2ad92d86cd270e66c38e051116987818"
}, {
  "url": "include/LD/assets/icons/standard/entity_milestone.svg",
  "revision": "f2e9d1f3ea791fca80ecdeaa33d0ca88"
}, {
  "url": "include/LD/assets/icons/standard/entity.svg",
  "revision": "c87517e6b757f9f4c357e12b5aed9ddc"
}, {
  "url": "include/LD/assets/icons/standard/environment_hub.svg",
  "revision": "5f782c1886f018a0fecb2c9a512bced3"
}, {
  "url": "include/LD/assets/icons/standard/event.svg",
  "revision": "3f0137c043a0d045fd4fb18c3612bec3"
}, {
  "url": "include/LD/assets/icons/standard/events.svg",
  "revision": "43230c90e47dddbd1d193008869d9858"
}, {
  "url": "include/LD/assets/icons/standard/feed.svg",
  "revision": "d76a498ab2b9db3b713dbf7574b6a3ea"
}, {
  "url": "include/LD/assets/icons/standard/feedback.svg",
  "revision": "9f54bde8eda984e876eea62c33386fa3"
}, {
  "url": "include/LD/assets/icons/standard/file.svg",
  "revision": "0d3eb96abce8ca98c0801793fbbb7f86"
}, {
  "url": "include/LD/assets/icons/standard/filter.svg",
  "revision": "f58a8b97deafbbea7e0ba3a79130a3c4"
}, {
  "url": "include/LD/assets/icons/standard/first_non_empty.svg",
  "revision": "00e6fe62d74fb5b75191f7d2160c6cd7"
}, {
  "url": "include/LD/assets/icons/standard/flow.svg",
  "revision": "8f83b8cca415401aff62ed2a598591c2"
}, {
  "url": "include/LD/assets/icons/standard/folder.svg",
  "revision": "1d5f7ab0932757e236592c18353f50d2"
}, {
  "url": "include/LD/assets/icons/standard/forecasts.svg",
  "revision": "21301029a700b074a1769b2bf0f1dd4d"
}, {
  "url": "include/LD/assets/icons/standard/formula.svg",
  "revision": "7acf5904b54900f7a141025f2de9b023"
}, {
  "url": "include/LD/assets/icons/standard/fulfillment_order.svg",
  "revision": "1684ea3282887646ace55b104100d307"
}, {
  "url": "include/LD/assets/icons/standard/generic_loading.svg",
  "revision": "782f85a63451e493542e14beb27ced53"
}, {
  "url": "include/LD/assets/icons/standard/global_constant.svg",
  "revision": "d0416bceeeab7545a7b02cadf427d295"
}, {
  "url": "include/LD/assets/icons/standard/goals.svg",
  "revision": "c7f0837c315f35609550b0bb0ccf814f"
}, {
  "url": "include/LD/assets/icons/standard/group_loading.svg",
  "revision": "09be219e7db27edc6259c529fafc83ad"
}, {
  "url": "include/LD/assets/icons/standard/groups.svg",
  "revision": "d730cef517f7d369169ae8b049cd451e"
}, {
  "url": "include/LD/assets/icons/standard/hierarchy.svg",
  "revision": "13b9f54d9231c966a2ba8538766b6766"
}, {
  "url": "include/LD/assets/icons/standard/high_velocity_sales.svg",
  "revision": "040de6e200d7922e5ad5498a138a4755"
}, {
  "url": "include/LD/assets/icons/standard/home.svg",
  "revision": "e836614fc06d1536ec4364cf5047d430"
}, {
  "url": "include/LD/assets/icons/standard/household.svg",
  "revision": "fa51e4a34607d46fc268fef4dd6981b9"
}, {
  "url": "include/LD/assets/icons/standard/individual.svg",
  "revision": "f1d8907ba9a54b9591045aa9f9f2014b"
}, {
  "url": "include/LD/assets/icons/standard/insights.svg",
  "revision": "0ef2d6f5dfb43e87025e05872fa61b17"
}, {
  "url": "include/LD/assets/icons/standard/instore_locations.svg",
  "revision": "8c765666b48dfd58f0f97bd9a14750a1"
}, {
  "url": "include/LD/assets/icons/standard/investment_account.svg",
  "revision": "e44ac0d159c5a8c79fd01000492aa539"
}, {
  "url": "include/LD/assets/icons/standard/invocable_action.svg",
  "revision": "e83e9b60f82fc5675102a567a804367f"
}, {
  "url": "include/LD/assets/icons/standard/iot_context.svg",
  "revision": "af0d61b2d58c65efb778d56fbd95e5c3"
}, {
  "url": "include/LD/assets/icons/standard/iot_orchestrations.svg",
  "revision": "4792c97d2ac6d9c47c2e622f1a23c0aa"
}, {
  "url": "include/LD/assets/icons/standard/javascript_button.svg",
  "revision": "fd6b171c6600de516bb7525bb54d87dc"
}, {
  "url": "include/LD/assets/icons/standard/job_profile.svg",
  "revision": "3de9127f58fc5a392c3aa56cea4117c1"
}, {
  "url": "include/LD/assets/icons/standard/kanban.svg",
  "revision": "6bc9d2babb79b548d0c4153fd1eb81db"
}, {
  "url": "include/LD/assets/icons/standard/knowledge.svg",
  "revision": "cbc881a1c9224c09d6edda3b704ed46a"
}, {
  "url": "include/LD/assets/icons/standard/lead_insights.svg",
  "revision": "e65766e481f2bb9eba9b73b53401f726"
}, {
  "url": "include/LD/assets/icons/standard/lead_list.svg",
  "revision": "2c98fdc7eb4e681f4363cab410384bf4"
}, {
  "url": "include/LD/assets/icons/standard/lead.svg",
  "revision": "1554b323d7e66ac451cebe0a9cc5a145"
}, {
  "url": "include/LD/assets/icons/standard/letterhead.svg",
  "revision": "74a4f933f3f46f6acd6b8f1ed50f29fc"
}, {
  "url": "include/LD/assets/icons/standard/lightning_component.svg",
  "revision": "5fa513243348388b55173ca2794feb33"
}, {
  "url": "include/LD/assets/icons/standard/lightning_usage.svg",
  "revision": "6655cfef2ac164e6bdc7bf325ef7f494"
}, {
  "url": "include/LD/assets/icons/standard/link.svg",
  "revision": "d424918a9d5b2219b4bfc41f795db0c3"
}, {
  "url": "include/LD/assets/icons/standard/list_email.svg",
  "revision": "b628fa622e77b0207027bf75adac251e"
}, {
  "url": "include/LD/assets/icons/standard/live_chat_visitor.svg",
  "revision": "5fedc89b1c177901f3adbf78d81c1e58"
}, {
  "url": "include/LD/assets/icons/standard/live_chat.svg",
  "revision": "a044bb1e9eb88c8e6a1bcf8d561db3ea"
}, {
  "url": "include/LD/assets/icons/standard/location.svg",
  "revision": "8aad13e25599d2d8e7471c3229391027"
}, {
  "url": "include/LD/assets/icons/standard/log_a_call.svg",
  "revision": "2d93bab55eb211eb8c2f70c7417bc4cd"
}, {
  "url": "include/LD/assets/icons/standard/logging.svg",
  "revision": "8e32b58a46c10fb2a609c6b0b617f301"
}, {
  "url": "include/LD/assets/icons/standard/loop.svg",
  "revision": "6ac8e8f66a47097ef82542cea55735b5"
}, {
  "url": "include/LD/assets/icons/standard/macros.svg",
  "revision": "0d3abd2220760aef917b205f13033fd3"
}, {
  "url": "include/LD/assets/icons/standard/maintenance_asset.svg",
  "revision": "c7b5a260d77dfeb337dd2aa8b0c5afb5"
}, {
  "url": "include/LD/assets/icons/standard/maintenance_plan.svg",
  "revision": "2a4ebcb703fde1c01ed21583e58c56ca"
}, {
  "url": "include/LD/assets/icons/standard/marketing_actions.svg",
  "revision": "6a6a184d8568f4ddf30705ff8f0d0fdc"
}, {
  "url": "include/LD/assets/icons/standard/merge.svg",
  "revision": "4e4ddbe0c57f14ab38ff778fe8acaa0e"
}, {
  "url": "include/LD/assets/icons/standard/messaging_conversation.svg",
  "revision": "26dc1448dd43a215db82e8060744987e"
}, {
  "url": "include/LD/assets/icons/standard/messaging_session.svg",
  "revision": "fe95834aa83b707dfa32ffb7b34df201"
}, {
  "url": "include/LD/assets/icons/standard/messaging_user.svg",
  "revision": "f5d2d1f0a7870d5cfbd065f552030b3f"
}, {
  "url": "include/LD/assets/icons/standard/metrics.svg",
  "revision": "c2a208ec4939355b8dc0a12e04483e80"
}, {
  "url": "include/LD/assets/icons/standard/multi_picklist.svg",
  "revision": "8be5beef4dbfca683a5eaddfc41de2d1"
}, {
  "url": "include/LD/assets/icons/standard/multi_select_checkbox.svg",
  "revision": "ecfda61165a6cf7e4a81307ea7ad6ac0"
}, {
  "url": "include/LD/assets/icons/standard/news.svg",
  "revision": "0ef2d6f5dfb43e87025e05872fa61b17"
}, {
  "url": "include/LD/assets/icons/standard/note.svg",
  "revision": "e1d18c7c6a5a28d0ea94f8dc94ab8be9"
}, {
  "url": "include/LD/assets/icons/standard/number_input.svg",
  "revision": "4dbbaf4743ee75de6f96474221728610"
}, {
  "url": "include/LD/assets/icons/standard/omni_supervisor.svg",
  "revision": "bb8f4ed031fae3dd10e247a1b45f32bd"
}, {
  "url": "include/LD/assets/icons/standard/operating_hours.svg",
  "revision": "1c08521b2c10c38cd25071e206654090"
}, {
  "url": "include/LD/assets/icons/standard/opportunity_contact_role.svg",
  "revision": "f6275a73e99d5fb33bf71d4ceb6468bb"
}, {
  "url": "include/LD/assets/icons/standard/opportunity_splits.svg",
  "revision": "08bb2fa7ea28e9cb16dc4727b5cb8305"
}, {
  "url": "include/LD/assets/icons/standard/opportunity.svg",
  "revision": "007b200cc4e392e078d353ec8f3d1255"
}, {
  "url": "include/LD/assets/icons/standard/order_item.svg",
  "revision": "98948f7361051c5094c4ad59b743b924"
}, {
  "url": "include/LD/assets/icons/standard/orders.svg",
  "revision": "a0405ccb18e7ddf146c06f6deba46b58"
}, {
  "url": "include/LD/assets/icons/standard/outcome.svg",
  "revision": "d8c5009d454ce74fece6b6ded64c8176"
}, {
  "url": "include/LD/assets/icons/standard/output.svg",
  "revision": "1560c4d125741c9d48031501ab9dd694"
}, {
  "url": "include/LD/assets/icons/standard/partner_fund_allocation.svg",
  "revision": "b6cdb653a420b1e51e9f44a99787bc56"
}, {
  "url": "include/LD/assets/icons/standard/partner_fund_claim.svg",
  "revision": "edaf576bfde73ab9ed0a7356a2fe878e"
}, {
  "url": "include/LD/assets/icons/standard/partner_fund_request.svg",
  "revision": "396b53b578887cf95b00cd4bdb57bd5a"
}, {
  "url": "include/LD/assets/icons/standard/partner_marketing_budget.svg",
  "revision": "887c2563e935fb20e693bc658f4199cf"
}, {
  "url": "include/LD/assets/icons/standard/partners.svg",
  "revision": "4904c502502b6e19bf47896094c6b6c0"
}, {
  "url": "include/LD/assets/icons/standard/password.svg",
  "revision": "9279b3df70bddabb5659b06e1e7acd7e"
}, {
  "url": "include/LD/assets/icons/standard/past_chat.svg",
  "revision": "cf46d415e85f620f234a35f39d6f3aef"
}, {
  "url": "include/LD/assets/icons/standard/people.svg",
  "revision": "55c51356065facdfcda2a6064b08a9cb"
}, {
  "url": "include/LD/assets/icons/standard/performance.svg",
  "revision": "e639587b4603fd84281d9b469aba5422"
}, {
  "url": "include/LD/assets/icons/standard/person_account.svg",
  "revision": "13afbca801422354508f52565cf46464"
}, {
  "url": "include/LD/assets/icons/standard/photo.svg",
  "revision": "8463e24b7821af816268cedc25e7414c"
}, {
  "url": "include/LD/assets/icons/standard/picklist_choice.svg",
  "revision": "39067418d247c96c10a71730aad88e95"
}, {
  "url": "include/LD/assets/icons/standard/picklist_type.svg",
  "revision": "6339c2f8ca1c93f4839838e3a00c60da"
}, {
  "url": "include/LD/assets/icons/standard/planogram.svg",
  "revision": "cf0f1019c3003a648ad4eeb414beff5e"
}, {
  "url": "include/LD/assets/icons/standard/poll.svg",
  "revision": "f4eb52f1a547120f18dbc90bcded78d0"
}, {
  "url": "include/LD/assets/icons/standard/portal_roles_and_subordinates.svg",
  "revision": "9087d7ec0c58bcacb2be00b019399b6e"
}, {
  "url": "include/LD/assets/icons/standard/portal_roles.svg",
  "revision": "6f9ff00beb309f3ccade3e81fb085b56"
}, {
  "url": "include/LD/assets/icons/standard/portal.svg",
  "revision": "58ff8267d2a0bf1a393f68071f2dfdfe"
}, {
  "url": "include/LD/assets/icons/standard/post.svg",
  "revision": "0eecbff8b397edb660eeef48a4f71642"
}, {
  "url": "include/LD/assets/icons/standard/pricebook.svg",
  "revision": "875ab6b8b0a0970d5b52a507a1e325d5"
}, {
  "url": "include/LD/assets/icons/standard/process.svg",
  "revision": "67ad0e120a870a102603dc38cc12dc1b"
}, {
  "url": "include/LD/assets/icons/standard/product_consumed.svg",
  "revision": "9ea8ad7928c39830003555efed625bfa"
}, {
  "url": "include/LD/assets/icons/standard/product_item_transaction.svg",
  "revision": "67250813a655764a5842ab7d73e658ab"
}, {
  "url": "include/LD/assets/icons/standard/product_item.svg",
  "revision": "601088c8f325c3a0b3270b972768502c"
}, {
  "url": "include/LD/assets/icons/standard/product_request_line_item.svg",
  "revision": "53ffa96fc83159868cba45d96003c717"
}, {
  "url": "include/LD/assets/icons/standard/product_request.svg",
  "revision": "20ad116444f898d3ec9401c24aa6149a"
}, {
  "url": "include/LD/assets/icons/standard/product_required.svg",
  "revision": "00f97d14bb79e153d3522c6673a90aac"
}, {
  "url": "include/LD/assets/icons/standard/product_transfer.svg",
  "revision": "28a926a4967964e1d6a23beab5b7c3cb"
}, {
  "url": "include/LD/assets/icons/standard/product.svg",
  "revision": "85e82972f4c66e8bb8d2e241b08d550a"
}, {
  "url": "include/LD/assets/icons/standard/proposition.svg",
  "revision": "37f93da8cc784fccb2e5316a94f8ebd5"
}, {
  "url": "include/LD/assets/icons/standard/question_best.svg",
  "revision": "bd3eca9d0e6c51b33930148bdd6206dc"
}, {
  "url": "include/LD/assets/icons/standard/question_feed.svg",
  "revision": "e8e48462cb888596251c2c923c8ad5d5"
}, {
  "url": "include/LD/assets/icons/standard/queue.svg",
  "revision": "739e19a7769b10b7a906efb3d831db65"
}, {
  "url": "include/LD/assets/icons/standard/quick_text.svg",
  "revision": "e059cf347f1be3f33e1028eea3e4c332"
}, {
  "url": "include/LD/assets/icons/standard/quip_sheet.svg",
  "revision": "b82289b376b08ed1f38eebda5a724ff5"
}, {
  "url": "include/LD/assets/icons/standard/quip.svg",
  "revision": "c57733467ac3ad8d61fa4ced11579abd"
}, {
  "url": "include/LD/assets/icons/standard/quotes.svg",
  "revision": "a80215462a6ef4a45927c5f48d7515d0"
}, {
  "url": "include/LD/assets/icons/standard/radio_button.svg",
  "revision": "aeebfdc41979e3043940478810cff76e"
}, {
  "url": "include/LD/assets/icons/standard/read_receipts.svg",
  "revision": "9b890623be9a2c757a02b6f3e4a444c6"
}, {
  "url": "include/LD/assets/icons/standard/recent.svg",
  "revision": "d773f5099c406eb95befc7b943729e92"
}, {
  "url": "include/LD/assets/icons/standard/record_create.svg",
  "revision": "01653055d1d35ee5905d2f7237272f1e"
}, {
  "url": "include/LD/assets/icons/standard/record_delete.svg",
  "revision": "8aa5e74e49cd3daf7f6f330a6f4e97c4"
}, {
  "url": "include/LD/assets/icons/standard/record_lookup.svg",
  "revision": "ad679969ade7c167a2be289a8a9089c0"
}, {
  "url": "include/LD/assets/icons/standard/record_update.svg",
  "revision": "895de1643a4a49710983d887e91284c1"
}, {
  "url": "include/LD/assets/icons/standard/record.svg",
  "revision": "aa174a30df068300093e61b9bc3125c6"
}, {
  "url": "include/LD/assets/icons/standard/recycle_bin.svg",
  "revision": "fde56d804b01ebfa96812e0ac63db81d"
}, {
  "url": "include/LD/assets/icons/standard/related_list.svg",
  "revision": "f5d2b7bf4f1e46a40991d9c1eb6b4806"
}, {
  "url": "include/LD/assets/icons/standard/relationship.svg",
  "revision": "48fd3a347169f3045a7c73f9dd8c6b5a"
}, {
  "url": "include/LD/assets/icons/standard/report.svg",
  "revision": "0ea8b3f9b58c7238317e438c0dec4006"
}, {
  "url": "include/LD/assets/icons/standard/resource_absence.svg",
  "revision": "9f8fab7fe52a9ff5f0600c1465ac52f6"
}, {
  "url": "include/LD/assets/icons/standard/resource_capacity.svg",
  "revision": "04a7755d89d4e3d79e545fecf201e1f9"
}, {
  "url": "include/LD/assets/icons/standard/resource_preference.svg",
  "revision": "5243dcda316ca41b3d6af87eab234a8c"
}, {
  "url": "include/LD/assets/icons/standard/resource_skill.svg",
  "revision": "a29ab0203aa1511d4cab2e0dcfbd254c"
}, {
  "url": "include/LD/assets/icons/standard/return_order_line_item.svg",
  "revision": "58ac71423c8dfde0159b78df5fa00bf1"
}, {
  "url": "include/LD/assets/icons/standard/return_order.svg",
  "revision": "303cc7d86784631d146b93dae4fc5321"
}, {
  "url": "include/LD/assets/icons/standard/reward.svg",
  "revision": "7be4cbfcf4c77292e2b55cb18579ad3e"
}, {
  "url": "include/LD/assets/icons/standard/rtc_presence.svg",
  "revision": "ff75633d29de74aae2c79e34e5c09f1a"
}, {
  "url": "include/LD/assets/icons/standard/sales_cadence_target.svg",
  "revision": "fd2920051397fc650d0516742d0fc5ce"
}, {
  "url": "include/LD/assets/icons/standard/sales_cadence.svg",
  "revision": "5500f6d3298f064c95da8fae7d5f8a14"
}, {
  "url": "include/LD/assets/icons/standard/sales_channel.svg",
  "revision": "989f0493aa2663dfc589fbc78cd0dff5"
}, {
  "url": "include/LD/assets/icons/standard/sales_path.svg",
  "revision": "dac9b6fcd28dd55a508bf087b2794336"
}, {
  "url": "include/LD/assets/icons/standard/sales_value.svg",
  "revision": "83cb4c640873df996ffd4a0c18fba0fb"
}, {
  "url": "include/LD/assets/icons/standard/salesforce_cms.svg",
  "revision": "62080320fedbf0e1b33b759fab38d926"
}, {
  "url": "include/LD/assets/icons/standard/scan_card.svg",
  "revision": "0cb7d39eca496d0eb4149bbc2c728ddd"
}, {
  "url": "include/LD/assets/icons/standard/screen.svg",
  "revision": "dbbb1b1ca843bbcf716a6fc60fe42f6d"
}, {
  "url": "include/LD/assets/icons/standard/search.svg",
  "revision": "81f931012789333721efd119ba2d1530"
}, {
  "url": "include/LD/assets/icons/standard/service_appointment_capacity_usage.svg",
  "revision": "c1511d9d293653427e278c4072b0ec61"
}, {
  "url": "include/LD/assets/icons/standard/service_appointment.svg",
  "revision": "c4e9a09c24415a10dd8449efe1617076"
}, {
  "url": "include/LD/assets/icons/standard/service_contract.svg",
  "revision": "e26c6e888a04e211b2514bb754a8d5c7"
}, {
  "url": "include/LD/assets/icons/standard/service_crew_member.svg",
  "revision": "5b1ace05c72136c82629ecb41889d216"
}, {
  "url": "include/LD/assets/icons/standard/service_crew.svg",
  "revision": "5505947f54ad8d282e5d6b6dd961d646"
}, {
  "url": "include/LD/assets/icons/standard/service_report.svg",
  "revision": "f4580ee1ecb091066297926db9ac6941"
}, {
  "url": "include/LD/assets/icons/standard/service_resource.svg",
  "revision": "a6a0a947b1dd60078270ab78a879a30b"
}, {
  "url": "include/LD/assets/icons/standard/service_territory_location.svg",
  "revision": "5d48a663182d6a8c87ea9e56d4ea704e"
}, {
  "url": "include/LD/assets/icons/standard/service_territory_member.svg",
  "revision": "8459f709d0996c87f5b190deb4f76192"
}, {
  "url": "include/LD/assets/icons/standard/service_territory.svg",
  "revision": "cd22366c913a7295e151740a78f1cbd3"
}, {
  "url": "include/LD/assets/icons/standard/settings.svg",
  "revision": "662c9c11c39f067b358bced53065b33b"
}, {
  "url": "include/LD/assets/icons/standard/shift_type.svg",
  "revision": "b0ece207eceefdacb8b5ea2eb6b7e679"
}, {
  "url": "include/LD/assets/icons/standard/shift.svg",
  "revision": "581f19d67cba4e5fc07530c5b9440b42"
}, {
  "url": "include/LD/assets/icons/standard/shipment.svg",
  "revision": "6cb66698ac9761fac1365723006f5009"
}, {
  "url": "include/LD/assets/icons/standard/skill_entity.svg",
  "revision": "b9f728a81c533423218641435bd6d306"
}, {
  "url": "include/LD/assets/icons/standard/skill_requirement.svg",
  "revision": "baf735c5b7f6e887acc69ee09990dfb1"
}, {
  "url": "include/LD/assets/icons/standard/skill.svg",
  "revision": "8b0e0813cd3d928f0b9bbd3e6da26040"
}, {
  "url": "include/LD/assets/icons/standard/sms.svg",
  "revision": "a27860fc3ea8eff2be2bec55e8873976"
}, {
  "url": "include/LD/assets/icons/standard/snippet.svg",
  "revision": "90183242c0a4ae0ad8654ef7e2601ad0"
}, {
  "url": "include/LD/assets/icons/standard/snippets.svg",
  "revision": "44170378770406c6986694a423335251"
}, {
  "url": "include/LD/assets/icons/standard/sobject_collection.svg",
  "revision": "50e49284bb0c7cc821c3727dbb19b840"
}, {
  "url": "include/LD/assets/icons/standard/sobject.svg",
  "revision": "4b60d2415469d01121ae4503e1ca391a"
}, {
  "url": "include/LD/assets/icons/standard/social.svg",
  "revision": "9aabfd3a3987ede4b416ab1f8ad4660c"
}, {
  "url": "include/LD/assets/icons/standard/solution.svg",
  "revision": "a9c880bd73d4942413a3ab530c08a45f"
}, {
  "url": "include/LD/assets/icons/standard/sort.svg",
  "revision": "288e48f3824d2da5dec353341d780501"
}, {
  "url": "include/LD/assets/icons/standard/sossession.svg",
  "revision": "3298790e7df67dfba9cd3ba4069df077"
}, {
  "url": "include/LD/assets/icons/standard/stage_collection.svg",
  "revision": "fef2309ed75b8ef38563dfdc030a29da"
}, {
  "url": "include/LD/assets/icons/standard/stage.svg",
  "revision": "0313526316d12dd8a72b1e7ff9ede826"
}, {
  "url": "include/LD/assets/icons/standard/steps.svg",
  "revision": "e73edf063d74d105c46fa9b9dc3383a1"
}, {
  "url": "include/LD/assets/icons/standard/store_group.svg",
  "revision": "78f9f31c567310547899b9155d4c37b1"
}, {
  "url": "include/LD/assets/icons/standard/store.svg",
  "revision": "b063c9f449b43e9020ad73905a03062b"
}, {
  "url": "include/LD/assets/icons/standard/story.svg",
  "revision": "89dd8bff659106183263323941f0a710"
}, {
  "url": "include/LD/assets/icons/standard/strategy.svg",
  "revision": "7663b2ac537131297ab94e9cc16bab4a"
}, {
  "url": "include/LD/assets/icons/standard/survey.svg",
  "revision": "a8989df57e8f755b43d7f829df9cef8a"
}, {
  "url": "include/LD/assets/icons/standard/system_and_global_variable.svg",
  "revision": "36274f668e63af2fb6e75d091de2b6c2"
}, {
  "url": "include/LD/assets/icons/standard/task.svg",
  "revision": "b4b875dd310f0eb1216e23d7c8ceadc4"
}, {
  "url": "include/LD/assets/icons/standard/task2.svg",
  "revision": "d329739d9d5fb14d76b0e5f8abe3ddbc"
}, {
  "url": "include/LD/assets/icons/standard/team_member.svg",
  "revision": "e9fa0cb17aba1bede23e682c5ff2534b"
}, {
  "url": "include/LD/assets/icons/standard/template.svg",
  "revision": "509e92374637532306ecef36753c71dc"
}, {
  "url": "include/LD/assets/icons/standard/text_template.svg",
  "revision": "857ed4ed01e27a4945e63b19f1ee5e28"
}, {
  "url": "include/LD/assets/icons/standard/text.svg",
  "revision": "cf31675be0cf7e8839ec9e6c198fcc11"
}, {
  "url": "include/LD/assets/icons/standard/textarea.svg",
  "revision": "c0787ab448aa3276e828128b6a60e242"
}, {
  "url": "include/LD/assets/icons/standard/textbox.svg",
  "revision": "69bbcddb1c5904102bb9f14d6c8ef084"
}, {
  "url": "include/LD/assets/icons/standard/thanks_loading.svg",
  "revision": "ccdd121f49564ae1c03d6eaf8a84d73f"
}, {
  "url": "include/LD/assets/icons/standard/thanks.svg",
  "revision": "86ab26cc07bffa6b021bf79343da2768"
}, {
  "url": "include/LD/assets/icons/standard/timesheet_entry.svg",
  "revision": "2606f6efce2c0d5ddebfa05a795cb0d3"
}, {
  "url": "include/LD/assets/icons/standard/timesheet.svg",
  "revision": "72011cfdaa47be5e3402c4ee9fd65574"
}, {
  "url": "include/LD/assets/icons/standard/timeslot.svg",
  "revision": "20998c4e0b35b0bffd6a51af960ea4fa"
}, {
  "url": "include/LD/assets/icons/standard/today.svg",
  "revision": "fbb1486a046387586e45cccda54c321d"
}, {
  "url": "include/LD/assets/icons/standard/topic.svg",
  "revision": "9cef056f68f43f3a28e48395b8246e7a"
}, {
  "url": "include/LD/assets/icons/standard/topic2.svg",
  "revision": "090ec35abdadc24b61a4d5585fea0f95"
}, {
  "url": "include/LD/assets/icons/standard/trailhead.svg",
  "revision": "0102d5a4d8783a2dffe1e2ddd9bb75ca"
}, {
  "url": "include/LD/assets/icons/standard/unmatched.svg",
  "revision": "f79cfe0f35c444b79f75eea399c4afbb"
}, {
  "url": "include/LD/assets/icons/standard/user_role.svg",
  "revision": "cf9c5fc5049033e013b9424efe6f8cb9"
}, {
  "url": "include/LD/assets/icons/standard/user.svg",
  "revision": "55c51356065facdfcda2a6064b08a9cb"
}, {
  "url": "include/LD/assets/icons/standard/variable.svg",
  "revision": "8290953a64f7908202b2e066865fc220"
}, {
  "url": "include/LD/assets/icons/standard/visit_templates.svg",
  "revision": "168db847a186c252e1f2c22627988c4e"
}, {
  "url": "include/LD/assets/icons/standard/visits.svg",
  "revision": "b66e49339779adc5c3b3372d1cbc04a6"
}, {
  "url": "include/LD/assets/icons/standard/visualforce_page.svg",
  "revision": "09da74f9ea2fa57278824abee613ace3"
}, {
  "url": "include/LD/assets/icons/standard/voice_call.svg",
  "revision": "80cbae2b313517d2955bd8a0cee82b64"
}, {
  "url": "include/LD/assets/icons/standard/waits.svg",
  "revision": "17004885025b4b2135543f2a2890438b"
}, {
  "url": "include/LD/assets/icons/standard/work_capacity_limit.svg",
  "revision": "a54a4d66394f2db9a5f16506e6d7a609"
}, {
  "url": "include/LD/assets/icons/standard/work_capacity_usage.svg",
  "revision": "70d46b8cc4945e4218948a256b53157a"
}, {
  "url": "include/LD/assets/icons/standard/work_order_item.svg",
  "revision": "9e2b7016ca0addb3ae600a31eea1915d"
}, {
  "url": "include/LD/assets/icons/standard/work_order.svg",
  "revision": "29e7e541f5ddd34815b9deb4d133ce51"
}, {
  "url": "include/LD/assets/icons/standard/work_queue.svg",
  "revision": "f3197591bff30c12fd53a3834cb83155"
}, {
  "url": "include/LD/assets/icons/standard/work_type_group.svg",
  "revision": "433667b735b90d011f9a669e803bf4eb"
}, {
  "url": "include/LD/assets/icons/standard/work_type.svg",
  "revision": "ad2bb8b463a2fa19eb0e594d1534b8b7"
}, {
  "url": "include/LD/assets/icons/utility-sprite/svg/symbols-rtl.svg",
  "revision": "97313b9fea49bb32e865e2e62322c05f"
}, {
  "url": "include/LD/assets/icons/utility-sprite/svg/symbols.svg",
  "revision": "091d54ce6a4db1d0c0c53341b3d95352"
}, {
  "url": "include/LD/assets/icons/utility/activity.svg",
  "revision": "01fd42ad941ba0f0908b7238a051de1e"
}, {
  "url": "include/LD/assets/icons/utility/ad_set.svg",
  "revision": "ec676228d19edf214c331bbe09ba2801"
}, {
  "url": "include/LD/assets/icons/utility/add.svg",
  "revision": "1867ec504cd6bcdf955b7fffa8d51b4a"
}, {
  "url": "include/LD/assets/icons/utility/adduser.svg",
  "revision": "15b6019b83206d007de92b5ad748f008"
}, {
  "url": "include/LD/assets/icons/utility/advanced_function.svg",
  "revision": "6ea969ec64d859f3186753e8196a4156"
}, {
  "url": "include/LD/assets/icons/utility/agent_session.svg",
  "revision": "41a014f3cf8619618d6bdf6671f2133d"
}, {
  "url": "include/LD/assets/icons/utility/alert.svg",
  "revision": "975f5ab5abbbf7e9937fb4328616fe40"
}, {
  "url": "include/LD/assets/icons/utility/all.svg",
  "revision": "cd1e7540a6a5c8676923b3da42d13df1"
}, {
  "url": "include/LD/assets/icons/utility/anchor.svg",
  "revision": "9a77fcacbb7cfb912ed986c6322f8efd"
}, {
  "url": "include/LD/assets/icons/utility/animal_and_nature.svg",
  "revision": "f0846c52de67bc241d9e3250e83524ce"
}, {
  "url": "include/LD/assets/icons/utility/announcement.svg",
  "revision": "3363818115295fbc44b75ac74bc87c04"
}, {
  "url": "include/LD/assets/icons/utility/answer.svg",
  "revision": "2018b72f67117a71cd7fb16444a6c410"
}, {
  "url": "include/LD/assets/icons/utility/answered_twice.svg",
  "revision": "2c73f543b1e566f713dee50db9de8110"
}, {
  "url": "include/LD/assets/icons/utility/apex_plugin.svg",
  "revision": "21317ebfbe3448d00a54f8f059a71056"
}, {
  "url": "include/LD/assets/icons/utility/apex.svg",
  "revision": "db7e7484a38f69afdef41247a763a896"
}, {
  "url": "include/LD/assets/icons/utility/approval.svg",
  "revision": "973ada77057d4ea4346ce34d0b53340e"
}, {
  "url": "include/LD/assets/icons/utility/apps.svg",
  "revision": "511641fba7b29dbcf70c8d9f9d138567"
}, {
  "url": "include/LD/assets/icons/utility/archive.svg",
  "revision": "3e5e8e544637be0f0603de207a45cc69"
}, {
  "url": "include/LD/assets/icons/utility/arrowdown.svg",
  "revision": "31157bf2414f0786efce72788193a523"
}, {
  "url": "include/LD/assets/icons/utility/arrowup.svg",
  "revision": "ee4dd4640c06ecfa863edd12b48be8ad"
}, {
  "url": "include/LD/assets/icons/utility/assignment.svg",
  "revision": "ff4ab5e6ccd3e3877e8265c19d0940d5"
}, {
  "url": "include/LD/assets/icons/utility/attach.svg",
  "revision": "9f4804cec1e296e8de3388bcf965202d"
}, {
  "url": "include/LD/assets/icons/utility/automate.svg",
  "revision": "923428033a97d962ed717608097c1321"
}, {
  "url": "include/LD/assets/icons/utility/away.svg",
  "revision": "75971b4521bbba088be99d67238663a8"
}, {
  "url": "include/LD/assets/icons/utility/back.svg",
  "revision": "7780a19449fba300b218bbbbbbf06dc7"
}, {
  "url": "include/LD/assets/icons/utility/ban.svg",
  "revision": "522a3a7630f017f493b73ebb3cd7c20a"
}, {
  "url": "include/LD/assets/icons/utility/block_visitor.svg",
  "revision": "42da3bef4cf58f53a357219dc1a24360"
}, {
  "url": "include/LD/assets/icons/utility/bold.svg",
  "revision": "2e2b1ba7aee097af9d6a213c7da62d0b"
}, {
  "url": "include/LD/assets/icons/utility/bookmark.svg",
  "revision": "463122d81c1c1d35ea17d54aea9505bc"
}, {
  "url": "include/LD/assets/icons/utility/breadcrumbs.svg",
  "revision": "6c899fb4b06911ca641dcebdcd3e73d7"
}, {
  "url": "include/LD/assets/icons/utility/broadcast.svg",
  "revision": "af2ad4e31d9d4a55a221becd2d80f768"
}, {
  "url": "include/LD/assets/icons/utility/brush.svg",
  "revision": "09c83b732d734b70049fa3abf9e243b2"
}, {
  "url": "include/LD/assets/icons/utility/bucket.svg",
  "revision": "1cb97c956f5acd50d5730e817f29c0c0"
}, {
  "url": "include/LD/assets/icons/utility/builder.svg",
  "revision": "dfbdeec7e11b5ee0eaf1943b431ad9f7"
}, {
  "url": "include/LD/assets/icons/utility/call.svg",
  "revision": "ef45b16896ae6a28d9c0af6204d81d53"
}, {
  "url": "include/LD/assets/icons/utility/campaign.svg",
  "revision": "e68f8a25069dcb0610ca2a590bcf0487"
}, {
  "url": "include/LD/assets/icons/utility/cancel_file_request.svg",
  "revision": "c318cc1a6618e2152c7c421d708a815a"
}, {
  "url": "include/LD/assets/icons/utility/cancel_transfer.svg",
  "revision": "228bc8ddaed9b36998458bc311fd096c"
}, {
  "url": "include/LD/assets/icons/utility/capslock.svg",
  "revision": "06b6755a787678c587c54de50174cd0f"
}, {
  "url": "include/LD/assets/icons/utility/cart.svg",
  "revision": "f3a172b5ae1451c626f30ddb3886cf78"
}, {
  "url": "include/LD/assets/icons/utility/case.svg",
  "revision": "76de36157caa27b477d49970af773bdf"
}, {
  "url": "include/LD/assets/icons/utility/cases.svg",
  "revision": "0db3c4a2a9fb4461e1b18fe08ad58480"
}, {
  "url": "include/LD/assets/icons/utility/center_align_text.svg",
  "revision": "24d68f4410d78196dfa18488a1182e1a"
}, {
  "url": "include/LD/assets/icons/utility/change_owner.svg",
  "revision": "92d871f9cf8e07a4923874bb3c8f63f2"
}, {
  "url": "include/LD/assets/icons/utility/change_record_type.svg",
  "revision": "6bb2a79175bba89d782d70de18b112c2"
}, {
  "url": "include/LD/assets/icons/utility/chart.svg",
  "revision": "4c95a45cbccbd63834b3d924e6bd6fd0"
}, {
  "url": "include/LD/assets/icons/utility/chat.svg",
  "revision": "76d09b1642cdb1f3c011ee452f4dcf38"
}, {
  "url": "include/LD/assets/icons/utility/check.svg",
  "revision": "a7fb6b1d178bab3a40731120babe0dd5"
}, {
  "url": "include/LD/assets/icons/utility/checkin.svg",
  "revision": "7da6cb33a87e2935457f327d62fbf630"
}, {
  "url": "include/LD/assets/icons/utility/chevrondown.svg",
  "revision": "c9fc7a9b850fce2485ba39d3b3952389"
}, {
  "url": "include/LD/assets/icons/utility/chevronleft.svg",
  "revision": "57eaecac1f61eb67761cdf4d52356d62"
}, {
  "url": "include/LD/assets/icons/utility/chevronright.svg",
  "revision": "ebd70232aa52ec181d7e490d0bb71751"
}, {
  "url": "include/LD/assets/icons/utility/chevronup.svg",
  "revision": "8e80a34e72409de1f7936d1b1373b5b0"
}, {
  "url": "include/LD/assets/icons/utility/choice.svg",
  "revision": "86faf6f151e9e0b3a4f792d2c248d7cb"
}, {
  "url": "include/LD/assets/icons/utility/classic_interface.svg",
  "revision": "b21dc5e164cae971f53f76c402195135"
}, {
  "url": "include/LD/assets/icons/utility/clear.svg",
  "revision": "1b14982ce83bcec67fd68e62951818cc"
}, {
  "url": "include/LD/assets/icons/utility/clock.svg",
  "revision": "f990fd06d32b73e8f0a3a3e62e5599d6"
}, {
  "url": "include/LD/assets/icons/utility/close.svg",
  "revision": "f8e43528a0b5031f7d87d4de0432e0c3"
}, {
  "url": "include/LD/assets/icons/utility/collapse_all.svg",
  "revision": "0a8314470c968fbb2983c8c34115e9f2"
}, {
  "url": "include/LD/assets/icons/utility/collection_variable.svg",
  "revision": "4d21664ef4c46a530f4b91c37026cead"
}, {
  "url": "include/LD/assets/icons/utility/color_swatch.svg",
  "revision": "1650f1b10d82da1b9819d12829178f5a"
}, {
  "url": "include/LD/assets/icons/utility/comments.svg",
  "revision": "04539a8c4fd8ac980d4cb6523249e188"
}, {
  "url": "include/LD/assets/icons/utility/company.svg",
  "revision": "d868a230712970b06b1335e88997ad41"
}, {
  "url": "include/LD/assets/icons/utility/component_customization.svg",
  "revision": "ce9cf626bac81dcd2afd6ed2b6eb4a5a"
}, {
  "url": "include/LD/assets/icons/utility/connected_apps.svg",
  "revision": "e7412492cf6f80b951c30696cd31e80b"
}, {
  "url": "include/LD/assets/icons/utility/constant.svg",
  "revision": "e77be8d987f8e8806550f380ec0a8e7f"
}, {
  "url": "include/LD/assets/icons/utility/contact_request.svg",
  "revision": "e110f714f0da07fce11a9b860bab8984"
}, {
  "url": "include/LD/assets/icons/utility/contract_alt.svg",
  "revision": "284693746f6617447fef1aae2eadb8b1"
}, {
  "url": "include/LD/assets/icons/utility/contract.svg",
  "revision": "975c92ff2dc2121dc154910bd0a43808"
}, {
  "url": "include/LD/assets/icons/utility/copy_to_clipboard.svg",
  "revision": "d6555c7b01d78ac8084f744e008e1b73"
}, {
  "url": "include/LD/assets/icons/utility/copy.svg",
  "revision": "ea35d6550044c39be121976f97ebaa68"
}, {
  "url": "include/LD/assets/icons/utility/crossfilter.svg",
  "revision": "732c0988815923d3b82e3bf66899749d"
}, {
  "url": "include/LD/assets/icons/utility/currency_input.svg",
  "revision": "ee9696138b1868d51365ecb3710100cc"
}, {
  "url": "include/LD/assets/icons/utility/currency.svg",
  "revision": "d4721d96555f899d59483dbb73125e44"
}, {
  "url": "include/LD/assets/icons/utility/custom_apps.svg",
  "revision": "4c28d8c88c0e1dbb597c03d227a6de16"
}, {
  "url": "include/LD/assets/icons/utility/cut.svg",
  "revision": "ede706ffe10adb86a0e0390a86994557"
}, {
  "url": "include/LD/assets/icons/utility/dash.svg",
  "revision": "dd5915a60ab7354fa2e71e49d39b5309"
}, {
  "url": "include/LD/assets/icons/utility/database.svg",
  "revision": "b71bfabeaddfd3f4ef87d43b4a89e92b"
}, {
  "url": "include/LD/assets/icons/utility/datadotcom.svg",
  "revision": "828eac6646bf3ec2ac10517a1718042d"
}, {
  "url": "include/LD/assets/icons/utility/date_input.svg",
  "revision": "157c870874412cb5ee8438d18c336888"
}, {
  "url": "include/LD/assets/icons/utility/date_time.svg",
  "revision": "57cace2f09334e29594ca6dc16345afd"
}, {
  "url": "include/LD/assets/icons/utility/dayview.svg",
  "revision": "1881e18fb5e31ef0afb947575df8a3f2"
}, {
  "url": "include/LD/assets/icons/utility/delete.svg",
  "revision": "5a727b715b796a063ea365f41a060666"
}, {
  "url": "include/LD/assets/icons/utility/deprecate.svg",
  "revision": "641fd73cada62946325efadc10427610"
}, {
  "url": "include/LD/assets/icons/utility/description.svg",
  "revision": "1784efb133e92dcf6dd4d4f3ff0dbad8"
}, {
  "url": "include/LD/assets/icons/utility/desktop_and_phone.svg",
  "revision": "f02b4089a9285bd615e2483dfe9cd008"
}, {
  "url": "include/LD/assets/icons/utility/desktop_console.svg",
  "revision": "7d68afed369dcfd326d6e0ed1d5f8d53"
}, {
  "url": "include/LD/assets/icons/utility/desktop.svg",
  "revision": "125ec92f588a9e30ddea24750163d35d"
}, {
  "url": "include/LD/assets/icons/utility/dialing.svg",
  "revision": "6b4f74f2baab5c62cb9593a39360edd3"
}, {
  "url": "include/LD/assets/icons/utility/diamond.svg",
  "revision": "8d65c736fa08bec2cd044784832736a9"
}, {
  "url": "include/LD/assets/icons/utility/dislike.svg",
  "revision": "28917b33b0cb403ae048ac554bb0e8da"
}, {
  "url": "include/LD/assets/icons/utility/display_rich_text.svg",
  "revision": "fddd45f017e501bce7e55fc41a6517dc"
}, {
  "url": "include/LD/assets/icons/utility/display_text.svg",
  "revision": "e8d8b9afb86bfcf693f8bfc4eefe0702"
}, {
  "url": "include/LD/assets/icons/utility/dock_panel.svg",
  "revision": "b0907f6edd9b53fdab7fdad80e49c401"
}, {
  "url": "include/LD/assets/icons/utility/down.svg",
  "revision": "2cfacf405a91ef0d08316476d2e52d4b"
}, {
  "url": "include/LD/assets/icons/utility/download.svg",
  "revision": "bebdb605afbb5eceb453afd7126bac69"
}, {
  "url": "include/LD/assets/icons/utility/drag_and_drop.svg",
  "revision": "b1e8dbfb06747e4761224d7ad07730b3"
}, {
  "url": "include/LD/assets/icons/utility/drag.svg",
  "revision": "2d1f30edeee2c8ea06b275f95bec0ccb"
}, {
  "url": "include/LD/assets/icons/utility/dynamic_record_choice.svg",
  "revision": "893af8ad81aa78b1b6ab3c3d17bc710b"
}, {
  "url": "include/LD/assets/icons/utility/edit_form.svg",
  "revision": "6ecc943f3260d31f99156e5a688acba8"
}, {
  "url": "include/LD/assets/icons/utility/edit.svg",
  "revision": "faca53a2140b7efff05a3ff3df2b34b3"
}, {
  "url": "include/LD/assets/icons/utility/education.svg",
  "revision": "0fba13de9767caecc732fdd0757e6c94"
}, {
  "url": "include/LD/assets/icons/utility/einstein.svg",
  "revision": "815883ea6bfe11f48662022b7ac6725d"
}, {
  "url": "include/LD/assets/icons/utility/email_open.svg",
  "revision": "e4e7b03f54ebc08c9815334df144efa8"
}, {
  "url": "include/LD/assets/icons/utility/email.svg",
  "revision": "7f3c6df6cdea1d957e4f9be9dac240b4"
}, {
  "url": "include/LD/assets/icons/utility/emoji.svg",
  "revision": "59104c8954316001e2fca3a0b7d9db65"
}, {
  "url": "include/LD/assets/icons/utility/end_call.svg",
  "revision": "fd54df9cc8f45040b2fe5dee9d9b571e"
}, {
  "url": "include/LD/assets/icons/utility/end_chat.svg",
  "revision": "0e25c7c1ec78ca2840a01e856338503a"
}, {
  "url": "include/LD/assets/icons/utility/end_messaging_session.svg",
  "revision": "a2b60d62bff4f193ab50f76a20752ed4"
}, {
  "url": "include/LD/assets/icons/utility/engage.svg",
  "revision": "5b40ff19b917e6c8bc7597bf4f462462"
}, {
  "url": "include/LD/assets/icons/utility/enter.svg",
  "revision": "ad03888652862d60b1b7741298678e96"
}, {
  "url": "include/LD/assets/icons/utility/erect_window.svg",
  "revision": "9ea3d5cbe59219bbdcf5e316dadd5c92"
}, {
  "url": "include/LD/assets/icons/utility/error.svg",
  "revision": "12888620ebe85f153cd223db0e28301a"
}, {
  "url": "include/LD/assets/icons/utility/event.svg",
  "revision": "feeedb395459c47a6f4fd6b51bc0691b"
}, {
  "url": "include/LD/assets/icons/utility/events.svg",
  "revision": "7ac1593b3cd30845cce82c7a5ac1ed7b"
}, {
  "url": "include/LD/assets/icons/utility/expand_all.svg",
  "revision": "02423b010dcc6862835d783635336aa7"
}, {
  "url": "include/LD/assets/icons/utility/expand_alt.svg",
  "revision": "7a09a8d3bd2489d82f249612340fc885"
}, {
  "url": "include/LD/assets/icons/utility/expand.svg",
  "revision": "a9d389f9921d7a60b73f948863b4af90"
}, {
  "url": "include/LD/assets/icons/utility/fallback.svg",
  "revision": "b746999da8e9f9f77ee4936760673edf"
}, {
  "url": "include/LD/assets/icons/utility/favorite.svg",
  "revision": "dd52aafae68298b7a07261cacef18ce8"
}, {
  "url": "include/LD/assets/icons/utility/feed.svg",
  "revision": "ccb177ef2674bdd7b619a3021a645cbb"
}, {
  "url": "include/LD/assets/icons/utility/file.svg",
  "revision": "cbcc4e5bdc02346aa7216d76f773539f"
}, {
  "url": "include/LD/assets/icons/utility/filter.svg",
  "revision": "9592b412549b77619e371464909d02a7"
}, {
  "url": "include/LD/assets/icons/utility/filterList.svg",
  "revision": "2a2ec090edffb4ffd165f95531a96859"
}, {
  "url": "include/LD/assets/icons/utility/flow.svg",
  "revision": "2d8a199b6221328f148488137904d77b"
}, {
  "url": "include/LD/assets/icons/utility/food_and_drink.svg",
  "revision": "1d3bfa0323ffcf7b1482b8b087ba041d"
}, {
  "url": "include/LD/assets/icons/utility/formula.svg",
  "revision": "d9bceb0aa6fa69354d7a75752a504b3a"
}, {
  "url": "include/LD/assets/icons/utility/forward_up.svg",
  "revision": "25b0998252d22f56d98657ea7ceada3b"
}, {
  "url": "include/LD/assets/icons/utility/forward.svg",
  "revision": "c3eae6cf81759c1565ba37323fede4dd"
}, {
  "url": "include/LD/assets/icons/utility/frozen.svg",
  "revision": "af1167e1ee824a67baac4ce633e64f5b"
}, {
  "url": "include/LD/assets/icons/utility/fulfillment_order.svg",
  "revision": "7e5dc8c126f7b63f7f059acca63a5259"
}, {
  "url": "include/LD/assets/icons/utility/full_width_view.svg",
  "revision": "04374d2c5231703a6eb6e8ede4b27319"
}, {
  "url": "include/LD/assets/icons/utility/global_constant.svg",
  "revision": "69780e15342f0617733a99a7280ec327"
}, {
  "url": "include/LD/assets/icons/utility/graph.svg",
  "revision": "573b093c8dce6d321579e888e13aa9f0"
}, {
  "url": "include/LD/assets/icons/utility/groups.svg",
  "revision": "9a4c9c7da0b015cf840c46caeba542d8"
}, {
  "url": "include/LD/assets/icons/utility/help_center.svg",
  "revision": "9235302189ad13e68b29b7f17c1c4609"
}, {
  "url": "include/LD/assets/icons/utility/help.svg",
  "revision": "0f762422b4e72ffb4e5c236df19d36e1"
}, {
  "url": "include/LD/assets/icons/utility/hide_mobile.svg",
  "revision": "6fdda512e5b6be16a3d78786018f7dbc"
}, {
  "url": "include/LD/assets/icons/utility/hide.svg",
  "revision": "f0aa93f60c3f0584b810d8652ad7e474"
}, {
  "url": "include/LD/assets/icons/utility/hierarchy.svg",
  "revision": "ee646a613606263120aa47d91a96332e"
}, {
  "url": "include/LD/assets/icons/utility/high_velocity_sales.svg",
  "revision": "d86a181c34bbeb2b3f03a7b20b3b1dd4"
}, {
  "url": "include/LD/assets/icons/utility/home.svg",
  "revision": "6c0e38efba2cae90d2aad50529d8cb9f"
}, {
  "url": "include/LD/assets/icons/utility/identity.svg",
  "revision": "4392af61ae4f97c72cd32726a26d1941"
}, {
  "url": "include/LD/assets/icons/utility/image.svg",
  "revision": "4f355209b7f1a2eb62eb2d869d4c90e4"
}, {
  "url": "include/LD/assets/icons/utility/in_app_assistant.svg",
  "revision": "916524cb1473b366165d7a083495cf17"
}, {
  "url": "include/LD/assets/icons/utility/inbox.svg",
  "revision": "8fbcb2b2bbc1a4468751aba7729cc39b"
}, {
  "url": "include/LD/assets/icons/utility/incoming_call.svg",
  "revision": "3dae067fabc1c91615d472c562bf82aa"
}, {
  "url": "include/LD/assets/icons/utility/info_alt.svg",
  "revision": "b33d9a6b3455e2adf66887dbe671b970"
}, {
  "url": "include/LD/assets/icons/utility/info.svg",
  "revision": "eba3730c5c02615a98f8a5935d7172df"
}, {
  "url": "include/LD/assets/icons/utility/insert_tag_field.svg",
  "revision": "a656627d6cf81a436d3c2c3329a5a337"
}, {
  "url": "include/LD/assets/icons/utility/insert_template.svg",
  "revision": "362cf14ac17a8a995cbc5a0d59ac873e"
}, {
  "url": "include/LD/assets/icons/utility/inspector_panel.svg",
  "revision": "907b370a22accd8e1ffab7eac7a12492"
}, {
  "url": "include/LD/assets/icons/utility/internal_share.svg",
  "revision": "95dacb40b892185c5229932ae78f09f3"
}, {
  "url": "include/LD/assets/icons/utility/italic.svg",
  "revision": "409d07b4c57dd6d65542cc008ae3b818"
}, {
  "url": "include/LD/assets/icons/utility/jump_to_bottom.svg",
  "revision": "96684eaa68e0e3109e75b1f1c36f7139"
}, {
  "url": "include/LD/assets/icons/utility/jump_to_top.svg",
  "revision": "8b9f251a1abfa93324a4fca5f4fcb3d1"
}, {
  "url": "include/LD/assets/icons/utility/justify_text.svg",
  "revision": "3c4bc79bba3109659d1cad505bb0d039"
}, {
  "url": "include/LD/assets/icons/utility/kanban.svg",
  "revision": "81ddfe5a5ec5683ed4fe119c27cf06e1"
}, {
  "url": "include/LD/assets/icons/utility/keyboard_dismiss.svg",
  "revision": "936c07fc4ca6130a8f68539005b73dcc"
}, {
  "url": "include/LD/assets/icons/utility/knowledge_base.svg",
  "revision": "776457d52341d7ba12e71ed106bd1f90"
}, {
  "url": "include/LD/assets/icons/utility/layers.svg",
  "revision": "a70f16e2e49d75e82a639fe6a4c28f1e"
}, {
  "url": "include/LD/assets/icons/utility/layout.svg",
  "revision": "9eec2b5c99f9aaa9da3dccaebd21305e"
}, {
  "url": "include/LD/assets/icons/utility/leave_conference.svg",
  "revision": "3bd595d0e2e0f4545af97eb65bfe2ab6"
}, {
  "url": "include/LD/assets/icons/utility/left_align_text.svg",
  "revision": "539077e1b3db194ee3e267c0c255ae06"
}, {
  "url": "include/LD/assets/icons/utility/left.svg",
  "revision": "be1ce07dc156a826bc535b6fae55f39f"
}, {
  "url": "include/LD/assets/icons/utility/level_down.svg",
  "revision": "161c4137c05200c31c70c2dfe6d8950d"
}, {
  "url": "include/LD/assets/icons/utility/level_up.svg",
  "revision": "ce0e3d562966903d107e1396f3d71854"
}, {
  "url": "include/LD/assets/icons/utility/light_bulb.svg",
  "revision": "607f3c00bdd09e4035c31dcab8432697"
}, {
  "url": "include/LD/assets/icons/utility/lightning_extension.svg",
  "revision": "b154202556ff96ef8415fb24bd17343a"
}, {
  "url": "include/LD/assets/icons/utility/lightning_inspector.svg",
  "revision": "e1e6d4fbbf787e805545fe5a966473b4"
}, {
  "url": "include/LD/assets/icons/utility/like.svg",
  "revision": "365b1a3d05d3eeffec68363b82c72081"
}, {
  "url": "include/LD/assets/icons/utility/link.svg",
  "revision": "d876ec47e93af49da332d469408be53f"
}, {
  "url": "include/LD/assets/icons/utility/linked.svg",
  "revision": "baee8ee8011531f35a8373c2217729ba"
}, {
  "url": "include/LD/assets/icons/utility/list.svg",
  "revision": "ce9301e6b3cbc2b641cc7e23c99f38d2"
}, {
  "url": "include/LD/assets/icons/utility/listen.svg",
  "revision": "689b33d4ece04e437afcbe3fa28b4e40"
}, {
  "url": "include/LD/assets/icons/utility/live_message.svg",
  "revision": "4c00486cfd940c0889c87e7e0618cf71"
}, {
  "url": "include/LD/assets/icons/utility/location.svg",
  "revision": "321dce93fa2e442bb440f4de9cbe6355"
}, {
  "url": "include/LD/assets/icons/utility/lock.svg",
  "revision": "06bface8b83b5d3bca8e02ad5a03b10c"
}, {
  "url": "include/LD/assets/icons/utility/locker_service_api_viewer.svg",
  "revision": "cde150397bce4aff03ee668e97da5375"
}, {
  "url": "include/LD/assets/icons/utility/locker_service_console.svg",
  "revision": "89882b967f6da34e4483ede750be6e5c"
}, {
  "url": "include/LD/assets/icons/utility/log_a_call.svg",
  "revision": "9bf20a70227e457dee4c44938e8c8fa4"
}, {
  "url": "include/LD/assets/icons/utility/logout.svg",
  "revision": "3a8a38678c69b15abeb149f94ac5d982"
}, {
  "url": "include/LD/assets/icons/utility/loop.svg",
  "revision": "287af05e24bf1114972b174fdad62a00"
}, {
  "url": "include/LD/assets/icons/utility/lower_flag.svg",
  "revision": "9c4e00d347dcb367df80812c55072f76"
}, {
  "url": "include/LD/assets/icons/utility/macros.svg",
  "revision": "9ed83702adf52bfca3f7a0d9fc8cb6c9"
}, {
  "url": "include/LD/assets/icons/utility/magicwand.svg",
  "revision": "259f63521d37b820ccbb1a771a96f370"
}, {
  "url": "include/LD/assets/icons/utility/mark_all_as_read.svg",
  "revision": "d47efec96f2bb1015a5c1d2307f5b996"
}, {
  "url": "include/LD/assets/icons/utility/matrix.svg",
  "revision": "67be604669e8595c721157411e9cbe81"
}, {
  "url": "include/LD/assets/icons/utility/merge_field.svg",
  "revision": "6600ca05dc02f6798ce5a7856bc222da"
}, {
  "url": "include/LD/assets/icons/utility/merge.svg",
  "revision": "5f28f8b744683708befd1c745f419de8"
}, {
  "url": "include/LD/assets/icons/utility/metrics.svg",
  "revision": "e555fc5e34f95b65b8bf6d6baeb5aa5f"
}, {
  "url": "include/LD/assets/icons/utility/minimize_window.svg",
  "revision": "67ee23b84ebebd4281e31453e60b404d"
}, {
  "url": "include/LD/assets/icons/utility/missed_call.svg",
  "revision": "304a3b84fdff98f47f63ec25ba2a4b17"
}, {
  "url": "include/LD/assets/icons/utility/money.svg",
  "revision": "4f6922c23144310bba9269c08039ff6e"
}, {
  "url": "include/LD/assets/icons/utility/moneybag.svg",
  "revision": "e085925f337e0ba6ce302ea3920df41f"
}, {
  "url": "include/LD/assets/icons/utility/monthlyview.svg",
  "revision": "433587d92001a27af06c22591ed9bc1a"
}, {
  "url": "include/LD/assets/icons/utility/move.svg",
  "revision": "6d7fac2a7b6feb2c39a112aaf3365972"
}, {
  "url": "include/LD/assets/icons/utility/multi_picklist.svg",
  "revision": "b4d4190af263e022e31d56343c678274"
}, {
  "url": "include/LD/assets/icons/utility/multi_select_checkbox.svg",
  "revision": "bd6779453c72bc4ffbab6b72f399b507"
}, {
  "url": "include/LD/assets/icons/utility/muted.svg",
  "revision": "e70cf32f860f3966e8c79eaf687482d2"
}, {
  "url": "include/LD/assets/icons/utility/new_direct_message.svg",
  "revision": "8052a7ad69dca36244f9c099e40cc980"
}, {
  "url": "include/LD/assets/icons/utility/new_window.svg",
  "revision": "3885deabb9d0b664e080af1e0c3255b4"
}, {
  "url": "include/LD/assets/icons/utility/new.svg",
  "revision": "280df80a84aa76fffa3f1c7d866c3f30"
}, {
  "url": "include/LD/assets/icons/utility/news.svg",
  "revision": "d13ccf808f2b39e373b7690090d0952c"
}, {
  "url": "include/LD/assets/icons/utility/note.svg",
  "revision": "ae23cacb66e001392b1abf137a387f94"
}, {
  "url": "include/LD/assets/icons/utility/notebook.svg",
  "revision": "8afe66ff0cb9a81776e7c4ba4c05bb1d"
}, {
  "url": "include/LD/assets/icons/utility/notification.svg",
  "revision": "177f5a9f622f3b2367d39ddbde684a66"
}, {
  "url": "include/LD/assets/icons/utility/number_input.svg",
  "revision": "7f341f7d56a6b2bb5e4c31e51bb4e7bf"
}, {
  "url": "include/LD/assets/icons/utility/office365.svg",
  "revision": "9b1b747b5008b56e37a98ba546ec78e1"
}, {
  "url": "include/LD/assets/icons/utility/offline_cached.svg",
  "revision": "4819e8272fa1e5f2a793e6d472bb0a14"
}, {
  "url": "include/LD/assets/icons/utility/offline.svg",
  "revision": "87e394d5fcc64064a5572e9e6fd3709d"
}, {
  "url": "include/LD/assets/icons/utility/omni_channel.svg",
  "revision": "c6acc5098d67214bb1647cb24f4a0158"
}, {
  "url": "include/LD/assets/icons/utility/open_folder.svg",
  "revision": "fb99f5a02f6559ed536c014479aeb8a9"
}, {
  "url": "include/LD/assets/icons/utility/open.svg",
  "revision": "cd59f762662792b803879acb1559c6f2"
}, {
  "url": "include/LD/assets/icons/utility/opened_folder.svg",
  "revision": "2a1b377777165fd1c019ec0b4c1c1209"
}, {
  "url": "include/LD/assets/icons/utility/outbound_call.svg",
  "revision": "faae3ed1cd70814605e39581b94bd756"
}, {
  "url": "include/LD/assets/icons/utility/outcome.svg",
  "revision": "848c596406db331cc92b6a3c0442eb85"
}, {
  "url": "include/LD/assets/icons/utility/overflow.svg",
  "revision": "e1bf30a0d8aaabda3004a1a81150e999"
}, {
  "url": "include/LD/assets/icons/utility/package_org_beta.svg",
  "revision": "0975c8985e5e3dd8d65ff99901452899"
}, {
  "url": "include/LD/assets/icons/utility/package_org.svg",
  "revision": "c652b73c05df04b6370286f382228a71"
}, {
  "url": "include/LD/assets/icons/utility/package.svg",
  "revision": "ba02ab69637b730ad0591799ca40ea44"
}, {
  "url": "include/LD/assets/icons/utility/page.svg",
  "revision": "389598e2a3f7e2798c68e6882cb74cb9"
}, {
  "url": "include/LD/assets/icons/utility/palette.svg",
  "revision": "01ec6e7a2a321b3439b143d11782f7ae"
}, {
  "url": "include/LD/assets/icons/utility/password.svg",
  "revision": "ec7a916120bc62f1bd404f46ef949dd4"
}, {
  "url": "include/LD/assets/icons/utility/paste.svg",
  "revision": "9c259fea5455b4662105b902c469a137"
}, {
  "url": "include/LD/assets/icons/utility/pause.svg",
  "revision": "be681a26f791ea52127299749bb186f8"
}, {
  "url": "include/LD/assets/icons/utility/people.svg",
  "revision": "1f23b638cff6e4325e0c2b2effe84357"
}, {
  "url": "include/LD/assets/icons/utility/phone_landscape.svg",
  "revision": "07f5b92f7e6f57d8c16a33903b340ebb"
}, {
  "url": "include/LD/assets/icons/utility/phone_portrait.svg",
  "revision": "fde5c6333ceea7a2065dc8e77c8bb51d"
}, {
  "url": "include/LD/assets/icons/utility/photo.svg",
  "revision": "56adfbf2822047f46dfe5dac89068767"
}, {
  "url": "include/LD/assets/icons/utility/picklist_choice.svg",
  "revision": "61717e6729bce815119cbae49c9051fc"
}, {
  "url": "include/LD/assets/icons/utility/picklist_type.svg",
  "revision": "8abe07adc16cc58de410fd952d31a6c1"
}, {
  "url": "include/LD/assets/icons/utility/picklist.svg",
  "revision": "e1b5a697f84d52eda1c76a718ec12982"
}, {
  "url": "include/LD/assets/icons/utility/pin.svg",
  "revision": "ef2bbb670fe07dca456e6edf0d0782d7"
}, {
  "url": "include/LD/assets/icons/utility/pinned.svg",
  "revision": "fe168503b1b87a06b42c281341c39e6d"
}, {
  "url": "include/LD/assets/icons/utility/play.svg",
  "revision": "d5c4b5220f9a726aa6a9aa36eb45370e"
}, {
  "url": "include/LD/assets/icons/utility/podcast_webinar.svg",
  "revision": "4f3cc01183374194fbdbc49a78423b3e"
}, {
  "url": "include/LD/assets/icons/utility/pop_in.svg",
  "revision": "bb44e4014ae66fb3ddb6de9a3b4c57af"
}, {
  "url": "include/LD/assets/icons/utility/power.svg",
  "revision": "0dc2d87280538ea0f71ec6269e8aba63"
}, {
  "url": "include/LD/assets/icons/utility/preview.svg",
  "revision": "12ad8cbeb2b4486adb7fec8abf8397b3"
}, {
  "url": "include/LD/assets/icons/utility/print.svg",
  "revision": "a9b4b150d791e6fd50a0a57869b4340c"
}, {
  "url": "include/LD/assets/icons/utility/priority.svg",
  "revision": "037437751a74cb8103477d45a7274cfc"
}, {
  "url": "include/LD/assets/icons/utility/privately_shared.svg",
  "revision": "a84c58a955502f11ca9c55b2ac57b30a"
}, {
  "url": "include/LD/assets/icons/utility/process.svg",
  "revision": "aa57ee21bc14a08eca42d04fe33b4134"
}, {
  "url": "include/LD/assets/icons/utility/prompt_edit.svg",
  "revision": "df862c34fbac5ec5ca4af65e719af5eb"
}, {
  "url": "include/LD/assets/icons/utility/prompt.svg",
  "revision": "2bbd8e6ca1da6b7469ef40a057557e74"
}, {
  "url": "include/LD/assets/icons/utility/push.svg",
  "revision": "4e5f10808c0392d7d22939a2f89d33f1"
}, {
  "url": "include/LD/assets/icons/utility/puzzle.svg",
  "revision": "92294a386cfeb597239bebde2ff9ff3e"
}, {
  "url": "include/LD/assets/icons/utility/question_mark.svg",
  "revision": "d29892042a9d3e81dbd440f8bae7ae1e"
}, {
  "url": "include/LD/assets/icons/utility/question.svg",
  "revision": "0f762422b4e72ffb4e5c236df19d36e1"
}, {
  "url": "include/LD/assets/icons/utility/questions_and_answers.svg",
  "revision": "c1bb1861af2046991dae5b1ad42a3e0b"
}, {
  "url": "include/LD/assets/icons/utility/quick_text.svg",
  "revision": "86e8c060630aec106d4c47aed4c376ce"
}, {
  "url": "include/LD/assets/icons/utility/quip.svg",
  "revision": "96a40850cb893c3d875662048c96a3ec"
}, {
  "url": "include/LD/assets/icons/utility/quotation_marks.svg",
  "revision": "b4da577be3e205a1bd36a5c13603c60d"
}, {
  "url": "include/LD/assets/icons/utility/quote.svg",
  "revision": "8c4b27d52cc6a6baf9db23a5aea64eb4"
}, {
  "url": "include/LD/assets/icons/utility/radio_button.svg",
  "revision": "f922d58638f31da4e6bc60ead5172af0"
}, {
  "url": "include/LD/assets/icons/utility/rating.svg",
  "revision": "1cd4d6fa5abfb41455c12848f0e8e91c"
}, {
  "url": "include/LD/assets/icons/utility/reassign.svg",
  "revision": "e6b1a64158da940d6eb8ffa5c6fe9535"
}, {
  "url": "include/LD/assets/icons/utility/record_create.svg",
  "revision": "7512caabfe0b79f9bf107d75b54c99a7"
}, {
  "url": "include/LD/assets/icons/utility/record_delete.svg",
  "revision": "684c3bca36424d62e311c3e6414fa4a8"
}, {
  "url": "include/LD/assets/icons/utility/record_lookup.svg",
  "revision": "a915fd9950fd0fcbbe238f0b7d0050d5"
}, {
  "url": "include/LD/assets/icons/utility/record_update.svg",
  "revision": "85b37d9a26bf4d10eefd3dd089bb2504"
}, {
  "url": "include/LD/assets/icons/utility/record.svg",
  "revision": "c5a457a7f16fc3b7dd5c61acb99d7aa4"
}, {
  "url": "include/LD/assets/icons/utility/recurring_exception.svg",
  "revision": "bf751a1ec8dea3c4fa56f2965c385d72"
}, {
  "url": "include/LD/assets/icons/utility/recycle_bin_empty.svg",
  "revision": "f7e1bec9687b35a7416d3291be2bd5e2"
}, {
  "url": "include/LD/assets/icons/utility/recycle_bin_full.svg",
  "revision": "5d37152cabe23ed90c0ebfe58c0e82f7"
}, {
  "url": "include/LD/assets/icons/utility/redo.svg",
  "revision": "a960ad77e1d3aa48f52689860d02d3f8"
}, {
  "url": "include/LD/assets/icons/utility/refresh.svg",
  "revision": "3cf7d2a5ea45a0abe3066b257146cbda"
}, {
  "url": "include/LD/assets/icons/utility/relate.svg",
  "revision": "0a095c9731c059b328d3d1b238c9130f"
}, {
  "url": "include/LD/assets/icons/utility/reminder.svg",
  "revision": "6e9e55ba014d5155debbc7002d7085f7"
}, {
  "url": "include/LD/assets/icons/utility/remove_formatting.svg",
  "revision": "0ec127d1c1abd730284acbf75b0d1324"
}, {
  "url": "include/LD/assets/icons/utility/remove_link.svg",
  "revision": "4046f30d6252fde5565bf37a9a021762"
}, {
  "url": "include/LD/assets/icons/utility/replace.svg",
  "revision": "6bb2a79175bba89d782d70de18b112c2"
}, {
  "url": "include/LD/assets/icons/utility/reply_all.svg",
  "revision": "8ac848cef86697fd10e8f271a6860f04"
}, {
  "url": "include/LD/assets/icons/utility/reply.svg",
  "revision": "437330bd80e03c4213adf43f694f09b1"
}, {
  "url": "include/LD/assets/icons/utility/report_issue.svg",
  "revision": "bc62f05f5f68ad898299a295ba832f5a"
}, {
  "url": "include/LD/assets/icons/utility/reset_password.svg",
  "revision": "04628c1ccd425c18f672b25831614415"
}, {
  "url": "include/LD/assets/icons/utility/resource_absence.svg",
  "revision": "51876ccc92312f22511799ca949f1af4"
}, {
  "url": "include/LD/assets/icons/utility/resource_capacity.svg",
  "revision": "551f856f31091db7c858a59da4b77a14"
}, {
  "url": "include/LD/assets/icons/utility/resource_territory.svg",
  "revision": "8f112d7e3e6b97a468689f5cf7d97858"
}, {
  "url": "include/LD/assets/icons/utility/retail_execution.svg",
  "revision": "c7043e119ee2d6d12d1a88aa2695f79e"
}, {
  "url": "include/LD/assets/icons/utility/retweet.svg",
  "revision": "4bb64acfbc61ee1571809eb229c3e2e0"
}, {
  "url": "include/LD/assets/icons/utility/ribbon.svg",
  "revision": "56f7f9e69e0ecf203b57acd0173c5979"
}, {
  "url": "include/LD/assets/icons/utility/richtextbulletedlist.svg",
  "revision": "f11bce992dcc14740d9fdc6dadfebd62"
}, {
  "url": "include/LD/assets/icons/utility/richtextindent.svg",
  "revision": "6a940574dbdf499efec15c03f78ad14e"
}, {
  "url": "include/LD/assets/icons/utility/richtextnumberedlist.svg",
  "revision": "293fc38b06d9d43c63a2050ea5410ede"
}, {
  "url": "include/LD/assets/icons/utility/richtextoutdent.svg",
  "revision": "81e36c7bc21563a6c8f2caef81778c5a"
}, {
  "url": "include/LD/assets/icons/utility/right_align_text.svg",
  "revision": "e68bbcc58f09d59b8e15b89d0b27ed6b"
}, {
  "url": "include/LD/assets/icons/utility/right.svg",
  "revision": "5a0583eaab4a2e8b4637fa679a8326fc"
}, {
  "url": "include/LD/assets/icons/utility/rotate.svg",
  "revision": "ddda42b90a0a8bf5469843aa4d3bb261"
}, {
  "url": "include/LD/assets/icons/utility/routing_offline.svg",
  "revision": "ec91544f65e99415fddcf0b3ce90d923"
}, {
  "url": "include/LD/assets/icons/utility/rows.svg",
  "revision": "164e48bb4892fc94270f972efe902608"
}, {
  "url": "include/LD/assets/icons/utility/rules.svg",
  "revision": "b901a76b66d07b6f797628968379c97b"
}, {
  "url": "include/LD/assets/icons/utility/salesforce1.svg",
  "revision": "d1075d43c6a8e137ab5be2a30d536869"
}, {
  "url": "include/LD/assets/icons/utility/save.svg",
  "revision": "76c0b04130246f7af3daf966a7f2b133"
}, {
  "url": "include/LD/assets/icons/utility/screen.svg",
  "revision": "9e6732f53fc29867d9e1f7a0ca432846"
}, {
  "url": "include/LD/assets/icons/utility/search.svg",
  "revision": "fd30d81f3e1b51331c2904da55fa3008"
}, {
  "url": "include/LD/assets/icons/utility/send.svg",
  "revision": "2a187df564cc5efcbf68ed23c1dd0f2d"
}, {
  "url": "include/LD/assets/icons/utility/sentiment_negative.svg",
  "revision": "859459cd586d28839a14dae864ddc7cf"
}, {
  "url": "include/LD/assets/icons/utility/sentiment_neutral.svg",
  "revision": "ca1081f6faa63e495e8024b617bc2bc6"
}, {
  "url": "include/LD/assets/icons/utility/settings.svg",
  "revision": "987fca88383aabb572ee5c0af6a34232"
}, {
  "url": "include/LD/assets/icons/utility/setup_assistant_guide.svg",
  "revision": "967e6084d76906152182f604c407b912"
}, {
  "url": "include/LD/assets/icons/utility/setup_modal.svg",
  "revision": "fd456a91bd2adf77be392da48889d705"
}, {
  "url": "include/LD/assets/icons/utility/setup.svg",
  "revision": "f787cc087b66178371634d4b0e7001f1"
}, {
  "url": "include/LD/assets/icons/utility/share_file.svg",
  "revision": "fcf67888d066a51928036d4bf9b5d73d"
}, {
  "url": "include/LD/assets/icons/utility/share_mobile.svg",
  "revision": "479b33c44059368aaa9fc999350a7b99"
}, {
  "url": "include/LD/assets/icons/utility/share_post.svg",
  "revision": "ee77077bbd46815bbb752eb3f9d07b9e"
}, {
  "url": "include/LD/assets/icons/utility/share.svg",
  "revision": "085e91beb9fa163cc41381193a4156be"
}, {
  "url": "include/LD/assets/icons/utility/shield.svg",
  "revision": "3c976923e3333941445fb7bf58feb0b1"
}, {
  "url": "include/LD/assets/icons/utility/shift_ui.svg",
  "revision": "a395cc26190f5b26998802617561ce19"
}, {
  "url": "include/LD/assets/icons/utility/shopping_bag.svg",
  "revision": "67d9bdd5016d87bb8bc69f8ae3610cd5"
}, {
  "url": "include/LD/assets/icons/utility/shortcuts.svg",
  "revision": "07cac871fc9d0aa7c84eea1cbf063c4e"
}, {
  "url": "include/LD/assets/icons/utility/side_list.svg",
  "revision": "a4561019174de0a8cd606c694c6f76ce"
}, {
  "url": "include/LD/assets/icons/utility/signpost.svg",
  "revision": "b3020cc1af210f4052d15d5deb6e0940"
}, {
  "url": "include/LD/assets/icons/utility/skip_back.svg",
  "revision": "aa548c75878812776a85e19a8c7755ca"
}, {
  "url": "include/LD/assets/icons/utility/skip_forward.svg",
  "revision": "c53cb3c1b370acddae54b6c908fb158f"
}, {
  "url": "include/LD/assets/icons/utility/skip.svg",
  "revision": "74a8a0ae167e19627169992875139e07"
}, {
  "url": "include/LD/assets/icons/utility/smiley_and_people.svg",
  "revision": "7152d43ec2e3cf98d60258079b4e2514"
}, {
  "url": "include/LD/assets/icons/utility/sms.svg",
  "revision": "7cd496ff6c9560a7e9923b9def1994af"
}, {
  "url": "include/LD/assets/icons/utility/snippet.svg",
  "revision": "001fc9d7c9852b58c149a31ebff03c84"
}, {
  "url": "include/LD/assets/icons/utility/sobject_collection.svg",
  "revision": "ff7fd01d1883238db0b0594705a5e161"
}, {
  "url": "include/LD/assets/icons/utility/sobject.svg",
  "revision": "d1212ab04e14b66333453cb447001d79"
}, {
  "url": "include/LD/assets/icons/utility/socialshare.svg",
  "revision": "062a68fecd1ff033bbac1cbf71989069"
}, {
  "url": "include/LD/assets/icons/utility/sort.svg",
  "revision": "beca2649fcb6cd20d9efa6394248b00c"
}, {
  "url": "include/LD/assets/icons/utility/spinner.svg",
  "revision": "40c8d310003dc0955e322150119a2bd1"
}, {
  "url": "include/LD/assets/icons/utility/stage_collection.svg",
  "revision": "ddbdb4aaa9bac6a99f22438f7d1630bc"
}, {
  "url": "include/LD/assets/icons/utility/stage.svg",
  "revision": "6930f9d4c39b9fb6843dd38de25a9518"
}, {
  "url": "include/LD/assets/icons/utility/standard_objects.svg",
  "revision": "ca0e997de9ad547a1bf06d298f556f57"
}, {
  "url": "include/LD/assets/icons/utility/steps.svg",
  "revision": "27b663cdd56bcbaffd84dab4a63087be"
}, {
  "url": "include/LD/assets/icons/utility/stop.svg",
  "revision": "5c675b9b6bf5c87d5efdf6929832f14a"
}, {
  "url": "include/LD/assets/icons/utility/store.svg",
  "revision": "76d3ae6ce636078c66739f3ba9a08345"
}, {
  "url": "include/LD/assets/icons/utility/strategy.svg",
  "revision": "1044d0ab00d4933d5182d81e18f0b69c"
}, {
  "url": "include/LD/assets/icons/utility/strikethrough.svg",
  "revision": "ff74a368212cafdb8646e0b8b53a06f9"
}, {
  "url": "include/LD/assets/icons/utility/success.svg",
  "revision": "ec48c944a774b12fb9697e2f823cc755"
}, {
  "url": "include/LD/assets/icons/utility/summary.svg",
  "revision": "fec811cffc5b51c516a0d529dd7cf5aa"
}, {
  "url": "include/LD/assets/icons/utility/summarydetail.svg",
  "revision": "f91b95f9f1ab2bbe54f21c6862a20d4f"
}, {
  "url": "include/LD/assets/icons/utility/survey.svg",
  "revision": "b0d3f1d71f983f580ef3767f2e022ebf"
}, {
  "url": "include/LD/assets/icons/utility/switch.svg",
  "revision": "5ef4c0939670f727c9b8892358cd7def"
}, {
  "url": "include/LD/assets/icons/utility/symbols.svg",
  "revision": "755f5bd901d89c8620319ac3da4198b2"
}, {
  "url": "include/LD/assets/icons/utility/sync.svg",
  "revision": "8180cb0b08868fd48f6f2206138bc827"
}, {
  "url": "include/LD/assets/icons/utility/system_and_global_variable.svg",
  "revision": "2169490b11a65d439bf4d5c291cb9cc4"
}, {
  "url": "include/LD/assets/icons/utility/table.svg",
  "revision": "fbce293e43ffb76eaa57332513fa8436"
}, {
  "url": "include/LD/assets/icons/utility/tablet_landscape.svg",
  "revision": "d1c40e5233d46f5f13c79616ab851bed"
}, {
  "url": "include/LD/assets/icons/utility/tablet_portrait.svg",
  "revision": "1396ff68e337b400860a746306af791f"
}, {
  "url": "include/LD/assets/icons/utility/tabset.svg",
  "revision": "a8ed0eb89681af11fa5a66ddee60dd2f"
}, {
  "url": "include/LD/assets/icons/utility/task.svg",
  "revision": "3cd52d70366ec9f7a97a9867100e3a52"
}, {
  "url": "include/LD/assets/icons/utility/text_background_color.svg",
  "revision": "e7d444e1fe0bebf7ad3f22ee02639242"
}, {
  "url": "include/LD/assets/icons/utility/text_color.svg",
  "revision": "9baf6f2327e8e6a9b1e9572935ac3e88"
}, {
  "url": "include/LD/assets/icons/utility/text_template.svg",
  "revision": "18fecee370abe420f4daf8ba86e782f4"
}, {
  "url": "include/LD/assets/icons/utility/text.svg",
  "revision": "11e7fb6c1dfb6457fcfba6cbf1176ee5"
}, {
  "url": "include/LD/assets/icons/utility/textarea.svg",
  "revision": "18392fcbf0cc9dcf5c5fd645101355af"
}, {
  "url": "include/LD/assets/icons/utility/textbox.svg",
  "revision": "c5c66cd214d60730bae676011351fede"
}, {
  "url": "include/LD/assets/icons/utility/threedots_vertical.svg",
  "revision": "3dd1b3ca5ad80f43cd404d9985697455"
}, {
  "url": "include/LD/assets/icons/utility/threedots.svg",
  "revision": "6446b98ddcaaf5ad04298f9bb6a92cc5"
}, {
  "url": "include/LD/assets/icons/utility/thunder.svg",
  "revision": "a161d9b4d889d1ae45cdda0c9938c19b"
}, {
  "url": "include/LD/assets/icons/utility/tile_card_list.svg",
  "revision": "903c7c0da60fe33cc4e1586db91f181b"
}, {
  "url": "include/LD/assets/icons/utility/toggle_panel_bottom.svg",
  "revision": "f8e35941865c8ceb6bd2ca5794abedea"
}, {
  "url": "include/LD/assets/icons/utility/toggle_panel_left.svg",
  "revision": "2d8a589fe7a774417ca83e917ea22ca9"
}, {
  "url": "include/LD/assets/icons/utility/toggle_panel_right.svg",
  "revision": "dcac6ca55acb96bfe14948b54e213213"
}, {
  "url": "include/LD/assets/icons/utility/toggle_panel_top.svg",
  "revision": "8588cfcce698c117ae269bada52f3a1b"
}, {
  "url": "include/LD/assets/icons/utility/topic.svg",
  "revision": "ab918c1914708f061eeb5bbfc0eb688e"
}, {
  "url": "include/LD/assets/icons/utility/topic2.svg",
  "revision": "dba5467434c7d2e34831a94a1251c97f"
}, {
  "url": "include/LD/assets/icons/utility/touch_action.svg",
  "revision": "f5c1c660f8eca0b7fc060ba19cfe70a6"
}, {
  "url": "include/LD/assets/icons/utility/tracker.svg",
  "revision": "7823df10d734a7fe985406ae46f42211"
}, {
  "url": "include/LD/assets/icons/utility/trail.svg",
  "revision": "6dd3dece365fdd3abb8302dc2d68599a"
}, {
  "url": "include/LD/assets/icons/utility/trailhead.svg",
  "revision": "bf61549df723bd5855c9444053be4147"
}, {
  "url": "include/LD/assets/icons/utility/travel_and_places.svg",
  "revision": "ba0c42bce439342c80a84d5e3a4dd686"
}, {
  "url": "include/LD/assets/icons/utility/trending.svg",
  "revision": "c0f1b19359d9d4c3353d645adfbe6e38"
}, {
  "url": "include/LD/assets/icons/utility/turn_off_notifications.svg",
  "revision": "7b414a34a99fbb176ae4364be4cd3324"
}, {
  "url": "include/LD/assets/icons/utility/type_tool.svg",
  "revision": "00226b5b1a57250e68478b1e76f5256a"
}, {
  "url": "include/LD/assets/icons/utility/type.svg",
  "revision": "463ab3c2e828b0b51f6999cb2fec474d"
}, {
  "url": "include/LD/assets/icons/utility/undelete.svg",
  "revision": "e8af8b634f35dd640b028fcfde6b497f"
}, {
  "url": "include/LD/assets/icons/utility/undeprecate.svg",
  "revision": "e0f9b6e5633b6ca439f07449a662a522"
}, {
  "url": "include/LD/assets/icons/utility/underline.svg",
  "revision": "a32ae67bd1fcc0ec76fa208a1c2d2311"
}, {
  "url": "include/LD/assets/icons/utility/undo.svg",
  "revision": "003bcb6137420c27dbf3041f519baf3e"
}, {
  "url": "include/LD/assets/icons/utility/unlinked.svg",
  "revision": "71605fcdd50bac2812c185f5c3308c97"
}, {
  "url": "include/LD/assets/icons/utility/unlock.svg",
  "revision": "959116234fc751cbe27b3db489e3d708"
}, {
  "url": "include/LD/assets/icons/utility/unmuted.svg",
  "revision": "dd1a09769a1012ec8cef818620cb5d3b"
}, {
  "url": "include/LD/assets/icons/utility/up.svg",
  "revision": "6c51cfd6332b131434a2701756f1b4e6"
}, {
  "url": "include/LD/assets/icons/utility/upload.svg",
  "revision": "c7a865ee55d392c0146e79737d4f9acf"
}, {
  "url": "include/LD/assets/icons/utility/user_role.svg",
  "revision": "d896c5a615d6892603b9318808318f48"
}, {
  "url": "include/LD/assets/icons/utility/user.svg",
  "revision": "eaf556fe598cd7822ef415c390df15dd"
}, {
  "url": "include/LD/assets/icons/utility/variable.svg",
  "revision": "57bc2d7f7d813d807ef5e7c3a200919b"
}, {
  "url": "include/LD/assets/icons/utility/video.svg",
  "revision": "83c12d9af3b4a3e39505a12505a9038c"
}, {
  "url": "include/LD/assets/icons/utility/voicemail_drop.svg",
  "revision": "fd42a57c15e3b3d06b5aa2ec1f5d457b"
}, {
  "url": "include/LD/assets/icons/utility/volume_high.svg",
  "revision": "2ff9e49eed31145d7c51d673b4fc2ba9"
}, {
  "url": "include/LD/assets/icons/utility/volume_low.svg",
  "revision": "ee65496756f0bd2ef2d01188465673f9"
}, {
  "url": "include/LD/assets/icons/utility/volume_off.svg",
  "revision": "402179568f57ceef12aec48fb0b94685"
}, {
  "url": "include/LD/assets/icons/utility/waits.svg",
  "revision": "38ff3da8a7b18d3c6bb4bb5537b244c0"
}, {
  "url": "include/LD/assets/icons/utility/warning.svg",
  "revision": "61e1dc42e363aecd75d1dd5e1f69a545"
}, {
  "url": "include/LD/assets/icons/utility/weeklyview.svg",
  "revision": "3db68a6d5c6671f9d2a5aee9373609eb"
}, {
  "url": "include/LD/assets/icons/utility/wifi.svg",
  "revision": "c4e561e3fbf45d9d08a2bd03def042bf"
}, {
  "url": "include/LD/assets/icons/utility/work_order_type.svg",
  "revision": "e1cb42a9644a23267cb82784c0cd9070"
}, {
  "url": "include/LD/assets/icons/utility/world.svg",
  "revision": "75fcd62e1d38cb6c1bfbbad392341114"
}, {
  "url": "include/LD/assets/icons/utility/yubi_key.svg",
  "revision": "be3db1fa4ccc946b17202ad4bbe8dd82"
}, {
  "url": "include/LD/assets/icons/utility/zoomin.svg",
  "revision": "0ce7e611bf2174e09a18e31b4ad9b80b"
}, {
  "url": "include/LD/assets/icons/utility/zoomout.svg",
  "revision": "961827106ab42735edb481000074aa1b"
}, {
  "url": "include/LD/assets/images/einstein-headers/einstein-figure.svg",
  "revision": "075cd62c37c61e487575ec7eef5d3c54"
}, {
  "url": "include/LD/assets/images/einstein-headers/einstein-header-background.svg",
  "revision": "d8ab98b9ac6eb29f29634dad79ac247f"
}, {
  "url": "include/LD/assets/images/illustrations/empty-state-assistant.svg",
  "revision": "50308afbf7d1f1e6b9a0a50d8099756e"
}, {
  "url": "include/LD/assets/images/illustrations/empty-state-events.svg",
  "revision": "77c0e0459771e9612b120728ddea77ac"
}, {
  "url": "include/LD/assets/images/illustrations/empty-state-tasks.svg",
  "revision": "fa7b8313f535a6140a0bfad15bc0bfc0"
}, {
  "url": "include/LD/assets/images/logo-noname.svg",
  "revision": "4f89fe7849ce8466563e77b10300658e"
}, {
  "url": "include/LD/assets/images/logo.svg",
  "revision": "4f89fe7849ce8466563e77b10300658e"
}, {
  "url": "include/LD/assets/styles/mainmenu.css",
  "revision": "60718c9174ed712f0c6a240e34adec1d"
}, {
  "url": "include/LD/assets/styles/override_lds.css",
  "revision": "f304bcad34585dd5835ea3eee334899e"
}, {
  "url": "include/LD/assets/styles/salesforce-lightning-design-system_touch.css",
  "revision": "aae0a7ead47c69e71bbd2ae18e509560"
}, {
  "url": "include/LD/assets/styles/salesforce-lightning-design-system_touch.min.css",
  "revision": "46fde5983c0f10a6c20d017c59a429ab"
}, {
  "url": "include/LD/assets/styles/salesforce-lightning-design-system-imports.sanitized.css",
  "revision": "a9ed1dfce257f33386bfda9af44fe433"
}, {
  "url": "include/LD/assets/styles/salesforce-lightning-design-system.css",
  "revision": "cbd3a11e6a41230e945013170267f563"
}, {
  "url": "include/LD/assets/styles/salesforce-lightning-design-system.min.css",
  "revision": "889b70380f786163decbb5c1a8a28a5c"
}, {
  "url": "include/LD/assets/styles/salesforce-lightning-design-system.sanitized.css",
  "revision": "a9a370a7638ecb835746227e16b8bbf2"
}, {
  "url": "include/ckeditor/adapters/jquery.js",
  "revision": "ccef5f71850287b35f32909cae86f96e"
}, {
  "url": "include/ckeditor/images/spacer.gif",
  "revision": "df3e567d6f16d040326c7a0ea29a4f41"
}, {
  "url": "include/ckeditor/lang/af.js",
  "revision": "f6d371af49c2e1b1279b7c5eb5b50853"
}, {
  "url": "include/ckeditor/lang/ar.js",
  "revision": "064c31eebc9541def15e61b276dd92cd"
}, {
  "url": "include/ckeditor/lang/bg.js",
  "revision": "f29058b47dfaea7865204627d40cf7e2"
}, {
  "url": "include/ckeditor/lang/bn.js",
  "revision": "2b15cd493bb3bc40074a9307ecca608e"
}, {
  "url": "include/ckeditor/lang/bs.js",
  "revision": "cace049dad1dace2d8e242964721d8c5"
}, {
  "url": "include/ckeditor/lang/ca.js",
  "revision": "d5bfffa4351f20f6d724933ea44dd84f"
}, {
  "url": "include/ckeditor/lang/cs.js",
  "revision": "af573449cf73cc29fbbe88d4aba5d1c4"
}, {
  "url": "include/ckeditor/lang/cy.js",
  "revision": "d1a5d44df6e09139dfdd2e9abbafc70c"
}, {
  "url": "include/ckeditor/lang/da.js",
  "revision": "1620ac787996e4b56126bcf43aa97e90"
}, {
  "url": "include/ckeditor/lang/de-ch.js",
  "revision": "002da4efce3a25611d7ae61795c21fd1"
}, {
  "url": "include/ckeditor/lang/de.js",
  "revision": "90fc6b41c0f4d1037655ae248b0030aa"
}, {
  "url": "include/ckeditor/lang/el.js",
  "revision": "fc4933158aaa499b180c5093459ee90f"
}, {
  "url": "include/ckeditor/lang/en-au.js",
  "revision": "12403a66a23b1924cafa8ac18df86608"
}, {
  "url": "include/ckeditor/lang/en-ca.js",
  "revision": "dcdd9c1bbee1abb13169cd343cd86bcf"
}, {
  "url": "include/ckeditor/lang/en-gb.js",
  "revision": "7eebff3bc2f7c72cd610f4e256e45c8b"
}, {
  "url": "include/ckeditor/lang/en.js",
  "revision": "3f98edc405308347d7e54e0649013cbc"
}, {
  "url": "include/ckeditor/lang/eo.js",
  "revision": "6f4e5de867d65d46e1f13f3c7d8eb951"
}, {
  "url": "include/ckeditor/lang/es.js",
  "revision": "0e113155a98cfb4cdedc0c07f235ec1c"
}, {
  "url": "include/ckeditor/lang/et.js",
  "revision": "eee60e223e7b5d8190d41de130d33d03"
}, {
  "url": "include/ckeditor/lang/eu.js",
  "revision": "e98616df677f18f9dd60ea041f9fa9aa"
}, {
  "url": "include/ckeditor/lang/fa.js",
  "revision": "8cd7bc976b49f4d77b73362f1b6cae4d"
}, {
  "url": "include/ckeditor/lang/fi.js",
  "revision": "d54fac4d907fe05bab61fb883935ff48"
}, {
  "url": "include/ckeditor/lang/fo.js",
  "revision": "168119e2b5fcf3558f4284e9e711f25c"
}, {
  "url": "include/ckeditor/lang/fr-ca.js",
  "revision": "de2f6818db81023df747fd11d461357b"
}, {
  "url": "include/ckeditor/lang/fr.js",
  "revision": "7d2885960d8d2cbe717ce2f9e60c02f7"
}, {
  "url": "include/ckeditor/lang/gl.js",
  "revision": "dc039af05d6693f9f1f269efee04e3bd"
}, {
  "url": "include/ckeditor/lang/gu.js",
  "revision": "900622ce3611edee75c0b77b92af2214"
}, {
  "url": "include/ckeditor/lang/he.js",
  "revision": "c3de2035f2a7a5560f6191bb720edc71"
}, {
  "url": "include/ckeditor/lang/hi.js",
  "revision": "51d529deac4c920738082875258f7ff0"
}, {
  "url": "include/ckeditor/lang/hr.js",
  "revision": "65ab1a4378775a1ffaf0a9e14c7e5db8"
}, {
  "url": "include/ckeditor/lang/hu.js",
  "revision": "3c8ba198d400aca7703020ad9196a053"
}, {
  "url": "include/ckeditor/lang/id.js",
  "revision": "adeb65253375e0c7d83b2244f9ed4a01"
}, {
  "url": "include/ckeditor/lang/is.js",
  "revision": "b50c87b69fb853e491a1be015b0a55b6"
}, {
  "url": "include/ckeditor/lang/it.js",
  "revision": "2b6c88cc7d2ff683f78fbd50e2118ce2"
}, {
  "url": "include/ckeditor/lang/ja.js",
  "revision": "4a8b6a7a661fd205a1012297d8cbacc7"
}, {
  "url": "include/ckeditor/lang/ka.js",
  "revision": "ca3b1261c167f06c83163cb0cd262567"
}, {
  "url": "include/ckeditor/lang/km.js",
  "revision": "7b0a903154ba7b61b79acad560dbb661"
}, {
  "url": "include/ckeditor/lang/ko.js",
  "revision": "9991aa0c7fefcbacc65a437199a510be"
}, {
  "url": "include/ckeditor/lang/ku.js",
  "revision": "17defa6f7961b85d33a9ff230fba428b"
}, {
  "url": "include/ckeditor/lang/lt.js",
  "revision": "2fe654d257b4d88f81c18b14136b9d37"
}, {
  "url": "include/ckeditor/lang/lv.js",
  "revision": "ef8f495f1c671f33b5ce119f7f27809e"
}, {
  "url": "include/ckeditor/lang/mk.js",
  "revision": "dae421b5e0d579b31cdf10b20c82ecb2"
}, {
  "url": "include/ckeditor/lang/mn.js",
  "revision": "5707c68ace6fce5262bb994a9a5c2cab"
}, {
  "url": "include/ckeditor/lang/ms.js",
  "revision": "5049dd5eddcc4e02223040b6f88a7f2d"
}, {
  "url": "include/ckeditor/lang/nb.js",
  "revision": "2dab24c29e6eee6288decf44935383f9"
}, {
  "url": "include/ckeditor/lang/nl.js",
  "revision": "9e62f2be2671bbc389ac8dee10586aa9"
}, {
  "url": "include/ckeditor/lang/no.js",
  "revision": "8023dd2a9b8149288fe983074389d061"
}, {
  "url": "include/ckeditor/lang/pl.js",
  "revision": "37bc032fddae2ca549748c7c4f1718a4"
}, {
  "url": "include/ckeditor/lang/pt-br.js",
  "revision": "2f24393ae106d670e69e7a639374afcb"
}, {
  "url": "include/ckeditor/lang/pt.js",
  "revision": "c2a419c948a6088c54552981d4d0bb41"
}, {
  "url": "include/ckeditor/lang/ro.js",
  "revision": "02b6fbcecf9f7b08ece0e19e8f58e568"
}, {
  "url": "include/ckeditor/lang/ru.js",
  "revision": "a7b9e19f688a34b8707d78fa1fcb6bb2"
}, {
  "url": "include/ckeditor/lang/si.js",
  "revision": "40301b3af237d3e473c36bfea7bd436a"
}, {
  "url": "include/ckeditor/lang/sk.js",
  "revision": "2a6c16571288d69c5b2649f99cafdabc"
}, {
  "url": "include/ckeditor/lang/sl.js",
  "revision": "a3822a5d7cdb531311c183ed48c72e01"
}, {
  "url": "include/ckeditor/lang/sq.js",
  "revision": "99a33673f7a9e49b8e91961decd64875"
}, {
  "url": "include/ckeditor/lang/sr-latn.js",
  "revision": "0bb41197f51a815689e426c02fa68cfe"
}, {
  "url": "include/ckeditor/lang/sr.js",
  "revision": "5a816b8ca0ee9df43040205994a0a5a5"
}, {
  "url": "include/ckeditor/lang/sv.js",
  "revision": "9af8fc5999f93e6b7b8d8a4362568459"
}, {
  "url": "include/ckeditor/lang/th.js",
  "revision": "94ca015609f6773d55652410ed6d1e4e"
}, {
  "url": "include/ckeditor/lang/tr.js",
  "revision": "804482e3b76041293eba5fd6178db04f"
}, {
  "url": "include/ckeditor/lang/tt.js",
  "revision": "5fdff97c0777a6db0f3e3e066b30e50d"
}, {
  "url": "include/ckeditor/lang/ug.js",
  "revision": "d9746fb84c4b809aeaaedfa31fc87a81"
}, {
  "url": "include/ckeditor/lang/uk.js",
  "revision": "ad97b6dd01c71ed0dde922a644c1b7cf"
}, {
  "url": "include/ckeditor/lang/vi.js",
  "revision": "a734d135fe107139d3da66b39d0aac63"
}, {
  "url": "include/ckeditor/lang/zh-cn.js",
  "revision": "451f3a29d132b4030223f0fc0c8f9b5e"
}, {
  "url": "include/ckeditor/lang/zh.js",
  "revision": "3ade1f9afeb618db4425b82ce44d27bc"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/a11yhelp.js",
  "revision": "c906ae04cdebc6cbc3921d50003b4bde"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/af.js",
  "revision": "553e745a570b423461a9093199acb4c6"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/ar.js",
  "revision": "dd2b314a2669755bb543b54ddaffda26"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/bg.js",
  "revision": "bccb22cdb8371f0f70710856a3aefc49"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/ca.js",
  "revision": "abc036c3a90c5f5197ac1f8dbd56c244"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/cs.js",
  "revision": "77098065f5d86f215bbfa3e0a8b6d195"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/cy.js",
  "revision": "d193057322cc82bd2d15912f7639611d"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/da.js",
  "revision": "d3fea6f1a9c66de78f5de496a7bb6e83"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/de-ch.js",
  "revision": "f97042007621a5b973407c11969d7c59"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/de.js",
  "revision": "1d0599e93873e55ec6e11bcaf32115ae"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/el.js",
  "revision": "0b74dc52aa225184a06a5751961a7f12"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/en-gb.js",
  "revision": "d894d758bf5af1a12832483a8354ec0a"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/en.js",
  "revision": "68a16ffd3231909cc0a39d894aba89aa"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/eo.js",
  "revision": "45a1cd2a6f6a77c7e179e9bfe896f6e4"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/es.js",
  "revision": "38dd0a3caef280c5e0c504853ff2180d"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/et.js",
  "revision": "86364a93769cab8ef19d883db5b0e09c"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/eu.js",
  "revision": "f43bf6be385f21c393deffc22d0c3482"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/fa.js",
  "revision": "5bc46bb815cf50c16bb4673aa04d4787"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/fi.js",
  "revision": "e426b048f06165595ab2413ca50e4da4"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/fo.js",
  "revision": "d961766bd87d0a18de30827cd73ce61f"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/fr-ca.js",
  "revision": "2dc13aa30a4ba040b5880840a7e2175e"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/fr.js",
  "revision": "f32361e216e30d3784ad5920f034a07b"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/gl.js",
  "revision": "3bba8421a304291a7e0348f519b381aa"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/gu.js",
  "revision": "5e0a3caab18ca1c2910d373b5d4e0723"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/he.js",
  "revision": "e898c0cd185ba8e61c49c250f0cf592d"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/hi.js",
  "revision": "e6dae7713f0b6948e92c74c3d5a98698"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/hr.js",
  "revision": "79c930a378a786e66d9b7bc967ca44ca"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/hu.js",
  "revision": "5a950600a4244106921edc76f108d171"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/id.js",
  "revision": "9fb9560ae70043c5e8045622822b36fa"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/it.js",
  "revision": "d9bd2a6940a345a76c783d80bc52bf21"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/ja.js",
  "revision": "cf30da3d4b4f9a8026529a0c3b09ae2f"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/km.js",
  "revision": "2f27233cf2186024f1df752075b46eef"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/ko.js",
  "revision": "03a292571d70db2c4a3b3c5d5518b0e9"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/ku.js",
  "revision": "3e34cf99f581d84268c22caabc7c4c8f"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/lt.js",
  "revision": "962c8badbdc62a2f83cd9329837f06aa"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/lv.js",
  "revision": "28417f0950f211fc8f6cfaf7f63df97b"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/mk.js",
  "revision": "bcf317fcf6f52e7c5ccbaf9f4b17b3ce"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/mn.js",
  "revision": "b2b5a688a76eb4ece381c15a8e9f6d96"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/nb.js",
  "revision": "2be1b55b0c2327fbb2521fccdb7e2540"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/nl.js",
  "revision": "134b2dc245f472b4f43e57c23de520d8"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/no.js",
  "revision": "71437c83ac15064606315a66f047e59b"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/pl.js",
  "revision": "e7151ebd23aa5e2893dead061b40f61c"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/pt-br.js",
  "revision": "f047f098673e46bae83a01c015c525e9"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/pt.js",
  "revision": "879e2bdf34819cf10c25c31cbdb2f66e"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/ro.js",
  "revision": "2729fa55289b2ddcd3ba286f3b43a66d"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/ru.js",
  "revision": "754fe9ca70dedf67210b9b7f6a2712fa"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/si.js",
  "revision": "d6ee00f42cf411de41f0e4157820d283"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/sk.js",
  "revision": "7f18259c7a6e3a42789b16805b574435"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/sl.js",
  "revision": "acdcb8ab80249ed32f16557f352d4ec7"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/sq.js",
  "revision": "af903a3367c70761f672c00af89d80a4"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/sr-latn.js",
  "revision": "7b15bd3e9800336a8f4c34b6defd350e"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/sr.js",
  "revision": "062aa900420c3bd5ab46b74f10a67163"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/sv.js",
  "revision": "f3fa00db64880849abbd926627bed1e4"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/th.js",
  "revision": "429bfd0a36687a17e733994975f5e9d6"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/tr.js",
  "revision": "c7c58fa20bbb9d9de80b64f7aea257b5"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/tt.js",
  "revision": "9bb4109d579bd746d39bd093c850b203"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/ug.js",
  "revision": "9f92226887f9ad02428732844e92fcd2"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/uk.js",
  "revision": "e90d16420b20027a8c882499630b046f"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/vi.js",
  "revision": "4f6cc5bf09c491223482b7d71d945f12"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/zh-cn.js",
  "revision": "6ec54c26306a2c1815e91ac7e3e1dca8"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/dialogs/lang/zh.js",
  "revision": "28944d626fa02b95a2f99307ade00e09"
}, {
  "url": "include/ckeditor/plugins/a11yhelp/lang/en.js",
  "revision": "e7e238d9e4ad38e663fe99ead9c19f80"
}, {
  "url": "include/ckeditor/plugins/about/dialogs/about.js",
  "revision": "1a3a3b27cd4c3c376600a330df359122"
}, {
  "url": "include/ckeditor/plugins/about/dialogs/hidpi/logo_ckeditor.png",
  "revision": "6318d2b6f7fc79b4ed0404ffbc2dac1e"
}, {
  "url": "include/ckeditor/plugins/about/dialogs/logo_ckeditor.png",
  "revision": "70dd831c761a20467a6ba9e5ae736f91"
}, {
  "url": "include/ckeditor/plugins/clipboard/dialogs/paste.js",
  "revision": "d6cab4f06e855d651de6b232a00bf8bd"
}, {
  "url": "include/ckeditor/plugins/colordialog/dialogs/colordialog.js",
  "revision": "01fe01366fe59d5247fd6e4095d65fbd"
}, {
  "url": "include/ckeditor/plugins/dialog/dialogDefinition.js",
  "revision": "9083322f743544942de24acaa732cb05"
}, {
  "url": "include/ckeditor/plugins/div/dialogs/div.js",
  "revision": "574ec1e97ae1e74720ae4f746a6af29a"
}, {
  "url": "include/ckeditor/plugins/fakeobjects/images/spacer.gif",
  "revision": "df3e567d6f16d040326c7a0ea29a4f41"
}, {
  "url": "include/ckeditor/plugins/find/dialogs/find.js",
  "revision": "7b6c648619e4239fe8e23909a89aa657"
}, {
  "url": "include/ckeditor/plugins/flash/dialogs/flash.js",
  "revision": "417ba136b89581f0474fc79f91f72ce8"
}, {
  "url": "include/ckeditor/plugins/flash/images/placeholder.png",
  "revision": "e9ac9384237d8d1cdaab68d31a22005d"
}, {
  "url": "include/ckeditor/plugins/forms/dialogs/button.js",
  "revision": "805085df88d3a01ea89816a203119337"
}, {
  "url": "include/ckeditor/plugins/forms/dialogs/checkbox.js",
  "revision": "5968b1bdff8181080fafb04e16d312fa"
}, {
  "url": "include/ckeditor/plugins/forms/dialogs/form.js",
  "revision": "a01c8011353066fae9d7583ec9d3c363"
}, {
  "url": "include/ckeditor/plugins/forms/dialogs/hiddenfield.js",
  "revision": "0eaa5504a3a7c2fbf0e82b8e05aad8b2"
}, {
  "url": "include/ckeditor/plugins/forms/dialogs/radio.js",
  "revision": "3c8b933e734d1febdd5753521718e96e"
}, {
  "url": "include/ckeditor/plugins/forms/dialogs/select.js",
  "revision": "c707a4d77fe37d18e2b91433b996f93d"
}, {
  "url": "include/ckeditor/plugins/forms/dialogs/textarea.js",
  "revision": "ac76075932fc24d952d7e46e5ca038d2"
}, {
  "url": "include/ckeditor/plugins/forms/dialogs/textfield.js",
  "revision": "20d91bc5a44c8910f7c2e8ac1923ab87"
}, {
  "url": "include/ckeditor/plugins/forms/images/hiddenfield.gif",
  "revision": "6e7765b0483daffb25f2b7bf5098e0d9"
}, {
  "url": "include/ckeditor/plugins/icons_hidpi.png",
  "revision": "16af6f6d04e4cd2180690cfcab4a7f9d"
}, {
  "url": "include/ckeditor/plugins/icons.png",
  "revision": "e1e5259ef4d132ac5cb3655a4f41cf95"
}, {
  "url": "include/ckeditor/plugins/iframe/dialogs/iframe.js",
  "revision": "1a210f09025841007c1c5cdf61db2731"
}, {
  "url": "include/ckeditor/plugins/iframe/images/placeholder.png",
  "revision": "a5ab5364efc6f7cea525e76a7bb619ae"
}, {
  "url": "include/ckeditor/plugins/iframedialog/plugin.js",
  "revision": "a0be666d0b8efdc9be16dd29ca23dc3e"
}, {
  "url": "include/ckeditor/plugins/image/dialogs/image.js",
  "revision": "66da3f2e5bfa417837f2d4030935be3f"
}, {
  "url": "include/ckeditor/plugins/image/images/noimage.png",
  "revision": "3eed23f5021065a8351126936bbe1e95"
}, {
  "url": "include/ckeditor/plugins/link/dialogs/anchor.js",
  "revision": "b217b01fd0802ef49daf0db0f9facf34"
}, {
  "url": "include/ckeditor/plugins/link/dialogs/link.js",
  "revision": "d0ef3ba60976b556710f37b7fccb9844"
}, {
  "url": "include/ckeditor/plugins/link/images/anchor.gif",
  "revision": "60a2121d55f9238f529458ee5f2e6e4e"
}, {
  "url": "include/ckeditor/plugins/link/images/anchor.png",
  "revision": "c23e1c6b52f6ca6678b77f38fef61789"
}, {
  "url": "include/ckeditor/plugins/link/images/hidpi/anchor.png",
  "revision": "9df1a4e40cabf35907a16ea59f3f9df1"
}, {
  "url": "include/ckeditor/plugins/liststyle/dialogs/liststyle.js",
  "revision": "740bcbdf7333ce748cd92dc6fcacd5a8"
}, {
  "url": "include/ckeditor/plugins/magicline/images/hidpi/icon-rtl.png",
  "revision": "b37d0404583c0ac273a27873451c3234"
}, {
  "url": "include/ckeditor/plugins/magicline/images/hidpi/icon.png",
  "revision": "5ba2e7b6aa50c7843ae9ca01ce08b606"
}, {
  "url": "include/ckeditor/plugins/magicline/images/icon-rtl.png",
  "revision": "a29eda8cd2b1ebcbd3379654acebfb85"
}, {
  "url": "include/ckeditor/plugins/magicline/images/icon.png",
  "revision": "baf6974c98b636142c7b0b5ba19bd96c"
}, {
  "url": "include/ckeditor/plugins/markdown/css/codemirror.min.css",
  "revision": "e7b6c35da3a02c05fbd75748b4674c93"
}, {
  "url": "include/ckeditor/plugins/markdown/icons/hidpi/markdown.png",
  "revision": "07a325c84b4a1698fbd6725862d97ed6"
}, {
  "url": "include/ckeditor/plugins/markdown/icons/markdown.png",
  "revision": "4e353c073dcf2e931b6eedee9d43ca09"
}, {
  "url": "include/ckeditor/plugins/markdown/js/codemirror-gfm-min.js",
  "revision": "fc5507436d1979168e19568e52f4a065"
}, {
  "url": "include/ckeditor/plugins/markdown/js/marked.js",
  "revision": "4e8365c19f29ed6b00310ba62a7816df"
}, {
  "url": "include/ckeditor/plugins/markdown/js/to-markdown.js",
  "revision": "8134cb0fdc13bc9df4a934a3bd3958e6"
}, {
  "url": "include/ckeditor/plugins/markdown/plugin.js",
  "revision": "2358b315ea5a22e19af7f9a632e7b23b"
}, {
  "url": "include/ckeditor/plugins/markdown/theme/3024-day.css",
  "revision": "68406c1477a4cb1b7ae9dc51be92a486"
}, {
  "url": "include/ckeditor/plugins/markdown/theme/3024-night.css",
  "revision": "90a9b887c1aaea63c629bdce48f95230"
}, {
  "url": "include/ckeditor/plugins/markdown/theme/ambiance-mobile.css",
  "revision": "256f2dd130b80c6afaa40fddf700d12a"
}, {
  "url": "include/ckeditor/plugins/markdown/theme/ambiance.css",
  "revision": "c65e357d96162daabe78bca2dbdce79c"
}, {
  "url": "include/ckeditor/plugins/markdown/theme/base16-dark.css",
  "revision": "bce9dddb84941d09a75dd3797a5dc11a"
}, {
  "url": "include/ckeditor/plugins/markdown/theme/base16-light.css",
  "revision": "38ded826fdb13e8fad57bc58553b96e3"
}, {
  "url": "include/ckeditor/plugins/markdown/theme/blackboard.css",
  "revision": "cf7fadda1ebdb98bbdc9c3144ec5894e"
}, {
  "url": "include/ckeditor/plugins/markdown/theme/cobalt.css",
  "revision": "7ac99f19422299b4d0ff8535556b94f8"
}, {
  "url": "include/ckeditor/plugins/markdown/theme/eclipse.css",
  "revision": "7c2f7b4b44b33fc9a5f857f542d007ac"
}, {
  "url": "include/ckeditor/plugins/markdown/theme/elegant.css",
  "revision": "c98914a034be0b11803bd3c24fba25dd"
}, {
  "url": "include/ckeditor/plugins/markdown/theme/erlang-dark.css",
  "revision": "75398b59ceeed0bba76357d6395a0018"
}, {
  "url": "include/ckeditor/plugins/markdown/theme/lesser-dark.css",
  "revision": "8cded6b0441648f1964788f80d944753"
}, {
  "url": "include/ckeditor/plugins/markdown/theme/mbo.css",
  "revision": "6ca14e2533afc4d47b697f199ce4cee4"
}, {
  "url": "include/ckeditor/plugins/markdown/theme/mdn-like.css",
  "revision": "770bc206c1fc62fe40e729b799380f66"
}, {
  "url": "include/ckeditor/plugins/markdown/theme/midnight.css",
  "revision": "96e728f928af79eb4c594c836c461db2"
}, {
  "url": "include/ckeditor/plugins/markdown/theme/monokai.css",
  "revision": "24b4f26461aa59004318db8561c2bdb6"
}, {
  "url": "include/ckeditor/plugins/markdown/theme/neat.css",
  "revision": "673552ecebac76569063801293e9c76c"
}, {
  "url": "include/ckeditor/plugins/markdown/theme/neo.css",
  "revision": "f65035d751bacec07f189e3477f50bda"
}, {
  "url": "include/ckeditor/plugins/markdown/theme/night.css",
  "revision": "25ac42da92cb242ce365efe6b34da645"
}, {
  "url": "include/ckeditor/plugins/markdown/theme/paraiso-dark.css",
  "revision": "3e29c028e094d75b203945bcdccdf02e"
}, {
  "url": "include/ckeditor/plugins/markdown/theme/paraiso-light.css",
  "revision": "6c4ff0ddd6f3c25f2c1494fe7ec0ce55"
}, {
  "url": "include/ckeditor/plugins/markdown/theme/pastel-on-dark.css",
  "revision": "b9c0773d5747bb5deb0dc1194d3221d7"
}, {
  "url": "include/ckeditor/plugins/markdown/theme/rubyblue.css",
  "revision": "9912ce413e966aabe603573ab5bb0d83"
}, {
  "url": "include/ckeditor/plugins/markdown/theme/solarized.css",
  "revision": "1ac07f4d1544921fe5beec04c19ffe8b"
}, {
  "url": "include/ckeditor/plugins/markdown/theme/the-matrix.css",
  "revision": "cc9d5612106e040187f780d897786cef"
}, {
  "url": "include/ckeditor/plugins/markdown/theme/tomorrow-night-eighties.css",
  "revision": "e73b9d5d85f48ebe7f55a8245046f546"
}, {
  "url": "include/ckeditor/plugins/markdown/theme/twilight.css",
  "revision": "ae5dfb3ea25d320f6c15284c1a4145bd"
}, {
  "url": "include/ckeditor/plugins/markdown/theme/vibrant-ink.css",
  "revision": "596536b3f6ca3d80729fa943a40e1ccb"
}, {
  "url": "include/ckeditor/plugins/markdown/theme/xq-dark.css",
  "revision": "1b5bb146d6fcc235103072589a347cc8"
}, {
  "url": "include/ckeditor/plugins/markdown/theme/xq-light.css",
  "revision": "481023ea9d2e1d4c1707a1867c500326"
}, {
  "url": "include/ckeditor/plugins/pagebreak/images/pagebreak.gif",
  "revision": "05dcfa6e3332b3ab7ac9218bf420cb58"
}, {
  "url": "include/ckeditor/plugins/pastefromword/filter/default.js",
  "revision": "0d9bb38c0b4fef01beb354de7964e9db"
}, {
  "url": "include/ckeditor/plugins/pastetext/dialogs/pastetext.js",
  "revision": "c889dda445f8c70cdfb5e6be15fb3d19"
}, {
  "url": "include/ckeditor/plugins/scayt/dialogs/options.js",
  "revision": "a56ca25171107cbb0b71f73c93636769"
}, {
  "url": "include/ckeditor/plugins/scayt/dialogs/toolbar.css",
  "revision": "abb7173bc76c982641101d81cc544ab0"
}, {
  "url": "include/ckeditor/plugins/showblocks/images/block_address.png",
  "revision": "9bec74c765f8a0938f50875912c07282"
}, {
  "url": "include/ckeditor/plugins/showblocks/images/block_blockquote.png",
  "revision": "6a75769ebc3efc29bea72ca39f2706d5"
}, {
  "url": "include/ckeditor/plugins/showblocks/images/block_div.png",
  "revision": "245b9fa9b31d1a230be294a4824ebc2a"
}, {
  "url": "include/ckeditor/plugins/showblocks/images/block_h1.png",
  "revision": "9c7fce3d77cc205e7bd2a52043ea93e3"
}, {
  "url": "include/ckeditor/plugins/showblocks/images/block_h2.png",
  "revision": "23e0bd942da90db8f0d1f02de9c102df"
}, {
  "url": "include/ckeditor/plugins/showblocks/images/block_h3.png",
  "revision": "e46278c31f23cea32eec3cdeaf4fd344"
}, {
  "url": "include/ckeditor/plugins/showblocks/images/block_h4.png",
  "revision": "e7f71965bd30638bdb845e46bb996487"
}, {
  "url": "include/ckeditor/plugins/showblocks/images/block_h5.png",
  "revision": "4eb09981f4bd28f37cb01ffde72937bd"
}, {
  "url": "include/ckeditor/plugins/showblocks/images/block_h6.png",
  "revision": "c59baac0a87734e16b44cdbac4fa5429"
}, {
  "url": "include/ckeditor/plugins/showblocks/images/block_p.png",
  "revision": "c3a4ca41007690fb063166eb94c6c40a"
}, {
  "url": "include/ckeditor/plugins/showblocks/images/block_pre.png",
  "revision": "2dd09308dc4573029ded1030ecea1a66"
}, {
  "url": "include/ckeditor/plugins/smiley/dialogs/smiley.js",
  "revision": "156cbc1b9467a2810a80caff0288315c"
}, {
  "url": "include/ckeditor/plugins/smiley/images/angel_smile.gif",
  "revision": "eb0d289bc2b6cf81cdcb3d172de01be3"
}, {
  "url": "include/ckeditor/plugins/smiley/images/angel_smile.png",
  "revision": "35de693f510be6092087e76fbd8d4858"
}, {
  "url": "include/ckeditor/plugins/smiley/images/angry_smile.gif",
  "revision": "01f7bf4165ed0ea9c575047512e6b254"
}, {
  "url": "include/ckeditor/plugins/smiley/images/angry_smile.png",
  "revision": "b1b142807ce4bb8784c5291e66c77e59"
}, {
  "url": "include/ckeditor/plugins/smiley/images/broken_heart.gif",
  "revision": "80bd5b8b6d380de82de62caacfdc5c31"
}, {
  "url": "include/ckeditor/plugins/smiley/images/broken_heart.png",
  "revision": "54051abe9b11365442eb133431055e4e"
}, {
  "url": "include/ckeditor/plugins/smiley/images/confused_smile.gif",
  "revision": "2c0fac96ca9ffc7946345f6bbb3f756f"
}, {
  "url": "include/ckeditor/plugins/smiley/images/confused_smile.png",
  "revision": "71ddba0809eaf8772a4f959c476dfd45"
}, {
  "url": "include/ckeditor/plugins/smiley/images/cry_smile.gif",
  "revision": "14eaed2d73022fca3bebfae0052b0c6b"
}, {
  "url": "include/ckeditor/plugins/smiley/images/cry_smile.png",
  "revision": "9f8eedc515716b59ffb31e0975ed70c6"
}, {
  "url": "include/ckeditor/plugins/smiley/images/devil_smile.gif",
  "revision": "e9421d09d8e14616be9571c92125933c"
}, {
  "url": "include/ckeditor/plugins/smiley/images/devil_smile.png",
  "revision": "9ebcc5258594dea600706f079ca84b48"
}, {
  "url": "include/ckeditor/plugins/smiley/images/embaressed_smile.gif",
  "revision": "666d0000b06a5dd44693b2d3ced7f547"
}, {
  "url": "include/ckeditor/plugins/smiley/images/embarrassed_smile.gif",
  "revision": "666d0000b06a5dd44693b2d3ced7f547"
}, {
  "url": "include/ckeditor/plugins/smiley/images/embarrassed_smile.png",
  "revision": "b99d286c8d3da9f3e91207bc6c829233"
}, {
  "url": "include/ckeditor/plugins/smiley/images/envelope.gif",
  "revision": "1448c4f72550074a49132c2895dafc4f"
}, {
  "url": "include/ckeditor/plugins/smiley/images/envelope.png",
  "revision": "579ad38a28eb7aa15daf8751a81ab246"
}, {
  "url": "include/ckeditor/plugins/smiley/images/heart.gif",
  "revision": "140f63f60c8cbdd8b54c10a43272c623"
}, {
  "url": "include/ckeditor/plugins/smiley/images/heart.png",
  "revision": "818362c20066b60184a5a0e8187baa79"
}, {
  "url": "include/ckeditor/plugins/smiley/images/kiss.gif",
  "revision": "5647a7d8a3f0e1e1536ce4156f5c2e25"
}, {
  "url": "include/ckeditor/plugins/smiley/images/kiss.png",
  "revision": "9615f97979a3674603cfd03bfeca451f"
}, {
  "url": "include/ckeditor/plugins/smiley/images/lightbulb.gif",
  "revision": "30d7063a64990b3b4c02566b4caa82e9"
}, {
  "url": "include/ckeditor/plugins/smiley/images/lightbulb.png",
  "revision": "952ab995f4cf77d7686d0ec853d2f232"
}, {
  "url": "include/ckeditor/plugins/smiley/images/omg_smile.gif",
  "revision": "23f1297b1e0bf882f47c2e7f99c5be7c"
}, {
  "url": "include/ckeditor/plugins/smiley/images/omg_smile.png",
  "revision": "10b2eb3edfab4bf94357bf8578f24377"
}, {
  "url": "include/ckeditor/plugins/smiley/images/regular_smile.gif",
  "revision": "d2eec284220e320bf730c56a1ac599e5"
}, {
  "url": "include/ckeditor/plugins/smiley/images/regular_smile.png",
  "revision": "bc23f5aef97ef9f12ac3b0d49bcd8afb"
}, {
  "url": "include/ckeditor/plugins/smiley/images/sad_smile.gif",
  "revision": "00185a83031165eee6389f74aefde902"
}, {
  "url": "include/ckeditor/plugins/smiley/images/sad_smile.png",
  "revision": "937e65674e30bd0f026d7260df698dc6"
}, {
  "url": "include/ckeditor/plugins/smiley/images/shades_smile.gif",
  "revision": "5adc692cc4db4637563136033890692b"
}, {
  "url": "include/ckeditor/plugins/smiley/images/shades_smile.png",
  "revision": "d25c5ca52217e776f33a709833f0cbfd"
}, {
  "url": "include/ckeditor/plugins/smiley/images/teeth_smile.gif",
  "revision": "98f94c05a790e302b74cfc8a02436571"
}, {
  "url": "include/ckeditor/plugins/smiley/images/teeth_smile.png",
  "revision": "82d6f950227d76aded2600aceac80f67"
}, {
  "url": "include/ckeditor/plugins/smiley/images/thumbs_down.gif",
  "revision": "b372f9ed85d5312d45a16b90e94f38f7"
}, {
  "url": "include/ckeditor/plugins/smiley/images/thumbs_down.png",
  "revision": "b2d9c5d63108c03b6ac62c1ae49c52d2"
}, {
  "url": "include/ckeditor/plugins/smiley/images/thumbs_up.gif",
  "revision": "aa9b9c654637e4416f6fa04a58a8f614"
}, {
  "url": "include/ckeditor/plugins/smiley/images/thumbs_up.png",
  "revision": "bb6ce02a0a423ef270217de51374f107"
}, {
  "url": "include/ckeditor/plugins/smiley/images/tongue_smile.gif",
  "revision": "1bea0b1184b1e5c3940ec8c5d6e81f86"
}, {
  "url": "include/ckeditor/plugins/smiley/images/tongue_smile.png",
  "revision": "d80a35ee23e3ee9cb6d32372a3182e39"
}, {
  "url": "include/ckeditor/plugins/smiley/images/tounge_smile.gif",
  "revision": "1bea0b1184b1e5c3940ec8c5d6e81f86"
}, {
  "url": "include/ckeditor/plugins/smiley/images/whatchutalkingabout_smile.gif",
  "revision": "381881cfa2765138a4c2e7f3da56bced"
}, {
  "url": "include/ckeditor/plugins/smiley/images/whatchutalkingabout_smile.png",
  "revision": "6e562cb0be0aa525d9a5b8b23759d4e4"
}, {
  "url": "include/ckeditor/plugins/smiley/images/wink_smile.gif",
  "revision": "1aab746a15472e6e4675369158ffb420"
}, {
  "url": "include/ckeditor/plugins/smiley/images/wink_smile.png",
  "revision": "9a5c2bebf35175e98a54c7edb62ae3cd"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/af.js",
  "revision": "71479f26d18da271a5251c1339dbc102"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/ar.js",
  "revision": "242e3d1c669cde291c2c0ec1a2ca81b5"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/bg.js",
  "revision": "267cc43f587c3b514e3bfa76f852613b"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/ca.js",
  "revision": "c1405614da9b8c106ca93dfcb1c4ee4c"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/cs.js",
  "revision": "1e3ca4eb94ef05bac32e4fce7f3dffcb"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/cy.js",
  "revision": "a19226091d8c0722657dc82152849ad5"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/da.js",
  "revision": "f04bab6366652883e229078aa3f97d72"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/de-ch.js",
  "revision": "ad2f02e0e8c2790560de0bb848604bc2"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/de.js",
  "revision": "7de71236111561aad3cb4c05e6040d6d"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/el.js",
  "revision": "105f389f4a77e279deeb38281f499a7c"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/en-gb.js",
  "revision": "fa9ed9233d66865b8730ca23a564fada"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/en.js",
  "revision": "64d91a1320d6dd6309d911524f4274c5"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/eo.js",
  "revision": "e3df19977f054dba444ff5d7d679f38e"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/es.js",
  "revision": "f9a1866732229f7a716dfbe973f9475f"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/et.js",
  "revision": "b809e64d03f58ce863de23092211ee93"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/eu.js",
  "revision": "5ab2dd603f22810a649922660b239275"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/fa.js",
  "revision": "c5501ff684654c809c1dcf081ec4b047"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/fi.js",
  "revision": "9962779efbd245da05bf64f48f9c4964"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/fr-ca.js",
  "revision": "e341518a81cdbbcb43921514f50060e9"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/fr.js",
  "revision": "8656042b9e548541b0a50c4b4c3413f0"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/gl.js",
  "revision": "a040710cdf41b2e65f70d1f050dbfcb3"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/he.js",
  "revision": "d963066572ca7c196469b8e293f9a9c9"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/hr.js",
  "revision": "2569616cba41d645817fdf217056d808"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/hu.js",
  "revision": "19f2c26e6b945c667f1697a0083ec82f"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/id.js",
  "revision": "c4e8a9e851fd7452672eee08b6247008"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/it.js",
  "revision": "0bb5416f936da3ca7f7d7c7073fe8797"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/ja.js",
  "revision": "e6ad5309e1cbc000d32b088587cfaeb0"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/km.js",
  "revision": "f155ab50c0dd87b2fe10620608e7792f"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/ko.js",
  "revision": "98ceacf77d2f8596e1d093323c2beb46"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/ku.js",
  "revision": "bdb43db60fa0d95dd2ceb87cac6480c1"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/lt.js",
  "revision": "6332821a9fd06d54db32a20528f987ad"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/lv.js",
  "revision": "30f4b6f543dd7535b0b351585ff6e835"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/nb.js",
  "revision": "14ac8c2a4964ecd5a3d01f3e4c078ed1"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/nl.js",
  "revision": "45aa3587f4d7f06be783c3a60e2655be"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/no.js",
  "revision": "2968ac3f78688849ceae5dce2aa9170b"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/pl.js",
  "revision": "07d2f8ffcc3b335bcb4b0f87281466e7"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/pt-br.js",
  "revision": "80cebdc4c6de643f25f747cef54259c6"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/pt.js",
  "revision": "106ab2d7670770e848c8fb7a3593cb59"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/ru.js",
  "revision": "0381daa5e893e2d23f2aa18b8d54791e"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/si.js",
  "revision": "c7f4759e7746b42f52576d120ce9c110"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/sk.js",
  "revision": "7ba061095626489d7ff71b184e8d9392"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/sl.js",
  "revision": "cced0c682e7357fdd019e8a2b8955b7e"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/sq.js",
  "revision": "69b7ae0cbaac040db832a45d5a8bbfdc"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/sv.js",
  "revision": "462e8b53b50756055be48182598b7152"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/th.js",
  "revision": "87a660a76a82b8a02378814598917316"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/tr.js",
  "revision": "6c52016576fee6b4f1df73fa9358a3fb"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/tt.js",
  "revision": "4c11ae70558865c4fb8e212832a0b531"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/ug.js",
  "revision": "ff9ff87722657daa79c568a92cbccb60"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/uk.js",
  "revision": "0e70ad688b02c8dcf03282f984ad0ca8"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/vi.js",
  "revision": "24525eba46f6c3e90b6353f58d7c56b5"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/zh-cn.js",
  "revision": "88cb0cad1971353eaf714fc5fb2971ab"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/lang/zh.js",
  "revision": "3ab3180d28689735b6d15ecdd8be2ff1"
}, {
  "url": "include/ckeditor/plugins/specialchar/dialogs/specialchar.js",
  "revision": "9c37cab95ec0ed9d4c99170e3ea62e77"
}, {
  "url": "include/ckeditor/plugins/styles/styles/default.js",
  "revision": "c050e18279257bd47e06293119f96f94"
}, {
  "url": "include/ckeditor/plugins/table/dialogs/table.js",
  "revision": "323208d23b11e838078ac418d7af15f6"
}, {
  "url": "include/ckeditor/plugins/tabletools/dialogs/tableCell.js",
  "revision": "5ed56499c4458e34fe44e0f8815ec011"
}, {
  "url": "include/ckeditor/plugins/templates/dialogs/templates.css",
  "revision": "c0895a50e0b3648de77fce7af4664c1b"
}, {
  "url": "include/ckeditor/plugins/templates/dialogs/templates.js",
  "revision": "91f3edf0ad84c5ff359083a6ce381544"
}, {
  "url": "include/ckeditor/plugins/templates/templates/default.js",
  "revision": "6e83568f3898f1a8f35cfdf927becd7e"
}, {
  "url": "include/ckeditor/plugins/templates/templates/images/template1.gif",
  "revision": "fc667c4366fe133c30ab122fe2ee7f20"
}, {
  "url": "include/ckeditor/plugins/templates/templates/images/template2.gif",
  "revision": "8a4d45166ebeef73e222270a8113d66f"
}, {
  "url": "include/ckeditor/plugins/templates/templates/images/template3.gif",
  "revision": "b8650f06582ac88ece68948bac1bf734"
}, {
  "url": "include/ckeditor/plugins/uicolor/dialogs/uicolor.js",
  "revision": "59a3c518ef6b28036ab887196c462525"
}, {
  "url": "include/ckeditor/plugins/uicolor/lang/en.js",
  "revision": "1c6d5616881f101039fcc13fb4c8cfc8"
}, {
  "url": "include/ckeditor/plugins/uicolor/plugin.js",
  "revision": "973fab87a5ab79cc5c16460c3cad6c52"
}, {
  "url": "include/ckeditor/plugins/uicolor/uicolor.gif",
  "revision": "83be344f9d97ac22ccfb90a07d128b04"
}, {
  "url": "include/ckeditor/plugins/uicolor/yui/assets/hue_bg.png",
  "revision": "85c2fc9570abba39f203039c948c5779"
}, {
  "url": "include/ckeditor/plugins/uicolor/yui/assets/hue_thumb.png",
  "revision": "8dfa46d350f447a5b09e1e3f6e6dc7b4"
}, {
  "url": "include/ckeditor/plugins/uicolor/yui/assets/picker_mask.png",
  "revision": "3193f0b5f7bd03ec403b9466148622c2"
}, {
  "url": "include/ckeditor/plugins/uicolor/yui/assets/picker_thumb.png",
  "revision": "d806b36442a90e313b76138ce0d1eabf"
}, {
  "url": "include/ckeditor/plugins/uicolor/yui/assets/yui.css",
  "revision": "cff6d0aae50e3b59ba3d29c3dbeb5849"
}, {
  "url": "include/ckeditor/plugins/uicolor/yui/yui.js",
  "revision": "71783bfee52c7f4433db88c20ca50441"
}, {
  "url": "include/ckeditor/plugins/wsc/dialogs/wsc_ie.js",
  "revision": "47e6654b545a57f00589137476a68fdc"
}, {
  "url": "include/ckeditor/plugins/wsc/dialogs/wsc.css",
  "revision": "9f63e9dd90b207fdf884bb6e8b5dfbaf"
}, {
  "url": "include/ckeditor/plugins/wsc/dialogs/wsc.js",
  "revision": "a326fbeed01112e6a2a905f5f5c33792"
}, {
  "url": "include/ckeditor/skins/kama/dialog.css",
  "revision": "ab9c6317743da7bc97e314770529157a"
}, {
  "url": "include/ckeditor/skins/kama/editor.css",
  "revision": "0a1f1ef636d0eea6bfae512c39077618"
}, {
  "url": "include/ckeditor/skins/kama/icons.png",
  "revision": "6f5de214381b2850c8fc61ac8cce4e05"
}, {
  "url": "include/ckeditor/skins/kama/images/dialog_sides_rtl.png",
  "revision": "e91e6dbf0680e420d5b118dbe8ac328e"
}, {
  "url": "include/ckeditor/skins/kama/images/dialog_sides.gif",
  "revision": "120d7ec1d25b985eeaa6b8e33cfe532b"
}, {
  "url": "include/ckeditor/skins/kama/images/dialog_sides.png",
  "revision": "7b4484847ba0490140a20e8021d50031"
}, {
  "url": "include/ckeditor/skins/kama/images/mini.gif",
  "revision": "44047e297a6b8de4c228e763b2fcd89a"
}, {
  "url": "include/ckeditor/skins/kama/images/noimage.png",
  "revision": "1c5c947a5325e0946a32bc33261fe22f"
}, {
  "url": "include/ckeditor/skins/kama/images/sprites_ie6.png",
  "revision": "25955ee66bc4753bef361c33519a588e"
}, {
  "url": "include/ckeditor/skins/kama/images/sprites.png",
  "revision": "f559dc4a2764a4fb4da397f87883adcf"
}, {
  "url": "include/ckeditor/skins/kama/images/toolbar_start.gif",
  "revision": "7ed13749e9da48abea49e9b22543120b"
}, {
  "url": "include/ckeditor/skins/kama/skin.js",
  "revision": "9920fd41362befb7163f36c068c7447a"
}, {
  "url": "include/ckeditor/skins/kama/templates.css",
  "revision": "57583f1dcc78ae6972a5e51fac088482"
}, {
  "url": "include/ckeditor/skins/moono/dialog_ie.css",
  "revision": "2ce5cadec903da77f56fd93489758db1"
}, {
  "url": "include/ckeditor/skins/moono/dialog_ie7.css",
  "revision": "ea4e7c0149a1ca6c101105072519e262"
}, {
  "url": "include/ckeditor/skins/moono/dialog_ie8.css",
  "revision": "c2c250f64f7a4414947c9699c3df60ee"
}, {
  "url": "include/ckeditor/skins/moono/dialog_iequirks.css",
  "revision": "2102ff4c45282f2a6981713a11335566"
}, {
  "url": "include/ckeditor/skins/moono/dialog_opera.css",
  "revision": "250fef2d6f64ed9113a3f1c82b969057"
}, {
  "url": "include/ckeditor/skins/moono/dialog.css",
  "revision": "aa427e03c64e38df1b9b568fdab8e6eb"
}, {
  "url": "include/ckeditor/skins/moono/editor_gecko.css",
  "revision": "1d01944cb45572938c8bab3d8fd9ff23"
}, {
  "url": "include/ckeditor/skins/moono/editor_ie.css",
  "revision": "55560a85a47811d6929fd2e48bb1ae85"
}, {
  "url": "include/ckeditor/skins/moono/editor_ie7.css",
  "revision": "355bc353d132eac2cfe304e640bc662b"
}, {
  "url": "include/ckeditor/skins/moono/editor_ie8.css",
  "revision": "2dfd70714fd314f1f0c5f56880c020ea"
}, {
  "url": "include/ckeditor/skins/moono/editor_iequirks.css",
  "revision": "f22e4b002363ed22e1b67062d1b66779"
}, {
  "url": "include/ckeditor/skins/moono/editor.css",
  "revision": "ba10052c65982f87092fdeba85f59b50"
}, {
  "url": "include/ckeditor/skins/moono/icons_hidpi.png",
  "revision": "16af6f6d04e4cd2180690cfcab4a7f9d"
}, {
  "url": "include/ckeditor/skins/moono/icons.png",
  "revision": "e1e5259ef4d132ac5cb3655a4f41cf95"
}, {
  "url": "include/ckeditor/skins/moono/images/arrow.png",
  "revision": "5b9854a7f865788fff62fe32b0324ca0"
}, {
  "url": "include/ckeditor/skins/moono/images/close.png",
  "revision": "9b497b65c0909aa80b21aa989363a0bb"
}, {
  "url": "include/ckeditor/skins/moono/images/hidpi/close.png",
  "revision": "cd269135b1c31c9044974c3d17059b04"
}, {
  "url": "include/ckeditor/skins/moono/images/hidpi/lock-open.png",
  "revision": "4f6b9606513757e04d4de3268a123eb7"
}, {
  "url": "include/ckeditor/skins/moono/images/hidpi/lock.png",
  "revision": "f6cf4b23d39107db8aaf907f686a0052"
}, {
  "url": "include/ckeditor/skins/moono/images/hidpi/refresh.png",
  "revision": "33ebeddcb7b69137ffbfca121b0f6213"
}, {
  "url": "include/ckeditor/skins/moono/images/lock-open.png",
  "revision": "e9dff089035fee4ac979a340ef8d4fcf"
}, {
  "url": "include/ckeditor/skins/moono/images/lock.png",
  "revision": "68f4c2f5309e4dbc0f98c4be79dc66c7"
}, {
  "url": "include/ckeditor/skins/moono/images/mini.png",
  "revision": "94486bdf738306e36bf7d144f4268f84"
}, {
  "url": "include/ckeditor/skins/moono/images/refresh.png",
  "revision": "0f54df868f75482f99157807f6f68ee0"
}, {
  "url": "include/ckeditor/skins/moono/images/spinner.gif",
  "revision": "7f32b6e67f42a0ef3e1ddb0b9401f6c5"
}, {
  "url": "include/ckeditor/skins/office2003/dialog.css",
  "revision": "8b946c91fb5e37e3a08632925aa520c7"
}, {
  "url": "include/ckeditor/skins/office2003/editor.css",
  "revision": "4d5e94c57913c94832e756bcc8faa14e"
}, {
  "url": "include/ckeditor/skins/office2003/icons.png",
  "revision": "6f5de214381b2850c8fc61ac8cce4e05"
}, {
  "url": "include/ckeditor/skins/office2003/images/dialog_sides_rtl.png",
  "revision": "de1a31b0b8955a8b8895dd6011b752c8"
}, {
  "url": "include/ckeditor/skins/office2003/images/dialog_sides.gif",
  "revision": "0347236eef1ae590f8f4da8d88d050d3"
}, {
  "url": "include/ckeditor/skins/office2003/images/dialog_sides.png",
  "revision": "99cec08e2ff5d144662f2390eeeb344b"
}, {
  "url": "include/ckeditor/skins/office2003/images/mini.gif",
  "revision": "44047e297a6b8de4c228e763b2fcd89a"
}, {
  "url": "include/ckeditor/skins/office2003/images/noimage.png",
  "revision": "1c5c947a5325e0946a32bc33261fe22f"
}, {
  "url": "include/ckeditor/skins/office2003/images/sprites_ie6.png",
  "revision": "547a271da89b430cc11806ba9e00492a"
}, {
  "url": "include/ckeditor/skins/office2003/images/sprites.png",
  "revision": "24714a341e673b22797953b1f6c40037"
}, {
  "url": "include/ckeditor/skins/office2003/skin.js",
  "revision": "5131f5f8fca63766062d8066b3c42b33"
}, {
  "url": "include/ckeditor/skins/office2003/templates.css",
  "revision": "1895f261fecaba4c9b2e3939aa89a8a2"
}, {
  "url": "include/ckeditor/skins/v2/dialog.css",
  "revision": "3e8978652d921f40f5035300242d5e29"
}, {
  "url": "include/ckeditor/skins/v2/editor.css",
  "revision": "73763bfc77e646de13db8cb1b5d08ead"
}, {
  "url": "include/ckeditor/skins/v2/icons.png",
  "revision": "6f5de214381b2850c8fc61ac8cce4e05"
}, {
  "url": "include/ckeditor/skins/v2/images/dialog_sides_rtl.png",
  "revision": "e91e6dbf0680e420d5b118dbe8ac328e"
}, {
  "url": "include/ckeditor/skins/v2/images/dialog_sides.gif",
  "revision": "120d7ec1d25b985eeaa6b8e33cfe532b"
}, {
  "url": "include/ckeditor/skins/v2/images/dialog_sides.png",
  "revision": "7b4484847ba0490140a20e8021d50031"
}, {
  "url": "include/ckeditor/skins/v2/images/mini.gif",
  "revision": "44047e297a6b8de4c228e763b2fcd89a"
}, {
  "url": "include/ckeditor/skins/v2/images/noimage.png",
  "revision": "1c5c947a5325e0946a32bc33261fe22f"
}, {
  "url": "include/ckeditor/skins/v2/images/sprites_ie6.png",
  "revision": "f8734fb1c22b17417e48ab7d8e5e05c0"
}, {
  "url": "include/ckeditor/skins/v2/images/sprites.png",
  "revision": "30e053624aee547b168af65a5aaed5bf"
}, {
  "url": "include/ckeditor/skins/v2/images/toolbar_start.gif",
  "revision": "7ed13749e9da48abea49e9b22543120b"
}, {
  "url": "include/ckeditor/skins/v2/skin.js",
  "revision": "aef2dec3b2c15d74388eabfb73abd95f"
}, {
  "url": "include/ckeditor/skins/v2/templates.css",
  "revision": "0898636a85ffdb0425f28dfc5459e0e1"
}, {
  "url": "include/ckeditor/themes/default/theme.js",
  "revision": "e72c3603b61fdfc673dc40b943724494"
}, {
  "url": "include/ckeditor/build-config.js",
  "revision": "e52e28c8c917d705929bd9e9e250ff54"
}, {
  "url": "include/ckeditor/ckeditor_basic_source.js",
  "revision": "096da3b66c5ae820810315e6843e4883"
}, {
  "url": "include/ckeditor/ckeditor_basic.js",
  "revision": "1a4c2d2cd8aa2778ac85e314a05d5640"
}, {
  "url": "include/ckeditor/ckeditor_source.js",
  "revision": "44165cdde8b0bf4101dbc1a5dae63c7c"
}, {
  "url": "include/ckeditor/ckeditor.js",
  "revision": "3c5d15bc77fb94797ad22446130e54a2"
}, {
  "url": "include/ckeditor/config_spellcheck.js",
  "revision": "1208d3cfef4fdcef120b5f0da8b9bf48"
}, {
  "url": "include/ckeditor/config.js",
  "revision": "5d2cc082f697602fa8913660b4cfcdc6"
}, {
  "url": "include/ckeditor/contents.css",
  "revision": "8a98d75ebe8e89a86e3eb722bc4958ca"
}, {
  "url": "include/ckeditor/styles.js",
  "revision": "36461354bd2b4245f83f86250d5b9dad"
}, {
  "url": "include/bunnyjs/ajax.min.js",
  "revision": "2029b517ce294867c673ccbeb4b51fb6"
}, {
  "url": "include/bunnyjs/api.min.js",
  "revision": "8ad974884a5943e36cb819496dbea0bb"
}, {
  "url": "include/bunnyjs/autocomplete.icons.min.js",
  "revision": "142390f3b7436ac6cc1316849138912b"
}, {
  "url": "include/bunnyjs/autocomplete.min.js",
  "revision": "9a4befac9319e5f52b96604d509a9890"
}, {
  "url": "include/bunnyjs/component.min.js",
  "revision": "c673e18a38ec56337d23ac1ba0fbe1f8"
}, {
  "url": "include/bunnyjs/core-helpers.min.js",
  "revision": "94aafe1e985e31d66565ef96a2cd23e2"
}, {
  "url": "include/bunnyjs/css/fade.css",
  "revision": "710d7b27d35263441cb8160e94033db5"
}, {
  "url": "include/bunnyjs/css/svg-icons.css",
  "revision": "34bae8c10bd58dc3e34f00bc039dd87d"
}, {
  "url": "include/bunnyjs/customselect.min.js",
  "revision": "8c1eafda5642fe0d2f38df66b8263c33"
}, {
  "url": "include/bunnyjs/datatable.icons.min.js",
  "revision": "d686a435a7c27e4ef3fb946f7e1b7c1a"
}, {
  "url": "include/bunnyjs/datatable.min.js",
  "revision": "bbd4566b6dc1b15244870876cfb8f066"
}, {
  "url": "include/bunnyjs/datatable.scrolltop.min.js",
  "revision": "8f9c366732c0cd6ea7ed8b4e4d494385"
}, {
  "url": "include/bunnyjs/date.min.js",
  "revision": "abd3441c461c3ad2693bf8ee365c29a1"
}, {
  "url": "include/bunnyjs/datepicker.min.js",
  "revision": "6a7d6737ace00ac060dfa82c439ea4a9"
}, {
  "url": "include/bunnyjs/dropdown.min.js",
  "revision": "7b04ae08d9e638918d1f401566ae8a75"
}, {
  "url": "include/bunnyjs/element.min.js",
  "revision": "a525d0b41dbc2c31ff7b4de6a3921f3b"
}, {
  "url": "include/bunnyjs/file.min.js",
  "revision": "b8eba89bdbacf51ab930af93897b9bce"
}, {
  "url": "include/bunnyjs/image-processor.min.js",
  "revision": "7bf8433ba456f650f59db6419eddcc97"
}, {
  "url": "include/bunnyjs/image-upload.min.js",
  "revision": "51a751a8de78e631e3530a315d378661"
}, {
  "url": "include/bunnyjs/image.min.js",
  "revision": "46137431845fbb082873ca715a8f26ed"
}, {
  "url": "include/bunnyjs/modal.min.js",
  "revision": "f0c6398cebf1f5a98680dca2e38e19a9"
}, {
  "url": "include/bunnyjs/normalize.min.js",
  "revision": "e897275ba28810cac0db7d44cee75ded"
}, {
  "url": "include/bunnyjs/notify.min.js",
  "revision": "1b8378505682c4b12fc6ee6081791460"
}, {
  "url": "include/bunnyjs/pagination.min.js",
  "revision": "de34123ebd5a33a5f60aa3480f9bccd1"
}, {
  "url": "include/bunnyjs/polyfill-array-from.min.js",
  "revision": "2923b3afb41872e425b9f8f87df1be69"
}, {
  "url": "include/bunnyjs/polyfill-fetch.min.js",
  "revision": "52aae16e223e161def5451f805fd8aa5"
}, {
  "url": "include/bunnyjs/polyfill-object-assign.min.js",
  "revision": "6018ae6b100151001ee113b5efa6c486"
}, {
  "url": "include/bunnyjs/polyfill-promise.min.js",
  "revision": "2db587acd482aa7bebcda1a96daf3b45"
}, {
  "url": "include/bunnyjs/polyfill-template.min.js",
  "revision": "79bca0401ba4d91a7df92164659e649c"
}, {
  "url": "include/bunnyjs/polyfills.min.js",
  "revision": "1b851aa95bb123015474db0e9e55f803"
}, {
  "url": "include/bunnyjs/route.min.js",
  "revision": "805bf87fb5c12f05e8e59531dbada208"
}, {
  "url": "include/bunnyjs/spinner.min.js",
  "revision": "9f54a6c1e97f5ae61652fed67b83ad38"
}, {
  "url": "include/bunnyjs/sprites.svg",
  "revision": "0b48192dd2f786878ce9c5b5e557c975"
}, {
  "url": "include/bunnyjs/svg/check.svg",
  "revision": "17ac532875e2931d1940cd9f1a1bbfdb"
}, {
  "url": "include/bunnyjs/svg/search.svg",
  "revision": "9df51d4e640dadfd9884b1a61c7b5642"
}, {
  "url": "include/bunnyjs/svg/spinner.svg",
  "revision": "f9eb5fc3c2e44f37ad8a79de44ff182d"
}, {
  "url": "include/bunnyjs/tabsection.min.js",
  "revision": "9717408591ba07ce9efe7e4142990e78"
}, {
  "url": "include/bunnyjs/template.min.js",
  "revision": "9f421b33d36d7c679368836db0350a37"
}, {
  "url": "include/bunnyjs/url.min.js",
  "revision": "0785fb5e7f89b051afaa7ff598886fbb"
}, {
  "url": "include/bunnyjs/utils-dom.min.js",
  "revision": "9c52a30a0ff3f06d926ecd58e922f0f0"
}, {
  "url": "include/bunnyjs/utils-string.min.js",
  "revision": "caacdd4f9e5e3466edd47b50c09c0d25"
}, {
  "url": "include/bunnyjs/utils-svg.min.js",
  "revision": "484edd16fb4c1afd9f284b6a5a6547d6"
}, {
  "url": "include/bunnyjs/validation.min.js",
  "revision": "6c3b63a761701b52fbd0b85c20a0aa98"
}, {
  "url": "include/dropzone/custom.css",
  "revision": "d6b63d223d69da7c7f169edbd6b9091d"
}, {
  "url": "include/dropzone/dropzone.css",
  "revision": "5e018ddcbacdc772d8ad3f48b8615657"
}, {
  "url": "include/dropzone/dropzone.js",
  "revision": "0885a777c714486ecdb35160f9045174"
}, {
  "url": "include/dropzone/upload_120.png",
  "revision": "2d2419776b214009a53a46d40915c4d3"
}, {
  "url": "include/dropzone/upload_32.png",
  "revision": "20ecfe994bcc1d1c4524eb9409976c68"
}, {
  "url": "include/images/announ.gif",
  "revision": "6ac2364bce104334b23b9652adb9315b"
}, {
  "url": "include/images/bgcolor.gif",
  "revision": "903015da7796376b5a9df5e16baa4370"
}, {
  "url": "include/images/blank.gif",
  "revision": "fc94fb0c3ed8a8f909dbc7630a0987ff"
}, {
  "url": "include/images/Blogs.png",
  "revision": "4cdf34a644f1ea4358432f43f5323b04"
}, {
  "url": "include/images/Facebook.png",
  "revision": "e4ac82cfe4f4593c1add6045f535747d"
}, {
  "url": "include/images/Forums.png",
  "revision": "518056c760dd96335d91af3daf6c71d4"
}, {
  "url": "include/images/installing.gif",
  "revision": "62572865757be59dc388808f7fe03dea"
}, {
  "url": "include/images/license.svg",
  "revision": "8cbd7c01ad2eff918b56f1fe69489e75"
}, {
  "url": "include/images/Linkedin.png",
  "revision": "148588e982b051aa5aa878077aed9211"
}, {
  "url": "include/images/Manuals.png",
  "revision": "c719d39c861d4a8eddd9fe460ba5d86b"
}, {
  "url": "include/images/noimage.gif",
  "revision": "07378cab4ad25735cc94ab6f74253583"
}, {
  "url": "include/images/right_arc.gif",
  "revision": "07ec4a686a96f0b84ee73fc13e225292"
}, {
  "url": "include/images/spacer.gif",
  "revision": "41c9bc7f3f78ed71115cc062c1c67b09"
}, {
  "url": "include/images/stable.svg",
  "revision": "13c1f074f75292020672f87d4674b434"
}, {
  "url": "include/images/topBg.gif",
  "revision": "a93a2692f708bce1ae5e39d9f680d0aa"
}, {
  "url": "include/images/Twitter.png",
  "revision": "319205c5f60c0d12f4be4dfe624d20b7"
}, {
  "url": "include/images/Youtube.png",
  "revision": "321798c3a4f90cafc07df60d8a8f0f2f"
}, {
  "url": "include/js/advancefilter.js",
  "revision": "802af94c92fdf421fc87ffc30dbd6f64"
}, {
  "url": "include/js/asterisk.js",
  "revision": "a1ecfb23bb0eabf3ae8ad819497766d7"
}, {
  "url": "include/js/clipboard.min.js",
  "revision": "3f3688138a1b9fc4ef669ce9056b6674"
}, {
  "url": "include/js/clock.js",
  "revision": "d63a09ae85fd37aee6be32fd9c9ca9ab"
}, {
  "url": "include/js/ColorPicker2.js",
  "revision": "60d39426c70ba2d12e821f934728ac2a"
}, {
  "url": "include/js/corebosjshooks.js",
  "revision": "1607aa9f922cfb8476b3635d3edd8286"
}, {
  "url": "include/js/customview.js",
  "revision": "fac2826174b3ef2d807edd9524c029f3"
}, {
  "url": "include/js/de_de.lang.js",
  "revision": "08c9813155d551bdef705a41138b8118"
}, {
  "url": "include/js/dedup.js",
  "revision": "3aab10a82c3f05de285ce73b6dc3be35"
}, {
  "url": "include/js/dtlviewajax.js",
  "revision": "6059341b114976dd92665c122b2e7410"
}, {
  "url": "include/js/en_gb.lang.js",
  "revision": "19e2b144c09cec23ff08e03c8bea78d2"
}, {
  "url": "include/js/en_us.lang.js",
  "revision": "d79b56de6e72352279f479d7e0f583fd"
}, {
  "url": "include/js/es_es.lang.js",
  "revision": "d95dfaf590a2c678bb3bf42a1952fa43"
}, {
  "url": "include/js/es_mx.lang.js",
  "revision": "78735f46f81081209934bbdebb833b85"
}, {
  "url": "include/js/FieldDependencies.js",
  "revision": "7023a4e6ee65093266df7e837578c52c"
}, {
  "url": "include/js/FieldDepFunc.js",
  "revision": "f1545b3ac72f267062e2d7e4b0fa02a0"
}, {
  "url": "include/js/fr_fr.lang.js",
  "revision": "70ba1f235d541fb6ca03498bdd32b405"
}, {
  "url": "include/js/general.js",
  "revision": "4d56b8b5dc682b8ece2cfc16a2f999a5"
}, {
  "url": "include/js/hu_hu.lang.js",
  "revision": "e4cc7bb9fdef5d2b8a887122f48cfc41"
}, {
  "url": "include/js/Inventory.js",
  "revision": "91536c6bbef632e4c599afe3f4173f40"
}, {
  "url": "include/js/it_it.lang.js",
  "revision": "82d0688c57068a35105286134212467f"
}, {
  "url": "include/js/jslog.js",
  "revision": "31c7a760819a0277d2cdb75b82c2c66f"
}, {
  "url": "include/js/ListView.js",
  "revision": "4f6a56365bb644d4651e29b64fe08f36"
}, {
  "url": "include/js/loadjslog.js",
  "revision": "d5317a375aa85d10e53f5989b1be0524"
}, {
  "url": "include/js/Mail.js",
  "revision": "d18e82ce1218134b2cb61291aae4159c"
}, {
  "url": "include/js/massive.js",
  "revision": "88c141893042950229a9ac13a5a8afc7"
}, {
  "url": "include/js/meld.js",
  "revision": "7c3894eb22d16cb4743a7ea226557ed1"
}, {
  "url": "include/js/Merge.js",
  "revision": "92db09ac10a555be4bfc0398f2a93379"
}, {
  "url": "include/js/nl_nl.lang.js",
  "revision": "c4d93b978d39e689fbd55d06ee72aaee"
}, {
  "url": "include/js/notebook.js",
  "revision": "d944ea5145ae45ecbafab41bcbd112f3"
}, {
  "url": "include/js/notificationPopup.js",
  "revision": "3cfd7e34e7fee928b8c27cd0e9f1f46c"
}, {
  "url": "include/js/PasswordManagement.js",
  "revision": "d871391e97faa107c84bfdf8e770f113"
}, {
  "url": "include/js/picklist.js",
  "revision": "2b72e46b2012116ecbb8c7cfb872d98a"
}, {
  "url": "include/js/popup.js",
  "revision": "9dcdc285def88f01bd2f4308acdf8755"
}, {
  "url": "include/js/pt_br.lang.js",
  "revision": "23bbe93eeb453312d9bd309a135397b3"
}, {
  "url": "include/js/QuickCreate.js",
  "revision": "e5029e6a617bfb7f9114191938738069"
}, {
  "url": "include/js/RelatedLists.js",
  "revision": "5bce2a6cf35180a097828b2cde3346ff"
}, {
  "url": "include/js/ro_ro.lang.js",
  "revision": "af46dc4ead40aec0477d31bdd325e84d"
}, {
  "url": "include/js/search.js",
  "revision": "d5c8ddcb6451bc0c05aa26f123363e0f"
}, {
  "url": "include/js/smoothscroll.js",
  "revision": "008f3e9768bd2d226e65ea69999c9f14"
}, {
  "url": "include/js/vtlib.js",
  "revision": "944544d95497d32e53627230b32492fa"
}, {
  "url": "include/components/checkboxrenderer.js",
  "revision": "dff2237c054565d7efe12c59bb02e590"
}, {
  "url": "include/components/ldsmodal.js",
  "revision": "bd7796d82792b933aacc59cb48e0335e"
}, {
  "url": "include/components/ldsprompt.js",
  "revision": "dcff6c137f5d5a9669c75dd4a719f2b0"
}, {
  "url": "include/components/loadjs.js",
  "revision": "0cd3b9c507677b2cb93c292225399888"
}, {
  "url": "include/components/toast-ui/grid/tui-grid.js",
  "revision": "051299fa50c7b8c07648dcc987d2b45d"
}, {
  "url": "include/components/toast-ui/grid/tui-grid.min.css",
  "revision": "39be71f6c5b6e488bffabb855d21ba38"
}, {
  "url": "include/components/toast-ui/grid/tui-grid.min.js",
  "revision": "49b76abb49dcdad1a116458fcac194ef"
}, {
  "url": "include/components/toast-ui/pagination/tui-pagination.css",
  "revision": "70ffbb5f994ca8a5038f529254ab0feb"
}, {
  "url": "include/components/toast-ui/pagination/tui-pagination.js",
  "revision": "58c5bf93525918796d8446ec8c546f6d"
}, {
  "url": "include/components/toast-ui/pagination/tui-pagination.min.css",
  "revision": "ce84e865c6b880f7b0cdaec4921bec98"
}, {
  "url": "include/components/toast-ui/pagination/tui-pagination.min.js",
  "revision": "89970755fc7188dd3c1891fe913c6be4"
}, {
  "url": "include/components/toast-ui/tui-date-picker/tui-date-picker.css",
  "revision": "eef135a433e455edbc6fc925b7f4d25b"
}, {
  "url": "include/components/toast-ui/tui-date-picker/tui-date-picker.js",
  "revision": "89f6ec217c6ebaa5892382c17ad0bc53"
}, {
  "url": "include/components/toast-ui/tui-date-picker/tui-date-picker.min.css",
  "revision": "6606b9374eda4036d447734c7dc8354e"
}, {
  "url": "include/components/toast-ui/tui-date-picker/tui-date-picker.min.js",
  "revision": "4046eb15953f945ca6fd547f17c9c424"
}, {
  "url": "include/chart.js/Chart.bundle.js",
  "revision": "fa54734fcf81ccf0f5d3940e146ea02e"
}, {
  "url": "include/chart.js/Chart.bundle.min.js",
  "revision": "86cc8cd0eb5d5a2b42c1fa46b922d338"
}, {
  "url": "include/chart.js/Chart.css",
  "revision": "28dc89b92b7e59392029cfd2769027ab"
}, {
  "url": "include/chart.js/Chart.js",
  "revision": "aa0d045c7eefcedf60a4e27a6c613d19"
}, {
  "url": "include/chart.js/Chart.min.css",
  "revision": "7d8693e997109f2aeac04066301679d6"
}, {
  "url": "include/chart.js/Chart.min.js",
  "revision": "b5c2301eb15826bf38c9bdcaa3bbe786"
}, {
  "url": "include/chart.js/chartjs-plugin-colorschemes.js",
  "revision": "01e8f3b0edbcca291d30828f2a66c39c"
}, {
  "url": "include/chart.js/chartjs-plugin-colorschemes.min.js",
  "revision": "1334459753dbd8da4dfd2e833d1be02e"
}, {
  "url": "include/chart.js/chartjs-plugin-datalabels.js",
  "revision": "741a33080e9a168f1759f4f3341af817"
}, {
  "url": "include/chart.js/chartjs-plugin-datalabels.min.js",
  "revision": "6d1047f4ab2d92a74bf3f09bf95f945b"
}, {
  "url": "include/chart.js/randomColor.js",
  "revision": "45019dacca57c65149a9e1a52ae798e6"
}, {
  "url": "include/jquery/jquery-ui.js",
  "revision": "9f508c8b3b387f6c46f76df16c7b3c69"
}, {
  "url": "include/jquery/jquery.js",
  "revision": "4f252523d4af0b478c810c2547a63e19"
}, {
  "url": "include/jquery/jquery.steps.min.js",
  "revision": "e1d5a5b0229a7ad0f0f92969064558c0"
}, {
  "url": "include/csrfmagic/csrf-magic.js",
  "revision": "143302dc3175324824d718937e339fff"
}, {
  "url": "include/style.css",
  "revision": "b88841f49337823f3942ea7efb185bdd"
}, {
  "url": "include/print.css",
  "revision": "6b1e0c714dd4c389fb51b840e4cf01d2"
}, {
  "url": "include/jquery.steps.css",
  "revision": "17c65e7385c544455d2235531d60c29d"
}, {
  "url": "include/ldswc/vaadingrid/vaadingrid.js",
  "revision": "6a9f7628348c8847f005a5be6693fdc9"
}, {
  "url": "modules/com_vtiger_workflow/com_vtiger_workflow.js",
  "revision": "d41d8cd98f00b204e9800998ecf8427e"
}, {
  "url": "modules/com_vtiger_workflow/com_vtiger_workflow.png",
  "revision": "240870f4b7d77ee89301eb4aff5cde47"
}, {
  "url": "modules/com_vtiger_workflow/resources/add.png",
  "revision": "15a73d14b6e91db79ae7847c2c0de1a1"
}, {
  "url": "modules/com_vtiger_workflow/resources/createentitytaskscript.js",
  "revision": "f492643e40bc7f965077acf8d68d8fb4"
}, {
  "url": "modules/com_vtiger_workflow/resources/edittaskscript.js",
  "revision": "2246ea2e1c8d902d01ebeb1e70f2dd8c"
}, {
  "url": "modules/com_vtiger_workflow/resources/editworkflowscript.js",
  "revision": "30a01e15d9f5c9417bb42dc2df067dca"
}, {
  "url": "modules/com_vtiger_workflow/resources/emailtaskscript.js",
  "revision": "bd5d9ef3b19bc577133b6de5f6fe28b8"
}, {
  "url": "modules/com_vtiger_workflow/resources/entitymethodtask.js",
  "revision": "43155535cd6da302c6ad1c34c077c642"
}, {
  "url": "modules/com_vtiger_workflow/resources/fieldexpressionpopup.js",
  "revision": "032a70f9519ad27ae91b48e1c30339a7"
}, {
  "url": "modules/com_vtiger_workflow/resources/fieldvalidator.js",
  "revision": "1061e8ade7faf06ee9e2e63590320a10"
}, {
  "url": "modules/com_vtiger_workflow/resources/functional.js",
  "revision": "507e0618da87701f11b973970362e3cf"
}, {
  "url": "modules/com_vtiger_workflow/resources/functionselect.js",
  "revision": "aac47043a92fb7ed7b47d3f146e00b28"
}, {
  "url": "modules/com_vtiger_workflow/resources/generateimagecode.js",
  "revision": "bc1627f9b46bae11f03fde6958ff5809"
}, {
  "url": "modules/com_vtiger_workflow/resources/generateReportWfTask.js",
  "revision": "a0f59214221e1ffdf620dcfdc681ce40"
}, {
  "url": "modules/com_vtiger_workflow/resources/ico-workflow.png",
  "revision": "851ca4caa867fa005174afec1fea762f"
}, {
  "url": "modules/com_vtiger_workflow/resources/jquery.timepicker.js",
  "revision": "cf04488523916947850d381c20427f50"
}, {
  "url": "modules/com_vtiger_workflow/resources/many2manyrelation.js",
  "revision": "8778e149e296ca91f9c4c941007fc17c"
}, {
  "url": "modules/com_vtiger_workflow/resources/parallelexecuter.js",
  "revision": "c75586a43f6d52d0528ae16a16ea689b"
}, {
  "url": "modules/com_vtiger_workflow/resources/remove.png",
  "revision": "c00f6462ceeb5ab01f6cdf297e0e8735"
}, {
  "url": "modules/com_vtiger_workflow/resources/style.css",
  "revision": "d62dc231921aab35a4b55aabcc5fc96c"
}, {
  "url": "modules/com_vtiger_workflow/resources/updatefieldstaskscript.js",
  "revision": "15d69567f5bb39bb9c454e7d1536995a"
}, {
  "url": "modules/com_vtiger_workflow/resources/updatemassivefieldstaskscript.js",
  "revision": "f1837704807d429d5a2f712d1a101992"
}, {
  "url": "modules/com_vtiger_workflow/resources/vtigerwebservices.js",
  "revision": "8acb4cd5b3583547db71a8345e8f667e"
}, {
  "url": "modules/com_vtiger_workflow/resources/wfexeexp.js",
  "revision": "70312429e9d136159b33fd985ede4ff8"
}, {
  "url": "modules/com_vtiger_workflow/resources/wfSendFile.js",
  "revision": "849ebcabb1d9f991f67bfb5d7f6581a8"
}, {
  "url": "modules/com_vtiger_workflow/resources/Whatsappckeditor.js",
  "revision": "fab39532561524488d757c60df5fedd7"
}, {
  "url": "modules/com_vtiger_workflow/resources/whatsappworkflowtaskscript.js",
  "revision": "03215231318fb05a428b2a80b382bb45"
}, {
  "url": "modules/com_vtiger_workflow/resources/workflowlistscript.js",
  "revision": "c772b9cea1354ccf15f30bea58aa0fc2"
}, {
  "url": "modules/GlobalVariable/GlobalVariable.js",
  "revision": "c240060c1b952bba5d36d4a506fa0e6e"
}, {
  "url": "modules/GlobalVariable/GlobalVariable.png",
  "revision": "e883ee9182ef7d21303bf992915b141b"
}, {
  "url": "modules/GlobalVariable/GlobalVariable64.png",
  "revision": "383f72ca2b714b85ec925ee42240cfd3"
}, {
  "url": "modules/GlobalVariable/tablesorter/jquery.tablesorter.min.js",
  "revision": "28f91818bc0e61a9b5445eed72e45ee5"
}, {
  "url": "modules/GlobalVariable/tablesorter/themes/blue/asc.gif",
  "revision": "f8a1940c9cf44ab8870319169f3a14ff"
}, {
  "url": "modules/GlobalVariable/tablesorter/themes/blue/bg.gif",
  "revision": "c01ad2e7c59d1a20a433cb873c21bd88"
}, {
  "url": "modules/GlobalVariable/tablesorter/themes/blue/desc.gif",
  "revision": "a54846803de3cc786eec3d69f9ac2d38"
}, {
  "url": "modules/GlobalVariable/tablesorter/themes/blue/style.css",
  "revision": "5b98d0810fb7dbb9fcbc2362655f0dd7"
}, {
  "url": "modules/GlobalVariable/tablesorter/themes/green/asc.png",
  "revision": "47d431b1524d523eae100b66b09babdc"
}, {
  "url": "modules/GlobalVariable/tablesorter/themes/green/bg.png",
  "revision": "7b0a5fe32e94b1595e48810a3df45648"
}, {
  "url": "modules/GlobalVariable/tablesorter/themes/green/desc.png",
  "revision": "0f7f4fd46fe145ed6ed4c81c3b26a93f"
}, {
  "url": "modules/GlobalVariable/tablesorter/themes/green/style.css",
  "revision": "8c047013d96b74708da195dac43980b7"
}, {
  "url": "modules/evvtMenu/32px.png",
  "revision": "db49c8de4f267eede40a9a8843efcdec"
}, {
  "url": "modules/evvtMenu/40px.png",
  "revision": "1f075735090412ed7eb8077d819b19c6"
}, {
  "url": "modules/evvtMenu/collapse.png",
  "revision": "875d8fec724352381ca879bad8d7b7d6"
}, {
  "url": "modules/evvtMenu/coloricons-sprite.png",
  "revision": "e0d204f0961bb6aa4aa4fb79fa262025"
}, {
  "url": "modules/evvtMenu/evvtMenu.css",
  "revision": "3897d24cb8412c6c611bc55029d1f341"
}, {
  "url": "modules/evvtMenu/evvtMenu.js",
  "revision": "49f1ad7e2f02a7581af0008dcc9ba4e4"
}, {
  "url": "modules/evvtMenu/evvtMenu.png",
  "revision": "27f4168de5f147e0300e7391174c75b0"
}, {
  "url": "modules/evvtMenu/expand.png",
  "revision": "a3f51f6bbdf0ac0ae90a52ef8975ce22"
}, {
  "url": "modules/evvtMenu/jquery.fancytree-all.min.js",
  "revision": "118faab2469b8a3e4a567545f42ec0f5"
}, {
  "url": "modules/evvtMenu/jstree.min.js",
  "revision": "8505b45e8cf71b1f556e97f3a34734a5"
}, {
  "url": "modules/evvtMenu/style.min.css",
  "revision": "5064bed2e1319fa871d2c6f2d18789e2"
}, {
  "url": "modules/evvtMenu/throbber.gif",
  "revision": "9ed4669f524bec38319be63a2ee4ba26"
}, {
  "url": "modules/MailManager/MailManager.js",
  "revision": "12d6ddb8c1784c10beadea337823e434"
}, {
  "url": "modules/MailManager/MailManager.png",
  "revision": "1c045d808355b6ed581cd4971f3436f9"
}, {
  "url": "modules/MailManager/resources/jquery.tokeninput.js",
  "revision": "2225af5abe843f40dc29d79e0b1ea565"
}, {
  "url": "modules/MailManager/resources/token-input-facebook.css",
  "revision": "35d8aea3992991060bddd7dd035717c5"
}, {
  "url": "modules/Tooltip/Tooltip.js",
  "revision": "651680d2a9ffb37861fcac6d52d83f24"
}, {
  "url": "modules/Tooltip/Tooltip.png",
  "revision": "fc384192e4fa1a8cf362dd13fc9795b4"
}, {
  "url": "modules/Tooltip/TooltipHeaderScript.js",
  "revision": "dfe4e586e0044d9973576b5c5b79065b"
}, {
  "url": "modules/Tooltip/TooltipSettings.js",
  "revision": "62977aea32e2ee795041c7de5641e6dd"
}, {
  "url": "modules/Accounts/Accounts.png",
  "revision": "f67a467753048c2b24896fea369f94c7"
}, {
  "url": "modules/Assets/Assets.png",
  "revision": "1731082fd75d2dd046cd8d2b205f205c"
}, {
  "url": "modules/Calendar/Events.png",
  "revision": "d272fe117e3a9104236b81d78dbf7a7c"
}, {
  "url": "modules/Campaigns/Campaigns.png",
  "revision": "9025d280f0e8b147e104dacec651c980"
}, {
  "url": "modules/CobroPago/CobroPago.png",
  "revision": "b3bd0c5f6a7d8c353aa5372337ededf9"
}, {
  "url": "modules/CobroPago/settings.png",
  "revision": "c85d74bdf7c2100636d21a3e167d1f2d"
}, {
  "url": "modules/Contacts/Contacts.png",
  "revision": "bff9fd764212760ee140c48e30985900"
}, {
  "url": "modules/Documents/Documents.png",
  "revision": "3494f23ed389037fba208eb171ed54b1"
}, {
  "url": "modules/Emails/Emails.png",
  "revision": "205bb82676beb92ec2dfa978a93277a2"
}, {
  "url": "modules/Faq/Faq.png",
  "revision": "dea034cc2268d01a96bde50176e3648a"
}, {
  "url": "modules/HelpDesk/HelpDesk.png",
  "revision": "597044722eefba6d7a8d0a1857a7c636"
}, {
  "url": "modules/Invoice/Invoice.png",
  "revision": "e12a8943e4b3f34872b3e59d000b1fa9"
}, {
  "url": "modules/Leads/Leads.png",
  "revision": "e9de77faccaef8c53e8faae287eb0ca1"
}, {
  "url": "modules/ModComments/ModComments.png",
  "revision": "6f257d77b24a2d0f79a3796ab9e3ff7f"
}, {
  "url": "modules/PBXManager/PBXManager.png",
  "revision": "755e4bbf9003cbad9cea23fe6cdae5d2"
}, {
  "url": "modules/Portal/Portal.png",
  "revision": "1befaba9851212e8ed11d32ad2891a84"
}, {
  "url": "modules/Potentials/Potentials.png",
  "revision": "1bcca2dd8d1c68f38da968e86f4f1bc4"
}, {
  "url": "modules/PriceBooks/PriceBooks.png",
  "revision": "f428aeafd5255f815189e02bd8a09a4b"
}, {
  "url": "modules/Products/placeholder.gif",
  "revision": "df6770aa59bb302b5cbc84554e391e26"
}, {
  "url": "modules/Products/Products.png",
  "revision": "88ad151939905891d2d3aee1795932f4"
}, {
  "url": "modules/ProjectMilestone/ProjectMilestone.png",
  "revision": "d0d3d5804ea8bd519024102a85a4c5da"
}, {
  "url": "modules/Project/Project.png",
  "revision": "d9c5ebde5ef3312548a7fd407fc52635"
}, {
  "url": "modules/ProjectTask/ProjectTask.png",
  "revision": "31c3a0e0d0e7b541611c878118994769"
}, {
  "url": "modules/PurchaseOrder/PurchaseOrder.png",
  "revision": "4979e62d7ef03142607b7afe14b6544d"
}, {
  "url": "modules/Quotes/Quotes.png",
  "revision": "e31f403bcc7013a1cc89b7cdfa0957a1"
}, {
  "url": "modules/RecycleBin/RecycleBin.png",
  "revision": "02e5a4e4236aa0c59788e8ee84a6272a"
}, {
  "url": "modules/Reports/Reports.png",
  "revision": "9b6aa0d9feeb3715ce0b178e65e3a185"
}, {
  "url": "modules/Rss/Rss.png",
  "revision": "c631db4abb5bc1e64e28fae877ed98c7"
}, {
  "url": "modules/SalesOrder/SalesOrder.png",
  "revision": "c218a0f935a156ab3306b0503a36d2fd"
}, {
  "url": "modules/ServiceContracts/ServiceContracts.png",
  "revision": "87c55137d73585a39a76ee6c6859f0c8"
}, {
  "url": "modules/Services/placeholder.gif",
  "revision": "df6770aa59bb302b5cbc84554e391e26"
}, {
  "url": "modules/Services/Services.png",
  "revision": "329befd4ae1808bf677acdd8dcff2f57"
}, {
  "url": "modules/Settings/Settings.png",
  "revision": "e6ae8afae0c601b790617737ef78f02a"
}, {
  "url": "modules/SMSNotifier/SMSNotifier.png",
  "revision": "7cfdd1c8aa1d269dfe6643b3d4bdc35d"
}, {
  "url": "modules/Users/Users.png",
  "revision": "0ee1f4360eea0b2ff9f13550bb9d536f"
}, {
  "url": "modules/Vendors/Vendors.png",
  "revision": "bad0f7ffab917e6e2a06966f0c91d277"
}, {
  "url": "modules/Documents/Documents.js",
  "revision": "5379a537fce7b3cba68f684538cbcec9"
}, {
  "url": "modules/SalesOrder/SalesOrder.js",
  "revision": "20916140c09d05b2965c64907a7a76d0"
}, {
  "url": "modules/Settings/Settings.js",
  "revision": "1001255e02a066e3fab9687eb4f7ec79"
}, {
  "url": "modules/Settings/profilePrivileges.js",
  "revision": "d81d32d5d140b3eb65edf400f88cd8b6"
}, {
  "url": "modules/Products/Productsslide.js",
  "revision": "721bd3d5367129defea7f6bd1bb8eee3"
}, {
  "url": "modules/Products/multifile.js",
  "revision": "2469a9d75b05798558ca746bff88ef36"
}, {
  "url": "modules/Products/Products.js",
  "revision": "ccfc370693479b43ba71ff20822c3199"
}, {
  "url": "modules/ModComments/ModComments.js",
  "revision": "253c82414388480dd8d07ec5f899968d"
}, {
  "url": "modules/ModComments/ModCommentsCommon.js",
  "revision": "90cb31321c92bc6d3f1e73e5ecd0255a"
}, {
  "url": "modules/MsgTemplate/MsgTemplate.js",
  "revision": "4137b49c61a51b277dec2522dc505491"
}, {
  "url": "modules/ProjectTask/ProjectTask.js",
  "revision": "c240060c1b952bba5d36d4a506fa0e6e"
}, {
  "url": "modules/Project/Project.js",
  "revision": "c240060c1b952bba5d36d4a506fa0e6e"
}, {
  "url": "modules/Assets/Assets.js",
  "revision": "69636c3bb0f5569277b10c98016af69a"
}, {
  "url": "modules/Emails/Emails.js",
  "revision": "92590b1064f23d4416aefcf66dd83c31"
}, {
  "url": "modules/Rss/Rss.js",
  "revision": "9e93cf2f8dddbea42be801f31e1f73f3"
}, {
  "url": "modules/PickList/DependencyPicklist.js",
  "revision": "8b38e7858ab48183ba87dc24e60178bc"
}, {
  "url": "modules/HelpDesk/HelpDesk.js",
  "revision": "a1d67a7ccdf46c451b108ae99930bba8"
}, {
  "url": "modules/Potentials/Potentials.js",
  "revision": "4ebb204b35da3e737622cff83e0de88c"
}, {
  "url": "modules/CronTasks/CronTasks.js",
  "revision": "5ed805a40a9bb0df7a12e9bd966311ab"
}, {
  "url": "modules/PBXManager/PBXManager.js",
  "revision": "c663d6d8dfeba2b51a542382c8623087"
}, {
  "url": "modules/Calendar/script.js",
  "revision": "72f944c5928692ba7107b1a9b04a37bc"
}, {
  "url": "modules/Calendar/Calendar.js",
  "revision": "42eb8ee2c489f5282fb720547738a037"
}, {
  "url": "modules/cbCalendar/cbCalendar.js",
  "revision": "963d1c00b77948ab883373732ad89a8e"
}, {
  "url": "modules/cbQuestion/cbQuestion.js",
  "revision": "df1beb66383c404ca19595bfede77a1f"
}, {
  "url": "modules/cbQuestion/resources/appendcontext.js",
  "revision": "87897968e569d8a5f69b730b27c8aa4b"
}, {
  "url": "modules/cbQuestion/resources/Builder.js",
  "revision": "f11bf7fb61310f00c2bb311981b36de9"
}, {
  "url": "modules/cbQuestion/resources/editbuilder.js",
  "revision": "882f43f4054b3056e66a674b35112825"
}, {
  "url": "modules/cbQuestion/resources/mermaid.min.js",
  "revision": "2cb9770b79c455efcb81bc3f58e35ef6"
}, {
  "url": "modules/cbQuestion/language/de_de.js",
  "revision": "d8ea1290c611022fce93863c19c60b5a"
}, {
  "url": "modules/cbQuestion/language/en_gb.js",
  "revision": "d8ea1290c611022fce93863c19c60b5a"
}, {
  "url": "modules/cbQuestion/language/en_us.js",
  "revision": "d8ea1290c611022fce93863c19c60b5a"
}, {
  "url": "modules/cbQuestion/language/es_es.js",
  "revision": "f8f50b19baeefb17e0c4fb706608d635"
}, {
  "url": "modules/cbQuestion/language/es_mx.js",
  "revision": "f8f50b19baeefb17e0c4fb706608d635"
}, {
  "url": "modules/cbQuestion/language/fr_fr.js",
  "revision": "d8ea1290c611022fce93863c19c60b5a"
}, {
  "url": "modules/cbQuestion/language/hu_hu.js",
  "revision": "d8ea1290c611022fce93863c19c60b5a"
}, {
  "url": "modules/cbQuestion/language/it_it.js",
  "revision": "d8ea1290c611022fce93863c19c60b5a"
}, {
  "url": "modules/cbQuestion/language/nl_nl.js",
  "revision": "d8ea1290c611022fce93863c19c60b5a"
}, {
  "url": "modules/cbQuestion/language/pt_br.js",
  "revision": "ee6ef3ad4183f84a96e77f57d84bc163"
}, {
  "url": "modules/cbQuestion/language/ro_ro.js",
  "revision": "d8ea1290c611022fce93863c19c60b5a"
}, {
  "url": "modules/Portal/Portal.js",
  "revision": "01b10c9bbf410ef98cc023a2821412e1"
}, {
  "url": "modules/ProjectMilestone/ProjectMilestone.js",
  "revision": "c240060c1b952bba5d36d4a506fa0e6e"
}, {
  "url": "modules/Leads/Leads.js",
  "revision": "c5db71d972b07b46dd4b9e24a0f0c92a"
}, {
  "url": "modules/WSAPP/WSAPP.js",
  "revision": "36b3093ded85fbdb78336ed100dae8ac"
}, {
  "url": "modules/Accounts/Accounts.js",
  "revision": "f60fafaea0f0957ec2535a531828e18c"
}, {
  "url": "modules/cbMap/cbMap.js",
  "revision": "ac4053b733101a16e9ba2a83b0bd7f5a"
}, {
  "url": "modules/cbMap/language/de_de.js",
  "revision": "1b9a44052d13e3c95c43264a510eba45"
}, {
  "url": "modules/cbMap/language/en_gb.js",
  "revision": "1b9a44052d13e3c95c43264a510eba45"
}, {
  "url": "modules/cbMap/language/en_us.js",
  "revision": "1b9a44052d13e3c95c43264a510eba45"
}, {
  "url": "modules/cbMap/language/es_es.js",
  "revision": "4c6ebc53f043d1ba6a121657e6df2ce9"
}, {
  "url": "modules/cbMap/language/es_mx.js",
  "revision": "4c6ebc53f043d1ba6a121657e6df2ce9"
}, {
  "url": "modules/cbMap/language/fr_fr.js",
  "revision": "1b9a44052d13e3c95c43264a510eba45"
}, {
  "url": "modules/cbMap/language/hu_hu.js",
  "revision": "1b9a44052d13e3c95c43264a510eba45"
}, {
  "url": "modules/cbMap/language/it_it.js",
  "revision": "1b9a44052d13e3c95c43264a510eba45"
}, {
  "url": "modules/cbMap/language/nl_nl.js",
  "revision": "1b9a44052d13e3c95c43264a510eba45"
}, {
  "url": "modules/cbMap/language/pt_br.js",
  "revision": "21fb0547c2e9283f69728d220ca3d8c7"
}, {
  "url": "modules/cbMap/language/ro_ro.js",
  "revision": "1b9a44052d13e3c95c43264a510eba45"
}, {
  "url": "modules/cbTermConditions/cbTermConditions.js",
  "revision": "c240060c1b952bba5d36d4a506fa0e6e"
}, {
  "url": "modules/Campaigns/Campaigns.js",
  "revision": "12de06af8b7cc1f09fb8e27c69736dbf"
}, {
  "url": "modules/Invoice/Invoice.js",
  "revision": "6491883f6fec8e2268507b4acb12e98a"
}, {
  "url": "modules/CobroPago/CobroPago.js",
  "revision": "c240060c1b952bba5d36d4a506fa0e6e"
}, {
  "url": "modules/CustomerPortal/CustomerPortal.js",
  "revision": "a0025e2eae5e58bf58564524adadca0c"
}, {
  "url": "modules/cbupdater/cbupdater.js",
  "revision": "cde88c33da015b3b7e277c8906c50457"
}, {
  "url": "modules/Home/Homestuff.js",
  "revision": "93d9fa4ef0d77c580a19d848f257a19d"
}, {
  "url": "modules/Home/js/HelpMeNow.js",
  "revision": "3facd26fc8ea33e48ec5c19fcef2472d"
}, {
  "url": "modules/Faq/Faq.js",
  "revision": "875ace6df1888378e0ca00db366abd62"
}, {
  "url": "modules/ModTracker/ModTracker.js",
  "revision": "c822e17c71c3f3e039528c4311f4cbae"
}, {
  "url": "modules/ModTracker/ModTrackerCommon.js",
  "revision": "0142cfef4d787e344e7c425124d80f4e"
}, {
  "url": "modules/ModTracker/language/de_de.js",
  "revision": "bdafc8809c554dd1490838aa8132e6c2"
}, {
  "url": "modules/ModTracker/language/en_gb.js",
  "revision": "bdafc8809c554dd1490838aa8132e6c2"
}, {
  "url": "modules/ModTracker/language/en_us.js",
  "revision": "bdafc8809c554dd1490838aa8132e6c2"
}, {
  "url": "modules/ModTracker/language/es_es.js",
  "revision": "a28cada0d4e3f34faf900a6b480cd788"
}, {
  "url": "modules/ModTracker/language/es_mx.js",
  "revision": "a28cada0d4e3f34faf900a6b480cd788"
}, {
  "url": "modules/ModTracker/language/fr_fr.js",
  "revision": "bdafc8809c554dd1490838aa8132e6c2"
}, {
  "url": "modules/ModTracker/language/hu_hu.js",
  "revision": "9635e354b109bfe94f3fd4911ec649b2"
}, {
  "url": "modules/ModTracker/language/it_it.js",
  "revision": "bdafc8809c554dd1490838aa8132e6c2"
}, {
  "url": "modules/ModTracker/language/nl_nl.js",
  "revision": "6531a4eabc9036cd9898b785671f2f2c"
}, {
  "url": "modules/ModTracker/language/pt_br.js",
  "revision": "07c5965a57fb166f85618202540443d9"
}, {
  "url": "modules/ModTracker/language/ro_ro.js",
  "revision": "bdafc8809c554dd1490838aa8132e6c2"
}, {
  "url": "modules/SMSNotifier/workflow/VTSMSTask.js",
  "revision": "8120de0e05492fe510dd0d89ae500002"
}, {
  "url": "modules/SMSNotifier/SMSNotifier.js",
  "revision": "3264e49d554e0ae77bd1eb7f58d3b2c1"
}, {
  "url": "modules/SMSNotifier/SMSConfigServer.js",
  "revision": "9e48754306aea76b1653bc67e96b1f03"
}, {
  "url": "modules/SMSNotifier/SMSNotifierCommon.js",
  "revision": "2bc5785bbc8662d43972828563d8e11a"
}, {
  "url": "modules/Contacts/Contacts.js",
  "revision": "78c1c0ca28f2b99fe8cb695c16eb560b"
}, {
  "url": "modules/Dashboard/Dashboard.js",
  "revision": "8ab2b632de69afd4fefcd5daccab5af6"
}, {
  "url": "modules/Services/Services.js",
  "revision": "6233d137c122fc3f08950b8f45dd49b6"
}, {
  "url": "modules/Vendors/Vendors.js",
  "revision": "29077a91b5317da8237274d0436bbc73"
}, {
  "url": "modules/InventoryDetails/InventoryDetails.js",
  "revision": "c240060c1b952bba5d36d4a506fa0e6e"
}, {
  "url": "modules/ServiceContracts/ServiceContracts.js",
  "revision": "c240060c1b952bba5d36d4a506fa0e6e"
}, {
  "url": "modules/Users/Users.js",
  "revision": "e9be4cf50f50eedc61a491219ac3fc86"
}, {
  "url": "modules/Utilities/Utilities.js",
  "revision": "864fa0c60694a45cfadf9bc2203c2ac7"
}, {
  "url": "modules/Import/resources/ImportStep2.js",
  "revision": "45c632cf97d1f03c7f6805c1f78ae0a7"
}, {
  "url": "modules/Import/resources/Import.js",
  "revision": "d5e127679f8ff08f74ca1168618c310d"
}, {
  "url": "modules/CustomView/CustomView.js",
  "revision": "65682db92a471ba46f12737f6f6b3e4d"
}, {
  "url": "modules/Reports/Reports.js",
  "revision": "29a48bc56aef2789fce03f0b3a5601fd"
}, {
  "url": "modules/Reports/ReportsSteps.js",
  "revision": "dac7c3f4b013798df0875b1450e27bc1"
}, {
  "url": "modules/PriceBooks/PriceBooks.js",
  "revision": "1975335374a344be4e3f98dbfe6756b4"
}, {
  "url": "modules/Quotes/Quotes.js",
  "revision": "e13706328f79bdc133480d480ede4968"
}, {
  "url": "modules/PurchaseOrder/PurchaseOrder.js",
  "revision": "e7a778edc2cbf9bbc6849469beb2746e"
}, {
  "url": "include/Webservices/WSClient.js",
  "revision": "ce5b9c7843df949930c6521cb685b0bd"
}, {
  "url": "include/freetag/jquery.tagcanvas.js",
  "revision": "0be5cdb88030adc8bdeeaef233fdb1a4"
}, {
  "url": "include/freetag/jquery.tagcanvas.min.js",
  "revision": "3e99f6e7297378ccbcdcfd0b907a511a"
}, {
  "url": "include/freetag/tagcanvas.min.js",
  "revision": "82199e635f5096bfb6a5cf79ce023933"
}, {
  "url": "include/freetag/tagcanvas.js",
  "revision": "88dd1cf2eb131225f8aab090d5fce221"
}], {});
