var uploader = WebUploader.create({
  server: '/wp-admin/admin-ajax.php',
  pick: '#picker',
  resize: false,
  formData: {action : 'qiniu_upload_action'}
});

$list =  jQuery(".uploader-list");

uploader.on( 'fileQueued', function( file ) {
  jQuery("#startUploadButton").css("visibility", "visible");

  $list.append( '<div id="' + file.id + '" class="item">' +
    '<div class="info">' + file.name + '</div>' + "<div class='progress'><div class='progressbar'>0%</div></div>" + "</div>" );
});

uploader.on( 'uploadProgress', function( file, percentage ) {
  var $li = jQuery( '#'+file.id );

  $li.find(".progressbar").text(parseInt(percentage * 100) + '%');
  $li.find(".progressbar").css( 'width', percentage * 100 + '%' );
});

uploader.on( 'uploadSuccess', function( file ) {
  jQuery( '#'+file.id ).find('div.progresstext').text('100%');
});

uploader.on( 'uploadError', function( file ) {
  jQuery( '#'+file.id ).find('p.state').text('上传出错');
});

uploader.on( 'uploadComplete', function( file ) {
});

jQuery("#ctlBtn").click(function(){

  if($list.children().length > 0) {
    uploader.upload();
  } else {
    alert("请选择文件");
  }

});

