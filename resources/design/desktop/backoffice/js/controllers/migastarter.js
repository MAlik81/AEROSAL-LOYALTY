App.config(function ($routeProvider) {
  $routeProvider.when(BASE_URL + "/migastarter/backoffice_migastarter", {
    controller: "MigastarterController",
    templateUrl: BASE_URL + "/migastarter/backoffice_migastarter/template",
  });
}).controller(
  "MigastarterController",
  function ($scope, Header, Label, Migastarter) {
    $scope.header = new Header();
    $scope.header.button.left.is_visible = false;
    $scope.form_loader_is_visible = false;

    $scope.license = null;
    $scope.help = null;

    $scope.loadLicense = function () {
      Migastarter.loadLicense().success(function (data) {
        $scope.header.title = data.title;
        $scope.header.icon = data.icon;
        $scope.license = data;
      });
    };

    $scope.saveLicense = function () {
      $scope.form_loader_is_visible = true;

      Migastarter.saveLicense($scope.license)
        .success(function (data) {
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
          $scope.form_loader_is_visible = false;
        });
    };

    $scope.loadHelp = function () {
      Migastarter.loadHelp().success(function (data) {
        $scope.help = data;
      });
    };

    $scope.saveHelp = function () {
      $scope.form_loader_help_is_visible = true;

      Migastarter.saveHelp($scope.help)
        .success(function (data) {
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
          $scope.form_loader_help_is_visible = false;
        });
    };

    $scope.loadAppLicenses = function () {
      Migastarter.loadAppLicenses().success(function (data) {
        $scope.app_licenses = data.app_licenses;
      });
    };

    $scope.loadLicense();
    $scope.loadHelp();
    $scope.loadAppLicenses();
  }
);
