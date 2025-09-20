App.config(function ($routeProvider) {
  $routeProvider.when(BASE_URL + "/migachat/backoffice_migachat", {
    controller: "MigachatController",

    templateUrl: BASE_URL + "/migachat/backoffice_migachat/template",
  });
}).controller("MigachatController", function ($scope, Header, Label, Migachat) {
  $scope.header = new Header();
  $scope.header.button.left.is_visible = false;

  $scope.form_loader_is_visible = false;

  $scope.license = null;

  $scope.help = null;
  $scope.history = {};
  $scope.history.history_duration = 3;
  $scope.blacklisted = {};
  $scope.blacklisted.blacklisted_numbers = '';
  $scope.loadLicense = function () {
    Migachat.loadLicense().success(function (data) {
      $scope.header.title = data.title;

      $scope.header.icon = data.icon;

      $scope.license = data;
    });
  };

  $scope.saveLicense = function () {
    $scope.form_loader_is_visible = true;
    console.log($scope.license);
    Migachat.saveLicense($scope.license)
      .success(function (data) {
        var message = Label.save.error;

        if (angular.isObject(data) && angular.isDefined(data.message)) {
          message = data.message;

          $scope.message.isError(false);
        } else {
          $scope.message.isError(true);
        }

        $scope.message
          .setText(message)

          .show();

        window.location.href = window.location.href;
      })
      .error(function (data) {
        var message = Label.save.error;

        if (angular.isObject(data) && angular.isDefined(data.message)) {
          message = data.message;
        }

        $scope.message
          .setText(message)

          .isError(true)

          .show();
      })
      .finally(function () {
        $scope.form_loader_is_visible = false;
      });
  };

  $scope.loadHelp = function () {
    Migachat.loadHelp().success(function (data) {
      $scope.help = data;
    });
  };

  $scope.saveHelp = function () {
    $scope.form_loader_help_is_visible = true;

    console.log($scope.help);

    Migachat.saveHelp($scope.help)
      .success(function (data) {
        var message = Label.save.error;

        if (angular.isObject(data) && angular.isDefined(data.message)) {
          message = data.message;

          $scope.message.isError(false);
        } else {
          $scope.message.isError(true);
        }

        $scope.message
          .setText(message)

          .show();

        window.location.href = window.location.href;
      })
      .error(function (data) {
        var message = Label.save.error;

        if (angular.isObject(data) && angular.isDefined(data.message)) {
          message = data.message;
        }

        $scope.message
          .setText(message)

          .isError(true)

          .show();
      })
      .finally(function () {
        $scope.form_loader_help_is_visible = false;
      });
  };

  $scope.loadAppLicenses = function () {
    Migachat.loadAppLicenses().success(function (data) {
      $scope.app_licenses = data.app_licenses;
    });
  };

    $scope.loadCronInfo = function() {

      Migachat.cronInfo().success(function (data) {

          $scope.cron = data;

      });

  };
  $scope.loadHistoryLimit = function () {
    Migachat.loadHistoryLimit().success(function (data) {
      $scope.history = data;
      console.log(data);
    });
  };
  $scope.saveHistoryLimit = function () {
    console.log($scope.history);
    $scope.form_loader_history_limit_is_visible = true;
    Migachat.saveHistoryLimit($scope.history)
      .success(function (data) {
        console.log(data);
        var message = Label.save.error;
        if (angular.isObject(data) && angular.isDefined(data.message)) {
          message = data.message;
          $scope.message.isError(false);
        } else {
          $scope.message.isError(true);
        }
        $scope.message.setText(message).show();
        window.location.href = window.location.href;
      })
      .error(function (data) {
        var message = Label.save.error;
        if (angular.isObject(data) && angular.isDefined(data.message)) {
          message = data.message;
        }
        $scope.message.setText(message).isError(true).show();
      })
      .finally(function () {
        $scope.form_loader_history_limit_is_visible = false;
      });
  };


  $scope.loadBlacklistedNumbers = function () {
    Migachat.loadBlacklistedNumbers().success(function (data) {
      $scope.blacklisted = data;
      console.log(data);
    });
  };
  $scope.saveBlacklistedNumbers = function () {
    console.log($scope.blacklisted);
    $scope.form_loader_blacklisted_numbers_is_visible = true;
    Migachat.saveBlacklistedNumbers($scope.blacklisted)
      .success(function (data) {
        console.log(data);
        var message = Label.save.error;
        if (angular.isObject(data) && angular.isDefined(data.message)) {
          message = data.message;
          $scope.message.isError(false);
        } else {
          $scope.message.isError(true);
        }
        $scope.message.setText(message).show();
        window.location.href = window.location.href;
      })
      .error(function (data) {
        var message = Label.save.error;
        if (angular.isObject(data) && angular.isDefined(data.message)) {
          message = data.message;
        }
        $scope.message.setText(message).isError(true).show();
      })
      .finally(function () {
        $scope.form_loader_blacklisted_numbers_is_visible = false;
      });
  };


  $scope.model_limit = {};
  $scope.loadGPTModels = function () {
    Migachat.loadGPTModels().success(function (data) {
      $scope.gpt_models = data.gpt_models;
      $scope.model_limit = data.model_limit;
      console.log(data);
    });
  };
  $scope.saveGPTModelsTokens = function () {
    console.log($scope.model_limit);
    Migachat.saveGPTModelsTokens($scope.model_limit)
      .success(function (data) {
        console.log(data);
        var message = Label.save.error;
        if (angular.isObject(data) && angular.isDefined(data.message)) {
          message = data.message;
          $scope.message.isError(false);
        } else {
          $scope.message.isError(true);
        }
        $scope.message.setText(message).show();
        window.location.href = window.location.href;
      })
      .error(function (data) {
        var message = Label.save.error;
        if (angular.isObject(data) && angular.isDefined(data.message)) {
          message = data.message;
        }
        $scope.message.setText(message).isError(true).show();
      })
      .finally(function () {
        $scope.form_loader_history_limit_is_visible = false;
      });
  };
  



  $scope.loadLicense();
  $scope.loadBlacklistedNumbers();
  $scope.loadHelp();
  $scope.loadHistoryLimit();
  $scope.loadAppLicenses();
  $scope.loadCronInfo();
  $scope.loadGPTModels();
});
