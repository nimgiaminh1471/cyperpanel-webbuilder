<?php
/*
# Class kết nối Database
*/

class DB implements ArrayAccess,IteratorAggregate{
	private $table,$distinct,$inRandomOrder,$select,$where,$whereRaw,$joins,$groupBy,$having,$havingRaw,$orderBy,$limit,$offset,$union,$pageCurrent, $withRelationship;
	protected $items = [];
	protected $connect;
	protected $connection = [];
	//Khởi tạo class & lưu tên table
	function __construct($table="", $connection = null){
		$this->table = $table;
		if( is_array($connection) ){
			$this->connection = $connection;
		}
		$this->connect();
	}

	//Lưu tên table
	public static function table($table, $connection = null){
		return new self($table, $connection);
	}


	private function connect(){
		$this->connect = @mysqli_connect(
			$this->connection["db_host"] ?? CONFIG["DB"]["db_host"],
			$this->connection["db_user"] ?? CONFIG["DB"]["db_user"],
			$this->connection["db_password"] ?? CONFIG["DB"]["db_password"],
			$this->connection["db_name"] ?? CONFIG["DB"]["db_name"]
		);
		if( $this->connect === false || empty($this->connect) ){
			PrintError("Hệ thống đang bảo trì, vui lòng quay lại sau, xin cảm ơn!");
		}
		mysqli_set_charset($this->connect, CONFIG["DB"]["db_charset"]);
	}

	

	//Câu lệnh query
	public function sqlQuery($sql=''){
		$query = mysqli_query($this->connect, $sql);
		if( $this->connect && !empty(mysqli_error($this->connect)) ){
			$errorMsg = str_replace("'", "\\'", mysqli_error($this->connect)).': '.str_replace("'", "\\'", $sql);
			Assets::footer('<script>console.log(\''.$errorMsg.'\')</script>');
		}
		return $query;
	}
	public static function query($sql){
		return (new self)->sqlQuery($sql);
	}


	//Tạo chuỗi truy vấn an toàn (PHP 8.1: không truyền null vào mysqli_real_escape_string)
	public function escapeString($str){
		if ( !$this->connect ) return (string) ($str ?? '');
		return mysqli_real_escape_string($this->connect, (string) ($str ?? ''));
	}
	public static function safeString($str){
		return (new self)->escapeString($str);
	}

	

	//Lọc cột
	public function columnFilter($column){
		$column=str_ireplace(["'", '"', ";"], "", $column);
		if( strpos($column, "(")!==false ){
			return $column;
		}
		$replace=[
			"."    =>"`.`",
			" as " =>"` AS `",
			"`*`"  =>"*",
		];
		$column="`$column`";
		foreach($replace as $from=>$to){
			$column=str_ireplace($from, $to, $column);
		}
		return $column;
	}
	





	//Truy xuất dữ liệu nhanh
	function __get($name){
		return $this->items->$name ?? null;
	} 
	function __isset($name){
		if(isset($this->items->$name)){ return true; }
	}

	// Select
	public function select($columns){
		if(!empty($columns)){
			$this->select=is_array($columns) ? $columns : func_get_args();
		}
		return $this;
	}

	//Distinct
	public function distinct(){
		$this->distinct=" DISTINCT";
		return $this;
	}
	//Random order
	public function inRandomOrder(){
		$this->inRandomOrder=" ORDER BY RAND()";
		return $this;
	}
	// Join
	public function join($table, $first="", $operator="", $second="", $type="INNER"){
		if(!empty($table)){
			$this->joins[]=is_array($table) ? $table : [$table,$first,$operator,$second,$type];
		}
		return $this;
	}

	// LeftJoin
	public function leftJoin($table, $first="", $operator="", $second=""){
		return $this->join($table,$first,$operator,$second,"LEFT");
	}

	// LeftJoin
	public function rightJoin($table, $first="", $operator="", $second=""){
		return $this->join($table,$first,$operator,$second,"RIGHT");
	}

	// Where
	public function where($column, $operator=null, $value=null, $type="where"){
		if(!empty($column)){
			if(is_array($column)){
				$this->where[][$type]=$column;
			}else{
				$this->where[][$type][]=[$column,$operator,$value];
			}
		}
		return $this;
	}

	//Where Raw
	public function whereRaw($where){
		$this->whereRaw=$where;
		return $this;
	}

	//GroupBy
	public function groupBy($groupBy){
		$this->groupBy=$groupBy;
		return $this;
	}

	// Where
	public function having($column, $operator=null, $value=null){
		if(!empty($column)){
			if(is_array($column)){
				$this->having[]["where"]=$column;
			}else{
				$this->having[]["where"][]=[$column,$operator,$value];
			}
		}
		return $this;
	}

	//Having Raw
	public function havingRaw($having){
		$this->havingRaw=$having;
		return $this;
	}


	// OrderBy
	public function orderBy($column,$sort="ASC"){
		if(!empty($column)){
			if(is_array($column)){ $this->orderBy=$column; }else{ $this->orderBy[]=func_get_args(); }
		}
		return $this;
	}

	// Limit
	public function limit($limit){
		$this->limit=$limit;
		return $this;
	}

	// Offset
	public function offset($offset){
		$this->offset=$offset;
		return $this;
	}



	//Tạo câu lệnh Where
	public function createWhere($where="",$table="",$prefix=" WHERE ",$sql=""){
		if(empty($where)){$where=$this->where;}
		if(empty($where)){return;}
		$isWhere=[
			"where"=>"",
			"whereIn"=>"IN",
			"whereNotIn"=>"NOT IN",
			"whereBetween"=>"BETWEEN",
			"whereNotBetween"=>"NOT BETWEEN",
			"whereNull"=>"IS NULL",
			"whereNotNull"=>"IS NOT NULL",
			"whereColumn"=>"COLUMN",
			"whereDate"=>"DATE",
			"whereDay"=>"DAY",
			"whereMonth"=>"MONTH",
			"whereYear"=>"YEAR",
		];
		$orWhere=[
			"orWhere"=>"",
			"orWhereIn"=>"IN",
			"orWhereNotIn"=>"NOT IN",
			"orWhereBetween"=>"BETWEEN",
			"orWhereNotBetween"=>"NOT BETWEEN",
			"orWhereNull"=>"IS NULL",
			"orWhereNotNull"=>"IS NOT NULL",
			"orWhereDate"=>"DATE",
			"orWhereDay"=>"DAY",
			"orWhereMonth"=>"MONTH",
			"orWhereYear"=>"YEAR",
		];
		foreach($where as $id=>$itemList){
			foreach($itemList as $method=>$items){
				if(isset($isWhere[$method])){
					$sql.="AND";
					$type=$isWhere[$method];
				}else{
					$sql.="OR";
					$type=$orWhere[$method];
				}
				//Bắt đầu nối lệnh where
				switch($type){

					//Where IN
					case "IN":
					case "NOT IN":
					foreach($items as $key=>$item){
						$whIn="(";
						foreach ($item[1] as $k=>$in) {
							$whIn.="".($k==0 ? "" : ",")."$in";
						}
						$whIn.=")";
						$sql.=" ".($key>0 ? "AND ":"")." {$table}".$this->columnFilter($item[0])."  {$type}{$this->escapeString($whIn)} ";
					}
					break;

					//Where Between
					case "BETWEEN":
					case "NOT BETWEEN":
					foreach($items as $key=>$item){
						$bet=" ".(INT)$item[1][0]." AND ".(INT)$item[1][1]."";
						$sql.=" ".($key>0 ? "AND ":"")." (".$this->columnFilter($item[0])."  {$type}".$this->escapeString($bet).") ";
					}
					break;

					//Where Null
					case "IS NULL":
					case "IS NOT NULL":
					foreach($items as $key=>$item){
						$sql.=" ".($key>0 ? "AND ":"")." ".$this->columnFilter($item[0])." {$type} ";
					}
					break;

					//Where Column
					case "COLUMN":
					foreach($items as $key=>$item){
						$column   = $item[0];
						$operator = is_null($item[2]) ? "=" : $item[1];
						$value    = is_null($item[2]) ? $item[1] : $item[2];
						$sql.=" ".($key>0 ? "AND ":"")." {$this->columnFilter($column)} $operator {$this->columnFilter($value)} ";
					}
					break;

					//Where Date
					case "DATE":
					case "DAY":
					case "MONTH":
					case "YEAR":
					foreach($items as $key=>$item){
						$sql.=" ".($key>0 ? "AND ":"")." {$type}(`{$item[0]}`) = '{$item[1]}' ";
					}
					break;


					//Where khác
					default:
					foreach($items as $key=>$item){
						$column   = $item[0];
						$operator = is_null($item[2]) ? "=" : $item[1];
						$value    = is_null($item[2]) ? $item[1] : $item[2];
						$sql.=" ".($key>0 ? "AND ":"")." {$table}".$this->columnFilter($column)." $operator '".$this->escapeString($value)."' ";
					}
				}
			}
		}
		return "{$prefix}".trim($sql,"AND");
	}

	// Nối các câu lệnh SQL
	public function sqlMerge($count=false,$get=false){
		$sql="";

		//Select
		if($count){
			$sql="SELECT COUNT(*) FROM `{$this->table}`";
		}else if(!empty($this->select)){
			$select="";
			foreach ($this->select as $id=>$sl) {
				$select.="".($id>0 ? "," : "")." ".$this->columnFilter($sl)." ";
			}
			$sql="SELECT".$this->distinct."{$select} ".(empty($this->table) ? "" : "FROM `{$this->table}`")."";
		}else if($get){
			$sql="SELECT * FROM `{$this->table}`";
		}

		//Join
		if(!empty($this->joins)){
			foreach($this->joins as $key=>$join){
				$sql.=" ".strtoupper("".$join[4]." JOIN")." `{$join[0]}` ON {$join[1]}{$join[2]}{$join[3]}";
			}
		}

		//Where
		if(!empty($this->where) && empty($this->whereRaw)){
			$sql.=$this->createWhere();
		}

		//Where raw
		if(!empty($this->whereRaw)){
			$sql.=" WHERE ";
			$sql.=$this->whereRaw;
		}

		//GroupBy
		if(!empty($this->groupBy)){
			$sql.=" GROUP BY ".$this->columnFilter($this->groupBy)."";
		}

		//Having
		if(!empty($this->having) && empty($this->havingRaw)){
			$sql.=$this->createWhere($this->having, "", " HAVING");
		}

		//Having raw
		if(!empty($this->havingRaw)){
			$sql.=" HAVING ";
			$sql.=$this->havingRaw;
		}


		//OrderBy
		if(!empty($this->orderBy)){
			$sql.=" ORDER BY";
			foreach($this->orderBy as $key=>$order){
				$sql.="".($key==0 ? "" : ",")." {$this->columnFilter($order[0])} {$order[1]}";
			}
		}

		//Random order
		if(!empty($this->inRandomOrder)){
			$sql.=$this->inRandomOrder;
		}

		//Limit
		if(!empty($this->limit) && !$count){
			$sql.=" LIMIT ".(INT)$this->limit."";
		}

		//Offset
		if(!empty($this->offset) && !$count){
			$sql.=" OFFSET ".(INT)$this->offset."";
		}

		//Union
		if(!empty($this->union)){
			foreach ($this->union as $type) {
				$sql.=" {$type["type"]} {$type["query"]} ";
			}
		}

		//echo '<br/>'.$sql.'<br/>';
		return $sql;
	}


	//Union
	public function union($query="",$type="UNION"){
		if(empty($query)){
			return ["type"=>$type, "query"=>$this->sqlMerge()];
		}else{
			if(isset($query["type"])){ $this->union[]=$query;  }else{ $this->union=$query; }
			return $this;
		}
	}

	//Union All
	public function unionAll($query="",$type="UNION ALL"){
		return $this->union($query,$type);
	}



	//Lấy dữ liệu
	public function get($export=false){
		if(empty($this->items)){
			if( $gData = $this->sqlQuery($this->sqlMerge(false,true)) ){
				$data = [];
				while($obData = mysqli_fetch_object($gData)){
					if( !empty($this->withRelationship) ){
						// Lấy dữ liệu từ bảng liên kết
						foreach($this->withRelationship as $item){
							$localKey = $item['localKey'];
							if( !empty( $obData->$localKey ) ){
								$getRelationshipData = DB::table($item['table'])->where($item['foreignKey'], $obData->$localKey);
								$key = $item['key'];
								$obData->$key = $item['many'] ? $getRelationshipData->get(true) : $getRelationshipData->first(true);
							}
						}
					}
					$data[] = $obData;
				}
				$this->items=$data;
			}
		}
		
		return $export ? $this->items : $this;
	}

	//Lấy toàn bộ dữ liệu
	public function all(){
		return $this->get();
	}

	//Lấy 1 bản ghi đầu tiên
	public function first($export=false){
		$this->get();
		$this->items=$this->items[0] ?? [];
		return $export ? $this->items : $this;
	}

	//Lấy 1 bản cuối cùng
	public function last($export=false){
		$this->get();
		$last=count($this->items)-1;
		$this->items=$this->items[$last] ?? [];
		return $export ? $this->items : $this;
	}

	//Lấy 1 cột duy nhất
	public function value($column){
		$this->select[]=$column;
		$this->get();
		return $this->items[0]->$column ?? NULL;
	}

	//Lấy giá trị bé nhất
	public function min($column,$type="MIN"){
		$data = $this->select("$type($column) AS `$type`")->first();
		return $data->$type ?? NULL;
	}

	//Lấy giá trị lớn nhất
	public function max($column){
		return $this->min($column,"MAX");
	}

	//Lấy giá trị trung bình
	public function avg($column){
		return $this->min($column,"AVG");
	}

	//Cộng giá trị
	public function sum($column){
		return $this->min($column,"SUM");
	}

	//Phân trang
	public function paginate($limit=10){
		$this->limit=(INT)$limit;
		$pageCurrent = isset($_REQUEST["page"]) ? (INT)$_REQUEST["page"] : 1;
		if($pageCurrent<1){$pageCurrent=1;}
		$this->pageCurrent=$pageCurrent;
		$this->offset = ($pageCurrent-1)*(INT)$limit;
		return $this->get();
	}


	//Link phân trang
	public function links($op = []){
		return paginationLinks($this->pageCurrent, $this->limit, $this->total(), is_array($op) ? $op : []);
	}


	//Insert
	public function insert($data='',$id=false){
		$k=$v='';
		foreach($data as $key => $value) {
			$k.=',`'.$key.'`';
			if( is_null($value) ){
				$v.=',null';
			}else{
				$v.=',"'.$this->escapeString($value).'"';
			}
		}
		$insert=$this->sqlQuery('INSERT INTO `'.$this->table.'` ('.ltrim($k,',').') VALUES ('.ltrim($v,',').')');
		//echo 'INSERT INTO `'.$this->table.'` ('.ltrim($k,',').') VALUES ('.ltrim($v,',').')'; die;
		if($id){ return mysqli_insert_id($this->connect); }
		return $insert;
	}

    //Insert & lấy ID
	public function insertGetId($data=''){
		return $this->insert($data,true);
	}


    //Update
	public function update($data='', $insert=false){
		$new = '';
		foreach($data as $key => $value) {
			if( is_null($value) ){
				$new.= ',`'. $key . '` = null';
			}else{
				$new.= ',`'. $key . '` = "'.$this->escapeString($value).'"';
			}
			$insrt[$key]=$value;
		}

		if($this->exists()){
			$sql = $this->sqlQuery('UPDATE `'.$this->table.'` SET '.ltrim($new,',').' '.$this->createWhere().'');
		}else{
			if($insert){ $sql = $this->insert($insrt); }else{ $sql = ''; }
		}
		return $sql;
	}


    //Insert dữ liệu nếu chưa có
	public function create($data=[]){
		if(!$this->exists()){
			return $this->insert($data);
		}
	}


    //Tự động tăng cột
	public function increment($column,$value=1){
		if(empty($this->where)){return;}
		$old=$this->select($column)->first()->$column ?? 0;
		return $this->update([$column=>($old+$value)]);
	}

    //Tự động giảm cột
	public function decrement($column,$value=1){
		if(empty($this->where)){return;}
		$old=$this->select($column)->first()->$column ?? 0;
		$new=$old-$value;
		if($new<0){$new=0;}
		return $this->update([$column=>$new]);
	}

    //Delete
	public function delete(){
		if(!empty($this->where)){
			return $this->sqlQuery('DELETE FROM `'.$this->table.'` '.$this->createWhere().'');
		}
	}


    //Đếm số dòng
	public function count(){
		$data  = $this->get();
		$count = count($this->items);
		return $count;
	}


    //Đếm toàn bộ
	public function total(){
		if( $result = $this->sqlQuery($this->sqlMerge(true)) ){
			$count  = mysqli_fetch_row($result)[0];
		}
		return $count ?? 0;
	}

    //Kiểm tra dữ liệu tồn tại
	public function exists(){
		return $this->total() > 0 ? TRUE : FALSE;
	}

    //Xóa toàn bộ bảng
	public function truncate(){
		$this->sqlQuery("DELETE FROM `{$this->table}`");
		$this->sqlQuery("ALTER TABLE `{$this->table}` AUTO_INCREMENT=1");
	}

    //Chuyển dữ liệu sang Json
	public function toJson(){
		return json_encode($this->items);
	}
    //Chuyển dữ liệu sang Array
	public function toArray(){
		$items=$this->items;
		if(is_array($items)){
			foreach ($items as $key => $value) {
				$array[$key]=(array)$value;
			}
		}
		return $array ?? (array)$items;
	}

	// Lấy column làm key chính
    public function keyBy($column){
        $data = [];
        foreach ($this->items as $item) {
            $data[$item->$column] = $item;
        }
        return $data;
    }

    // Chỉ lấy dữ liệu cột
    public function pluck($column){
        $data = [];
        foreach ($this->items as $item) {
            $data[] = $item->$column;
        }
        return $data;
    }

    //Lấy dữ liệu từ bảng quan hệ
    public function with($withRelationship){
        $this->withRelationship = $withRelationship;
        return $this;
    }

	function __call($method,$params){
    	//Gọi các method
		$where=[
			"whereIn", "whereNotIn", "whereBetween","whereNotBetween","whereNull","whereNotNull","whereColumn","whereDate","whereDay","whereMonth","whereYear",
			"orWhere", "orWhereIn", "orWhereNotIn", "orWhereBetween","orWhereNotBetween","orWhereNull","orWhereNotNull","orWhereDay","orWhereMonth","orWhereYear",
		];
		if(in_array($method,$where)){
			if(is_array($params[0])){
				return $this->where($params[0],"","",$method);
			}else{
				return $this->where($params[0],$params[1] ?? null,$params[2] ?? null,$method);
			}
		}
	}

    //Xuất dữ liệu $this->items (PHP 8 IteratorAggregate)
	public function getIterator(): \Traversable {
		return new \ArrayIterator($this->items ?? []);
	}

    //Truy vấn dữ liệu nhanh dạng array[] (PHP 8 ArrayAccess)
	public function offsetGet(mixed $offset): mixed {
		return $this->items[$offset] ?? false;
	}
	public function offsetExists(mixed $offset): bool {
		if(is_array($this->items) && isset($this->items[$offset])){return true;}
		return false;
	}
	public function offsetUnset(mixed $offset): void {
		unset($this->items[$offset]);
	}
	public function offsetSet(mixed $offset, mixed $value): void {}

    //Đóng kết nối database
	function __destruct(){
		if( !empty($this->connect) ){
			@mysqli_close($this->connect);
		}
	}

}//</Class>