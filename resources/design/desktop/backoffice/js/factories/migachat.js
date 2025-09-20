

App.factory('Migachat', function($http, Url) {



    var factory = {};



    factory.loadLicense = function() {

        return $http({

            method: 'GET',

            url: Url.get("migachat/public_license/validate"),

            cache: true,

            responseType:'json'

        });

    };



    factory.saveLicense = function(license) {

        return $http({

            method: 'POST',

            data: license,

            url: Url.get("migachat/public_license/save"),

            cache: false,

            responseType:'json'

        });

    };

	

	factory.loadHelp = function() {

        return $http({

            method: 'GET',

            url: Url.get("migachat/backoffice_migachat/help"),

            cache: true,

            responseType:'json'

        });

    };



    factory.saveHelp = function(help) {

        return $http({

            method: 'POST',

            data: help,

            url: Url.get("migachat/backoffice_migachat/savehelp"),

            cache: false,

            responseType:'json'

        });

    };



    factory.loadAppLicenses = function() {

        return $http({

            method: 'GET',

            url: Url.get("migachat/public_license/applicenses"),

            cache: true,

            responseType:'json'

        });

    };
    
    factory.cronInfo = function() {

        return $http({

            method: 'GET',

            url: Url.get("migachat/public_cron/croninfo"),

            cache: true,

            responseType:'json'

        });

    };
    factory.loadHistoryLimit = function () {
        return $http({
          method: "GET",
    
          url: Url.get("migachat/backoffice_migachat/loadhistorylimit"),
    
          cache: true,
    
          responseType: "json",
        });
      };
    
      factory.saveHistoryLimit = function (history) {
        return $http({
          method: "POST",
    
          data: history,
    
          url: Url.get("migachat/backoffice_migachat/savehistorylimit"),
    
          cache: false,
    
          responseType: "json",
        });
      };



      factory.loadBlacklistedNumbers = function () {
        return $http({
          method: "GET",
    
          url: Url.get("migachat/backoffice_migachat/loadblacklistednumbers"),
    
          cache: true,
    
          responseType: "json",
        });
      };
    
      factory.saveBlacklistedNumbers = function (numbers) {
        return $http({
          method: "POST",
    
          data: numbers,
    
          url: Url.get("migachat/backoffice_migachat/saveblacklistednumbers"),
    
          cache: false,
    
          responseType: "json",
        });
      };


      factory.loadGPTModels = function () {
        return $http({
          method: "GET",
    
          url: Url.get("migachat/backoffice_migachat/loadgptmodels"),
    
          cache: true,
    
          responseType: "json",
        });
      };
      
      factory.saveGPTModelsTokens = function (tokens) {
        return $http({
          method: "POST",
    
          data: tokens,
    
          url: Url.get("migachat/backoffice_migachat/savegptmodelstokens"),
    
          cache: false,
    
          responseType: "json",
        });
      };
    return factory;

	

});