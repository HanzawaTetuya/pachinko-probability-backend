'use strict';
const MANIFEST = 'flutter-app-manifest';
const TEMP = 'flutter-temp-cache';
const CACHE_NAME = 'flutter-app-cache';

const RESOURCES = {"assets/AssetManifest.bin": "886b03e065fe3d069305d9278710b73a",
"assets/AssetManifest.bin.json": "30fda3571eb9bd2541f044a41a71461b",
"assets/AssetManifest.json": "e08459ad3e1118a42c74dfbc3e4cf200",
"assets/assets/back-button.png": "6a71325d57bd84ca3ac213803d5c9eeb",
"assets/assets/comparison/number-tag-b-1.png": "1fc4e568dec7f87a5f9b7ff683e439bd",
"assets/assets/comparison/number-tag-b-2.png": "5a36ab5727f0872de109dd0c28c4584d",
"assets/assets/comparison/number-tag-b-3.png": "019426426616b58f4b164ebf29700b9c",
"assets/assets/comparison/number-tag-s-1.png": "55718722fc1b1e9472af16492b716d56",
"assets/assets/comparison/number-tag-s-2.png": "46ce771fbb10829a907d64dc45b56859",
"assets/assets/comparison/number-tag-s-3.png": "25e1e3435654c0fb389c928c9a52f0e0",
"assets/assets/delete-icon.png": "a06cc1d96126ec274003a80118154592",
"assets/assets/detail-arrow-white.png": "2224d58769ac9dabb82f31cfc2c11b16",
"assets/assets/favorite.png": "2d1d0988251c2a091f0844fa77c5e6e7",
"assets/assets/fonts/NotoSansJP-Black.otf": "2b4777ccae990731c94ea834df3f503b",
"assets/assets/fonts/NotoSansJP-Bold.otf": "3db31919b255ed7261e7329f0f8303a4",
"assets/assets/fonts/NotoSansJP-Light.otf": "5da08a6f2f4e08e906fe93ba44232278",
"assets/assets/fonts/NotoSansJP-Medium.otf": "366120c9b4029066302ca6d75afb6f8b",
"assets/assets/fonts/NotoSansJP-Regular.otf": "b5f8ef0cb10cf4fa6dfc584d29908421",
"assets/assets/fonts/NotoSansJP-Thin.otf": "b2aae66e06d9d23604f17240ae03d07a",
"assets/assets/footer/home-icon-top.png": "0839e5ab1bfa96d74e54043f6de2cb7d",
"assets/assets/footer/home-icon.png": "838cc4508750e41aab62920401a9c626",
"assets/assets/footer/mypage-icon-top.png": "9545a7514c4f0706e523f5fe5dd2f614",
"assets/assets/footer/mypage-icon.png": "17d6b7305502afe282306cd7fe5f362a",
"assets/assets/footer/news-icon-top.png": "255bf11925977b2fdfec40954aaec069",
"assets/assets/footer/news-icon.png": "30af3ab916c0ffb283bd4821bf61efe6",
"assets/assets/footer/product-icon-top.png": "0f4e1f9d05322e848c1b9d63e98f0c5e",
"assets/assets/footer/product-icon.png": "d6c5aa5ca4b1c3534e35ed7141a4156c",
"assets/assets/footer/use-icon-top.png": "f841de3e5c65c3ef6c5a754bd68639a8",
"assets/assets/footer/use-icon.png": "e905c15acab5cf64d297f063581ca728",
"assets/assets/header-back-button.png": "70ff4a5404fe6e88a70ceb0d9cb3e0c2",
"assets/assets/home/detail-button.png": "a720de5b268df655c3b17cd21d893e1a",
"assets/assets/home/user-icon.png": "9edc1905b3c0e4a39d32dfefd42f49ee",
"assets/assets/home-back.png": "cbd2d9859aca406171b62b1fbbd6ad41",
"assets/assets/img-big.png": "30446edf7ba5528880babf5d4df36fdd",
"assets/assets/main-logo.png": "dae5ac907e6a3a3c4e63ea78b2b725cc",
"assets/assets/mypage/mypage-cart.png": "9b25d9c29447b0618860afb4cae72376",
"assets/assets/mypage/mypage-favorite.png": "ca66b078313ce50389e1e9b089701529",
"assets/assets/mypage/mypage-notification.png": "04b2d918b92e079c5e8130323a846abc",
"assets/assets/mypage/mypage-support.png": "9044babc5add9b509bd3678b05033bb0",
"assets/assets/mypage/mypage-user.png": "13027be53b4a0e905bb5d519dbc718e5",
"assets/assets/mypage/user-detail.png": "2224d58769ac9dabb82f31cfc2c11b16",
"assets/assets/news/small-detail.png": "d0b618b01b677b3a25fbf62ec901b49a",
"assets/assets/no-see.svg": "45f99313090496190369b110a53e4f6f",
"assets/assets/not-favorite.png": "2bbc25f7a6ba62d5a203849e2ebe1b43",
"assets/assets/product/cart.png": "72141a63d32ffa371b88121404cb6673",
"assets/assets/product/product-detail.png": "5f46d08dcf294279712ab229caa262d9",
"assets/assets/search.png": "5c817b31f4b4d8710ef10c659c75d732",
"assets/assets/see.svg": "49a384dd1f95ee1418e13575cfbbea26",
"assets/assets/test-top-img.png": "4c837103e0025e0993913330f73c8c82",
"assets/assets/user-detail-black.png": "bcc9d7718e639cd8e4beda13c46f7f85",
"assets/FontManifest.json": "82cca82d4ca30f51fcde97658de9ed3f",
"assets/fonts/MaterialIcons-Regular.otf": "c1c06afe632424ca55b1b8e7af76fa4c",
"assets/NOTICES": "948b67b1b252b0579c82e61fd51bbfeb",
"assets/shaders/ink_sparkle.frag": "ecc85a2e95f5e9f53123dcaf8cb9b6ce",
"canvaskit/canvaskit.js": "66177750aff65a66cb07bb44b8c6422b",
"canvaskit/canvaskit.js.symbols": "48c83a2ce573d9692e8d970e288d75f7",
"canvaskit/canvaskit.wasm": "1f237a213d7370cf95f443d896176460",
"canvaskit/chromium/canvaskit.js": "671c6b4f8fcc199dcc551c7bb125f239",
"canvaskit/chromium/canvaskit.js.symbols": "a012ed99ccba193cf96bb2643003f6fc",
"canvaskit/chromium/canvaskit.wasm": "b1ac05b29c127d86df4bcfbf50dd902a",
"canvaskit/skwasm.js": "694fda5704053957c2594de355805228",
"canvaskit/skwasm.js.symbols": "262f4827a1317abb59d71d6c587a93e2",
"canvaskit/skwasm.wasm": "9f0c0c02b82a910d12ce0543ec130e60",
"canvaskit/skwasm.worker.js": "89990e8c92bcb123999aa81f7e203b1c",
"favicon.png": "5dcef449791fa27946b3d35ad8803796",
"flutter.js": "f393d3c16b631f36852323de8e583132",
"flutter_bootstrap.js": "ff67d805d8fa1b7bdbae7c3014fc8891",
"icons/Icon-192.png": "ac9a721a12bbc803b44f645561ecb1e1",
"icons/Icon-512.png": "96e752610906ba2a93c65f8abe1645f1",
"icons/Icon-maskable-192.png": "c457ef57daa1d16f64b27b786ec2ea3c",
"icons/Icon-maskable-512.png": "301a7604d45b3e739efc881eb04896ea",
"index.html": "25f592252e5d2e6a0f4b2127115a4d08",
"/": "25f592252e5d2e6a0f4b2127115a4d08",
"main.dart.js": "8c6fb1cdb3d03d7db02e0382487f7287",
"manifest.json": "860b748ee5f825b39d70bfde8e81283e",
"version.json": "69f1412c7495d51736b30175f03ea5d9"};
// The application shell files that are downloaded before a service worker can
// start.
const CORE = ["main.dart.js",
"index.html",
"flutter_bootstrap.js",
"assets/AssetManifest.bin.json",
"assets/FontManifest.json"];

// During install, the TEMP cache is populated with the application shell files.
self.addEventListener("install", (event) => {
  self.skipWaiting();
  return event.waitUntil(
    caches.open(TEMP).then((cache) => {
      return cache.addAll(
        CORE.map((value) => new Request(value, {'cache': 'reload'})));
    })
  );
});
// During activate, the cache is populated with the temp files downloaded in
// install. If this service worker is upgrading from one with a saved
// MANIFEST, then use this to retain unchanged resource files.
self.addEventListener("activate", function(event) {
  return event.waitUntil(async function() {
    try {
      var contentCache = await caches.open(CACHE_NAME);
      var tempCache = await caches.open(TEMP);
      var manifestCache = await caches.open(MANIFEST);
      var manifest = await manifestCache.match('manifest');
      // When there is no prior manifest, clear the entire cache.
      if (!manifest) {
        await caches.delete(CACHE_NAME);
        contentCache = await caches.open(CACHE_NAME);
        for (var request of await tempCache.keys()) {
          var response = await tempCache.match(request);
          await contentCache.put(request, response);
        }
        await caches.delete(TEMP);
        // Save the manifest to make future upgrades efficient.
        await manifestCache.put('manifest', new Response(JSON.stringify(RESOURCES)));
        // Claim client to enable caching on first launch
        self.clients.claim();
        return;
      }
      var oldManifest = await manifest.json();
      var origin = self.location.origin;
      for (var request of await contentCache.keys()) {
        var key = request.url.substring(origin.length + 1);
        if (key == "") {
          key = "/";
        }
        // If a resource from the old manifest is not in the new cache, or if
        // the MD5 sum has changed, delete it. Otherwise the resource is left
        // in the cache and can be reused by the new service worker.
        if (!RESOURCES[key] || RESOURCES[key] != oldManifest[key]) {
          await contentCache.delete(request);
        }
      }
      // Populate the cache with the app shell TEMP files, potentially overwriting
      // cache files preserved above.
      for (var request of await tempCache.keys()) {
        var response = await tempCache.match(request);
        await contentCache.put(request, response);
      }
      await caches.delete(TEMP);
      // Save the manifest to make future upgrades efficient.
      await manifestCache.put('manifest', new Response(JSON.stringify(RESOURCES)));
      // Claim client to enable caching on first launch
      self.clients.claim();
      return;
    } catch (err) {
      // On an unhandled exception the state of the cache cannot be guaranteed.
      console.error('Failed to upgrade service worker: ' + err);
      await caches.delete(CACHE_NAME);
      await caches.delete(TEMP);
      await caches.delete(MANIFEST);
    }
  }());
});
// The fetch handler redirects requests for RESOURCE files to the service
// worker cache.
self.addEventListener("fetch", (event) => {
  if (event.request.method !== 'GET') {
    return;
  }
  var origin = self.location.origin;
  var key = event.request.url.substring(origin.length + 1);
  // Redirect URLs to the index.html
  if (key.indexOf('?v=') != -1) {
    key = key.split('?v=')[0];
  }
  if (event.request.url == origin || event.request.url.startsWith(origin + '/#') || key == '') {
    key = '/';
  }
  // If the URL is not the RESOURCE list then return to signal that the
  // browser should take over.
  if (!RESOURCES[key]) {
    return;
  }
  // If the URL is the index.html, perform an online-first request.
  if (key == '/') {
    return onlineFirst(event);
  }
  event.respondWith(caches.open(CACHE_NAME)
    .then((cache) =>  {
      return cache.match(event.request).then((response) => {
        // Either respond with the cached resource, or perform a fetch and
        // lazily populate the cache only if the resource was successfully fetched.
        return response || fetch(event.request).then((response) => {
          if (response && Boolean(response.ok)) {
            cache.put(event.request, response.clone());
          }
          return response;
        });
      })
    })
  );
});
self.addEventListener('message', (event) => {
  // SkipWaiting can be used to immediately activate a waiting service worker.
  // This will also require a page refresh triggered by the main worker.
  if (event.data === 'skipWaiting') {
    self.skipWaiting();
    return;
  }
  if (event.data === 'downloadOffline') {
    downloadOffline();
    return;
  }
});
// Download offline will check the RESOURCES for all files not in the cache
// and populate them.
async function downloadOffline() {
  var resources = [];
  var contentCache = await caches.open(CACHE_NAME);
  var currentContent = {};
  for (var request of await contentCache.keys()) {
    var key = request.url.substring(origin.length + 1);
    if (key == "") {
      key = "/";
    }
    currentContent[key] = true;
  }
  for (var resourceKey of Object.keys(RESOURCES)) {
    if (!currentContent[resourceKey]) {
      resources.push(resourceKey);
    }
  }
  return contentCache.addAll(resources);
}
// Attempt to download the resource online before falling back to
// the offline cache.
function onlineFirst(event) {
  return event.respondWith(
    fetch(event.request).then((response) => {
      return caches.open(CACHE_NAME).then((cache) => {
        cache.put(event.request, response.clone());
        return response;
      });
    }).catch((error) => {
      return caches.open(CACHE_NAME).then((cache) => {
        return cache.match(event.request).then((response) => {
          if (response != null) {
            return response;
          }
          throw error;
        });
      });
    })
  );
}
