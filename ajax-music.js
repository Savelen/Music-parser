let search = window.location.search;
if(search){
	let reg = /=(.*)/;
	search = reg.exec(search)[1];
	reg = /(\+)/g;
	search = search.replace(reg, ' ');
	let search_box = document.querySelector('input');
	search_box.value = search;
	go_ajax('GET','/parser.php?search='+search);
}

let button_search = document.querySelector('button.query_search');
let button_next = document.querySelector('button.next');
button_next.addEventListener('click',()=>{
	go_ajax('POST','/parser.php',button_next.value)
})
function go_ajax(metod,url,page) {
	let request = new XMLHttpRequest();
	request.open(metod,url,true);
	if(metod == "POST"){
		request.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	}
	request.addEventListener('readystatechange',()=>{
		if((request.status == 200)&&(request.readyState == 4)){
			let response = JSON.parse(request.responseText);
			let name_arr = response.name;
			let url_arr = response.url;
			let next = response.next;

			url_arr.forEach((url, i) =>{
				//контейнер для трека и названия
				let container = document.createElement("div");
				container.class = "music__unit";
				container.style.display = "inline-block";
				container.style.width = "350px";
				container.style.paddingLeft = "17%";
				// имя трека
				let name = document.createElement("p");
				name.textContent = name_arr[i].replace('&ndash;','\u2013');
				// трек
				let audio_tag = document.createElement("audio");
				audio_tag.controls = "control";
				audio_tag.preload = "metadata";
				let source_tag = document.createElement("source");
				source_tag.src = url;
				source_tag.type = "audio/mpeg";
				audio_tag.appendChild(source_tag);
				// добавление всего в контейнер
				container.appendChild(name);
				container.appendChild(audio_tag);
				// вывод порсинга
				let doc = document.querySelector('div.music');
				doc.appendChild(container);
			});
			if (next){
				next = next.replace('&amp;','\u0026');
				button_next.value = next;
				button_next.hidden = false;
			}
		}
	});

	if(metod == "POST"){
		request.send("page="+page);
	}
	else{
		request.send();
	}

}
