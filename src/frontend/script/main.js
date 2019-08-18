/* Constants */
const dicts = {
    'henbane-borine': 'هەنبانەبۆرینە (کوردیی ناوەندی-فارسی)',
    xal: 'خاڵ (کوردیی ناوەندی-کوردیی ناوەندی)',
    kawe: 'کاوە (فارسی-کوردیی ناوەندی)',
    bashur: 'باشوور (کوردیی گۆرانی-فارسی)',
    kameran: 'کامەران (کوردیی کورمانجی-کوردیی کورمانجی)',
    //dictio: 'دیکتیۆ (ئینگلیزی-کوردیی ناوەندی)',
    e2k: 'e2k (ئینگلیزی-کوردیی ناوەندی)',
    zkurd: 'zkurd (ئینگلیزی-کوردیی ناوەندی)',
};
const dicts_selected_storage_name = 'dicts_selected';
const dicts_selected_storage = isJSON(localStorage.getItem(dicts_selected_storage_name));
const dicts_selected = dicts_selected_storage || [ 'henbane-borine' , 'kawe' , 'xal' ];
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

function lookup ()
{
    const q_el = document.getElementById(q_el_id);
    const dicts = get_selected_dicts();
    const dicts_req = dicts.join(',');
    const result_el = document.getElementById(result_el_id);
    const q = encodeURIComponent(q_el.value.trim());
    const request = `src/backend/lookup.php?q=${q}&dicts=${dicts_req}&output=json`;
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
    
    getUrl(request, function(response) {
	response = isJSON(response);
	if(! response) return;
	
	let toprint = '';
	for(const i in response)
	{
	    const res = response[i];
	    toprint += `<h2>${dict_to_kurdish(i)}</h2>`;
	    if(!res.length)
		toprint += `<p><i>(نەدۆزرایەوە)</i></p>`;
	    for(const j in res)
	    {
		const r = res[j];
		toprint += `<p>- ${r}</p>`;
	    }
	}
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
});
