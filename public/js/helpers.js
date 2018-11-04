// JavaScript Helper (.js)
// =================================================================================================== //
// To use your own customized Java script code do the following ...
// Either creat your own helpers.js file or simply download them from somewhere like
// https://cdnjs.cloudflare.com
// Place the helpers.js file Within your public/js directory.
//
// Now add <script type="text/javascript" src="{{asset('js/helpers.js')}}" async></script>
// to any xxx.blade.php file to have it included in that view.
// 
// To include in ALL views, place the same statement in your main app file -> app.blade.php
// Note: It is apparently more efficient to use the async option so that the rest of the page loads
// whilst the script code is being fetched. Most of the time this will be fine. But it may break your
// page if your code is expected to alter HTML during load. 
//
// Now this will be available to all your blades.
// =================================================================================================== //

// =================================================================================================== //
// Trace
function _T(data, trace=true) {
	if (trace) {
		if (_T.caller.caller) {
			console.log(_T.caller.caller, _T.caller, arguments);
		} else {
			console.log(_T.caller, arguments);
		}
	}
	return trace;
}

// =================================================================================================== //
// Lets us use "_(id)" as a shortcut for document.getElementById(id)
function _(el) {
	return document.getElementById(el);
}

// =================================================================================================== //
// Toggles the sort direction indicator on index views   
// place this at the end of your view mySortArrow({!! json_encode($sort) !!});
function mySortArrow(sort='id'){
	_T(sort);
	sort=sort==null ? 'id' : sort;
	sortCol=sort.substr(0, 1);
	sortDir=sort.length>1 ? sort.substr(1, 1) : 'a';
	divI=_('sort-'+sortCol);
	if (sortDir=='d') {
		divI.innerHTML='<i class="fas fa-long-arrow-alt-up mr-1"></i>';
	} else {
		divI.innerHTML='<i class="fas fa-long-arrow-alt-down mr-1"></i>';
	}	
}

// ========================================================================== //
// Saves the view (list or lightbox) to session storage
// Retrieves the view from session storage and sets accordion elements
// place this at the end of your view myView('file', 'accordionf2', 'accordionf1');
function myView(zone, id1, id2=false) {
	_T(zone, id1, id2);
	if (id2) {												// Session Load			
		var view=sessionStorage.getItem('view_'+zone);
		if (view=='true') {
			var elview2=_(id2);
			elview2.classList.remove('show');
			var elview1=_(id1);
			elview1.classList.add('show');					
		}
	} else {												// Click
		var elView=_(id1);
		var view=elView.classList.contains('show') ? 'false' : 'true';
		sessionStorage.setItem('view_'+zone, view);
	}
}

// ========================================================================== //
// Saves the volume to session storage
// Retrieves the volume from session storage and sets All video/audio elements
// NS01 NS02 NS03  
function myVolume(vol=false) {
	_T(vol);
	elVids=document.getElementsByTagName('video');							// NS03	
	for (i=0; i<elVids.length; ++i) {										// NS03
		elVid=elVids[i];													// NS03 
		if (elVid) {														// NS02
			if (vol) {
				if (vol=='session') {
					vol=sessionStorage.getItem('volume');
				}
				if (vol>=0 && vol<=1) { elVid.volume=vol; }					// NS01
			} else {
				vol=elVid.volume;
			}
			sessionStorage.setItem('volume', vol);
			//console.log('Volume='+sessionStorage.getItem('volume'));
		}
	}
}

// ========================================================================== //
// For debug only for <img> load errors
// Debug:      onerror="this.onerror=null; myImgDebug(this);"
// Production: onerror="this.onerror=null; this.src='{{ asset('favicon.ico') }}';" 
function myImgDebug($this) {
	_T($this);
	var elP=$this.parentNode;
	var elC2=document.createElement('iframe');
	elC2.src=$this.src;
	elC2.classList=$this.classList;
	elC2.style.width='100%';
	elC2.style.maxHeight='2000px';
	elP.replaceChild(elC2, $this); 
}

// ========================================================================== //
// Hide / Show elements - input is a list of ids OR element objects
function myHideShowElement(tagIDs={}, op='none') {
	_T(tagIDs, op);
	for (var i=0; i<tagIDs.length; i++){
		var el=tagIDs[i];
		if (typeof el === 'string') {
			el=_(el);
		}	
		if (el) {
			if (op=='block') { el.style.display='block';}
			else 			 { el.style.display='none'; }	
		}	
	}	
}

// ========================================================================== //
// Load all files using Ajax to manage a progress bar
// myUpload({
// 		formID: 				'myForm1',									
//		url: 					"{{ route('files.update', $file->id) }}",	
//		formInputFileName: 		'files[]',
//		formMsgsID:				'myMsgs1',
//		formProgressBarID:		'myPbar1',									
// });
function myUpload(p) {
	_T(p);
	formRequest=new XMLHttpRequest(); 
	elForm=_(p.formID);
	elForm.addEventListener('submit', function (e) {
		e.preventDefault();
		formData=new FormData(elForm);
		formRequest.open('post', p.url);
		formRequest.setRequestHeader('X-Requested-With', 'XMLHttpRequest'); // Allows PHP to see request as AJAX
		formRequest.upload.addEventListener('progress', function(event) {
			myUploadInProgress(event, p);
		});
		formRequest.addEventListener('load', function(event) {
			myUploadTransferComplete(event, p);
		});
		formRequest.send(formData);
	});
};

function myUploadInProgress(data, p) {
	_T(data, p);
	//console.log(p.formProgressBarID);
	var percent=(data.loaded / data.total) * 100;
	if (data.lengthComputable) {
		elPbar=_(p.formProgressBarID);
		elPbar.style.backgroundColor='#007bff'; // Primary
		percent=Math.round((data.loaded/data.total)*100);
		elPbar.style.width=percent+'%';
		elPbar.innerHTML=percent+'%';
	}
}

function myUploadTransferComplete(data, p) {
	_T(data, p);
	//console.log(data.currentTarget.response);
	json=JSON.parse(data.currentTarget.response);
	elPbar=_(p.formProgressBarID);
	if (json.countBad>0) {
		elPbar.style.backgroundColor='#dc3545';	// Danger
		elPbar.innerHTML='Server Upload Error';
	} else {
		elPbar.style.backgroundColor='#28a745'; // Success
		elPbar.innerHTML='Server Upload OK';
	}
	if (json.hasOwnProperty('url')) { window.location.href=json.url; }
	else { location.reload(false); }
}
