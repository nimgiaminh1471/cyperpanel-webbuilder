<?php
/*
# Cập nhật sitemap
*/
namespace classes;
use models\Posts;
use models\PostsCategories;
use models\Files;
use Storage;

class Sitemap{
	public static function update(){
		$data[HOME."/"]=["time"=>time()];
		//Lấy toàn bộ chuyên mục
		foreach(PostsCategories::orderBy("created_at", "DESC")->all() as $c){
			$data[HOME."/".$c->link]=["time"=>timestamp($c->created_at)];
		}

		//Lấy toàn bộ bài viết
		foreach(Posts::where("status", "public")->orderBy("updated_at", "DESC")->all() as $p){
			$data[HOME."/".$p->link]=["time"=>timestamp($p->updated_at)];

			//Ảnh trong bài viết
			foreach(Files::where("posts_id", $p->id)->where("type", "image")->get() as $i){
				$data[HOME."/".$p->link]["image"][]=HOME."".$i->folder."".$i->name;
			}
		}
		$out='<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
		foreach($data as $url=>$params){
			$out.='
<url>
<loc>'.$url.'</loc>
<lastmod>'.date("c", $params["time"]).'</lastmod>
';
			foreach($params["image"]??[] as $img){
				$out.='<image:image>
<image:loc>'.$img.'</image:loc>
</image:image>
';
			}				
			$out.='</url>
			';
		}
		$out.='
</urlset>';
$robots='User-agent: *
'.(Storage::option("website_robots",1)==1 ? 'Allow: *' : 'Disallow: /').'
Sitemap: '.HOME.'/sitemap.xml';
		file_put_contents(PUBLIC_ROOT."/robots.txt", $robots);
		file_put_contents(PUBLIC_ROOT."/sitemap.xml", $out);
	}
}