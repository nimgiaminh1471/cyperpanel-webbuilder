<?php

$document=[
"Hẹn giờ chạy lệnh"=>'
//Lưu ý: phải có người truy cập thì lệnh mới chạy
global $Schedule;
echo $Schedule->i( function($text){
	echo $text;
},["Chạy lệnh mỗi phút 1 lần"] );

echo $Schedule->h( function(){
	echo "Chạy lệnh mỗi giờ 1 lần";
});

echo $Schedule->d( function(){
	echo "Chạy lệnh mỗi ngày 1 lần";
});

echo $Schedule->W( function(){
	echo "Chạy lệnh mỗi tuần 1 lần";
});

echo $Schedule->m( function(){
	echo "Chạy lệnh mỗi tháng 1 lần";
});

echo $Schedule->y( function(){
	echo "Chạy lệnh mỗi năm 1 lần";
});
',
"Storage: Thao tác từ bảng lưu trữ"=>'
<?php
$dataUpdate=[
	"key_1"=>"giá trị 1",
	"key_2"=>"giá trị 2"
];
Storage::update("option_name", $dataUpdate);//Lưu giá trị
Storage::option_name();//Lấy toàn bộ option_name trong bảng storage
echo Storage::option_name("key_1", "mặc định khi key_1 không tồn tại");//giá trị 1
Storage::delete("option_name");//Xóa toàn bộ dữ liệu của option_name
Storage::delete("option_name", "key_2");//Xóa key_2 khỏi option_name

$data2=[
	"key_1"=>["sub1"=>1,"sub2"=>2],
	"key_2"=>["sub1"=>1,"sub2"=>2]
];
Storage::update("option_name", $data2, false);//Không cập nhật sub1,sub_2

$data3=[
	"key_1"=>["sub1"=>1,"sub2"=>2],
];
Storage::update("option_name", $data3, null);//Không giữ key_2
',

"PHPMailer: Gửi email"=>'
#####Cấu hình Gmail:
1. Tắt xác minh 2 bước:         https://myaccount.google.com/signinoptions/two-step-verification/enroll-welcome?pli=1
2. Bật Allow less secure apps:  https://myaccount.google.com/lesssecureapps
3. Bật Display Unlock Captcha:  https://accounts.google.com/DisplayUnlockCaptcha

<?php
//Gửi email
use system\mailer\WebMail;

$sendMail = WebMail::send([
	"To"          => ["email1@gmail.com","email2@gmail.com","email3@yahoo.com"],
	"Subject"     => "Tiêu đề email",
	"Body"        => "Nội dung email <br/> <b>Hỗ trợ các thẻ HTML</b>",
	"Attachments" => ["/var/path/File1.jpg","/var/path/File2.jpg"],
	"ReplyTo"     => "myemail@gmail.vn"
]);
if(empty($sendMail)){
	echo "Đã gửi mail thành công";
}else{
	echo "Gửi thất bại: $sendMail";
}
',

"Paginate: Phân trang"=>'
<?php
//Phân trang nhanh

$data = DB::table("tên_bảng")
->select("*")
->where("id",">","1")
->orderBy("id","DESC")
->paginate(10);

foreach($data as $key){
	echo $key->id;//hiện cột id
}

echo $data->links([
"class"=>"center",
"next" => \'<i class="fa fa-arrow-right"></i> Sau\',
"prev" => \'<i class="fa fa-arrow-left"></i> Trước\'
]);
',
"Tạo route"=>'
##### Hướng dẫn tạo trang (VD muốn tạo: domain/test):

B1: tạo thư mục test trong thư mục apps.
B2: truy cập domain/test - sau đó hệ thống sẽ tự tạo thư mục controllers,views, @Route.php trong thư mục test


<?php
//Các kiểu route
Route::link("/path/{gia_tri1}/{gia_tri2?}","ControllerName@function");//Dạng link: domain/path
Route::get("Test","ControllerName@function");//Khi có $_GET["Test"]
Route::post("Test","ControllerName@function");//Khi có $_POST["Test"]
',
"Model Eloquent ORM"=>'
<?php

//Tìm id = 1
User::find(1);

//Tìm nhiều id
User::find([1,2,3,4]);

//Lấy toàn bộ bảng
User::all();

//Xóa id = 1
User::destroy(1);
hoặc
User::delete(1);
//Xóa nhiều id
User::destroy([1,2,3,4]);

//Insert data
$db=new User;
$db->email="email@gmail.com";
$db->name="Name";
$db->save();
//Insert nhanh
$data=["email"=>"email@gmail.com","name"=>"Name"];
User::create($data);

//Update id = 1
$db=User::find(1);
$db->email="newemail@gmail.com";
$db->name="New Name";
$db->save();
//Update nhanh id = 1
$data=["email"=>"newemail@gmail.com","name"=>"New Name"];
$db=User::find(1);
$db->update($data);

//Xóa trống bảng
User::truncate();
',


"QueryBuilder: lệnh cơ bản"=>'
<?php

//Distinct
DB::table("options")->select("value","key")->distinct()->where("id", ">", "0")->get();

//OrderBy
DB::table("test")->where("id",">","0")->orderBy("id","DESC")//orderBy 1 cột
DB::table("test")->where("id",">","0")->orderBy([ ["id","ASC"], ["key","DESC"] ])//orderBy nhiều cột

//Select
DB::table("test")->select("*")//chọn toàn bộ
DB::table("test")->select("column1","column2","column3")//chọn từng cột

//Where hoặc orWhere
DB::table("test")
	->where("id",">","0")
	->orWhere("id", "=", "10")
	->get();
DB::table("test")
	->where([ ["id",">","0"],["time",">","10"],["content", "LIKE", "%tim%"] ])
	->orWhere([ ["id",">","0"],["time",">","10"],["content", "LIKE", "%tim%"] ])
	->get();//Nhiều điều kiện

//Group & having
DB::table("users")
->groupBy("id")
->having("id", ">", 100)
->get();

//WhereIn - orWhereIn - whereNotIn - orWhereNotIn
DB::table("test")
	->whereIn("id", [1,3,4,6])
	->whereNotIn("id", [1,3,4,6])
	->orWhereIn("id", [1,3,4,6])
	->orWhereNotIn("id", [1,3,4,6])
	->get();

//whereBetWeen - whereNotBetWeen
DB::table("test")
	->whereBetWeen("id", [1,10])
	->whereNotBetWeen("id", [1,10])
	->orWhereBetWeen("id", [1,10])
	->orWhereNotBetWeen("id", [1,10])
	->get();

//whereNull - whereNotNull
DB::table("test")
	->whereNull("column")
	->whereNotNull("column")
	->orWhereNull("column")
	->orWhereNotNull("column")
	->get();

//whereColumn
DB::table("test")
	->whereColumn("created_at", "=", "updated_at")
	->get();

//whereDate
DB::table("test")
	->whereDate("created_at", "2019-12-31")
	->get();
//whereDay-Month-Year
DB::table("test")
	->whereDay("created_at", "31")
	->whereMonth("created_at", "12")
	->whereYear("created_at", "2019")
	->get();


//Create dữ liệu (nếu chưa có sẽ insert)
$data = DB::table("tên_bảng")
	->where("id","=","1")
	->create(
		["id"=>"1", "name"=>"Tên mới", "email"=>"new@gmail.com"]
	);//Nếu không tồn tại id = 1 sẽ tự insert

//Xóa dữ liệu
$data = DB::table("tên_bảng")
	->where("id","=","1")
	->delete();//xóa nếu id = 1

//Insert dữ liệu
$data = DB::table("tên_bảng")
	->insert(
		["name"=>"Tên", "email"=>"test@gmail.com"]
	);

//Update dữ liệu
$data = DB::table("tên_bảng")
	->where("id","=","1")
	->update(
		["name"=>"Tên mới", "email"=>"new@gmail.com"]
	);//Cập nhất nếu id=1

//Update dữ liệu (nếu không tồn tại sẽ insert)
$data = DB::table("tên_bảng")
	->where("id","=","1")
	->update(
		["id"=>"1", "name"=>"Tên mới", "email"=>"new@gmail.com"]
	,true);//true = Nếu không tồn tại id = 1 sẽ tự insert
',

"QueryBuilder: lấy dữ liệu"=>'
<?php

//Lấy dữ liệu (nhiều)
$data = DB::table("tên_bảng")
->select("*")
->where("id",">","1")
->orderBy("id","DESC")
->limit(10)
->get();

foreach($data as $key){
	echo $key->id;//hiện cột id
}

echo $data->count();// Đếm số dòng dữ liệu lấy được
echo $data->total();// Đếm tổng số dòng dữ liệu
echo $data->exists();// Kiểm tra dữ liệu có tồn tại hay không (TRUE = tồn tại) (FALSE = không tồn tại)




//Lấy dữ liệu (một)
$data = DB::table("tên_bảng")
->select("*")
->where("id","=","1")
->first();//hoặc last()
echo $key->id;//hiện cột id

echo $data->value("id");//hiện luôn giá trị id


$table = DB::table("tên_bảng");
echo $table->min("cột");//Lấy giá trị nhỏ nhất
echo $table->max("cột");//Lấy giá trị lớn nhất
echo $table->avg("cột");//Lấy giá trị trung bình
echo $table->sum("cột");//Cộng giá trị của cột
',

"QueryBuilder: Group & Having"=>'
<?php
//Group & Having
$data = DB::table("bills")
->select("product", "cate", "SUM(price) AS price")
->where("id",">","0")
->groupBy("cate")
->having("price",">","1000")
->orderBy("price","DESC")
->limit(10);


foreach($data->get() as $key){
	echo $key->product.": ".$key->price;//hiện cột id
}
',

"QueryBuilder: Join table"=>'
<?php
/*
# Join bảng
*/

//Join 1 bảng - cate sang topic
$data = DB::table("topic")
->select("topic.*","cate.name as cate_name")
->join("cate", "topic.cate_id", "=", "cate.id")
->where("topic.id",">","0")
->orderBy("topic.id","DESC")
->limit(10)
->get();

foreach($data as $key){
	echo $key->id;//hiện cột id
}


foreach($data as $key){
	echo $key->id;//hiện cột id
}
',

"QueryBuilder: Union"=>'
<?php

//Union 2 bảng options,topic - unionAll cũng tương tụ
$options = DB::table("options")
->select("id","key")
->where("id",">","1")
->limit(10)
->union();

$data = DB::table("topic")
->select("id","key")
->where("id",">","1")
->union($options)
->limit(10)
->get();


foreach($data as $key){
	echo $key->key;//hiện cột key
}




//Union nhiều bảng options,topic,cate - unionAll cũng tương tụ
$options = DB::table("options")
->select("id","key")
->where("id",">","1")
->limit(10)
->union();
$cate = DB::table("cate")
->select("id","key")
->where("id",">","1")
->limit(10)
->unionAll();

$data = DB::table("topic")
->select("id","key")
->where("id",">","1")
->union([$options,$cate])
->limit(10)
->get();


foreach($data as $key){
	echo $key->key;//hiện cột key
}
',

"Blade template"=>'
@php
	$bien="giá trị";
@endphp

//echo ra $bien (Mã hóa HTML)
{{$bien}}

//echo ra $bien (Cho phép HTML)
{!!$bien!!}

//Nếu dùng JS framework như angular: cần đổi {{angular-content}} sang @{{angular-content}}
@{{angular-content}}

{{--Phần comment--}}

//For
@for($i=0; $i < 10; $i++)
	<p>Vòng lặp for: {{ $i }}</p>
	@if($i==8)
		Dừng lặp
		@break
	@endif
@endfor

//Foreach
@php
$array=["Thử","nào"];
@endphp

@foreach($array as $test)
	@php
		$i++;
	@endphp

	@if($i==1)
		Bỏ qua
	@continue

	@else if($i==2)
		Dừng vòng lặp
	@break

	@endif

	{{$test}} <br/>
@endforeach

//Switch
@php
	$id="hi";
@endphp
@switch($id)
	@case("0")
		id = 0
	@break

	@case("hi")
		id = hi
	@break

	@default
		id = khác
@endswitch

//While loop
@php
	$id = 0;
@endphp
@while($id < 10)
	<p>Vòng lặp while: {{$id}}.</p>
	@php
		$id++;
	@endphp
@endwhile




//Do while loop
@php
	$i = 1;
	do{
		echo $i;
		$i++;
	}while ($i <= 10);
@php

//Json encode
@php
$array=["Thử","nào"];
@endphp
<script>
var json = @json($array);
</script>
',

];