<script type="text/html" id="qiniuButtonHtml">
    <input alt='#TB_inline?height=600&amp;width=800&amp;inlineId=QiniuMediaPopup' title='七牛云文件库' class='thickbox button button-primary button-large' type='button' value='添加七牛云文件' />
</script>
<script type="text/html" id="attachmentItem">
    <li>
        <div class="type-image portrait">
            <div class="thumbnail">
                <div class="centered" imgid="{{imgid}}">
                    <img src="{{src}}" alt="">
                </div>
            </div>
        </div>
    </li>
</script>
<style>
    .pagination {
        display: flex;
        justify-content: flex-end
    }

    .pagination a {
        text-decoration: none;
    }
    .pagination a.next-page:hover, .pagination a.previous-page:hover{
        border-color: #5b9dd9;
        color: #fff;
        background: #00a0d2;
        box-shadow: none;
        outline: 0;
    }
    .pagination a.next-page, .pagination a.previous-page {
        display: inline-block;
        min-width: 17px;
        border: 1px solid #ccc;
        padding: 3px 5px 7px;
        background: #e5e5e5;
        font-size: 16px;
        line-height: 1;
        font-weight: 400;
        text-align: center;
        margin: 0 20px;
    }

    .pagination .pagenumber {
        font-size: 12px;
        letter-spacing: 5px;
        display: inline-block;
        padding-top: 6px;
    }

    .pagination .pagenumber-input {
        width: 50px;
        text-align: center;
        margin-right: 20px;
    }

    .pagination .pagejump {
        margin-right: 20px !important;
    }

    #qiniu-media-list {
        padding: 12px;
    }

    #qiniu-media-list .selected {
        border: solid 4px #0073aa;
    }

    #qiniu-media-list li {
        display: inline-block;
        border: solid 2px #ccc;
    }

</style>
<?php
$mediaCategories = get_categories('taxonomy=category&post_type=qiniu_media');

?>

<div id="QiniuMediaPopup" style="display:none">
    <div class="media-modal wp-core-ui">

        <button type="button" class="media-modal-close"><span class="media-modal-icon"><span class="screen-reader-text">关闭媒体面板</span></span>
        </button>

        <div class="media-modal-content" id="vuetest">
            <div class="media-frame mode-select wp-core-ui">
                <div class="media-frame-menu">
                    <div class="media-menu">
                        <?php
                        if ($mediaCategories) {
                            $i = 0;
                            foreach($mediaCategories as $c) {
                                if($i == 0) {
                                    printf("<a class='active media-menu-item media-menu-item-qiniu' href='#' catid='%d'>%s</a>",$c->term_id, $c->name);
                                } else {
                                    printf("<a class='media-menu-item media-menu-item-qiniu' href='#' catid='%d'>%s</a>",$c->term_id, $c->name);
                                }
                                $i = $i + 1;
                            }
                            unset($i);
                        }
                        ?>
                    </div>
                </div>
            </div>


            <div class="media-frame-title">
                <h1>添加图片<span class="dashicons dashicons-arrow-down"></span></h1>
            </div>

            <div class="media-frame-content qiniu-media-frame-content">
                <ul class="attachments" id="qiniu-media-list">

                </ul>

                <div class="pagination">
                    <a class="previous-page" href="#">
                        <span aria-hidden="true">&lt;</span>
                    </a>
                    <div class="pagenumber"><span class="currentPage"></span>/ <span class="totalPage"></span> </div>
                    <a class="next-page" href="#">
                        <span aria-hidden="true">&gt;</span>
                    </a>
                    <input type="text" value="" class="pagenumber-input" /> <div class="button media-button button-primary pagejump">跳转</div>
                </div>
            </div>

            <div class="media-frame-toolbar">
                <div class="media-toolbar">
                    <div class="media-toolbar-primary search-form">
                        <div id="insertQiniuMedia" class='button media-button button-primary button-large media-button-insert'>添加</div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>