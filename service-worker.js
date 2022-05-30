try {
  self['workbox:core:6.4.1'] && _();
} catch (e) {}

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
  Copyright 2019 Google LLC
  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
const logger = (() => {
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
    groupEnd: null // No colored prefix on groupEnd

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
  }; // eslint-disable-next-line @typescript-eslint/ban-types


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

    const classNameStr = className ? `${className}.` : '';
    return `The parameter '${paramName}' passed into ` + `'${moduleName}.${classNameStr}` + `${funcName}()' must be of type ${expectedType}.`;
  },
  'incorrect-class': ({
    expectedClassName,
    paramName,
    moduleName,
    className,
    funcName,
    isReturnValueProblem
  }) => {
    if (!expectedClassName || !moduleName || !funcName) {
      throw new Error(`Unexpected input to 'incorrect-class' error.`);
    }

    const classNameStr = className ? `${className}.` : '';

    if (isReturnValueProblem) {
      return `The return value from ` + `'${moduleName}.${classNameStr}${funcName}()' ` + `must be an instance of class ${expectedClassName}.`;
    }

    return `The parameter '${paramName}' passed into ` + `'${moduleName}.${classNameStr}${funcName}()' ` + `must be an instance of class ${expectedClassName}.`;
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

    return `Two of the entries passed to ` + `'workbox-precaching.PrecacheController.addToCacheList()' had the URL ` + `${firstEntry} but different revision details. Workbox is ` + `unable to cache and version the asset correctly. Please remove one ` + `of the entries.`;
  },
  'plugin-error-request-will-fetch': ({
    thrownErrorMessage
  }) => {
    if (!thrownErrorMessage) {
      throw new Error(`Unexpected input to ` + `'plugin-error-request-will-fetch', error.`);
    }

    return `An error was thrown by a plugins 'requestWillFetch()' method. ` + `The thrown error message was: '${thrownErrorMessage}'.`;
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
    return `The precaching request for '${url}' failed` + (status ? ` with an HTTP status of ${status}.` : `.`);
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
  },
  'cross-origin-copy-response': ({
    origin
  }) => {
    return `workbox-core.copyResponse() can only be used with same-origin ` + `responses. It was passed a response with origin ${origin}.`;
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

const messageGenerator = generatorFunction;

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
    const message = messageGenerator(errorCode, details);
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

const isInstance = (object, // Need the general type to do the check later.
// eslint-disable-next-line @typescript-eslint/ban-types
expectedClass, details) => {
  if (!(object instanceof expectedClass)) {
    details['expectedClassName'] = expectedClass.name;
    throw new WorkboxError('incorrect-class', details);
  }
};

const isOneOf = (value, validValues, details) => {
  if (!validValues.includes(value)) {
    details['validValueDescription'] = `Valid values are ${JSON.stringify(validValues)}.`;
    throw new WorkboxError('invalid-value', details);
  }
};

const isArrayOfClass = (value, // Need general type to do check later.
expectedClass, // eslint-disable-line
details) => {
  const error = new WorkboxError('not-array-of-class', details);

  if (!Array.isArray(value)) {
    throw error;
  }

  for (const item of value) {
    if (!(item instanceof expectedClass)) {
      throw error;
    }
  }
};

const finalAssertExports = {
  hasMethod,
  isArray,
  isInstance,
  isOneOf,
  isType,
  isArrayOfClass
};

try {
  self['workbox:routing:6.4.1'] && _();
} catch (e) {}

/*
  Copyright 2018 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
/**
 * The default HTTP method, 'GET', used when there's no specific method
 * configured for a route.
 *
 * @type {string}
 *
 * @private
 */

const defaultMethod = 'GET';
/**
 * The list of valid HTTP methods associated with requests that could be routed.
 *
 * @type {Array<string>}
 *
 * @private
 */

const validMethods = ['DELETE', 'GET', 'HEAD', 'PATCH', 'POST', 'PUT'];

/*
  Copyright 2018 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
/**
 * @param {function()|Object} handler Either a function, or an object with a
 * 'handle' method.
 * @return {Object} An object with a handle method.
 *
 * @private
 */

const normalizeHandler = handler => {
  if (handler && typeof handler === 'object') {
    {
      finalAssertExports.hasMethod(handler, 'handle', {
        moduleName: 'workbox-routing',
        className: 'Route',
        funcName: 'constructor',
        paramName: 'handler'
      });
    }

    return handler;
  } else {
    {
      finalAssertExports.isType(handler, 'function', {
        moduleName: 'workbox-routing',
        className: 'Route',
        funcName: 'constructor',
        paramName: 'handler'
      });
    }

    return {
      handle: handler
    };
  }
};

/*
  Copyright 2018 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
/**
 * A `Route` consists of a pair of callback functions, "match" and "handler".
 * The "match" callback determine if a route should be used to "handle" a
 * request by returning a non-falsy value if it can. The "handler" callback
 * is called when there is a match and should return a Promise that resolves
 * to a `Response`.
 *
 * @memberof module:workbox-routing
 */

class Route {
  /**
   * Constructor for Route class.
   *
   * @param {module:workbox-routing~matchCallback} match
   * A callback function that determines whether the route matches a given
   * `fetch` event by returning a non-falsy value.
   * @param {module:workbox-routing~handlerCallback} handler A callback
   * function that returns a Promise resolving to a Response.
   * @param {string} [method='GET'] The HTTP method to match the Route
   * against.
   */
  constructor(match, handler, method = defaultMethod) {
    {
      finalAssertExports.isType(match, 'function', {
        moduleName: 'workbox-routing',
        className: 'Route',
        funcName: 'constructor',
        paramName: 'match'
      });

      if (method) {
        finalAssertExports.isOneOf(method, validMethods, {
          paramName: 'method'
        });
      }
    } // These values are referenced directly by Router so cannot be
    // altered by minificaton.


    this.handler = normalizeHandler(handler);
    this.match = match;
    this.method = method;
  }
  /**
   *
   * @param {module:workbox-routing-handlerCallback} handler A callback
   * function that returns a Promise resolving to a Response
   */


  setCatchHandler(handler) {
    this.catchHandler = normalizeHandler(handler);
  }

}

/*
  Copyright 2018 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
/**
 * RegExpRoute makes it easy to create a regular expression based
 * [Route]{@link module:workbox-routing.Route}.
 *
 * For same-origin requests the RegExp only needs to match part of the URL. For
 * requests against third-party servers, you must define a RegExp that matches
 * the start of the URL.
 *
 * [See the module docs for info.]{@link https://developers.google.com/web/tools/workbox/modules/workbox-routing}
 *
 * @memberof module:workbox-routing
 * @extends module:workbox-routing.Route
 */

class RegExpRoute extends Route {
  /**
   * If the regular expression contains
   * [capture groups]{@link https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/RegExp#grouping-back-references},
   * the captured values will be passed to the
   * [handler's]{@link module:workbox-routing~handlerCallback} `params`
   * argument.
   *
   * @param {RegExp} regExp The regular expression to match against URLs.
   * @param {module:workbox-routing~handlerCallback} handler A callback
   * function that returns a Promise resulting in a Response.
   * @param {string} [method='GET'] The HTTP method to match the Route
   * against.
   */
  constructor(regExp, handler, method) {
    {
      finalAssertExports.isInstance(regExp, RegExp, {
        moduleName: 'workbox-routing',
        className: 'RegExpRoute',
        funcName: 'constructor',
        paramName: 'pattern'
      });
    }

    const match = ({
      url
    }) => {
      const result = regExp.exec(url.href); // Return immediately if there's no match.

      if (!result) {
        return;
      } // Require that the match start at the first character in the URL string
      // if it's a cross-origin request.
      // See https://github.com/GoogleChrome/workbox/issues/281 for the context
      // behind this behavior.


      if (url.origin !== location.origin && result.index !== 0) {
        {
          logger.debug(`The regular expression '${regExp.toString()}' only partially matched ` + `against the cross-origin URL '${url.toString()}'. RegExpRoute's will only ` + `handle cross-origin requests if they match the entire URL.`);
        }

        return;
      } // If the route matches, but there aren't any capture groups defined, then
      // this will return [], which is truthy and therefore sufficient to
      // indicate a match.
      // If there are capture groups, then it will return their values.


      return result.slice(1);
    };

    super(match, handler, method);
  }

}

/*
  Copyright 2018 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/

const getFriendlyURL = url => {
  const urlObj = new URL(String(url), location.href); // See https://github.com/GoogleChrome/workbox/issues/2323
  // We want to include everything, except for the origin if it's same-origin.

  return urlObj.href.replace(new RegExp(`^${location.origin}`), '');
};

/*
  Copyright 2018 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
/**
 * The Router can be used to process a FetchEvent through one or more
 * [Routes]{@link module:workbox-routing.Route} responding  with a Request if
 * a matching route exists.
 *
 * If no route matches a given a request, the Router will use a "default"
 * handler if one is defined.
 *
 * Should the matching Route throw an error, the Router will use a "catch"
 * handler if one is defined to gracefully deal with issues and respond with a
 * Request.
 *
 * If a request matches multiple routes, the **earliest** registered route will
 * be used to respond to the request.
 *
 * @memberof module:workbox-routing
 */

class Router {
  /**
   * Initializes a new Router.
   */
  constructor() {
    this._routes = new Map();
    this._defaultHandlerMap = new Map();
  }
  /**
   * @return {Map<string, Array<module:workbox-routing.Route>>} routes A `Map` of HTTP
   * method name ('GET', etc.) to an array of all the corresponding `Route`
   * instances that are registered.
   */


  get routes() {
    return this._routes;
  }
  /**
   * Adds a fetch event listener to respond to events when a route matches
   * the event's request.
   */


  addFetchListener() {
    // See https://github.com/Microsoft/TypeScript/issues/28357#issuecomment-436484705
    self.addEventListener('fetch', event => {
      const {
        request
      } = event;
      const responsePromise = this.handleRequest({
        request,
        event
      });

      if (responsePromise) {
        event.respondWith(responsePromise);
      }
    });
  }
  /**
   * Adds a message event listener for URLs to cache from the window.
   * This is useful to cache resources loaded on the page prior to when the
   * service worker started controlling it.
   *
   * The format of the message data sent from the window should be as follows.
   * Where the `urlsToCache` array may consist of URL strings or an array of
   * URL string + `requestInit` object (the same as you'd pass to `fetch()`).
   *
   * ```
   * {
   *   type: 'CACHE_URLS',
   *   payload: {
   *     urlsToCache: [
   *       './script1.js',
   *       './script2.js',
   *       ['./script3.js', {mode: 'no-cors'}],
   *     ],
   *   },
   * }
   * ```
   */


  addCacheListener() {
    // See https://github.com/Microsoft/TypeScript/issues/28357#issuecomment-436484705
    self.addEventListener('message', event => {
      // event.data is type 'any'
      // eslint-disable-next-line @typescript-eslint/no-unsafe-member-access
      if (event.data && event.data.type === 'CACHE_URLS') {
        // eslint-disable-next-line @typescript-eslint/no-unsafe-assignment
        const {
          payload
        } = event.data;

        {
          logger.debug(`Caching URLs from the window`, payload.urlsToCache);
        }

        const requestPromises = Promise.all(payload.urlsToCache.map(entry => {
          if (typeof entry === 'string') {
            entry = [entry];
          }

          const request = new Request(...entry);
          return this.handleRequest({
            request,
            event
          }); // TODO(philipwalton): TypeScript errors without this typecast for
          // some reason (probably a bug). The real type here should work but
          // doesn't: `Array<Promise<Response> | undefined>`.
        })); // TypeScript

        event.waitUntil(requestPromises); // If a MessageChannel was used, reply to the message on success.

        if (event.ports && event.ports[0]) {
          void requestPromises.then(() => event.ports[0].postMessage(true));
        }
      }
    });
  }
  /**
   * Apply the routing rules to a FetchEvent object to get a Response from an
   * appropriate Route's handler.
   *
   * @param {Object} options
   * @param {Request} options.request The request to handle.
   * @param {ExtendableEvent} options.event The event that triggered the
   *     request.
   * @return {Promise<Response>|undefined} A promise is returned if a
   *     registered route can handle the request. If there is no matching
   *     route and there's no `defaultHandler`, `undefined` is returned.
   */


  handleRequest({
    request,
    event
  }) {
    {
      finalAssertExports.isInstance(request, Request, {
        moduleName: 'workbox-routing',
        className: 'Router',
        funcName: 'handleRequest',
        paramName: 'options.request'
      });
    }

    const url = new URL(request.url, location.href);

    if (!url.protocol.startsWith('http')) {
      {
        logger.debug(`Workbox Router only supports URLs that start with 'http'.`);
      }

      return;
    }

    const sameOrigin = url.origin === location.origin;
    const {
      params,
      route
    } = this.findMatchingRoute({
      event,
      request,
      sameOrigin,
      url
    });
    let handler = route && route.handler;
    const debugMessages = [];

    {
      if (handler) {
        debugMessages.push([`Found a route to handle this request:`, route]);

        if (params) {
          debugMessages.push([`Passing the following params to the route's handler:`, params]);
        }
      }
    } // If we don't have a handler because there was no matching route, then
    // fall back to defaultHandler if that's defined.


    const method = request.method;

    if (!handler && this._defaultHandlerMap.has(method)) {
      {
        debugMessages.push(`Failed to find a matching route. Falling ` + `back to the default handler for ${method}.`);
      }

      handler = this._defaultHandlerMap.get(method);
    }

    if (!handler) {
      {
        // No handler so Workbox will do nothing. If logs is set of debug
        // i.e. verbose, we should print out this information.
        logger.debug(`No route found for: ${getFriendlyURL(url)}`);
      }

      return;
    }

    {
      // We have a handler, meaning Workbox is going to handle the route.
      // print the routing details to the console.
      logger.groupCollapsed(`Router is responding to: ${getFriendlyURL(url)}`);
      debugMessages.forEach(msg => {
        if (Array.isArray(msg)) {
          logger.log(...msg);
        } else {
          logger.log(msg);
        }
      });
      logger.groupEnd();
    } // Wrap in try and catch in case the handle method throws a synchronous
    // error. It should still callback to the catch handler.


    let responsePromise;

    try {
      responsePromise = handler.handle({
        url,
        request,
        event,
        params
      });
    } catch (err) {
      responsePromise = Promise.reject(err);
    } // Get route's catch handler, if it exists


    const catchHandler = route && route.catchHandler;

    if (responsePromise instanceof Promise && (this._catchHandler || catchHandler)) {
      responsePromise = responsePromise.catch(async err => {
        // If there's a route catch handler, process that first
        if (catchHandler) {
          {
            // Still include URL here as it will be async from the console group
            // and may not make sense without the URL
            logger.groupCollapsed(`Error thrown when responding to: ` + ` ${getFriendlyURL(url)}. Falling back to route's Catch Handler.`);
            logger.error(`Error thrown by:`, route);
            logger.error(err);
            logger.groupEnd();
          }

          try {
            return await catchHandler.handle({
              url,
              request,
              event,
              params
            });
          } catch (catchErr) {
            if (catchErr instanceof Error) {
              err = catchErr;
            }
          }
        }

        if (this._catchHandler) {
          {
            // Still include URL here as it will be async from the console group
            // and may not make sense without the URL
            logger.groupCollapsed(`Error thrown when responding to: ` + ` ${getFriendlyURL(url)}. Falling back to global Catch Handler.`);
            logger.error(`Error thrown by:`, route);
            logger.error(err);
            logger.groupEnd();
          }

          return this._catchHandler.handle({
            url,
            request,
            event
          });
        }

        throw err;
      });
    }

    return responsePromise;
  }
  /**
   * Checks a request and URL (and optionally an event) against the list of
   * registered routes, and if there's a match, returns the corresponding
   * route along with any params generated by the match.
   *
   * @param {Object} options
   * @param {URL} options.url
   * @param {boolean} options.sameOrigin The result of comparing `url.origin`
   *     against the current origin.
   * @param {Request} options.request The request to match.
   * @param {Event} options.event The corresponding event.
   * @return {Object} An object with `route` and `params` properties.
   *     They are populated if a matching route was found or `undefined`
   *     otherwise.
   */


  findMatchingRoute({
    url,
    sameOrigin,
    request,
    event
  }) {
    const routes = this._routes.get(request.method) || [];

    for (const route of routes) {
      let params; // route.match returns type any, not possible to change right now.
      // eslint-disable-next-line @typescript-eslint/no-unsafe-assignment

      const matchResult = route.match({
        url,
        sameOrigin,
        request,
        event
      });

      if (matchResult) {
        {
          // Warn developers that using an async matchCallback is almost always
          // not the right thing to do.
          if (matchResult instanceof Promise) {
            logger.warn(`While routing ${getFriendlyURL(url)}, an async ` + `matchCallback function was used. Please convert the ` + `following route to use a synchronous matchCallback function:`, route);
          }
        } // See https://github.com/GoogleChrome/workbox/issues/2079
        // eslint-disable-next-line @typescript-eslint/no-unsafe-assignment


        params = matchResult;

        if (Array.isArray(params) && params.length === 0) {
          // Instead of passing an empty array in as params, use undefined.
          params = undefined;
        } else if (matchResult.constructor === Object && // eslint-disable-line
        Object.keys(matchResult).length === 0) {
          // Instead of passing an empty object in as params, use undefined.
          params = undefined;
        } else if (typeof matchResult === 'boolean') {
          // For the boolean value true (rather than just something truth-y),
          // don't set params.
          // See https://github.com/GoogleChrome/workbox/pull/2134#issuecomment-513924353
          params = undefined;
        } // Return early if have a match.


        return {
          route,
          params
        };
      }
    } // If no match was found above, return and empty object.


    return {};
  }
  /**
   * Define a default `handler` that's called when no routes explicitly
   * match the incoming request.
   *
   * Each HTTP method ('GET', 'POST', etc.) gets its own default handler.
   *
   * Without a default handler, unmatched requests will go against the
   * network as if there were no service worker present.
   *
   * @param {module:workbox-routing~handlerCallback} handler A callback
   * function that returns a Promise resulting in a Response.
   * @param {string} [method='GET'] The HTTP method to associate with this
   * default handler. Each method has its own default.
   */


  setDefaultHandler(handler, method = defaultMethod) {
    this._defaultHandlerMap.set(method, normalizeHandler(handler));
  }
  /**
   * If a Route throws an error while handling a request, this `handler`
   * will be called and given a chance to provide a response.
   *
   * @param {module:workbox-routing~handlerCallback} handler A callback
   * function that returns a Promise resulting in a Response.
   */


  setCatchHandler(handler) {
    this._catchHandler = normalizeHandler(handler);
  }
  /**
   * Registers a route with the router.
   *
   * @param {module:workbox-routing.Route} route The route to register.
   */


  registerRoute(route) {
    {
      finalAssertExports.isType(route, 'object', {
        moduleName: 'workbox-routing',
        className: 'Router',
        funcName: 'registerRoute',
        paramName: 'route'
      });
      finalAssertExports.hasMethod(route, 'match', {
        moduleName: 'workbox-routing',
        className: 'Router',
        funcName: 'registerRoute',
        paramName: 'route'
      });
      finalAssertExports.isType(route.handler, 'object', {
        moduleName: 'workbox-routing',
        className: 'Router',
        funcName: 'registerRoute',
        paramName: 'route'
      });
      finalAssertExports.hasMethod(route.handler, 'handle', {
        moduleName: 'workbox-routing',
        className: 'Router',
        funcName: 'registerRoute',
        paramName: 'route.handler'
      });
      finalAssertExports.isType(route.method, 'string', {
        moduleName: 'workbox-routing',
        className: 'Router',
        funcName: 'registerRoute',
        paramName: 'route.method'
      });
    }

    if (!this._routes.has(route.method)) {
      this._routes.set(route.method, []);
    } // Give precedence to all of the earlier routes by adding this additional
    // route to the end of the array.


    this._routes.get(route.method).push(route);
  }
  /**
   * Unregisters a route with the router.
   *
   * @param {module:workbox-routing.Route} route The route to unregister.
   */


  unregisterRoute(route) {
    if (!this._routes.has(route.method)) {
      throw new WorkboxError('unregister-route-but-not-found-with-method', {
        method: route.method
      });
    }

    const routeIndex = this._routes.get(route.method).indexOf(route);

    if (routeIndex > -1) {
      this._routes.get(route.method).splice(routeIndex, 1);
    } else {
      throw new WorkboxError('unregister-route-route-not-registered');
    }
  }

}

/*
  Copyright 2019 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
let defaultRouter;
/**
 * Creates a new, singleton Router instance if one does not exist. If one
 * does already exist, that instance is returned.
 *
 * @private
 * @return {Router}
 */

const getOrCreateDefaultRouter = () => {
  if (!defaultRouter) {
    defaultRouter = new Router(); // The helpers that use the default Router assume these listeners exist.

    defaultRouter.addFetchListener();
    defaultRouter.addCacheListener();
  }

  return defaultRouter;
};

/*
  Copyright 2019 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
/**
 * Easily register a RegExp, string, or function with a caching
 * strategy to a singleton Router instance.
 *
 * This method will generate a Route for you if needed and
 * call [registerRoute()]{@link module:workbox-routing.Router#registerRoute}.
 *
 * @param {RegExp|string|module:workbox-routing.Route~matchCallback|module:workbox-routing.Route} capture
 * If the capture param is a `Route`, all other arguments will be ignored.
 * @param {module:workbox-routing~handlerCallback} [handler] A callback
 * function that returns a Promise resulting in a Response. This parameter
 * is required if `capture` is not a `Route` object.
 * @param {string} [method='GET'] The HTTP method to match the Route
 * against.
 * @return {module:workbox-routing.Route} The generated `Route`(Useful for
 * unregistering).
 *
 * @memberof module:workbox-routing
 */

function registerRoute(capture, handler, method) {
  let route;

  if (typeof capture === 'string') {
    const captureUrl = new URL(capture, location.href);

    {
      if (!(capture.startsWith('/') || capture.startsWith('http'))) {
        throw new WorkboxError('invalid-string', {
          moduleName: 'workbox-routing',
          funcName: 'registerRoute',
          paramName: 'capture'
        });
      } // We want to check if Express-style wildcards are in the pathname only.
      // TODO: Remove this log message in v4.


      const valueToCheck = capture.startsWith('http') ? captureUrl.pathname : capture; // See https://github.com/pillarjs/path-to-regexp#parameters

      const wildcards = '[*:?+]';

      if (new RegExp(`${wildcards}`).exec(valueToCheck)) {
        logger.debug(`The '$capture' parameter contains an Express-style wildcard ` + `character (${wildcards}). Strings are now always interpreted as ` + `exact matches; use a RegExp for partial or wildcard matches.`);
      }
    }

    const matchCallback = ({
      url
    }) => {
      {
        if (url.pathname === captureUrl.pathname && url.origin !== captureUrl.origin) {
          logger.debug(`${capture} only partially matches the cross-origin URL ` + `${url.toString()}. This route will only handle cross-origin requests ` + `if they match the entire URL.`);
        }
      }

      return url.href === captureUrl.href;
    }; // If `capture` is a string then `handler` and `method` must be present.


    route = new Route(matchCallback, handler, method);
  } else if (capture instanceof RegExp) {
    // If `capture` is a `RegExp` then `handler` and `method` must be present.
    route = new RegExpRoute(capture, handler, method);
  } else if (typeof capture === 'function') {
    // If `capture` is a function then `handler` and `method` must be present.
    route = new Route(capture, handler, method);
  } else if (capture instanceof Route) {
    route = capture;
  } else {
    throw new WorkboxError('unsupported-route-type', {
      moduleName: 'workbox-routing',
      funcName: 'registerRoute',
      paramName: 'capture'
    });
  }

  const defaultRouter = getOrCreateDefaultRouter();
  defaultRouter.registerRoute(route);
  return route;
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
  Copyright 2020 Google LLC
  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
/**
 * A utility method that makes it easier to use `event.waitUntil` with
 * async functions and return the result.
 *
 * @param {ExtendableEvent} event
 * @param {Function} asyncFn
 * @return {Function}
 * @private
 */

function waitUntil(event, asyncFn) {
  const returnPromise = asyncFn();
  event.waitUntil(returnPromise);
  return returnPromise;
}

try {
  self['workbox:precaching:6.4.1'] && _();
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
  Copyright 2020 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
/**
 * A plugin, designed to be used with PrecacheController, to determine the
 * of assets that were updated (or not updated) during the install event.
 *
 * @private
 */

class PrecacheInstallReportPlugin {
  constructor() {
    this.updatedURLs = [];
    this.notUpdatedURLs = [];

    this.handlerWillStart = async ({
      request,
      state
    }) => {
      // TODO: `state` should never be undefined...
      if (state) {
        state.originalRequest = request;
      }
    };

    this.cachedResponseWillBeUsed = async ({
      event,
      state,
      cachedResponse
    }) => {
      if (event.type === 'install') {
        if (state && state.originalRequest && state.originalRequest instanceof Request) {
          // TODO: `state` should never be undefined...
          const url = state.originalRequest.url;

          if (cachedResponse) {
            this.notUpdatedURLs.push(url);
          } else {
            this.updatedURLs.push(url);
          }
        }
      }

      return cachedResponse;
    };
  }

}

/*
  Copyright 2020 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
/**
 * A plugin, designed to be used with PrecacheController, to translate URLs into
 * the corresponding cache key, based on the current revision info.
 *
 * @private
 */

class PrecacheCacheKeyPlugin {
  constructor({
    precacheController
  }) {
    this.cacheKeyWillBeUsed = async ({
      request,
      params
    }) => {
      // Params is type any, can't change right now.

      /* eslint-disable */
      const cacheKey = (params === null || params === void 0 ? void 0 : params.cacheKey) || this._precacheController.getCacheKeyForURL(request.url);
      /* eslint-enable */


      return cacheKey ? new Request(cacheKey, {
        headers: request.headers
      }) : request;
    };

    this._precacheController = precacheController;
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
 * This method is intentionally limited to same-origin responses, regardless of
 * whether CORS was used or not.
 *
 * @param {Response} response
 * @param {Function} modifier
 * @memberof module:workbox-core
 */

async function copyResponse(response, modifier) {
  let origin = null; // If response.url isn't set, assume it's cross-origin and keep origin null.

  if (response.url) {
    const responseURL = new URL(response.url);
    origin = responseURL.origin;
  }

  if (origin !== self.location.origin) {
    throw new WorkboxError('cross-origin-copy-response', {
      origin
    });
  }

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

/*
  Copyright 2020 Google LLC
  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/

function stripParams(fullURL, ignoreParams) {
  const strippedURL = new URL(fullURL);

  for (const param of ignoreParams) {
    strippedURL.searchParams.delete(param);
  }

  return strippedURL.href;
}
/**
 * Matches an item in the cache, ignoring specific URL params. This is similar
 * to the `ignoreSearch` option, but it allows you to ignore just specific
 * params (while continuing to match on the others).
 *
 * @private
 * @param {Cache} cache
 * @param {Request} request
 * @param {Object} matchOptions
 * @param {Array<string>} ignoreParams
 * @return {Promise<Response|undefined>}
 */


async function cacheMatchIgnoreParams(cache, request, ignoreParams, matchOptions) {
  const strippedRequestURL = stripParams(request.url, ignoreParams); // If the request doesn't include any ignored params, match as normal.

  if (request.url === strippedRequestURL) {
    return cache.match(request, matchOptions);
  } // Otherwise, match by comparing keys


  const keysOptions = Object.assign(Object.assign({}, matchOptions), {
    ignoreSearch: true
  });
  const cacheKeys = await cache.keys(request, keysOptions);

  for (const cacheKey of cacheKeys) {
    const strippedCacheKeyURL = stripParams(cacheKey.url, ignoreParams);

    if (strippedRequestURL === strippedCacheKeyURL) {
      return cache.match(cacheKey, matchOptions);
    }
  }

  return;
}

/*
  Copyright 2018 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
/**
 * The Deferred class composes Promises in a way that allows for them to be
 * resolved or rejected from outside the constructor. In most cases promises
 * should be used directly, but Deferreds can be necessary when the logic to
 * resolve a promise must be separate.
 *
 * @private
 */

class Deferred {
  /**
   * Creates a promise and exposes its resolve and reject functions as methods.
   */
  constructor() {
    this.promise = new Promise((resolve, reject) => {
      this.resolve = resolve;
      this.reject = reject;
    });
  }

}

/*
  Copyright 2018 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
// Can't change Function type right now.
// eslint-disable-next-line @typescript-eslint/ban-types

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
  Copyright 2019 Google LLC
  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
/**
 * Returns a promise that resolves and the passed number of milliseconds.
 * This utility is an async/await-friendly version of `setTimeout`.
 *
 * @param {number} ms
 * @return {Promise}
 * @private
 */

function timeout(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

try {
  self['workbox:strategies:6.4.1'] && _();
} catch (e) {}

/*
  Copyright 2020 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/

function toRequest(input) {
  return typeof input === 'string' ? new Request(input) : input;
}
/**
 * A class created every time a Strategy instance instance calls
 * [handle()]{@link module:workbox-strategies.Strategy~handle} or
 * [handleAll()]{@link module:workbox-strategies.Strategy~handleAll} that wraps all fetch and
 * cache actions around plugin callbacks and keeps track of when the strategy
 * is "done" (i.e. all added `event.waitUntil()` promises have resolved).
 *
 * @memberof module:workbox-strategies
 */


class StrategyHandler {
  /**
   * Creates a new instance associated with the passed strategy and event
   * that's handling the request.
   *
   * The constructor also initializes the state that will be passed to each of
   * the plugins handling this request.
   *
   * @param {module:workbox-strategies.Strategy} strategy
   * @param {Object} options
   * @param {Request|string} options.request A request to run this strategy for.
   * @param {ExtendableEvent} options.event The event associated with the
   *     request.
   * @param {URL} [options.url]
   * @param {*} [options.params]
   *     [match callback]{@link module:workbox-routing~matchCallback},
   *     (if applicable).
   */
  constructor(strategy, options) {
    this._cacheKeys = {};
    /**
     * The request the strategy is performing (passed to the strategy's
     * `handle()` or `handleAll()` method).
     * @name request
     * @instance
     * @type {Request}
     * @memberof module:workbox-strategies.StrategyHandler
     */

    /**
     * The event associated with this request.
     * @name event
     * @instance
     * @type {ExtendableEvent}
     * @memberof module:workbox-strategies.StrategyHandler
     */

    /**
     * A `URL` instance of `request.url` (if passed to the strategy's
     * `handle()` or `handleAll()` method).
     * Note: the `url` param will be present if the strategy was invoked
     * from a workbox `Route` object.
     * @name url
     * @instance
     * @type {URL|undefined}
     * @memberof module:workbox-strategies.StrategyHandler
     */

    /**
     * A `param` value (if passed to the strategy's
     * `handle()` or `handleAll()` method).
     * Note: the `param` param will be present if the strategy was invoked
     * from a workbox `Route` object and the
     * [match callback]{@link module:workbox-routing~matchCallback} returned
     * a truthy value (it will be that value).
     * @name params
     * @instance
     * @type {*|undefined}
     * @memberof module:workbox-strategies.StrategyHandler
     */

    {
      finalAssertExports.isInstance(options.event, ExtendableEvent, {
        moduleName: 'workbox-strategies',
        className: 'StrategyHandler',
        funcName: 'constructor',
        paramName: 'options.event'
      });
    }

    Object.assign(this, options);
    this.event = options.event;
    this._strategy = strategy;
    this._handlerDeferred = new Deferred();
    this._extendLifetimePromises = []; // Copy the plugins list (since it's mutable on the strategy),
    // so any mutations don't affect this handler instance.

    this._plugins = [...strategy.plugins];
    this._pluginStateMap = new Map();

    for (const plugin of this._plugins) {
      this._pluginStateMap.set(plugin, {});
    }

    this.event.waitUntil(this._handlerDeferred.promise);
  }
  /**
   * Fetches a given request (and invokes any applicable plugin callback
   * methods) using the `fetchOptions` (for non-navigation requests) and
   * `plugins` defined on the `Strategy` object.
   *
   * The following plugin lifecycle methods are invoked when using this method:
   * - `requestWillFetch()`
   * - `fetchDidSucceed()`
   * - `fetchDidFail()`
   *
   * @param {Request|string} input The URL or request to fetch.
   * @return {Promise<Response>}
   */


  async fetch(input) {
    const {
      event
    } = this;
    let request = toRequest(input);

    if (request.mode === 'navigate' && event instanceof FetchEvent && event.preloadResponse) {
      const possiblePreloadResponse = await event.preloadResponse;

      if (possiblePreloadResponse) {
        {
          logger.log(`Using a preloaded navigation response for ` + `'${getFriendlyURL(request.url)}'`);
        }

        return possiblePreloadResponse;
      }
    } // If there is a fetchDidFail plugin, we need to save a clone of the
    // original request before it's either modified by a requestWillFetch
    // plugin or before the original request's body is consumed via fetch().


    const originalRequest = this.hasCallback('fetchDidFail') ? request.clone() : null;

    try {
      for (const cb of this.iterateCallbacks('requestWillFetch')) {
        request = await cb({
          request: request.clone(),
          event
        });
      }
    } catch (err) {
      if (err instanceof Error) {
        throw new WorkboxError('plugin-error-request-will-fetch', {
          thrownErrorMessage: err.message
        });
      }
    } // The request can be altered by plugins with `requestWillFetch` making
    // the original request (most likely from a `fetch` event) different
    // from the Request we make. Pass both to `fetchDidFail` to aid debugging.


    const pluginFilteredRequest = request.clone();

    try {
      let fetchResponse; // See https://github.com/GoogleChrome/workbox/issues/1796

      fetchResponse = await fetch(request, request.mode === 'navigate' ? undefined : this._strategy.fetchOptions);

      if ("development" !== 'production') {
        logger.debug(`Network request for ` + `'${getFriendlyURL(request.url)}' returned a response with ` + `status '${fetchResponse.status}'.`);
      }

      for (const callback of this.iterateCallbacks('fetchDidSucceed')) {
        fetchResponse = await callback({
          event,
          request: pluginFilteredRequest,
          response: fetchResponse
        });
      }

      return fetchResponse;
    } catch (error) {
      {
        logger.log(`Network request for ` + `'${getFriendlyURL(request.url)}' threw an error.`, error);
      } // `originalRequest` will only exist if a `fetchDidFail` callback
      // is being used (see above).


      if (originalRequest) {
        await this.runCallbacks('fetchDidFail', {
          error: error,
          event,
          originalRequest: originalRequest.clone(),
          request: pluginFilteredRequest.clone()
        });
      }

      throw error;
    }
  }
  /**
   * Calls `this.fetch()` and (in the background) runs `this.cachePut()` on
   * the response generated by `this.fetch()`.
   *
   * The call to `this.cachePut()` automatically invokes `this.waitUntil()`,
   * so you do not have to manually call `waitUntil()` on the event.
   *
   * @param {Request|string} input The request or URL to fetch and cache.
   * @return {Promise<Response>}
   */


  async fetchAndCachePut(input) {
    const response = await this.fetch(input);
    const responseClone = response.clone();
    void this.waitUntil(this.cachePut(input, responseClone));
    return response;
  }
  /**
   * Matches a request from the cache (and invokes any applicable plugin
   * callback methods) using the `cacheName`, `matchOptions`, and `plugins`
   * defined on the strategy object.
   *
   * The following plugin lifecycle methods are invoked when using this method:
   * - cacheKeyWillByUsed()
   * - cachedResponseWillByUsed()
   *
   * @param {Request|string} key The Request or URL to use as the cache key.
   * @return {Promise<Response|undefined>} A matching response, if found.
   */


  async cacheMatch(key) {
    const request = toRequest(key);
    let cachedResponse;
    const {
      cacheName,
      matchOptions
    } = this._strategy;
    const effectiveRequest = await this.getCacheKey(request, 'read');
    const multiMatchOptions = Object.assign(Object.assign({}, matchOptions), {
      cacheName
    });
    cachedResponse = await caches.match(effectiveRequest, multiMatchOptions);

    {
      if (cachedResponse) {
        logger.debug(`Found a cached response in '${cacheName}'.`);
      } else {
        logger.debug(`No cached response found in '${cacheName}'.`);
      }
    }

    for (const callback of this.iterateCallbacks('cachedResponseWillBeUsed')) {
      cachedResponse = (await callback({
        cacheName,
        matchOptions,
        cachedResponse,
        request: effectiveRequest,
        event: this.event
      })) || undefined;
    }

    return cachedResponse;
  }
  /**
   * Puts a request/response pair in the cache (and invokes any applicable
   * plugin callback methods) using the `cacheName` and `plugins` defined on
   * the strategy object.
   *
   * The following plugin lifecycle methods are invoked when using this method:
   * - cacheKeyWillByUsed()
   * - cacheWillUpdate()
   * - cacheDidUpdate()
   *
   * @param {Request|string} key The request or URL to use as the cache key.
   * @param {Response} response The response to cache.
   * @return {Promise<boolean>} `false` if a cacheWillUpdate caused the response
   * not be cached, and `true` otherwise.
   */


  async cachePut(key, response) {
    const request = toRequest(key); // Run in the next task to avoid blocking other cache reads.
    // https://github.com/w3c/ServiceWorker/issues/1397

    await timeout(0);
    const effectiveRequest = await this.getCacheKey(request, 'write');

    {
      if (effectiveRequest.method && effectiveRequest.method !== 'GET') {
        throw new WorkboxError('attempt-to-cache-non-get-request', {
          url: getFriendlyURL(effectiveRequest.url),
          method: effectiveRequest.method
        });
      } // See https://github.com/GoogleChrome/workbox/issues/2818


      const vary = response.headers.get('Vary');

      if (vary) {
        logger.debug(`The response for ${getFriendlyURL(effectiveRequest.url)} ` + `has a 'Vary: ${vary}' header. ` + `Consider setting the {ignoreVary: true} option on your strategy ` + `to ensure cache matching and deletion works as expected.`);
      }
    }

    if (!response) {
      {
        logger.error(`Cannot cache non-existent response for ` + `'${getFriendlyURL(effectiveRequest.url)}'.`);
      }

      throw new WorkboxError('cache-put-with-no-response', {
        url: getFriendlyURL(effectiveRequest.url)
      });
    }

    const responseToCache = await this._ensureResponseSafeToCache(response);

    if (!responseToCache) {
      {
        logger.debug(`Response '${getFriendlyURL(effectiveRequest.url)}' ` + `will not be cached.`, responseToCache);
      }

      return false;
    }

    const {
      cacheName,
      matchOptions
    } = this._strategy;
    const cache = await self.caches.open(cacheName);
    const hasCacheUpdateCallback = this.hasCallback('cacheDidUpdate');
    const oldResponse = hasCacheUpdateCallback ? await cacheMatchIgnoreParams( // TODO(philipwalton): the `__WB_REVISION__` param is a precaching
    // feature. Consider into ways to only add this behavior if using
    // precaching.
    cache, effectiveRequest.clone(), ['__WB_REVISION__'], matchOptions) : null;

    {
      logger.debug(`Updating the '${cacheName}' cache with a new Response ` + `for ${getFriendlyURL(effectiveRequest.url)}.`);
    }

    try {
      await cache.put(effectiveRequest, hasCacheUpdateCallback ? responseToCache.clone() : responseToCache);
    } catch (error) {
      if (error instanceof Error) {
        // See https://developer.mozilla.org/en-US/docs/Web/API/DOMException#exception-QuotaExceededError
        if (error.name === 'QuotaExceededError') {
          await executeQuotaErrorCallbacks();
        }

        throw error;
      }
    }

    for (const callback of this.iterateCallbacks('cacheDidUpdate')) {
      await callback({
        cacheName,
        oldResponse,
        newResponse: responseToCache.clone(),
        request: effectiveRequest,
        event: this.event
      });
    }

    return true;
  }
  /**
   * Checks the list of plugins for the `cacheKeyWillBeUsed` callback, and
   * executes any of those callbacks found in sequence. The final `Request`
   * object returned by the last plugin is treated as the cache key for cache
   * reads and/or writes. If no `cacheKeyWillBeUsed` plugin callbacks have
   * been registered, the passed request is returned unmodified
   *
   * @param {Request} request
   * @param {string} mode
   * @return {Promise<Request>}
   */


  async getCacheKey(request, mode) {
    const key = `${request.url} | ${mode}`;

    if (!this._cacheKeys[key]) {
      let effectiveRequest = request;

      for (const callback of this.iterateCallbacks('cacheKeyWillBeUsed')) {
        effectiveRequest = toRequest(await callback({
          mode,
          request: effectiveRequest,
          event: this.event,
          // params has a type any can't change right now.
          params: this.params // eslint-disable-line

        }));
      }

      this._cacheKeys[key] = effectiveRequest;
    }

    return this._cacheKeys[key];
  }
  /**
   * Returns true if the strategy has at least one plugin with the given
   * callback.
   *
   * @param {string} name The name of the callback to check for.
   * @return {boolean}
   */


  hasCallback(name) {
    for (const plugin of this._strategy.plugins) {
      if (name in plugin) {
        return true;
      }
    }

    return false;
  }
  /**
   * Runs all plugin callbacks matching the given name, in order, passing the
   * given param object (merged ith the current plugin state) as the only
   * argument.
   *
   * Note: since this method runs all plugins, it's not suitable for cases
   * where the return value of a callback needs to be applied prior to calling
   * the next callback. See
   * [`iterateCallbacks()`]{@link module:workbox-strategies.StrategyHandler#iterateCallbacks}
   * below for how to handle that case.
   *
   * @param {string} name The name of the callback to run within each plugin.
   * @param {Object} param The object to pass as the first (and only) param
   *     when executing each callback. This object will be merged with the
   *     current plugin state prior to callback execution.
   */


  async runCallbacks(name, param) {
    for (const callback of this.iterateCallbacks(name)) {
      // TODO(philipwalton): not sure why `any` is needed. It seems like
      // this should work with `as WorkboxPluginCallbackParam[C]`.
      await callback(param);
    }
  }
  /**
   * Accepts a callback and returns an iterable of matching plugin callbacks,
   * where each callback is wrapped with the current handler state (i.e. when
   * you call each callback, whatever object parameter you pass it will
   * be merged with the plugin's current state).
   *
   * @param {string} name The name fo the callback to run
   * @return {Array<Function>}
   */


  *iterateCallbacks(name) {
    for (const plugin of this._strategy.plugins) {
      if (typeof plugin[name] === 'function') {
        const state = this._pluginStateMap.get(plugin);

        const statefulCallback = param => {
          const statefulParam = Object.assign(Object.assign({}, param), {
            state
          }); // TODO(philipwalton): not sure why `any` is needed. It seems like
          // this should work with `as WorkboxPluginCallbackParam[C]`.

          return plugin[name](statefulParam);
        };

        yield statefulCallback;
      }
    }
  }
  /**
   * Adds a promise to the
   * [extend lifetime promises]{@link https://w3c.github.io/ServiceWorker/#extendableevent-extend-lifetime-promises}
   * of the event event associated with the request being handled (usually a
   * `FetchEvent`).
   *
   * Note: you can await
   * [`doneWaiting()`]{@link module:workbox-strategies.StrategyHandler~doneWaiting}
   * to know when all added promises have settled.
   *
   * @param {Promise} promise A promise to add to the extend lifetime promises
   *     of the event that triggered the request.
   */


  waitUntil(promise) {
    this._extendLifetimePromises.push(promise);

    return promise;
  }
  /**
   * Returns a promise that resolves once all promises passed to
   * [`waitUntil()`]{@link module:workbox-strategies.StrategyHandler~waitUntil}
   * have settled.
   *
   * Note: any work done after `doneWaiting()` settles should be manually
   * passed to an event's `waitUntil()` method (not this handler's
   * `waitUntil()` method), otherwise the service worker thread my be killed
   * prior to your work completing.
   */


  async doneWaiting() {
    let promise;

    while (promise = this._extendLifetimePromises.shift()) {
      await promise;
    }
  }
  /**
   * Stops running the strategy and immediately resolves any pending
   * `waitUntil()` promises.
   */


  destroy() {
    this._handlerDeferred.resolve(null);
  }
  /**
   * This method will call cacheWillUpdate on the available plugins (or use
   * status === 200) to determine if the Response is safe and valid to cache.
   *
   * @param {Request} options.request
   * @param {Response} options.response
   * @return {Promise<Response|undefined>}
   *
   * @private
   */


  async _ensureResponseSafeToCache(response) {
    let responseToCache = response;
    let pluginsUsed = false;

    for (const callback of this.iterateCallbacks('cacheWillUpdate')) {
      responseToCache = (await callback({
        request: this.request,
        response: responseToCache,
        event: this.event
      })) || undefined;
      pluginsUsed = true;

      if (!responseToCache) {
        break;
      }
    }

    if (!pluginsUsed) {
      if (responseToCache && responseToCache.status !== 200) {
        responseToCache = undefined;
      }

      {
        if (responseToCache) {
          if (responseToCache.status !== 200) {
            if (responseToCache.status === 0) {
              logger.warn(`The response for '${this.request.url}' ` + `is an opaque response. The caching strategy that you're ` + `using will not cache opaque responses by default.`);
            } else {
              logger.debug(`The response for '${this.request.url}' ` + `returned a status code of '${response.status}' and won't ` + `be cached as a result.`);
            }
          }
        }
      }
    }

    return responseToCache;
  }

}

/*
  Copyright 2020 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
/**
 * An abstract base class that all other strategy classes must extend from:
 *
 * @memberof module:workbox-strategies
 */

class Strategy {
  /**
   * Creates a new instance of the strategy and sets all documented option
   * properties as public instance properties.
   *
   * Note: if a custom strategy class extends the base Strategy class and does
   * not need more than these properties, it does not need to define its own
   * constructor.
   *
   * @param {Object} [options]
   * @param {string} [options.cacheName] Cache name to store and retrieve
   * requests. Defaults to the cache names provided by
   * [workbox-core]{@link module:workbox-core.cacheNames}.
   * @param {Array<Object>} [options.plugins] [Plugins]{@link https://developers.google.com/web/tools/workbox/guides/using-plugins}
   * to use in conjunction with this caching strategy.
   * @param {Object} [options.fetchOptions] Values passed along to the
   * [`init`](https://developer.mozilla.org/en-US/docs/Web/API/WindowOrWorkerGlobalScope/fetch#Parameters)
   * of [non-navigation](https://github.com/GoogleChrome/workbox/issues/1796)
   * `fetch()` requests made by this strategy.
   * @param {Object} [options.matchOptions] The
   * [`CacheQueryOptions`]{@link https://w3c.github.io/ServiceWorker/#dictdef-cachequeryoptions}
   * for any `cache.match()` or `cache.put()` calls made by this strategy.
   */
  constructor(options = {}) {
    /**
     * Cache name to store and retrieve
     * requests. Defaults to the cache names provided by
     * [workbox-core]{@link module:workbox-core.cacheNames}.
     *
     * @type {string}
     */
    this.cacheName = cacheNames.getRuntimeName(options.cacheName);
    /**
     * The list
     * [Plugins]{@link https://developers.google.com/web/tools/workbox/guides/using-plugins}
     * used by this strategy.
     *
     * @type {Array<Object>}
     */

    this.plugins = options.plugins || [];
    /**
     * Values passed along to the
     * [`init`]{@link https://developer.mozilla.org/en-US/docs/Web/API/WindowOrWorkerGlobalScope/fetch#Parameters}
     * of all fetch() requests made by this strategy.
     *
     * @type {Object}
     */

    this.fetchOptions = options.fetchOptions;
    /**
     * The
     * [`CacheQueryOptions`]{@link https://w3c.github.io/ServiceWorker/#dictdef-cachequeryoptions}
     * for any `cache.match()` or `cache.put()` calls made by this strategy.
     *
     * @type {Object}
     */

    this.matchOptions = options.matchOptions;
  }
  /**
   * Perform a request strategy and returns a `Promise` that will resolve with
   * a `Response`, invoking all relevant plugin callbacks.
   *
   * When a strategy instance is registered with a Workbox
   * [route]{@link module:workbox-routing.Route}, this method is automatically
   * called when the route matches.
   *
   * Alternatively, this method can be used in a standalone `FetchEvent`
   * listener by passing it to `event.respondWith()`.
   *
   * @param {FetchEvent|Object} options A `FetchEvent` or an object with the
   *     properties listed below.
   * @param {Request|string} options.request A request to run this strategy for.
   * @param {ExtendableEvent} options.event The event associated with the
   *     request.
   * @param {URL} [options.url]
   * @param {*} [options.params]
   */


  handle(options) {
    const [responseDone] = this.handleAll(options);
    return responseDone;
  }
  /**
   * Similar to [`handle()`]{@link module:workbox-strategies.Strategy~handle}, but
   * instead of just returning a `Promise` that resolves to a `Response` it
   * it will return an tuple of [response, done] promises, where the former
   * (`response`) is equivalent to what `handle()` returns, and the latter is a
   * Promise that will resolve once any promises that were added to
   * `event.waitUntil()` as part of performing the strategy have completed.
   *
   * You can await the `done` promise to ensure any extra work performed by
   * the strategy (usually caching responses) completes successfully.
   *
   * @param {FetchEvent|Object} options A `FetchEvent` or an object with the
   *     properties listed below.
   * @param {Request|string} options.request A request to run this strategy for.
   * @param {ExtendableEvent} options.event The event associated with the
   *     request.
   * @param {URL} [options.url]
   * @param {*} [options.params]
   * @return {Array<Promise>} A tuple of [response, done]
   *     promises that can be used to determine when the response resolves as
   *     well as when the handler has completed all its work.
   */


  handleAll(options) {
    // Allow for flexible options to be passed.
    if (options instanceof FetchEvent) {
      options = {
        event: options,
        request: options.request
      };
    }

    const event = options.event;
    const request = typeof options.request === 'string' ? new Request(options.request) : options.request;
    const params = 'params' in options ? options.params : undefined;
    const handler = new StrategyHandler(this, {
      event,
      request,
      params
    });

    const responseDone = this._getResponse(handler, request, event);

    const handlerDone = this._awaitComplete(responseDone, handler, request, event); // Return an array of promises, suitable for use with Promise.all().


    return [responseDone, handlerDone];
  }

  async _getResponse(handler, request, event) {
    await handler.runCallbacks('handlerWillStart', {
      event,
      request
    });
    let response = undefined;

    try {
      response = await this._handle(request, handler); // The "official" Strategy subclasses all throw this error automatically,
      // but in case a third-party Strategy doesn't, ensure that we have a
      // consistent failure when there's no response or an error response.

      if (!response || response.type === 'error') {
        throw new WorkboxError('no-response', {
          url: request.url
        });
      }
    } catch (error) {
      if (error instanceof Error) {
        for (const callback of handler.iterateCallbacks('handlerDidError')) {
          response = await callback({
            error,
            event,
            request
          });

          if (response) {
            break;
          }
        }
      }

      if (!response) {
        throw error;
      } else {
        logger.log(`While responding to '${getFriendlyURL(request.url)}', ` + `an ${error instanceof Error ? error.toString() : ''} error occurred. Using a fallback response provided by ` + `a handlerDidError plugin.`);
      }
    }

    for (const callback of handler.iterateCallbacks('handlerWillRespond')) {
      response = await callback({
        event,
        request,
        response
      });
    }

    return response;
  }

  async _awaitComplete(responseDone, handler, request, event) {
    let response;
    let error;

    try {
      response = await responseDone;
    } catch (error) {// Ignore errors, as response errors should be caught via the `response`
      // promise above. The `done` promise will only throw for errors in
      // promises passed to `handler.waitUntil()`.
    }

    try {
      await handler.runCallbacks('handlerDidRespond', {
        event,
        request,
        response
      });
      await handler.doneWaiting();
    } catch (waitUntilError) {
      if (waitUntilError instanceof Error) {
        error = waitUntilError;
      }
    }

    await handler.runCallbacks('handlerDidComplete', {
      event,
      request,
      response,
      error: error
    });
    handler.destroy();

    if (error) {
      throw error;
    }
  }

}
/**
 * Classes extending the `Strategy` based class should implement this method,
 * and leverage the [`handler`]{@link module:workbox-strategies.StrategyHandler}
 * arg to perform all fetching and cache logic, which will ensure all relevant
 * cache, cache options, fetch options and plugins are used (per the current
 * strategy instance).
 *
 * @name _handle
 * @instance
 * @abstract
 * @function
 * @param {Request} request
 * @param {module:workbox-strategies.StrategyHandler} handler
 * @return {Promise<Response>}
 *
 * @memberof module:workbox-strategies.Strategy
 */

/*
  Copyright 2020 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
/**
 * A [Strategy]{@link module:workbox-strategies.Strategy} implementation
 * specifically designed to work with
 * [PrecacheController]{@link module:workbox-precaching.PrecacheController}
 * to both cache and fetch precached assets.
 *
 * Note: an instance of this class is created automatically when creating a
 * `PrecacheController`; it's generally not necessary to create this yourself.
 *
 * @extends module:workbox-strategies.Strategy
 * @memberof module:workbox-precaching
 */

class PrecacheStrategy extends Strategy {
  /**
   *
   * @param {Object} [options]
   * @param {string} [options.cacheName] Cache name to store and retrieve
   * requests. Defaults to the cache names provided by
   * [workbox-core]{@link module:workbox-core.cacheNames}.
   * @param {Array<Object>} [options.plugins] [Plugins]{@link https://developers.google.com/web/tools/workbox/guides/using-plugins}
   * to use in conjunction with this caching strategy.
   * @param {Object} [options.fetchOptions] Values passed along to the
   * [`init`]{@link https://developer.mozilla.org/en-US/docs/Web/API/WindowOrWorkerGlobalScope/fetch#Parameters}
   * of all fetch() requests made by this strategy.
   * @param {Object} [options.matchOptions] The
   * [`CacheQueryOptions`]{@link https://w3c.github.io/ServiceWorker/#dictdef-cachequeryoptions}
   * for any `cache.match()` or `cache.put()` calls made by this strategy.
   * @param {boolean} [options.fallbackToNetwork=true] Whether to attempt to
   * get the response from the network if there's a precache miss.
   */
  constructor(options = {}) {
    options.cacheName = cacheNames.getPrecacheName(options.cacheName);
    super(options);
    this._fallbackToNetwork = options.fallbackToNetwork === false ? false : true; // Redirected responses cannot be used to satisfy a navigation request, so
    // any redirected response must be "copied" rather than cloned, so the new
    // response doesn't contain the `redirected` flag. See:
    // https://bugs.chromium.org/p/chromium/issues/detail?id=669363&desc=2#c1

    this.plugins.push(PrecacheStrategy.copyRedirectedCacheableResponsesPlugin);
  }
  /**
   * @private
   * @param {Request|string} request A request to run this strategy for.
   * @param {module:workbox-strategies.StrategyHandler} handler The event that
   *     triggered the request.
   * @return {Promise<Response>}
   */


  async _handle(request, handler) {
    const response = await handler.cacheMatch(request);

    if (response) {
      return response;
    } // If this is an `install` event for an entry that isn't already cached,
    // then populate the cache.


    if (handler.event && handler.event.type === 'install') {
      return await this._handleInstall(request, handler);
    } // Getting here means something went wrong. An entry that should have been
    // precached wasn't found in the cache.


    return await this._handleFetch(request, handler);
  }

  async _handleFetch(request, handler) {
    let response;
    const params = handler.params || {}; // Fall back to the network if we're configured to do so.

    if (this._fallbackToNetwork) {
      {
        logger.warn(`The precached response for ` + `${getFriendlyURL(request.url)} in ${this.cacheName} was not ` + `found. Falling back to the network.`);
      }

      const integrityInManifest = params.integrity;
      const integrityInRequest = request.integrity;
      const noIntegrityConflict = !integrityInRequest || integrityInRequest === integrityInManifest;
      response = await handler.fetch(new Request(request, {
        integrity: integrityInRequest || integrityInManifest
      })); // It's only "safe" to repair the cache if we're using SRI to guarantee
      // that the response matches the precache manifest's expectations,
      // and there's either a) no integrity property in the incoming request
      // or b) there is an integrity, and it matches the precache manifest.
      // See https://github.com/GoogleChrome/workbox/issues/2858

      if (integrityInManifest && noIntegrityConflict) {
        this._useDefaultCacheabilityPluginIfNeeded();

        const wasCached = await handler.cachePut(request, response.clone());

        {
          if (wasCached) {
            logger.log(`A response for ${getFriendlyURL(request.url)} ` + `was used to "repair" the precache.`);
          }
        }
      }
    } else {
      // This shouldn't normally happen, but there are edge cases:
      // https://github.com/GoogleChrome/workbox/issues/1441
      throw new WorkboxError('missing-precache-entry', {
        cacheName: this.cacheName,
        url: request.url
      });
    }

    {
      const cacheKey = params.cacheKey || (await handler.getCacheKey(request, 'read')); // Workbox is going to handle the route.
      // print the routing details to the console.

      logger.groupCollapsed(`Precaching is responding to: ` + getFriendlyURL(request.url));
      logger.log(`Serving the precached url: ${getFriendlyURL(cacheKey instanceof Request ? cacheKey.url : cacheKey)}`);
      logger.groupCollapsed(`View request details here.`);
      logger.log(request);
      logger.groupEnd();
      logger.groupCollapsed(`View response details here.`);
      logger.log(response);
      logger.groupEnd();
      logger.groupEnd();
    }

    return response;
  }

  async _handleInstall(request, handler) {
    this._useDefaultCacheabilityPluginIfNeeded();

    const response = await handler.fetch(request); // Make sure we defer cachePut() until after we know the response
    // should be cached; see https://github.com/GoogleChrome/workbox/issues/2737

    const wasCached = await handler.cachePut(request, response.clone());

    if (!wasCached) {
      // Throwing here will lead to the `install` handler failing, which
      // we want to do if *any* of the responses aren't safe to cache.
      throw new WorkboxError('bad-precaching-response', {
        url: request.url,
        status: response.status
      });
    }

    return response;
  }
  /**
   * This method is complex, as there a number of things to account for:
   *
   * The `plugins` array can be set at construction, and/or it might be added to
   * to at any time before the strategy is used.
   *
   * At the time the strategy is used (i.e. during an `install` event), there
   * needs to be at least one plugin that implements `cacheWillUpdate` in the
   * array, other than `copyRedirectedCacheableResponsesPlugin`.
   *
   * - If this method is called and there are no suitable `cacheWillUpdate`
   * plugins, we need to add `defaultPrecacheCacheabilityPlugin`.
   *
   * - If this method is called and there is exactly one `cacheWillUpdate`, then
   * we don't have to do anything (this might be a previously added
   * `defaultPrecacheCacheabilityPlugin`, or it might be a custom plugin).
   *
   * - If this method is called and there is more than one `cacheWillUpdate`,
   * then we need to check if one is `defaultPrecacheCacheabilityPlugin`. If so,
   * we need to remove it. (This situation is unlikely, but it could happen if
   * the strategy is used multiple times, the first without a `cacheWillUpdate`,
   * and then later on after manually adding a custom `cacheWillUpdate`.)
   *
   * See https://github.com/GoogleChrome/workbox/issues/2737 for more context.
   *
   * @private
   */


  _useDefaultCacheabilityPluginIfNeeded() {
    let defaultPluginIndex = null;
    let cacheWillUpdatePluginCount = 0;

    for (const [index, plugin] of this.plugins.entries()) {
      // Ignore the copy redirected plugin when determining what to do.
      if (plugin === PrecacheStrategy.copyRedirectedCacheableResponsesPlugin) {
        continue;
      } // Save the default plugin's index, in case it needs to be removed.


      if (plugin === PrecacheStrategy.defaultPrecacheCacheabilityPlugin) {
        defaultPluginIndex = index;
      }

      if (plugin.cacheWillUpdate) {
        cacheWillUpdatePluginCount++;
      }
    }

    if (cacheWillUpdatePluginCount === 0) {
      this.plugins.push(PrecacheStrategy.defaultPrecacheCacheabilityPlugin);
    } else if (cacheWillUpdatePluginCount > 1 && defaultPluginIndex !== null) {
      // Only remove the default plugin; multiple custom plugins are allowed.
      this.plugins.splice(defaultPluginIndex, 1);
    } // Nothing needs to be done if cacheWillUpdatePluginCount is 1

  }

}

PrecacheStrategy.defaultPrecacheCacheabilityPlugin = {
  async cacheWillUpdate({
    response
  }) {
    if (!response || response.status >= 400) {
      return null;
    }

    return response;
  }

};
PrecacheStrategy.copyRedirectedCacheableResponsesPlugin = {
  async cacheWillUpdate({
    response
  }) {
    return response.redirected ? await copyResponse(response) : response;
  }

};

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
   * @param {Object} [options]
   * @param {string} [options.cacheName] The cache to use for precaching.
   * @param {string} [options.plugins] Plugins to use when precaching as well
   * as responding to fetch events for precached assets.
   * @param {boolean} [options.fallbackToNetwork=true] Whether to attempt to
   * get the response from the network if there's a precache miss.
   */
  constructor({
    cacheName,
    plugins = [],
    fallbackToNetwork = true
  } = {}) {
    this._urlsToCacheKeys = new Map();
    this._urlsToCacheModes = new Map();
    this._cacheKeysToIntegrities = new Map();
    this._strategy = new PrecacheStrategy({
      cacheName: cacheNames.getPrecacheName(cacheName),
      plugins: [...plugins, new PrecacheCacheKeyPlugin({
        precacheController: this
      })],
      fallbackToNetwork
    }); // Bind the install and activate methods to the instance.

    this.install = this.install.bind(this);
    this.activate = this.activate.bind(this);
  }
  /**
   * @type {module:workbox-precaching.PrecacheStrategy} The strategy created by this controller and
   * used to cache assets and respond to fetch events.
   */


  get strategy() {
    return this._strategy;
  }
  /**
   * Adds items to the precache list, removing any duplicates and
   * stores the files in the
   * ["precache cache"]{@link module:workbox-core.cacheNames} when the service
   * worker installs.
   *
   * This method can be called multiple times.
   *
   * @param {Array<Object|string>} [entries=[]] Array of entries to precache.
   */


  precache(entries) {
    this.addToCacheList(entries);

    if (!this._installAndActiveListenersAdded) {
      self.addEventListener('install', this.install);
      self.addEventListener('activate', this.activate);
      this._installAndActiveListenersAdded = true;
    }
  }
  /**
   * This method will add items to the precache list, removing duplicates
   * and ensuring the information is valid.
   *
   * @param {Array<module:workbox-precaching.PrecacheController.PrecacheEntry|string>} entries
   *     Array of entries to precache.
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
   * Note: this method calls `event.waitUntil()` for you, so you do not need
   * to call it yourself in your event handlers.
   *
   * @param {ExtendableEvent} event
   * @return {Promise<module:workbox-precaching.InstallResult>}
   */


  install(event) {
    // waitUntil returns Promise<any>
    // eslint-disable-next-line @typescript-eslint/no-unsafe-return
    return waitUntil(event, async () => {
      const installReportPlugin = new PrecacheInstallReportPlugin();
      this.strategy.plugins.push(installReportPlugin); // Cache entries one at a time.
      // See https://github.com/GoogleChrome/workbox/issues/2528

      for (const [url, cacheKey] of this._urlsToCacheKeys) {
        const integrity = this._cacheKeysToIntegrities.get(cacheKey);

        const cacheMode = this._urlsToCacheModes.get(url);

        const request = new Request(url, {
          integrity,
          cache: cacheMode,
          credentials: 'same-origin'
        });
        await Promise.all(this.strategy.handleAll({
          params: {
            cacheKey
          },
          request,
          event
        }));
      }

      const {
        updatedURLs,
        notUpdatedURLs
      } = installReportPlugin;

      {
        printInstallDetails(updatedURLs, notUpdatedURLs);
      }

      return {
        updatedURLs,
        notUpdatedURLs
      };
    });
  }
  /**
   * Deletes assets that are no longer present in the current precache manifest.
   * Call this method from the service worker activate event.
   *
   * Note: this method calls `event.waitUntil()` for you, so you do not need
   * to call it yourself in your event handlers.
   *
   * @param {ExtendableEvent} event
   * @return {Promise<module:workbox-precaching.CleanupResult>}
   */


  activate(event) {
    // waitUntil returns Promise<any>
    // eslint-disable-next-line @typescript-eslint/no-unsafe-return
    return waitUntil(event, async () => {
      const cache = await self.caches.open(this.strategy.cacheName);
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
   * @param {string} url A cache key whose SRI you want to look up.
   * @return {string} The subresource integrity associated with the cache key,
   * or undefined if it's not set.
   */


  getIntegrityForCacheKey(cacheKey) {
    return this._cacheKeysToIntegrities.get(cacheKey);
  }
  /**
   * This acts as a drop-in replacement for
   * [`cache.match()`](https://developer.mozilla.org/en-US/docs/Web/API/Cache/match)
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
      const cache = await self.caches.open(this.strategy.cacheName);
      return cache.match(cacheKey);
    }

    return undefined;
  }
  /**
   * Returns a function that looks up `url` in the precache (taking into
   * account revision information), and returns the corresponding `Response`.
   *
   * @param {string} url The precached URL which will be used to lookup the
   * `Response`.
   * @return {module:workbox-routing~handlerCallback}
   */


  createHandlerBoundToURL(url) {
    const cacheKey = this.getCacheKeyForURL(url);

    if (!cacheKey) {
      throw new WorkboxError('non-precached-url', {
        url
      });
    }

    return options => {
      options.request = new Request(url);
      options.params = Object.assign({
        cacheKey
      }, options.params);
      return this.strategy.handle(options);
    };
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
  ignoreURLParametersMatching = [/^utm_/, /^fbclid$/],
  directoryIndex = 'index.html',
  cleanURLs = true,
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
  Copyright 2020 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
/**
 * A subclass of [Route]{@link module:workbox-routing.Route} that takes a
 * [PrecacheController]{@link module:workbox-precaching.PrecacheController}
 * instance and uses it to match incoming requests and handle fetching
 * responses from the precache.
 *
 * @memberof module:workbox-precaching
 * @extends module:workbox-routing.Route
 */

class PrecacheRoute extends Route {
  /**
   * @param {PrecacheController} precacheController A `PrecacheController`
   * instance used to both match requests and respond to fetch events.
   * @param {Object} [options] Options to control how requests are matched
   * against the list of precached URLs.
   * @param {string} [options.directoryIndex=index.html] The `directoryIndex` will
   * check cache entries for a URLs ending with '/' to see if there is a hit when
   * appending the `directoryIndex` value.
   * @param {Array<RegExp>} [options.ignoreURLParametersMatching=[/^utm_/, /^fbclid$/]] An
   * array of regex's to remove search params when looking for a cache match.
   * @param {boolean} [options.cleanURLs=true] The `cleanURLs` option will
   * check the cache for the URL with a `.html` added to the end of the end.
   * @param {module:workbox-precaching~urlManipulation} [options.urlManipulation]
   * This is a function that should take a URL and return an array of
   * alternative URLs that should be checked for precache matches.
   */
  constructor(precacheController, options) {
    const match = ({
      request
    }) => {
      const urlsToCacheKeys = precacheController.getURLsToCacheKeys();

      for (const possibleURL of generateURLVariations(request.url, options)) {
        const cacheKey = urlsToCacheKeys.get(possibleURL);

        if (cacheKey) {
          const integrity = precacheController.getIntegrityForCacheKey(cacheKey);
          return {
            cacheKey,
            integrity
          };
        }
      }

      {
        logger.debug(`Precaching did not find a match for ` + getFriendlyURL(request.url));
      }

      return;
    };

    super(match, precacheController.strategy);
  }

}

/*
  Copyright 2019 Google LLC
  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
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
 * @param {Object} [options] See
 * [PrecacheRoute options]{@link module:workbox-precaching.PrecacheRoute}.
 *
 * @memberof module:workbox-precaching
 */

function addRoute(options) {
  const precacheController = getOrCreatePrecacheController();
  const precacheRoute = new PrecacheRoute(precacheController, options);
  registerRoute(precacheRoute);
}

/*
  Copyright 2019 Google LLC

  Use of this source code is governed by an MIT-style
  license that can be found in the LICENSE file or at
  https://opensource.org/licenses/MIT.
*/
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
  precacheController.precache(entries);
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
 * [PrecacheRoute options]{@link module:workbox-precaching.PrecacheRoute}.
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

self.skipWaiting();
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
  "revision": "662c160592a0c67e0798be5e9918e008"
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
  "revision": "13320878869a993d1c6cc8ef5c6ccd4e"
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
  "revision": "3c5b056eeb97499afe252002bdf13661"
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
  "revision": "2d4e606e8e5c04af04ea6baf1c6fa6fd"
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
  "revision": "7a23e7b8d45100ddcdc1279fa4769ae4"
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
  "revision": "462956d3d01f40c97f1d6714dfe495b1"
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
  "revision": "73d71926accac35ed7c0537f19892935"
}, {
  "url": "include/LD/assets/icons/action-sprite/svg/symbols.svg",
  "revision": "ce36438767abb92dc417e608f9a55647"
}, {
  "url": "include/LD/assets/icons/action/add_contact.svg",
  "revision": "9a2614a543529b4e012cedc6e069c1c0"
}, {
  "url": "include/LD/assets/icons/action/add_file.svg",
  "revision": "9a8521f9d8ae87bc5d77460775292a46"
}, {
  "url": "include/LD/assets/icons/action/add_photo_video.svg",
  "revision": "7219ea6c20bb361549eb9764832c46ad"
}, {
  "url": "include/LD/assets/icons/action/add_relationship.svg",
  "revision": "84258c2f5be52f07b42578efe8c7fbcf"
}, {
  "url": "include/LD/assets/icons/action/adjust_value.svg",
  "revision": "2735d232c7be3260e37efa47ebb5d6df"
}, {
  "url": "include/LD/assets/icons/action/announcement.svg",
  "revision": "fa9c2267f994f13eb0f4a2365f483418"
}, {
  "url": "include/LD/assets/icons/action/apex.svg",
  "revision": "096144d0b1718dc95380db2f6357aedf"
}, {
  "url": "include/LD/assets/icons/action/approval.svg",
  "revision": "bf8291b6f73f789c87a26b77efe4d704"
}, {
  "url": "include/LD/assets/icons/action/back.svg",
  "revision": "6c74b97594dd2c90d2dc7446bb34ffb5"
}, {
  "url": "include/LD/assets/icons/action/bug.svg",
  "revision": "e690182c74fbb4314a343fbcac32739c"
}, {
  "url": "include/LD/assets/icons/action/call.svg",
  "revision": "6b65e5ad5cd0aceaf078d6010c579338"
}, {
  "url": "include/LD/assets/icons/action/canvas.svg",
  "revision": "0d8a40d419285c3d2407b31f4fd49e0d"
}, {
  "url": "include/LD/assets/icons/action/change_owner.svg",
  "revision": "69e1652150cd997cc1e06470e24cf492"
}, {
  "url": "include/LD/assets/icons/action/change_record_type.svg",
  "revision": "aa6f8ccf185d344c04968d7e8274bc80"
}, {
  "url": "include/LD/assets/icons/action/check.svg",
  "revision": "d1101682a7056b1d5793a11c9916f5e1"
}, {
  "url": "include/LD/assets/icons/action/clone.svg",
  "revision": "833aa55959f7b3bc4ed5d6cab0fc106a"
}, {
  "url": "include/LD/assets/icons/action/close.svg",
  "revision": "7599de61bd44f53019789a01e929d2bc"
}, {
  "url": "include/LD/assets/icons/action/defer.svg",
  "revision": "b46b064b62dcaeb9b2c22e9982bd3721"
}, {
  "url": "include/LD/assets/icons/action/delete.svg",
  "revision": "21388fa0a15cd6f5af5521e833ca40de"
}, {
  "url": "include/LD/assets/icons/action/description.svg",
  "revision": "2bb85137e80287c69df30c8f5a253be2"
}, {
  "url": "include/LD/assets/icons/action/dial_in.svg",
  "revision": "5744655a8ca12048c3b78d8b08ef7436"
}, {
  "url": "include/LD/assets/icons/action/download.svg",
  "revision": "5b71d1944d7c826654b5d61fc38df184"
}, {
  "url": "include/LD/assets/icons/action/edit_groups.svg",
  "revision": "129825b670a9509422f2c6faabdb93db"
}, {
  "url": "include/LD/assets/icons/action/edit_relationship.svg",
  "revision": "bb8eb4b1d8e85e8b24284c32e78cde62"
}, {
  "url": "include/LD/assets/icons/action/edit.svg",
  "revision": "9bf25613ab7b436aade021a5bca86d41"
}, {
  "url": "include/LD/assets/icons/action/email.svg",
  "revision": "eb24e9fe80e1543054cdcecbd6bbef5e"
}, {
  "url": "include/LD/assets/icons/action/fallback.svg",
  "revision": "8567fe478f25d6092d02e21890602a5a"
}, {
  "url": "include/LD/assets/icons/action/filter.svg",
  "revision": "e8e9960a0a1708786c3e54c1bae25376"
}, {
  "url": "include/LD/assets/icons/action/flow.svg",
  "revision": "41ebcba34bc8629b9ab91960aa5c5434"
}, {
  "url": "include/LD/assets/icons/action/follow.svg",
  "revision": "71a9125bbaba8b50df43786e5ffe2b11"
}, {
  "url": "include/LD/assets/icons/action/following.svg",
  "revision": "bc80d7a9b23408951103d0631cd1e3f2"
}, {
  "url": "include/LD/assets/icons/action/freeze_user.svg",
  "revision": "1a60bcb44a6576ed32691c2ed573f12e"
}, {
  "url": "include/LD/assets/icons/action/goal.svg",
  "revision": "8224a1bee95a71664643de6ea1787813"
}, {
  "url": "include/LD/assets/icons/action/google_news.svg",
  "revision": "d1da974db07d25762ba3ad6dea698741"
}, {
  "url": "include/LD/assets/icons/action/info.svg",
  "revision": "904bd0ab4ea48fb552d7bb65d6e30631"
}, {
  "url": "include/LD/assets/icons/action/join_group.svg",
  "revision": "b440924f91dbacb489bc0c4210bdb4d9"
}, {
  "url": "include/LD/assets/icons/action/lead_convert.svg",
  "revision": "b676092af697ae8fc88d19732ee3d135"
}, {
  "url": "include/LD/assets/icons/action/leave_group.svg",
  "revision": "ccbee1a82593152b829aa6401727fbf3"
}, {
  "url": "include/LD/assets/icons/action/log_a_call.svg",
  "revision": "e327685c899542df7233cdae66a7de73"
}, {
  "url": "include/LD/assets/icons/action/log_event.svg",
  "revision": "b0a36386c57d3070adcd0fa993d6ae9b"
}, {
  "url": "include/LD/assets/icons/action/manage_perm_sets.svg",
  "revision": "38c0e857289653bde4bfb5b91d44cec6"
}, {
  "url": "include/LD/assets/icons/action/map.svg",
  "revision": "7b02732dd62a4eee5cae431646723bd5"
}, {
  "url": "include/LD/assets/icons/action/more.svg",
  "revision": "b50568deee868bc6e0c5ca2abb2dea8c"
}, {
  "url": "include/LD/assets/icons/action/new_account.svg",
  "revision": "b2e79baa03410c6c7d81eee5bc413e25"
}, {
  "url": "include/LD/assets/icons/action/new_campaign.svg",
  "revision": "0fc77ebb1be263d72dfd771ad468291d"
}, {
  "url": "include/LD/assets/icons/action/new_case.svg",
  "revision": "8e4c6e6ff94ff380a976ab5164e6f942"
}, {
  "url": "include/LD/assets/icons/action/new_child_case.svg",
  "revision": "cec9c6611a6d688e2eaa9090a70eff77"
}, {
  "url": "include/LD/assets/icons/action/new_contact.svg",
  "revision": "9a2614a543529b4e012cedc6e069c1c0"
}, {
  "url": "include/LD/assets/icons/action/new_custom1.svg",
  "revision": "94e566286c48c8f9d3608e1ad5d705b6"
}, {
  "url": "include/LD/assets/icons/action/new_custom10.svg",
  "revision": "d738d8142da5cfbd418497b48e303929"
}, {
  "url": "include/LD/assets/icons/action/new_custom100.svg",
  "revision": "9fca06d64f4240a8cee6c3e7b7fff142"
}, {
  "url": "include/LD/assets/icons/action/new_custom11.svg",
  "revision": "a71a125337d667093368d2c11e54c969"
}, {
  "url": "include/LD/assets/icons/action/new_custom12.svg",
  "revision": "29ca2480729b0886aea1da70dc446c36"
}, {
  "url": "include/LD/assets/icons/action/new_custom13.svg",
  "revision": "4120d3d22ed5b043a8ab17dd0433b039"
}, {
  "url": "include/LD/assets/icons/action/new_custom14.svg",
  "revision": "d41f71d83d258ccf53328cc23029c586"
}, {
  "url": "include/LD/assets/icons/action/new_custom15.svg",
  "revision": "28aaa026b54a12b837586096ed4c4977"
}, {
  "url": "include/LD/assets/icons/action/new_custom16.svg",
  "revision": "6ad74ebcd9f5a9b0fb420182143c6b4e"
}, {
  "url": "include/LD/assets/icons/action/new_custom17.svg",
  "revision": "82f59118f8d28ff1f2895f42f2d7bc3b"
}, {
  "url": "include/LD/assets/icons/action/new_custom18.svg",
  "revision": "2f60de6b998abfac88fd5ce8bfa56995"
}, {
  "url": "include/LD/assets/icons/action/new_custom19.svg",
  "revision": "ca48b31252eb425812e802410b6a58d9"
}, {
  "url": "include/LD/assets/icons/action/new_custom2.svg",
  "revision": "9e78a2f282fd3cef5e45bef07f37848e"
}, {
  "url": "include/LD/assets/icons/action/new_custom20.svg",
  "revision": "935b0e7ba552352b4cd14e3dd37aedfc"
}, {
  "url": "include/LD/assets/icons/action/new_custom21.svg",
  "revision": "51d83969d53efb98a59e2f52efa76f2b"
}, {
  "url": "include/LD/assets/icons/action/new_custom22.svg",
  "revision": "3aa01cc7ac32517bb5e155c29ace8a1f"
}, {
  "url": "include/LD/assets/icons/action/new_custom23.svg",
  "revision": "e4862744e7d7a1a32f5ddc2fa4b25daf"
}, {
  "url": "include/LD/assets/icons/action/new_custom24.svg",
  "revision": "0716fc0235f306c17aa35e8ec24c6070"
}, {
  "url": "include/LD/assets/icons/action/new_custom25.svg",
  "revision": "3606717d5461e3d55921f44ae857b6d6"
}, {
  "url": "include/LD/assets/icons/action/new_custom26.svg",
  "revision": "556c22875fd5061c0c944d8e5b910d1d"
}, {
  "url": "include/LD/assets/icons/action/new_custom27.svg",
  "revision": "13672268ec2fc04b0c3356fadeb6bd22"
}, {
  "url": "include/LD/assets/icons/action/new_custom28.svg",
  "revision": "9604f5b4709b29767b9db2a9b5639b1b"
}, {
  "url": "include/LD/assets/icons/action/new_custom29.svg",
  "revision": "2567f2af22b775cfdbbd42e2a9ad25d7"
}, {
  "url": "include/LD/assets/icons/action/new_custom3.svg",
  "revision": "871929f4c0f8b6a5a56345914dbb1a0d"
}, {
  "url": "include/LD/assets/icons/action/new_custom30.svg",
  "revision": "5ca8f813de46586803027150b3a7064e"
}, {
  "url": "include/LD/assets/icons/action/new_custom31.svg",
  "revision": "35176de10dbcc02558566f9e4da1679a"
}, {
  "url": "include/LD/assets/icons/action/new_custom32.svg",
  "revision": "19bca97ab8b81c9bcf394be3794bab97"
}, {
  "url": "include/LD/assets/icons/action/new_custom33.svg",
  "revision": "a415bc89655df8a8c9ca67e95aec419c"
}, {
  "url": "include/LD/assets/icons/action/new_custom34.svg",
  "revision": "b4539e4f7089b981a0ebf14c59dd5eaa"
}, {
  "url": "include/LD/assets/icons/action/new_custom35.svg",
  "revision": "9206a497b3cef8fd62683dfa3248d6f6"
}, {
  "url": "include/LD/assets/icons/action/new_custom36.svg",
  "revision": "b96d31880048561c14851630ab1c0d23"
}, {
  "url": "include/LD/assets/icons/action/new_custom37.svg",
  "revision": "d7ce97929da06965540fdec88799b346"
}, {
  "url": "include/LD/assets/icons/action/new_custom38.svg",
  "revision": "7cd19b584c841545b0cc3486975a6e1a"
}, {
  "url": "include/LD/assets/icons/action/new_custom39.svg",
  "revision": "78c6e463eb9aa9d5e35cc4dbb7124e67"
}, {
  "url": "include/LD/assets/icons/action/new_custom4.svg",
  "revision": "38b6e91396bf5176fbe534f163f618c7"
}, {
  "url": "include/LD/assets/icons/action/new_custom40.svg",
  "revision": "c66746a6f02153d0c3978e4391a4e26a"
}, {
  "url": "include/LD/assets/icons/action/new_custom41.svg",
  "revision": "cd71bfcb68c7e19e604e975808378912"
}, {
  "url": "include/LD/assets/icons/action/new_custom42.svg",
  "revision": "4120d3d22ed5b043a8ab17dd0433b039"
}, {
  "url": "include/LD/assets/icons/action/new_custom43.svg",
  "revision": "120f4b4a2a9448572b4f963b48ff1ca4"
}, {
  "url": "include/LD/assets/icons/action/new_custom44.svg",
  "revision": "e028b29ed4b4f0862a52698ac57d1ee3"
}, {
  "url": "include/LD/assets/icons/action/new_custom45.svg",
  "revision": "6efb310e1aab8af467e30fcf86b8c117"
}, {
  "url": "include/LD/assets/icons/action/new_custom46.svg",
  "revision": "8830c162018c5c1b8e25c32544ec567b"
}, {
  "url": "include/LD/assets/icons/action/new_custom47.svg",
  "revision": "d9261e6c75e9782650a69090a503e33e"
}, {
  "url": "include/LD/assets/icons/action/new_custom48.svg",
  "revision": "95498550b8d841e3a3edb43742496e97"
}, {
  "url": "include/LD/assets/icons/action/new_custom49.svg",
  "revision": "d82e2effc96531e1a4bb9bde3c3e4f77"
}, {
  "url": "include/LD/assets/icons/action/new_custom5.svg",
  "revision": "fdc55062bcad416ad37e550b8ce96e65"
}, {
  "url": "include/LD/assets/icons/action/new_custom50.svg",
  "revision": "cdcff9de2141e09430201ccb80dbbf4c"
}, {
  "url": "include/LD/assets/icons/action/new_custom51.svg",
  "revision": "a9ad7ff497b1f766c78edd690b98eb9f"
}, {
  "url": "include/LD/assets/icons/action/new_custom52.svg",
  "revision": "f6b417aefea21d4385d1696efbbadbed"
}, {
  "url": "include/LD/assets/icons/action/new_custom53.svg",
  "revision": "45d002da38f8033a9b61524d24434cc9"
}, {
  "url": "include/LD/assets/icons/action/new_custom54.svg",
  "revision": "94a4819e231a06f84d64fb99e5486a39"
}, {
  "url": "include/LD/assets/icons/action/new_custom55.svg",
  "revision": "7dcd4ee0a8adf572adb35c0a00d8570d"
}, {
  "url": "include/LD/assets/icons/action/new_custom56.svg",
  "revision": "000cb8166e8b7f0bed4165bcefd0c118"
}, {
  "url": "include/LD/assets/icons/action/new_custom57.svg",
  "revision": "5ab7cc8a8a47525078ebcedcefb8b7f4"
}, {
  "url": "include/LD/assets/icons/action/new_custom58.svg",
  "revision": "b9c6206780de12a7c34ae0dac0643ea9"
}, {
  "url": "include/LD/assets/icons/action/new_custom59.svg",
  "revision": "d96c0fc92c5f360b86a692a7fb4b9cc6"
}, {
  "url": "include/LD/assets/icons/action/new_custom6.svg",
  "revision": "7d3a9d2f0bf2d7d6f9fb522e072e6d34"
}, {
  "url": "include/LD/assets/icons/action/new_custom60.svg",
  "revision": "d6520f4e3cced7dacb049664fdf8d1ee"
}, {
  "url": "include/LD/assets/icons/action/new_custom61.svg",
  "revision": "26bddb88ae889cf3a368a40e8cd5533c"
}, {
  "url": "include/LD/assets/icons/action/new_custom62.svg",
  "revision": "1a114c8b7dfe817415a859f6a3fe64cc"
}, {
  "url": "include/LD/assets/icons/action/new_custom63.svg",
  "revision": "63707d6c143e7fabdf1789f02d426734"
}, {
  "url": "include/LD/assets/icons/action/new_custom64.svg",
  "revision": "86e09200e9384946e8b1723576854188"
}, {
  "url": "include/LD/assets/icons/action/new_custom65.svg",
  "revision": "ca128f67be7f7de07aa19246be4bedda"
}, {
  "url": "include/LD/assets/icons/action/new_custom66.svg",
  "revision": "250007bd6684433b84f8c364d37ead61"
}, {
  "url": "include/LD/assets/icons/action/new_custom67.svg",
  "revision": "f00e7a82b5de993387271d8dac4799d6"
}, {
  "url": "include/LD/assets/icons/action/new_custom68.svg",
  "revision": "43a022ef1d2e92dec4768334e366b2e5"
}, {
  "url": "include/LD/assets/icons/action/new_custom69.svg",
  "revision": "bde3950fec8aaafd0a76905c5f1cc639"
}, {
  "url": "include/LD/assets/icons/action/new_custom7.svg",
  "revision": "e7f8f1b40a968e7b66928c86ed7b42aa"
}, {
  "url": "include/LD/assets/icons/action/new_custom70.svg",
  "revision": "5d9644cdfbef44629003987b1b9e74d1"
}, {
  "url": "include/LD/assets/icons/action/new_custom71.svg",
  "revision": "b9e5733f6b340949a8bd080c096fcedd"
}, {
  "url": "include/LD/assets/icons/action/new_custom72.svg",
  "revision": "90c2288fb5c8b6f937f1ac2dffef3664"
}, {
  "url": "include/LD/assets/icons/action/new_custom73.svg",
  "revision": "52567d29809ca7e11c138959ef4b8c8b"
}, {
  "url": "include/LD/assets/icons/action/new_custom74.svg",
  "revision": "e280a11c48fde608e23dc9d89454b79c"
}, {
  "url": "include/LD/assets/icons/action/new_custom75.svg",
  "revision": "29cc541ffd7d552ae770fb98df9db379"
}, {
  "url": "include/LD/assets/icons/action/new_custom76.svg",
  "revision": "b19a8ececea90b0cba2481dce1f125c3"
}, {
  "url": "include/LD/assets/icons/action/new_custom77.svg",
  "revision": "567d1248a0f93f583c992ed5eb294211"
}, {
  "url": "include/LD/assets/icons/action/new_custom78.svg",
  "revision": "2d40378081a23c9ca6b3c43e4fc061f8"
}, {
  "url": "include/LD/assets/icons/action/new_custom79.svg",
  "revision": "79247b2df7e02f8a770628c1c30120c2"
}, {
  "url": "include/LD/assets/icons/action/new_custom8.svg",
  "revision": "cecdfdf15ddd506a42b8628b90a97a82"
}, {
  "url": "include/LD/assets/icons/action/new_custom80.svg",
  "revision": "c46d2feb47af332a4e19d1146d759a18"
}, {
  "url": "include/LD/assets/icons/action/new_custom81.svg",
  "revision": "78af2a732f5db89202411bc90f5fff67"
}, {
  "url": "include/LD/assets/icons/action/new_custom82.svg",
  "revision": "6cffb0b125f55e1cb3d6e0aeb7d95390"
}, {
  "url": "include/LD/assets/icons/action/new_custom83.svg",
  "revision": "e397b6e7dde13c8752756d7b3bc942fe"
}, {
  "url": "include/LD/assets/icons/action/new_custom84.svg",
  "revision": "79e285bdbb4a8a74e619c50882655c41"
}, {
  "url": "include/LD/assets/icons/action/new_custom85.svg",
  "revision": "9683e4b76f12804bb6053652ab332bfb"
}, {
  "url": "include/LD/assets/icons/action/new_custom86.svg",
  "revision": "1186c5c9c6d607dc765fe680417ffdcf"
}, {
  "url": "include/LD/assets/icons/action/new_custom87.svg",
  "revision": "f785a265153c15f08426ad44aec4aeb4"
}, {
  "url": "include/LD/assets/icons/action/new_custom88.svg",
  "revision": "feafd491304e61a1c59a63137cb1cd7b"
}, {
  "url": "include/LD/assets/icons/action/new_custom89.svg",
  "revision": "812a82c3a51f93e3553501cc55be7109"
}, {
  "url": "include/LD/assets/icons/action/new_custom9.svg",
  "revision": "c946b891089ff949039c155e536ecead"
}, {
  "url": "include/LD/assets/icons/action/new_custom90.svg",
  "revision": "25db8600f0a1b4009d275c98bbb2a37f"
}, {
  "url": "include/LD/assets/icons/action/new_custom91.svg",
  "revision": "2e7af81f019612659c952e9ccbb07d3f"
}, {
  "url": "include/LD/assets/icons/action/new_custom92.svg",
  "revision": "bf743cea0376a1b06946d0dd1260b03b"
}, {
  "url": "include/LD/assets/icons/action/new_custom93.svg",
  "revision": "831b7f40a24a1036da754238e7079b19"
}, {
  "url": "include/LD/assets/icons/action/new_custom94.svg",
  "revision": "ef383aac3dfc1c7875727d612e43d665"
}, {
  "url": "include/LD/assets/icons/action/new_custom95.svg",
  "revision": "661479d5f522a260987a2e39b724ccd9"
}, {
  "url": "include/LD/assets/icons/action/new_custom96.svg",
  "revision": "64f94112475ab39df3cdb8afd7ae135f"
}, {
  "url": "include/LD/assets/icons/action/new_custom97.svg",
  "revision": "eb3d36e9f3b10ebff553f9712905a9d2"
}, {
  "url": "include/LD/assets/icons/action/new_custom98.svg",
  "revision": "95cc02d9d511147fa1e9d2aa11e97597"
}, {
  "url": "include/LD/assets/icons/action/new_custom99.svg",
  "revision": "ae7d6fe1887aa0645862a7da922b6717"
}, {
  "url": "include/LD/assets/icons/action/new_event.svg",
  "revision": "f3e1e89085e6ae00e595b26beb8e26bc"
}, {
  "url": "include/LD/assets/icons/action/new_group.svg",
  "revision": "232ab72ef04eb925d2388d578e369a79"
}, {
  "url": "include/LD/assets/icons/action/new_lead.svg",
  "revision": "270fe54f0c6fd8507b81bb0841a4833a"
}, {
  "url": "include/LD/assets/icons/action/new_note.svg",
  "revision": "6b31c843cce4adc55948ee2e47283e80"
}, {
  "url": "include/LD/assets/icons/action/new_notebook.svg",
  "revision": "8e4a394fc50aa5a7207fb2a541236010"
}, {
  "url": "include/LD/assets/icons/action/new_opportunity.svg",
  "revision": "03547a9d43a12cbcfc1be61a9ee213eb"
}, {
  "url": "include/LD/assets/icons/action/new_person_account.svg",
  "revision": "c2c81d1722794453f6e48df3debe84ef"
}, {
  "url": "include/LD/assets/icons/action/new_task.svg",
  "revision": "64a2bfa01f497d1986f9822ee869ab0f"
}, {
  "url": "include/LD/assets/icons/action/new.svg",
  "revision": "d7cfd08833a7d763fcc36e089c5834f0"
}, {
  "url": "include/LD/assets/icons/action/password_unlock.svg",
  "revision": "91fa03957c183630c016f725df46316f"
}, {
  "url": "include/LD/assets/icons/action/preview.svg",
  "revision": "34cfb771450fb3ca527153eea525dde7"
}, {
  "url": "include/LD/assets/icons/action/priority.svg",
  "revision": "e47665ba57ad6706a25fe47a4ee3d664"
}, {
  "url": "include/LD/assets/icons/action/question_post_action.svg",
  "revision": "9eecf5ec6e4ceb2e467118b7474f7375"
}, {
  "url": "include/LD/assets/icons/action/quote.svg",
  "revision": "a47bd876cd1c52f64daafb1f1fa783b0"
}, {
  "url": "include/LD/assets/icons/action/recall.svg",
  "revision": "cd8175da8248e425755d7ecd9d808394"
}, {
  "url": "include/LD/assets/icons/action/record.svg",
  "revision": "bb020ea18d4c34f95696aadbf4dd2d01"
}, {
  "url": "include/LD/assets/icons/action/refresh.svg",
  "revision": "05e6cf3cddb478efcae1f206b5bd1345"
}, {
  "url": "include/LD/assets/icons/action/reject.svg",
  "revision": "7599de61bd44f53019789a01e929d2bc"
}, {
  "url": "include/LD/assets/icons/action/remove_relationship.svg",
  "revision": "d7b52a451c18a04b7cfef5a2efc9882f"
}, {
  "url": "include/LD/assets/icons/action/remove.svg",
  "revision": "7599de61bd44f53019789a01e929d2bc"
}, {
  "url": "include/LD/assets/icons/action/reset_password.svg",
  "revision": "05e6cf3cddb478efcae1f206b5bd1345"
}, {
  "url": "include/LD/assets/icons/action/script.svg",
  "revision": "f06ec7f4ec4addf08ea38f91ce25e46f"
}, {
  "url": "include/LD/assets/icons/action/share_file.svg",
  "revision": "e54c13ed7d4a47af98e96174fb0c61b0"
}, {
  "url": "include/LD/assets/icons/action/share_link.svg",
  "revision": "d84b6c992fe8abf8a5a139a52e981d31"
}, {
  "url": "include/LD/assets/icons/action/share_poll.svg",
  "revision": "fb3c7a97281eaf810ece5d809a5693d4"
}, {
  "url": "include/LD/assets/icons/action/share_post.svg",
  "revision": "2392204dbaf2df35ade29b1a0382049b"
}, {
  "url": "include/LD/assets/icons/action/share_thanks.svg",
  "revision": "9694db26a5a1896039cf94482efd7e86"
}, {
  "url": "include/LD/assets/icons/action/share.svg",
  "revision": "2eeaa6082d03eba3d1610a3a70a38553"
}, {
  "url": "include/LD/assets/icons/action/sort.svg",
  "revision": "b339905758c96c77e204e66e13e71949"
}, {
  "url": "include/LD/assets/icons/action/submit_for_approval.svg",
  "revision": "ad039025ed3814cee775e07f191e6c34"
}, {
  "url": "include/LD/assets/icons/action/update_status.svg",
  "revision": "5880762fcd8db59a0a0a8fc582d28926"
}, {
  "url": "include/LD/assets/icons/action/update.svg",
  "revision": "2fe0f9322e26087cbe1df9639bef0ae7"
}, {
  "url": "include/LD/assets/icons/action/upload.svg",
  "revision": "ddfde4d9ce2198027ad89d43b2e65a0f"
}, {
  "url": "include/LD/assets/icons/action/user_activation.svg",
  "revision": "b1816354649fb364b0bd472e0b929594"
}, {
  "url": "include/LD/assets/icons/action/user.svg",
  "revision": "c721b01f767c4814faad1cb7e5e204bc"
}, {
  "url": "include/LD/assets/icons/action/view_relationship.svg",
  "revision": "c61d453046d1ec84a85cb2c0c6930b56"
}, {
  "url": "include/LD/assets/icons/action/web_link.svg",
  "revision": "3242afd7a97812522edce478b1632f82"
}, {
  "url": "include/LD/assets/icons/custom-sprite/svg/symbols-rtl.svg",
  "revision": "e022d7d7c4e34ba2e509112b5f654070"
}, {
  "url": "include/LD/assets/icons/custom-sprite/svg/symbols.svg",
  "revision": "94d3aea79173c7a80fb25c29aeb55bc9"
}, {
  "url": "include/LD/assets/icons/custom/custom1.svg",
  "revision": "4c4464d6d5eb198aeca99fa46dc1d439"
}, {
  "url": "include/LD/assets/icons/custom/custom10.svg",
  "revision": "4514203e2e4015ec50c998116e1d38a8"
}, {
  "url": "include/LD/assets/icons/custom/custom100.svg",
  "revision": "4d9498507a57f233573b24db4f8e4b11"
}, {
  "url": "include/LD/assets/icons/custom/custom101.svg",
  "revision": "11e608d160fdedfad2a5e0a068ecb958"
}, {
  "url": "include/LD/assets/icons/custom/custom102.svg",
  "revision": "5aff1103cab5ea37a585566acffca959"
}, {
  "url": "include/LD/assets/icons/custom/custom103.svg",
  "revision": "e136e52a77c056ae27ab779ed0f66b59"
}, {
  "url": "include/LD/assets/icons/custom/custom104.svg",
  "revision": "2a37becf60b01381222767e11d32a782"
}, {
  "url": "include/LD/assets/icons/custom/custom105.svg",
  "revision": "6e2138295af5a0bc3ed90b038b2d29d4"
}, {
  "url": "include/LD/assets/icons/custom/custom106.svg",
  "revision": "a01f7acd6e1206ea69598186b9b202b5"
}, {
  "url": "include/LD/assets/icons/custom/custom107.svg",
  "revision": "f0053e4f51bf46f4b8a83f497446a376"
}, {
  "url": "include/LD/assets/icons/custom/custom108.svg",
  "revision": "8e0b3b9b4cfe968fe35d63f0761ee302"
}, {
  "url": "include/LD/assets/icons/custom/custom109.svg",
  "revision": "faa19ac6a0724b8fa3932b484b2c35ff"
}, {
  "url": "include/LD/assets/icons/custom/custom11.svg",
  "revision": "8f36e5b858bbee5b16ce410ccf502cd8"
}, {
  "url": "include/LD/assets/icons/custom/custom110.svg",
  "revision": "5ac6a41052b889003e8aa60481f4ccdf"
}, {
  "url": "include/LD/assets/icons/custom/custom111.svg",
  "revision": "d27231569e37fbd8cb34972ba73e9d69"
}, {
  "url": "include/LD/assets/icons/custom/custom112.svg",
  "revision": "909d25a7e10b19994981c1594f9771e7"
}, {
  "url": "include/LD/assets/icons/custom/custom113.svg",
  "revision": "98f249a82fc6d095276458a02e09f15f"
}, {
  "url": "include/LD/assets/icons/custom/custom12.svg",
  "revision": "7d5a13faca95453288efdba9d126ba73"
}, {
  "url": "include/LD/assets/icons/custom/custom13.svg",
  "revision": "14e7c450d5d739c8f2fecd96c739dd90"
}, {
  "url": "include/LD/assets/icons/custom/custom14.svg",
  "revision": "2451d8b31160501853f2777378652a86"
}, {
  "url": "include/LD/assets/icons/custom/custom15.svg",
  "revision": "14553144bfafdb5d1f340318447d0820"
}, {
  "url": "include/LD/assets/icons/custom/custom16.svg",
  "revision": "b548c8426ce7d57446f8bc30e1c8cc5e"
}, {
  "url": "include/LD/assets/icons/custom/custom17.svg",
  "revision": "3447e58506fcaa8249a6c2c2bf9aac9d"
}, {
  "url": "include/LD/assets/icons/custom/custom18.svg",
  "revision": "f036677217774dd9c621a25fe3e96483"
}, {
  "url": "include/LD/assets/icons/custom/custom19.svg",
  "revision": "94ab851f7ad269920452189e477bc904"
}, {
  "url": "include/LD/assets/icons/custom/custom2.svg",
  "revision": "a52fdb28bba736b619fb830a40c146bc"
}, {
  "url": "include/LD/assets/icons/custom/custom20.svg",
  "revision": "38e8ddc154d9bc24d76f4a87a54eda63"
}, {
  "url": "include/LD/assets/icons/custom/custom21.svg",
  "revision": "a682a37482a7fe516b1b273c9240e3ae"
}, {
  "url": "include/LD/assets/icons/custom/custom22.svg",
  "revision": "c5ac85635ab87389101792a1c6c984db"
}, {
  "url": "include/LD/assets/icons/custom/custom23.svg",
  "revision": "fc7d552835e87f46cb3d80b2bb816700"
}, {
  "url": "include/LD/assets/icons/custom/custom24.svg",
  "revision": "506fea2fd37cc927171d80cf9558dd81"
}, {
  "url": "include/LD/assets/icons/custom/custom25.svg",
  "revision": "eee2c97808cf338d7830c92bd5bd1f23"
}, {
  "url": "include/LD/assets/icons/custom/custom26.svg",
  "revision": "5bc6751e0ea9da53f0dd52c18b36eb9d"
}, {
  "url": "include/LD/assets/icons/custom/custom27.svg",
  "revision": "9ee79a8a10e1be4932816d4aa4625dc5"
}, {
  "url": "include/LD/assets/icons/custom/custom28.svg",
  "revision": "944a30faed345c512c1ff9a2ed6b5ef7"
}, {
  "url": "include/LD/assets/icons/custom/custom29.svg",
  "revision": "c28c3ee4afccfb9842913f8066ee13c2"
}, {
  "url": "include/LD/assets/icons/custom/custom3.svg",
  "revision": "430e392af9fb24c3d304748efa150320"
}, {
  "url": "include/LD/assets/icons/custom/custom30.svg",
  "revision": "b29181707d59399ef7d5ed3dec929d8d"
}, {
  "url": "include/LD/assets/icons/custom/custom31.svg",
  "revision": "1d8930ec328d9b0a0841078dc0561c5e"
}, {
  "url": "include/LD/assets/icons/custom/custom32.svg",
  "revision": "79b7b25a2f91128b00a1060c9654ea82"
}, {
  "url": "include/LD/assets/icons/custom/custom33.svg",
  "revision": "d4fb3558fa6a4b44ce876db8d78be906"
}, {
  "url": "include/LD/assets/icons/custom/custom34.svg",
  "revision": "e43d9df9a514a93b46bf92be06121a90"
}, {
  "url": "include/LD/assets/icons/custom/custom35.svg",
  "revision": "897351e3f3e8d0761a2271e5d6d46472"
}, {
  "url": "include/LD/assets/icons/custom/custom36.svg",
  "revision": "cbbece35c0140b12ac1379160b2e2474"
}, {
  "url": "include/LD/assets/icons/custom/custom37.svg",
  "revision": "04cb71756878442d60b9eb6329368c4b"
}, {
  "url": "include/LD/assets/icons/custom/custom38.svg",
  "revision": "8e1e2b0e9dc4d24587b1f48f0bd1a356"
}, {
  "url": "include/LD/assets/icons/custom/custom39.svg",
  "revision": "ff67b2dd616e45dd589846e4a01ed918"
}, {
  "url": "include/LD/assets/icons/custom/custom4.svg",
  "revision": "935d9e6a92d81df3c1402c26a5a99a87"
}, {
  "url": "include/LD/assets/icons/custom/custom40.svg",
  "revision": "02de69cd9a93c1d9288e6267038ae49b"
}, {
  "url": "include/LD/assets/icons/custom/custom41.svg",
  "revision": "43c159550024266def65b30d44b1610b"
}, {
  "url": "include/LD/assets/icons/custom/custom42.svg",
  "revision": "14e7c450d5d739c8f2fecd96c739dd90"
}, {
  "url": "include/LD/assets/icons/custom/custom43.svg",
  "revision": "5a7e44bcc55c96fef2e00a5bea1a0436"
}, {
  "url": "include/LD/assets/icons/custom/custom44.svg",
  "revision": "2dc0d9e646a52eaca864b102e3429532"
}, {
  "url": "include/LD/assets/icons/custom/custom45.svg",
  "revision": "35db292d4efd4bf50bbf61593dc781ef"
}, {
  "url": "include/LD/assets/icons/custom/custom46.svg",
  "revision": "1a94ec98f82ed1062b43082c52f4d2b7"
}, {
  "url": "include/LD/assets/icons/custom/custom47.svg",
  "revision": "4ccbbc5eeeef157207622030190b036f"
}, {
  "url": "include/LD/assets/icons/custom/custom48.svg",
  "revision": "563b298b7f787a2a519ecd8956c0bae4"
}, {
  "url": "include/LD/assets/icons/custom/custom49.svg",
  "revision": "72a468956034c9c754acc30fe13614cb"
}, {
  "url": "include/LD/assets/icons/custom/custom5.svg",
  "revision": "27654305c72160542ee2afac1d9d8ff5"
}, {
  "url": "include/LD/assets/icons/custom/custom50.svg",
  "revision": "8a405c0417e04f262326947f32ebbc92"
}, {
  "url": "include/LD/assets/icons/custom/custom51.svg",
  "revision": "06ca5e4a07c722dc9fe5623484957b35"
}, {
  "url": "include/LD/assets/icons/custom/custom52.svg",
  "revision": "c6417b5cf44b0d448591ea141b9e449a"
}, {
  "url": "include/LD/assets/icons/custom/custom53.svg",
  "revision": "e9c0cdc06dba0716d5249e0821d58f84"
}, {
  "url": "include/LD/assets/icons/custom/custom54.svg",
  "revision": "b3cba12f28a9887e487002414ebc3e0e"
}, {
  "url": "include/LD/assets/icons/custom/custom55.svg",
  "revision": "abeeca371b9f2cb92333879ef35bac0a"
}, {
  "url": "include/LD/assets/icons/custom/custom56.svg",
  "revision": "797877dbcbf836bb64122122e7600962"
}, {
  "url": "include/LD/assets/icons/custom/custom57.svg",
  "revision": "a8b7c831542fc3576fa470c235e99e13"
}, {
  "url": "include/LD/assets/icons/custom/custom58.svg",
  "revision": "e23672c66d1aa6bd7765775ea5230b49"
}, {
  "url": "include/LD/assets/icons/custom/custom59.svg",
  "revision": "2f4f1b9032be7bfe0f821262a33b3278"
}, {
  "url": "include/LD/assets/icons/custom/custom6.svg",
  "revision": "e7bf237b7f3aae420c8aafcd492e1d65"
}, {
  "url": "include/LD/assets/icons/custom/custom60.svg",
  "revision": "d4f095417e6092127a2c0440625ea51e"
}, {
  "url": "include/LD/assets/icons/custom/custom61.svg",
  "revision": "38af17c1637b547eda80ec7d997b59f2"
}, {
  "url": "include/LD/assets/icons/custom/custom62.svg",
  "revision": "b5ddb92db8ffd85485252d960c851ee3"
}, {
  "url": "include/LD/assets/icons/custom/custom63.svg",
  "revision": "cec22aa1cd309eeaf32830045b6c8bcf"
}, {
  "url": "include/LD/assets/icons/custom/custom64.svg",
  "revision": "2310fd69a443ae2c33876c4513c2fa47"
}, {
  "url": "include/LD/assets/icons/custom/custom65.svg",
  "revision": "dbb69c6df0bd91b4561cd0713612fe78"
}, {
  "url": "include/LD/assets/icons/custom/custom66.svg",
  "revision": "a6421885586c58079d29cc12cb36257f"
}, {
  "url": "include/LD/assets/icons/custom/custom67.svg",
  "revision": "4b38490846d1877ce1ac3b0ec3424460"
}, {
  "url": "include/LD/assets/icons/custom/custom68.svg",
  "revision": "414ab6508b130f6d8d70120928890717"
}, {
  "url": "include/LD/assets/icons/custom/custom69.svg",
  "revision": "f29bcf345f547ddeb7297d18c6adcb64"
}, {
  "url": "include/LD/assets/icons/custom/custom7.svg",
  "revision": "ff2f155c921a429e688fac08711a2c70"
}, {
  "url": "include/LD/assets/icons/custom/custom70.svg",
  "revision": "d1060f8a7e14becc99c4f235710a34b9"
}, {
  "url": "include/LD/assets/icons/custom/custom71.svg",
  "revision": "bdebfb7e8f14913325af160fa30da878"
}, {
  "url": "include/LD/assets/icons/custom/custom72.svg",
  "revision": "2529655d62bf9ef6055d3c914aefdf8f"
}, {
  "url": "include/LD/assets/icons/custom/custom73.svg",
  "revision": "c6a3c1a046f03f868bf7afa525c4f6f1"
}, {
  "url": "include/LD/assets/icons/custom/custom74.svg",
  "revision": "ec7d8b6e85a8ae896b40a0d5bdc70bb3"
}, {
  "url": "include/LD/assets/icons/custom/custom75.svg",
  "revision": "8a5a24c6a9908c19eb1caeebbf56805d"
}, {
  "url": "include/LD/assets/icons/custom/custom76.svg",
  "revision": "de2b4dfcb5af211a2eee465575d449c6"
}, {
  "url": "include/LD/assets/icons/custom/custom77.svg",
  "revision": "1f27d23caf8ca5febe4b8141ea91ae05"
}, {
  "url": "include/LD/assets/icons/custom/custom78.svg",
  "revision": "3d34d7214dd006e3205fa0d6aa52bb3b"
}, {
  "url": "include/LD/assets/icons/custom/custom79.svg",
  "revision": "fe0f1424f32fde8232cd2213a57b7b7b"
}, {
  "url": "include/LD/assets/icons/custom/custom8.svg",
  "revision": "6f0468d538b8448dc6e6b73dd535a56e"
}, {
  "url": "include/LD/assets/icons/custom/custom80.svg",
  "revision": "9adb90afcfba1373f1ade870e18dcb95"
}, {
  "url": "include/LD/assets/icons/custom/custom81.svg",
  "revision": "e79ecd2a736cc5261fd987c9ab92f865"
}, {
  "url": "include/LD/assets/icons/custom/custom82.svg",
  "revision": "17be52d6170e455403d35692b5217542"
}, {
  "url": "include/LD/assets/icons/custom/custom83.svg",
  "revision": "e1107faee29797c874e89024f759dea9"
}, {
  "url": "include/LD/assets/icons/custom/custom84.svg",
  "revision": "303a0078a00ca827c6957cc3a2d229e6"
}, {
  "url": "include/LD/assets/icons/custom/custom85.svg",
  "revision": "8fc73f3bfbd02b8fabc1b364d8e6251b"
}, {
  "url": "include/LD/assets/icons/custom/custom86.svg",
  "revision": "0e076877b21989d18e82e273eb94fe2f"
}, {
  "url": "include/LD/assets/icons/custom/custom87.svg",
  "revision": "dc5c173d3be26ddab7ebdce9b06aaff1"
}, {
  "url": "include/LD/assets/icons/custom/custom88.svg",
  "revision": "cf12c9fc9943ebe83fa08f5fda70c880"
}, {
  "url": "include/LD/assets/icons/custom/custom89.svg",
  "revision": "635d047c5bed0cfc65b3aba41e347ec6"
}, {
  "url": "include/LD/assets/icons/custom/custom9.svg",
  "revision": "da6c777d5dad80500ef4cd716fa23a06"
}, {
  "url": "include/LD/assets/icons/custom/custom90.svg",
  "revision": "2b66d6cc0d355eef2fe2b087afa72d4c"
}, {
  "url": "include/LD/assets/icons/custom/custom91.svg",
  "revision": "24ca8917f798a3865a9f0311cf7afc3e"
}, {
  "url": "include/LD/assets/icons/custom/custom92.svg",
  "revision": "cfdae05e229d7c802714b815128fc65c"
}, {
  "url": "include/LD/assets/icons/custom/custom93.svg",
  "revision": "de99f9af7bff6f27c3a2419952790ca2"
}, {
  "url": "include/LD/assets/icons/custom/custom94.svg",
  "revision": "3f7b7e1f8c23f75fe87af4ed4668666b"
}, {
  "url": "include/LD/assets/icons/custom/custom95.svg",
  "revision": "998e6c74aa8ffef1baeb92a3ed4fe2a0"
}, {
  "url": "include/LD/assets/icons/custom/custom96.svg",
  "revision": "5723dba6e1f1c5e29db3cf09795e30a6"
}, {
  "url": "include/LD/assets/icons/custom/custom97.svg",
  "revision": "fc9cd69e95cd588b51b76a2a00d789b2"
}, {
  "url": "include/LD/assets/icons/custom/custom98.svg",
  "revision": "788039ffcac20ee0f85654f711a3900b"
}, {
  "url": "include/LD/assets/icons/custom/custom99.svg",
  "revision": "ab3b1d397f324cd0bc39b2aa22a7c823"
}, {
  "url": "include/LD/assets/icons/doctype-sprite/svg/symbols-rtl.svg",
  "revision": "6fcc97d2a0e1a45fc20337bb4a657be0"
}, {
  "url": "include/LD/assets/icons/doctype-sprite/svg/symbols.svg",
  "revision": "db41022c28f7b9291edad8f4c72e4a9f"
}, {
  "url": "include/LD/assets/icons/doctype/ai.svg",
  "revision": "8b4a4de8b278c408bd4225f8dcab8168"
}, {
  "url": "include/LD/assets/icons/doctype/attachment.svg",
  "revision": "d1c1cad4ce1d68501d025b6c99e0578c"
}, {
  "url": "include/LD/assets/icons/doctype/audio.svg",
  "revision": "b3bbd3c0c0138088c8b882ed93bfd57a"
}, {
  "url": "include/LD/assets/icons/doctype/box_notes.svg",
  "revision": "b524d374c7adac6c4c5c285364c684b2"
}, {
  "url": "include/LD/assets/icons/doctype/csv.svg",
  "revision": "0df6aa3359185ff5b14fae6b55181047"
}, {
  "url": "include/LD/assets/icons/doctype/eps.svg",
  "revision": "dfc475c62b75bcb33aba470ee6e05299"
}, {
  "url": "include/LD/assets/icons/doctype/excel.svg",
  "revision": "54a06c39da81daf2613b7ddc691dcc07"
}, {
  "url": "include/LD/assets/icons/doctype/exe.svg",
  "revision": "dbc49a97885ff524aa6b12d195c16b60"
}, {
  "url": "include/LD/assets/icons/doctype/flash.svg",
  "revision": "01db8ea88835720e440563b608338fb2"
}, {
  "url": "include/LD/assets/icons/doctype/folder.svg",
  "revision": "cc3eee142a88b55f59afed93fb41068e"
}, {
  "url": "include/LD/assets/icons/doctype/gdoc.svg",
  "revision": "bb9f8a66d93635809d17bfc3716f5b7a"
}, {
  "url": "include/LD/assets/icons/doctype/gdocs.svg",
  "revision": "077febbcfc84d40fcf33d9fe3dc41b93"
}, {
  "url": "include/LD/assets/icons/doctype/gform.svg",
  "revision": "025f9e2b2c0a99bd49c4f350dd18c158"
}, {
  "url": "include/LD/assets/icons/doctype/gpres.svg",
  "revision": "ebcaf0bf7f4478f855aeaa19e1224563"
}, {
  "url": "include/LD/assets/icons/doctype/gsheet.svg",
  "revision": "3c2702db7b522aa951ba387abc12c606"
}, {
  "url": "include/LD/assets/icons/doctype/html.svg",
  "revision": "3886685dac0e975b5caf04781a01fc60"
}, {
  "url": "include/LD/assets/icons/doctype/image.svg",
  "revision": "e26c5b8c989bc9026111cb9a0741f4ca"
}, {
  "url": "include/LD/assets/icons/doctype/keynote.svg",
  "revision": "1eabc141365bc2fdc2c8e1cc954c1977"
}, {
  "url": "include/LD/assets/icons/doctype/library_folder.svg",
  "revision": "e21cbd2fd87f487993abd30f884d7c87"
}, {
  "url": "include/LD/assets/icons/doctype/link.svg",
  "revision": "14bb71c084920a0c99de51dd29ddcfad"
}, {
  "url": "include/LD/assets/icons/doctype/mp4.svg",
  "revision": "289929ae0731c163d35fca8e6a4fbe50"
}, {
  "url": "include/LD/assets/icons/doctype/overlay.svg",
  "revision": "00ebbcf8bb264d530a098599e0a77866"
}, {
  "url": "include/LD/assets/icons/doctype/pack.svg",
  "revision": "b3942cf53423c730e9f62f31ce0a2d00"
}, {
  "url": "include/LD/assets/icons/doctype/pages.svg",
  "revision": "1e72114b3877b356e8bb91ecb87d231d"
}, {
  "url": "include/LD/assets/icons/doctype/pdf.svg",
  "revision": "fb4b5a6309f704f4a288cff4ecf0ffce"
}, {
  "url": "include/LD/assets/icons/doctype/ppt.svg",
  "revision": "6303e0006976f1a9866ab986c0565030"
}, {
  "url": "include/LD/assets/icons/doctype/psd.svg",
  "revision": "ac5d914e8e9c6f232630fb77dd3a0f94"
}, {
  "url": "include/LD/assets/icons/doctype/quip_doc.svg",
  "revision": "024bb8988907da37f1102b34952776c2"
}, {
  "url": "include/LD/assets/icons/doctype/quip_sheet.svg",
  "revision": "d8f044a3722fc03d8e722bf2f99c0ac9"
}, {
  "url": "include/LD/assets/icons/doctype/quip_slide.svg",
  "revision": "ccbf0447825c700e2b8f52fd618f736e"
}, {
  "url": "include/LD/assets/icons/doctype/rtf.svg",
  "revision": "4556242e5e720c5ebb318586b394e09b"
}, {
  "url": "include/LD/assets/icons/doctype/slide.svg",
  "revision": "6cc00bdbe748d432f8caaf9c6392057b"
}, {
  "url": "include/LD/assets/icons/doctype/stypi.svg",
  "revision": "e8ccac53e8a64a2ffc66cfc4e10d88de"
}, {
  "url": "include/LD/assets/icons/doctype/txt.svg",
  "revision": "94a367f70b5e8f4ee43456e55c289868"
}, {
  "url": "include/LD/assets/icons/doctype/unknown.svg",
  "revision": "8ce647a8e3630ed8693874bebfa87130"
}, {
  "url": "include/LD/assets/icons/doctype/video.svg",
  "revision": "6fabea2a370e29380cb1a67dff9727d3"
}, {
  "url": "include/LD/assets/icons/doctype/visio.svg",
  "revision": "b8cdd577222277f025564071714d6885"
}, {
  "url": "include/LD/assets/icons/doctype/webex.svg",
  "revision": "0bd37100a1095cbff1948f00dd3ee774"
}, {
  "url": "include/LD/assets/icons/doctype/word.svg",
  "revision": "fa0623d14fb56f95e84669fe444ec2d9"
}, {
  "url": "include/LD/assets/icons/doctype/xml.svg",
  "revision": "f5efeb8bfc60daf035e240b2e9dc2a9d"
}, {
  "url": "include/LD/assets/icons/doctype/zip.svg",
  "revision": "c55127645b2639803b13826da1d378bc"
}, {
  "url": "include/LD/assets/icons/standard-sprite/svg/symbols-rtl.svg",
  "revision": "777b6c44cc1675990b8b305e39cc161a"
}, {
  "url": "include/LD/assets/icons/standard-sprite/svg/symbols.svg",
  "revision": "e33e78ac59f964743d3d89daca2d89f1"
}, {
  "url": "include/LD/assets/icons/standard/account_info.svg",
  "revision": "96b1c6df2051de9cc5c13e1b3bad4361"
}, {
  "url": "include/LD/assets/icons/standard/account.svg",
  "revision": "9b6dc919aa62383651eb3386a09c85d7"
}, {
  "url": "include/LD/assets/icons/standard/action_list_component.svg",
  "revision": "8beb777a861c5edc6d80b957761b3cd6"
}, {
  "url": "include/LD/assets/icons/standard/actions_and_buttons.svg",
  "revision": "e02636a639960eece8b19b0ba0723ea2"
}, {
  "url": "include/LD/assets/icons/standard/activation_target.svg",
  "revision": "176e1d60f859d0e1e4aa2963f866c843"
}, {
  "url": "include/LD/assets/icons/standard/activations.svg",
  "revision": "9305c20aff6e406681d3d8e86af4a826"
}, {
  "url": "include/LD/assets/icons/standard/address.svg",
  "revision": "16b6319208b2593d5ca64409cb3d5bfe"
}, {
  "url": "include/LD/assets/icons/standard/agent_home.svg",
  "revision": "104b8b207cba22dafd52fd9a4715df5e"
}, {
  "url": "include/LD/assets/icons/standard/agent_session.svg",
  "revision": "37427f3774b555b56e0939bd1f174c8b"
}, {
  "url": "include/LD/assets/icons/standard/aggregation_policy.svg",
  "revision": "c803f5b662f7e6854bf8272733f9c7df"
}, {
  "url": "include/LD/assets/icons/standard/all.svg",
  "revision": "ca204f973a36cdbd6bb2edf700bd3ae3"
}, {
  "url": "include/LD/assets/icons/standard/announcement.svg",
  "revision": "622d28097efe3c7900aaf64b5571f1cf"
}, {
  "url": "include/LD/assets/icons/standard/answer_best.svg",
  "revision": "afef77cc17a32db25f2bd21cee0858e8"
}, {
  "url": "include/LD/assets/icons/standard/answer_private.svg",
  "revision": "50657d94e2ae65dd67fddc3956c23beb"
}, {
  "url": "include/LD/assets/icons/standard/answer_public.svg",
  "revision": "8ae70cd8912fb0c2b62a0df378db8a1a"
}, {
  "url": "include/LD/assets/icons/standard/apex_plugin.svg",
  "revision": "adaad4b417ada453fc71ed5695fa4130"
}, {
  "url": "include/LD/assets/icons/standard/apex.svg",
  "revision": "ec20620b379bdd4c484040d93db23df6"
}, {
  "url": "include/LD/assets/icons/standard/app.svg",
  "revision": "03c2697b22cb7cfc74c6fccae9ca1c0d"
}, {
  "url": "include/LD/assets/icons/standard/approval.svg",
  "revision": "f0eb4a6da60e8917ee03f3e6068a5e56"
}, {
  "url": "include/LD/assets/icons/standard/apps_admin.svg",
  "revision": "57852a7fb70fe7a1098c6270c892359c"
}, {
  "url": "include/LD/assets/icons/standard/apps.svg",
  "revision": "239de371d06019e54c2666b5dbe2f9c2"
}, {
  "url": "include/LD/assets/icons/standard/article.svg",
  "revision": "d886274e602aead6110d7854ccb99fb7"
}, {
  "url": "include/LD/assets/icons/standard/asset_action_source.svg",
  "revision": "935e10ece87a564035d668079db10de8"
}, {
  "url": "include/LD/assets/icons/standard/asset_action.svg",
  "revision": "4644ca09996e1823c2a67f63b5b86ade"
}, {
  "url": "include/LD/assets/icons/standard/asset_audit.svg",
  "revision": "061897290c865f2643ffd12aaf7024ee"
}, {
  "url": "include/LD/assets/icons/standard/asset_downtime_period.svg",
  "revision": "e618f9a20a105d9977a1fbf62da8c4ba"
}, {
  "url": "include/LD/assets/icons/standard/asset_object.svg",
  "revision": "78677844e48625e013e9a92222dab028"
}, {
  "url": "include/LD/assets/icons/standard/asset_relationship.svg",
  "revision": "4163db872faecafcf7490297993acc92"
}, {
  "url": "include/LD/assets/icons/standard/asset_state_period.svg",
  "revision": "891f384a6702da5f2f671c870bc43285"
}, {
  "url": "include/LD/assets/icons/standard/asset_warranty.svg",
  "revision": "7fa66991329c5b8a06d6c435e0a647c5"
}, {
  "url": "include/LD/assets/icons/standard/assigned_resource.svg",
  "revision": "6c49414e9f4fa25de313eff7fad9842e"
}, {
  "url": "include/LD/assets/icons/standard/assignment.svg",
  "revision": "1023cf0390477cea6c9e6b5f09eb7b51"
}, {
  "url": "include/LD/assets/icons/standard/attach.svg",
  "revision": "c6e0974e6c44d6222778ef6e7b4c8322"
}, {
  "url": "include/LD/assets/icons/standard/avatar_loading.svg",
  "revision": "f417e8f4b2b28af029a955b438fc82ea"
}, {
  "url": "include/LD/assets/icons/standard/avatar.svg",
  "revision": "0357e330d4edbe28d946cdf7a2d827aa"
}, {
  "url": "include/LD/assets/icons/standard/bot_training.svg",
  "revision": "7277c9ac62eae977bee00043192e7fe7"
}, {
  "url": "include/LD/assets/icons/standard/bot.svg",
  "revision": "6ce48e0c876bf24cc33d50b9cada6551"
}, {
  "url": "include/LD/assets/icons/standard/branch_merge.svg",
  "revision": "01235779fca1e486c4003728a974935c"
}, {
  "url": "include/LD/assets/icons/standard/brand.svg",
  "revision": "5e619f969dbb44e673fda5127f286520"
}, {
  "url": "include/LD/assets/icons/standard/budget_allocation.svg",
  "revision": "b3159060de3dc0da403b2edfe6e548a5"
}, {
  "url": "include/LD/assets/icons/standard/budget.svg",
  "revision": "75a558faea4efbb1aae1f0b6692fcd8f"
}, {
  "url": "include/LD/assets/icons/standard/bundle_config.svg",
  "revision": "efedcc9422437b9bffa3af92f038bf07"
}, {
  "url": "include/LD/assets/icons/standard/bundle_policy.svg",
  "revision": "d7a7fe72132109e21b0958c82b6d8dbc"
}, {
  "url": "include/LD/assets/icons/standard/business_hours.svg",
  "revision": "cb0f101405531460c573b6d5d48a1ca1"
}, {
  "url": "include/LD/assets/icons/standard/buyer_account.svg",
  "revision": "1c69c852087732271d395a7663b2e818"
}, {
  "url": "include/LD/assets/icons/standard/buyer_group.svg",
  "revision": "a77f79e8885e37c95d6e9dcac2af900a"
}, {
  "url": "include/LD/assets/icons/standard/calculated_insights.svg",
  "revision": "5a77d86c59defeb0f3fee5ed41207b10"
}, {
  "url": "include/LD/assets/icons/standard/calibration.svg",
  "revision": "76dc601ebb7a2e448971abe2f0b50e7e"
}, {
  "url": "include/LD/assets/icons/standard/call_coaching.svg",
  "revision": "1e4cd5a6385b2eb767c815a5e619456d"
}, {
  "url": "include/LD/assets/icons/standard/call_history.svg",
  "revision": "8282f183fde79bbb7d1dbcdac44c66bf"
}, {
  "url": "include/LD/assets/icons/standard/call.svg",
  "revision": "c5ac85635ab87389101792a1c6c984db"
}, {
  "url": "include/LD/assets/icons/standard/campaign_members.svg",
  "revision": "f60d7394be1d751ca06b9bd92516e54d"
}, {
  "url": "include/LD/assets/icons/standard/campaign.svg",
  "revision": "a740b413d5ed3d8f8e3a8961b99ccf1c"
}, {
  "url": "include/LD/assets/icons/standard/cancel_checkout.svg",
  "revision": "612516ace45ac4b5d7b2983566138406"
}, {
  "url": "include/LD/assets/icons/standard/canvas.svg",
  "revision": "8272da3cf18dbdb6f8ee5fa0d9f08c1a"
}, {
  "url": "include/LD/assets/icons/standard/capacity_plan.svg",
  "revision": "b2ebd710c1b0739455c720b28397fba3"
}, {
  "url": "include/LD/assets/icons/standard/care_request_reviewer.svg",
  "revision": "7983e5f9b724d2a5a6b89b573fbb4789"
}, {
  "url": "include/LD/assets/icons/standard/carousel.svg",
  "revision": "32eef45c2f0f9c19a8028a7898e9df0a"
}, {
  "url": "include/LD/assets/icons/standard/case_change_status.svg",
  "revision": "c031900f177b3616f733c45157c067a6"
}, {
  "url": "include/LD/assets/icons/standard/case_comment.svg",
  "revision": "9eed5a89f0dda0b84726ec84e81c3fe1"
}, {
  "url": "include/LD/assets/icons/standard/case_email.svg",
  "revision": "1c945dc5c622bb5ef71000230ba65146"
}, {
  "url": "include/LD/assets/icons/standard/case_log_a_call.svg",
  "revision": "75f6410f10228d3582b4e9f9c633a9df"
}, {
  "url": "include/LD/assets/icons/standard/case_milestone.svg",
  "revision": "59be7b2b110f5832d5157a6176c5d877"
}, {
  "url": "include/LD/assets/icons/standard/case_transcript.svg",
  "revision": "962d9a3921f26d44c8c5d0429448a06c"
}, {
  "url": "include/LD/assets/icons/standard/case_wrap_up.svg",
  "revision": "5cace7e3340cfa9137d4a8427a2b0ba1"
}, {
  "url": "include/LD/assets/icons/standard/case.svg",
  "revision": "e73e897f74e49066f88e75aeac517b15"
}, {
  "url": "include/LD/assets/icons/standard/catalog.svg",
  "revision": "e8893d82f16e0755bf9b193baf24b7bf"
}, {
  "url": "include/LD/assets/icons/standard/category.svg",
  "revision": "70be4684609369a307cd08ba76e1eca7"
}, {
  "url": "include/LD/assets/icons/standard/change_request.svg",
  "revision": "572a1c904ae44ecf72650ec8c71616bc"
}, {
  "url": "include/LD/assets/icons/standard/channel_program_history.svg",
  "revision": "a17e19910281a95388d9601726e7fe1b"
}, {
  "url": "include/LD/assets/icons/standard/channel_program_levels.svg",
  "revision": "0b1e587962fb4c8bca6e2532e760b9d1"
}, {
  "url": "include/LD/assets/icons/standard/channel_program_members.svg",
  "revision": "efeae32d3808d9b168130efa691d3937"
}, {
  "url": "include/LD/assets/icons/standard/channel_programs.svg",
  "revision": "4b33e06c188fb0af8d06b03daef832c5"
}, {
  "url": "include/LD/assets/icons/standard/chart.svg",
  "revision": "b73b5f27fed3baa53c80d35dcb3c3955"
}, {
  "url": "include/LD/assets/icons/standard/checkout.svg",
  "revision": "4efe6cb8ba25f19765e6e3b1d9b20f95"
}, {
  "url": "include/LD/assets/icons/standard/choice.svg",
  "revision": "8ea23ca111aee7337c20f84049479161"
}, {
  "url": "include/LD/assets/icons/standard/client.svg",
  "revision": "5924e9dcd2126bb48bc1b842bfc213d1"
}, {
  "url": "include/LD/assets/icons/standard/cms.svg",
  "revision": "79b93c2ad8bb24b5ac44572a9f4755d0"
}, {
  "url": "include/LD/assets/icons/standard/coaching.svg",
  "revision": "f7243514a5a3cf9d81d4953910b00c0a"
}, {
  "url": "include/LD/assets/icons/standard/code_playground.svg",
  "revision": "52f911400eb680ecc091f06bba6cc52f"
}, {
  "url": "include/LD/assets/icons/standard/code_set_bundle.svg",
  "revision": "22bfe302644abd8b26b6d377d95a5f1c"
}, {
  "url": "include/LD/assets/icons/standard/code_set.svg",
  "revision": "b250cd33fa9b50e62655b081ad568f30"
}, {
  "url": "include/LD/assets/icons/standard/collection_variable.svg",
  "revision": "2bf949cebcb9ee78f908e5245f7ee50b"
}, {
  "url": "include/LD/assets/icons/standard/collection.svg",
  "revision": "e69b570ffa1a5d3b1cc15f86f2cef9db"
}, {
  "url": "include/LD/assets/icons/standard/connected_apps.svg",
  "revision": "55aca9d4f081e50e04e383a470669de3"
}, {
  "url": "include/LD/assets/icons/standard/constant.svg",
  "revision": "c3c317522472cb3bc0ffcae0088ec7f0"
}, {
  "url": "include/LD/assets/icons/standard/contact_list.svg",
  "revision": "05ce63c76219871ba757bb451f254094"
}, {
  "url": "include/LD/assets/icons/standard/contact_request.svg",
  "revision": "c6701b98acac15a08ef4ff68da177c30"
}, {
  "url": "include/LD/assets/icons/standard/contact.svg",
  "revision": "4c9ac663a83a449a3a112e43f8c5fc47"
}, {
  "url": "include/LD/assets/icons/standard/contract_line_item.svg",
  "revision": "df12db57a17a58704c7057ecb9d45ecc"
}, {
  "url": "include/LD/assets/icons/standard/contract_payment.svg",
  "revision": "b75cb6823180db99899f267a4ee2d72f"
}, {
  "url": "include/LD/assets/icons/standard/contract.svg",
  "revision": "4db400f850fad314164c3a1f5e7c9dc9"
}, {
  "url": "include/LD/assets/icons/standard/coupon_codes.svg",
  "revision": "627027c01495b5a8fbe03856b6fc15e2"
}, {
  "url": "include/LD/assets/icons/standard/currency_input.svg",
  "revision": "329be2bdecaebc553c6a7c8ed432a467"
}, {
  "url": "include/LD/assets/icons/standard/currency.svg",
  "revision": "3ec373f9c80f30c4ffedb7cd30830007"
}, {
  "url": "include/LD/assets/icons/standard/custom_component_task.svg",
  "revision": "8bf7b8fb3b3ff622664ffe5928c4b5ff"
}, {
  "url": "include/LD/assets/icons/standard/custom_notification.svg",
  "revision": "7532c5c1020231b1b7e2f3e65fbc13cd"
}, {
  "url": "include/LD/assets/icons/standard/custom.svg",
  "revision": "94ab851f7ad269920452189e477bc904"
}, {
  "url": "include/LD/assets/icons/standard/customer_360.svg",
  "revision": "c50b5851f55c7d70e4c47bf64af81911"
}, {
  "url": "include/LD/assets/icons/standard/customer_lifecycle_analytics.svg",
  "revision": "b94452aaca2b2aca7b7d499fa94b73a1"
}, {
  "url": "include/LD/assets/icons/standard/customer_portal_users.svg",
  "revision": "36e4b80bb8db840257e34a3b79901198"
}, {
  "url": "include/LD/assets/icons/standard/customers.svg",
  "revision": "8c45ee58edda26b2a7fc45e95a30e85a"
}, {
  "url": "include/LD/assets/icons/standard/dashboard_ea.svg",
  "revision": "183656ae96a9f5ce6ccf4adc2dbfa7ee"
}, {
  "url": "include/LD/assets/icons/standard/dashboard.svg",
  "revision": "cefcb93c6f0552cab099e462ca1ab332"
}, {
  "url": "include/LD/assets/icons/standard/data_integration_hub.svg",
  "revision": "ac44e86e1e60ea50ddbd5926dd8adeb8"
}, {
  "url": "include/LD/assets/icons/standard/data_mapping.svg",
  "revision": "b01b38ebe40ae763efa4188ff4bf820a"
}, {
  "url": "include/LD/assets/icons/standard/data_model.svg",
  "revision": "751fe2c0233aeb60b01b0094a340a258"
}, {
  "url": "include/LD/assets/icons/standard/data_streams.svg",
  "revision": "1b34b23ef7b31357be7abcc7263dbcb6"
}, {
  "url": "include/LD/assets/icons/standard/datadotcom.svg",
  "revision": "91e8a97597f2c676dec699620fbe609d"
}, {
  "url": "include/LD/assets/icons/standard/dataset.svg",
  "revision": "5d5540bdfa3092ae2b1d599b536e5992"
}, {
  "url": "include/LD/assets/icons/standard/date_input.svg",
  "revision": "a8d1b5cbd1e688dee9774207bef8062b"
}, {
  "url": "include/LD/assets/icons/standard/date_time.svg",
  "revision": "4cef81d48f5cfb2c8b2d64006d66e839"
}, {
  "url": "include/LD/assets/icons/standard/decision.svg",
  "revision": "73a6231e89a8896a26ad49bc172ab3f6"
}, {
  "url": "include/LD/assets/icons/standard/default.svg",
  "revision": "a03920127c8b6c7d2942bcb5ca1d9981"
}, {
  "url": "include/LD/assets/icons/standard/delegated_account.svg",
  "revision": "290bb63539636f0f6385def81b7d5b04"
}, {
  "url": "include/LD/assets/icons/standard/device.svg",
  "revision": "1b6d8250a854aefec3fe2499fb8e7d63"
}, {
  "url": "include/LD/assets/icons/standard/discounts.svg",
  "revision": "19a86c6643be24dad6c0d079c1765df9"
}, {
  "url": "include/LD/assets/icons/standard/display_rich_text.svg",
  "revision": "e1a31a8386a73d0100b7e5015e542e0f"
}, {
  "url": "include/LD/assets/icons/standard/display_text.svg",
  "revision": "37061e33f4216123ef13ac17f33b77c8"
}, {
  "url": "include/LD/assets/icons/standard/document_reference.svg",
  "revision": "ca146af851d5d8a84f4d3755dd9b9c15"
}, {
  "url": "include/LD/assets/icons/standard/document.svg",
  "revision": "c668c38381465a3abdbe7d2064a0fffa"
}, {
  "url": "include/LD/assets/icons/standard/drafts.svg",
  "revision": "94125cde4f387fbf7234ebed744b1c94"
}, {
  "url": "include/LD/assets/icons/standard/duration_downscale.svg",
  "revision": "5ca938239fc662973b95eaac283665f3"
}, {
  "url": "include/LD/assets/icons/standard/dynamic_record_choice.svg",
  "revision": "7e15fba48ef595b83a3785c9a442d332"
}, {
  "url": "include/LD/assets/icons/standard/education.svg",
  "revision": "066a5226dd99caf18790bfaa73220cd1"
}, {
  "url": "include/LD/assets/icons/standard/einstein_replies.svg",
  "revision": "516d626b2b7afb1383c88db487fe1c61"
}, {
  "url": "include/LD/assets/icons/standard/email_chatter.svg",
  "revision": "240bad6b4a8f861a9067302a063d3f74"
}, {
  "url": "include/LD/assets/icons/standard/email.svg",
  "revision": "240bad6b4a8f861a9067302a063d3f74"
}, {
  "url": "include/LD/assets/icons/standard/employee_asset.svg",
  "revision": "767032ef19c9cbd852c3dfe2ffac46b0"
}, {
  "url": "include/LD/assets/icons/standard/employee_contact.svg",
  "revision": "9550aeb382231e8684a4e84b8aad2b59"
}, {
  "url": "include/LD/assets/icons/standard/employee_job_position.svg",
  "revision": "09f5601b00c34275c5899a5f80dbf49e"
}, {
  "url": "include/LD/assets/icons/standard/employee_job.svg",
  "revision": "df1e4e67a48dc2857ebf2eadb331d4dd"
}, {
  "url": "include/LD/assets/icons/standard/employee_organization.svg",
  "revision": "b5df129d4c874a4d92ec1db7579a4d60"
}, {
  "url": "include/LD/assets/icons/standard/employee.svg",
  "revision": "f50c947b2b119ea2dd95c326330ddd41"
}, {
  "url": "include/LD/assets/icons/standard/empty.svg",
  "revision": "d10b2a23e065993ceceaf6841eb00854"
}, {
  "url": "include/LD/assets/icons/standard/endorsement.svg",
  "revision": "89b7cf0bc08f18beff1e653048ee0a09"
}, {
  "url": "include/LD/assets/icons/standard/entitlement_policy.svg",
  "revision": "784602e7a7e3739a80dbea59dcd0e60a"
}, {
  "url": "include/LD/assets/icons/standard/entitlement_process.svg",
  "revision": "e0df1a91947621aba81b160cb6d9dde6"
}, {
  "url": "include/LD/assets/icons/standard/entitlement_template.svg",
  "revision": "5a4571b0f77f60de0a5f16245af2590c"
}, {
  "url": "include/LD/assets/icons/standard/entitlement.svg",
  "revision": "bdf2a9c34058b7374390ea4c8a82f348"
}, {
  "url": "include/LD/assets/icons/standard/entity_milestone.svg",
  "revision": "52f4b915c5a1e115d2cba88cd2602765"
}, {
  "url": "include/LD/assets/icons/standard/entity.svg",
  "revision": "11f31c533bb91abd1d6cfb504e60e4fc"
}, {
  "url": "include/LD/assets/icons/standard/environment_hub.svg",
  "revision": "da1b1c6eee1d8def1340089a11b70581"
}, {
  "url": "include/LD/assets/icons/standard/event.svg",
  "revision": "4654ac473ba8bb910494e2658e9a3304"
}, {
  "url": "include/LD/assets/icons/standard/events.svg",
  "revision": "225a6d1fec90ccba205967ea31d22ac5"
}, {
  "url": "include/LD/assets/icons/standard/expense_report_entry.svg",
  "revision": "65784972c221ba385043396e09f22306"
}, {
  "url": "include/LD/assets/icons/standard/expense_report.svg",
  "revision": "1f4899bf56680de37abdb99f4e32e85a"
}, {
  "url": "include/LD/assets/icons/standard/expense.svg",
  "revision": "9c8209eb3e05236b67330a0bd03cb042"
}, {
  "url": "include/LD/assets/icons/standard/feed.svg",
  "revision": "f139ab6a51919807747ae4624918798a"
}, {
  "url": "include/LD/assets/icons/standard/feedback.svg",
  "revision": "51c08235ede4692b59faf1d8b9cc1d3e"
}, {
  "url": "include/LD/assets/icons/standard/field_sales.svg",
  "revision": "580540101f5ce375b6ff09e3112c296b"
}, {
  "url": "include/LD/assets/icons/standard/file.svg",
  "revision": "2b014c79b2f51b08eac4e1894bdaa742"
}, {
  "url": "include/LD/assets/icons/standard/filter_criteria_rule.svg",
  "revision": "310f578e47eb26fcfc7b2280b4fcb986"
}, {
  "url": "include/LD/assets/icons/standard/filter_criteria.svg",
  "revision": "654c4b31085949b8c378c326dd91cfe6"
}, {
  "url": "include/LD/assets/icons/standard/filter.svg",
  "revision": "4735d13bdfa2a557d978f4d1d7fd46ad"
}, {
  "url": "include/LD/assets/icons/standard/first_non_empty.svg",
  "revision": "86e27d55725dbcaf88eae8ae9125436b"
}, {
  "url": "include/LD/assets/icons/standard/flow.svg",
  "revision": "a85d9d4e1bfa0664c15c71e7bcf48243"
}, {
  "url": "include/LD/assets/icons/standard/folder.svg",
  "revision": "1b15c123f26542ab9957d1e395efb69e"
}, {
  "url": "include/LD/assets/icons/standard/forecasts.svg",
  "revision": "c0a4d35ccaf4e63225479b83d2f2d60d"
}, {
  "url": "include/LD/assets/icons/standard/form.svg",
  "revision": "b1ae589b6baf9866d5fc79a1e524fc55"
}, {
  "url": "include/LD/assets/icons/standard/formula.svg",
  "revision": "45bf2f6c11c9f74679abf9035673a2a2"
}, {
  "url": "include/LD/assets/icons/standard/fulfillment_order.svg",
  "revision": "47c6ceeae1f1c06eee3d29612f452b99"
}, {
  "url": "include/LD/assets/icons/standard/generic_loading.svg",
  "revision": "1da2f273be0f2d8c1f71a7da6f46ded4"
}, {
  "url": "include/LD/assets/icons/standard/global_constant.svg",
  "revision": "eb26657e4c577f3b670a88fecb246de2"
}, {
  "url": "include/LD/assets/icons/standard/goals.svg",
  "revision": "cd235f6be06291a9d4e3eff700086b82"
}, {
  "url": "include/LD/assets/icons/standard/group_loading.svg",
  "revision": "8ab9392e2a89983b00f93be0acb450b6"
}, {
  "url": "include/LD/assets/icons/standard/groups.svg",
  "revision": "44d73c97dbd03e04ea30cb8a0d36ebb2"
}, {
  "url": "include/LD/assets/icons/standard/guidance_center.svg",
  "revision": "6a8288a92db4bd811cabc8d6c4ec0871"
}, {
  "url": "include/LD/assets/icons/standard/hierarchy.svg",
  "revision": "fe3ed50570f7445ca6e7cd440e906145"
}, {
  "url": "include/LD/assets/icons/standard/high_velocity_sales.svg",
  "revision": "cbf13f3f5c4c6dd37e00adad9da119b7"
}, {
  "url": "include/LD/assets/icons/standard/historical_adherence.svg",
  "revision": "754a4a79c8170e8091e24176b789e54b"
}, {
  "url": "include/LD/assets/icons/standard/holiday_operating_hours.svg",
  "revision": "b31372cb90de28fcf4a6dbdb2ce6c913"
}, {
  "url": "include/LD/assets/icons/standard/home.svg",
  "revision": "c1a771ec63256b998e04631445431066"
}, {
  "url": "include/LD/assets/icons/standard/household.svg",
  "revision": "2eb9d4f53ecc1a48a1fd44e4ff6fefd6"
}, {
  "url": "include/LD/assets/icons/standard/identifier.svg",
  "revision": "68595053f09ba0ed6812f2f38d605c4c"
}, {
  "url": "include/LD/assets/icons/standard/immunization.svg",
  "revision": "559a397ba65e281a6672565418302639"
}, {
  "url": "include/LD/assets/icons/standard/incident.svg",
  "revision": "6752c470644a24e13c81e2a371d22143"
}, {
  "url": "include/LD/assets/icons/standard/individual.svg",
  "revision": "0670ddc28ec3a02e988ab7cc97c787c0"
}, {
  "url": "include/LD/assets/icons/standard/insights.svg",
  "revision": "65b3ec9a650d5f1ee6821a1549428c1b"
}, {
  "url": "include/LD/assets/icons/standard/instore_locations.svg",
  "revision": "51d995563fc0ed56e0417ff66707beb5"
}, {
  "url": "include/LD/assets/icons/standard/investment_account.svg",
  "revision": "857ca3da5ba00f81d647daf3fbaa853a"
}, {
  "url": "include/LD/assets/icons/standard/invocable_action.svg",
  "revision": "31ca2f2371cf8efee93475d30b21f181"
}, {
  "url": "include/LD/assets/icons/standard/iot_context.svg",
  "revision": "9ca5664436794287edb2c0c355067497"
}, {
  "url": "include/LD/assets/icons/standard/iot_orchestrations.svg",
  "revision": "d4312bd3c3e43a94543b8b4128d5aa3c"
}, {
  "url": "include/LD/assets/icons/standard/javascript_button.svg",
  "revision": "622fc44e067afcfa517914efbfca48ea"
}, {
  "url": "include/LD/assets/icons/standard/job_family.svg",
  "revision": "65bbeced70e471e30c701aa84fe404e9"
}, {
  "url": "include/LD/assets/icons/standard/job_position.svg",
  "revision": "ea0ce6daf4c62d51b3018fd6080e8361"
}, {
  "url": "include/LD/assets/icons/standard/job_profile.svg",
  "revision": "dca711534c1543846e27a6a0e591ade0"
}, {
  "url": "include/LD/assets/icons/standard/kanban.svg",
  "revision": "7e192a247ab2883185a18b664470c536"
}, {
  "url": "include/LD/assets/icons/standard/key_dates.svg",
  "revision": "1f5195400cea58d8f15d56b1cfc97b3b"
}, {
  "url": "include/LD/assets/icons/standard/knowledge.svg",
  "revision": "bd4cd89d04d7e95049c393e1e1f3d331"
}, {
  "url": "include/LD/assets/icons/standard/lead_insights.svg",
  "revision": "d31624f9eb0dee7b3823189b754563df"
}, {
  "url": "include/LD/assets/icons/standard/lead_list.svg",
  "revision": "d749b49a7b565d4d4f8af91a1ce0b7d8"
}, {
  "url": "include/LD/assets/icons/standard/lead.svg",
  "revision": "19dc4486071f3e2721e3361146cc2f2b"
}, {
  "url": "include/LD/assets/icons/standard/letterhead.svg",
  "revision": "d74171024d298cad3cd16939d426fec5"
}, {
  "url": "include/LD/assets/icons/standard/lightning_component.svg",
  "revision": "d699b3044afe98ab6c87838c89b6df73"
}, {
  "url": "include/LD/assets/icons/standard/lightning_usage.svg",
  "revision": "f083655274347428b4460c3867072c46"
}, {
  "url": "include/LD/assets/icons/standard/link.svg",
  "revision": "8a2a5fc4431733ceda3ba712535a10e4"
}, {
  "url": "include/LD/assets/icons/standard/list_email.svg",
  "revision": "f503951d387d73a8cc9308640da2e4f7"
}, {
  "url": "include/LD/assets/icons/standard/live_chat_visitor.svg",
  "revision": "1c5e5e2021c84cc320aa3aa1c608ca74"
}, {
  "url": "include/LD/assets/icons/standard/live_chat.svg",
  "revision": "3dace92704610e2e24afdd2765ebd654"
}, {
  "url": "include/LD/assets/icons/standard/location_permit.svg",
  "revision": "0265795e11d7dbf63a4247bcf39e4c44"
}, {
  "url": "include/LD/assets/icons/standard/location.svg",
  "revision": "444aa9515f61be652135f140ddcbf659"
}, {
  "url": "include/LD/assets/icons/standard/log_a_call.svg",
  "revision": "e415ad1c5a33c68066ddd490ed0265a2"
}, {
  "url": "include/LD/assets/icons/standard/logging.svg",
  "revision": "7f85b72db3b2df3f7bba7a2804583485"
}, {
  "url": "include/LD/assets/icons/standard/loop.svg",
  "revision": "a3a9306c00ea30e75dd3d1140cc5ec9f"
}, {
  "url": "include/LD/assets/icons/standard/macros.svg",
  "revision": "08919cc836691355c85eee13bcfba8cd"
}, {
  "url": "include/LD/assets/icons/standard/maintenance_asset.svg",
  "revision": "d6f03b86433c8cfbc7c19808c77ba4f6"
}, {
  "url": "include/LD/assets/icons/standard/maintenance_plan.svg",
  "revision": "3919cb0b0ef50a13803d3902a499c9fb"
}, {
  "url": "include/LD/assets/icons/standard/maintenance_work_rule.svg",
  "revision": "8b8ec846cd5d853cb329aaa4cd46b5c3"
}, {
  "url": "include/LD/assets/icons/standard/marketing_actions.svg",
  "revision": "15ba449302789cbaa6829c62a57214f3"
}, {
  "url": "include/LD/assets/icons/standard/med_rec_recommendation.svg",
  "revision": "84f293cd46cd308d0ce56321d276582d"
}, {
  "url": "include/LD/assets/icons/standard/med_rec_statement_recommendation.svg",
  "revision": "261a10b30540f4f534a28aeab2569f9a"
}, {
  "url": "include/LD/assets/icons/standard/medication_dispense.svg",
  "revision": "e3f90feec94caf4d64b1a1c27c74fcaa"
}, {
  "url": "include/LD/assets/icons/standard/medication_ingredient.svg",
  "revision": "9b216d8943a5680c8cb22ba0c7f53526"
}, {
  "url": "include/LD/assets/icons/standard/medication_reconciliation.svg",
  "revision": "68f04aecd578075c487326c015575d88"
}, {
  "url": "include/LD/assets/icons/standard/medication_statement.svg",
  "revision": "c2d89405b269a4bdbcc9a756530320e9"
}, {
  "url": "include/LD/assets/icons/standard/medication.svg",
  "revision": "f0895445464272351ae06e74fa6bab0d"
}, {
  "url": "include/LD/assets/icons/standard/merge.svg",
  "revision": "c92c7046129d5fc340f545ec9d30429f"
}, {
  "url": "include/LD/assets/icons/standard/messaging_conversation.svg",
  "revision": "7725979405b4f7c89cbb626bf3fe9eb9"
}, {
  "url": "include/LD/assets/icons/standard/messaging_session.svg",
  "revision": "58391a767ec1a596125c912362d3068a"
}, {
  "url": "include/LD/assets/icons/standard/messaging_user.svg",
  "revision": "3a9c419f27c067138ff3b08312c71621"
}, {
  "url": "include/LD/assets/icons/standard/metrics.svg",
  "revision": "f3bd723a496e4d9a7983ecf35684932b"
}, {
  "url": "include/LD/assets/icons/standard/multi_picklist.svg",
  "revision": "73a0f8823b78e9375be021d8a583c150"
}, {
  "url": "include/LD/assets/icons/standard/multi_select_checkbox.svg",
  "revision": "c5fea759c21e4fab02264f9d91f1fb24"
}, {
  "url": "include/LD/assets/icons/standard/network_contract.svg",
  "revision": "9e449b15d42b8fb71235bab0d9b43033"
}, {
  "url": "include/LD/assets/icons/standard/news.svg",
  "revision": "65b3ec9a650d5f1ee6821a1549428c1b"
}, {
  "url": "include/LD/assets/icons/standard/note.svg",
  "revision": "0bd620b4d00682b13cd1c80c1c483254"
}, {
  "url": "include/LD/assets/icons/standard/number_input.svg",
  "revision": "b4b21776cc7cfd6bb87c26d809f7f45d"
}, {
  "url": "include/LD/assets/icons/standard/observation_component.svg",
  "revision": "d2b1d59572ea654bbb5c9466ca4d1644"
}, {
  "url": "include/LD/assets/icons/standard/omni_supervisor.svg",
  "revision": "c0af58ba4caf51918d9eeee5ca081f79"
}, {
  "url": "include/LD/assets/icons/standard/operating_hours.svg",
  "revision": "3d739e8f81c3788d01795a492df7deb6"
}, {
  "url": "include/LD/assets/icons/standard/opportunity_contact_role.svg",
  "revision": "369eb5bdd48e31e96a70be3f3d289333"
}, {
  "url": "include/LD/assets/icons/standard/opportunity_splits.svg",
  "revision": "4da9e46a674078e673fe8aab02ebea5f"
}, {
  "url": "include/LD/assets/icons/standard/opportunity.svg",
  "revision": "45414bddb8380d384126828fa5d1434f"
}, {
  "url": "include/LD/assets/icons/standard/orchestrator.svg",
  "revision": "9c83335854aa43c91b0fc6eb7f699403"
}, {
  "url": "include/LD/assets/icons/standard/order_item.svg",
  "revision": "2786cf38fb07333e469ff91930564880"
}, {
  "url": "include/LD/assets/icons/standard/orders.svg",
  "revision": "e5f650d86e9963cdd3397fb4c89db656"
}, {
  "url": "include/LD/assets/icons/standard/outcome.svg",
  "revision": "a235fecee9323094a3c77f18b8f1fc26"
}, {
  "url": "include/LD/assets/icons/standard/output.svg",
  "revision": "4ca8786eabd273a979ef3a4bdd9ab120"
}, {
  "url": "include/LD/assets/icons/standard/partner_fund_allocation.svg",
  "revision": "f4d96d8e6933385f5f35d44342864ae4"
}, {
  "url": "include/LD/assets/icons/standard/partner_fund_claim.svg",
  "revision": "31e09c1df9ac2fa5117133f8067cf481"
}, {
  "url": "include/LD/assets/icons/standard/partner_fund_request.svg",
  "revision": "1414c5732ff5de301c757a27aa366be4"
}, {
  "url": "include/LD/assets/icons/standard/partner_marketing_budget.svg",
  "revision": "c7a95416c09cb46b856aa99388fb97d6"
}, {
  "url": "include/LD/assets/icons/standard/partners.svg",
  "revision": "f295f16329cd88e5187746d0626d279e"
}, {
  "url": "include/LD/assets/icons/standard/password.svg",
  "revision": "3290ee19f5e8f0643bfb0c229983a19d"
}, {
  "url": "include/LD/assets/icons/standard/past_chat.svg",
  "revision": "d682cb6eb29b63116c0764c8133d0b47"
}, {
  "url": "include/LD/assets/icons/standard/patient_medication_dosage.svg",
  "revision": "579e40154d9613547e23dd7916c1c262"
}, {
  "url": "include/LD/assets/icons/standard/payment_gateway.svg",
  "revision": "8262c3b36191daff57ffbd852ef293cd"
}, {
  "url": "include/LD/assets/icons/standard/people.svg",
  "revision": "0357e330d4edbe28d946cdf7a2d827aa"
}, {
  "url": "include/LD/assets/icons/standard/performance.svg",
  "revision": "e47d4d3d69a4290ba7ce2be6727a99cf"
}, {
  "url": "include/LD/assets/icons/standard/person_account.svg",
  "revision": "dc693eac85facc3919cfe9d3bba2cdd6"
}, {
  "url": "include/LD/assets/icons/standard/person_language.svg",
  "revision": "85bf56fa983400209fd2313d220fabfa"
}, {
  "url": "include/LD/assets/icons/standard/person_name.svg",
  "revision": "eef6a47f284590d5f7cd6460e8fdb6f6"
}, {
  "url": "include/LD/assets/icons/standard/photo.svg",
  "revision": "1b26558b3bd9d2e83783a79591cb9c19"
}, {
  "url": "include/LD/assets/icons/standard/picklist_choice.svg",
  "revision": "433f8d910cba185e51c0c0f3467095a5"
}, {
  "url": "include/LD/assets/icons/standard/picklist_type.svg",
  "revision": "261176fbc8752c9ef32ad1eb3cf28e9e"
}, {
  "url": "include/LD/assets/icons/standard/planogram.svg",
  "revision": "729cfdc7261b9c86922ee1882238406c"
}, {
  "url": "include/LD/assets/icons/standard/poll.svg",
  "revision": "f14707ecbf6e727eeea449d98b864728"
}, {
  "url": "include/LD/assets/icons/standard/portal_roles_and_subordinates.svg",
  "revision": "1cc8e1b02ce69591ce3855bd979b2ca0"
}, {
  "url": "include/LD/assets/icons/standard/portal_roles.svg",
  "revision": "bbe952ce112b2184c0cb544ddc91f080"
}, {
  "url": "include/LD/assets/icons/standard/portal.svg",
  "revision": "f73d5ac59ae33fbff07f7c217dfe04ff"
}, {
  "url": "include/LD/assets/icons/standard/post.svg",
  "revision": "da6cf968b482bdde06a8982d364ad11a"
}, {
  "url": "include/LD/assets/icons/standard/practitioner_role.svg",
  "revision": "a25a3ceade923631b11bb90faed93668"
}, {
  "url": "include/LD/assets/icons/standard/price_book_entries.svg",
  "revision": "48549324dd604dcbe1853122d6d15c47"
}, {
  "url": "include/LD/assets/icons/standard/price_books.svg",
  "revision": "d6226cad88d74ef0765af8e48ce2c2db"
}, {
  "url": "include/LD/assets/icons/standard/pricebook.svg",
  "revision": "9551af8f1044c793a9e852e4fee799b4"
}, {
  "url": "include/LD/assets/icons/standard/pricing_workspace.svg",
  "revision": "55e165f0ece42bb63e59a9f0d28ff308"
}, {
  "url": "include/LD/assets/icons/standard/problem.svg",
  "revision": "80bd2016399a9a5d515664369a46999c"
}, {
  "url": "include/LD/assets/icons/standard/procedure_detail.svg",
  "revision": "3e7e86828ff2c1338f0859bdf91462d8"
}, {
  "url": "include/LD/assets/icons/standard/procedure.svg",
  "revision": "6fb2156b8294c6e8255a4b64fd42f550"
}, {
  "url": "include/LD/assets/icons/standard/process_exception.svg",
  "revision": "7fc7edf71d1e65a70b368d1935e2fc5e"
}, {
  "url": "include/LD/assets/icons/standard/process.svg",
  "revision": "bfcf8411544ee42e8e85110562e8394a"
}, {
  "url": "include/LD/assets/icons/standard/product_consumed_state.svg",
  "revision": "cac321e1b0f07425e495db825fbdb8ac"
}, {
  "url": "include/LD/assets/icons/standard/product_consumed.svg",
  "revision": "d139b381753bd677451e75973731a113"
}, {
  "url": "include/LD/assets/icons/standard/product_item_transaction.svg",
  "revision": "1d0c10fc39576f197a5ba71f42889672"
}, {
  "url": "include/LD/assets/icons/standard/product_item.svg",
  "revision": "29523ed6b8a89b023ecb104ada2039bc"
}, {
  "url": "include/LD/assets/icons/standard/product_quantity_rules.svg",
  "revision": "5031dd08a6831ca254606c67098abd67"
}, {
  "url": "include/LD/assets/icons/standard/product_request_line_item.svg",
  "revision": "2f6c92f16b1027f89ed2638d478cc8ba"
}, {
  "url": "include/LD/assets/icons/standard/product_request.svg",
  "revision": "e75b27d4114aefc42bd047dad915a1a5"
}, {
  "url": "include/LD/assets/icons/standard/product_required.svg",
  "revision": "ad274140f20a8aa3ad9ac6fbaadce7c5"
}, {
  "url": "include/LD/assets/icons/standard/product_service_campaign_item.svg",
  "revision": "7b8144906a66efde5309ce3e9bf13473"
}, {
  "url": "include/LD/assets/icons/standard/product_service_campaign.svg",
  "revision": "8cf86682fa60d4c2cf17d2e826c49134"
}, {
  "url": "include/LD/assets/icons/standard/product_transfer_state.svg",
  "revision": "b9d59e609569e44d45c33828d7881e8f"
}, {
  "url": "include/LD/assets/icons/standard/product_transfer.svg",
  "revision": "3efe6db9707001b8f48affc8b67bde1b"
}, {
  "url": "include/LD/assets/icons/standard/product_warranty_term.svg",
  "revision": "8d6c166b96b67c961903fc28c2bcf13d"
}, {
  "url": "include/LD/assets/icons/standard/product_workspace.svg",
  "revision": "0b6e654afb306375a77214476572779a"
}, {
  "url": "include/LD/assets/icons/standard/product.svg",
  "revision": "e0cf8eb6428eb9ca76212d5fa6dd5985"
}, {
  "url": "include/LD/assets/icons/standard/products.svg",
  "revision": "f3f0136462245b7f843deafe40543103"
}, {
  "url": "include/LD/assets/icons/standard/promotion_segments.svg",
  "revision": "325104eda92fc40f42210b615dbb8f58"
}, {
  "url": "include/LD/assets/icons/standard/promotions_workspace.svg",
  "revision": "d4af6de6ff15f60d49e47bcbb9bb2b20"
}, {
  "url": "include/LD/assets/icons/standard/promotions.svg",
  "revision": "75879ba5a957b91c6420915ebf6c3999"
}, {
  "url": "include/LD/assets/icons/standard/propagation_policy.svg",
  "revision": "a1f7e8a063eee0452428b7cb6daf3b91"
}, {
  "url": "include/LD/assets/icons/standard/proposition.svg",
  "revision": "0750c6165fe382c1160723c00e8002bd"
}, {
  "url": "include/LD/assets/icons/standard/qualifications.svg",
  "revision": "fc5291836448bf539d6eecff44a81bad"
}, {
  "url": "include/LD/assets/icons/standard/question_best.svg",
  "revision": "2324838ae3639479089613e8ff16dadc"
}, {
  "url": "include/LD/assets/icons/standard/question_feed.svg",
  "revision": "963e4908af45321d62a79f2efb9a8a7f"
}, {
  "url": "include/LD/assets/icons/standard/queue.svg",
  "revision": "d429150c719f982ae75f553a3f7bf7d2"
}, {
  "url": "include/LD/assets/icons/standard/quick_text.svg",
  "revision": "2603a8ccd56d4fffbf8c360c2f2369cd"
}, {
  "url": "include/LD/assets/icons/standard/quip_sheet.svg",
  "revision": "0df82a66a30362c9d4612d8220fd38df"
}, {
  "url": "include/LD/assets/icons/standard/quip.svg",
  "revision": "4a07cf4ab1cba93ebff1a083faf5db40"
}, {
  "url": "include/LD/assets/icons/standard/quotes.svg",
  "revision": "1db18df6839da79bd372d44861cd0bd0"
}, {
  "url": "include/LD/assets/icons/standard/radio_button.svg",
  "revision": "34d3ee01015a124d2a940e1b22f82dd4"
}, {
  "url": "include/LD/assets/icons/standard/read_receipts.svg",
  "revision": "60f8488047a4a32f45e7c6a4d3000200"
}, {
  "url": "include/LD/assets/icons/standard/recent.svg",
  "revision": "c2fd55706184f1220acb2dc4447b55d1"
}, {
  "url": "include/LD/assets/icons/standard/recipe.svg",
  "revision": "9c1ae87eb1bfe55a262c8d813128935c"
}, {
  "url": "include/LD/assets/icons/standard/record_create.svg",
  "revision": "e3b4147c11df4960ca36cdec273fe9f3"
}, {
  "url": "include/LD/assets/icons/standard/record_delete.svg",
  "revision": "19a406e2c72220becbfbfc8425bb2573"
}, {
  "url": "include/LD/assets/icons/standard/record_lookup.svg",
  "revision": "bd16a2d70e427fe1eab715ed7afa36a7"
}, {
  "url": "include/LD/assets/icons/standard/record_signature_task.svg",
  "revision": "d2e24390b202fa425fbf7c08fe4f12b0"
}, {
  "url": "include/LD/assets/icons/standard/record_update.svg",
  "revision": "eeebc78d7faa53854fa6e4c9019d7ec4"
}, {
  "url": "include/LD/assets/icons/standard/record.svg",
  "revision": "da378197aac500d6f01fb6c4a9364a35"
}, {
  "url": "include/LD/assets/icons/standard/recycle_bin.svg",
  "revision": "3bca5e3c30bfed2cab79e0cd430bd7ef"
}, {
  "url": "include/LD/assets/icons/standard/related_list.svg",
  "revision": "ce58414c440b5a5646dcaf4b2b058082"
}, {
  "url": "include/LD/assets/icons/standard/relationship.svg",
  "revision": "4386b9a44c7ca8b19d65b273ec36b64f"
}, {
  "url": "include/LD/assets/icons/standard/reply_text.svg",
  "revision": "31024955803ff7c7942d5fb98e5949bf"
}, {
  "url": "include/LD/assets/icons/standard/report_type.svg",
  "revision": "6e9514b12ba3e298fe1a406c2cd00840"
}, {
  "url": "include/LD/assets/icons/standard/report.svg",
  "revision": "061e0f45107a5587e47c1b8397a0f10d"
}, {
  "url": "include/LD/assets/icons/standard/resource_absence.svg",
  "revision": "c12955b27ac7144298020e5a53c78fc9"
}, {
  "url": "include/LD/assets/icons/standard/resource_capacity.svg",
  "revision": "3f8bb6da24c1d7a0c38ba8c569f6ae42"
}, {
  "url": "include/LD/assets/icons/standard/resource_preference.svg",
  "revision": "16d873ab635bbb5089f65c5ca1e67b03"
}, {
  "url": "include/LD/assets/icons/standard/resource_skill.svg",
  "revision": "9d01bdcc17b0f28c58a0179449b2efca"
}, {
  "url": "include/LD/assets/icons/standard/restriction_policy.svg",
  "revision": "053e75d51250f3372235ff57ff7f9ded"
}, {
  "url": "include/LD/assets/icons/standard/return_order_line_item.svg",
  "revision": "247255106888d1f63a4f58706776b8ee"
}, {
  "url": "include/LD/assets/icons/standard/return_order.svg",
  "revision": "27b6de2b7742569ba77bc08bd08ab1cb"
}, {
  "url": "include/LD/assets/icons/standard/reward.svg",
  "revision": "1ecbd6347280ea3f3ed776d0f7254642"
}, {
  "url": "include/LD/assets/icons/standard/rtc_presence.svg",
  "revision": "83e02a0c9d5966aab62133690d66bb88"
}, {
  "url": "include/LD/assets/icons/standard/sales_cadence_target.svg",
  "revision": "8b6857bb76b5092e286c092351ee0ef6"
}, {
  "url": "include/LD/assets/icons/standard/sales_cadence.svg",
  "revision": "4a52dbb516dbff4bd7686508b98a08a3"
}, {
  "url": "include/LD/assets/icons/standard/sales_channel.svg",
  "revision": "92a78e23bca1f4ebb24576771fe05729"
}, {
  "url": "include/LD/assets/icons/standard/sales_path.svg",
  "revision": "c71d6122a016e25a1dc20d9789b3b6c1"
}, {
  "url": "include/LD/assets/icons/standard/sales_value.svg",
  "revision": "30a944e52b9351561acb44f72b4619bf"
}, {
  "url": "include/LD/assets/icons/standard/salesforce_cms.svg",
  "revision": "f6189057c5c03c132be5cfc478579282"
}, {
  "url": "include/LD/assets/icons/standard/scan_card.svg",
  "revision": "c165f6760ba56302580ad19e6df98d08"
}, {
  "url": "include/LD/assets/icons/standard/schedule_objective.svg",
  "revision": "3f97e9c7f50143a6c53798e25871a157"
}, {
  "url": "include/LD/assets/icons/standard/scheduling_constraint.svg",
  "revision": "052af09535e86421a7c3dc02f7d8d62d"
}, {
  "url": "include/LD/assets/icons/standard/scheduling_policy.svg",
  "revision": "810eb7b9d6c674ce08a7d720d04b6fa7"
}, {
  "url": "include/LD/assets/icons/standard/screen.svg",
  "revision": "7bb85c6c813a6e59f8da2c7867ae18f5"
}, {
  "url": "include/LD/assets/icons/standard/search.svg",
  "revision": "6c678821819d9af08f002cf0a75f5dc5"
}, {
  "url": "include/LD/assets/icons/standard/section.svg",
  "revision": "edaeb7838e5155a72c2c0b4d2aff0c7e"
}, {
  "url": "include/LD/assets/icons/standard/segments.svg",
  "revision": "e501f32d9febae4cfd76ef6d71efba74"
}, {
  "url": "include/LD/assets/icons/standard/selling_model.svg",
  "revision": "51672f206200643cf193610146cf9183"
}, {
  "url": "include/LD/assets/icons/standard/serialized_product_transaction.svg",
  "revision": "02ab377d221b06d528cd9e8968c10536"
}, {
  "url": "include/LD/assets/icons/standard/serialized_product.svg",
  "revision": "166cce32fd33cc1d5b36b1e0c3c52b1d"
}, {
  "url": "include/LD/assets/icons/standard/service_appointment_capacity_usage.svg",
  "revision": "b9a0a616f49d03eff3e76b3d5ff838cb"
}, {
  "url": "include/LD/assets/icons/standard/service_appointment.svg",
  "revision": "377ffc18d88ee9f06e06694c3da5d42b"
}, {
  "url": "include/LD/assets/icons/standard/service_contract.svg",
  "revision": "e5ba973e0b10dadbd1a389a5c1ab3386"
}, {
  "url": "include/LD/assets/icons/standard/service_crew_member.svg",
  "revision": "94080cc4180a18bebbc6bbebbda8c4b0"
}, {
  "url": "include/LD/assets/icons/standard/service_crew.svg",
  "revision": "6c7e875ed1a321129dc14fce7a578aa2"
}, {
  "url": "include/LD/assets/icons/standard/service_report.svg",
  "revision": "cd1564f9e08963d8f5cc8e4a6561f179"
}, {
  "url": "include/LD/assets/icons/standard/service_request_detail.svg",
  "revision": "2b2ef5d76700116f540cc6ece71fbe78"
}, {
  "url": "include/LD/assets/icons/standard/service_request.svg",
  "revision": "90c1d488cb778abf11cf86d73784976b"
}, {
  "url": "include/LD/assets/icons/standard/service_resource.svg",
  "revision": "c1f48606c104252a99cb4e591476fbce"
}, {
  "url": "include/LD/assets/icons/standard/service_territory_location.svg",
  "revision": "d6d2da781456a7e3b2ab94b47f88641e"
}, {
  "url": "include/LD/assets/icons/standard/service_territory_member.svg",
  "revision": "aacf6cc5ef7894b61e646d95c185c305"
}, {
  "url": "include/LD/assets/icons/standard/service_territory_policy.svg",
  "revision": "9ad929102d0e66dc49520a4959a00bf6"
}, {
  "url": "include/LD/assets/icons/standard/service_territory.svg",
  "revision": "c60ae41140a47b99bc0d16edc84c65bc"
}, {
  "url": "include/LD/assets/icons/standard/settings.svg",
  "revision": "977b0b69187b63e269749ad29d8c5028"
}, {
  "url": "include/LD/assets/icons/standard/shift_pattern_entry.svg",
  "revision": "f3fc262501c387700915cbda5499b7d1"
}, {
  "url": "include/LD/assets/icons/standard/shift_pattern.svg",
  "revision": "73e19e14c21778d61ac5cc80a811ce56"
}, {
  "url": "include/LD/assets/icons/standard/shift_preference.svg",
  "revision": "5280434ad947ec33b24995b85033280d"
}, {
  "url": "include/LD/assets/icons/standard/shift_scheduling_operation.svg",
  "revision": "68f1ffd63cc356e035005bda73c1e548"
}, {
  "url": "include/LD/assets/icons/standard/shift_template.svg",
  "revision": "12574a780efaebe9c5f2cb97943e167e"
}, {
  "url": "include/LD/assets/icons/standard/shift_type.svg",
  "revision": "db8d20db0846df34ae9a879bbab5e9e0"
}, {
  "url": "include/LD/assets/icons/standard/shift.svg",
  "revision": "22d339616dac68e89b1a519aee0982aa"
}, {
  "url": "include/LD/assets/icons/standard/shipment.svg",
  "revision": "ce67a798df7cae160dffdac2f0857891"
}, {
  "url": "include/LD/assets/icons/standard/skill_entity.svg",
  "revision": "267331a7c57b1457479eb73c23d71cd6"
}, {
  "url": "include/LD/assets/icons/standard/skill_requirement.svg",
  "revision": "faf0c3c57dea8a2bc8703229344711d1"
}, {
  "url": "include/LD/assets/icons/standard/skill.svg",
  "revision": "33bcada12cf78627e9efa0f18e6c56c9"
}, {
  "url": "include/LD/assets/icons/standard/slider.svg",
  "revision": "0136a9e3477efdd12c633aa1a75007c3"
}, {
  "url": "include/LD/assets/icons/standard/sms.svg",
  "revision": "b2fb0c3d256aedd96889eb995b81b502"
}, {
  "url": "include/LD/assets/icons/standard/snippet.svg",
  "revision": "3004c5428652c84c0284cf72bd1467a1"
}, {
  "url": "include/LD/assets/icons/standard/snippets.svg",
  "revision": "f5187057f24b6b4647d1c97b0e70c99c"
}, {
  "url": "include/LD/assets/icons/standard/sobject_collection.svg",
  "revision": "e4b30be49dbace5ff9bd1989e24ac94b"
}, {
  "url": "include/LD/assets/icons/standard/sobject.svg",
  "revision": "af07e5c1f26c3470970c8638e54107d7"
}, {
  "url": "include/LD/assets/icons/standard/social.svg",
  "revision": "b9384925439027d1e8167b38fc03298e"
}, {
  "url": "include/LD/assets/icons/standard/solution.svg",
  "revision": "5caabc6d05b26acdf182f0d122e40387"
}, {
  "url": "include/LD/assets/icons/standard/sort_policy.svg",
  "revision": "684004615fbfbd922f8f815a5a5e8a59"
}, {
  "url": "include/LD/assets/icons/standard/sort.svg",
  "revision": "7a86addeab02712fb7e97d1d11af9448"
}, {
  "url": "include/LD/assets/icons/standard/sossession.svg",
  "revision": "0442407a3e39adc80361914cbe6c8575"
}, {
  "url": "include/LD/assets/icons/standard/stage_collection.svg",
  "revision": "af5a1293e7c5b316216099a75e5a785e"
}, {
  "url": "include/LD/assets/icons/standard/stage.svg",
  "revision": "830b46c72f9af4d7cbdae2626e4cef24"
}, {
  "url": "include/LD/assets/icons/standard/steps.svg",
  "revision": "df2aa311622513697c8eff9844670672"
}, {
  "url": "include/LD/assets/icons/standard/store_group.svg",
  "revision": "d19893e798c2a8e2e0747490c7ef8baf"
}, {
  "url": "include/LD/assets/icons/standard/store.svg",
  "revision": "f6acd4de8cd594ce4d6763068700766d"
}, {
  "url": "include/LD/assets/icons/standard/story.svg",
  "revision": "4b1ceddaf3f2611a8640d766e825eaa5"
}, {
  "url": "include/LD/assets/icons/standard/strategy.svg",
  "revision": "c0cefa8ea8b52d8a38a17b038596f0bb"
}, {
  "url": "include/LD/assets/icons/standard/survey.svg",
  "revision": "e197df030459363e9f7a6a367be71134"
}, {
  "url": "include/LD/assets/icons/standard/swarm_request.svg",
  "revision": "55b7b19f11714aec98b39615788cd24d"
}, {
  "url": "include/LD/assets/icons/standard/swarm_session.svg",
  "revision": "3f6b3d4af3b726546f6aaf70f439d89e"
}, {
  "url": "include/LD/assets/icons/standard/system_and_global_variable.svg",
  "revision": "f1f38795f55fbb950cfd51911e53e2bf"
}, {
  "url": "include/LD/assets/icons/standard/task.svg",
  "revision": "67cf069e6763f230c776290dc59a2781"
}, {
  "url": "include/LD/assets/icons/standard/task2.svg",
  "revision": "d6ed7aed50c90e77fddd4d384c07927d"
}, {
  "url": "include/LD/assets/icons/standard/team_member.svg",
  "revision": "a8073d64ac4bfb15a4a22f4773056202"
}, {
  "url": "include/LD/assets/icons/standard/template.svg",
  "revision": "dd31912c3b50ed0ab9d4b47aed650064"
}, {
  "url": "include/LD/assets/icons/standard/text_template.svg",
  "revision": "5e18032872dcf98f5ba1fd7f46df2e2d"
}, {
  "url": "include/LD/assets/icons/standard/text.svg",
  "revision": "5d5c6c3e2650a6eaf09e4c1b9f47367f"
}, {
  "url": "include/LD/assets/icons/standard/textarea.svg",
  "revision": "e748f0e6973367ab75e9d6c007cc703b"
}, {
  "url": "include/LD/assets/icons/standard/textbox.svg",
  "revision": "39501e3cccbdff8dc71e7a30bbfb7eb5"
}, {
  "url": "include/LD/assets/icons/standard/thanks_loading.svg",
  "revision": "9b0fcdadb66beb5fa1acf04ee7660fe7"
}, {
  "url": "include/LD/assets/icons/standard/thanks.svg",
  "revision": "d9701af923cc7fc9cbf27e6eeca06e07"
}, {
  "url": "include/LD/assets/icons/standard/timesheet_entry.svg",
  "revision": "c78af7ec4ee17184c77a0d7742df2742"
}, {
  "url": "include/LD/assets/icons/standard/timesheet.svg",
  "revision": "5db42f68ea56495ba94ad5da550b5ea6"
}, {
  "url": "include/LD/assets/icons/standard/timeslot.svg",
  "revision": "6bc7cab70d0b6a4c255a76b02bf2b4e0"
}, {
  "url": "include/LD/assets/icons/standard/today.svg",
  "revision": "20b63cb73ee7bf64a954eaf567ba53a0"
}, {
  "url": "include/LD/assets/icons/standard/toggle.svg",
  "revision": "3a1e22d6d0362a8a187eaae594a7c52b"
}, {
  "url": "include/LD/assets/icons/standard/topic.svg",
  "revision": "d963e27e983e6e4cb8e9992e68764e04"
}, {
  "url": "include/LD/assets/icons/standard/topic2.svg",
  "revision": "bd37d51c6c1072db1f49f0ee304228e4"
}, {
  "url": "include/LD/assets/icons/standard/tour_check.svg",
  "revision": "29f16b974c7d28d7bf263a2c1a876832"
}, {
  "url": "include/LD/assets/icons/standard/tour.svg",
  "revision": "77961f72e9b8f2ebd2cd34f0958ccd18"
}, {
  "url": "include/LD/assets/icons/standard/trailhead_alt.svg",
  "revision": "477140b1593cf25bac2a165731f0d701"
}, {
  "url": "include/LD/assets/icons/standard/trailhead.svg",
  "revision": "65bf3d41ea3ad87404b9e7dcf4de83fe"
}, {
  "url": "include/LD/assets/icons/standard/travel_mode.svg",
  "revision": "304ae391cd2032e36fde92e645fdd9be"
}, {
  "url": "include/LD/assets/icons/standard/unified_health_score.svg",
  "revision": "e71154f56645f87065aa9de6d798a458"
}, {
  "url": "include/LD/assets/icons/standard/unmatched.svg",
  "revision": "6701409641e59183e8f4ac3e09759316"
}, {
  "url": "include/LD/assets/icons/standard/user_role.svg",
  "revision": "76bf0337477c6d7399b84c69331568e4"
}, {
  "url": "include/LD/assets/icons/standard/user.svg",
  "revision": "0357e330d4edbe28d946cdf7a2d827aa"
}, {
  "url": "include/LD/assets/icons/standard/variable.svg",
  "revision": "51ce9564719905626b0da66155e49ac4"
}, {
  "url": "include/LD/assets/icons/standard/variation_attribute_setup.svg",
  "revision": "569865073fda0cb6dbf483162e93b18b"
}, {
  "url": "include/LD/assets/icons/standard/variation_products.svg",
  "revision": "360c2c364ca092a1f94b9f270a78895f"
}, {
  "url": "include/LD/assets/icons/standard/video.svg",
  "revision": "9ae3e6ee0993c2e42e49b3a25a1daf5b"
}, {
  "url": "include/LD/assets/icons/standard/visit_templates.svg",
  "revision": "cacf83377f1340405c2aaaecba6857be"
}, {
  "url": "include/LD/assets/icons/standard/visits.svg",
  "revision": "7a21e0046f3e562163977ce1715dbb95"
}, {
  "url": "include/LD/assets/icons/standard/visualforce_page.svg",
  "revision": "c1e7cb1eb37fae59c426335f888f848a"
}, {
  "url": "include/LD/assets/icons/standard/voice_call.svg",
  "revision": "2a5f6982f1590b55a088cddb4b7b8784"
}, {
  "url": "include/LD/assets/icons/standard/waits.svg",
  "revision": "e4c69e4d3a2c01402875ec3219911ee0"
}, {
  "url": "include/LD/assets/icons/standard/warranty_term.svg",
  "revision": "7dab230c5a23c5736f1f4e69914f32f8"
}, {
  "url": "include/LD/assets/icons/standard/webcart.svg",
  "revision": "822c2dc4a7fdedce7c98201758750368"
}, {
  "url": "include/LD/assets/icons/standard/work_capacity_limit.svg",
  "revision": "34a76cdd7b2e99c2aa86fc62908bf28c"
}, {
  "url": "include/LD/assets/icons/standard/work_capacity_usage.svg",
  "revision": "b1f1bab00fad69fefb27067997e03076"
}, {
  "url": "include/LD/assets/icons/standard/work_contract.svg",
  "revision": "f75be8262323e834d3830c22dbac4878"
}, {
  "url": "include/LD/assets/icons/standard/work_forecast.svg",
  "revision": "7a98ea10553a04a0209f00fbf2ded878"
}, {
  "url": "include/LD/assets/icons/standard/work_order_item.svg",
  "revision": "5f4544bdadf1e3718d825bd088537671"
}, {
  "url": "include/LD/assets/icons/standard/work_order.svg",
  "revision": "89ef4be892a38209d5c1dedcbd46e33b"
}, {
  "url": "include/LD/assets/icons/standard/work_plan_rule.svg",
  "revision": "d5a0a7485e952f0c1e771d307f24fdd1"
}, {
  "url": "include/LD/assets/icons/standard/work_plan_template_entry.svg",
  "revision": "6d4c525eb7a1ac4bdadaeca8ce2468ba"
}, {
  "url": "include/LD/assets/icons/standard/work_plan_template.svg",
  "revision": "2c24419463178e001961ba6d92f5d13f"
}, {
  "url": "include/LD/assets/icons/standard/work_plan.svg",
  "revision": "f096156c5c5aa7110a3642cdd7fa9e82"
}, {
  "url": "include/LD/assets/icons/standard/work_queue.svg",
  "revision": "e6087de7a4cf2ed25464e48fad9ebd78"
}, {
  "url": "include/LD/assets/icons/standard/work_step_template.svg",
  "revision": "615c2772c0fc0ed8bfd68c6027db62bc"
}, {
  "url": "include/LD/assets/icons/standard/work_step.svg",
  "revision": "03ea2aad5f2673e75364ba3edec41d7e"
}, {
  "url": "include/LD/assets/icons/standard/work_type_group.svg",
  "revision": "261a18c59966e884594c5bcf186d1795"
}, {
  "url": "include/LD/assets/icons/standard/work_type.svg",
  "revision": "69e72b17727305cbf439b04f79fc0774"
}, {
  "url": "include/LD/assets/icons/standard/workforce_engagement.svg",
  "revision": "d4138ed840b6c0c41891fd9d58b5f101"
}, {
  "url": "include/LD/assets/icons/utility-sprite/svg/symbols-rtl.svg",
  "revision": "025eefd0fc3e93a552019eb19931f238"
}, {
  "url": "include/LD/assets/icons/utility-sprite/svg/symbols.svg",
  "revision": "4b15345ebadeb353f67398898947310a"
}, {
  "url": "include/LD/assets/icons/utility/activity.svg",
  "revision": "412a5a24d455b68a7ead81eee0efadf3"
}, {
  "url": "include/LD/assets/icons/utility/ad_set.svg",
  "revision": "0830edfae68fc65bddc856c08ec5244e"
}, {
  "url": "include/LD/assets/icons/utility/add.svg",
  "revision": "d7cfd08833a7d763fcc36e089c5834f0"
}, {
  "url": "include/LD/assets/icons/utility/adduser.svg",
  "revision": "4a4925c234031fabc8cb89dd0eca1b53"
}, {
  "url": "include/LD/assets/icons/utility/adjust_value.svg",
  "revision": "2735d232c7be3260e37efa47ebb5d6df"
}, {
  "url": "include/LD/assets/icons/utility/advanced_function.svg",
  "revision": "713ecbeb10422e6681e3a81da6f7cc3e"
}, {
  "url": "include/LD/assets/icons/utility/agent_home.svg",
  "revision": "caa3e6d1485953b2ef8bc302f8e5a9d2"
}, {
  "url": "include/LD/assets/icons/utility/agent_session.svg",
  "revision": "70cc245f05045e81f947e519a35808bf"
}, {
  "url": "include/LD/assets/icons/utility/aggregation_policy.svg",
  "revision": "ca6b115be5a9f442afe6ac31b8b28174"
}, {
  "url": "include/LD/assets/icons/utility/alert.svg",
  "revision": "1980deace6631e21ca365fcc78523e38"
}, {
  "url": "include/LD/assets/icons/utility/all.svg",
  "revision": "5931a645d3035da4ac41ecf515d01f32"
}, {
  "url": "include/LD/assets/icons/utility/anchor.svg",
  "revision": "0ce13d0fc6e3ae7e4cf5d8e219a50657"
}, {
  "url": "include/LD/assets/icons/utility/animal_and_nature.svg",
  "revision": "93ec84492334b5b0406d88d1d5be8349"
}, {
  "url": "include/LD/assets/icons/utility/announcement.svg",
  "revision": "fa9c2267f994f13eb0f4a2365f483418"
}, {
  "url": "include/LD/assets/icons/utility/answer.svg",
  "revision": "287d77014cb985f4cc49608e4c43582e"
}, {
  "url": "include/LD/assets/icons/utility/answered_twice.svg",
  "revision": "0f9fd8428d0881dca97c34be7928d502"
}, {
  "url": "include/LD/assets/icons/utility/anywhere_alert.svg",
  "revision": "f990a84c3625867915ee7074a5796352"
}, {
  "url": "include/LD/assets/icons/utility/anywhere_chat.svg",
  "revision": "7a8b5f2d69b94162e7e6bec43431b30d"
}, {
  "url": "include/LD/assets/icons/utility/apex_plugin.svg",
  "revision": "d136d9711242b28488ce86222cee57a5"
}, {
  "url": "include/LD/assets/icons/utility/apex.svg",
  "revision": "8c36a8ac72d1d8d003c9c485fe0fa93c"
}, {
  "url": "include/LD/assets/icons/utility/approval.svg",
  "revision": "f36ad2573e360ed6a37d3cf3074bcc5f"
}, {
  "url": "include/LD/assets/icons/utility/apps.svg",
  "revision": "b8d277f8b5d31e145643923533b77f74"
}, {
  "url": "include/LD/assets/icons/utility/archive.svg",
  "revision": "d52f0de58589278e00ecb46d6cbb1c42"
}, {
  "url": "include/LD/assets/icons/utility/arrow_bottom.svg",
  "revision": "48058028f05d00a3227e71cdff3dce5f"
}, {
  "url": "include/LD/assets/icons/utility/arrow_left.svg",
  "revision": "d52af175175b06f4d0e2498bf8480936"
}, {
  "url": "include/LD/assets/icons/utility/arrow_right.svg",
  "revision": "a50e1ac69b21a4da9d18ac09ecaa6c3e"
}, {
  "url": "include/LD/assets/icons/utility/arrow_top.svg",
  "revision": "7bd0827a22c44828c10a50700e477c2c"
}, {
  "url": "include/LD/assets/icons/utility/arrowdown.svg",
  "revision": "b6956bf885b84d49eb06d7f89a41990c"
}, {
  "url": "include/LD/assets/icons/utility/arrowup.svg",
  "revision": "99f76d1583fdf3d28109012c15d65ed7"
}, {
  "url": "include/LD/assets/icons/utility/asset_audit.svg",
  "revision": "1606e85c044639e5a1779b56b393e76f"
}, {
  "url": "include/LD/assets/icons/utility/asset_warranty.svg",
  "revision": "61bc7fa36e8668f228e24ed0f11a26ca"
}, {
  "url": "include/LD/assets/icons/utility/assignment.svg",
  "revision": "43b28692edbbb20f9111fb5722f8cd7b"
}, {
  "url": "include/LD/assets/icons/utility/attach.svg",
  "revision": "b96a076b321fcd6fedc756db2c9f8875"
}, {
  "url": "include/LD/assets/icons/utility/automate.svg",
  "revision": "2993b76c90efd5dae2024adf530e2bc6"
}, {
  "url": "include/LD/assets/icons/utility/away.svg",
  "revision": "6a7d259c1bb6d9bfa350a0e2c5e3c169"
}, {
  "url": "include/LD/assets/icons/utility/az.svg",
  "revision": "d6a9581fea3113acf1db2e69fb871b5b"
}, {
  "url": "include/LD/assets/icons/utility/back.svg",
  "revision": "d142fff5d02d84dee6bb28157858c132"
}, {
  "url": "include/LD/assets/icons/utility/ban.svg",
  "revision": "f6820d8fa3cbc7696a62821726dec0cf"
}, {
  "url": "include/LD/assets/icons/utility/block_visitor.svg",
  "revision": "21fd3f50b516bdd51809186f09525d00"
}, {
  "url": "include/LD/assets/icons/utility/bold.svg",
  "revision": "fd3d4c56361cfb9fea1e9b5b529d02de"
}, {
  "url": "include/LD/assets/icons/utility/bookmark_alt.svg",
  "revision": "ec3fd02b1887196b8b19d31304c8f1af"
}, {
  "url": "include/LD/assets/icons/utility/bookmark.svg",
  "revision": "d1c12b972990451bffa2f2665e7934d5"
}, {
  "url": "include/LD/assets/icons/utility/bottom_align.svg",
  "revision": "2e06ecd74df1117fb93c1c1fd59c6e1c"
}, {
  "url": "include/LD/assets/icons/utility/breadcrumbs.svg",
  "revision": "cbee69971f47c5c61e972e54015d2d29"
}, {
  "url": "include/LD/assets/icons/utility/broadcast.svg",
  "revision": "9e622c4ca9723f989a0e26318d28e916"
}, {
  "url": "include/LD/assets/icons/utility/brush.svg",
  "revision": "2dc74c21e7762418026d9a25dac6c815"
}, {
  "url": "include/LD/assets/icons/utility/bucket.svg",
  "revision": "d7f42ef01e49265a9fe056ff286a3a03"
}, {
  "url": "include/LD/assets/icons/utility/bug.svg",
  "revision": "bd0d076c8d20a64a92a339bd1b0b972a"
}, {
  "url": "include/LD/assets/icons/utility/builder.svg",
  "revision": "a67418051c82dec57d406d6566fad379"
}, {
  "url": "include/LD/assets/icons/utility/bundle_config.svg",
  "revision": "a08a8220dde32eefc2a0477f6b00cbc8"
}, {
  "url": "include/LD/assets/icons/utility/bundle_policy.svg",
  "revision": "815f641bb0416b6a9f6356e8e253ace5"
}, {
  "url": "include/LD/assets/icons/utility/button_choice.svg",
  "revision": "c6d3997db4f4a9f5107e8df03bf78522"
}, {
  "url": "include/LD/assets/icons/utility/calculated_insights.svg",
  "revision": "bc02bc8d25824a6bbbff40661eea8673"
}, {
  "url": "include/LD/assets/icons/utility/call.svg",
  "revision": "b5aac00e3fc4ce5a1333ad3094b056d9"
}, {
  "url": "include/LD/assets/icons/utility/campaign.svg",
  "revision": "27cb917695edd8fa215a2d843197b2a0"
}, {
  "url": "include/LD/assets/icons/utility/cancel_file_request.svg",
  "revision": "be7f6f5d0b4acf3b35a88d28d5963fde"
}, {
  "url": "include/LD/assets/icons/utility/cancel_transfer.svg",
  "revision": "51f8bacbe5fe5ced7b25164df65c723d"
}, {
  "url": "include/LD/assets/icons/utility/capacity_plan.svg",
  "revision": "a94791ded92e8a01e1d34780222930b7"
}, {
  "url": "include/LD/assets/icons/utility/capslock.svg",
  "revision": "11399b837f69b603c499a31aec69ddf3"
}, {
  "url": "include/LD/assets/icons/utility/cart.svg",
  "revision": "fa3706e792944078b9de002cfbb42b9f"
}, {
  "url": "include/LD/assets/icons/utility/case.svg",
  "revision": "0959ced2eeb0b7b25a188ca70dd57f0e"
}, {
  "url": "include/LD/assets/icons/utility/cases.svg",
  "revision": "f97483719bbcdf9c72118870424575c6"
}, {
  "url": "include/LD/assets/icons/utility/center_align_text.svg",
  "revision": "b4a620ee287cfa6a882745340c40ebb6"
}, {
  "url": "include/LD/assets/icons/utility/center_align.svg",
  "revision": "27d023b52e97c57ef034a16031d5a8c8"
}, {
  "url": "include/LD/assets/icons/utility/change_owner.svg",
  "revision": "b225f761b4c6960255725ce00fb81b30"
}, {
  "url": "include/LD/assets/icons/utility/change_record_type.svg",
  "revision": "aa6f8ccf185d344c04968d7e8274bc80"
}, {
  "url": "include/LD/assets/icons/utility/change_request.svg",
  "revision": "e57d8a90567ea3efec7127eb6dffcc7d"
}, {
  "url": "include/LD/assets/icons/utility/chart.svg",
  "revision": "6bd60be02fc373de92b9a8cdee9e121f"
}, {
  "url": "include/LD/assets/icons/utility/chat.svg",
  "revision": "466b2629c00cc5b4d8c211b9e6489965"
}, {
  "url": "include/LD/assets/icons/utility/check.svg",
  "revision": "bdd0b91fbd9e82c94525965e0d7f73f3"
}, {
  "url": "include/LD/assets/icons/utility/checkin.svg",
  "revision": "5fadd1b89e9f8e85e72a52c76ca302e0"
}, {
  "url": "include/LD/assets/icons/utility/checkout.svg",
  "revision": "ff64ac10b29c5a909f355870606e50b2"
}, {
  "url": "include/LD/assets/icons/utility/chevrondown.svg",
  "revision": "0171ece983f555de162563e8eb416a7b"
}, {
  "url": "include/LD/assets/icons/utility/chevronleft.svg",
  "revision": "ec0890f8f212f4bfb8b0722ddfc86ed9"
}, {
  "url": "include/LD/assets/icons/utility/chevronright.svg",
  "revision": "a053f790d821cd28ffd287cd1454c20f"
}, {
  "url": "include/LD/assets/icons/utility/chevronup.svg",
  "revision": "c1605410c72f79c354b5288a334a9fa9"
}, {
  "url": "include/LD/assets/icons/utility/choice.svg",
  "revision": "4203d4916bd9e84731adf82013cc66f0"
}, {
  "url": "include/LD/assets/icons/utility/classic_interface.svg",
  "revision": "c119f2d3535e4f0753d80959736f3721"
}, {
  "url": "include/LD/assets/icons/utility/clear.svg",
  "revision": "711cbda8eaa251b962cfaa3b78b2b016"
}, {
  "url": "include/LD/assets/icons/utility/clock.svg",
  "revision": "b46b064b62dcaeb9b2c22e9982bd3721"
}, {
  "url": "include/LD/assets/icons/utility/close.svg",
  "revision": "45edcd9775227d41c4f18d1a01cad0a8"
}, {
  "url": "include/LD/assets/icons/utility/collapse_all.svg",
  "revision": "0e96aa71f8b1365d9f55b9ae973f3adf"
}, {
  "url": "include/LD/assets/icons/utility/collection_alt.svg",
  "revision": "5a58be11e19e9acbc135d67f5cef9a01"
}, {
  "url": "include/LD/assets/icons/utility/collection_variable.svg",
  "revision": "311716c8a1188211e6ca47ce8600eb29"
}, {
  "url": "include/LD/assets/icons/utility/collection.svg",
  "revision": "86aede064dd5646eae8a0d5eed2f1661"
}, {
  "url": "include/LD/assets/icons/utility/color_swatch.svg",
  "revision": "2b08f7d6a99dc9bec12acbce9075f500"
}, {
  "url": "include/LD/assets/icons/utility/comments.svg",
  "revision": "1347b5f723d569e022d533febae60349"
}, {
  "url": "include/LD/assets/icons/utility/company.svg",
  "revision": "4246e86def71c964669d09120dab0640"
}, {
  "url": "include/LD/assets/icons/utility/component_customization.svg",
  "revision": "54794477fe66c50d25ff468dcd59ec4e"
}, {
  "url": "include/LD/assets/icons/utility/connected_apps.svg",
  "revision": "f544c2fc9b9d7606d7734b03ecd49ced"
}, {
  "url": "include/LD/assets/icons/utility/constant.svg",
  "revision": "eb311a1dc5007b7bf1793d86e3629b58"
}, {
  "url": "include/LD/assets/icons/utility/contact_request.svg",
  "revision": "a36ee1829b82f300217d8ed85a3534bf"
}, {
  "url": "include/LD/assets/icons/utility/contract_alt.svg",
  "revision": "5e3325fae9794c4ec97c132e51e911dd"
}, {
  "url": "include/LD/assets/icons/utility/contract_doc.svg",
  "revision": "0daf2212973ae8253409c2ded52e8325"
}, {
  "url": "include/LD/assets/icons/utility/contract_payment.svg",
  "revision": "ad6228e78df82b22b20f85d57d0a047b"
}, {
  "url": "include/LD/assets/icons/utility/contract.svg",
  "revision": "00b821ad3b2e1c23525fb93213cb41e4"
}, {
  "url": "include/LD/assets/icons/utility/copy_to_clipboard.svg",
  "revision": "f54a9b4fe0d9cef57698421bc2478a1f"
}, {
  "url": "include/LD/assets/icons/utility/copy.svg",
  "revision": "823ba0697dacc94ac97c8cb6ab5c9bdf"
}, {
  "url": "include/LD/assets/icons/utility/coupon_codes.svg",
  "revision": "adfac7de19e958b5a1b9fef1f85d54dc"
}, {
  "url": "include/LD/assets/icons/utility/crossfilter.svg",
  "revision": "c2eac8c15801e3771053c93266f296d0"
}, {
  "url": "include/LD/assets/icons/utility/currency_input.svg",
  "revision": "0bdb67ee245856389f335e4193a4fe71"
}, {
  "url": "include/LD/assets/icons/utility/currency.svg",
  "revision": "65b607a55c829a29a37d8d6d9a7e6e42"
}, {
  "url": "include/LD/assets/icons/utility/custom_apps.svg",
  "revision": "8330a6ce8e0929bf90b04b97233b1162"
}, {
  "url": "include/LD/assets/icons/utility/cut.svg",
  "revision": "c2485ad0553c5d654e2b52031c64ea81"
}, {
  "url": "include/LD/assets/icons/utility/dash.svg",
  "revision": "d85b3ac700bd8a28df81c86cfa7c9c4b"
}, {
  "url": "include/LD/assets/icons/utility/data_mapping.svg",
  "revision": "ef33f89829aeea012a98c81a8e5edae9"
}, {
  "url": "include/LD/assets/icons/utility/database.svg",
  "revision": "1ee0f35fc8ab3257770d92d81158fa8d"
}, {
  "url": "include/LD/assets/icons/utility/datadotcom.svg",
  "revision": "7c4a4df6f5e05b5d96d7ce0fc7d2da72"
}, {
  "url": "include/LD/assets/icons/utility/date_input.svg",
  "revision": "1b229d0d37134f2d452796deff0481c8"
}, {
  "url": "include/LD/assets/icons/utility/date_time.svg",
  "revision": "fca7c13650b0097e771759bdbc4ed142"
}, {
  "url": "include/LD/assets/icons/utility/dayview.svg",
  "revision": "1c74eb18e5db7b56b19c1385e08906d6"
}, {
  "url": "include/LD/assets/icons/utility/delete.svg",
  "revision": "21388fa0a15cd6f5af5521e833ca40de"
}, {
  "url": "include/LD/assets/icons/utility/deprecate.svg",
  "revision": "07757ac6868fb7dcb487525940720000"
}, {
  "url": "include/LD/assets/icons/utility/description.svg",
  "revision": "2bb85137e80287c69df30c8f5a253be2"
}, {
  "url": "include/LD/assets/icons/utility/desktop_and_phone.svg",
  "revision": "2703769e5594cbd98ab5afa2a73941d0"
}, {
  "url": "include/LD/assets/icons/utility/desktop_console.svg",
  "revision": "bb0c5f2861682065b324fa54000d86f6"
}, {
  "url": "include/LD/assets/icons/utility/desktop.svg",
  "revision": "e2dbe8dfb199b51b4437bf7dcd2e5846"
}, {
  "url": "include/LD/assets/icons/utility/dialing.svg",
  "revision": "077d0e81a640275b7f2397ac229443e2"
}, {
  "url": "include/LD/assets/icons/utility/diamond.svg",
  "revision": "830670b02d112a822a216f5e7d4c21f6"
}, {
  "url": "include/LD/assets/icons/utility/discounts.svg",
  "revision": "fd899141aa00c774c5b113d3fd7b60ff"
}, {
  "url": "include/LD/assets/icons/utility/dislike.svg",
  "revision": "4ce085012791ca9c7781c8aceeebb5eb"
}, {
  "url": "include/LD/assets/icons/utility/display_rich_text.svg",
  "revision": "9d1366969c91a4ed6d5a84e32cd6a759"
}, {
  "url": "include/LD/assets/icons/utility/display_text.svg",
  "revision": "45fa0735c5e29f1a2a935913bd0accf7"
}, {
  "url": "include/LD/assets/icons/utility/dock_panel.svg",
  "revision": "7cc7d483fe95d6030950cdbac3660e6d"
}, {
  "url": "include/LD/assets/icons/utility/down.svg",
  "revision": "b2d0178bd1714288fe6c0910fb39ebdb"
}, {
  "url": "include/LD/assets/icons/utility/download.svg",
  "revision": "5b71d1944d7c826654b5d61fc38df184"
}, {
  "url": "include/LD/assets/icons/utility/drag_and_drop.svg",
  "revision": "9c4b96c2abdd57b44655ba94d71d7568"
}, {
  "url": "include/LD/assets/icons/utility/drag.svg",
  "revision": "fbecf74da22af9ea436b20a5f68edde8"
}, {
  "url": "include/LD/assets/icons/utility/duration_downscale.svg",
  "revision": "99976df041148311d66c94a31e4dbd5f"
}, {
  "url": "include/LD/assets/icons/utility/dynamic_record_choice.svg",
  "revision": "6d59cdca9556ee9b5dee8d37b01b90bc"
}, {
  "url": "include/LD/assets/icons/utility/edit_form.svg",
  "revision": "2f60de6b998abfac88fd5ce8bfa56995"
}, {
  "url": "include/LD/assets/icons/utility/edit.svg",
  "revision": "f6be9462d3e4fa166c8e4fd4cd4368a8"
}, {
  "url": "include/LD/assets/icons/utility/education.svg",
  "revision": "af91d3451b5a0de83448a789e34c3957"
}, {
  "url": "include/LD/assets/icons/utility/einstein.svg",
  "revision": "9b54d6c0e38fbd8861ede5bd831504d7"
}, {
  "url": "include/LD/assets/icons/utility/email_open.svg",
  "revision": "0ea0f42044a499cd86671a8ca4af8a93"
}, {
  "url": "include/LD/assets/icons/utility/email.svg",
  "revision": "d41f11acc8bef84d6f41bacebeb1f5d3"
}, {
  "url": "include/LD/assets/icons/utility/emoji.svg",
  "revision": "510353f3f9def8c59eafb492aa6b38b3"
}, {
  "url": "include/LD/assets/icons/utility/end_call.svg",
  "revision": "a51241432bd6f8eaa60a476c7d7431b7"
}, {
  "url": "include/LD/assets/icons/utility/end_chat.svg",
  "revision": "fb71ce345bcc01a7dc0042ab34ae749c"
}, {
  "url": "include/LD/assets/icons/utility/end_messaging_session.svg",
  "revision": "7aecda24bec1426893310574c3d8a6f3"
}, {
  "url": "include/LD/assets/icons/utility/engage.svg",
  "revision": "5b6d0d6c532b1ad928c0aa8ad6a0f108"
}, {
  "url": "include/LD/assets/icons/utility/enter.svg",
  "revision": "6b79ce5ede0feb7447c39c469da1498d"
}, {
  "url": "include/LD/assets/icons/utility/entitlement.svg",
  "revision": "7b27d76e23c43961fc8f52362a4cdc94"
}, {
  "url": "include/LD/assets/icons/utility/erect_window.svg",
  "revision": "f74694d71153260b74c0ca587263c796"
}, {
  "url": "include/LD/assets/icons/utility/error.svg",
  "revision": "f70c017b18c9499cd38afc131fb321dc"
}, {
  "url": "include/LD/assets/icons/utility/event_ext.svg",
  "revision": "98e71935a184e71e3184087db28b3732"
}, {
  "url": "include/LD/assets/icons/utility/event.svg",
  "revision": "acb3b137402a0be5e80952ef0a213bf4"
}, {
  "url": "include/LD/assets/icons/utility/events.svg",
  "revision": "e7f32927c9375d740fe4565c0de33387"
}, {
  "url": "include/LD/assets/icons/utility/expand_all.svg",
  "revision": "d967f6cc78abcaed0859ec4b508ecdfd"
}, {
  "url": "include/LD/assets/icons/utility/expand_alt.svg",
  "revision": "f6df88b9bbaf4e6485ce63a3d3e43f7a"
}, {
  "url": "include/LD/assets/icons/utility/expand.svg",
  "revision": "9cb6a41e24e983ad235fe2311afcac59"
}, {
  "url": "include/LD/assets/icons/utility/fallback.svg",
  "revision": "0e6f47d91e059602819e1d80449eb638"
}, {
  "url": "include/LD/assets/icons/utility/favorite.svg",
  "revision": "1b4d449dee5741e6d082510503bd335f"
}, {
  "url": "include/LD/assets/icons/utility/feed.svg",
  "revision": "07c289b6eefff7dfd46f2674901e4b75"
}, {
  "url": "include/LD/assets/icons/utility/field_sales.svg",
  "revision": "bb6d727c715858221344129288890ac0"
}, {
  "url": "include/LD/assets/icons/utility/file.svg",
  "revision": "1b1f8ed95e9a6d3d6b5d079cb845111c"
}, {
  "url": "include/LD/assets/icons/utility/filter_criteria_rule.svg",
  "revision": "624060964b1b632567be0dda062557cc"
}, {
  "url": "include/LD/assets/icons/utility/filter_criteria.svg",
  "revision": "fb1ebaba11a80ac979e9b387f8753f66"
}, {
  "url": "include/LD/assets/icons/utility/filter.svg",
  "revision": "817034e619a35c90994fcc973ce0f399"
}, {
  "url": "include/LD/assets/icons/utility/filterList.svg",
  "revision": "6c057a67e29c233195b8ce1d514e0042"
}, {
  "url": "include/LD/assets/icons/utility/flow_alt.svg",
  "revision": "2051be9387be7db4c50c53d69d27d1e9"
}, {
  "url": "include/LD/assets/icons/utility/flow.svg",
  "revision": "bae5c39fff56c08d1b81303b65d0d22f"
}, {
  "url": "include/LD/assets/icons/utility/food_and_drink.svg",
  "revision": "efc06490cf9f88293e31c152d1800347"
}, {
  "url": "include/LD/assets/icons/utility/form.svg",
  "revision": "b4cb45f2a0de0d7f69cf069ee4399fbd"
}, {
  "url": "include/LD/assets/icons/utility/formula.svg",
  "revision": "a0cb807e2600cc5bd67cf7d8dc66b62e"
}, {
  "url": "include/LD/assets/icons/utility/forward_up.svg",
  "revision": "5b8b451a4097cad9dadfa5a398bc75b8"
}, {
  "url": "include/LD/assets/icons/utility/forward.svg",
  "revision": "447786f79d70c0c2710969a78b7abd25"
}, {
  "url": "include/LD/assets/icons/utility/freeze_column.svg",
  "revision": "7d3be73fbd4672efd0701b4b71e12a59"
}, {
  "url": "include/LD/assets/icons/utility/frozen.svg",
  "revision": "1a60bcb44a6576ed32691c2ed573f12e"
}, {
  "url": "include/LD/assets/icons/utility/fulfillment_order.svg",
  "revision": "e98271d8b9b3161821d35c10d79b8c75"
}, {
  "url": "include/LD/assets/icons/utility/full_width_view.svg",
  "revision": "7417c42f0d4415b0d0eb206bf6adb046"
}, {
  "url": "include/LD/assets/icons/utility/global_constant.svg",
  "revision": "83cfe7b037703bd868edbf4c21d147c9"
}, {
  "url": "include/LD/assets/icons/utility/graph.svg",
  "revision": "93b67b8bc38d855b39f19eb19656ddd4"
}, {
  "url": "include/LD/assets/icons/utility/groups.svg",
  "revision": "3bf90f1eb595512750f1c4910bdddeff"
}, {
  "url": "include/LD/assets/icons/utility/help_center.svg",
  "revision": "05e93c084e10e01345d8b846132d4fb6"
}, {
  "url": "include/LD/assets/icons/utility/help_doc_ext.svg",
  "revision": "db1e7e845ba30dbfa952e83d9edc3352"
}, {
  "url": "include/LD/assets/icons/utility/help.svg",
  "revision": "9eecf5ec6e4ceb2e467118b7474f7375"
}, {
  "url": "include/LD/assets/icons/utility/hide_mobile.svg",
  "revision": "d0b2662a10dce2c62db429120cae3152"
}, {
  "url": "include/LD/assets/icons/utility/hide.svg",
  "revision": "af5fbaa770cdf01d855784696c53ec6a"
}, {
  "url": "include/LD/assets/icons/utility/hierarchy.svg",
  "revision": "129eabd7b3c8b65a34481b2faefdf21e"
}, {
  "url": "include/LD/assets/icons/utility/high_velocity_sales.svg",
  "revision": "988434ba087f3a3368dd095669cf2f25"
}, {
  "url": "include/LD/assets/icons/utility/holiday_operating_hours.svg",
  "revision": "623f17e9d7d2792bf03a76c7603bfbd0"
}, {
  "url": "include/LD/assets/icons/utility/home.svg",
  "revision": "e7bab9c7fed51f859a0a0ccaa679602a"
}, {
  "url": "include/LD/assets/icons/utility/identity.svg",
  "revision": "95b8d3200d54b5976b09fb4d887ec6b5"
}, {
  "url": "include/LD/assets/icons/utility/image.svg",
  "revision": "cdb8d1b6b9d1f9f25dc31b36e5584124"
}, {
  "url": "include/LD/assets/icons/utility/in_app_assistant.svg",
  "revision": "93875543525a29acb706eff8c78f81ef"
}, {
  "url": "include/LD/assets/icons/utility/inbox.svg",
  "revision": "803af12e9e8f10710d6ce47ac6adff12"
}, {
  "url": "include/LD/assets/icons/utility/incident.svg",
  "revision": "37fd2386c09014c4a94e6dcbef2f67b4"
}, {
  "url": "include/LD/assets/icons/utility/incoming_call.svg",
  "revision": "aeb855053b7a98212c23cf95e65f4648"
}, {
  "url": "include/LD/assets/icons/utility/info_alt.svg",
  "revision": "5c3cd1e9e7fd7280ccceb783a7dfdeef"
}, {
  "url": "include/LD/assets/icons/utility/info.svg",
  "revision": "904bd0ab4ea48fb552d7bb65d6e30631"
}, {
  "url": "include/LD/assets/icons/utility/insert_tag_field.svg",
  "revision": "9ffa665673b6c3a121f3323c048ae0e7"
}, {
  "url": "include/LD/assets/icons/utility/insert_template.svg",
  "revision": "6760d4b7c43c56e8f297ba5cfe79d9b5"
}, {
  "url": "include/LD/assets/icons/utility/inspector_panel.svg",
  "revision": "1b49628f858da403d4d6d13f411e96a9"
}, {
  "url": "include/LD/assets/icons/utility/internal_share.svg",
  "revision": "76c8743dce887704a8506db86a323c81"
}, {
  "url": "include/LD/assets/icons/utility/italic.svg",
  "revision": "a8342ed8fed6222a370de4e00ea834c4"
}, {
  "url": "include/LD/assets/icons/utility/jump_to_bottom.svg",
  "revision": "c411db2d40087b781e52a14ace84b985"
}, {
  "url": "include/LD/assets/icons/utility/jump_to_left.svg",
  "revision": "91e7c7d7367d04b2959b7a92362114d7"
}, {
  "url": "include/LD/assets/icons/utility/jump_to_right.svg",
  "revision": "828755163bdee293048d5ca431732b61"
}, {
  "url": "include/LD/assets/icons/utility/jump_to_top.svg",
  "revision": "b79827e7c1df71799af5b429939d3ecc"
}, {
  "url": "include/LD/assets/icons/utility/justify_text.svg",
  "revision": "75fbeadd96ac513c8753c7ca6b05fad7"
}, {
  "url": "include/LD/assets/icons/utility/kanban.svg",
  "revision": "dc7ceff84734d960d38b70e5eb7cf59b"
}, {
  "url": "include/LD/assets/icons/utility/key_dates.svg",
  "revision": "64afc67c4b12b9d1d8ce5b2ddcb0c5b1"
}, {
  "url": "include/LD/assets/icons/utility/key.svg",
  "revision": "595a4a95e7f077be4795fd57977f3b7a"
}, {
  "url": "include/LD/assets/icons/utility/keyboard_dismiss.svg",
  "revision": "319a2de25c2a12a44e0a856f4c8e386f"
}, {
  "url": "include/LD/assets/icons/utility/keypad.svg",
  "revision": "95258df6048d210527a58cb9d59de03b"
}, {
  "url": "include/LD/assets/icons/utility/knowledge_base.svg",
  "revision": "99b118f4f6e913b6f6883334c0f22e88"
}, {
  "url": "include/LD/assets/icons/utility/layers.svg",
  "revision": "ae428fc67e68431a50fed267c6011fed"
}, {
  "url": "include/LD/assets/icons/utility/layout_banner.svg",
  "revision": "0496f810dcf4b8e17155075096a78459"
}, {
  "url": "include/LD/assets/icons/utility/layout_card.svg",
  "revision": "fbd0d0bf305f9cb59ddef2706ab6b98e"
}, {
  "url": "include/LD/assets/icons/utility/layout_overlap.svg",
  "revision": "8594e105079c5f004fa1821a5c8f7856"
}, {
  "url": "include/LD/assets/icons/utility/layout_tile.svg",
  "revision": "5e0fbf62609a869af22b5d6f240d6999"
}, {
  "url": "include/LD/assets/icons/utility/layout.svg",
  "revision": "a52963ce4fc82b230c329af871bd495a"
}, {
  "url": "include/LD/assets/icons/utility/leave_conference.svg",
  "revision": "dcbc615cc5e075f1c3d32752189a60b0"
}, {
  "url": "include/LD/assets/icons/utility/left_align_text.svg",
  "revision": "8927cc2aaef9e5a62864d52c5262ce1c"
}, {
  "url": "include/LD/assets/icons/utility/left_align.svg",
  "revision": "a5743473d97dfdb1b5268e25097f1fc6"
}, {
  "url": "include/LD/assets/icons/utility/left.svg",
  "revision": "ba67542fdcc8a6282bd3e4bd4be003d8"
}, {
  "url": "include/LD/assets/icons/utility/level_down.svg",
  "revision": "01b4dc258ce2fa091c40385371d1cf39"
}, {
  "url": "include/LD/assets/icons/utility/level_up.svg",
  "revision": "cb7f5b43f36aa76f8a97f48474ab1463"
}, {
  "url": "include/LD/assets/icons/utility/light_bulb.svg",
  "revision": "479593e0cd95cf005c2461e52de7c107"
}, {
  "url": "include/LD/assets/icons/utility/lightning_extension.svg",
  "revision": "14a862adaa3b1fd94af80f421847f48a"
}, {
  "url": "include/LD/assets/icons/utility/lightning_inspector.svg",
  "revision": "7dcdadc7f3bf0e10f86c5cb1d1c5be16"
}, {
  "url": "include/LD/assets/icons/utility/like.svg",
  "revision": "d25eb276cb86eb4f100286339ce5b653"
}, {
  "url": "include/LD/assets/icons/utility/link.svg",
  "revision": "d84b6c992fe8abf8a5a139a52e981d31"
}, {
  "url": "include/LD/assets/icons/utility/linked.svg",
  "revision": "dad63dfd60289198895637ec6d9cce66"
}, {
  "url": "include/LD/assets/icons/utility/list.svg",
  "revision": "8e9bac2949e82b12a5c97a171a64a85e"
}, {
  "url": "include/LD/assets/icons/utility/listen.svg",
  "revision": "6e21105d9a781d8cb069dfce9ee5b8a9"
}, {
  "url": "include/LD/assets/icons/utility/live_message.svg",
  "revision": "370a9bbb2ce61d9faa7ecffb6c27b790"
}, {
  "url": "include/LD/assets/icons/utility/location_permit.svg",
  "revision": "ae6577f9feda159b447102f6b83d2b88"
}, {
  "url": "include/LD/assets/icons/utility/location.svg",
  "revision": "7b02732dd62a4eee5cae431646723bd5"
}, {
  "url": "include/LD/assets/icons/utility/lock.svg",
  "revision": "3dcbde34db823c7818cce305bff205ee"
}, {
  "url": "include/LD/assets/icons/utility/locker_service_api_viewer.svg",
  "revision": "70f44e0b9fb06f38cd86eb9bf8b49866"
}, {
  "url": "include/LD/assets/icons/utility/locker_service_console.svg",
  "revision": "daba37e881876e82d76d10e3e29ff5a6"
}, {
  "url": "include/LD/assets/icons/utility/log_a_call.svg",
  "revision": "423cef68350f48d57ea9141f6a095e69"
}, {
  "url": "include/LD/assets/icons/utility/logout.svg",
  "revision": "e11adc9d5fd9be9d6a67940084b20c9a"
}, {
  "url": "include/LD/assets/icons/utility/loop.svg",
  "revision": "7ca35a1558c7b744062b108932b52750"
}, {
  "url": "include/LD/assets/icons/utility/lower_flag.svg",
  "revision": "dcc63ffb90686ab41acc51a93579ccaa"
}, {
  "url": "include/LD/assets/icons/utility/macros.svg",
  "revision": "8936f7df57ddd65a62a1d99441d64a70"
}, {
  "url": "include/LD/assets/icons/utility/magicwand.svg",
  "revision": "7023055f24e9792045e83a4e791ea5fc"
}, {
  "url": "include/LD/assets/icons/utility/mark_all_as_read.svg",
  "revision": "648aacb2021ad38289f8f0db823fcf08"
}, {
  "url": "include/LD/assets/icons/utility/matrix.svg",
  "revision": "c923eb4dcc5546ca2bb7f55d20509b49"
}, {
  "url": "include/LD/assets/icons/utility/meet_content_source.svg",
  "revision": "a33227d91d310a619f82435c05182a04"
}, {
  "url": "include/LD/assets/icons/utility/meet_focus_content.svg",
  "revision": "19946a1e6cdf7791bcb91a5ba789d176"
}, {
  "url": "include/LD/assets/icons/utility/meet_focus_equal.svg",
  "revision": "d26e0a14c5b572c2868e701dbfbac062"
}, {
  "url": "include/LD/assets/icons/utility/meet_focus_presenter.svg",
  "revision": "b3646d583029ac06d46af27809a6bf8a"
}, {
  "url": "include/LD/assets/icons/utility/meet_present_panel.svg",
  "revision": "1f247f3c730c00d1d3b177d79a507562"
}, {
  "url": "include/LD/assets/icons/utility/merge_field.svg",
  "revision": "da9dae95400ccee9ec2310bcc725aaf2"
}, {
  "url": "include/LD/assets/icons/utility/merge.svg",
  "revision": "b6ceff9336763443aeabba35a9899843"
}, {
  "url": "include/LD/assets/icons/utility/metrics.svg",
  "revision": "2a9a21027e7e7550bdea357b9d825c7b"
}, {
  "url": "include/LD/assets/icons/utility/middle_align.svg",
  "revision": "7e5141a1d7c81d23cad865922ff3fd02"
}, {
  "url": "include/LD/assets/icons/utility/minimize_window.svg",
  "revision": "435050a733dee2214afb868f9edd3b4c"
}, {
  "url": "include/LD/assets/icons/utility/missed_call.svg",
  "revision": "688b87803688d74aa9a07795efd5933e"
}, {
  "url": "include/LD/assets/icons/utility/money.svg",
  "revision": "5c24fbe20a388e415487587472c32f27"
}, {
  "url": "include/LD/assets/icons/utility/moneybag.svg",
  "revision": "661f69fd5e4bdd282c28d577f7593398"
}, {
  "url": "include/LD/assets/icons/utility/monthlyview.svg",
  "revision": "6a009a7d0bb902bf7d5657f6fed9bb1f"
}, {
  "url": "include/LD/assets/icons/utility/move.svg",
  "revision": "ed2d81c2bf8a4950381d03acb9bbef8c"
}, {
  "url": "include/LD/assets/icons/utility/multi_picklist.svg",
  "revision": "ee8a774ee965b5b4d3075da1ca944ba1"
}, {
  "url": "include/LD/assets/icons/utility/multi_select_checkbox.svg",
  "revision": "b20b08883b1d6b3dbf830d92318355eb"
}, {
  "url": "include/LD/assets/icons/utility/muted.svg",
  "revision": "49950942391de9946bd68f220288ed8b"
}, {
  "url": "include/LD/assets/icons/utility/new_direct_message.svg",
  "revision": "e40b66d5773688059e547d2b1ae6372c"
}, {
  "url": "include/LD/assets/icons/utility/new_window.svg",
  "revision": "168e8cac053fce456bf5c43f49353e75"
}, {
  "url": "include/LD/assets/icons/utility/new.svg",
  "revision": "be4eb0482020f1e9f987d3167a59de94"
}, {
  "url": "include/LD/assets/icons/utility/news.svg",
  "revision": "515db98c44d1a235e6b81214dc424d68"
}, {
  "url": "include/LD/assets/icons/utility/note.svg",
  "revision": "0a8f58a34afc61612147c3f4ddfd9f06"
}, {
  "url": "include/LD/assets/icons/utility/notebook.svg",
  "revision": "8e4a394fc50aa5a7207fb2a541236010"
}, {
  "url": "include/LD/assets/icons/utility/notification.svg",
  "revision": "d0168d3962fbcf00c90ecdbd91ea9daa"
}, {
  "url": "include/LD/assets/icons/utility/number_input.svg",
  "revision": "229f6c4550f51c283cfc430838d33f4c"
}, {
  "url": "include/LD/assets/icons/utility/office365.svg",
  "revision": "a4d6c0a5fff099b6aefcca8fa3af0911"
}, {
  "url": "include/LD/assets/icons/utility/offline_briefcase.svg",
  "revision": "a69fa000f0c3f3032b77bf4d202684a7"
}, {
  "url": "include/LD/assets/icons/utility/offline_cached.svg",
  "revision": "3c8c01a9aa7b621baee154736da3596c"
}, {
  "url": "include/LD/assets/icons/utility/offline.svg",
  "revision": "90c1641ee12b8390fe6906dc6f9e659d"
}, {
  "url": "include/LD/assets/icons/utility/omni_channel.svg",
  "revision": "a8c1838261f45bfbf9a96ad2e82630f3"
}, {
  "url": "include/LD/assets/icons/utility/open_folder.svg",
  "revision": "8f779bf1436dfc628fa120c8636471c7"
}, {
  "url": "include/LD/assets/icons/utility/open.svg",
  "revision": "df505e7586b31be16d9f2bae0e353c7f"
}, {
  "url": "include/LD/assets/icons/utility/opened_folder.svg",
  "revision": "8286e4747c852cc3924bdc6ec360d6b6"
}, {
  "url": "include/LD/assets/icons/utility/orchestrator.svg",
  "revision": "7b435254e9d78c91a2153826c2f99e73"
}, {
  "url": "include/LD/assets/icons/utility/org_chart.svg",
  "revision": "59cada0120ab61681fcbdfca5c4465ac"
}, {
  "url": "include/LD/assets/icons/utility/outbound_call.svg",
  "revision": "887502d6680203b84e05bf97ec61481e"
}, {
  "url": "include/LD/assets/icons/utility/outcome.svg",
  "revision": "2ea9140ec15c4bf73dae5fa02069d8da"
}, {
  "url": "include/LD/assets/icons/utility/overflow.svg",
  "revision": "b9a2271a83974966f8716843c9423fee"
}, {
  "url": "include/LD/assets/icons/utility/package_org_beta.svg",
  "revision": "d208d73d0854d6ab220833e3ac2ec97d"
}, {
  "url": "include/LD/assets/icons/utility/package_org.svg",
  "revision": "180753bd158a2d45f1f4c33623ef10af"
}, {
  "url": "include/LD/assets/icons/utility/package.svg",
  "revision": "ada1a58a0d61bf7b40a992e58992de7c"
}, {
  "url": "include/LD/assets/icons/utility/page.svg",
  "revision": "7016c73369f23e297ab8cc818b045e1a"
}, {
  "url": "include/LD/assets/icons/utility/palette.svg",
  "revision": "c946b891089ff949039c155e536ecead"
}, {
  "url": "include/LD/assets/icons/utility/password.svg",
  "revision": "bfa0cb1d210da4c30a6afb3a76c9c4fc"
}, {
  "url": "include/LD/assets/icons/utility/paste.svg",
  "revision": "d02e8f5286f73ed61d074d011f28a1a0"
}, {
  "url": "include/LD/assets/icons/utility/pause_alt.svg",
  "revision": "0a2b88184ba058850b22324427382c97"
}, {
  "url": "include/LD/assets/icons/utility/pause.svg",
  "revision": "453a07d7a2c5fac869c6c0aee719bbbd"
}, {
  "url": "include/LD/assets/icons/utility/payment_gateway.svg",
  "revision": "33eac9fce74ecce125c9c7e496572412"
}, {
  "url": "include/LD/assets/icons/utility/pdf_ext.svg",
  "revision": "05aebfe407b146fa60a7cf617598214c"
}, {
  "url": "include/LD/assets/icons/utility/people.svg",
  "revision": "16804ee5c57b322f859099b6e0309caf"
}, {
  "url": "include/LD/assets/icons/utility/percent.svg",
  "revision": "ed804487fbfc9c33b7f9c78bcfa4f2ba"
}, {
  "url": "include/LD/assets/icons/utility/phone_landscape.svg",
  "revision": "7a2c2fe435658783deca11267b17b8b3"
}, {
  "url": "include/LD/assets/icons/utility/phone_portrait.svg",
  "revision": "19a50cd8b2e73c349aec406899e6e43a"
}, {
  "url": "include/LD/assets/icons/utility/photo.svg",
  "revision": "1efc17bbac9f1b84b8d8531d77d5d781"
}, {
  "url": "include/LD/assets/icons/utility/picklist_choice.svg",
  "revision": "ceb4dd4916264af53a05c5d66623620a"
}, {
  "url": "include/LD/assets/icons/utility/picklist_type.svg",
  "revision": "99e3c56cc2991fa0bf65ff1709a8896f"
}, {
  "url": "include/LD/assets/icons/utility/picklist.svg",
  "revision": "7e95497b89ca19660a4cd0ee65551a6c"
}, {
  "url": "include/LD/assets/icons/utility/pin.svg",
  "revision": "9f71881d360b4d47d255c048a135a552"
}, {
  "url": "include/LD/assets/icons/utility/pinned.svg",
  "revision": "74716f21ec575694b61c7c3683b924e6"
}, {
  "url": "include/LD/assets/icons/utility/planning_poker.svg",
  "revision": "b5bebceb52975cd7e344972d8857e6c5"
}, {
  "url": "include/LD/assets/icons/utility/play.svg",
  "revision": "ac1b9f50a6e2b4b2869879b71f7d06fb"
}, {
  "url": "include/LD/assets/icons/utility/podcast_webinar.svg",
  "revision": "050a468ab59d26eef1b4e9c67e9dd449"
}, {
  "url": "include/LD/assets/icons/utility/pop_in.svg",
  "revision": "4c691e57c8c0dff34fec919e37496936"
}, {
  "url": "include/LD/assets/icons/utility/power.svg",
  "revision": "dd7085e148609f473790e5632dc52c63"
}, {
  "url": "include/LD/assets/icons/utility/preview.svg",
  "revision": "34cfb771450fb3ca527153eea525dde7"
}, {
  "url": "include/LD/assets/icons/utility/price_book_entries.svg",
  "revision": "2eda48c060211845acb318008da0f374"
}, {
  "url": "include/LD/assets/icons/utility/price_books.svg",
  "revision": "d989d6815b19eecf7d44d01ff6767b36"
}, {
  "url": "include/LD/assets/icons/utility/pricing_workspace.svg",
  "revision": "47271c9f92b10f1a80431a57b48b2d12"
}, {
  "url": "include/LD/assets/icons/utility/print.svg",
  "revision": "64f6f11f13236472b4f77be187f00797"
}, {
  "url": "include/LD/assets/icons/utility/priority.svg",
  "revision": "e47665ba57ad6706a25fe47a4ee3d664"
}, {
  "url": "include/LD/assets/icons/utility/privately_shared.svg",
  "revision": "27c66e85438eea9068a37b7ca2cde0d6"
}, {
  "url": "include/LD/assets/icons/utility/problem.svg",
  "revision": "05fcdb344e7631cf719f257d7cf280ac"
}, {
  "url": "include/LD/assets/icons/utility/process.svg",
  "revision": "2038d509d7c852759f27dc07058c97f9"
}, {
  "url": "include/LD/assets/icons/utility/product_consumed_state.svg",
  "revision": "cfe89ed8fd94a80d94db5c75545f278c"
}, {
  "url": "include/LD/assets/icons/utility/product_quantity_rules.svg",
  "revision": "7710a19714fa597624d3dcbd7d1e31d5"
}, {
  "url": "include/LD/assets/icons/utility/product_service_campaign_item.svg",
  "revision": "9b2b89f02039e5509bffbd2cdb23e837"
}, {
  "url": "include/LD/assets/icons/utility/product_service_campaign.svg",
  "revision": "20a543196c6794aacfd0e4d2950bc939"
}, {
  "url": "include/LD/assets/icons/utility/product_transfer_state.svg",
  "revision": "13806955ca81fa07420d8ed565a0202b"
}, {
  "url": "include/LD/assets/icons/utility/product_transfer.svg",
  "revision": "f99cb3b01a34343a51d0663234c51e54"
}, {
  "url": "include/LD/assets/icons/utility/product_warranty_term.svg",
  "revision": "837c76339a7529e0ecfe1d76fa391607"
}, {
  "url": "include/LD/assets/icons/utility/product_workspace.svg",
  "revision": "cfade77d719aa9e54beb267f9593ea4d"
}, {
  "url": "include/LD/assets/icons/utility/products.svg",
  "revision": "b62ea483dab5c3065e1c296520600fa1"
}, {
  "url": "include/LD/assets/icons/utility/profile.svg",
  "revision": "8ad0c14e5239de109a06c532e7b66cf6"
}, {
  "url": "include/LD/assets/icons/utility/promotion_segments.svg",
  "revision": "2d6bbdb99b6cbf22964306f3c1273490"
}, {
  "url": "include/LD/assets/icons/utility/promotions_workspace.svg",
  "revision": "d56e3e1eae851211648e8dd1df3d81a3"
}, {
  "url": "include/LD/assets/icons/utility/promotions.svg",
  "revision": "c66ca2d6b7478697e439fc35267ac8ba"
}, {
  "url": "include/LD/assets/icons/utility/prompt_edit.svg",
  "revision": "b4365fa87c87d76c97199b4e6a74f235"
}, {
  "url": "include/LD/assets/icons/utility/prompt.svg",
  "revision": "9e119575ae61893b3dbfbda5def26edc"
}, {
  "url": "include/LD/assets/icons/utility/propagation_policy.svg",
  "revision": "825b239d248ac16dd4204d5ba939e1ae"
}, {
  "url": "include/LD/assets/icons/utility/push.svg",
  "revision": "38ef8aa105c16b6f56cd6f4c454b15f4"
}, {
  "url": "include/LD/assets/icons/utility/puzzle.svg",
  "revision": "0d8a40d419285c3d2407b31f4fd49e0d"
}, {
  "url": "include/LD/assets/icons/utility/qualifications.svg",
  "revision": "67e0be17b84ba0cb1eba71f6077a57b2"
}, {
  "url": "include/LD/assets/icons/utility/question_mark.svg",
  "revision": "c968b946a080dab1a0bd88dbd70fce45"
}, {
  "url": "include/LD/assets/icons/utility/question.svg",
  "revision": "9eecf5ec6e4ceb2e467118b7474f7375"
}, {
  "url": "include/LD/assets/icons/utility/questions_and_answers.svg",
  "revision": "5d9a0eb48680265b6049665708211d26"
}, {
  "url": "include/LD/assets/icons/utility/quick_text.svg",
  "revision": "a045979e496a1e15d57463cc96c07189"
}, {
  "url": "include/LD/assets/icons/utility/quip.svg",
  "revision": "b6a6507081d2007e1dbe96d6f7116667"
}, {
  "url": "include/LD/assets/icons/utility/quotation_marks.svg",
  "revision": "edacb1f84597829619c993c4055f66a5"
}, {
  "url": "include/LD/assets/icons/utility/quote.svg",
  "revision": "6bfb2da58a7dd4259dc9da375ec02f76"
}, {
  "url": "include/LD/assets/icons/utility/radio_button.svg",
  "revision": "61fbccc318b7f8076a3543e8303d3a7d"
}, {
  "url": "include/LD/assets/icons/utility/rating.svg",
  "revision": "105b18cbced520a2b916f3889a66490f"
}, {
  "url": "include/LD/assets/icons/utility/reassign.svg",
  "revision": "c7867d08b24bb383c9878dd810365861"
}, {
  "url": "include/LD/assets/icons/utility/recipe.svg",
  "revision": "958854623f2bed1cb25a9e3274000843"
}, {
  "url": "include/LD/assets/icons/utility/record_create.svg",
  "revision": "8a3b913c52bba21930d8dbda0251d33e"
}, {
  "url": "include/LD/assets/icons/utility/record_delete.svg",
  "revision": "5117370169635e832fa216ebca04817c"
}, {
  "url": "include/LD/assets/icons/utility/record_lookup.svg",
  "revision": "fa0994df3e3b8584586389f09990b622"
}, {
  "url": "include/LD/assets/icons/utility/record_update.svg",
  "revision": "eae3602e19ba827425d735707b40b990"
}, {
  "url": "include/LD/assets/icons/utility/record.svg",
  "revision": "fb624da22b198f83b16fe6e00f4b672b"
}, {
  "url": "include/LD/assets/icons/utility/recurring_exception.svg",
  "revision": "7a8708686f4b57e0088680a745d0d3a8"
}, {
  "url": "include/LD/assets/icons/utility/recycle_bin_empty.svg",
  "revision": "8263a594df9a66ddf9b94f3a52ef8bad"
}, {
  "url": "include/LD/assets/icons/utility/recycle_bin_full.svg",
  "revision": "f3c4870624e5dc773fb3a35b1f9567f3"
}, {
  "url": "include/LD/assets/icons/utility/redo.svg",
  "revision": "02fbffda3f72bcbb93b91e2aa11cd440"
}, {
  "url": "include/LD/assets/icons/utility/refresh.svg",
  "revision": "05e6cf3cddb478efcae1f206b5bd1345"
}, {
  "url": "include/LD/assets/icons/utility/relate.svg",
  "revision": "f2ec76d5f11cf3d6666b5027df4c6b00"
}, {
  "url": "include/LD/assets/icons/utility/reminder.svg",
  "revision": "fc04b367808471dc94f4391713ef95c4"
}, {
  "url": "include/LD/assets/icons/utility/remove_formatting.svg",
  "revision": "68354ffad301a8d1dda6283b493c60d2"
}, {
  "url": "include/LD/assets/icons/utility/remove_link.svg",
  "revision": "4f24eb8bc9a35f32bee3ca9984ebf7a9"
}, {
  "url": "include/LD/assets/icons/utility/replace.svg",
  "revision": "aa6f8ccf185d344c04968d7e8274bc80"
}, {
  "url": "include/LD/assets/icons/utility/reply_all.svg",
  "revision": "679e267e5867601adb50e4794f30a152"
}, {
  "url": "include/LD/assets/icons/utility/reply.svg",
  "revision": "024ada5b6b28a3ed3db29a6cb8f0650f"
}, {
  "url": "include/LD/assets/icons/utility/report_issue.svg",
  "revision": "7cf0bb2a026f71404feca8e9ab1d9bb8"
}, {
  "url": "include/LD/assets/icons/utility/reset_password.svg",
  "revision": "061cf083ae940a895536789e3c676614"
}, {
  "url": "include/LD/assets/icons/utility/resource_absence.svg",
  "revision": "82023202e9704e1b5842cfa1363f9d54"
}, {
  "url": "include/LD/assets/icons/utility/resource_capacity.svg",
  "revision": "1468674bd752802bef07bd12f8390936"
}, {
  "url": "include/LD/assets/icons/utility/resource_territory.svg",
  "revision": "bc804416ae5d5ee31f3aea277efd4f46"
}, {
  "url": "include/LD/assets/icons/utility/restriction_policy.svg",
  "revision": "57c524581067ec95548bea00ac558e8f"
}, {
  "url": "include/LD/assets/icons/utility/retail_execution.svg",
  "revision": "3a58f1a448c44b41c5a539b4dab1ab0d"
}, {
  "url": "include/LD/assets/icons/utility/retweet.svg",
  "revision": "daa15c1494fddda32197c3af048e1283"
}, {
  "url": "include/LD/assets/icons/utility/ribbon.svg",
  "revision": "c984f78f7d78d439b38c6fa429b9b930"
}, {
  "url": "include/LD/assets/icons/utility/richtextbulletedlist.svg",
  "revision": "584f83e3e96654010dbe52847384f042"
}, {
  "url": "include/LD/assets/icons/utility/richtextindent.svg",
  "revision": "6126f92a904c4cc366a5a0d0067dd263"
}, {
  "url": "include/LD/assets/icons/utility/richtextnumberedlist.svg",
  "revision": "cf903876422776eb291afaadeb0ce648"
}, {
  "url": "include/LD/assets/icons/utility/richtextoutdent.svg",
  "revision": "d8a11ffb96b5cf099ff4b3aa1c7f4f2d"
}, {
  "url": "include/LD/assets/icons/utility/right_align_text.svg",
  "revision": "54984b894e07181ff8c3189da5ae7ca3"
}, {
  "url": "include/LD/assets/icons/utility/right_align.svg",
  "revision": "23c97579346a70e767c1c94bdde55b76"
}, {
  "url": "include/LD/assets/icons/utility/right.svg",
  "revision": "da5389322e945e64ff84d400428f455d"
}, {
  "url": "include/LD/assets/icons/utility/rotate.svg",
  "revision": "cd9d98fb661cb76618579a1267c0f4ce"
}, {
  "url": "include/LD/assets/icons/utility/routing_offline.svg",
  "revision": "a9b2f814135c2e56ab96f24232cc0671"
}, {
  "url": "include/LD/assets/icons/utility/rows.svg",
  "revision": "d453258390dd74376bc73e901883c2c9"
}, {
  "url": "include/LD/assets/icons/utility/rules.svg",
  "revision": "8c7b609c9d15fa7472fd699f61436666"
}, {
  "url": "include/LD/assets/icons/utility/salesforce_page.svg",
  "revision": "6b473557302f326fe4b46bfd69820ce6"
}, {
  "url": "include/LD/assets/icons/utility/salesforce1.svg",
  "revision": "f9054de0da023ae424fe42374880be79"
}, {
  "url": "include/LD/assets/icons/utility/save.svg",
  "revision": "079f3e5d66e9fa51d0e9dc9ce74a30b6"
}, {
  "url": "include/LD/assets/icons/utility/scan.svg",
  "revision": "9763f52d33af28d4dc3ac6e844ac6dbd"
}, {
  "url": "include/LD/assets/icons/utility/screen.svg",
  "revision": "6a9bf861b1796155e532b293c579e266"
}, {
  "url": "include/LD/assets/icons/utility/search.svg",
  "revision": "9ed8f59a16c625a398d9326e24e47a1e"
}, {
  "url": "include/LD/assets/icons/utility/section.svg",
  "revision": "af8ff47367143324d30e3bb9c3356bd1"
}, {
  "url": "include/LD/assets/icons/utility/send.svg",
  "revision": "3f31c19f24bc46b8e330b5022698f643"
}, {
  "url": "include/LD/assets/icons/utility/sentiment_negative.svg",
  "revision": "3178d5b5395f4575e30d1f191461bd68"
}, {
  "url": "include/LD/assets/icons/utility/sentiment_neutral.svg",
  "revision": "cff4fd5e94e213d44f6298d74a4bcd53"
}, {
  "url": "include/LD/assets/icons/utility/serialized_product_transaction.svg",
  "revision": "fd0a4773fd3da8c6c2f913f37bee1512"
}, {
  "url": "include/LD/assets/icons/utility/serialized_product.svg",
  "revision": "bb60ec72fefc9f0857920b800a6cd562"
}, {
  "url": "include/LD/assets/icons/utility/service_territory_policy.svg",
  "revision": "070db816ab74c62db6652b63f2e3b679"
}, {
  "url": "include/LD/assets/icons/utility/settings.svg",
  "revision": "5cf91b2f5c0eb4fa8506afa58b10431f"
}, {
  "url": "include/LD/assets/icons/utility/setup_assistant_guide.svg",
  "revision": "b7f1b987d747949679747c5c8bef6a57"
}, {
  "url": "include/LD/assets/icons/utility/setup_modal.svg",
  "revision": "5561032efa53b7e9ec59fe3aaca71322"
}, {
  "url": "include/LD/assets/icons/utility/setup.svg",
  "revision": "6b4770dfb524b1122b2ca3cd5f4a7755"
}, {
  "url": "include/LD/assets/icons/utility/share_file.svg",
  "revision": "b88fd862f8fb5cb02c292f7f1841d511"
}, {
  "url": "include/LD/assets/icons/utility/share_mobile.svg",
  "revision": "2fdd04c238eba4ece1d06c616d1f1e4a"
}, {
  "url": "include/LD/assets/icons/utility/share_post.svg",
  "revision": "2392204dbaf2df35ade29b1a0382049b"
}, {
  "url": "include/LD/assets/icons/utility/share.svg",
  "revision": "29ec5dff8e3bc483e61005b4b0354e72"
}, {
  "url": "include/LD/assets/icons/utility/shield.svg",
  "revision": "345eed08c00668dd04c2ffd37fe11c52"
}, {
  "url": "include/LD/assets/icons/utility/shift_pattern_entry.svg",
  "revision": "96b0462782714c0c433cb29813491281"
}, {
  "url": "include/LD/assets/icons/utility/shift_pattern.svg",
  "revision": "7b575a9b1657dc823d595f4cd925b7d2"
}, {
  "url": "include/LD/assets/icons/utility/shift_scheduling_operation.svg",
  "revision": "b42967e022793d4080dd3fc3fe5a4430"
}, {
  "url": "include/LD/assets/icons/utility/shift_ui.svg",
  "revision": "8ac54cdd5ccfe5b715be681885c50200"
}, {
  "url": "include/LD/assets/icons/utility/shopping_bag.svg",
  "revision": "ad7b8d6308d0649b22a0c771fae4d959"
}, {
  "url": "include/LD/assets/icons/utility/shortcuts.svg",
  "revision": "5239d4b6f237e3720ea62e38d8f18491"
}, {
  "url": "include/LD/assets/icons/utility/side_list.svg",
  "revision": "e74ef8c604427ae7a506993d0186c194"
}, {
  "url": "include/LD/assets/icons/utility/signature.svg",
  "revision": "42bc268ff489ff850485d04ac2a73801"
}, {
  "url": "include/LD/assets/icons/utility/signpost.svg",
  "revision": "64f94112475ab39df3cdb8afd7ae135f"
}, {
  "url": "include/LD/assets/icons/utility/skip_back.svg",
  "revision": "c4281f8fc86682c7f4a7fa3a4d94da4f"
}, {
  "url": "include/LD/assets/icons/utility/skip_forward.svg",
  "revision": "fa8dd2ced16b28f3c42d4fe8cf87191c"
}, {
  "url": "include/LD/assets/icons/utility/skip.svg",
  "revision": "7c645502e82eb70bd95e911f01bd6f63"
}, {
  "url": "include/LD/assets/icons/utility/slider.svg",
  "revision": "450bbd18b966c6c702cb1ae237e64bf9"
}, {
  "url": "include/LD/assets/icons/utility/smiley_and_people.svg",
  "revision": "85e94b22fb533e3c0ec5574d180a6267"
}, {
  "url": "include/LD/assets/icons/utility/sms.svg",
  "revision": "46258e189c808be3673fe18ea43162fb"
}, {
  "url": "include/LD/assets/icons/utility/snippet.svg",
  "revision": "e9025acd3260736efee3c119729e11a7"
}, {
  "url": "include/LD/assets/icons/utility/sobject_collection.svg",
  "revision": "01549463687934727457da5f60ae0680"
}, {
  "url": "include/LD/assets/icons/utility/sobject.svg",
  "revision": "cfb620fe9e2cd1fbe440fccf89954fe7"
}, {
  "url": "include/LD/assets/icons/utility/socialshare.svg",
  "revision": "8f18c55e382419a8a45eeeecfd031bec"
}, {
  "url": "include/LD/assets/icons/utility/sort_policy.svg",
  "revision": "29e5c2c4f46d6fd761f5a06b3a122da2"
}, {
  "url": "include/LD/assets/icons/utility/sort.svg",
  "revision": "b339905758c96c77e204e66e13e71949"
}, {
  "url": "include/LD/assets/icons/utility/spacer.svg",
  "revision": "5cd86218e950705c0800791b747ba9ab"
}, {
  "url": "include/LD/assets/icons/utility/spinner.svg",
  "revision": "6d30032912f3c2a242c8e12d64935452"
}, {
  "url": "include/LD/assets/icons/utility/stage_collection.svg",
  "revision": "94e5a3250757b77ab64da518bd29f523"
}, {
  "url": "include/LD/assets/icons/utility/stage.svg",
  "revision": "e60cbc602b9a1c5a67a6df586da0c50a"
}, {
  "url": "include/LD/assets/icons/utility/standard_objects.svg",
  "revision": "2272b5bc8b5fe93304458ea70b1011d8"
}, {
  "url": "include/LD/assets/icons/utility/steps.svg",
  "revision": "59639e39758bd680bd779416747ff3df"
}, {
  "url": "include/LD/assets/icons/utility/stop.svg",
  "revision": "9a11303e170f451324d58c5548dc8f8a"
}, {
  "url": "include/LD/assets/icons/utility/store.svg",
  "revision": "581cf5c9df02b1e0e91bc5ad8c9055ae"
}, {
  "url": "include/LD/assets/icons/utility/strategy.svg",
  "revision": "4a52596cad072aca6471ec6fe97ebde6"
}, {
  "url": "include/LD/assets/icons/utility/strikethrough.svg",
  "revision": "7327eeafc19ce734304da04cbb8b8729"
}, {
  "url": "include/LD/assets/icons/utility/success.svg",
  "revision": "54ffad72118a8d88fa30826417cbfb3e"
}, {
  "url": "include/LD/assets/icons/utility/summary.svg",
  "revision": "ff81e984a3a331b24ae53c34edbcb851"
}, {
  "url": "include/LD/assets/icons/utility/summarydetail.svg",
  "revision": "8d01ef6bad9deb3d082e9e8e0d0ae5c0"
}, {
  "url": "include/LD/assets/icons/utility/survey.svg",
  "revision": "666c120cc5773431ff918ea1234be250"
}, {
  "url": "include/LD/assets/icons/utility/swarm_request.svg",
  "revision": "00bb57ceb2dbe932c1328d74ffa5f708"
}, {
  "url": "include/LD/assets/icons/utility/swarm_session.svg",
  "revision": "2671f824e2bc3c3e3cff28b9d8ec6f85"
}, {
  "url": "include/LD/assets/icons/utility/switch.svg",
  "revision": "5d0d1888f26a35e464a510cee68cb696"
}, {
  "url": "include/LD/assets/icons/utility/symbols.svg",
  "revision": "15d36b0c6c75da99e84de21a401a187c"
}, {
  "url": "include/LD/assets/icons/utility/sync.svg",
  "revision": "b844885827bf07148966fb353729184c"
}, {
  "url": "include/LD/assets/icons/utility/system_and_global_variable.svg",
  "revision": "822dc50a3f13c0ec401796ba185cb04e"
}, {
  "url": "include/LD/assets/icons/utility/table_settings.svg",
  "revision": "2412820105ad50a31b3cedd9094edb47"
}, {
  "url": "include/LD/assets/icons/utility/table.svg",
  "revision": "baf7e2f0802ef9fa862abb866e3ea3ee"
}, {
  "url": "include/LD/assets/icons/utility/tablet_landscape.svg",
  "revision": "4b22a8a5d3afb5d69cdd0ad1f06915be"
}, {
  "url": "include/LD/assets/icons/utility/tablet_portrait.svg",
  "revision": "ae08107f6e15d579c09521f0cd88c684"
}, {
  "url": "include/LD/assets/icons/utility/tabset.svg",
  "revision": "506a8f14a928bd0aa4733f99a0c7e246"
}, {
  "url": "include/LD/assets/icons/utility/talent_development.svg",
  "revision": "f80eeffcc4aae5e2a93d1875e43ec2f4"
}, {
  "url": "include/LD/assets/icons/utility/target_mode.svg",
  "revision": "4c7fc8df3f47dc3e1952d5b13d1c2f86"
}, {
  "url": "include/LD/assets/icons/utility/target.svg",
  "revision": "1548e228a9ed682e4f2bf56d49fcd029"
}, {
  "url": "include/LD/assets/icons/utility/task.svg",
  "revision": "64a2bfa01f497d1986f9822ee869ab0f"
}, {
  "url": "include/LD/assets/icons/utility/text_background_color.svg",
  "revision": "72846f09d5934ca8cc0f24f212743fc3"
}, {
  "url": "include/LD/assets/icons/utility/text_color.svg",
  "revision": "f20035e4f90ccbfcbad152e611f3132b"
}, {
  "url": "include/LD/assets/icons/utility/text_template.svg",
  "revision": "c2786f795d6de92a47b7a2759b9df461"
}, {
  "url": "include/LD/assets/icons/utility/text.svg",
  "revision": "bd9076f0d74b8c648497e9bbac5115e5"
}, {
  "url": "include/LD/assets/icons/utility/textarea.svg",
  "revision": "65f39fa152cd297f0ca9cfad0924827e"
}, {
  "url": "include/LD/assets/icons/utility/textbox.svg",
  "revision": "0df003f848c093f60a85944cebf5b8a4"
}, {
  "url": "include/LD/assets/icons/utility/threedots_vertical.svg",
  "revision": "6fe14a6fb38d2f09159a658e28266747"
}, {
  "url": "include/LD/assets/icons/utility/threedots.svg",
  "revision": "b50568deee868bc6e0c5ca2abb2dea8c"
}, {
  "url": "include/LD/assets/icons/utility/thunder.svg",
  "revision": "4453155b1c0f76642bb0a8002e74fd2b"
}, {
  "url": "include/LD/assets/icons/utility/tile_card_list.svg",
  "revision": "7801718c8d2cc00bcbb4294779cafa3e"
}, {
  "url": "include/LD/assets/icons/utility/toggle_panel_bottom.svg",
  "revision": "797f924c07cc42ce4bf7bb7febb2ced2"
}, {
  "url": "include/LD/assets/icons/utility/toggle_panel_left.svg",
  "revision": "311da64379061a238b474227c11596e0"
}, {
  "url": "include/LD/assets/icons/utility/toggle_panel_right.svg",
  "revision": "4f4ba423d2f1f4abd47de2b5a4d6067d"
}, {
  "url": "include/LD/assets/icons/utility/toggle_panel_top.svg",
  "revision": "15beb9b901f83c065f02dd2cc1925440"
}, {
  "url": "include/LD/assets/icons/utility/toggle.svg",
  "revision": "4087313d4ce275a4c58ca6f06725db96"
}, {
  "url": "include/LD/assets/icons/utility/top_align.svg",
  "revision": "471eac2734e45c0e25c14d8a6e0b2179"
}, {
  "url": "include/LD/assets/icons/utility/topic.svg",
  "revision": "3fdeb89e2e34542c2d4f6bae379cbba6"
}, {
  "url": "include/LD/assets/icons/utility/topic2.svg",
  "revision": "0ce8619007f4984f392da554349dc34a"
}, {
  "url": "include/LD/assets/icons/utility/touch_action.svg",
  "revision": "4f96cf9831ccbaf81e9e7316b9004dd9"
}, {
  "url": "include/LD/assets/icons/utility/tour_check.svg",
  "revision": "395af3a98baef48d1b670c04fe3523f7"
}, {
  "url": "include/LD/assets/icons/utility/tour.svg",
  "revision": "cad8f3023ac8998060d3af1627fd55c1"
}, {
  "url": "include/LD/assets/icons/utility/tracker.svg",
  "revision": "9f112d0d814fd3396f29b880506354f1"
}, {
  "url": "include/LD/assets/icons/utility/trail.svg",
  "revision": "63a69bcd5877b683e9614df31cac2276"
}, {
  "url": "include/LD/assets/icons/utility/trailblazer_ext.svg",
  "revision": "0ed0ab58ea9d6565a2a2f1ff8b5f6bc2"
}, {
  "url": "include/LD/assets/icons/utility/trailhead_alt.svg",
  "revision": "b7301f7eb6774e045e17b7109ea026df"
}, {
  "url": "include/LD/assets/icons/utility/trailhead_ext.svg",
  "revision": "dbf4910a8cc6854db9cec174ea0b0ff0"
}, {
  "url": "include/LD/assets/icons/utility/trailhead.svg",
  "revision": "b5394518cbc4b329b22bd5797468f6a8"
}, {
  "url": "include/LD/assets/icons/utility/transparent.svg",
  "revision": "d578bfb29d5aa9ec6293d7c4d8de7b18"
}, {
  "url": "include/LD/assets/icons/utility/travel_and_places.svg",
  "revision": "4eedcc924a4d9e2d708bfaa3fc5770ee"
}, {
  "url": "include/LD/assets/icons/utility/trending.svg",
  "revision": "32c24cece52d6cd857a3bce03540d2cb"
}, {
  "url": "include/LD/assets/icons/utility/truck.svg",
  "revision": "3eb514afaff47b8e9d9f61646606c142"
}, {
  "url": "include/LD/assets/icons/utility/turn_off_notifications.svg",
  "revision": "0473edd3a7754605b6df251bffd4708f"
}, {
  "url": "include/LD/assets/icons/utility/type_tool.svg",
  "revision": "a18d4da9f3911aaeff1f0134876e29dc"
}, {
  "url": "include/LD/assets/icons/utility/type.svg",
  "revision": "77180c3e640561cdf7a4a66073dcc3a8"
}, {
  "url": "include/LD/assets/icons/utility/undelete.svg",
  "revision": "e603b84bbd95f615d4a1dc49b2b5ebcb"
}, {
  "url": "include/LD/assets/icons/utility/undeprecate.svg",
  "revision": "3f0975da2e58276ae22d4cf5e71c8bec"
}, {
  "url": "include/LD/assets/icons/utility/underline.svg",
  "revision": "76669bb537e5ecdb83a6e233d6568161"
}, {
  "url": "include/LD/assets/icons/utility/undo.svg",
  "revision": "da405ae1b44fa5bb33ec12dde8614e37"
}, {
  "url": "include/LD/assets/icons/utility/unlinked.svg",
  "revision": "d832116c49cd174963542a3688a1abc5"
}, {
  "url": "include/LD/assets/icons/utility/unlock.svg",
  "revision": "91fa03957c183630c016f725df46316f"
}, {
  "url": "include/LD/assets/icons/utility/unmuted.svg",
  "revision": "68c6bcf4e4821306a6e16cf75fd159ec"
}, {
  "url": "include/LD/assets/icons/utility/up.svg",
  "revision": "d97d727ad18a9f16f517453fd91b29fd"
}, {
  "url": "include/LD/assets/icons/utility/upload.svg",
  "revision": "ddfde4d9ce2198027ad89d43b2e65a0f"
}, {
  "url": "include/LD/assets/icons/utility/user_role.svg",
  "revision": "6384de60a17aa71ea712021970a03377"
}, {
  "url": "include/LD/assets/icons/utility/user.svg",
  "revision": "c721b01f767c4814faad1cb7e5e204bc"
}, {
  "url": "include/LD/assets/icons/utility/variable.svg",
  "revision": "c8a361c5523bafb939edc49006c83b93"
}, {
  "url": "include/LD/assets/icons/utility/variation_attribute_setup.svg",
  "revision": "57fcdb3ae2771eeb7bdf6a54f7782c78"
}, {
  "url": "include/LD/assets/icons/utility/variation_products.svg",
  "revision": "23bc65cd055bfbc4ebb3278757025379"
}, {
  "url": "include/LD/assets/icons/utility/video.svg",
  "revision": "bf84afdd5b97873cd646b3a67d78727e"
}, {
  "url": "include/LD/assets/icons/utility/voicemail_drop.svg",
  "revision": "95bb073d98957717a1ad0a5500407a25"
}, {
  "url": "include/LD/assets/icons/utility/volume_high.svg",
  "revision": "ddc8ac59fea2aba7ee5515c25f847467"
}, {
  "url": "include/LD/assets/icons/utility/volume_low.svg",
  "revision": "2daa06df5ab8b8f2f30719cbd5f961ad"
}, {
  "url": "include/LD/assets/icons/utility/volume_off.svg",
  "revision": "59349a58835b6922cd59abec80bb476d"
}, {
  "url": "include/LD/assets/icons/utility/waits.svg",
  "revision": "339ab731530aebe1210c0579eade2a27"
}, {
  "url": "include/LD/assets/icons/utility/warning.svg",
  "revision": "bf3bf19c8faa9a2fffb91c13ce4e678a"
}, {
  "url": "include/LD/assets/icons/utility/warranty_term.svg",
  "revision": "433c1e764c44d8977e0846d9c8abf249"
}, {
  "url": "include/LD/assets/icons/utility/watchlist.svg",
  "revision": "0eb7ae661094cb25ae0c9e8f7478d5f9"
}, {
  "url": "include/LD/assets/icons/utility/weeklyview.svg",
  "revision": "4d52da08a5080213aa0b92899f46cced"
}, {
  "url": "include/LD/assets/icons/utility/wellness.svg",
  "revision": "8814938181b151c727e825c20b4bfbbc"
}, {
  "url": "include/LD/assets/icons/utility/wifi.svg",
  "revision": "b7ca39c036281220865e1625d6408389"
}, {
  "url": "include/LD/assets/icons/utility/work_forecast.svg",
  "revision": "61d046ddd55ffe7d3dc1fa2f2435fe00"
}, {
  "url": "include/LD/assets/icons/utility/work_order_type.svg",
  "revision": "6eadd6e26145f6bead0063af5df2e0d6"
}, {
  "url": "include/LD/assets/icons/utility/workforce_engagement.svg",
  "revision": "14b825d8ec1ee0eb55cae2f294a70a41"
}, {
  "url": "include/LD/assets/icons/utility/world.svg",
  "revision": "df5e77c1d9069a96eb36a2097ef25fb2"
}, {
  "url": "include/LD/assets/icons/utility/yubi_key.svg",
  "revision": "7ace600e7790ace3a8da7527b6eb6164"
}, {
  "url": "include/LD/assets/icons/utility/zoomin.svg",
  "revision": "40c770c551917f4940a8f0b9c234dce4"
}, {
  "url": "include/LD/assets/icons/utility/zoomout.svg",
  "revision": "ebc4e1d04b18bdccf48b4fb95c994cd8"
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
  "revision": "1c73aef82642440ff8fb12e7c6804a6b"
}, {
  "url": "include/LD/assets/styles/salesforce-lightning-design-system_touch.css",
  "revision": "a65bfc28217f7bfb5dc501bfcb2fcf90"
}, {
  "url": "include/LD/assets/styles/salesforce-lightning-design-system_touch.min.css",
  "revision": "b4561dad22aaed6d7ed04e59d1f76312"
}, {
  "url": "include/LD/assets/styles/salesforce-lightning-design-system-imports.sanitized.css",
  "revision": "40426b83f37781ef65e14cc61271734a"
}, {
  "url": "include/LD/assets/styles/salesforce-lightning-design-system-legacy.css",
  "revision": "7f003ef6936b7ecf8be2cbfae79e8fbe"
}, {
  "url": "include/LD/assets/styles/salesforce-lightning-design-system-legacy.min.css",
  "revision": "f0950b8e5592ffd2be16340b569b1945"
}, {
  "url": "include/LD/assets/styles/salesforce-lightning-design-system-offline.css",
  "revision": "ae092b2cd8d12cd5e0a52036f23f8edd"
}, {
  "url": "include/LD/assets/styles/salesforce-lightning-design-system-offline.min.css",
  "revision": "571cce04488412cebac9be84a2ba5ee4"
}, {
  "url": "include/LD/assets/styles/salesforce-lightning-design-system.css",
  "revision": "df3b352dd8bd57be43c836b2d719bb14"
}, {
  "url": "include/LD/assets/styles/salesforce-lightning-design-system.min.css",
  "revision": "9aaa8278dbd9bd33e07dbfbc73a0b6d6"
}, {
  "url": "include/LD/assets/styles/salesforce-lightning-design-system.sanitized.css",
  "revision": "e6d3c65b15a4648c5e8a717437334bc3"
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
  "revision": "5dbe3cbe379baaf2559a3ca2335c5a39"
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
  "revision": "27589b8550521582f854f81920830eea"
}, {
  "url": "include/js/asterisk.js",
  "revision": "a1ecfb23bb0eabf3ae8ad819497766d7"
}, {
  "url": "include/js/clipboard.min.js",
  "revision": "3f3688138a1b9fc4ef669ce9056b6674"
}, {
  "url": "include/js/clock.js",
  "revision": "0b6bdbfee7b61226371c555ced95bde2"
}, {
  "url": "include/js/corebosjshooks.js",
  "revision": "1607aa9f922cfb8476b3635d3edd8286"
}, {
  "url": "include/js/customview.js",
  "revision": "5c870b070f867f9e010109f6faace4b1"
}, {
  "url": "include/js/de_de.lang.js",
  "revision": "9e158766a679ca94741946313fbd9505"
}, {
  "url": "include/js/dedup.js",
  "revision": "f09b1b73041383bfe8a85ccce9641617"
}, {
  "url": "include/js/dtlviewajax.js",
  "revision": "42e62b85a43a3e624ee8351c039846bb"
}, {
  "url": "include/js/en_gb.lang.js",
  "revision": "aff679c94ea9eb3794ac550935694468"
}, {
  "url": "include/js/en_us.lang.js",
  "revision": "5bd3ccb75654a790f677e51ca1521439"
}, {
  "url": "include/js/es_es.lang.js",
  "revision": "5f4698a8a811c240a82924ac53f97d95"
}, {
  "url": "include/js/es_mx.lang.js",
  "revision": "8cb1ae543c70c46e51bf350a145a8d0e"
}, {
  "url": "include/js/FieldDependencies.js",
  "revision": "8131530b0e458005e4e4006251df90a7"
}, {
  "url": "include/js/FieldDepFunc.js",
  "revision": "54277198622685e8a624edb51fb2f2c8"
}, {
  "url": "include/js/fr_fr.lang.js",
  "revision": "47d7a4074986d4ebff3b960d7c4002e1"
}, {
  "url": "include/js/general.js",
  "revision": "577d9f4ee6638f6fc2f0026c36bc6072"
}, {
  "url": "include/js/hu_hu.lang.js",
  "revision": "e0baa3a0ac210db95b02c9eb695fa8af"
}, {
  "url": "include/js/Inventory.js",
  "revision": "995e82a81fff28de27f58d755cf5a793"
}, {
  "url": "include/js/it_it.lang.js",
  "revision": "9e9bddb29852aabbdddb5a2242eea8a2"
}, {
  "url": "include/js/jslog.js",
  "revision": "4fad4667b01c3c4db2f1313c9e24968e"
}, {
  "url": "include/js/ListView.js",
  "revision": "961af040b1d4960dddde2817912ad133"
}, {
  "url": "include/js/ListViewJSON.js",
  "revision": "510877401360ea1c0880f04fdd61a9da"
}, {
  "url": "include/js/ListViewRenderes.js",
  "revision": "3111bb0321f2065d3b9694836e16d1ee"
}, {
  "url": "include/js/loadjslog.js",
  "revision": "d5317a375aa85d10e53f5989b1be0524"
}, {
  "url": "include/js/Mail.js",
  "revision": "38f36445f41a532a04011c39feb50e58"
}, {
  "url": "include/js/massive.js",
  "revision": "5a09d9ad89882ba8ad3e993e11a08055"
}, {
  "url": "include/js/masterdetailgrid.js",
  "revision": "f1b1b723e5f38cbf8b79a8f8cfcbe015"
}, {
  "url": "include/js/meld.js",
  "revision": "7c3894eb22d16cb4743a7ea226557ed1"
}, {
  "url": "include/js/Merge.js",
  "revision": "92db09ac10a555be4bfc0398f2a93379"
}, {
  "url": "include/js/nl_nl.lang.js",
  "revision": "05a8d20f6b32a3f97dbbd5bbd970b47a"
}, {
  "url": "include/js/notebook.js",
  "revision": "73d64bfb31e007a957e7de2ed1fdc5aa"
}, {
  "url": "include/js/notificationPopup.js",
  "revision": "3cfd7e34e7fee928b8c27cd0e9f1f46c"
}, {
  "url": "include/js/PasswordManagement.js",
  "revision": "b330f25041c993762b1467bbcd0041ce"
}, {
  "url": "include/js/picklist.js",
  "revision": "ea64995ae0a42b003106d3241d5aef29"
}, {
  "url": "include/js/popup.js",
  "revision": "f7193e74de91d378f10f4cb9941dc70c"
}, {
  "url": "include/js/pt_br.lang.js",
  "revision": "db2e772bc7d6dc72bde227385df56ba4"
}, {
  "url": "include/js/QuickCreate.js",
  "revision": "9663dd1cea6cb7fb0cfe983e54baa186"
}, {
  "url": "include/js/RelatedLists.js",
  "revision": "afd1f3cd61451676111b6d7b6adb1e16"
}, {
  "url": "include/js/ro_ro.lang.js",
  "revision": "19aef31823b4860239e5cad8f6943621"
}, {
  "url": "include/js/search.js",
  "revision": "d5c8ddcb6451bc0c05aa26f123363e0f"
}, {
  "url": "include/js/smoothscroll.js",
  "revision": "008f3e9768bd2d226e65ea69999c9f14"
}, {
  "url": "include/js/vtlib.js",
  "revision": "93fc7a80bf9ffdd6e6abeafffa52b164"
}, {
  "url": "include/components/checkboxrenderer.js",
  "revision": "30a720fa2d1634076e325d7cd721acf7"
}, {
  "url": "include/components/Colorizer/listview.css",
  "revision": "05becc7a0a84cda65e7fec3e97ff7933"
}, {
  "url": "include/components/ldsmodal.js",
  "revision": "7dfd6f1a5414ea716cabb1982cbdcbac"
}, {
  "url": "include/components/ldsprompt.js",
  "revision": "dcff6c137f5d5a9669c75dd4a719f2b0"
}, {
  "url": "include/components/listview/listview.css",
  "revision": "a6fdd21cdff361260ef6c59dc7215016"
}, {
  "url": "include/components/loadjs.js",
  "revision": "03a39dd327aca956ea9448782aca04eb"
}, {
  "url": "include/components/PaintJS/control.css",
  "revision": "633a32ba70580b50494e92cf1eceb66d"
}, {
  "url": "include/components/PaintJS/gradientcreator.css",
  "revision": "bef2033786929d2f3889f3f1a118ee83"
}, {
  "url": "include/components/PaintJS/images/block.png",
  "revision": "1b269616a8a6718edbddb9e47c48fbd9"
}, {
  "url": "include/components/PaintJS/images/brush.png",
  "revision": "f0fb641da5e72270add4e9cf6a4b113a"
}, {
  "url": "include/components/PaintJS/images/grab.png",
  "revision": "bf3a09b1ff490703541b37eb5d1de87a"
}, {
  "url": "include/components/PaintJS/images/line.png",
  "revision": "87651d5a2ae6e56c303a500f15898fd8"
}, {
  "url": "include/components/PaintJS/images/picker.png",
  "revision": "bcc9df3deaeb788102cb22d087299325"
}, {
  "url": "include/components/PaintJS/images/randomlocation.png",
  "revision": "bd16a4f26d1c5a656035428bdbf88165"
}, {
  "url": "include/components/PaintJS/images/select.png",
  "revision": "2d7fb763b4d3a5962129fe7e2fd2a5a1"
}, {
  "url": "include/components/PaintJS/images/text.png",
  "revision": "06d323669c1ffdbe9d2d2e0e25c0a83e"
}, {
  "url": "include/components/PaintJS/images/transparent.png",
  "revision": "3c07752b1d92c1ed6217d5451bd72a66"
}, {
  "url": "include/components/PaintJS/images/undo.png",
  "revision": "50db48bed898b31c941546fb39dd7004"
}, {
  "url": "include/components/PaintJS/images/zoom.png",
  "revision": "0347588626a19f83a1a66c78f56513ec"
}, {
  "url": "include/components/PaintJS/images/zoomin.png",
  "revision": "5ba825d630c0a60bee37e8b17828ae96"
}, {
  "url": "include/components/PaintJS/images/zoomout.png",
  "revision": "51642eabf4149bba697d725960a4197a"
}, {
  "url": "include/components/PaintJS/images/zoomreset.png",
  "revision": "14d93ea9cba42407539bee8538d6f73f"
}, {
  "url": "include/components/PaintJS/introjs.css",
  "revision": "4318a4823670d9318f0462560978e233"
}, {
  "url": "include/components/PaintJS/paint.css",
  "revision": "b94db8c78295f03090586ab6457ba0c7"
}, {
  "url": "include/components/PaintJS/Paint.min.js",
  "revision": "60bbe4b642c22f2fa8f3c21fb9166c42"
}, {
  "url": "include/components/PaintJS/quicksettings.css",
  "revision": "1877ec1f864f1a115c0f134e97ef63a4"
}, {
  "url": "include/components/PaintJS/script.js",
  "revision": "94ee218976c17d84c4e88f36e7eb171d"
}, {
  "url": "include/components/PaintJS/spectrum.css",
  "revision": "2a1922c40199c91899ee4c07033b8dfa"
}, {
  "url": "include/components/Select2/css/select2.css",
  "revision": "675de2a347f3407251629544a114be97"
}, {
  "url": "include/components/Select2/js/select2.min.js",
  "revision": "3e6e33cd306b1235add3d25fcda8541d"
}, {
  "url": "include/components/toast-ui/grid/tui-grid.css",
  "revision": "4a7f0ca8abc6d524a4231cd5e790211f"
}, {
  "url": "include/components/toast-ui/grid/tui-grid.js",
  "revision": "665e90b14ed8bb8d5785e078e591db31"
}, {
  "url": "include/components/toast-ui/grid/tui-grid.min.css",
  "revision": "af7ae0b1f88c7d2c1c1fea9d52b2b744"
}, {
  "url": "include/components/toast-ui/grid/tui-grid.min.js",
  "revision": "ee7c5a46f5d0b0ab56374502fedaefda"
}, {
  "url": "include/components/toast-ui/pagination/tui-pagination.css",
  "revision": "70ffbb5f994ca8a5038f529254ab0feb"
}, {
  "url": "include/components/toast-ui/pagination/tui-pagination.js",
  "revision": "08f21dc4edd18e61d75bb952880bb6af"
}, {
  "url": "include/components/toast-ui/pagination/tui-pagination.min.css",
  "revision": "69a84e67789721fcd503c0bfe8c9346f"
}, {
  "url": "include/components/toast-ui/pagination/tui-pagination.min.js",
  "revision": "502672ea6d25834d6b694fb592f01b7c"
}, {
  "url": "include/components/toast-ui/tui-date-picker/tui-date-picker.css",
  "revision": "282a5cb4943a9abaddce76ad0bfe5292"
}, {
  "url": "include/components/toast-ui/tui-date-picker/tui-date-picker.js",
  "revision": "e7ac7c73b882a8b563da929c4a227b6a"
}, {
  "url": "include/components/toast-ui/tui-date-picker/tui-date-picker.min.css",
  "revision": "011afcc90ef141f2a25a53191f109fdc"
}, {
  "url": "include/components/toast-ui/tui-date-picker/tui-date-picker.min.js",
  "revision": "377becbfd944235b82b8ae0e9f188fce"
}, {
  "url": "include/components/toast-ui/tui-time-picker/tui-time-picker.css",
  "revision": "c8d66f9880ba4f699547dbcfaf5d71e0"
}, {
  "url": "include/components/toast-ui/tui-time-picker/tui-time-picker.js",
  "revision": "7486ffbf3d3421292477f1e4dd83087e"
}, {
  "url": "include/components/toast-ui/tui-time-picker/tui-time-picker.min.css",
  "revision": "4c740d41e4546080debfb619eafa217f"
}, {
  "url": "include/components/toast-ui/tui-time-picker/tui-time-picker.min.js",
  "revision": "93ba6c21f2a3038f2a0e5e4582acb536"
}, {
  "url": "include/chart.js/Chart.bundle.js",
  "revision": "cda1cb9319dc70d4cb41ce6c9164740b"
}, {
  "url": "include/chart.js/Chart.bundle.min.js",
  "revision": "2ef089205edd1cf2c7953f54ceca8670"
}, {
  "url": "include/chart.js/Chart.css",
  "revision": "28dc89b92b7e59392029cfd2769027ab"
}, {
  "url": "include/chart.js/Chart.js",
  "revision": "ff2c8fa2e8f8c348765b8072a78ff161"
}, {
  "url": "include/chart.js/Chart.min.css",
  "revision": "7d8693e997109f2aeac04066301679d6"
}, {
  "url": "include/chart.js/Chart.min.js",
  "revision": "9b1ae20c4c7048d6e4a1b2e1aee7fb31"
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
  "url": "include/MassCreateGridView/MassCreateGridView.js",
  "revision": "a0ee36da94058a469df96cba89b1910b"
}, {
  "url": "include/style.css",
  "revision": "d366c521d8140f4799b529e65f6d53df"
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
  "revision": "fc6646a77fe08a9ae143dc4384b6844f"
}, {
  "url": "modules/com_vtiger_workflow/resources/edittaskscript.js",
  "revision": "fa771ef0bc64b0d810f564fe7eef632f"
}, {
  "url": "modules/com_vtiger_workflow/resources/editworkflowscript.js",
  "revision": "1f4424f72609c356317ea1f8c15a5a5d"
}, {
  "url": "modules/com_vtiger_workflow/resources/emailtaskscript.js",
  "revision": "fc683735b7a78c8fc840d73220632b65"
}, {
  "url": "modules/com_vtiger_workflow/resources/entitymethodtask.js",
  "revision": "43155535cd6da302c6ad1c34c077c642"
}, {
  "url": "modules/com_vtiger_workflow/resources/fieldexpressionpopup.js",
  "revision": "ebe549a55c73d6944f383b6a1ea2c7a6"
}, {
  "url": "modules/com_vtiger_workflow/resources/fieldvalidator.js",
  "revision": "91e8f50079fee141af553fbb28f2588f"
}, {
  "url": "modules/com_vtiger_workflow/resources/functional.js",
  "revision": "507e0618da87701f11b973970362e3cf"
}, {
  "url": "modules/com_vtiger_workflow/resources/functionselect.js",
  "revision": "fbbb38c1f413160c975118511269cc09"
}, {
  "url": "modules/com_vtiger_workflow/resources/generateimagecode.js",
  "revision": "5e5ab3e366035fbf7da2e20ca4edb606"
}, {
  "url": "modules/com_vtiger_workflow/resources/generateReportWfTask.js",
  "revision": "8c30d1f6f296735bb822882e9016d03b"
}, {
  "url": "modules/com_vtiger_workflow/resources/ico-workflow.png",
  "revision": "851ca4caa867fa005174afec1fea762f"
}, {
  "url": "modules/com_vtiger_workflow/resources/jquery.timepicker.js",
  "revision": "cf04488523916947850d381c20427f50"
}, {
  "url": "modules/com_vtiger_workflow/resources/launchworkflowtask.js",
  "revision": "f9abcd1f86a21b38f20e043ff01df6e4"
}, {
  "url": "modules/com_vtiger_workflow/resources/many2manyrelation.js",
  "revision": "8778e149e296ca91f9c4c941007fc17c"
}, {
  "url": "modules/com_vtiger_workflow/resources/onesignalckeditor.js",
  "revision": "db5bb45a2c7939934e6903c9818790bb"
}, {
  "url": "modules/com_vtiger_workflow/resources/onesignalworkflowtaskscript.js",
  "revision": "3510b916b211fa90839f375cba24edc2"
}, {
  "url": "modules/com_vtiger_workflow/resources/parallelexecuter.js",
  "revision": "c75586a43f6d52d0528ae16a16ea689b"
}, {
  "url": "modules/com_vtiger_workflow/resources/remove.png",
  "revision": "c00f6462ceeb5ab01f6cdf297e0e8735"
}, {
  "url": "modules/com_vtiger_workflow/resources/style.css",
  "revision": "a92ca52bdb5b6d1350e3681bf93f4682"
}, {
  "url": "modules/com_vtiger_workflow/resources/updatefieldstaskscript.js",
  "revision": "dd2433dead063a2ba37f9c7c37e6190e"
}, {
  "url": "modules/com_vtiger_workflow/resources/updatemassivefieldstaskscript.js",
  "revision": "6d9f81de1ff75ec596813151f2bd3034"
}, {
  "url": "modules/com_vtiger_workflow/resources/upserttask.js",
  "revision": "553834ae8f29bd25ced0730f0c33e19e"
}, {
  "url": "modules/com_vtiger_workflow/resources/vtigerwebservices.js",
  "revision": "57eb8c551a6bceb48b08822fb48438d0"
}, {
  "url": "modules/com_vtiger_workflow/resources/wfexeexp.js",
  "revision": "70312429e9d136159b33fd985ede4ff8"
}, {
  "url": "modules/com_vtiger_workflow/resources/wfSendFile.js",
  "revision": "9ca8d03c511c69f165bb5fa56b9c144c"
}, {
  "url": "modules/com_vtiger_workflow/resources/Whatsappckeditor.js",
  "revision": "fab39532561524488d757c60df5fedd7"
}, {
  "url": "modules/com_vtiger_workflow/resources/whatsappworkflowtaskscript.js",
  "revision": "4ba8509ee8c1b014e06df45653e562d5"
}, {
  "url": "modules/com_vtiger_workflow/resources/workflowlistscript.js",
  "revision": "deebc6a6db9909ad860362f26eb864b3"
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
  "revision": "8e07d2d467ad11f0e020d3936d71b856"
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
  "revision": "c0e3c989708fc430859a004175b9e59c"
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
  "revision": "d166fb54361a463cf82a8b5cd908708c"
}, {
  "url": "modules/MailManager/MailManager.png",
  "revision": "1c045d808355b6ed581cd4971f3436f9"
}, {
  "url": "modules/MailManager/resources/jquery.tokeninput.js",
  "revision": "2225af5abe843f40dc29d79e0b1ea565"
}, {
  "url": "modules/MailManager/resources/token-input-facebook.css",
  "revision": "a521fc5fcc6cc8dcf84edf3b26158805"
}, {
  "url": "modules/Tooltip/Tooltip.js",
  "revision": "651680d2a9ffb37861fcac6d52d83f24"
}, {
  "url": "modules/Tooltip/Tooltip.png",
  "revision": "fc384192e4fa1a8cf362dd13fc9795b4"
}, {
  "url": "modules/Tooltip/TooltipHeaderScript.js",
  "revision": "eecf7ef8e5030d1053235e5bf68311f5"
}, {
  "url": "modules/Tooltip/TooltipSettings.js",
  "revision": "7d1c2445657e28da2cbcdebcace07c96"
}, {
  "url": "modules/Accounts/Accounts.png",
  "revision": "f67a467753048c2b24896fea369f94c7"
}, {
  "url": "modules/Assets/Assets.png",
  "revision": "1731082fd75d2dd046cd8d2b205f205c"
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
  "revision": "e32fd9e9d52794e54e0de77c15cc8acf"
}, {
  "url": "modules/SalesOrder/SalesOrder.js",
  "revision": "16492cb9a5e8791e2b8f443f5aedac2f"
}, {
  "url": "modules/Settings/Settings.js",
  "revision": "c081711ead8af84fe7f995d23462f0e7"
}, {
  "url": "modules/Settings/profilePrivileges.js",
  "revision": "e8735c467cb77eab04e51f77a14ca87b"
}, {
  "url": "modules/Products/Productsslide.js",
  "revision": "721bd3d5367129defea7f6bd1bb8eee3"
}, {
  "url": "modules/Products/multifile.js",
  "revision": "d9dc85cdf658755d29cdb4704fea4882"
}, {
  "url": "modules/Products/Products.js",
  "revision": "df2676fb062d6670d5f0c696bbe74c17"
}, {
  "url": "modules/ModComments/ModComments.js",
  "revision": "253c82414388480dd8d07ec5f899968d"
}, {
  "url": "modules/ModComments/ModCommentsCommon.js",
  "revision": "842d921f10a4cdd8e17ee17b92a238e1"
}, {
  "url": "modules/MsgTemplate/MsgTemplate.js",
  "revision": "fa465d9a6a3e943b658f9db8aec162f5"
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
  "revision": "c5f394c40d03b359242e795da97a9d45"
}, {
  "url": "modules/Rss/Rss.js",
  "revision": "72cbe52c78db93657e84d24dfadce955"
}, {
  "url": "modules/PickList/DependencyPicklist.js",
  "revision": "f2a574dba224e1925d8f0fb6df197a16"
}, {
  "url": "modules/HelpDesk/HelpDesk.js",
  "revision": "7cdb17802602d0c07de8fbeb2d2efbc4"
}, {
  "url": "modules/Potentials/Potentials.js",
  "revision": "5e37ebfc8e680819028987f996246f3a"
}, {
  "url": "modules/CronTasks/CronTasks.js",
  "revision": "5ed805a40a9bb0df7a12e9bd966311ab"
}, {
  "url": "modules/PBXManager/PBXManager.js",
  "revision": "c663d6d8dfeba2b51a542382c8623087"
}, {
  "url": "modules/cbCalendar/script.js",
  "revision": "540de50fa20840cf36d96db9df7a208a"
}, {
  "url": "modules/cbCalendar/cbCalendar.js",
  "revision": "850ff484cc49801561ed0620843b7b19"
}, {
  "url": "modules/cbQuestion/cbQuestion.js",
  "revision": "df1beb66383c404ca19595bfede77a1f"
}, {
  "url": "modules/cbQuestion/resources/appendcontext.js",
  "revision": "45f26b6647e4bb9c319c2623e23281f7"
}, {
  "url": "modules/cbQuestion/resources/Builder.js",
  "revision": "5c1c29a15ca0846167275c8ef8cbb74a"
}, {
  "url": "modules/cbQuestion/resources/editbuilder.js",
  "revision": "581d7a38ba0424597a4cf76897ae493d"
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
  "revision": "e927ef1579f603e81f2fa3a80201d5fa"
}, {
  "url": "modules/ProjectMilestone/ProjectMilestone.js",
  "revision": "c240060c1b952bba5d36d4a506fa0e6e"
}, {
  "url": "modules/Leads/Leads.js",
  "revision": "bd824c092fa3ac7e497908067a598afb"
}, {
  "url": "modules/WSAPP/WSAPP.js",
  "revision": "36b3093ded85fbdb78336ed100dae8ac"
}, {
  "url": "modules/Accounts/Accounts.js",
  "revision": "62ebabcb3d9f2ffc968f93808a84fc82"
}, {
  "url": "modules/cbMap/cbMap.js",
  "revision": "ac4053b733101a16e9ba2a83b0bd7f5a"
}, {
  "url": "modules/cbMap/language/de_de.js",
  "revision": "07af75347840dd7440241b543551a284"
}, {
  "url": "modules/cbMap/language/en_gb.js",
  "revision": "07af75347840dd7440241b543551a284"
}, {
  "url": "modules/cbMap/language/en_us.js",
  "revision": "07af75347840dd7440241b543551a284"
}, {
  "url": "modules/cbMap/language/es_es.js",
  "revision": "0bf0bf86e6072e58cf6e0125c5c8295d"
}, {
  "url": "modules/cbMap/language/es_mx.js",
  "revision": "0bf0bf86e6072e58cf6e0125c5c8295d"
}, {
  "url": "modules/cbMap/language/fr_fr.js",
  "revision": "07af75347840dd7440241b543551a284"
}, {
  "url": "modules/cbMap/language/hu_hu.js",
  "revision": "07af75347840dd7440241b543551a284"
}, {
  "url": "modules/cbMap/language/it_it.js",
  "revision": "07af75347840dd7440241b543551a284"
}, {
  "url": "modules/cbMap/language/nl_nl.js",
  "revision": "07af75347840dd7440241b543551a284"
}, {
  "url": "modules/cbMap/language/pt_br.js",
  "revision": "139baffdd70d96a39bad3fd8eecc3736"
}, {
  "url": "modules/cbMap/language/ro_ro.js",
  "revision": "07af75347840dd7440241b543551a284"
}, {
  "url": "modules/cbMap/generatemap/DecisionTable.js",
  "revision": "7c6a0066ea3fb375f23412a076d5063a"
}, {
  "url": "modules/cbMap/generatemap/DetailViewLayoutMapping.js",
  "revision": "d333d135915576c76507e5b2f47b3729"
}, {
  "url": "modules/cbMap/generatemap/DuplicateRelations.js",
  "revision": "a2c24e60f601be44f6d8a25b0a5e8431"
}, {
  "url": "modules/cbMap/generatemap/MassUpsertGrid.js",
  "revision": "a5f75e9578cc608a1747d58ab810cb24"
}, {
  "url": "modules/cbTermConditions/cbTermConditions.js",
  "revision": "c240060c1b952bba5d36d4a506fa0e6e"
}, {
  "url": "modules/Campaigns/Campaigns.js",
  "revision": "36804a710b0d90fecd80178cc5f6f0a2"
}, {
  "url": "modules/Invoice/Invoice.js",
  "revision": "9fc40d1e38d6a707c4b0a3c08e464885"
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
  "revision": "15e47a85456e109ca860fd85ec223d13"
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
  "revision": "2c3a5d0e3cfb7010482b1afed64a4746"
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
  "revision": "068213bd61bee2aeb53b4b95f6b72dc8"
}, {
  "url": "modules/ModTracker/language/ro_ro.js",
  "revision": "bdafc8809c554dd1490838aa8132e6c2"
}, {
  "url": "modules/SMSNotifier/workflow/VTSMSTask.js",
  "revision": "4e628b4202dd68d3887196edfef07704"
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
  "revision": "26560f6da689eaa78e22f13f70aca335"
}, {
  "url": "modules/Dashboard/Dashboard.js",
  "revision": "f7fa08f24ea66f67c5b455f7562ad998"
}, {
  "url": "modules/Services/Services.js",
  "revision": "8239244f488a12072bf6d22bfcd24f3e"
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
  "revision": "aba71d0bc4a7c39fed8e8e9e421d5b35"
}, {
  "url": "modules/Users/ChangePassword.js",
  "revision": "aba9f38466e2ee904b8465a3cbe65de7"
}, {
  "url": "modules/Utilities/Utilities.js",
  "revision": "18844ee70d6993cd1e9bbb8bb412d097"
}, {
  "url": "modules/Import/resources/ImportStep2.js",
  "revision": "f98f9eeb54cd2b95375d74c1af6a9f14"
}, {
  "url": "modules/Import/resources/Import.js",
  "revision": "6880725f9f001f12a48fe442b8bf4bbf"
}, {
  "url": "modules/CustomView/CustomView.js",
  "revision": "65682db92a471ba46f12737f6f6b3e4d"
}, {
  "url": "modules/Reports/Reports.js",
  "revision": "6f79422b9a80f2a8c68196bad5376eac"
}, {
  "url": "modules/Reports/ReportsSteps.js",
  "revision": "9eb56ccf0c9161a66bdbd8170be17d3f"
}, {
  "url": "modules/PriceBooks/PriceBooks.js",
  "revision": "c8af6625b22f69a1f9ff221f2312b252"
}, {
  "url": "modules/Quotes/Quotes.js",
  "revision": "c2bc50ca831f1b6183c8f806d0c230f6"
}, {
  "url": "modules/PurchaseOrder/PurchaseOrder.js",
  "revision": "c4d6beb74ea9772d82bdde6a243a16fd"
}, {
  "url": "include/Webservices/WSClient.js",
  "revision": "062def9baf01f014c4baf6568704316a"
}, {
  "url": "include/freetag/jquery.tagcanvas.js",
  "revision": "c6e953f037f6294b34a42c9155fc20bf"
}, {
  "url": "include/freetag/jquery.tagcanvas.min.js",
  "revision": "2730cc84e040244a8fb029ffe2609e63"
}, {
  "url": "include/freetag/tagcanvas.min.js",
  "revision": "b82c049a6299fc20fddde4c4ec998e9c"
}, {
  "url": "include/freetag/tagcanvas.js",
  "revision": "109089828ca587dcf0be263da4bf0533"
}], {});
