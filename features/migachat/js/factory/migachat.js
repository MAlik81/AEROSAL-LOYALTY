angular.module("starter").factory("Migachat", function ($pwaRequest, $http, Url) {
  var factory = {
    find: function (value_id, customer_id, startIndex, limit) {
      if (!value_id) return;

      return $http({
        method: "GET",
        url: Url.get("migachat/mobile_view/find", {
          value_id: value_id,
          customer_id: customer_id,
          start_index: startIndex,
          limit: limit
        }),
        cache: false,
        responseType: "json",
      });
    },

    checkNewMessages: function (value_id, customer_id) {
      if (!value_id) return;

      return $http({
        method: "GET",
        url: Url.get("migachat/mobile_view/checknewmessages", {
          value_id: value_id,
          customer_id: customer_id,
        }),
        cache: false,
        responseType: "json",
      });
    },

    getBotReply: function (message, customer_id, value_id) {
      return value_id
        ? $pwaRequest.post("migachat/mobile_view/getbotreply", {
          urlParams: {
            value_id: value_id
          },
          data: {
            message: message,
            customer_id: customer_id,
          },
          cache: !1
        })
        : $pwaRequest.reject("[Factory::Migacouponpro.use] missing value_id");
    },
    updateMsgsStatus: function (value_id, customer_id) {
      if (!value_id) return;

      return $http({
        method: "GET",

        url: Url.get("migachat/mobile_view/updatemsgsstatus", {
          value_id: value_id,
          customer_id: customer_id,
        }),
        cache: false,
        responseType: "json",
      });
    },
    clearHistory: function (customer_id,value_id) {
      if (!value_id) return;

      return $http({
        method: "GET",

        url: Url.get("migachat/mobile_view/clearhistory", {
          value_id: value_id,
          customer_id: customer_id,
        }),
        cache: false,
        responseType: "json",
      });
    },
    loadTitlePlaceholder: function (value_id) {
      if (!value_id) return;

      return $http({
        method: "GET",
        url: Url.get("migachat/mobile_view/loadtitleplaceholder", {
          value_id: value_id,
        }),
        cache: false,
        responseType: "json",
      });
    },
  };

  
  return factory;
});
