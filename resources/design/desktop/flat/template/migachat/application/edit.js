/*global
    $, bindForms, search_placeholder
 */
  value_id = $("#value_id").val();
// Function to format the date as "yyyy-mm-dd"
function formatDate(date) {
  var year = date.getFullYear();
  var month = (date.getMonth() + 1).toString().padStart(2, "0");
  var day = date.getDate().toString().padStart(2, "0");
  return year + "-" + month + "-" + day;
}
var k8 = ["gpt-4", "gpt-4-0613", "gpt-4-0314", "code-davinci-002"];
var k16 = ["gpt-3.5-turbo-16k", "gpt-3.5-turbo-16k-0613"];
var k32 = ["gpt-4-32k", "gpt-4-32k-0613", "gpt-4-32k-0314", "code-davinci-002"];
var k128 = ['gpt-4o-mini', 'gpt-4-vision-preview','chatgpt-4o-latest','gpt-4o-mini-2024-07-18','gpt-4o-2024-08-06','gpt-4o-2024-05-13','gpt-4o'];




function tokenStats(customer_id, fromDate, toDate) {
  $.ajax({
    url: "/migachat/application/gettokensstats",
    type: "post",
    data: {
      value_id: value_id,
      from_date: fromDate,
      to_date: toDate,
      customer_id: customer_id
    },
    dataType: "json"
  })
    .done(function (response) {
      $("#prompt_tokens").html(response.chatlogs.total_prompt_tokens);
      $("#completion_tokens").html(response.chatlogs.total_completion_tokens);
      $("#total_tokens").html(response.chatlogs.total_total_tokens);
    })
    .fail(function (response) {
      // new AlertMessage(response.message, true)
      // .isError(true)
      // .show();
    })
    .always(function (response) {
      // $("#mask").hide();
    });
}
$(document).ready(function () {

  // use_assistant-input
  $("#use_assistant-input").on("change", function () {
      var value = $(this).val();
      if (value === "1") {
        $("#chat-gpt-inputs-2").hide();
      }else{
        $("#chat-gpt-inputs-2").show();
      }
  });

  $('#api_chat_csv_form').on('submit', function (e) {
      e.preventDefault(); // Prevent the default form submission

      // Create a FormData object to handle the file and form data
      let formData = new FormData(this);

      $.ajax({
          url: $(this).attr('action'), // Use the form's action attribute
          type: 'POST',
          data: formData,
          processData: false, // Prevent jQuery from processing the data
          contentType: false, // Prevent jQuery from setting the content type
          beforeSend: function () {
              $('#import_csv_api_chat').prop('disabled', true).text('Importing...');
          },
          success: function (response) {
            console.log(response);
            response = JSON.parse(response);
            console.log(response.success);
            console.log(response.message);
              if (response.success) {
                new AlertMessage(response.message, true).isError(false).show();

                setTimeout(function() {
                  page.reload();
                }, 3000);
              } else {
                new AlertMessage(response.message, true).isError(true).show();
              }
          },
          error: function (xhr, status, error) {
              new AlertMessage(error, true).isError(true).show();
          },
          complete: function () {
              $('#import_csv_api_chat').prop('disabled', false).text('Import CSV');
          }
      });
  });
});

$(document).ready(function () {
  



  $("#api_config_form, #openai_config,#bridge_api_form, #models_token_form,#gdpr_settings_form,#operator_settings_form").submit(function () {
    reload(this, this.action, true, function (data) {
      console.log(data);
      page.reload();
    });
    return false;
  });
  $("#prompt_setting_form").submit(function () {
    reload(this, this.action, true, function (data) {
      console.log(data);

      $("#app_delete_all_chat_history_update_prompt").fadeIn();
    });
    return false;
  });
  $("#close-app_delete_all_chat_history_update_prompt,#app_delete_all_chat_history_update_prompt_no").click(function () {
    $("#app_delete_all_chat_history_update_prompt").fadeOut();
    page.reload();
  });
  $("#api-type-input").trigger("change");
  console.log($("#api-type-input").val());
  if ($("#api-type-input").val() === "chatgpt") {
    $("#chat-gpt-inputs").show();
    $("#webhook-inputs").hide();
  } else if ($("#api-type-input").val() === "webhook") {
    $("#chat-gpt-inputs").hide();
    $("#webhook-inputs").show();
  }
  $("#api-type-input").on("change", function () {
    var selectedOption = $(this).val();
    console.log($(this).val());
    if (selectedOption === "chatgpt") {
      $("#chat-gpt-inputs").show();
      $("#webhook-inputs").hide();
    } else if (selectedOption === "webhook") {
      $("#chat-gpt-inputs").hide();
      $("#webhook-inputs").show();
    } else {
      $("#chat-gpt-inputs").hide();
      $("#webhook-inputs").hide();
    }
  });
});

// ************chat logs ********
$(document).ready(function () {
  value_id = $("#value_id").val();
  // Function to handle the AJAX request

  function formatDateTime(dateTime) {
    return moment(dateTime).format("h:mmA, D-M-YYYY");
  }
});

$(document).ready(function () {
  // generate-auth-token on the botton click and set inout value
  // create a fun in jquery to generate 20 length alphanumeric code generation

  $("#generate-auth-token").click(function (e) {
    e.preventDefault();
    var chars =
      "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    var string_length = 20;
    var randomstring = "";
    for (var i = 0; i < string_length; i++) {
      var rnum = Math.floor(Math.random() * chars.length);
      randomstring += chars.substring(rnum, rnum + 1);
    }
    $("#auth-token").val(randomstring);
  });

  $("#generate-auth-token_bridge").click(function (e) {
    e.preventDefault();
    var chars =
      "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    var string_length = 20;
    var randomstring = "";
    for (var i = 0; i < string_length; i++) {
      var rnum = Math.floor(Math.random() * chars.length);
      randomstring += chars.substring(rnum, rnum + 1);
    }
    $.ajax({
      url: "/migachat/bridgeapi/saveauthtoken",
      type: "post",
      data: {
        value_id: value_id,
        auth_token: randomstring
      },
      dataType: "json"
    })
      .done(function (response) {
        console.log(response.message);

        new AlertMessage(response.message, true).isError(false).show();
        $("#mask").hide();
      })
      .fail(function (response) {
        new AlertMessage(response.message, true).isError(true).show();
      })
      .always(function (response) {
        $("#mask").hide();
      });
    $("#auth-token_bridge").val(randomstring);
  });

  $("#close-app_delete_all_chat_history,#app_delete_all_chat_history_complete").click(function () {
    $("#app_delete_all_chat_history").fadeOut();
  });

  $("#delete_app_chat_archive").click(function (e) {
    $("#app_delete_all_chat_history").fadeIn();
  });

  $("#app_delete_all_chat_history_complete").click(function (e) {
    e.preventDefault();
    $("#app_delete_all_chat_history").fadeOut();
  });
  $("#app_delete_all_chat_history_complete").click(function (e) {
    e.preventDefault();
    $("#app_delete_all_chat_history").fadeOut();
  });

  $("#close-app_delete_single_chat_history").click(function () {
    $("#app_delete_single_chat_history").fadeOut();
  });

  $(".delete_chat_history").click(function (e) {
    $("#app_delete_single_chat_history").fadeIn();
    // e.preventDefault();
    // customer_id = $("#app_chat_archive_filter").val();
    // console.log(customer_id);
    // if (confirm("Are you sure you want to delete")) {
    //   $.ajax({
    //     url: "/migachat/application/deletectchathistory",
    //     type: "post",
    //     data: {
    //       value_id: value_id,
    //       customer_id: customer_id
    //     },
    //     dataType: "json"
    //   })
    //     .done(function(response) {
    //       console.log(response.message);

    //       new AlertMessage(response.message, true).isError(false).show();

    //       page.reload();
    //       $("#mask").hide();
    //     })
    //     .fail(function(response) {
    //       new AlertMessage(response.message, true).isError(true).show();
    //     })
    //     .always(function(response) {
    //       $("#mask").hide();
    //     });
    // }
  });
});
function deleteChatAfterPromptUpdate() {
  $.ajax({
    url: "/migachat/application/deletectchathistory",
    type: "post",
    data: {
      value_id: value_id,
      data_type: 1
    },
    dataType: "json"
  })
    .done(function (response) {
      console.log(response.message);

      new AlertMessage(response.message, true).isError(false).show();

      page.reload();
      $("#mask").hide();
    })
    .fail(function (response) {
      new AlertMessage(response.message, true).isError(true).show();
    })
    .always(function (response) {
      $("#mask").hide();
    });

  $.ajax({
    url: "/migachat/bridgeapi/deleteinstancechathistory",
    type: "post",
    data: {
      value_id: value_id,
      delete_type: 2
    },
    dataType: "json"
  })
    .done(function (response) {
      console.log(response.message);

      new AlertMessage(response.message, true).isError(false).show();

      page.reload();
      $("#mask").hide();
    })
    .fail(function (response) {
      new AlertMessage(response.message, true).isError(true).show();
    })
    .always(function (response) {
      $("#mask").hide();
    });
  $("#app_delete_all_chat_history_update_prompt").fadeOut();
}
$("#app_delete_all_chat_history_update_prompt_no").click(function (e) {
  $("#app_delete_all_chat_history_update_prompt").fadeOut();
});
function deleteAppChat(data_type, delete_type) {
  if (delete_type == 2) {
    if (data_type == 1) {
      $.ajax({
        url: "/migachat/application/deletectchathistory",
        type: "post",
        data: {
          value_id: value_id,
          data_type: data_type
        },
        dataType: "json"
      })
        .done(function (response) {
          new AlertMessage(response.message, true).isError(false).show();

          page.reload();
          $("#mask").hide();
        })
        .fail(function (response) {
          new AlertMessage(response.message, true).isError(true).show();
        })
        .always(function (response) {
          $("#mask").hide();
        });
    } else {
      customer_id = $("#app_chat_archive_filter").val();
      $.ajax({
        url: "/migachat/application/deletectchathistory",
        type: "post",
        data: {
          value_id: value_id,
          customer_id: customer_id,
          data_type: data_type
        },
        dataType: "json"
      })
        .done(function (response) {

          new AlertMessage(response.message, true).isError(false).show();

          page.reload();
          $("#mask").hide();
        })
        .fail(function (response) {
          new AlertMessage(response.message, true).isError(true).show();
        })
        .always(function (response) {
          $("#mask").hide();
        });
    }
  }
  $("#app_delete_single_chat_history").fadeOut();
  $("#app_delete_all_chat_history").fadeOut();
}

$(document).ready(function () {
  setTimeout(() => {
    var customerId = $("#chat_log_customer_filter").val();
    var dateFrom = $("#chat_log_datefrom_filter").val();
    var dateTo = $("#chat_log_dateto_filter").val();
    if (!dateFrom && !dateTo) {
      // Set the date range to a month range
      var today = new Date();
      var firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
      var lastDayOfMonth = new Date(
        today.getFullYear(),
        today.getMonth() + 1,
        0
      );

      // Format the dates as strings in the "yyyy-mm-dd" format
      var fromDate = formatDate(firstDayOfMonth);
      var toDate = formatDate(lastDayOfMonth);
      $("#chat_log_datefrom_filter").val(fromDate);
      $("#chat_log_dateto_filter").val(toDate);

      tokenStats(customerId, fromDate, toDate);
    } else {
      tokenStats(customerId, dateFrom, dateTo);
    }
  }, 5000);
});

$(document).ready(function () {
  $("#gpt_model-input").change(function () {
    var selectedOption = $(this).find("option:selected");
    if ($.inArray($(this).val(), k8) !== -1) {
      $("#system_prompt_limit").val(2000);
      $("#total_prompt_limit").val(4000);
      $("#system_prompt_limit").attr("max", 2000);
      $("#total_prompt_limit").attr("max", 4000);

    } else if ($.inArray($(this).val(), k16) !== -1) {
      $("#system_prompt_limit").val(4000);
      $("#total_prompt_limit").val(8000);
      $("#system_prompt_limit").attr("max", 4000);
      $("#total_prompt_limit").attr("max", 8000);
    } else if ($.inArray($(this).val(), k32) !== -1) {
      $("#system_prompt_limit").val(8000);
      $("#total_prompt_limit").val(16000);
      $("#system_prompt_limit").attr("max", 8000);
      $("#total_prompt_limit").attr("max", 16000);
    }else if ($.inArray($(this).val(), k128) !== -1) {
      $("#system_prompt_limit").val(32000);
      $("#total_prompt_limit").val(64000);
      $("#system_prompt_limit").attr("max", 32000);
      $("#total_prompt_limit").attr("max", 64000);
    }else {
      $("#system_prompt_limit").val(1500);
      $("#total_prompt_limit").val(3000);
      $("#system_prompt_limit").attr("max", 1500);
      $("#total_prompt_limit").attr("max", 3000);
    }
  });
  $("#gpt_model-input").change(function (e) {
    e.preventDefault();
  });
  $("#system_prompt_limit").change(function (e) {
    e.preventDefault();
    $("#token_limit").val($(this).val());
    // $("#text_area_limit").text($(this).val());
  });
  $("#total_prompt_limit").change(function (e) {
    e.preventDefault();
    val_tot = $("#total_prompt_limit").val();
    var max = val_tot / 2;
    $("#system_prompt_limit").attr("max", max);
  });
});
$(document).ready(function () {
  $("#download_app_chat_stats_csv").click(function (e) {
    e.preventDefault();
    var date_from = $("#app-chat-date-from").val();
    var date_to = $("#app-chat-date-to").val();
    $("#mask").show();
    $.ajax({
      url: "/migachat/application/getappchatstatscsvdownload",
      type: "post",
      data: {
        value_id: value_id,
        date_from: date_from,
        date_to: date_to
      },
      dataType: "json"
    })
      .done(function (response) {
        // Convert the JSON response into CSV content
        var csvContent =
          '"Customer Id","Customer Name","email","mobile","Role","Message","Message Date","Tokens Used"' +
          "\n";

        for (var i = 0; i < response.data.length; i++) {
          var row = response.data[i];
          var role = "Agent";
          var tokens = row["completion_tokens"];
          if (row["message_sent_received"] == "sent") {
            role = "User";
            tokens = row["prompt_tokens"];
          }
          var message_content = row["message_content"];
          // Escape double quotes within the string and enclose the string in double quotes
          message_content = message_content.replace(/,/g, " ");
          message_content = message_content.replace(/"/g, " ");

          console.log(message_content);
          csvContent +=
            '"' +
            row["customer_id"] +
            '","' +
            row["firstname"] +
            " " +
            row["lastname"] +
            '","' +
            row["email"] +
            '","' +
            row["mobile"] +
            '","' +
            role +
            '","' +
            message_content +
            '","' +
            row["created_at"] +
            '","' +
            tokens +
            '"';

          if (i < response.data.length - 1) {
            csvContent += "\n";
          }
        }
        // Create a Blob containing the CSV data
        var blob = new Blob([csvContent], { type: "text/csv" });

        // Create a temporary URL for the Blob
        var url = window.URL.createObjectURL(blob);

        // Create a download link and trigger the click event
        var a = document.createElement("a");
        a.href = url;
        a.download = "customer_chat_logs.csv";
        document.body.appendChild(a);
        a.click();

        // Clean up
        window.URL.revokeObjectURL(url);
        $("#mask").hide();
      })
      .fail(function (response) {
        new AlertMessage(response.message, true).isError(true).show();
        $("#mask").hide();
      })
      .always(function (response) {
        $("#mask").hide();
      });
  });

  $("#filter_app_chat_stats").click(function (e) {
    e.preventDefault();
    var date_from = $("#app-chat-date-from").val();
    var date_to = $("#app-chat-date-to").val();
    $("#mask").show();
    $.ajax({
      url: "/migachat/application/getappchatstats",
      type: "post",
      data: {
        value_id: value_id,
        date_from: date_from,
        date_to: date_to
      },
      dataType: "json"
    })
      .done(function (response) {
        console.log(response);
        $("#app-chat-total-chats").val(response.data.unique_customer_count);
        $("#app-chat-prompt-tokens").val(
          response.data.chat_stats.total_prompt_tokens
        );
        $("#app-chat-completion-tokens").val(
          response.data.chat_stats.total_completion_tokens
        );
        $("#mask").hide();
      })
      .fail(function (response) {
        new AlertMessage(response.message, true).isError(true).show();
      })
      .always(function (response) {
        $("#mask").hide();
      });
  });

  $("#app_chat_archive_search").click(function (e) {
    $("#app_chat_archive_user_name").val("");
    $("#app_chat_archive_user_email").val("");
    $("#app_chat_archive_user_mobile").val("");
    $("#app_chat_archive_table_body").html("");
    e.preventDefault();
    var customer_id = $("#app_chat_archive_filter").val();
    var date_from = $("#app_chat_archive_datefrom").val();
    var date_to = $("#app_chat_archive_dateto").val();
    $("#mask").show();
    $.ajax({
      url: "/migachat/application/getchatlogs",
      type: "post",
      data: {
        value_id: value_id,
        customer_id: customer_id,
        date_from: date_from,
        date_to: date_to
      },
      dataType: "json"
    })
      .done(function (response) {
        $("#app_chat_archive_user_name").html(response.customer_name);
        $("#app_chat_archive_user_email").html(response.email);
        $("#app_chat_archive_user_mobile").html(response.mobile);

        var table_rows = "";
        for (var i = 0; i < response.chatlogs.length; i++) {
          var row = response.chatlogs[i];
          table_rows += "<tr>";
          table_rows += "<td>" + row["date_time"] + "</td>";
          table_rows += "<td>" + row["sender"] + "</td>";
          table_rows += "<td>" + row["text"] + "</td>";
          table_rows += "<td>" + row["tokens"] + "</td>";
          table_rows += "</tr>";
        }
        $("#app_chat_archive_table_body").html(table_rows);
        $("#mask").hide();
      })
      .fail(function (response) {
        $("#mask").hide();
        new AlertMessage(response.message, true).isError(true).show();
      })
      .always(function (response) {
        $("#mask").hide();
      });
  });
  // ...

  $("#app_chat_archive_table_csv").click(function (e) {
    e.preventDefault();
    var customer_id = $("#app_chat_archive_filter").val();
    var date_from = $("#app_chat_archive_datefrom").val();
    var date_to = $("#app_chat_archive_dateto").val();
    $.ajax({
      url: "/migachat/application/downloadcsv",
      method: "POST",
      data: {
        value_id: value_id,
        customer_id: customer_id,
        date_from: date_from,
        date_to: date_to
      },
      dataType: "json",
      success: function (data) {

        // Convert the JSON response into CSV content
        var csvContent = "";

        for (var i = 0; i < data.length; i++) {
          var row = data[i];
          csvContent +=
            '"' +
            row[0] +
            '","' +
            row[1] +
            '","' +
            row[2] +
            '","' +
            row[3] +
            '","' +
            row[4] +
            '","' +
            row[5] +
            '","' +
            row[6] +
            '"';

          if (i < data.length - 1) {
            csvContent += "\n";
          }
        }

        // Create a Blob containing the CSV data
        var blob = new Blob([csvContent], { type: "text/csv" });

        // Create a temporary URL for the Blob
        var url = window.URL.createObjectURL(blob);

        // Create a download link and trigger the click event
        var a = document.createElement("a");
        a.href = url;
        a.download = "customer_chat_logs.csv";
        document.body.appendChild(a);
        a.click();

        // Clean up
        window.URL.revokeObjectURL(url);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.error("Error loading user chat data:", errorThrown);
      }
    });
  });

  $("#optimize_system_prompt").click(function (e) {
    e.preventDefault();
    var system_prompt = $("#system_prompt").val();
    $("#mask").show();
    $.ajax({
      url: "/migachat/application/optimzeprompt",
      type: "post",
      data: {
        value_id: value_id,
        system_prompt: system_prompt
      },
      dataType: "json"
    })
      .done(function (response) {
        $("#system-prompt-popup").fadeIn();
        $("#system-prompt-message").val(response.data);
        $("#system-prompt-tokens").val(response.tokens);

        $("#mask").hide();
      })
      .fail(function (response) {
        $("#mask").hide();
        new AlertMessage(response.message, true).isError(true).show();
      })
      .always(function (response) {
        $("#mask").hide();
      });
  });

  $("#translate_system_prompt").click(function (e) {
    e.preventDefault();
    var system_prompt = $("#system_prompt").val();
    $("#mask").show();
    $.ajax({
      url: "/migachat/application/translateprompt",
      type: "post",
      data: {
        value_id: value_id,
        system_prompt: system_prompt
      },
      dataType: "json"
    })
      .done(function (response) {
        $("#system-prompt-popup").fadeIn();
        $("#system-prompt-message").val(response.data);
        $("#system-prompt-tokens").val(response.tokens);

        $("#mask").hide();
      })
      .fail(function (response) {
        $("#mask").hide();
        new AlertMessage(response.message, true).isError(true).show();
      })
      .always(function (response) {
        $("#mask").hide();
      });
  });
});

$(document).ready(function () {
  $("#system-prompt-open-popup").click(function () {
    $("#system-prompt-popup").fadeIn();
  });

  $("#close-system-prompt-popup").click(function () {
    $("#system-prompt-popup").fadeOut();
  });

  $("#system-prompt-confirm").click(function () {
    var message = $("#system-prompt-message").val();
    var tokens = $("#system-prompt-tokens").val();
    $("#mask").show();
    $.ajax({
      url: "/migachat/application/savesystempromptoptimzed",
      type: "post",
      data: {
        value_id: value_id,
        system_prompt: message,
        system_prompt_tokens: tokens
      },
      dataType: "json"
    })
      .done(function (response) {
        new AlertMessage(response.message, true).isError(false).show();
        page.reload();
        $("#mask").hide();
      })
      .fail(function (response) {
        new AlertMessage(response.message, true).isError(true).show();
      })
      .always(function (response) {
        $("#mask").hide();
      });
    $("#system-prompt-popup").fadeOut();
  });

  $("#system-prompt-decline").click(function () {
    $("#system-prompt-popup").fadeOut();
  });
});


$("#close-log_details-popup").click(function () {
  $("#log_details-popup").fadeOut();
});

function getChatLog(log_id) {
  $("#mask").show();
  $.ajax({
    url: "/migachat/application/getlogdetails",
    type: "post",
    data: {
      value_id: value_id,
      log_id: log_id
    },
    dataType: "json"
  })
    .done(function (response) {
      $("#log_details-popup").fadeIn();
      $(".log_details-popup-textarea").html(response.prepaired_string);
      $("#mask").hide();
    })
    .fail(function (response) {
    })
    .always(function (response) {
      $("#mask").hide();
    });
}

$('.accepted_request').click(function () {
  var rerquest_id = $(this).data('migachat_operator_request_id');
  $("#mask").show();
  $.ajax({
    url: "/migachat/application/acceptedrequest",
    type: "post",
    data: {
      value_id: value_id,
      rerquest_id: rerquest_id
    },
    dataType: "json"
  })
    .done(function (response) {
      new AlertMessage(response.message, true).isError(false).show();
      page.reload();
      $("#mask").hide();
    })
    .fail(function (response) {
      new AlertMessage(response.message, true).isError(true).show();
      page.reload();
    })
    .always(function (response) {
      $("#mask").hide();
    });
})


$("#test_webhook").click(function (e) { 
  e.preventDefault();
  webhook_url_operator = $('#webhook_url_operator').val();
  if (webhook_url_operator) {
    $("#mask").show();
  $.ajax({
    url: "/migachat/application/checkwebhook",
    type: "post",
    data: {
      value_id: value_id,
      webhook_url_operator: webhook_url_operator
    },
    dataType: "json"
  })
    .done(function (response) {
      new AlertMessage(response.message, true).isError(false).show();
      // page.reload();
      $("#mask").hide();
    })
    .fail(function (response) {
      new AlertMessage(response.message, true).isError(true).show();
      // page.reload();
      $("#mask").hide();
    })
    .always(function (response) {
      $("#mask").hide();
    });
  }
});
  