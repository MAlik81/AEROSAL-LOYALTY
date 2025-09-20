angular
  .module("starter")
  .controller("MigachatController", function(
    $sce,
    $scope,
    $state,
    $stateParams,
    Customer,
    Dialog,
    Migachat,
    SB,
    Loader,
    $translate,
    $interval,
    $timeout,
    $ionicScrollDelegate
  ) {
    $scope.page_title = $translate.instant("Chat Bot", "Migachat");
    $scope.place_holder = $translate.instant("Type your message.", "Migachat");

    angular.extend($scope, {
      is_loading: false,
      value_id: $stateParams.value_id,
      is_logged_in: Customer.isLoggedIn(),
      dateFrom: new Date(Date.parse("2022-10-01")),
      dateTo: new Date()
    });
    $scope.isLoadingMessages = false;
    // calling login function
    $scope.login = function() {
      Customer.loginModal($scope);
      $state.go("home");
    }
    function scrollToBottom() {
      $ionicScrollDelegate.scrollBottom(false);
    }
    $scope.allowScroll = true;
    // Function to disable scrolling
    $scope.disableScroll = function() {
      $scope.allowScroll = false;
    };
    // Function to enable scrolling after a delay
    $scope.enableScroll = function() {
      $timeout(function() {
        $scope.allowScroll = true;
      }, 1000); // Adjust the delay as needed
    };
    $scope.message = {};
    $scope.message.input = "";
    $scope.messages = [];
    $scope.responce_o = {};
    $scope.responce_o.is_loading = false;

    $scope.loadNewMessages = function() {
      Migachat.checkNewMessages($scope.value_id, $scope.customer_id)
        .success(function(data) {
          var newMessages = data.data;
          if (newMessages.length > 0) {
            $scope.messages = $scope.messages.concat(newMessages);
            $scope.update_msgs_read_status();
            // Scroll to the bottom of the chat container after receiving new messages
            // $timeout(scrollToBottom, 0);
          }
        })
        .error(function onError(data) {
          Dialog.alert("Error", data.message, "OK", -1);
        });
    };

    $scope.startIndex = 0;
    $scope.limit = 50;

    $scope.loadTitlePlaceholder = function() {
      $scope.messages = [];
      Migachat.loadTitlePlaceholder(
        $scope.value_id
      )
        .success(function(data) {
          console.log(data);
          $scope.page_title = data.page_title;
          $scope.place_holder = data.place_holder;
        })
        .error(function onError(data) {
          Dialog.alert("Error", data.message, "OK", -1);
          reply = "";
        })
        .finally(function() {
          Loader.hide();
        });
    };
    $scope.loadTitlePlaceholder();
    $scope.logo2 = '';
    $scope.loadMessages = function() {
      $scope.messages = [];
      Migachat.find(
        $scope.value_id,
        $scope.customer_id,
        $scope.startIndex,
        $scope.limit
      )
        .success(function(data) {
          console.log(data);
          $scope.logo2 = data.logo2;
          $scope.messages = data.data;
          $scope.update_msgs_read_status();
          $timeout(scrollToBottom, 100);
        })
        .error(function onError(data) {
          Dialog.alert("Error", data.message, "OK", -1);
          reply = "";
        })
        .finally(function() {
          Loader.hide();
        });
    };

    $scope.loadMoreMessages = function() {
      // Check if already loading messages to prevent duplicate loading
      if ($scope.isLoadingMessages) {
        return;
      }

      $scope.isLoadingMessages = true;
      // Freeze the scroll to prevent automatic scrolling to the top

      $ionicScrollDelegate.freezeScroll(true);
      $scope.startIndex += $scope.limit;
      Migachat.find(
        $scope.value_id,
        $scope.customer_id,
        $scope.startIndex,
        $scope.limit
      )
        .success(function(data) {
          console.log(data);
          $scope.messages = data.data.concat($scope.messages);
          $scope.update_msgs_read_status();
        })
        .error(function onError(data) {
          Dialog.alert("Error", data.message, "OK", -1);
          reply = "";
        })
        .finally(function() {
          // After loading new messages, scroll back to the previous position
          $timeout($ionicScrollDelegate.freezeScroll(false), 1000);

          $scope.isLoadingMessages = false;
        });
    };

    $scope.handleScroll = function() {
      // Check if already loading messages to prevent calling handleScroll while loading
      if ($scope.isLoadingMessages) {
        return;
      }

      var scrollPosition = $ionicScrollDelegate.getScrollPosition();
      var scrollTop = scrollPosition.top;

      // Determine how close to the top of the chat container the user is (adjust as needed)
      var topThreshold = 10; // For example, consider the user at the top if within 10 pixels

      // Check if the user has scrolled close to the top
      if (scrollTop < topThreshold) {
        // Call the function to load more messages when the user is close to the top
        $scope.loadMoreMessages();
      }
    };
    $scope.update_msgs_read_status = function() {
      Migachat.updateMsgsStatus($scope.value_id, $scope.customer_id)
        .success(function(data) {
          console.log(data);
        })
        .error(function onError(data) {
          Dialog.alert("Error", data.message, "OK", -1);
          reply = "";
        })
        .finally(function() {
          Loader.hide();
        });
    };

    $scope.customer_id = null;
    if ($scope.is_logged_in) {
      $scope.customer_id = Customer.customer.id;
      console.log(Customer.customer.id);
      $scope.loadMessages();
      // Schedule the function to be called every 10 seconds
      $scope.intervalPromise = $interval($scope.loadNewMessages, 10000);
    } else {
      $scope.customer_id = null;

      Dialog.alert(
        $translate.instant("Restricted Area", "Migachat"),
        $translate.instant(
          "You must be logged in to use this feature.",
          "Migachat"
        ),
        "OK"
      );
      $scope.login();
    }

    $scope.$on(SB.EVENTS.AUTH.loginSuccess, function() {
      $scope.customer_id = Customer.customer.id;
      $scope.is_logged_in = true;
      $scope.loadMessages();

      // Schedule the function to be called every 10 seconds
      // $scope.intervalPromise = $interval($scope.loadNewMessages, 10000);
    });
    $scope.$on(SB.EVENTS.AUTH.logoutSuccess, function() {
      $scope.is_logged_in = false;
      $scope.customer_id = null;
      $scope.$on("$destroy", function() {
        $interval.cancel($scope.intervalPromise);
      });

      $scope.login();
    });

    $scope.getBotReply = function(message) {
      // Loader.show();

      var reply = "";
      Migachat.getBotReply(message, $scope.customer_id, $scope.value_id)
        .success(function(data) {
          // reply = $sce.trustAsHtml(data.data);
          reply = data.data;
          console.log(reply);
          $scope.responce_o.is_loading = false;

          if (data.type === "chatgpt") {
            if (reply) {
              $scope.messages.push({ content: reply, sender: "bot" });

              // Scroll to the bottom of the chat container after receiving a new message
              $timeout(scrollToBottom, 10);
            }
          }
        })
        .error(function onError(data) {
          $scope.responce_o.is_loading = false;
          Dialog.alert("Error", data.message, "OK", -1);
          reply = "";
        })
        .finally(function() {
          $scope.responce_o.is_loading = false;
          // Loader.hide();
        });
    };

    $scope.sendMessage = function() {
      $scope.userMessage = $scope.message.input;
      $scope.responce_o.is_loading = true;

      $scope.messages.push({ content: $scope.userMessage, sender: "user" });

      $scope.botReply = $scope.getBotReply($scope.userMessage);

      $scope.message.input = "";

      // Scroll to the bottom of the chat container after sending a message
      $timeout(scrollToBottom, 100);
    };

    $scope.clearHistory = function() {
      confirm_text = $translate.instant("Confirmation");
      confirm_body = $translate.instant(
        "Are you sure you want to clear the history?"
      );
      $translate.instant("Confirmation");
      Dialog.confirm(
        confirm_text,
        confirm_body,
        ["Yes", "No"],
        "text-center"
      ).then(function(result) {
        console.log(result);
        if (result) {
          Loader.show();
          Migachat.clearHistory($scope.customer_id, $scope.value_id)
            .success(function(data) {
              Dialog.alert("Success", data.message, "OK");
            })
            .error(function onError(data) {
              Dialog.alert("Error", data.message, "OK");
            })
            .finally(function() {
              $scope.responce_o.is_loading = false;
              Loader.hide();
            });
        }
      });
    };

    $scope.$on("$destroy", function() {
      $interval.cancel($scope.intervalPromise);
    });
    // ==========================
    // ng-click="copyTokenToClipboard()"
    $scope.copyToClipboard = function(content) {
      console.log(content);
      var modifiedContent = content.replace(/<br>/g, '\n');
      console.log(modifiedContent);
      try {
        if (SB.DEVICE.TYPE_BROWSER === DEVICE_TYPE) {
          if (navigator.clipboard) {
            navigator.clipboard
              .writeText(modifiedContent)
              .then(function() {
                Dialog.alert("Success", $translate.instant("Text copied to clipboard!", "Migachat"), "OK");
              })
              .catch(function(error) {
                console.error("Error copying Text to clipboard:", error);
              });
          } else {
            console.error(
              "Something went wrong while copying Text to clipboard!"
            );
          }
        } else {
          cordova.plugins.clipboard.copy(modifiedContent);
          window.plugins.toast.showShortCenter(
            $translate.instant("Text copied to clipboard!", "Migachat")
          );
        }
      } catch (e) {
        console.error("Something went wrong while copiyng Text to clipboard!");
      }
    };
  });
