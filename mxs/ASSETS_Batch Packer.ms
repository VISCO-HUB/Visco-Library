try(fileIn (getFileNamePath (getThisScriptFileName()) + "ASSETS_Pack Model.ms")) catch(return messageBox "Cant run ASSETS_Pack Model.ms")

global suggestModelsList = #()
global suggestManufacturerList = #()
global suggestProjectsList = #()
global suggestClientsList = #()
global suggestTagsList = #()

global catID = -1
global tmpCatID = -1

global category1 = #(#("0", " "))
global category2 = #(#("0", " "))
global category3 = #(#("0", " "))
	
global catId1 = 1
global catId2 = 1
global catId3 = 1
	
global iCat1 = 1
global iCat2 = 1
global iCat3 = 1

try(closeRolloutFloater rAssetsLibraryBatchPacker)catch()
global rAssetsLibraryBatchPacker = newRolloutFloater "Batch Packer" 450 800
global rBatchPacker
global rBatchSettings
	
global _URL = "http://svg-web-003/"
global Keys = dotNetClass "System.Windows.Forms.Keys"

global imgsList = #()	
global filesList = #()	

fn getDefaultIni = (
	d = getDir (#temp) + @"\"
	if(filesList[1] != undefined) do d = getFileNamePath filesList[1]	
	return d + @"BatchPacker.ini"
)
	
fn setDefaultSetting s v = (
	ini =getDefaultIni()
	setINISetting ini "DEFAULT" s v
)

fn getDefaultSetting s = (
	ini = getDefaultIni()
	return getINISetting ini "DEFAULT" s
)

fn stripSlashes f = (
	f = substituteString f "\\" ""
	f = substituteString f ":" ""
	f = substituteString f "/" ""
	
	return f
)

fn setFileSetting f s v = (
	--f = stripSlashes f
		
	ini = /*(getDir (#temp)) + @"\" +*/ f + ".ini"
	setINISetting ini "FILE" s v
)

fn getFileSetting f s = (
	--f = stripSlashes f
	
	ini = /*(getDir (#temp)) + @"\" +*/ f + ".ini"
		
	return getINISetting ini "FILE" s	
)

fn getActuatlMaxVersion =
(
	v = maxVersion()
	v = (v[1] / 1000) - 2
	
	a = 2000 + v
	
	o = case v of 
	(
		11: #(a - 1, a)
		12: #(a - 2, a - 1, a)
		default: #(a - 3, a - 2, a - 1, a)		
	)
	
	return (for i in o collect i as string)
)
	
fn useSettings k p w =
(
	/*
		useSettings "key" "value/int/float/bool/bool_not" "r/w" 
	*/
	
	u = sysInfo.username
	d = getFilenamePath  (getThisScriptFilename())
	n = "ASSETS_Pack Model.ms"		
	settingsFile = d + @"ini\" + u + "_" + n + ".ini"
		
	v = maxVersion()
	v = 2000 + ((v[1] / 1000) - 2)
	
	t = v as string
	
	case w of
	(
		"r":
		(
			s = getINISetting settingsFile t k
			case p of
			(
				"int": return s as integer
				"float": return s as float
				"bool": if s == "true" or s.count < 1 then true else false
				"bool_not": if s == "true" then true else false
				
				default: return s
			)
		)
		default: return setINISetting settingsFile t k (p as string)
	)
)	

fn getColor r g b = (dotnetclass "System.Drawing.Color").FromArgb r g b
fn saveProp p d = try(fileProperties.addProperty #custom p d) catch()
fn getProp p type: #text =
(
	i = fileProperties.findProperty #custom p
	if(type == #array and i !=0) do return explode (fileProperties.getPropertyValue #custom i)
	if(i != 0) do return  fileProperties.getPropertyValue #custom i
	
	case type of
	(
		#bool: return false
		#text: return ""
		#int: return 0
		#negative: return -1
		#array: return #("")
		default: ""
	)
)

-----------------------------
/* TAGS */

fn trim s c: " " = trimRight (trimLeft s c) c
fn explode s tag: ";" = return filterString s tag
fn implode a tag: ";" = 
(
	s = ""
	for i in a do s += i as string + tag
	return trimRight s tag
)

fn addTag a b =
(	
	txt = execute (b + ".text")
	
	t = for i in filterString txt "," collect toLower (trim i)
	t = for i in 1 to t.count - 1 where t[i].count > 0 and t[i] != " " collect t[i]
	o1 = t.count 
	append t (execute (a + ".caption"))
	t = makeUniqueArray t
	o2 = t.count 
		
	new = (implode t tag: ", ") + ", "	
	start = new.count

	execute (b + ".text=\""  + new + "\"")	
	execute ("setFocus " + b)
	execute (b + ".SelectionStart = " + start as string)
)

fn setActiveHint h t = (
		
	v = for i in 1 to 5 where execute(t + (i as string) + ".visible") == true collect i
	total = v.count
		
	for i in 1 to total do (
			
			b = execute (t + (i as string) + ".checked")				
			
			if(b == true) do (
				execute (t + (i as string) + ".checked=false")
				n  = (i + h)
				if(n > total) do n = 1
				if(n < 1) do n = total
				
				execute (t + (n as string) + ".checked=true")
				exit
			)
		)					
)

fn addActiveHint tt edt = (		
	for i in 1 to 5 do (
		c = execute(tt + i as string + ".checked") 
		v = execute(tt + i as string + ".visible") 
		t = execute(tt + i as string + ".caption") 			
		if(c == true and v == true) do addTag (tt + i as string) edt 
	)
)

fn resetActiveHint t = (
	for i in 1 to 5 do execute(t + (i as string) + ".checked=false") 
	execute (t + "1.checked = true")
)

fn tagChanged tt edt = (
	ss = execute ("filterString " + edt + ".text \",\"")
		
	words = #()
	tag = ""
	
	if(ss.count != 0) do (
		tag = (trimLeft (trimRight ss[ss.count]))
		words = for i in suggestTagsList where matchPattern i pattern: (tag + "*") collect i
	)
	
	resetActiveHint tt
	
	for i in 1 to 5 do (
		execute (tt + i as string + ".visible=false")
		if(words[i] == undefined or tag.count == 0) do continue
					
		execute (tt + i as string + ".caption=\"" + words[i] + "\"")
		execute (tt + i as string + ".visible=true")
	)
)

-----------------------------

fn packAllModels = (
			
	try(closeRolloutFloater rAssetsLibraryModelPack)catch()	
	local f = getFileNamePath (getThisScriptFileName()) + "ASSETS_Pack Model.ms"
	try(fileIn f) catch(return messageBox "Cant run ASSETS_Pack Model.ms")
		
	global _SILENT_MODE = true
		
	for i in 1 to filesList.count do (					
		::sModelName = execute ("rBatchPacker.edtModelName" + (i as string) + ".text")				
		::sManufacturer = rBatchSettings.edtManufacturer.text
		::sModeller = rBatchSettings.edtModeller.text
		::sProject = rBatchSettings.edtProjects.text
		
			t1 = rBatchSettings.edtTags.text
			t2 = execute ("rBatchPacker.edtTags" + (i as string) + ".text")
			tags = t1 + "," + t2
			tags = for tt in explode tags tag:"," collect trim tt
			makeUniqueArray tags
			
		::sTags = implode tags tag: ","
			
		::catID = tmpCatID
		::sToken = generateUniqueID()
			
		::bRigged = false
		::bAnimated = false
		::bBakedTextures = false
		::bGameEngineReady = false
		::bLods = false
		::bUnwrap = false
		::bLights = false	
		::sPreviewPath = imgsList[i]
		::bMainPreview = #(true)
			
		saveAllProp saveCat: #force	
			
		goStep 5	
			
		loadMaxFile filesList[i] useFileUnits: true quiet: true
			
		select objects
		
		_STEP5.btnPackModel.pressed()
			
		::catID = tmpCatID	
	)
	
		
		/*
	global sModelName = "test"
	global sManufacturer = ""
	global sModeller ="Evermotion"
	global sProject = "Project"
	global sTags = "test1, test2, test3"
	global catID = "256"
	global sToken = generateUniqueID()
			
	bRigged = false
	bAnimated = false
	bBakedTextures = false
	bGameEngineReady = false
	bLods = false
	bUnwrap = false
	bLights = false

	saveAllProp saveCat: #force
	
	goStep 5
	
	select objects
	
	_STEP5.btnPackModel.pressed()*/
	
	::catID = tmpCatID
	
	shellLaunch @"C:\temp\Assets Library" ""
)

fn autoComplete c s = (
	--if(c == undefined) do return false
	c.AutoCompleteMode = c.AutoCompleteMode.SuggestAppend
	c.AutoCompleteSource = c.AutoCompleteSource.CustomSource
	c.AutoCompleteCustomSource.AddRange s
)

fn showImage i = (	
	try(
		a = execute ("rBatchPacker.lbxImages" + (i as string) + ".selection")
		img = imgsList[i][a]	
		shellLaunch img ""
	) catch()
)

fn setImagesPreview i a = (
	#(theBmp, undefined, 1,1,1,1,1 )
	img = imgsList[i][a]
	
	ex = ""
	
	if(img == undefined or not doesFileExist img) then (
		ex += "btm = bitmap 1 1 \n"
	) else (
		ex += "btm = openBitMap @\"" + (img as string)+ "\" \n"		
	)
	
	
	ex += "try(rBatchPacker.btnImage" + (i as string) + ".images=#(btm, undefined, 1,1,1,1,1)) catch() \n" 
	return ex 
)

fn buildImagesList i a: undefined = (	
	ex = "rBatchPacker.lbxImages" + (i as string) + ".items=for l in imgsList[" + (i as string) + "] collect (filenameFromPath (l)) \n" 
	
	if(a != undefined) do (
		
		ex += setImagesPreview i a
	)
	return ex 
)

fn getOpenImage = 
(
	f = #()
	
	imageDialog = dotNetObject "System.Windows.Forms.OpenFileDialog" 
	imageDialog.title = "Select Pictures"
	imageDialog.Multiselect = true
	imageDialog.Filter = "JPG (*.jpg)|*.jpg"
	imageDialog.FilterIndex = 1
	
	result = imageDialog.showDialog() 
	result.ToString() 
	result.Equals result.OK 
	result.Equals result.Cancel 
	
	f = imageDialog.fileNames 
	 
	return f
)

fn addImage i = (
	f = getOpenImage()
	if(f.count == 0) do return false
	
	for img in f do append imgsList[i] img
	
	a = imgsList[i].count

	execute (buildImagesList i a:a)
	execute ("rBatchPacker.lbxImages" + (i as string) + ".selection=" + a	as string)
)

fn imageRemove i a = (
	try(deleteItem imgsList[i] a) catch()
	execute (buildImagesList i)
	
	execute (setImagesPreview i 1)
)

fn showRightClick i a = (
	global imageId = i
	global imageItem = a
	
	rcmenu rcImages
	(	
		menuItem rcRemove "Remove"
		on rcRemove picked do imageRemove imageId imageItem
	)

	popUpMenu rcImages
)

fn buildFilesList files =
(
	try(removeRollout rBatchPacker) catch()
	ex = ""
	n = "\n"	
	
	rr = "rBatchPacker."
		
	ex += "rollout rBatchPacker \"Pack List\" category: 2 (" + n
	
	for i in 1 to files.count do (				
		c = i as string
		f = files[i]
		ff = getFileNameFile f
		pp = getFileNamePath f
		
		imgs = #()
		
		if(doesFileExist (pp + ff + ".jpg")) do (
			append imgs (pp + ff + ".jpg")
			other = getFiles (pp + ff + "_*.jpg")
			join imgs other
		)
		
		append imgsList imgs
		
		ex += "button btnImage" + c + " \"\" width: 84 height: 84 align: #left across: 3" + n
		ex += "listbox lbxImages" + c + " \"\" height: 6 width: 280 offset: [-42, 0]" + n
		ex += "button btnAddImage" + c + " \"+\" width: 25 height: 84 offset: [56, 0]" + n
		ex += "label lblModelName" + c + " \"ModelName:\" across: 2 align: #left offset: [0, 3]" + n
		ex += "dotNetControl edtModelName" + c + " \"System.Windows.Forms.TextBox\" text: \"" + ff + "\" height: 25 tabstop: true offset: [-110, 0] width: 315" + n
		ex += "label lblTags" + c + "\"Tags:\" across: 2 align: #left offset: [0, 3]" + n
		ex += "dotNetControl edtTags" + c + " \"System.Windows.Forms.TextBox\" height: 40 tabstop: true offset: [-110, 0] width: 315" + n
		
		for t in 1 to 5 do (
			ex += "checkbutton btnTag" + c + "_" + (t as string) + " visible: false width: 80 border: false align: #right highlightColor: gray"
			if(t == 1) do ex += " across: 5 "
			ex += n
			
			-- TAGS
			--ex += "on btnTag" + c + "_" + (t as string) + " changed v do (addTag \"" + rr + "btnTag" + c + "_" + (t as string) + "\" \"" + rr + "edtTags" + c + "\" )" + n
			
			ex += "on edtTags" + c + " KeyUp s e do (" + n			
				ex += "sel = s.text.count" + n
				ex += "if(e.KeyCode == Keys.Enter) do (" + n			
					ex +=" if(btnTag" + c + "_1.visible == true and btnTag" + c + "_1.caption.count > 0) do (" + n
						ex += "addActiveHint \"" + rr + "btnTag" + c + "_\" \"" + rr + "edtTags" + c + "\"" + n
						ex += "e.handled  = true " + n						
				ex += "))" + n
			
				ex += "if(e.KeyCode == Keys.Down) do (" + n
				ex += "setActiveHint 1 \"" + rr + "btnTag" + c + "_\"" + n
				ex += "s.SelectionStart = sel" + n
				ex += "e.handled  = true )" + n
				
				ex += "if(e.KeyCode == Keys.Up) do (" + n
				ex += "setActiveHint -1 \"" + rr + "btnTag" + c + "_\"" + n
				ex += "s.SelectionStart = sel" + n
				ex += "e.handled  = true )" + n
				ex += "setFileSetting @\"" + f + "\" \"TAGS\" (edtTags" + c + ".text)"  + n
				
			ex += ")" + n
			
			ex += "on btnTag" + c + "_" + (t as string) + " changed v do (addTag \"" + rr + "BtnTag" +  c + "_" + (t as string) + "\" \"" + rr + "edtTags" + c + "\")" + n
		)
					
		if(i != files.count) do ex += "progressbar pbDivider" + c + " \"\" height: 1" + n
		
		ex += "on lbxImages" + c + " rightClick a do (showRightClick " + c + " a )" +  n		
		ex += "on btnAddImage" + c + " pressed do (addImage " + c + ")" + n
		ex += "on lbxImages" + c + " selected a do (execute (setImagesPreview " + c + " a))" + n
		ex += "on btnImage" + c + " pressed do (showImage " + c + ")" + n
		ex += "on lbxImages" +c + " doubleClicked a do (showImage " + c + ")" + n
		ex += "on edtModelName" + c + " KeyUp s e do (setFileSetting @\"" + f + "\" \"NAME\" (s.text))" + n
		
		ex += "on edtTags" + c + " TextChanged s a do tagChanged \"" + rr + "btnTag" + c + "_" + "\" \"" + rr + "edtTags" + c + "\"" + n
		
	)
	
	ex += "on rBatchPacker open do (" + n
	for i in 1 to files.count do (
		ex += buildImagesList i
		ex += setImagesPreview i 1
		ex += "autoComplete edtModelName"+ (i as string) + " suggestModelsList"  + n
		
		if(doesFileExist (files[i] + ".ini")) do (
			fileTags = getFileSetting files[i] "TAGS"
			fileName = getFileSetting files[i] "NAME"
			ex += "edtTags" + (i as string) + ".text=\"" + fileTags + "\"" + n
			if(fileName.count > 3) do ex += "edtModelName" + (i as string) + ".text=\"" + fileName + "\"" + n
		)
		
		-- TAGS
		
		ex += "edtTags" + (i as string) + ".ScrollBars = (dotNetClass \"System.Windows.Forms.ScrollBars\").Vertical" + n
		ex += "edtTags" + (i as string) + ".Multiline = true" + n
		ex += "edttags" + (i as string) + ".WordWrap = true" + n
	)
	ex += ")" + n
	ex += ")"	
	
	execute ex
	
	addRollout rBatchPacker rAssetsLibraryBatchPacker border: true
)



fn implodeCat c =
(
	s = ""
	for i in c do s += i[1] as string + "|" + i[2] as string + ";"
	return s
)

fn getList s = 
(
	l = filterString s ";"
	items = #(#(-1, ""))
	for i in l do append items (filterString i "|")
	
	return items
)

fn getTagsList =
(
	url = _URL + "vault/mxs/mxsGetTags.php?time=" + timeStamp() as string
	r = httpGet url			
	return filterString r ";"
)

fn getModellerList =
(
	url = _URL + "vault/mxs/mxsGetUsers.php?time=" + timeStamp() as string
	r = httpGet url			
	return filterString r ";"
)

fn getModels =
(
	url = _URL + "vault/mxs/mxsGetModels.php?time=" + timeStamp() as string
	r = httpGet url			
	return filterString r ";"
)

fn getCat id =
(
	--catID = -1
	url = _URL + "vault/mxs/mxsGetCat.php?pid=" + id as string + "&time=" + timeStamp() as string
	r = httpGet url
	
	return getList r
)

fn findIndex c id =
(
	for i in 1 to c.count where c[i][1] == id as string do return i
	return -1
)

fn implode a tag: ";" = 
(
	s = ""
	for i in a do s += i as string + tag
	return trimRight s tag
)

rollout rBatchSettings "Settings" category: 1  (
	group "Category"
	(
		label lbl6 "Library:" align: #left across: 2 
		dotNetControl lblWarn3 "System.Windows.Forms.Label" text: "*" offset: [-84-80, 0] width: 10		
		dropdownlist ddlLib "" 
		
		label lbl7 "Category:" align: #left across: 2 
		dotNetControl lblWarn4 "System.Windows.Forms.Label" text: "*" offset: [-72-80, 0] width: 10	
		dropdownlist ddlCat ""
		
		label lbl8 "Sub Category:" align: #left across: 2 
		dotNetControl lblWarn5 "System.Windows.Forms.Label" text: "*" offset: [-51-80, 0] width: 10	
		dropdownlist ddlSubCat ""		
	)
	
	group "Default Settings" (
	--	label lblModelNam "Model Name:" across: 2 align: #left
	--	dotNetControl edtModelName "System.Windows.Forms.TextBox" height: 25 tabstop: true offset: [-110, 0] width: 315
		label lblManugacturer "Manufacturer:" across: 2 align: #left
		dotNetControl edtManufacturer "System.Windows.Forms.TextBox" height: 25 tabstop: true offset: [-110, 0] width: 315
		label lblModeller "Modeller:" across: 2 align: #left
		dotNetControl edtModeller "System.Windows.Forms.TextBox" height: 25 tabstop: true offset: [-110, 0] width: 315
		label lblProject "Project:" across: 2 align: #left
		dotNetControl edtProjects "System.Windows.Forms.TextBox" height: 25 tabstop: true offset: [-110, 0] width: 315	
		
		label lblTags "Tags:" align: #left
		dotNetControl edtTags "System.Windows.Forms.TextBox" height: 60
		
		checkbutton btnTag1 "" visible: false width: 80 border: false align: #right across: 5 highlightColor: gray
		checkbutton btnTag2 "" visible: false width: 80 border: false align: #right highlightColor: gray
		checkbutton btnTag3 "" visible: false width: 80 border: false align: #right highlightColor: gray
		checkbutton btnTag4 "" visible: false width: 80 border: false align: #right highlightColor: gray
		checkbutton btnTag5 "" visible: false width: 80 border: false align: #right	highlightColor: gray	
	)
	
		
	group "" (
		dotNetControl edtModelsPath "System.Windows.Forms.TextBox" text: "Please select models..." readonly: true height: 25 tabstop: true	
		button btnLoadModels "Load Models" height: 30 width: 200
	) 
	
	group "Pack Settings" (
		label lblMaxVer "3Ds Max Version: " across: 2 align: #left 
		dropdownlist ddlMaxVer "" width: 70 offset: [-110, -3]
		checkbox cbxRenameTextures "Rename Textures" checked: true across: 3	
		checkbox cbxRenameObjects "Rename Objects" checked: true 
		checkbox cbxRemoveLayers "Remove Layers" checked: true 
		
		checkbox cbxRenameMaterials "Rename Materials" checked: true across: 3
		checkbox cbxRenameGroups "Rename Groups" checked: true
		checkbox cbxDumy "Dummy" checked: true visible: false
		
		
		button btnPackAllModels "Pack All Models" height: 30 width: 200
	)
	
	timer tmr1 "Tmr1" active: false interval: 300
	timer tmrStartPack "TmrPack" active: false interval: 600
		
	fn disableUi d = (
		for i in rBatchSettings.controls do i.enabled = d
		btnLoadModels.enabled = true
		edtModelsPath.enabled = true
	)
	
	fn getUpdateSettings = (
		ini = getDefaultIni()
		if(doesFileExist ini) do (
			q = queryBox "Do you want load settings from previous session?" title: "Confirm?"
			if(q) then (
				edtManufacturer.text = getDefaultSetting "edtManufacturer"
				edtModeller.text = getDefaultSetting "edtModeller"
				edtProjects.text = getDefaultSetting "edtProjects"
				edtTags.text = getDefaultSetting "edtTags"
			) 
		)		
	)
	
	fn checkBadNames = (
		errors = #()
		for i in 1 to filesList.count do (
			t = execute ("rBatchPacker.edtModelName" + (i as string) + ".text")
			if(t.count < 4) do append errors (getFileNameFile filesList[i])			
		)
		
		return errors
	)
	
	on btnPackAllModels pressed do (
		if(catID == -1) do return messageBox "Please select the category!" title: "Warning!"
		if(imgsList.count == 0 or imgsList.count != filesList.count) do return messageBox "Please add models!" title: "Warning!"
		errorImgs = for i in imgsList where i == undefined or i.count == 0 collect true
		if(errorImgs.count > 0) do return messageBox "Please add images for all models!" title: "Warning!"
		badNames = checkBadNames()
		if(badNames.count > 0) do return messageBox "Please check all models names!\nModel name must be at least 3 symbols!" title: "Warning!"
			
		tmpCatID = catID
		
		--try(closeRolloutFloater rAssetsLibraryModelPack)catch()	
		--local f = getFileNamePath (getThisScriptFileName()) + "ASSETS_Pack Model.ms"
		
		
		packAllModels()
		--tmrStartPack.active = true		
	)
	
	on tmrStartPack tick do (
		packAllModels()
		
		tmrStartPack.active = false
	)
	
	fn ddlLib_selected v =
	(		
		catID = -1
		ddlCat.items = #()
		ddlSubCat.items = #()
		category2 = getCat (category1[v][1])	
				
		useSettings "CAT1"	category1[v][1] "w"	
		
		ddlCat.items = for i in category2 collect i[2]			
	)
	
	fn ddlCat_selected v =
	(
		catID = -1		
		ddlSubCat.items = #()
		category3 = getCat (category2[v][1])	
		
		useSettings "CAT2"	category2[v][1] "w"	
				
		ddlSubCat.items = for i in category3 collect i[2]			
	)
		
	fn saveCat = (
		saveProp "catId1" (if(iCat1 > 0) then category1[iCat1][1] else -1)
		saveProp "catId2" (if(iCat2 > 0) then category2[iCat2][1] else -1)
		saveProp "catId3" (if(iCat3 > 0) then category3[iCat3][1] else -1)
			
		saveProp "category1" (implodeCat category1)
		saveProp "category2" (implodeCat category2)
		saveProp "category3" (implodeCat category3)		
		
		iCat1 = ddlLib.selection
		iCat2 = ddlCat.selection
		iCat3 = ddlSubCat.selection
		
		if(iCat1 > 0) do catId1 = category1[iCat1][1]
		if(iCat2 > 0) do catId2 = category2[iCat2][1]
		if(iCat3 > 0) do catId3 = category3[iCat3][1]
	)
	
	fn ddlSubCat_selected v = 
	(
		useSettings "CAT3"	category3[v][1] "w"	
		catID = category3[v][1] as integer
		
		saveCat()
	)
	
	fn loadCategories =
	(			
		catID = -1
		category1 = getCat 0
		
		ddlLib.items = for i in category1 collect i[2]		
		id = findIndex category1 catId1	
		
		if(id > 0) do (
			ddlLib_selected id
			ddlLib.selection = id
			
			id = findIndex category2 catId2
			if(id > 0) do (
				ddlCat_selected id
				ddlCat.selection = id
				
				id = findIndex category3 catId3
				if(id > 0) do (
					ddlSubCat_selected id
					ddlSubCat.selection = id
					catID = category3[id][1] as integer
				)
			)
		)	
	)
	
	fn switchControl e d = if(e.KeyCode == e.KeyCode.Enter or e.KeyCode == e.KeyCode.Tab)  do try(setFocus d)catch()
	
	fn getOpenMax = 
	(
		f = #()
		
		imageDialog = dotNetObject "System.Windows.Forms.OpenFileDialog" 
		imageDialog.title = "Select File"
		imageDialog.Multiselect = true
		imageDialog.Filter = "MAX (*.max)|*.max"
		imageDialog.FilterIndex = 1
		
		result = imageDialog.showDialog() 
		result.ToString() 
		result.Equals result.OK 
		result.Equals result.Cancel 
		
		f = imageDialog.fileNames 
		 
		return f
	)
	

	
	on edtModelName KeyUp s e do switchControl e edtManufacturer
	on edtManufacturer KeyUp s e do (
		switchControl e edtModeller
		setDefaultSetting "edtManufacturer" (s.text)
	)
	on edtModeller KeyUp s e do (
		switchControl e edtProjects
		setDefaultSetting "edtModeller" (s.text)
	)
	on edtProjects KeyUp s e do (
		switchControl e edtTags
		setDefaultSetting "edtModeller" (s.text)
	)
	
	on btnLoadModels pressed do (
		f = getOpenMax()
		if(f.count == 0) do return false

		imgsList = #()	
		filesList = #()
		
		filesList = f
		
		buildFilesList f
		edtModelsPath.text = (getFileNamePath f[1])
		
		disableUi true
		getUpdateSettings()
	)
	
	-------

	on edtTags TextChanged s a do (		
		tagChanged "rBatchSettings.btnTag" "rBatchSettings.edtTags"		
		
		setDefaultSetting "edtTags" (s.text)
	)
		
	on edtTags KeyUp s e do
	(	
		sel = s.text.count
		
		if(e.KeyCode == Keys.Enter) do (			
			if(btnTag1.visible == true and btnTag1.caption.count > 0) do (
				addActiveHint "rBatchSettings.btnTag" "rBatchSettings.edtTags"
				e.handled  = true
			)
		)	

		if(e.KeyCode == Keys.Down) do (
			setActiveHint 1 "rBatchSettings.btnTag" 
			e.handled  = true
			s.SelectionStart = sel
		)
		
		if(e.KeyCode == Keys.Up) do (
			setActiveHint -1 "rBatchSettings.btnTag" 
			e.handled  = true
			s.SelectionStart = sel			
		)				
	)
	
	on btnTag1 changed v do addTag "rBatchSettings.btnTag1" "rBatchSettings.edtTags"
	on btnTag2 changed v do addTag "rBatchSettings.btnTag2" "rBatchSettings.edtTags"
	on btnTag3 changed v do addTag "rBatchSettings.btnTag3" "rBatchSettings.edtTags"
	on btnTag4 changed v do addTag "rBatchSettings.btnTag4" "rBatchSettings.edtTags"
	on btnTag5 changed v do addTag "rBatchSettings.btnTag5" "rBatchSettings.edtTags"
		
	fn getBgColor = 
	(
		b = (colorman.getcolor #background) * 255
		c = getColor b[1] b[2] b[3]
		return c
	)

	fn getTextColor =
	(
		t = (colorMan.getColor #text) * 255
		c = getColor t[1] t[2] t[3]
		return c
	)
		
	on rBatchSettings close do (
		try(closeRolloutFloater rAssetsLibraryModelPack)catch()	
	)
	
	on cbxRenameTextures changed v do useSettings "RENTEX"	v "w"	
	on cbxRenameObjects changed v do useSettings "RENOBJ"	v "w"	
	on cbxRenameMaterials changed v do useSettings "RENMAT"	v "w"	
	on cbxRenameGroups changed v do useSettings "RENGRP"	v "w"
	on cbxRemoveLayers changed v do useSettings "REMLAYERS"	v "w"
	on ddlMaxVer selected v do _lblFormat.text = ddlMaxVer.selected
	
	on rBatchSettings open do (
		
		disableUi false

		
		try(setFocus (rBatchSettings.edtModelsPath)) catch()
		
		warnLabels = #(lblWarn3, lblWarn4, lblWarn5)
		bg = getBgColor()
		for i in warnLabels do 
		(
			i.backColor = bg
			i.foreColor = i.foreColor.red
		)	
		
		v = useSettings "RENTEX" "bool" "r"	
		cbxRenameTextures.checked = if(v == false) then false else true
			
		v = useSettings "RENOBJ" "bool" "r"	
		cbxRenameObjects.checked = if(v == false) then false else true	
			
		v = useSettings "RENMAT" "bool" "r"	
		cbxRenameMaterials.checked = if(v == false) then false else true
			
		v = useSettings "RENGRP" "bool" "r"	
		cbxRenameGroups.checked = if(v == false) then false else true
		
		v = useSettings "REMLAYERS" "bool" "r"	
		cbxRemoveLayers.checked = if(v == false) then false else true
		
		v = getActuatlMaxVersion()
		ddlMaxVer.items = v
				
		suggestTagsList = getTagsList()
		suggestModellerList = getModellerList()
			
		for i in getModels() do 
		(	
			t = filterString i "|"
			if(t.count > 1) do
			(
				if(t[1] != undefined) do appendIfUnique suggestModelsList t[1]
				if(t[2] != undefined) do appendIfUnique suggestManufacturerList t[2]
				if(t[3] != undefined) do appendIfUnique suggestProjectsList t[3]
				if(t[4] != undefined) do appendIfUnique suggestClientsList t[4]
			)
		)
			
		autoComplete edtModeller suggestModellerList
		autoComplete edtManufacturer suggestManufacturerList
		autoComplete edtProjects suggestProjectsList
			
		edtTags.ScrollBars = (dotNetClass "System.Windows.Forms.ScrollBars").Vertical
		edtTags.MultiLine = true
		edtTags.WordWrap = true
				
		
		catId1 = useSettings "CAT1" "int" "r"
		catId2 = useSettings "CAT2" "int" "r"
		catId3 = useSettings "CAT3" "int" "r"
				
		catID = (getProp "catID" type: #negative) as integer
		
		tmr1.active = true				
	)
		
	on tmr1 tick do (
		loadCategories()
		tmr1.active = false
	)
	
	on ddlLib selected v do 
	(	
		ddlCat.selection = 0
		ddlSubCat.selection = 0
		
		ddlLib_selected v	
	)
	on ddlCat selected v do 
	(	
		ddlSubCat.selection = 0
		ddlCat_selected v
	)
	on ddlSubCat selected v do ddlSubCat_selected v
)


addRollout rBatchSettings rAssetsLibraryBatchPacker border: false