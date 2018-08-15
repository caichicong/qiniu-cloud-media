jQuery("#wp-content-media-buttons").append(jQuery("#qiniuButtonHtml").html());

// 关闭弹框
jQuery("#QiniuMediaPopup .media-modal-close").click(function () {
  window.parent.tb_remove();
});

function insertImageShortcode(text) {
  window.parent.send_to_editor(text);
}

var insertImageIds = [];
var currentCategory = 0;
var currentPage = 1;
var totalPage = 0;

// 插入代码
jQuery("#insertQiniuMedia").click(function () {
  for(let i = 0; i < insertImageIds.length; i++) {
    insertImageShortcode("[qimg action='' id='" + insertImageIds[i] + "']");
  }

  // clear
  insertImageIds = [];
  jQuery("#qiniu-media-list li").removeClass("selected");

  window.parent.tb_remove();
});


// 按分类获取图片
function getImageListForCategory(catid, page) {
  jQuery(".pagination .currentPage").html(page);
  var tpl = jQuery("#attachmentItem").html();

  var getImagePromise = jQuery.post("/wp-admin/admin-ajax.php", {page: page, catid: catid, action: 'get_qiniu_image_list'});

  getImagePromise.done(function (response) {

    if(response.imgs.length == 0) {
      return;
    }

    totalPage = parseInt(response.pagecount);
    jQuery(".pagination .totalPage").html(response.pagecount);

    jQuery("#qiniu-media-list").html('');

    var imageAction = "imageView2/1/w/150/h/150/format/jpg/q/75|imageslim";

    response.imgs.forEach(function (item) {
      let lihtml = tpl.replace("{{src}}", "http://videotest.hexjoy.com/" + item.key + "?"  + imageAction);
      lihtml = lihtml.replace("{{imgid}}", item.id);
      jQuery("#qiniu-media-list").append(lihtml);
    });

  }).fail(function (err) {
    console.log(err);
  });
}

// 选择分类
jQuery(".media-menu-item-qiniu").click(function () {
  currentCategory = $(this).attr("catid");
  $(".media-menu-item-qiniu").removeClass("active");
  $(this).addClass("active");
  getImageListForCategory(currentCategory, 1);
});

// 默认选择第一个分类
function makeFirstCategoryActive() {
  jQuery(".media-menu-item-qiniu").eq(0).addClass("active");
  currentCategory = jQuery(".media-menu-item-qiniu").eq(0).attr("catid");
  getImageListForCategory(currentCategory, 1);
}
makeFirstCategoryActive();

// 选择图片
jQuery("#qiniu-media-list").delegate("img","click",function(){
  var imgid = jQuery(this).parent().attr("imgid");

  jQuery(this).parent().parent().parent().parent().addClass("selected");
  insertImageIds.push(imgid);
});

// 翻页
jQuery(".qiniu-media-frame-content .pagination").delegate(".next-page", "click", function () {
  if(currentPage < totalPage) {
    currentPage = currentPage + 1;
    getImageListForCategory(currentCategory, currentPage);
  }
});

jQuery(".qiniu-media-frame-content .pagination").delegate(".previous-page", "click", function () {
  if(currentPage > 1) {
    currentPage = currentPage - 1;
    getImageListForCategory(currentCategory, currentPage);
  }
});

jQuery(".qiniu-media-frame-content .pagination").delegate(".pagejump", "click", function () {
  var page = jQuery(".pagenumber-input", ".pagination").val();
  if(page) {
    getImageListForCategory(currentCategory, parseInt(page));
  } else {
    alert("请填写页数");
  }

});
