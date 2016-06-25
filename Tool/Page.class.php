<?php
	class PageTool{

		private $total;  				//所有记录数
		private $listRow;				//每一页记录数
		private $limit;					//记录限制条件
		private $uri;					//分页url
		private $page;					//当前页码
		private $totalPage;				//得到全部页码
		private $listNum = 8;			//页码列表的数量
		private $config = array(		//页码列表配置
			'header'	=>	'个记录数',
			'first'		=>	'首页',
			'prev'		=>	'上一页',
			'next'		=>	'下一页',
			'last'		=>	'尾页'
		);


		/**
		* 构造函数
		* @param $total 总记录数
		* @param $num 	每一页记录数
		* @param $pa 	url传递的其他参数
		* @return void
		*/
		public function __construct($total,$num,$pa=''){
			$this->total 	=	$total;
			$this->listRow	=	$num;
			$this->page 	=	isset($_GET['page'])?$_GET['page']:1;
			$this->uri 		=	$this->getUri($pa);
			$this->totalPage=	ceil($this->total/$this->listRow);
			$this->setLimit();
		}

		/**
		* 设置 limit offset 和 N 的值
		*/
		private function setLimit(){
			$this->limit 	= 	($this->page -1)*$this->listRow.','.$this->listRow;
		}

		/**
		* 分析url链接，并且返回分页链接
		*/
		private function getUri($pa){
			$uri  = $_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'],'?')?'':'?').$pa;
			$pUrl = parse_url($uri);
			if(isset($pUrl['query'])){
				parse_str($pUrl['query'],$params);
				unset($params['page']);
				$uri = $pUrl['path'].'?'.http_build_query($params);
			}
			return $uri;
		}

		/**
		* 使用 魔术方法访问私有变量 $limit 
		*/
		public function __get($val){
			if($val == 'limit')
				return $this->limit;
			else
				return;
		}

		/**
		* 动态配置页码列表配置项
		*/
		public function __set($key,$val){
			if(array_key_exists($key,$this->config)){
				$this->config[$key]	=	$val;
			}
		}

		/**
		* 显示当前页码的第一条记录
		*/
		private function start(){
			if($this->total == 0)
				return 0;
			else
				return ($this->page - 1) * $this->listRow + 1;
		}

		/**
		* 显示当前页码的最后一条记录
		*/
		private function end(){
			return min($this->page*$this->listRow,$this->total);
		}

		/**
		* 设置首页链接
		*/
		private function first(){
			if($this->page <= 1)
				return "&nbsp;{$this->config['first']} ";
			else
				return "&nbsp;<a href='{$this->uri}&page=1'>{$this->config['first']}</a> ";
		}

		/**
		* 设置上一页链接
		*/
		private function prev(){
			if($this->page <= 1)
				return "&nbsp;{$this->config['prev']} ";
			else
				return "&nbsp;<a href='{$this->uri}&page=".($this->page-1)."'>{$this->config['prev']}</a> ";
		}

		/**
		* 设置页码列表
		*/
		private function pageLists(){
			$iNum		=	floor($this->listNum/2);
			$listPage	=	'';
			for($i = $iNum;$i>=1;$i--){
				$page = $this->page - $i;
				if($page < 1)
					continue;
				$listPage .= "&nbsp;<a href='{$this->uri}&page=".$page."'>{$page}</a> ";
			}
			$listPage .= "&nbsp;{$this->page}</a> ";
			for($i=1;$i<=$iNum;$i++){
				$page = $this->page + $i;
				if($page > $this->totalPage)
					break;
				$listPage .= "&nbsp;<a href='{$this->uri}&page=".$page."'>{$page}</a> ";
			}
			return $listPage;
		}

		/**
		* 设置下一页链接
		*/
		private function next(){
			if($this->page >= $this->totalPage)
				return "&nbsp;{$this->config['next']} ";
			else
				return "&nbsp;<a href='{$this->uri}&page=".($this->page+1)."'>{$this->config['next']}</a> ";
		}

		/**
		* 使用JavaScript 实现 页码跳转
		*/
		private function goPage(){
			$goPage  = ' <input type="text" value="'.$this->page.'" style="width:25px" ';
			$goPage .= ' onkeydown="javascript:if(event.keyCode == 13){ var fpage = (this.value > '.$this->totalPage.')?'.$this->totalPage.':this.value;';
			$goPage .=	'location =\''.$this->uri.'&page=\'+fpage+\'\'};" />';
			$goPage .=  '<input type="button" value="GO" width="25"';
			$goPage .=	' onclick ="javascript:var fpage = (this.previousSibling.value > '.$this->totalPage.')?'.$this->totalPage.':this.previousSibling.value;';
			$goPage .=	'location =\''.$this->uri.'&page=\'+fpage+\'\';" />';
			return $goPage;
		}

		/**
		* 设置尾页链接
		*/
		private function last(){
			if($this->page >= $this->totalPage)
				return "&nbsp;{$this->config['last']} ";
			else
				return "&nbsp;<a href='{$this->uri}&page=".($this->totalPage)."'>{$this->config['last']}</a> ";
		}

		/**
		* 显示页码列表
		*/
		public function fpage($display = array(0,1,2,3,4,5,6,7,8,9)){
			$html[0] = "&nbsp;<b>{$this->total}</b>".$this->config['header'].' ';
			$html[1] = "&nbsp;每页显示<b>".($this->end() - $this->start() +1 )."</b>条 ";
			$html[2] = "&nbsp;本页<b>{$this->start()}/{$this->end()}</b>条 ";
			$html[3] = "&nbsp;<b>{$this->page}/{$this->totalPage}</b>页 ";
			$html[4] = $this->first();
			$html[5] = $this->prev();
			$html[6] = $this->pageLists();
			$html[7] = $this->next();
			$html[8] = $this->last();
			$html[9] = $this->goPage();

			$setHtml = '';
			foreach($display as $val){
				$setHtml .= $html[$val];
			}
			return $setHtml;
		}
	}

?>