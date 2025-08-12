App.factory('Migastarter', function ($http, Url, $pwaRequest, $q, $ocLazyLoad) {

  var factory = {
    value_id: null,
    image: null,
    module: null,
  };

  factory.load = function (value_id) {
    if (!value_id) return;
    return $http({
      method: "GET",
      url: Url.get("migastarter/mobile_view/load", {
        value_id: value_id,
      }),
      cache: false,
      responseType: "json",
    });
  };
  
  return factory;
});

