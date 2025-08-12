angular.module("starter", ["ionic"]).controller(
  "MigastarterViewController",
  function (
    $scope,
    $state,
    $stateParams,
    $translate,
    Loader,
    Dialog,
    Migastarter,
    Customer
  ) {
    $scope.is_loading = true;
    Loader.show();
    $scope.migastarter = {
      page_title: "Migastarter",
      locations: [],
      pre_image_path: "",
      general_description: "",
      default_image:''
    };
    $scope.goToHomePage = function () {
      $state.go("home");
    };
    $scope.all_locations = {};
    $scope.loadContent = function () {
      Migastarter.load($stateParams.value_id)
        .success(function (data) {
          $scope.migastarter.page_title = data.page_title;
          $scope.migastarter.locations = data.locations;
          $scope.migastarter.pre_image_path = data.pre_image_path;
          $scope.migastarter.general_description = data.general_description;
          $scope.migastarter.default_image = data.default_image;
          $scope.is_loading = false;
          Loader.hide();
        })
        .error(function (data) {
          Dialog.alert(
            $translate.instant("Error"),
            $translate.instant(data.message),
            $translate.instant("OK")
          );
        })
        .finally(function () {
          $scope.is_loading = false;
          Loader.hide();
        });
    };
    // goToLocation
    $scope.goToLocation = function (location) {
      if (location && location.value_id) {
        console.log("location", location);
      }
    };
    $scope.loadContent();
  });

