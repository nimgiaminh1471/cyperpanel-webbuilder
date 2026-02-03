<?php
/*
* Class xử lý Model
*/

class Model implements ArrayAccess,IteratorAggregate{
    protected $table,$column,$fillable,$guarded,$insertData,$continue,$exportData,$relationshipId,$beLongsToMany,$wherePivot,$findId, $withRelationship, $withRelationshipTables;
    protected $withRelationshipId = 0;
    protected $timestamps=true;
    protected $primaryKey="id";
    protected $items     =[];
    protected $withPivot =[];
    private   $findPrimaryId="";

    
    function __construct($insertData=""){
        $this->insertData=$insertData;
    }

    //Lấy theo ID
    private function _find($id){
        $this->findId=$id;
        if(is_array($id)){
            $data = DB::table($this->table)->whereIn($this->primaryKey,$id);
            $this->items=$data->get(true);
        }else{
            $data = DB::table($this->table)->where($this->primaryKey,$id);
            $this->items=$data->first(true);
            $this->findPrimaryId=$this->items->{$this->primaryKey} ?? NULL;
            $this->continue=["table"=>$this->table,"where"=> [[$this->primaryKey, "=" ,DB::safeString($id)]] ];
        }
        return $this;
    }

    //Lấy dữ liệu từ bảng quan hệ
    private function _with($tables){
        if( is_string($tables) ){
            $tables = [$tables];
        }
        foreach($tables as $table){
            $this->withRelationshipTables[] = $table;
            new $this->$table;
        }
        $this->continue = [
            "table"            => $this->table,
            "withRelationship" => $this->withRelationship
        ];
        return $this;
    }


    //Insert & update dữ liệu
    public function save(){
        $allow       = false;
        $table       = $this->table;
        $fillable    = $this->fillable;
        $guarded     = $this->guarded;
        $timestamps  = $this->timestamps;
        $whereColumn = $this->primaryKey;
        $whereValue  = $this->findPrimaryId;
        if(is_array($this->relationshipId)){//Bảng quan hệ
            $to          = $this->relationshipId["class"];
            $class       = new $to;
            $table       = $class->table;
            $fillable    = $class->fillable;
            $guarded     = $class->guarded;
            $timestamps  = $class->timestamps;
            $whereColumn = $this->relationshipId["column"];
            $whereValue  = $this->relationshipId["value"];
        }

        $data = empty($this->insertData) ? $this->column : $this->insertData;
        //Kiểm column có cho phép ghi
        if(is_array($fillable)){
            foreach($data as $col=>$value){
            if( strlen(array_search($col,$fillable))==0 ){ $deny=1; }
            }
            if(empty($deny)){ $allow=true; }
        }else if(is_array($guarded)){
            foreach($data as $col=>$value){
            if( strlen(array_search($col,$guarded))!=0 ){ $deny=1; }
            }
            if(empty($deny)){ $allow=true; }
        }else{
            $allow=true;
        }

        if($timestamps){
            $data["updated_at"]=timestamp();
        }
        if($allow){
            if(strlen($whereValue)>0){
                // Cập nhật bảng dữ liệu
                $out = DB::table($table)->where($whereColumn,"=",$whereValue)->update($data);
                $data['id'] = $whereValue;
            }else if(is_null($whereValue)){
                // Không tìm thấy find id
            }else{
                // Insert
                unset($data['id']);
                if($timestamps){
                    $data["created_at"]=timestamp();
                }
                $out = DB::table($table)->insertGetId($data);
                $data['id'] = $out ?? false;
            }
            $this->items = (object)$data;
            return $out ?? false;
        }
    }

    //Update hàng loạt
    public function update($insertData){
        $this->insertData=$insertData;
        return $this->save();
    }

    //Insert hàng loạt
    private function _create($insertData=''){
        $this->insertData=$insertData;
        return $this->save();
    }



    //Xóa dữ liệu
    private function _destroy($id="",$idar=""){
        if(!empty($idar)){ $id=func_get_args(); }
        if(is_array($id)){
            return DB::table($this->table)->whereIn($this->primaryKey,$id)->delete();
        }else{
            return DB::table($this->table)->where($this->primaryKey,"=",$id)->delete();
        }
    }



    //Quan hệ bảng
    private function relationship($to, $foreignKey="", $localKey="", $many=false){
        $class=new $to;
        if(empty($localKey)){
            $localKey   = "{$class->table}_id";
        }
        if(empty($foreignKey)){
            $foreignKey = "id";
        }
        // Nếu gọi qua with
        if( empty($this->findId) ){
            $this->withRelationship[] = [
                'key'        => $this->withRelationshipTables[$this->withRelationshipId],
                'table'      => $class->table,
                'foreignKey' => $foreignKey,
                'localKey'   => $localKey,
                'many'       => $many
            ];
            $this->withRelationshipId++;
        }
        $findId = $this->$localKey ?? null;
        if( !empty($findId) ){
            if(empty($this->exportData)){
                $this->continue=["table"=>$class->table,"where"=> [[$foreignKey, "=" ,$findId]] ];
                if(!$many){
                    $data=DB::table($class->table)->where($foreignKey, "=" ,$findId);
                    $items=$data->first(true);
                }
            }else{
                $data=DB::table($class->table)->where($foreignKey, "=" ,$findId);
                $items=$many ? $data->get(true) : $data->first(true);
                $this->items=$items;
            }
            if(isset($items->$foreignKey)){
                $this->relationshipId = ["class"=>$to,"column"=>$foreignKey,"value"=>$items->$foreignKey];
            }
        }
        return $this;
    }

    //Quan hệ bảng 1-1
    public function hasOne($to,$foreignKey="",$localKey="id"){
        return $this->relationship($to,$foreignKey,$localKey);
    }

    //Quan hệ bảng 1-nhiều
    public function hasMany($to,$foreignKey="",$localKey="id"){
        return $this->relationship($to,$foreignKey,$localKey,true);
    }

    //Thuộc về 1 bảng
    public function beLongsTo($to,$foreignKey="id",$localKey=""){
        return $this->relationship($to,$foreignKey,$localKey);
    }









    //Quan hệ nhiều - nhiều
    public function beLongsToMany($to,$pivotTable="",$firstKey="",$secondKey=""){
        if(!empty($this->findId)>0){
            $second = new $to;
            $this->beLongsToMany["firstTable"] =$this->table;//EX: car
            $this->beLongsToMany["secondTable"]=$second->table;//EX: color
            $this->beLongsToMany["secondPrKey"]=$second->primaryKey;//EX: id (color)
            $this->beLongsToMany["pivotTable"] =empty($pivotTable) ? "{$this->beLongsToMany["firstTable"]}_{$this->beLongsToMany["secondTable"]}" : $pivotTable;//EX: car_color
            $this->beLongsToMany["firstKey"]   =empty($firstKey) ? "{$this->beLongsToMany["firstTable"]}_id" : $firstKey;//EX: car_id
            $this->beLongsToMany["secondKey"]  =empty($secondKey) ? "{$this->beLongsToMany["secondTable"]}_id" : $secondKey;//EX: color_id
        }
        return $this;
    }


    //Lấy thêm cột
    public function withPivot($column){
        $this->withPivot=func_get_args();
        return $this;
    }

    //Đặt điều kiện bảng trung gian
    public function wherePivot($column, $operator=null, $value=null,$type="where"){
        if(is_array($column)){
            $this->wherePivot[$type]=$column;
        }else{
            $this->wherePivot[$type][]=[$column,$operator,$value];
        }
        return $this;
    }
    public function wherePivotIn($column, $operator=null, $value=null){
        return $this->wherePivot($column,$operator,$value,"whereIn");
    }

    public function withTimestamps(){
        $this->withPivot[]="created_at";
        $this->withPivot[]="updated_at";
        return $this;
    }

    //Xuất dữ liệu
    public function beLongsToManyGet($continue=false){
        if(!isset($this->beLongsToMany["exists"]) && !empty($this->beLongsToMany)){
            extract($this->beLongsToMany);
            $select=["{$secondTable}.*","{$pivotTable}.{$firstKey} AS pivot__{$firstKey}","{$pivotTable}.{$secondKey} AS pivot__{$secondKey}"];
            if(!empty($this->withPivot)){
                foreach($this->withPivot as $col){
                    $select[]="{$pivotTable}.{$col} AS pivot__{$col}";
                }
            }

            if(is_array($this->findId)){
                $whereIn[]=["{$pivotTable}.{$firstKey}",$this->findId];
            }else{
                $where[]=["{$pivotTable}.{$firstKey}","=","{$this->findPrimaryId}"];
            }

            if(isset($this->wherePivot["where"])){
                foreach($this->wherePivot["where"] as $k => $w) {
                    $where[]=["{$pivotTable}.{$w[0]}",$w[1],$w[2]];
                }
            }
            if(isset($this->wherePivot["whereIn"])){
                foreach($this->wherePivot["whereIn"] as $k => $wi) {
                    $whereIn[]=["{$pivotTable}.{$wi[0]}",$wi[1]];
                }
            }

            

            $join=[$pivotTable,"{$secondTable}.{$secondPrKey}","=","{$pivotTable}.{$secondKey}","INNER"];
            if($continue){
                $this->continue=[
                    "table"   =>$secondTable,
                    "where"   =>$where??"",
                    "whereIn" =>$whereIn??"",
                    "join"    =>$join,
                    "select"  =>$select,
                ];
            }else{

                $items=DB::table($secondTable)->select($select)->join($join)->where($where)->whereIn($whereIn??"")->get(true);
                foreach($items as $pkey=>$item){
                    foreach($item as $key=>$value){
                       if(preg_match('/^pivot__(.+)/', $key)){
                            $nKey[str_replace("pivot__","",$key)]=$value;
                            $items[$pkey]->pivot=(object)$nKey;
                            unset($item->$key);
                       }
                    }
                }
                $this->beLongsToMany["exists"]=1;
                $this->items=$items;
            }
       }
    }

    //Thêm bản ghi beLongsToMany
    public function attach($ids){
        extract($this->beLongsToMany);
        foreach($ids as $id=>$data){
            if(!is_array($data)){
                $id=$data;
                $data=[];
            }
            if(array_search("created_at",$this->withPivot)){
                $data["created_at"]=timestamp();
                $data["updated_at"]=timestamp();
            }
            DB::table($pivotTable)->insert( array_merge([$firstKey=>$this->findPrimaryId, $secondKey=>$id],$data) );
        }
    }

    //Xóa bản ghi beLongsToMany
    public function detach($ids=""){
        $ids = is_array($ids) ? $ids : func_get_args();
        extract($this->beLongsToMany);
        if(empty($ids)){
            DB::table($pivotTable)->where($firstKey,$this->findPrimaryId)->delete();
        }else{
            DB::table($pivotTable)->where($firstKey,$this->findPrimaryId)->whereIn($secondKey,$ids)->delete();
        }
    }

    //Đồng bộ bản ghi beLongsToMany
    public function sync($ids){
        extract($this->beLongsToMany);
        foreach($ids as $id => $data){
            $exclude[]=is_array($data) ? $id : $data;
           $this->syncWithoutDetaching($ids);
        }
        DB::table($pivotTable)->where($firstKey,$this->findPrimaryId)->whereNotIn($secondKey,$exclude)->delete();
    }
    public function syncWithoutDetaching($ids){
        extract($this->beLongsToMany);
        foreach($ids as $id=>$data){
            if(!is_array($data)){
                $id=$data;
                $data=[];
            }
            if(array_search("updated_at",$this->withPivot)){
                $data["created_at"]=timestamp();
                $data["updated_at"]=timestamp();
            }
            DB::table($pivotTable)->where($firstKey,$this->findPrimaryId)->where($secondKey,$id)->update( array_merge([$firstKey=>$this->findPrimaryId, $secondKey=>$id],$data) ,true);
        }
    }

    //Cập nhật bản ghi beLongsToMany
    public function updateExistingPivot($id,$attr){
        extract($this->beLongsToMany);
        if(array_search("updated_at",$this->withPivot)){
            $attr["updated_at"]=timestamp();
        }
        DB::table($pivotTable)->where($firstKey,$this->findPrimaryId)->where($secondKey,$id)->update( array_merge([$firstKey=>$this->findPrimaryId, $secondKey=>$id],$attr));
    }








    //Chuyển dữ liệu sang Json
    public function toJson(){
        $this->beLongsToManyGet();
        return json_encode($this->items);
    }
    //Chuyển dữ liệu sang Array
    public function toArray(){
        $this->beLongsToManyGet();
        $items=$this->items;
        if(is_array($items)){
            foreach ($items as $key => $value) {
                $array[$key]=(array)$value;
            }
        }
        return $array ?? (array)$items;
    }

    //Truy vấn dữ liệu nhanh dạng array[]
    public function offsetGet($offset){
        $this->beLongsToManyGet();
        return $this->items[$offset] ?? false;
    }
    public function offsetExists($offset){
        $this->beLongsToManyGet();
        if(is_array($this->items) && isset($this->items[$offset])){return true;}
        return false;
    }
    public function offsetUnset($offset){
        $this->beLongsToManyGet();
        unset($this->items[$offset]);
    }
    public function offsetSet($offset, $value){}



    //Xuất dữ liệu $this->items
    public function getIterator(){
        $this->beLongsToManyGet();
        return new ArrayIterator($this->items);
    }




	//Lưu các cột tương ứng giá trị
	function __set($name,$value){
		$this->column[$name]=$value;
	} 

	//Lấy thông số $data->item
	function __get($name){
		if(isset($this->items->$name)){
			return $this->items->$name;
		}else{
            if(method_exists($this, $name)){
                $this->exportData=1;
                return $this->{$name}();
            }
		}
	}
	function __isset($name){
		if(isset($this->items->$name)){ return true; }
	}


    //Gọi các phương thức
	public function __call($method,$params){
		$this->beLongsToManyGet(true);
		return DB::table($this->continue["table"]??$this->table)
		->select($this->continue["select"] ?? "")
        ->where($this->continue["where"] ?? "")
        ->whereIn($this->continue["whereIn"] ?? "")
        ->join($this->continue["join"] ?? "")
        ->with($this->continue["withRelationship"] ?? [])
        ->{$method}(...$params);
	}

	//Khởi tạo lại class
    public static function __callStatic($method,$params){
        if(in_array($method, ["find", "create", "destroy", "with"])){
            $method="_".$method;
        }
        return (new static)->$method(...$params);
    }
	




}//</Class>