<?php
class Model{

	protected  	$pk 		=	'';			// 表主键
	protected 	$table 		=	'';			// 表名
	protected	$prefix		=	'';			//表前缀
	protected 	$db	 		=	'';			// 数据库链接
	public		$error  	= 	'';			// 错误提示
	protected	$_auto 		= 	array();	// 自动完成数组
	protected	$methods 	= 	array('field','alias','where','group','having','order','limit','join');
	protected	$options	=	array();	// 要操作的子句数组
	protected	$data		=	array();	// 用户调教的数据

	/**
	* 构造函数，调用mysqli单例类
	* @access public
	* @return void
	*/
	public function __construct(){

		$this->prefix = C('DBPREFIX');
		$this->table = $this->prefix.$this->table;
		$this->db = Mysqlis::getIns();
	}

	/**
	* 魔术方法，访问一个不可访问的方法
	* @access public
	* @param string $name 方法名称
	* @param array $args 方法的参数
	* @return void
	*/
	public function __call($name,$args){
		$this->$name = (isset($args) && !empty($args))?$args[0]:'';
		return $this;
	}

	/**
	* 魔术方法，访问一个不存在的属性
	* @access public
	* @param string $key 属性名
	* @param string $val 属性值
	* @return void
	*/
	public function __set($key,$val){
		if(in_array($key,$this->methods)){
			$this->options[$key]  =  $val;
		}else{
			$this->data[$key] = $val;
		}
	}

	/**
	* 魔术方法，得到一个无权限访问的属性
	* @access public
	* @param string $key 属性名称
	* @return void
	*/
	public function __get($key){
		if(in_array($key,$this->methods)){
			return isset($this->options[$key])?$this->options[$key]:null;
		}else{
			return isset($this->data[$key]) ? $this->dta[$key] : null ;
		}
	}

	/**
	* select 语句生成
	* @access public
	* @return mixed(bool/resource)
	*/
	public function select(){
		$sql = $this->query();
		return $this->db->getAll($sql);
	}

	/**
	* 得到表名
	* @access public
	* @param string $table 表名
	*/
	public function table($table){
		$this->table = $this->prefix.$table;
		return $this;
	}
	/**
	* 写入子句
	* @access public
	* @param  array $data 用户提交的信息
	* @return mixed(int/boolen) 受影响的行数
	*/
	public function add($data=''){
		$this->db->autoExecute($this->table,!empty($this->data)?$this->data:$data,'insert',$this->where);
		return $this->affected_rows();
	}

	/**
	* 删除子句
	* @param mixed(string/int) 主键
	* @return mixed(boolen/int) 受影响的行数
	*/
	public function delete($id=''){
		$wh1 = ' WHERE ';
		if(isset($id) && !empty($id) && is_numeric($id)){ $wh1 .= $this->pk.' = '.$id; }
		if(isset($id) && !empty($id) && is_string($id)) { $wh1 .= $this->pk.' in ('.$id.')'; }

		$wh2 = is_string($this->where)?' WHERE '.($this->where?$this->where:1):'';
		$where = (isset($id) && !empty($id) )?$wh1:$wh2;
		$sql = 'delete from '.$this->table." {$where} ";
		$this->db->query($sql);
		return $this->db->affect_row();
	}

	/**
	* 数据创建
	* @access public
	* @param $data 用户提交的信息
	* @return mixed(int/boolen) 受影响的行数
	*/
	public function save($data=''){
		$where = is_string($this->where)?' WHERE '.($this->where?$this->where:1):'';
		$limit = (is_string($this->limit)|| is_numeric($this->limit))?'  LIMIT '.$this->limit:'';
		$where = $where.' '.$limit;
		$this->db->autoExecute($this->table,!empty($this->data)?$this->data:$data,'update',$where);
		$this->data = array();
		$data = array();
		return $this->db->affect_row();
	}

	/**
	* 单条数据查询
	* @access public
	* @return array 单条数组
	*/
	public function find(){
		$sql = $this->query();
		return $this->db->getOne($sql);
	}

	/**
	* 连接查询(INNER JOIN|LEFT JOIN|RIGHT JOIN)
	* @param mixed(string/array) 连接语句
	* @return object 返回当前实例化的对象
	*/
	public function join($join){
		if(is_array($join)){
			$join = implode(' ',$join);
		}
		$join = preg_replace_callback('/left join|right join/i',function($matches){ return strtoupper($matches[0]); },strtolower($join));
		if(!preg_match('/left join|right join/',strtolower($join))){
			$join = ' INNER JOIN '.$join;
		}
		$this->options['join'][] = $join;
		return $this;
	}

	/**
	* 查询子句
	* @access public
	* @return mixed(string/boolen/resource) 查询结果
	*/
	public function query($sql=''){
		if(empty($sql)){
			$field = is_string($this->field)?$this->field:'*';									//字段
			$alias = is_string($this->alias)?' AS '.$this->alias:'';							//表别名
			$joins = isset($this->options['join'])?implode(' ', $this->options['join']):' ';	//连接子句

			$sql   = "SELECT {$field} FROM {$this->table} {$alias} {$joins} ";

			$sql .= is_string($this->where)?' WHERE '.$this->where:'';							//where 子句
			$sql .= is_string($this->group)?' GROUP BY '.$this->group:'';						//group 子句
			$sql .= is_string($this->having)?' HAVING '.$this->having:'';						//having 子句
			$sql .= is_string($this->order)?' ORDER BY'.$this->order:'';						//order  子句
			$sql .= (is_string($this->limit)|| is_numeric($this->limit))?' LIMIT '.$this->limit:'';	//limit 子句

			//数组缓存清除
			$this->options = array();
			return $sql;
		}
		return $this->db->query($sql);
	}

	/**
	* 自动完成方法(对提交数据中的特定数据，进行补全)
	* array('regtime','function','time'),
	* array('passwd','function','md5')
	* @access public
	* @param array $data 用户提交的数据
	* @return array 返回补全的数据
	*/
	public function _automatic($data){
		foreach($this->_auto as $k=>$v){
			if(@array_key_exists($v[0],$data)){
				switch($v[1]){
					case 'value':
						$data[$v[0]]	=	$v[2];
						break;
					case 'function':
						$param = !empty($data[$v[0]])?$data[$v[0]]:'';
						$data[$v[0]]	=	call_user_func($v[2],$param);
						break;
				}
			}
		}
		return $data;
	}


	/**
	* 自动验证方法
	* array('username',1,'用户名不能为空','require'),
	* array('username',0,'用户名必须在3-16字符串内','length','3,16'),
	* array('passwd',1,'密码不能为空','require'),
	* @access public
	* @return void
	*
	*/
	public function _vaildata($data){
		if(empty($this->_vaild)){
			return false;
		}
		foreach($this->_vaild as $k =>$v){
			switch($v[1]){
				case 0 :  //存在字段就验证
					$v[4] = isset($v[4])?$v[4]:'';
					if(isset($data[$v[0]])){
						if(!$this->checkRules($data[$v[0]],$v[3],$v[4])){
							$this->error = $v[2];
							return false;
						}
					}
					break;
				case 1 :  //必须验证
					$v[4] = isset($v[4])?$v[4]:'';
					if(!$this->checkRules($data[$v[0]],$v[3],$v[4])){
						$this->error = $v[2];
						return false;
					}
					break;
				case 2 : //值不为空的时候验证
					$v[4] = isset($v[4])?$v[4]:'';
					if(isset($data[$v[0]]) && !empty($data[$v[0]])){
						if(!$this->checkRules($data[$v[0]],$v[3],$v[4])){
							$this->error = $v[2];
							return false;
						}
					}
					break;

			}
		}
		return $data;
	}

	/**
	* 进行验证的规则
	* @access public
	* @param string $value 要验证的数据
	* @param string $rules 验证的规则
	* @param mixed  $param 验证的数据范围
	* @return mixed
	*/
	private function checkRules($value,$rules,$param=''){
		switch($rules){
			case 'require':		//判断数据是否为空
				return !empty($value);
				break;
			case 'length': 		//判断数据的字符数
				list($min,$max)	=	explode(',',$param);
				return (mb_strlen($value) >= $min && mb_strlen($value) <= $max);
				break;
			case 'in':			//判断数据的范围
				return in_array($value,explode(',',$param));
				break;
			case 'between':		//判断数据的区间
				list($min,$max)	=	explode(',',$param);
				return ((int)$value >= $min && (int)$value <= $max);
				break;
			case 'numeric':  	//判断数据是否是数值型
				return is_numeric($value);
				break;
			case 'email':		//判断是否是邮箱
				$pattern = '/^(\w)+@(\w)+\.[a-z]{2,3}$/i';
				return preg_match($pattern,$value);
				break;
		}
	}
}

?>