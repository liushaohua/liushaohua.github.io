<?php
	/**
	*	分页公共类
	*	作者：张庆方
	*	时间：2014-9-29
	*
	**/
	
	class Page{
		//数据总条数
		protected $total_rows;
		//每页显示条数
		public $list_rows;
		//总页数
		protected $total_pages;
		//每页起始条目
		public $first_row;
		//当前页数
		protected $now_page;
		//上一页
		
		//下一页
		
		//偏移量
		public $plus = 3;
		//处理情况 Ajax分页 Html分页(静态化时) 普通get方式 
		protected  $method  = 'defalut'; 
		
		//
		protected  $url;
		protected  $parameter = '';
		protected  $page_name;
		protected  $ajax_func_name;
		 /**
		* 构造函数
		* 参数 $data
		**/
		public function __construct($data = array()){
			$this->total_rows = $data['total_rows'];
			$this->list_rows = !empty($data['list_rows']) && $data['list_rows'] <= 100 ? $data['list_rows'] : 15;
			$this->total_pages = ceil($this->total_rows / $this->list_rows);
			$this->page_name = !empty($data['page_name']) ? $data['page_name'] : 'page';
			$this->ajax_func_name = !empty($data['ajax_func_name']) ? $data['ajax_func_name'] : '';
			$this->method = !empty($data['method']) ? $data['method'] : '';
			$this->parameter = !empty($data['parameter']) ? $data['parameter'] : '';
			/* 当前页面 */
			if(!empty($data['now_page'])){
				$this->now_page = intval($data['now_page']);
			}else{
				$this->now_page = !empty($_GET[$this->page_name]) ? intval($_GET[$this->page_name]):1;
			}
			$this->now_page = $this->now_page <= 0 ? 1 : $this->now_page;
			if(!empty($this->total_pages) && $this->now_page > $this->total_pages){
				$this->now_page = $this->total_pages;
			}
			$this->first_row = $this->list_rows * ($this->now_page - 1);
		} 

		/**
		* 得到当前连接
		* @param $page
		* @param $text
		* @return string
		**/
		protected function _get_link($page,$text){
			switch ($this->method) {
				case 'ajax':
					$parameter = '';
					if($this->parameter)
					{
						$parameter = ','.$this->parameter;
					}
					return '<a onclick="' . $this->ajax_func_name . '(\'' . $page . '\''.$parameter.')" href="javascript:void(0)">' . $text . '</a>' . "\n";
				break;
				 
				case 'html':
					$url = str_replace('?', $page,$this->parameter);
					return '<a href="' .$url . '" class="page-num">' . $text . '</a>' . "\n";
				break;
				 
				default:
					return '<a href="' . $this->_get_url($page) . '" class="page-num">' . $text . '</a>' . "\n";
				break;
			}
		}
		/**
		* 设置当前页面链接
		**/
		protected function _set_url(){
			$url  =  $_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'],'?')?'':"?").$this->parameter;
			$parse = parse_url($url);
			if(isset($parse['query'])) {
				parse_str($parse['query'],$params);
				unset($params[$this->page_name]);
				$url   =  $parse['path'].'?'.http_build_query($params);
			}
			if(!empty($params))
			{
				$url .= '&';
			}
			$this->url = $url;
		}
		/**
		* 得到$page的url
		* @param $page 页面
		* @return string
		**/
		protected function _get_url($page){
			if($this->url === NULL)
			{
				$this->_set_url();   
			}
		// $lable = strpos('&', $this->url) === FALSE ? '' : '&';
			return $this->url . $this->page_name . '=' . $page;
		}
     
     
		/**
		 * 得到第一页
		 * @return string
		**/
		public function first_page($name = '首页'){
			// if($this->now_page > 5)
			// {
				return $this->_get_link('1', $name);
			// }   
			// return '';
		}
     
		/**
		* 最后一页
		* @param $name
		* @return string
		**/
		public function last_page($name = '末页'){
			// if($this->now_page < $this->total_pages - 5)
			// {
				return $this->_get_link($this->total_pages, $name);
			// }   
			// return '';
		}  
     
		/**
		* 上一页
		* @return string
		**/
		public function up_page(){
			if($this->now_page != 1)
			{
				return '<a href="' . $this->_get_url($this->now_page - 1) . '" class="page-pre">上一页</a>' . "\n";
			}
			return '';
		}
     
		/**
		* 下一页
		* @return string
		**/
		public function down_page(){
			if($this->now_page < $this->total_pages)
			{
				return '<a href="' . $this->_get_url($this->now_page + 1) . '" class="page-pre">下一页</a>' . "\n";
			}
			return '';
		}
		public function go_page(){
			return '<span class="f14 p-left-10">转到</span><input id="turnto" type="text" value = "" class="turnTo" /><span class="f14 p-right-10 ">页</span><input id="go_btn" class="hyq-round-btn" type="button" value="GO"/>';
		}
		
    /**
     * 分页样式输出
     * @param $param
     * @return string
     */
    public function show($param = 1)
    {
        if($this->total_rows < 1)
        {
            return '';
        }
         
        $className = 'show_' . $param;
         
        $classNames = get_class_methods($this);
 
        if(in_array($className, $classNames))
        {
            return $this->$className();
        }
        return '';
    }
	public function show_2()
    {
        if($this->total_pages != 1)
        {
            $return = '';
            $return .= $this->up_page();
            for($i = 1;$i<=$this->total_pages;$i++)
            {
                if($i == $this->now_page)
                {
                    $return .= "<span class='current'>$i</span>\n";
                }
                else
                {
                    if($this->now_page-$i>=3 && $i != 1)
                    {
                        $return .="<span>...</span>\n";
                        $i = $this->now_page-3;
                    }
                    else
                    {
                        if($i >= $this->now_page+3 && $i != $this->total_pages)
                        {
                            $return .="<span>...</span>\n"; 
                            $i = $this->total_pages;
                        }
                        $return .= $this->_get_link($i, $i) . "\n";
                    }
                }
            }
            $return .= $this->down_page();
            return $return;
        }
    }
     
    public function show_1() 
    {
        $plus = $this->plus;
        if( $plus + $this->now_page > $this->total_pages)
        {
            $begin = $this->total_pages - $plus * 2;
        }else{
            $begin = $this->now_page - $plus;
        }
         
        $begin = ($begin >= 1) ? $begin : 1;
        $return = '';
        $return .= $this->first_page();
        $return .= $this->up_page();
        for ($i = $begin; $i <= $begin + $plus * 2;$i++)
        {
            if($i>$this->total_pages)
            {
                break;
            }
            if($i == $this->now_page)
            {
                $return .= "<a  class=' page-num  current' id='cutpage'><span class='f14'>$i</span></a>\n";
            }
            else
            {
                $return .= $this->_get_link($i, $i) . "\n";
            }
        }
        $return .= $this->down_page();
		//echo $return.'<hr>';
		$return .= $this->go_page();
        $return .= $this->last_page();
		//echo $return;
        return $return;
    }
	
    public function show_3()
    {
        $plus = $this->plus;
        if( $plus + $this->now_page > $this->total_pages)
        {
            $begin = $this->total_pages - $plus * 2;
        }else{
            $begin = $this->now_page - $plus;
        }       
        $begin = ($begin >= 1) ? $begin : 1;
        $return = '总计 ' .$this->total_rows. ' 个记录分为 ' .$this->total_pages. ' 页, 当前第 ' . $this->now_page . ' 页 ';
        $return .= ',每页 ';
        $return .= '<input type="text" value="'.$this->list_rows.'" id="pageSize" size="3"> ';
        $return .= $this->first_page()."\n";
        $return .= $this->up_page()."\n"; 
        $return .= $this->down_page()."\n";
        $return .= $this->last_page()."\n";
        $return .= '<select onchange="'.$this->ajax_func_name.'(this.value)" id="gotoPage">';
        
        for ($i = $begin;$i<=$begin+10;$i++)
        {
            if($i>$this->total_pages)
            {
                break;
            }           
            if($i == $this->now_page)
            {
                $return .= '<option selected="true" value="'.$i.'">'.$i.'</option>';
            }
            else
            {
                $return .= '<option value="' .$i. '">' .$i. '</option>';
            }           
        }
         $return .= '</select>';
        return $return;
    }
	
	}