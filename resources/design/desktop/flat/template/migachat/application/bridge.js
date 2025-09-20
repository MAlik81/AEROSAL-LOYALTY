$("#bridge_custom_container").hide();

// ************chat logs ********
$(document).ready(function() {
  // Function to handle the AJAX request
  value_id = $("#value_id").val();

  function formatDateTime(dateTime) {
    return moment(dateTime).format("h:mmA, D-M-YYYY");
  }

  // Function to format the date as "yyyy-mm-dd"
  function formatDate(date) {
    var year = date.getFullYear();
    var month = (date.getMonth() + 1).toString().padStart(2, "0");
    var day = date.getDate().toString().padStart(2, "0");
    return year + "-" + month + "-" + day;
  }

  $("#close-api_delete_single_chat_history").click(function() {
    $("#api_delete_single_chat_history").fadeOut();
  });
  $("#delete_bridge_chat_history").click(function(e) {
    $("#api_delete_single_chat_history").fadeIn();
    // e.preventDefault();
    // customer_id = $("#api_chat_archive_filter").val();
    // if (confirm('This function will delete the current chat id '+customer_id+' history. Are you sure you want to proceed?')) {
    //   $.ajax({
    //     url: "/migachat/bridgeapi/deletectchathistory",
    //     type: "post",
    //     data: {
    //       value_id: value_id,
    //       customer_id: customer_id
    //     },
    //     dataType: "json",
    //   })
    //     .done(function(response) {
    //       console.log(response.message);

    //       new AlertMessage(response.message, true)
    //       .isError(false)
    //       .show();

    //       page.reload();
    //       $("#mask").hide();
    //     })
    //     .fail(function(response) {
    //       new AlertMessage(response.message, true)
    //       .isError(true)
    //       .show();
    //     })
    //     .always(function(response) {
    //       $("#mask").hide();
    //     });
    // }
  });

  $("#close-api_delete_all_chat_history").click(function() {
    $("#api_delete_all_chat_history").fadeOut();
  });
  $("#delete_api_chat_archive").click(function(e) {
    $("#api_delete_all_chat_history").fadeIn();
    // e.preventDefault();
    // if (confirm('This function will delete the chat history for all chat ids, are you sure do you want to proceed?')) {
    //   $.ajax({
    //     url: "/migachat/bridgeapi/deleteinstancechathistory",
    //     type: "post",
    //     data: {
    //       value_id: value_id,
    //     },
    //     dataType: "json",
    //   })
    //     .done(function(response) {
    //       console.log(response.message);

    //       new AlertMessage(response.message, true)
    //       .isError(false)
    //       .show();

    //       page.reload();
    //       $("#mask").hide();
    //     })
    //     .fail(function(response) {
    //       new AlertMessage(response.message, true)
    //       .isError(true)
    //       .show();
    //     })
    //     .always(function(response) {
    //       $("#mask").hide();
    //     });
    // }
  });
});
// #assistantsettings on submit send ajax post request with form_data, form data have also files
$(document).ready(function() {
  $("#assistantsettings").submit(function(e) {
    e.preventDefault();
    var formData = new FormData(this); // This will include file inputs as well
    console.log(formData);
    $("#mask").show();
    var actionUrl = $(this).attr("action");
    $.ajax({
      url: actionUrl,
      type: "POST",
      data: formData,
      contentType: false, // Required for FormData with files
      processData: false, // Required for FormData with files
      dataType: "json"
    })
      .done(function(response) {
        console.log(response);
        new AlertMessage(response.message, true).isError(false).show();
        $("#mask").hide();
        page.reload();
      })
      .fail(function(response) {
        console.log(response);
        new AlertMessage(response.message, true).isError(true).show();
      })
      .always(function(response) {
        $("#mask").hide();
      });
  });
});

function deleteApiChat(data_type, delete_type) {
  if (data_type == 1) {
    if (
      confirm(
        "This function will delete the chat history for all chat ids, are you sure do you want to proceed?"
      )
    ) {
      $.ajax({
        url: "/migachat/bridgeapi/deleteinstancechathistory",
        type: "post",
        data: {
          value_id: value_id,
          delete_type: delete_type
        },
        dataType: "json"
      })
        .done(function(response) {
          console.log(response.message);

          new AlertMessage(response.message, true).isError(false).show();

          page.reload();
          $("#mask").hide();
        })
        .fail(function(response) {
          new AlertMessage(response.message, true).isError(true).show();
        })
        .always(function(response) {
          $("#mask").hide();
        });
    }
  } else {
    customer_id = $("#api_chat_archive_filter").val();
    if (
      confirm(
        "This function will delete the current chat id " +
          customer_id +
          " history. Are you sure you want to proceed?"
      )
    ) {
      $.ajax({
        url: "/migachat/bridgeapi/deletectchathistory",
        type: "post",
        data: {
          value_id: value_id,
          customer_id: customer_id,
          delete_type: delete_type
        },
        dataType: "json"
      })
        .done(function(response) {
          console.log(response.message);

          new AlertMessage(response.message, true).isError(false).show();

          page.reload();
          $("#mask").hide();
        })
        .fail(function(response) {
          new AlertMessage(response.message, true).isError(true).show();
        })
        .always(function(response) {
          $("#mask").hide();
        });
    }
  }
  $("#api_delete_single_chat_history").fadeOut();
  $("#api_delete_all_chat_history").fadeOut();
}
$(document).ready(function() {
  $("#disable-bridge-api").click(function(e) {
    e.preventDefault();
    $("#mask").show();
    if (confirm("Are you sure you want to disable the bridge api?")) {
      $.ajax({
        url: "/migachat/bridgeapi/disablebridgeapi",
        type: "post",
        data: {
          value_id: value_id
        },
        dataType: "json"
      })
        .done(function(response) {
          console.log(response.message);

          new AlertMessage(response.message, true).isError(false).show();

          page.reload();
          $("#mask").hide();
        })
        .fail(function(response) {
          new AlertMessage(response.message, true).isError(true).show();
        })
        .always(function(response) {
          $("#mask").hide();
        });
    }
  });

  $("#download_api_chat_stats_csv").click(function(e) {
    e.preventDefault();
    console.log($("#api_chat-date-from").val());
    console.log($("#api_chat-date-to").val());
    var date_from = $("#api_chat-date-from").val();
    var date_to = $("#api_chat-date-to").val();
    $("#mask").show();
    $.ajax({
      url: "/migachat/bridgeapi/getapichatstatscsvdownload",
      type: "post",
      data: {
        value_id: value_id,
        date_from: date_from,
        date_to: date_to
      },
      dataType: "json"
    })
      .done(function(response) {
        console.log(response);
        // Convert the JSON response into CSV content
        var csvContent =
          '"Chat Id","Name","email","mobile","Role","Message","Message Date","Tokens Used"' +
          "\n";

        for (var i = 0; i < response.data.length; i++) {
          var row = response.data[i];
          var role = "Agent";
          var tokens = row["completion_tokens"];
          if (row["role"] == "user") {
            role = "User";
            tokens = row["prompt_tokens"];
          }
          var message_content = row["message_content"];
          // Escape double quotes within the string and enclose the string in double quotes

          message_content = message_content.replace(/,/g, " ");
          message_content = message_content.replace(/"/g, " ");
          csvContent +=
            '"' +
            row["chat_id"] +
            '","' +
            row["user_name"] +
            '","' +
            row["user_email"] +
            '","' +
            row["user_mobile"] +
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
        console.log(csvContent);
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
      .fail(function(response) {
        new AlertMessage(response.message, true).isError(true).show();
        $("#mask").hide();
      })
      .always(function(response) {
        $("#mask").hide();
      });
  });

  $("#filter_api_chat_stats").click(function(e) {
    e.preventDefault();
    console.log($("#api_chat-date-from").val());
    console.log($("#api_chat-date-to").val());
    var date_from = $("#api_chat-date-from").val();
    var date_to = $("#api_chat-date-to").val();
    $("#mask").show();
    $.ajax({
      url: "/migachat/bridgeapi/getapichatstats",
      type: "post",
      data: {
        value_id: value_id,
        date_from: date_from,
        date_to: date_to
      },
      dataType: "json"
    })
      .done(function(response) {
        console.log(response);
        $("#api_chat-total-chats").val(response.data.unique_customer_count);
        $("#api-chat-prompt-tokens").val(
          response.data.chat_stats.total_prompt_tokens
        );
        $("#api-chat-completion-tokens").val(
          response.data.chat_stats.total_completion_tokens
        );
        $("#mask").hide();
      })
      .fail(function(response) {
        new AlertMessage(response.message, true).isError(true).show();
      })
      .always(function(response) {
        $("#mask").hide();
      });
  });
  function safeDecodeURIComponent(input) {
    try {
        return decodeURIComponent(input);
    } catch (e) {
        console.error("Error decoding:", input);
        return input; // Return the original input if decoding fails
    }
}
  $("#api_chat_archive_search").click(function(e) {
    $("#api_chat_archive_chat_id").val("");
    $("#api_chat_archive_user_name").val("");
    $("#api_chat_archive_user_email").val("");
    $("#api_chat_archive_user_mobile").val("");
    $("#api_chat_archive_table_body").html("");
    $("#api_chat_archive_ai_on").hide();
    $("#api_chat_archive_ai_off").hide();
    $("#api_chat_archive_limit_off").hide();
    $("#temporary_blacklist_msg").hide();
    $("#temporary_blacklist_user").hide();
    $("#delete_bridge_chat_history").hide();
    $("#api_chat_archive_table_csv").hide();
    e.preventDefault();
    console.log($("#api_chat_archive_datefrom").val());
    console.log($("#api_chat_archive_dateto").val());
    var customer_id = $("#api_chat_archive_filter").val();
    var date_from = $("#api_chat_archive_datefrom").val();
    var date_to = $("#api_chat_archive_dateto").val();
    if (customer_id) {
      $("#chat_id_delete").show();
    } else {
      $("#chat_id_delete").hide();
    }
    if (customer_id) {
      $("#mask").show();
      $.ajax({
        url: "/migachat/bridgeapi/getbridgeapichatlogs",
        type: "post",
        data: {
          value_id: value_id,
          customer_id: customer_id,
          date_from: date_from,
          date_to: date_to
        },
        dataType: "json"
      })
        .done(function(response) {
          console.log(response);
          $("#api_chat_archive_chat_id").html(response.chat_id);
          $("#api_chat_archive_user_name").html(response.name);
          $("#api_chat_archive_user_email").html(response.email);
          $("#api_chat_archive_user_mobile").html(response.mobile);
          $("#delete_bridge_chat_history").show();
          $("#api_chat_archive_table_csv").show();
          if (response.is_ai_turned_off) {
            $("#api_chat_archive_ai_on").hide();
            $("#api_chat_archive_ai_off").show();
          } else {
            $("#api_chat_archive_ai_on").show();
            $("#api_chat_archive_ai_off").hide();
          }
          if (response.is_temp_blaclist) {
            $("#temporary_blacklist_msg").show();
            $("#temporary_blacklist_user").hide();
          }else{
            $("#temporary_blacklist_msg").hide();
            $("#temporary_blacklist_user").show();
          }
          if (response.is_limit_turned_off) {
            $("#api_chat_archive_limit_off").show();
          }
          var table_rows = "";
          for (var i = 0; i < response.chatlogs.length; i++) {
            var row = response.chatlogs[i];
            console.log(row["text"]);
            
            table_rows += "<tr>";
            table_rows += "<td>" + row["date_time"] + "</td>";
            table_rows += "<td>" + row["sender"] + "</td>";
            table_rows += "<td>" + safeDecodeURIComponent(row["text"]) + "</td>";
            table_rows += "<td>" + row["tokens"] + "</td>";
            table_rows += "</tr>";
          }
          $("#api_chat_archive_table_body").html(table_rows);
          $("#mask").hide();
        })
        .fail(function(response) {
          $("#mask").hide();
          new AlertMessage(response.message, true).isError(true).show();
        })
        .always(function(response) {
          $("#mask").hide();
        });
    }
  });

  $("#api_chat_archive_table_csv").click(function(e) {
    e.preventDefault();
    console.log($("#api_chat_archive_datefrom").val());
    console.log($("#api_chat_archive_dateto").val());
    var customer_id = $("#api_chat_archive_filter").val();
    var date_from = $("#api_chat_archive_datefrom").val();
    var date_to = $("#api_chat_archive_dateto").val();

    $("#api_chat_archive_ai_on").hide();
    $("#api_chat_archive_ai_off").hide();
    $("#api_chat_archive_limit_off").hide();
    $.ajax({
      url: "/migachat/bridgeapi/downloadcsv",
      method: "POST",
      data: {
        value_id: value_id,
        customer_id: customer_id,
        date_from: date_from,
        date_to: date_to
      },
      dataType: "json",
      success: function(data) {
        console.log(data);

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
            '","' +
            row[7] +
            '","' +
            row[8] +
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
      error: function(jqXHR, textStatus, errorThrown) {
        console.error("Error loading user chat data:", errorThrown);
      }
    });
  });

  $("#reset_privacy_consent_for_all").click(function(e) {
    e.preventDefault();

    if (
      confirm(
        "Are you sure you want to restart privacy collection process for all users?"
      )
    ) {
      $("#mask").show();
      $.ajax({
        url: "/migachat/bridgeapi/resetprivacyconsentforall",
        type: "post",
        data: {
          value_id: value_id
        },
        dataType: "json"
      })
        .done(function(response) {
          console.log(response);

          new AlertMessage(response.message, true).isError(false).show();
          $("#mask").hide();
          // page.reload();
        })
        .fail(function(response) {
          new AlertMessage(response.message, true).isError(true).show();
        })
        .always(function(response) {
          $("#mask").hide();
        });
    }
  });
  function resetConsent(chat_id) {
    console.log(chat_id);
    if (
      confirm(
        "Are you sure you want to restart privacy collection process for this user?"
      )
    ) {
      $("#mask").show();
      $.ajax({
        url: "/migachat/bridgeapi/resetprivacyconsentforone",
        type: "post",
        data: {
          value_id: value_id,
          chat_id: chat_id
        },
        dataType: "json"
      })
        .done(function(response) {
          console.log(response);

          new AlertMessage(response.message, true).isError(false).show();
          $("#mask").hide();
          // page.reload();
        })
        .fail(function(response) {
          new AlertMessage(response.message, true).isError(true).show();
        })
        .always(function(response) {
          $("#mask").hide();
        });
    }
  }

  $("#csv_privacy_consent_for_all").click(function(e) {
    e.preventDefault();

    $.ajax({
      url: "/migachat/bridgeapi/downloadcsvgdpr",
      method: "POST",
      data: {
        value_id: value_id
      },
      dataType: "json",
      success: function(data) {
        console.log(data);

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
            '","' +
            row[7] +
            '","' +
            row[8] +
            '","' +
            row[9] +
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
        a.download = "customer_gdpr_consents.csv";
        document.body.appendChild(a);
        a.click();

        // Clean up
        window.URL.revokeObjectURL(url);
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.error("Error loading user chat data:", errorThrown);
      }
    });
  });
});
$(document).ready(function() {
  // Function to handle the AJAX request
  value_id = $("#value_id").val();
  $("#temporary_blacklist_user").click(function(e) {
    e.preventDefault();
    // api_chat_archive_filter selected value
    var chat_id = $("#api_chat_archive_filter").val();
    if (chat_id) {
      $("#mask").show();
      $.ajax({
        url: "/migachat/bridgeapi/temproraryblacklist",
        type: "post",
        data: {
          value_id: value_id,
          chat_id: chat_id
        },
        dataType: "json"
      })
        .done(function(response) {
          console.log(response);
          new AlertMessage(response.message, true).isError(false).show();
          $("#mask").hide();
          // page.reload();
        })
        .fail(function(response) {
          new AlertMessage(response.message, true).isError(true).show();
        })
        .always(function(response) {
          $("#mask").hide();
        });
    }else{
      var alertMessage = "<?php echo p__('Migachat','Please select a chat id'); ?>";
      new AlertMessage(alertMessage, true).isError(true).show();
    }
  });
  $('#assistant_id_dd').change(function() {
    var assistant_id = $(this).val();
    console.log(assistant_id);
    
    if (assistant_id !== "" && assistant_id !== 0 && assistant_id !== '0') {
      $("#mask").show();
      $.ajax({
        url: "/migachat/bridgeapi/getassistantinfo",
        type: "post",
        data: {
          value_id: value_id,
          assistant_id: assistant_id
        },
        dataType: "json"
      })
        .done(function(response) {
          console.log(response.data);
          
          var response2 = response.data;
          $("#assistant_name").val(response2.name);
          $("#assistant_description").text(response2.description);
          $("#assistant_instructions").text(response2.instructions);
          $("#assistant_model").val(response2.model).change();
          $("#top_p-input").val(response2.top_p);
          $("#temperature-input").val(response2.temperature);
            // Parse openai_file_ids as JSON if it's a JSON string
            
            var filesHtml = "<ol>";
            if (response.vector_store_ids) {
              filesHtml += "<li><b><?php echo p__('Migachat','Vector Store ID:'); ?></b><input type='hidden' name='vector_store_ids' value='"+response.vector_store_ids+"'> " + response.vector_store_ids + "</li>";
            
            filesHtml +="<li> <b><?php echo p__('Migachat','OpenAI File IDs'); ?>:</b></li>";
            filesHtml +="<li> <ol id='existing_files'>";
              console.log(response.file_ids);
              
            if (response.file_ids.length > 0) {
              for (var i = 0; i < response.file_ids.length; i++) {
                filesHtml +=
                  "<li style='padding-bottom:5px;'>" +
                  response.file_ids[i] +
                  ' <button type="button" class="remove-file btn color-red" data-vs="'+response.vector_store_ids+'"  data-fid="'+response.file_ids[i]+'"><i class="fa fa-trash"></i></button></li>';
              }
            } else {
                filesHtml += "<li><?php echo p__('Migachat','No files found.'); ?></li>";
            }

            filesHtml += "</ol></li>";
              
            }else {
              filesHtml += "<li><?php echo p__('Migachat','No Vector Store IDs found.'); ?></li>";
            }
            filesHtml += "</ol>";

            
            $("#existing_files").html(filesHtml);
          $("#mask").hide();
        })
        .fail(function(response) {
          new AlertMessage(response.message, true).isError(true).show();
        })
        .always(function(response) {
          $("#mask").hide();
        });
    } else {
      $("#assistant_name").val("");
      $("#assistant_description").text("");
      $("#assistant_instructions").text("");
      $("#assistant_model").val("");
      $("#top_p-input").val("");
      $("#temperature-input").val("");
      $("#existing_files").html("");
    }
  });
})
// ready
$(document).ready(function() {
  // assistant_id_dd trigger change event after page load 2 seconds
  setTimeout(function() {
  $('#assistant_id_dd').trigger('change');
  }, 2000);
});
      // Attach click handler after rendering
$(document).unbind('click.removeFile"').on('click.removeFile', '.remove-file', function () {
    var fileId = $(this).data("fid");
    var vectorStoreId = $(this).data("vs");
    console.log("Removing file with ID:", fileId, "from Vector Store ID:", vectorStoreId);
    var confirmationMessage = "<?php echo p__('Migachat','Are you sure you want to remove this file from the vector store?'); ?>";
    if (confirm(confirmationMessage)) {
      $("#mask").show();
      $.ajax({
        url: "/migachat/bridgeapi/removefilefromvectorstore",
        type: "post",
        data: {
          value_id: value_id,
          file_id: fileId,
          vector_store_id: vectorStoreId
        },
        dataType: "json"
      })
        .done(function(response) {
          console.log(response.message);
          new AlertMessage(response.message, true).isError(false).show();
          // Reload the assistant info to update the file list
          $('#assistant_id_dd').trigger('change');
        })
        .fail(function(response) {
          new AlertMessage(response.message, true).isError(true).show();
        })
        .always(function(response) {
          $("#mask").hide();
        });
    }
  });

// api_chat_archive_filter
// temporary_blacklist_user