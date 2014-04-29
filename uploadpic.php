<?php
class uploadPic
    {
        private $imgtype;
        private $mimetype;
        private $path;
        private $error;
        
        public $picname;
        public $picwidth;
        public $picheight;

        public $types = array();
		
		public function __construct($types=array("image/gif","image/jpeg","image/pjpeg","image/png")){
			
			$this->types = $types;
		}

        //返回函数
        function goback($str)
        {
            echo "<script>alert('".$str."');history.go(-1);</script>";
            exit();
        }
        function check($file)//检查文件类型
        {
            if($_FILES[$file]["name"] == ""){
                $this->goback("没有文件要上传");
			}
			if(!in_array($_FILES[$file]["type"],$this->types))
                $this->goback("请用jpg/gif/png格式的图片");
            if($_FILES[$file]["size"] > 600000)
                $this->goback('对不起,图片大小不能超过600k,请用photoshop压缩或换一幅小的图片!');
            
            $this->mimetype = $_FILES[$file]["type"];
            $size = getimagesize($_FILES[$file]['tmp_name']);
            $this->picwidth = $size[0];
            $this->height = $size[1];
            $this->path = $_FILES[$file]["tmp_name"];
            $this->error = $_FILES[$file]["error"];
            $this->getType();
        }
        function getType()//返回文件扩展名
        {
            switch($this->mimetype)
            {
                case "image/gif":
                    $this->imgtype = ".gif";
                    break;
                case "image/jpeg":
                    $this->imgtype = ".jpg";
                    break;
                case "image/pjpeg":
                    $this->imgtype = ".jpg";
                    break;
                case "image/png":
                    $this->imgtype = ".png";
                    break;
            }
        }
        function getError()
        {
            switch($this->error)
            {
                case 0:
                    break;
                case 1:
                    $this->goback("文件大小超过限制，请缩小后再上传");
                    break;
                case 2:
                    $this->goback("文件大小超过限制，请缩小后再上传");
                    break;
                case 3:
                    $this->goback("文件上传过程中出错，请稍后再上传");
                    break;
                case 4:
                    $this->goback("文件上传失败，请重新上传");
                    break;
            }
        }
        function upfile($path,$file)
        {
            $this->check($file);
            //$this->goback($this->mimetype);
            $this->picname = date('YmdHis').rand(0,9);
            $this->getError();
            move_uploaded_file($this->path,$path.$this->picname.$this->imgtype);
            
            $this->picname = $this->picname.$this->imgtype;
        }
    }
    
?>
