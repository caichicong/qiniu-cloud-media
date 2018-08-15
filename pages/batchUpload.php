<style>
    .uploader-list .item{
        background-color: #fff;
        padding:5px 20px;
    }

    .uploader-list .info {
        width: 600px;
        display: inline-block;
    }

    #thelist {
        margin-top:20px;
    }

    .progress {
        background-color: #ccc;
        border: #333;
        color:#fff;
        width:200px;
        display: inline-block;
        border-radius: 4px;
        text-align: center;
    }

    .progressbar {
        padding:5px 10px;
        width:0%;
        background-color: #008ec2;
        border-radius: 4px;
    }

    .webuploader-pick {
        background-color:#008ec2;
    }

    .btns-wrapper {
        display: flex;
        justify-content: center
    }
</style>
<div class="wrap">
    <h1>上传文件到七牛云</h1>
    <div id="uploader" class="wu-example">
        <div class="btns btns-wrapper">
            <div id="picker">选择要上传的文件</div>
        </div>
        <!--用来存放文件信息-->
        <div id="thelist" class="uploader-list"></div>
        <div class="btns btns-wrapper" id="startUploadButton" style="visibility: hidden;">
            <div id="ctlBtn" style="margin-top:20px;" class="button button-primary button-large">开始上传</div>
        </div>
    </div>
    <div>
        <a href="/wp-admin/edit.php?post_type=qiniu_media">查看已上传文件</a>
    </div>
</div>
