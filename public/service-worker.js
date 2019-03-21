self.addEventListener('install', function(event) {
});
var CACHE_NAME = 'my-site-cache-v1';
var urlsToCache = [

  '../css/utils.css'
];

self.addEventListener('fetch', function(event) {
  event.respondWith(caches.match(event.request).then(function(response) {
    if (response !== undefined) {
      return response;
    } else {
      return fetch(event.request).then(function (response) {
        let responseClone = response.clone();
        return response;
      }).catch(function () {
        return caches.match();
      });
    }
  }));
});