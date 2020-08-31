<?php
if($curl = curl_init()){
	if(!isset($_POST['page'])){
		$find = $_GET["search"]; // поисковой запрос
		curl_setopt($curl,CURLOPT_URL,'https://zaycev.net/search.html?query_search='.$find);
	}
	else {
		curl_setopt($curl,CURLOPT_URL,'https://zaycev.net/'.$_POST['page'].'&query_search='.$_POST["query_search"]);
	}
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($curl,CURLOPT_HEADER,true);
	curl_setopt($curl,CURLOPT_FOLLOWLOCATION,false);
	curl_setopt($curl,CURLOPT_COOKIESESSION,false);
	curl_setopt($curl,CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
	curl_setopt($curl,CURLOPT_TIMEOUT,30);

	$html = curl_exec($curl);
	//очиска ресурсов
	curl_close($curl);

	//достаём div с данными о треке
	preg_match('|\<div class="musicset-track-list__items"\>(\n.+\</div\>)+\</div\>|', $html,$song_list);
	//достаём имя трека
	preg_match_all('~(Прослушать .*)(?="\sdata-rbt)~',$song_list[0],$music_name);
	// Json запрос для трека
	preg_match_all('~/musicset/play/(\d|\w)*/\d*\.json~',$song_list[0],$request);

	$curl = curl_init();
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($curl,CURLOPT_FOLLOWLOCATION,true);
	curl_setopt($curl,CURLOPT_TIMEOUT,30);
	// Отправляем запрос API
	foreach ($request[0] as $url) {
		curl_setopt($curl,CURLOPT_URL,'https://zaycev.net'.$url);

		$answer_buf = curl_exec($curl);
		$answer .= ($answer_buf."\n");
		// time_sleep_until(time()+1.0);
	}
	curl_close($curl);
	//Достаём url трека
	preg_match_all('~https://cdndl.zaycev.net/.*?(?=")~',$answer,$music_url);
	// следующая стр.
	preg_match('~(\<a href="search\.html\?page=.{1,120})(?=\sclass="pager__item pager__item_last")~',$html,$next);
	preg_match('~"(.*?)"~',$next[0],$next);
	//отправляем
	echo json_encode(['name' => $music_name[0], 'url' => $music_url[0], 'next' => $next[1]]);

}
else echo 'error:'.curl_error($error);
?>