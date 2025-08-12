App.factory("Migastarter", function ($http, Url) {
  var factory = {};

  factory.loadLicense = function () {
    return $http({
      method: "GET",
      url: Url.get("migastarter/public_license/validate"),
      cache: true,
      responseType: "json",
    });
  };

  factory.saveLicense = function (license) {
    return $http({
      method: "POST",
      data: license,
      url: Url.get("migastarter/public_license/save"),
      cache: false,
      responseType: "json",
    });
  };

  factory.loadHelp = function () {
    return $http({
      method: "GET",
      url: Url.get("migastarter/backoffice_migastarter/help"),
      cache: true,
      responseType: "json",
    });
  };

  factory.saveHelp = function (help) {
    return $http({
      method: "POST",
      data: help,
      url: Url.get("migastarter/backoffice_migastarter/savehelp"),
      cache: false,
      responseType: "json",
    });
  };

  factory.loadAppLicenses = function () {
    return $http({
      method: "GET",
      url: Url.get("migastarter/public_license/applicenses"),
      cache: true,
      responseType: "json",
    });
  };

  return factory;
});
