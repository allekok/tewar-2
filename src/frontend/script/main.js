/* Constants */
const dicts = {
	'henbane-borine': 'هەنبانەبۆرینە (کوردیی ناوەندی-فارسی)',
	xal: 'خاڵ (کوردیی ناوەندی-کوردیی ناوەندی)',
	kawe: 'کاوە (فارسی-کوردیی ناوەندی)',
	bashur: 'باشوور (کوردیی گۆرانی-فارسی)',
	kameran: 'کامێران (کوردیی کورمانجی-کوردیی کورمانجی)',
	e2k: 'e2k (ئینگلیزی-کوردیی کورمانجی)',
	zkurd: 'zkurd (ئینگلیزی-کوردیی ناوەندی)',
	// dictio: 'دیکتیۆ (ئینگلیزی-کوردیی ناوەندی)',
};
const dicts_selected_storage_name = 'dicts_selected';
const dicts_selected_storage = isJSON(
	localStorage.getItem(dicts_selected_storage_name));
const dicts_selected = dicts_selected_storage ||
      [ 'henbane-borine' , 'xal' ];
const dicts_el_id = 'dicts';
const q_el_id = 'q';
const result_el_id = 'result';
const form_el_id = 'frm';

/* Functions */
function getUrl (url, callback)
{
	const client = new XMLHttpRequest();
	client.open('get', url);
	client.onload = function ()
	{
		callback(this.responseText);
	}
	client.send();
}

function postUrl (url, request, callback)
{
	const client = new XMLHttpRequest();
	client.open('post', url);
	client.onload = function ()
	{
		callback(this.responseText);
	}
	client.setRequestHeader(
		"Content-type","application/x-www-form-urlencoded");
	client.send(request);
}

function lookup ()
{
	const q_el = document.getElementById(q_el_id);
	const dicts = get_selected_dicts();
	const dicts_str = dicts.join(",");
	const result_el = document.getElementById(result_el_id);
	const q = encodeURIComponent(q_el.value.trim());
	const url = 'src/backend/lookup.php';
	const loading = '<div class="loading"></div>';

	if(!q)
	{
		q_el.focus();
		return;
	}
	if(dicts.length == 0)
	{
		result_el.innerHTML = '<p>(تکایە فەرهەنگێک هەڵبژێرن)</p>';
		return;
	}

	// Loading animation
	result_el.innerHTML = loading;

	// Save selected dicts
	save_selected_dicts(dicts);

	const request = `q=${q}&dicts=${dicts_str}&output=json&n=10`;
	getUrl(`${url}?${request}`, function(response) {
		response = isJSON(response);
		if(! response) return;
		
		let toprint = `<p>گەڕان ${response['time']} چرکەی خایاند.</p>`;
		delete response['time'];
		
		let wm_html = '';
		for(const i in response)
		{
			let d = response[i][0],
			    w = response[i][1],
			    m = response[i][2];
			if(m) wm_html += `<p>${ckNum(String(Number(i)+1))}. <b>${w}</b>: <i class='dict-tag'>${dict_to_kurdish(d)}</i></p><p>${m}</p>`;
		}
		toprint += wm_html ? wm_html :
			'<p><i style="color:#555">(نەدۆزرایەوە)</i></p>';
		
		result_el.innerHTML = toprint;
	});
}

function isJSON (string)
{
	try
	{
		return JSON.parse(string);
	}
	catch (e)
	{
		console.log(e);
		return false;
	}
}

function get_selected_dicts ()
{
	let selected = [];
	
	const dicts_el = document.getElementById(dicts_el_id);
	const dicts_checks = dicts_el.querySelectorAll('input[type=checkbox]');
	dicts_checks.forEach(function (o) {
		const d = o.id;
		if(o.checked && dict_valid(d))
			selected.push(d);
	});
	
	return selected;
}

function save_selected_dicts (selected_dicts)
{
	localStorage.setItem(dicts_selected_storage_name,
			     JSON.stringify(selected_dicts));
}

function dict_to_kurdish (dict)
{
	dict = dict.toLowerCase();
	try
	{
		return dicts[dict];
	}
	catch (e)
	{
		console.log(e);
		return false;
	}
}

function dicts_print ()
{
	let dicts_html = '';
	for (const i in dicts)
	{
		dicts_html += `<div><input type="checkbox" id="${i}" 
${dicts_selected.indexOf(i) !== -1 ? 'checked' : ''}
><label for="${i}">${dicts[i]}</label></div>`;
	}
	document.getElementById(dicts_el_id).innerHTML = dicts_html;
}

function dict_valid (dict)
{
	for(const i in dicts)
		if(i == dict) return dict;
	
	return false;
}

function clear_screen ()
{
	const result_el = document.getElementById(result_el_id);
	const q_el = document.getElementById(q_el_id);
	
	result_el.innerHTML = '';
	q_el.value = '';

	q_el.focus();
}

function process_url ()
{
	let query = window.location.toString();
	query = query.substr(query.indexOf('?'));
	if(query.substr(0, 3) != "?q=")
		return;
	const q_el = document.getElementById(q_el_id);
	q_el.value = decodeURIComponent(query.substr(3));
	lookup();
}

function ckNum (s) {
	const en = ['0','1','2','3','4','5','6','7','8','9'],
	      ck = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];
	for (const i in en)
		s = s.replace(new RegExp(en[i]), ck[i]);
	return s;
}

/* Events */
window.addEventListener('load', function () {
	// Dicts
	dicts_print();

	// Form
	const form_el = document.getElementById(form_el_id);
	form_el.addEventListener('submit', function(e) {
		e.preventDefault();
		lookup();
	});

	// Header
	const header_h1_el = document.querySelector('header h1');
	header_h1_el.addEventListener('click', function() {
		clear_screen();
	});

	// Process URL
	process_url();
});
