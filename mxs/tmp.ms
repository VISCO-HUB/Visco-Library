global _URL = "http://svg-web-003/"

try(closeRolloutFloater rAssetsLibraryModelPack)catch()
global rAssetsLibraryModelPack = newRolloutFloater "Pack Model" 280 680

global _STEP1
global _STEP2
global _STEP3
global _STEP4
global _STEP5
global rAbout

global Keys = dotNetClass "System.Windows.Forms.Keys"

global isLoadedStep1 = false
global catID = -1
global category1 = #()
global category2 = #()
global category3 = #()
global tags = #()
global modellers = #()
global models= #()
global projects = #()
global manufacturers= #()
global suggestTag = ""

global sModelName = ""
global sManufacturer = ""
global sModeller = ""
global sProject = ""
global sTags = ""

global bRigged = false
global bAnimated = false
global bUvw = false
global bUnwrap = false

global iCat1 =1
global iCat2 =1
global iCat3 =1


fn goStep step =
(
	k = #(_STEP1, _STEP2, _STEP3, _STEP4, _STEP5, rAbout)
	for i in k do 
	(
		removeRollout i rAssetsLibraryModelPack
	)
	
	addRollout k[step] rAssetsLibraryModelPack border: true
	addRollout rAbout rAssetsLibraryModelPack border: true rolledUp: true
)

fn httpGet url =
(	
	t = ""
	url += "&time=" + (timeStamp() as string)
	try
	(
		w = dotNetObject (dotNetClass "System.Net.WebClient")
		s = dotNetClass "System.IO.StreamReader"
		o = w.OpenRead url
		l = dotNetObject s o
		t = l.ReadToEnd()
	)
	catch()
	
	return t
)


fn httpUpload file =
(	
	url = _URL + "vault/upload.php"
	file = @"C:\Users\v.lukyanenko\Desktop\test model\Tabano_Chair_Leather\Tabano_Chair_Leather.zip"
	
	try
	(
		w = dotNetObject (dotNetClass "System.Net.WebClient")
		return w.uploadFile url file

	)
	catch()
	
	return false
)
--httpUpload ""
fn findTag t =
(
	s = filterString t ","
	if(s.count == 0) do return #()
	tag = trimLeft (trimRight s[s.count] " ") " "
	
	find = for i in tags where tag.count > 0 and matchPattern i pattern: (tag + "*") collect i
	suggestTag	= ""
	if(find.count > 0) do suggestTag = find[1]
	
	return find
)

rollout _STEP1 "STEP 1: Model Info" 
(	

	group "Category"
	(
		dropdownlist ddlLib "Library:" 
		dropdownlist ddlCat "Category:"
		dropdownlist ddlSubCat "Sub Category:"
	)
	
	group "Model Info"
	(
		label lbl1 "Model Name:" align: #left		
		dotNetControl edtModelName "System.Windows.Forms.TextBox" height: 25
		label lbl2 "Manufacturer:" align: #left		
		dotNetControl edtManufacturer "System.Windows.Forms.TextBox" height: 25
		label lbl3 "Modeller:" align: #left		
		dotNetControl edtModeller "System.Windows.Forms.TextBox" height: 25
		label lbl4 "Project:" align: #left		
		dotNetControl edtProject "System.Windows.Forms.TextBox" height: 25
		label lbl5 "Tags:" align: #left		
		dotNetControl edtTags "System.Windows.Forms.TextBox" height: 25
		
		button btnTag1 "" across: 4 visible: false width: 60 border: false
		button btnTag2 ""  visible: false width: 60 border: false
		button btnTag3 "" visible: false width: 60 border: false
		button btnTag4 "" visible: false width: 60 border: false
	)
	
	group "Additional Info"
	(
		checkbox cbxRigged "Rigged"
		checkbox cbxAnimated "Animated"
		checkbox cbxUbw "UVW"
		checkbox cbxUnwrap "Unwrap"
	)
	
	timer tmr1 "Tmr1" active: false interval: 300
	progressBar hr "" height: 3 offset:[0,15]
	button btnNext "Next >>" align: #right  enabled: true 
	
	fn trim t = trimLeft (trimRight t " ") " "
	
	fn setTagText t = 
	(
		c = for i in _STEP1.controls where isProperty i "name" and matchPattern i.name pattern: "btnTag*" collect i
		for i in c do i.visible = false
		
		for i in 1 to t.count where i <= 4 do
		(
			c[i].caption = t[i] 
			c[i].visible = true
		)
	)
	
	fn addTag a b =
	(
		t = b.text			
		t = filterString t ","
		t = for i in t collect trim i
		if(findItem t a.caption == 0) then
		(
			n = ""
			for i in 1 to t.count - 1 do n += t[i] + ", "
			n = trimLeft n ", "
			n += a.caption + ", "		
			b.text = n
			b.SelectionStart = (b.text.count)
		)
		else
		(
			messageBox "Tag already exist!" title: "Warning!"
		)
		
		setFocus b
	)
	
	on btnNext pressed do 
	( 		
		if(catID == -1) do return messageBox "Please select the category!" title: "Warning!"		
		
		sModelName = edtModelName.text		
		sManufacturer = edtManufacturer.text
		sModeller = edtModeller.text
		sProject = edtProject.text
		sTags = edtTags.text

		bRigged = cbxRigged.checked
		bAnimated = cbxAnimated.checked
		bUvw = cbxUbw.checked
		bUnwrap = cbxUnwrap.checked
			
		iCat1 = ddlLib.selection
		iCat2 = ddlCat.selection
		iCat3 = ddlSubCat.selection
		
		goStep 2	
	)
	
	on edtTags TextChanged s a do
	(		
		t = findTag edtTags.text		
		if(t.count == 0) do return false
		setTagText t	
	)
	
	on edtTags KeyUp s e do
	(
		if(e.KeyCode == Keys.Enter) do (			
			if(btnTag1.visible == true) do addTag btnTag1 edtTags	
		)
	)
	
	fn getList s = 
	(
		l = filterString s ";"
		items = #(#(-1, ""))
		for i in l do append items (filterString i "|")
		
		return items
	)
	
	fn getCat id =
	(
		catID = -1
		url = _URL + "vault/mxs/mxsGetCat.php?pid=" + id as string + "&time=" + timeStamp() as string
		r = httpGet url
		
		return getList r
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
	
	fn getProjects =
	(
		url = _URL + "vault/mxs/mxsGetProjects.php?time=" + timeStamp() as string
		r = httpGet url			
		return filterString r ";"
	)
	
	on _STEP1 open do
	(	
		edtModelName.text = sModelName
		edtModelName.text = sModelName
		edtManufacturer.text = sManufacturer 
		edtModeller.text = sModeller 
		edtProject.text = sProject
		edtTags.text = sTags

		cbxRigged.checked = bRigged
		cbxAnimated.checked = bAnimated
		cbxUbw.checked = bUvw
		cbxUnwrap.checked	= bUnwrap
		
		ddlLib.items = for i in category1 collect i[2]
		ddlCat.items = for i in category2 collect i[2]
		ddlSubCat.items = for i in category3 collect i[2]
		
		ddlLib.selection = iCat1
		ddlCat.selection = iCat2
		ddlSubCat.selection = iCat3

		
		edtModeller.AutoCompleteMode = edtModeller.AutoCompleteMode.SuggestAppend
		edtModeller.AutoCompleteSource = edtModeller.AutoCompleteSource.CustomSource
		
		edtModelName.AutoCompleteMode = edtModelName.AutoCompleteMode.SuggestAppend
		edtModelName.AutoCompleteSource = edtModelName.AutoCompleteSource.CustomSource
		
		edtManufacturer.AutoCompleteMode = edtManufacturer.AutoCompleteMode.SuggestAppend
		edtManufacturer.AutoCompleteSource = edtManufacturer.AutoCompleteSource.CustomSource		
		
		edtProject.AutoCompleteMode = edtProject.AutoCompleteMode.SuggestAppend
		edtProject.AutoCompleteSource = edtProject.AutoCompleteSource.CustomSource	
		
		tmr1.active = true		
	)
		
	on _STEP1 close do
	(
	
	)
	
	on tmr1 tick do
	(print "---"
		print isLoadedStep1
		if(not isLoadedStep1) do		
		(
			category1 = getCat 0
			ddlLib.items = for i in category1 collect i[2]
				
			tags = getTagsList()			
			modellers = getModellerList()
			
			edtModeller.AutoCompleteCustomSource.AddRange modellers
			
			projects = getProjects()
			edtProject.AutoCompleteCustomSource.AddRange projects
			
			for i in getModels() do 
			(	
				t = filterString i "|"
				if(t.count > 1) do
				(
					append models t[1]
					append manufacturers t[2]
				)
			)

			edtModelName.AutoCompleteCustomSource.AddRange models
			edtManufacturer.AutoCompleteCustomSource.AddRange manufacturers
			
			isLoadedStep1 = true
		)
		
		tmr1.active = false
	)
	
	on ddlLib selected v do
	(
		ddlCat.items = #()
		ddlSubCat.items = #()
		category2 = getCat (category1[v][1])		
		ddlCat.items = for i in category2 collect i[2]
	)
	
	on ddlCat selected v do
	(
		ddlSubCat.items = #()
		category3 = getCat (category2[v][1])		
		ddlSubCat.items = for i in category3 collect i[2]
	)

	
	on ddlSubCat selected v do
	(
		catID = category3[v][1]
	)
	
	on btnTag1 pressed do addTag btnTag1 edtTags
	on btnTag2 pressed do addTag btnTag2 edtTags
	on btnTag3 pressed do addTag btnTag3 edtTags
	on btnTag4 pressed do addTag btnTag4 edtTags
	
)

rollout _STEP2 "STEP 2: Model Transform" 
(
	progressBar hr "" height: 3 offset:[0,15]
	button btnPrev "<< Back" align: #left  across: 2
	button btnNext "Next >>" align: #right  enabled: true	
	on btnPrev pressed do goStep 1		
	on btnNext pressed do goStep 3		
)

rollout _STEP3 "STEP 3: Basic Setup" 
(	
	
	
	progressBar hr "" height: 3 offset:[0,15]
	button btnPrev "<< Back" align: #left  across: 2
	button btnNext "Next >>" align: #right  enabled: true	
	on btnPrev pressed do goStep 2		
	on btnNext pressed do goStep 4
)

rollout _STEP4 "STEP 4: Preview" 
(
		
	progressBar hr "" height: 3 offset:[0,15]
	button btnPrev "<< Back" align: #left  across: 2
	button btnNext "Next >>" align: #right  enabled: true	
	on btnPrev pressed do goStep 3		
	on btnNext pressed do goStep 5
	
	
)

rollout _STEP5 "STEP 5: Create Archive" 
(
	
	
	progressBar hr "" height: 3 offset:[0,15]
	button btnPrev "<< Back" align: #left 
	on btnPrev pressed do goStep 4	
)


rollout rAbout "About" 
(
	
)

goStep 1

--addRollout _STEP1 rAssetsLibraryModelPack rolledUp:false 
/*addRollout _STEP2 rAssetsLibraryModelPack rolledUp:true 
addRollout _STEP3 rAssetsLibraryModelPack rolledUp:true 
addRollout _STEP4 rAssetsLibraryModelPack rolledUp:true 
addRollout _STEP5 rAssetsLibraryModelPack rolledUp:true 
addRollout rAbout rAssetsLibraryModelPack rolledUp:true */
