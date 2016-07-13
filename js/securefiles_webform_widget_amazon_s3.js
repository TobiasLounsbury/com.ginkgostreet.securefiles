(function ($, D) {

  function uploadFileWithPromise(file, key, field) {

    var dfd = $.Deferred();

    var params = {
      Key: key,
      ContentType: file.type,
      Body: file
    };

    //server-side Encryption Settings
    if(D.settings.SecureFilesWidget.useEncryption) {
      params.ServerSideEncryption = D.settings.SecureFilesWidget.encryptionType;
    }

    D.settings.SecureFilesWidget.Bucket.putObject(params, function (err, data) {
      if (err) {
        dfd.reject(err);
      } else {
        //Unset the file so it is never uploaded.
        $(field).val("");

        //Resolve this promise
        var fieldId = $(field).attr("id").replace(/.*custom-/g, "");
        dfd.resolve({"field": fieldId, "name": key, "mime_type": file.type, "data": data});
      }
    });


    return dfd.promise();
  }


  $(".securefiles_upload input:file").on("securefiles_upload", function(event) {
    if (D.settings.SecureFilesWidget.useSTS) {
      //Upload the file using the JavaScript SDK
      D.settings.SecureFilesWidget.promises = [];

      var obj = event.target;
      var i = 0;
      if(obj.files[i]) {
        //Looks like we have something to upload
        var file = obj.files[i];
        var fileTitle = obj.files[i].name;
        if (D.settings.SecureFilesWidget.S3Fields.hasOwnProperty($(obj).attr("id")) &&
            D.settings.SecureFilesWidget.S3Fields[$(obj).attr("id")].filename) {
          fileTitle = D.settings.SecureFilesWidget.S3Fields[$(obj).attr("id")].filename;
        }
        var objKey = D.settings.SecureFilesWidget.currentContactId + '/' + fileTitle;

        D.settings.SecureFilesWidget.promises.push(uploadFileWithPromise(obj.files[i], objKey, obj));
      }

      $.when.apply($, D.settings.SecureFilesWidget.promises).done(function(data) {
        $(obj).trigger("securefiles_upload_complete", data);
      }).fail(function(err) {
        console.log(err);
      });

    }
  });



  //Setup Creds for S3 if we need them.
  if(D.settings.SecureFilesWidget.useSTS) {

    AWS.config.update({
      accessKeyId: D.settings.SecureFilesWidget.Credentials.AccessKeyId,
      secretAccessKey: D.settings.SecureFilesWidget.Credentials.SecretAccessKey,
      sessionToken: D.settings.SecureFilesWidget.Credentials.SessionToken
    });

    AWS.config.region = D.settings.SecureFilesWidget.S3Region;

    //This tells the js sdk to log debugging symbols to the console
    //AWS.config.logger = console;

    D.settings.SecureFilesWidget.Bucket = new AWS.S3({
      params: {
        Bucket: D.settings.SecureFilesWidget.S3Bucket
      }
    });
  }

})(jQuery, Drupal);