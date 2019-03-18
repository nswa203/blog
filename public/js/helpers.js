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

// ========================================================================== //
// Trace
function _T() {
	trace = true;
	if (trace) {
		if (_T.caller.caller) {
			console.log(_T.caller.caller, _T.caller, arguments);
		} else {
			console.log(_T.caller, arguments);
		}
	}
	return trace;
}

// ========================================================================== //
// Sets up constants required by myImagAll() myImage() myFile()  
// Should be called first to make myImageVars available Globally
// Best to place your own custom copy at the end of your view 
function myImageInit() {
	_T();
	myImageVars = {
		attr_image_new:        'data-imgNew', 		  // data-imgNew
		attr_image_old:        'data-imgOld', 		  // data-imgOld
		attr_image:            'data-img', 			  // data-img
		class_input_file:      'custom-file-input',   // File Input
		class_label_file:      'custom-file-label',   // File Label
		class_button_delete:   'myFile-img-delete',   // Delete Button
		class_button_delCheck: 'myFile-img-delCheck', // Delete CheckBox
		class_button_reset:    'myFile-img-reset'  	  // Reset Button
	};
};
// ========================================================================== //
// Called @ page load to simulate Reset for each image unit
// place this at the end of your view myImageAll('myFile-img-reset');
// 'myFile-img-reset' must be a class on the Reset Button  
function myImageAll() {
	_T(myImageVars);
	var elFiles=document.getElementsByClassName(myImageVars.class_button_reset);
	for (i=0; i< elFiles.length; ++i) {
		myImage(elFiles[i].firstElementChild);
	}	
}
// ========================================================================== //
// Toggle visibity of image controls and image locations
function xmyImage($this, op='reset', imgNew=false, imgOld=false, img=false) {
	_T($this, op, imgNew, imgOld, img);
	var elRow=$this.parentNode.parentNode;										// Owning DIV
	// console.dir(elRow); alert('!1');
	if (!imgNew) { imgNew=elRow.getAttribute(myImageVars.attr_image_new); } 				// data-imgNew
	if (!imgOld) { imgOld=elRow.getAttribute(myImageVars.attr_image_old); } 				// data-imgOld
	if (!img   ) { img   =elRow.getAttribute(myImageVars.attr_image    ); } 				// data-img
	var elFile  	=elRow.getElementsByClassName(myImageVars.class_input_file	    )[0];	// File Input
	var elLabel 	=elRow.getElementsByClassName(myImageVars.class_label_file	    )[0];	// File Label	
	var elDelete	=elRow.getElementsByClassName(myImageVars.class_button_delete	)[0];	// Delete Button
	var elDelCheck	=elRow.getElementsByClassName(myImageVars.class_button_delCheck	)[0];	// Delete CheckBox
	var elReset 	=elRow.getElementsByClassName(myImageVars.class_button_reset 	)[0];	// Reset Button
	var elMeta 		=elRow.getElementsByClassName(myImageVars.class_meta_list   	)[0];	// Meta List
	if (op=='delete') {
		myHideShowElement([elDelete, imgOld]);
		myHideShowElement([elReset], 'block');
		elLabel.innerHTML='<p class="text-danger"><i class="fas fa-trash-alt mr-2"></i>Image will be Deleted.</p>';
	} else {
		if (img) {
			var show=[elFile, elDelete, imgOld];
		} else {
			var show=[elFile];
		}
		myHideShowElement([elReset, imgNew]);
		myHideShowElement(show, 'block');
		elDelCheck.checked=false;
		elFile.value='';
		elLabel.textContent="Select a file...";
	}
}
// ========================================================================== //
// Toggle visibity of image controls and image locations
function myImage($this, op='reset', imgNew=false, imgOld=false, img=false) {
	_T($this, op, imgNew, imgOld, img);
	var elRow=$this.parentNode.parentNode;										// Owning DIV
	if (!imgNew) { imgNew=elRow.getAttribute(myImageVars.attr_image_new); } 				// data-imgNew
	if (!imgOld) { imgOld=elRow.getAttribute(myImageVars.attr_image_old); } 				// data-imgOld
	if (!img   ) { img   =elRow.getAttribute(myImageVars.attr_image    ); } 				// data-img
	var elFile  	=elRow.getElementsByClassName(myImageVars.class_input_file	    )[0];	// File Input
	var elLabel 	=elRow.getElementsByClassName(myImageVars.class_label_file	    )[0];	// File Label	
	var elDelete	=elRow.getElementsByClassName(myImageVars.class_button_delete	)[0];	// Delete Button
	var elDelCheck	=elRow.getElementsByClassName(myImageVars.class_button_delCheck	)[0];	// Delete CheckBox
	var elReset 	=elRow.getElementsByClassName(myImageVars.class_button_reset 	)[0];	// Reset Button
	if (op=='delete') {
		myHideShowElement([elDelete, imgOld]);
		myHideShowElement([elReset], 'block');
		elLabel.innerHTML='<p class="text-danger"><i class="fas fa-trash-alt mr-2"></i>Image will be Deleted.</p>';
	} else {
		if (img) {
			var show=[elFile, elDelete, imgOld];
		} else {
			var show=[elFile];
		}
		myHideShowElement([elReset, imgNew]);
		myHideShowElement(show, 'block');
		elDelCheck.checked=false;
		elFile.value='';
		elLabel.textContent="Select a file...";
	}
}
// ========================================================================== //
// Put the filename of the selected file into Form::file->label  
// Load and render the NEW image file
// Hide the DELETE button and any OLD image
// Show the RESET button and our NEW image 
function myFile($this, imgNew=false, imgOld=false) {
	_T($this, imgNew, imgOld);
	var elRow=$this.parentNode;							    			// Owning DIV
	if (!imgNew) { imgNew=elRow.getAttribute(myImageVars.attr_image_new); } 		// data-imgNew
	if (!imgOld) { imgOld=elRow.getAttribute(myImageVars.attr_image_old); } 		// data-imgOld			

	var elInput =$this.childNodes[1]; 												// file input element
	var elLabel =$this.childNodes[3]; 												// file label element
	var elDelete=elRow.getElementsByClassName(myImageVars.class_button_delete)[0];	// Delete Button
	var elReset =elRow.getElementsByClassName(myImageVars.class_button_reset )[0];	// Reset Button

	var fileName=elInput.files[0].name;
	var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif|\.jpe|\.ico)$/i;
	if (allowedExtensions.exec(fileName)) {
    	var maxSize=50;
		if (fileName.length>maxSize+3) {								// Trim for Label
			var part=parseInt(maxSize/2);
			fileName=fileName.substr(0,part) + '...' + fileName.substr(fileName.length-part, part);
		}
		elLabel.textContent=fileName;

		renderImage(elInput.files[0], imgNew);							// Load image
		myHideShowElement([elDelete, imgOld]);							// Hide
		myHideShowElement([elReset,  imgNew], 'block');					// Show
	}	
}
// ========================================================================== //
// Loads a file and renders the image to the specied tag
function renderImage(file, tagID) {
	_T(file, tagID);
	var reader=new FileReader();
	reader.onload=function(event) {
		the_url=event.target.result
		$('#'+tagID).html("<img src='" + the_url + "' width=100% />")
		}	
	reader.readAsDataURL(file);
}

// ========================================================================== //
// If a single file, put the filename of the selected file into Form::file->label
// Otherwise put the files count into Form::file->label
// If a media file, load and render the NEW file
// Hide the DELETE button and any OLD images
// Show the RESET  button and any NEW images 
function myFiles($this, imgNew=false, imgOld=false, mimes=false) {
	_T($this, imgNew, imgOld, mimes);
	var elRow=$this.parentNode;										       			// Owning DIV
	if (!imgNew){ imgNew=elRow.getAttribute(myImageVars.attr_image_new); } 			// data-imgNew
	if (!imgOld){ imgOld=elRow.getAttribute(myImageVars.attr_image_old); } 			// data-imgOld
	if (!mimes) { mimes =elRow.getAttribute(myImageVars.attr_mime);      } 			// data-mime

	var elInput =$this.childNodes[1]; 												// file input element
	var elLabel =$this.childNodes[3]; 												// file label element
	var elDelete=elRow.getElementsByClassName(myImageVars.class_button_delete)[0];  // Delete Button
	var elReset =elRow.getElementsByClassName(myImageVars.class_button_reset )[0];	// Reset Button
	var elMeta	=elRow.getElementsByClassName(myImageVars.class_meta_list    )[0];	// Meta List

	myHideShowElement([elDelete, imgOld]);								// Hide
	rendered=previewMedia(elInput.files, imgNew, elMeta);				// Load & render images/videos/audios
	if (rendered[0]+rendered[1]+rendered[2]>0) {         											
		myHideShowElement([elReset, imgNew], 'block');					// Show images/videos/audios & Reset
	} else {
		myHideShowElement([imgNew]);									// Hide
		myHideShowElement([elReset], 'block');							// Show Reset
	}	

	if (elInput.files.length==1) {										// Inject fileName / count	
		var msg=elInput.files[0].name;
    	var maxSize=50;
		if (msg.length>maxSize+3) {										// Trim for Label
			var part=parseInt(maxSize/2);
			msg=msg.substr(0, part) + '...' + msg.substr(msg.length-part, part);
		}	    		
	} else { msg=elInput.files.length+' files selected: '+rendered; }
	elLabel.textContent=msg;
}

// ========================================================================== //
// Retrieve EXIF & IPTC metadata from image file and append to output element  
function myGetExif(file, elOut) {
	_T(file, elOut);
	EXIF.getData(file, function() {
        var newMetaData=EXIF.getAllTags(this);
        newMetaData.filename=file.name;
		var newItem=document.createElement("option");
       	newItem.text=JSON.stringify(newMetaData, null, "\t");
       	elOut.add(newItem);
    });
}

// ========================================================================== //
// Initialises tinymce with standard settings   
// place this at the end of your view myTinymce('#textarea-description');
function myTinymce(selector) {
	_T(selector);
	tinymce.init ({
		selector: '#'+selector,
		plugins: "advlist autolink lists link image charmap print preview anchor searchreplace visualblocks code fullscreen insertdatetime media table contextmenu paste",
		menubar: false,
		extended_valid_elements: "iframe[src|width|height|name|align|frameborder|scrolling]",			
		toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | alignleft aligncenter alignright alignjustify",
		forced_root_block : 'div',
        branding: false,
  	}); 		
}

// ========================================================================== //
// Lets us use "_(id)" as a shortcut for document.getElementById(id)
function _(el) {
	return document.getElementById(el);
}

// ========================================================================== //
// Toggles the sort direction indicator on index views   
// place this at the end of your view mySortArrow({!! json_encode($sort) !!});
function mySortArrow(sort='id') {
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
// place this at the end of your view myView(resouce_name, 'accordionf2', 'accordionf1');
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
					//console.log('myVolume GET: '+vol);
				}
				if (vol>=0 && vol<=1) { elVid.volume=vol; }					// NS01
			} else {
				vol=elVid.volume;
				//console.log('myVolume SET: '+vol);
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
	for (var i=0; i<tagIDs.length; ++i){
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
