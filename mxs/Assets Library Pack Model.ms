/* GLOBAL SETTINGS */

global _URL = "http://svg-web-003/"
global _SAVEPATH = @"C:\temp\Assets Library\"

try(closeRolloutFloater rAssetsLibraryModelPack)catch()
global rAssetsLibraryModelPack = newRolloutFloater "Pack Model" 280 700

/* GLOBALS */

global _STEP1
global _STEP2
global _STEP3
global _STEP4
global _STEP5
global rAbout

global Keys = dotNetClass "System.Windows.Forms.Keys"
global szGoodChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890&$%#()!_-+ " 
global BitmapTypes = #(Bitmaptexture, CoronaBitmap)

global catID = -1
global category1 = #(#("0", " "))
global category2 = #(#("0", " "))
global category3 = #(#("0", " "))
global tags = #()
global modellers = #()
global models= #()
global projects = #()
global manufacturers = #()
global sPreviewPath = #()
global bMainPreview = #()
global sOverview = ""
global zipper
	
global sModelName = ""
global sManufacturer = ""
global sModeller = ""
global sProject = ""
global sTags = ""

global bRigged = false
global bAnimated = false
global bBakedTextures = false
global bLods = false
global bUnwrap = false

global iCat1 = 1
global iCat2 = 1
global iCat3 = 1

/* CLASSES */

struct zip
(
	dirs = #(),
	files = #(),
	
	fn getRecusive d =
	(		
		c = getDirectories (d + "*")
		f = getFiles (d + "*")
		join dirs c
		join files f
		for i in c do getRecusive i
	),
	fn writeFile f t =
	(
		local tmp
		try(tmp = createFile f) catch (return false)			
		format t to:tmp
		flush tmp
		close tmp
				
		return true
	),
	
	fn br t =
	(
		return "\"" + t + "\""
	),
	
	fn rmDir d =
	(
		cmd = "RMDIR " + (br d) + " /S /Q"
		hiddendosCommand cmd 				
	),
	
	fn packFiles source: "" target: "" name: "tmpzip" del: false =
	(
		dirs = #()
		files = #()
		
		makeDir @"C:\temp\"
		makeDir target
		tmp = @"C:\temp\ziptmp.txt" 
		maxZip = (getDir #maxRoot) + "maxzip.exe"
			
		source = (trimRight source "\\") + "\\"
		target = (trimRight target "\\") + "\\"
		
		if(not doesFileExist source or source.count < 2) do return false
		if(not doesFileExist target or target.count < 2) do return false
		
		getRecusive source
		
		if(files.count == 0) do return false
		
		if(doesFileExist tmp) do deleteFile tmp
		t = ""		
		for i in files do t += (substituteString i source "") + "\n"
		writeFile tmp t
		
		curDir = sysInfo.currentDir
		sysInfo.currentDir = source
		out = target + name + ".zip"
		
		cmd = (br maxZip) + " " + (br out) + " @" + tmp
		
		hiddendosCommand cmd exitcode:&exitcode
		sysInfo.currentDir = curDir 
		
		if(del == true) do rmDir source
		
		if(exitcode == 0) do return true 
		return false
	)
)

/* MAIN FUNCTIONS */

fn invalidBitmap b = 
(	
	return b == undefined or b.filename == undefined or b.filename == "" or b.filename.count == 0
)

fn validBitmap b = 
(	
	return b != undefined and b.filename != undefined and b.filename != "" and b.filename.count != 0
)

fn getFullPath p = 
(	
	if(doesFileExist p) do return p
	
	return mapPaths.getFullFilePath (filenameFromPath  p)
)

fn getColor r g b = (dotnetclass "System.Drawing.Color").FromArgb r g b

fn getUnits = 
(	
	u = trimLeft (units.SystemType as string) "#"
	u[1] = toUpper u[1]
	return u
)

fn stripName n =
(
	n = substituteString n " " "-"
)

fn getDim s =
(
	d = s.max - s.min
	
	d = for i in 1 to 3 collect ((d[i] * 100) as integer / 100.0 ) as string
	
	return d[1] + " x " + d[2] + " x " + d[3]
)

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

fn implode a tag: ";" = 
(
	s = ""
	for i in a do s += i as string + tag
	return trimRight s tag
)

fn explode s tag: ";" = return filterString s tag
	
fn implodeCat c =
(
	s = ""
	for i in c do s += i[1] as string + "|" + i[2] as string + ";"
	return s
)
	
fn explodeCat s = for i in filterString s ";" collect filterString i "|" splitEmptyTokens: true		

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
		#int: return 1
		#negative: return -1
		#array: return #("")
		default: ""
	)
)

fn isSelected =
(
	if(selection.count < 1) do 
	(
		messageBox "Please select model!" title: "Warning!"
		return false
	)
	
	return true
)

fn isGroup s =
(
	g = for i in s where (isGroupHead i) and (not (isGroupMember i)) collect i
	if(g[1] != undefined) do return g[1]	
	return s
)

fn isValidText t title: "" =
(
	c = for i in 1 to t.count where findString szGoodChars t[i] == undefined collect t[i]

	if(c.count > 0) do 
	(
		c = makeUniqueArray c
		m = "Unsupported symbols:"
		for i in c do m += " " + i
			
		messageBox m title: title
		return false
	)
	
	return true
)

fn getMaxVersion =
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

fn getRender =
(
	r = renderers.current as string

	if matchpattern r pattern:"*Corona*" do return #("-corona", "Corona")
	if matchpattern r pattern:"*V_Ray_Adv*" do return #("-vray", "VRay")
	if matchpattern r pattern:"*Default_Scanline*" do return #("-scanline", "Scanline")
	if matchpattern r pattern:"*mental_ray*" do return #("-mentalray", "MentalRay")
	if matchpattern r pattern:"*iray_Renderer*" do return #("-iray", "IRay")
	if matchpattern r pattern:"*maxwell*" do return #("-maxwell", "Maxwell")
		
	return #("", "")
)

fn getNum n c:3 =
(
	n = n as string
	
	if(n.count < c) do
	(
		d = c - n.count
		
		s = ""
		
		for i in 1 to d do s += "0"
		return s + n
	)
	
	return n
)

fn polyCount s  =
(	
	iPoly = 0
	
	a = for i in s collect (getPolygonCount i)[1]
	for i in a do iPoly += i
	
	r = ""
	ii = 1
	sPoly = iPoly as string
	
	for i in sPoly.count to 1 by -1 do
	(
		space = ""	
			
		if (ii == 3) do 
		(
			space = " "
			ii = 0
		)
		
		ii += 1
		
		r = space + sPoly[i] + r
	)

	return (trimLeft r " ")
)

fn ungroupRecursive s = for i in s where isGroupHead i and isValidNode i do explodeGroup i
	
fn getColor r g b = (dotnetclass "System.Drawing.Color").FromArgb r g b

/* HTTP FUNCTIONS */

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
		
	try
	(
		w = dotNetObject (dotNetClass "System.Net.WebClient")
		return w.uploadFile url file

	)
	catch()
	
	return false
)

 --httpUpload @"D:\PROJECTS\ASSETS LIBRARY\CD 155\aaa.zip"


fn findTag t =
(
	s = filterString t ","
	if(s.count == 0) do return #()
	tag = trimLeft (trimRight s[s.count] " ") " "
	
	find = for i in tags where tag.count > 0 and matchPattern i pattern: (tag + "*") collect i
		
	return find
)

fn saveAllProp = 
(
	saveProp "edtModelName" sModelName
	saveProp "edtManufacturer" sManufacturer
	saveProp "edtModeller" sModeller
	saveProp "edtProject" sProject
	saveProp "edtTags" sTags
	saveProp "catID" catID
		
	saveProp "cbxRigged" bRigged
	saveProp "cbxAnimated" bAnimated
	saveProp "cbxBakedTextures" bBakedTextures
	saveProp "cbxLods" bLods
	saveProp "cbxUnwrap" bUnwrap
	
	saveProp "ddlLib" iCat1
	saveProp "ddlCat" iCat2
	saveProp "ddlSubCat" iCat3
		
	saveProp "category1" (implodeCat category1)
	saveProp "category2" (implodeCat category2)
	saveProp "category3" (implodeCat category3)
	saveProp "edtOverview" sOverview
)

rollout _STEP1 "STEP 1: Model Transform" 
(
	group "1. Geometry Fix"
	(
		button btnResetXForm "Reset XForm" width: 80 across: 2 tooltip: "Collaps modifier stack and reset objects transformation."
		button btnBoxTrick "Box Trick" width: 80 tooltip: "Fix broken geometry.\nAttach  selected objects to box then remove generated faces of box."
	)

	group "2. Group"
	(
		button btnGroup "Group All" width: 80 tooltip: "Make group from selected objects." across: 2
		button btnUngroup "Ungroup All" width: 80 tooltip: "Recursive explode all groups."
	)
	
	group "3. Set Pivot"
	(
		button btnSetPivotBotton "Bottom" across: 5 tooltip: "Set pivot point in center and bottom of objects." align: #right offset: [15,0] width: 50
		label lblOr "or" width: 10 offset: [10,3] 
		button btnSetPivotCenter "Center" tooltip: "Set pivot point in center of objects." align: #left width: 50
		label lblOr2 "or" width: 10 offset: [-6,3]
		button btnSetPivotTop "Top" tooltip: "Set pivot point in center and top of objects." align: #left offset: [-15,0] width: 50
	)
	
	group "4. Position"
	(
		button btnPostiton "Position in Center Scene" tooltip: "Move objects in to center of scene."
	)
	
	Timer tmrBoxTrick "" pos:[0,0] width:24 height:24 interval:100 active:false
	
	fn setPivot zpos: undefined =
	(
		selection.pivot = selection.center
		if(zpos != undefined) do selection.pivot.z = zpos
			
		ForceCompleteRedraw()
	)
	
	on tmrBoxTrick tick do
	(		
		clearListener()
		progressStart "Box Trick"
		escapeEnable
			
		hObj = selection as array
	
		forSelect = #()
		
		for i = 1 to hObj.count where not isDeleted hObj[i] and superClassOf hObj[i] == GeometryClass do
		(														
			g = isGroup	hObj[i]
			
			convertToMesh hObj[i]
			convertToPoly hObj[i]
			
			b = convertToPoly(box())				
			b.rotation = hObj[i].rotation
			b.name = hObj[i].name
			b.pos = hObj[i].pos
			b.wirecolor = hObj[i].wirecolor
			setUserPropBuffer b (getUserPropBuffer hObj[i])
					
			b.EditablePoly.SetSelection #Face #{1..6}
			b.EditablePoly.delete #Face		
			
			polyop.attach b hObj[i]						
			
			progressUpdate (100.0 * i / hObj.count)	
						
			if(getProgressCancel() == true) do exit	

				
			append forSelect b
			setUserProp b "boxtrick" true
		)
		
		progressEnd()
		
		btnBoxTrick.enabled = true
		tmrBoxTrick.active = false
		
		try(select forSelect)catch()
	)
	
	on btnBoxTrick pressed do
	(
		if(not isSelected()) do return false
		
		ungroupRecursive selection
			
		tmrBoxTrick.active = true
		btnBoxTrick.enabled = false
	)
	
	on btnResetXForm pressed do
	(
		if(not isSelected()) do return false
		
		for i in selection where superClassOf i == GeometryClass do
		(			
			ResetXForm i
			collapseStack i
			
			setUserProp i "xform" true
		)
	)
	
	on btnGroup pressed do
	(
		if(not isSelected()) do return false
	--	if(sModelName.count < 1) do return messageBox "Please do STEP 1" title: "Warning!"
			
		ungroupRecursive selection
			
		group selection name: "Container" select:true
	)

	on btnUngroup pressed do
	(
		if(not isSelected()) do return false
			
		ungroupRecursive selection
	)
	
	on btnSetPivotBotton pressed do
	(
		if(not isSelected()) do return false
			
		setPivot zpos:selection.min.z
	)
	
	on btnSetPivotTop pressed do
	(
		if(not isSelected()) do return false
			
		setPivot zpos:selection.max.z
	)
	
	on btnSetPivotCenter pressed do
	(
		if(not isSelected()) do return false
			
		setPivot()
	)
	
	on btnPostiton pressed do
	(
		if(not isSelected()) do return false
		
		g = group selection

		g.pos = [0,0,0]
		g.pos.z = 0 - selection.min.z
			
		ungroup g		
	)
	
	progressBar hr "" height: 3 offset:[0,15]
	button btnNext "Next >>" align: #right  enabled: true	
	on btnNext pressed do 
	(
		if(not isSelected()) do return false	
			
		goStep 2
	)
)

rollout _STEP2 "STEP 2: Model Info" 
(	

	group "Category"
	(
		label lbl6 "Library:" align: #left across: 2 
		dotNetControl lblWarn3 "System.Windows.Forms.Label" text: "*" offset: [-84, 0] 		
		dropdownlist ddlLib "" 
		
		label lbl7 "Category:" align: #left across: 2 
		dotNetControl lblWarn4 "System.Windows.Forms.Label" text: "*" offset: [-72, 0] 
		dropdownlist ddlCat ""
		
		label lbl8 "Sub Category:" align: #left across: 2 
		dotNetControl lblWarn5 "System.Windows.Forms.Label" text: "*" offset: [-51, 0] 
		dropdownlist ddlSubCat ""
	)
	
	group "Model Info"
	(
		label lbl1 "Model Name:" align: #left across: 2  	
		dotNetControl lblWarn1 "System.Windows.Forms.Label" text: "*" offset: [-59, 0] 
		dotNetControl edtModelName "System.Windows.Forms.TextBox" height: 25 tabstop: true
		label lbl2 "Manufacturer:" align: #left		
		dotNetControl edtManufacturer "System.Windows.Forms.TextBox" height: 25 tabstop: true
		label lbl3 "Modeller:" align: #left		
		dotNetControl edtModeller "System.Windows.Forms.TextBox" height: 25 tabstop: true
		label lbl4 "Project:" align: #left		
		dotNetControl edtProject "System.Windows.Forms.TextBox" height: 25 tabstop: true
		label lbl5 "Tags:" align: #left across: 2 	
		dotNetControl lblWarn2 "System.Windows.Forms.Label" text: "*" offset: [-94, 0]
		dotNetControl edtTags "System.Windows.Forms.TextBox" height: 25 tabstop: true
		
		button btnTag1 "" across: 4 visible: false width: 60 border: false
		button btnTag2 ""  visible: false width: 60 border: false
		button btnTag3 "" visible: false width: 60 border: false
		button btnTag4 "" visible: false width: 60 border: false
	)
	
	group "Additional Info"
	(
		checkbox cbxUnwrap "Unwrap"
		checkbox cbxRigged "Rigged"
		checkbox cbxAnimated "Animated"
		checkbox cbxBakedTextures "Baked Textures"
		checkbox cbxLods "Lods"
		
	)
	
	timer tmr1 "Tmr1" active: false interval: 300
	progressBar hr "" height: 3 offset:[0,15]
	button btnPrev "<< Back" align: #left  across: 2
	button btnNext "Next >>" align: #right  enabled: true 
	
	global bgErrorColor = getColor 255 229 153
	global fgErrorColor = getColor 215 144 0
	
	fn switchControl e d = if(e.KeyCode == e.KeyCode.Enter or e.KeyCode == e.KeyCode.Tab)  do try(setFocus d)catch()
		
	on edtModelName KeyUp s e do switchControl e edtManufacturer
	on edtManufacturer KeyUp s e do switchControl e edtModeller
	on edtModeller KeyUp s e do switchControl e edtProject
	on edtProject KeyUp s e do switchControl e edtTags
		
	fn initDotNet = 
	(			
		edtModeller.AutoCompleteMode = edtModeller.AutoCompleteMode.SuggestAppend
		edtModeller.AutoCompleteSource = edtModeller.AutoCompleteSource.CustomSource
		
		edtModelName.AutoCompleteMode = edtModelName.AutoCompleteMode.SuggestAppend
		edtModelName.AutoCompleteSource = edtModelName.AutoCompleteSource.CustomSource
		
		edtManufacturer.AutoCompleteMode = edtManufacturer.AutoCompleteMode.SuggestAppend
		edtManufacturer.AutoCompleteSource = edtManufacturer.AutoCompleteSource.CustomSource		
		
		edtProject.AutoCompleteMode = edtProject.AutoCompleteMode.SuggestAppend
		edtProject.AutoCompleteSource = edtProject.AutoCompleteSource.CustomSource	
		
	
		warnLabels = #(lblWarn1, lblWarn2, lblWarn3, lblWarn4, lblWarn5)
		bg = getBgColor()
		txt = getTextColor()
				
		for i in warnLabels do 
		(
			i.backColor = bg
			i.foreColor = i.foreColor.red
		)
	)
	
	
	fn trim t = trimLeft (trimRight t " ") " "
	
	fn setTagText t = 
	(
		c = for i in _STEP2.controls where isProperty i "name" and matchPattern i.name pattern: "btnTag*" collect i
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
		sModelName = edtModelName.text		
		sManufacturer = edtManufacturer.text
		sModeller = edtModeller.text
		sProject = edtProject.text
		sTags = edtTags.text

		bRigged = cbxRigged.checked
		bAnimated = cbxAnimated.checked
		bBakedTextures = cbxBakedTextures.checked
		bLods = cbxLods.checked
		bUnwrap = cbxUnwrap.checked
			
		iCat1 = ddlLib.selection
		iCat2 = ddlCat.selection
		iCat3 = ddlSubCat.selection

		if(catID == -1) do return messageBox "Please select the category!" title: "Warning!"		
		if(sModelName.count == 0) do return messageBox "Please enter correct Model Name!" title: "Warning!"		
		if(sTags.count == 0) do return messageBox "Please enter few tags!" title: "Warning!"
				
		if(not isValidText sModelName title: "Model Name") do return false 
		if(not isValidText sManufacturer title: "Manufacturer") do return false 
		if(not isSelected()) do return false
			
		saveAllProp()
		
		goStep 3	
	)
	
	on btnPrev pressed do
	(
		goStep 1
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
	
	on edtModelName LostFocus s a do isValidText (edtModelName.text) title: "Model Name"
	on edtManufacturer LostFocus s a do isValidText (edtManufacturer.text) title: "Manufacturer"
		
	fn getList s = 
	(
		l = filterString s ";"
		items = #(#(-1, ""))
		for i in l do append items (filterString i "|")
		
		return items
	)
	
	fn getCat id =
	(
		--catID = -1
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
	
	on _STEP2 open do
	(	
		initDotNet()

		edtModelName.text = getProp "edtModelName"
		edtManufacturer.text = getProp "edtManufacturer" 
		edtModeller.text =  getProp "edtModeller"
		edtProject.text =  getProp "edtProject"
		edtTags.text = getProp "edtTags"

		cbxRigged.checked = getProp "cbxRigged" type: #bool
		cbxAnimated.checked = getProp "cbxAnimated" type: #bool
		cbxBakedTextures.checked = getProp "cbxBakedTextures" type: #bool
		cbxLods.checked = getProp "cbxLods" type: #bool
		cbxUnwrap.checked	= getProp "cbxUnwrap" type: #bool
		
		category1 = explodeCat (getProp "category1")
		category2 = explodeCat (getProp "category2")
		category3 = explodeCat (getProp "category3")
		
		ddlLib.items = for i in category1 collect i[2]
		ddlCat.items = for i in category2 collect i[2]
		ddlSubCat.items = for i in category3 collect i[2]
		
		ddlLib.selection = getProp "ddlLib" type: #int
		ddlCat.selection = getProp "ddlCat" type: #int
		ddlSubCat.selection = getProp "ddlSubCat" type: #int
		
		catID = (getProp "catID" type: #negative) as integer
		
		tmr1.active = true	
	
		zipper = zip()
	)
		
	on _STEP2 close do
	(
	
	)
	
	on tmr1 tick do
	(	
		category1 = getCat 0
				
		ddlLib.items = for i in category1 collect i[2]
			
		tags = getTagsList()			
		modellers = getModellerList()
		
		edtModeller.AutoCompleteCustomSource.AddRange modellers
		
		for i in getModels() do 
		(	
			t = filterString i "|"
			if(t.count > 1) do
			(
				appendIfUnique models t[1]
				appendIfUnique manufacturers t[2]
				appendIfUnique projects t[3]
			)
		)
		
		
		edtProject.AutoCompleteCustomSource.AddRange projects
		edtModelName.AutoCompleteCustomSource.AddRange models
		edtManufacturer.AutoCompleteCustomSource.AddRange manufacturers
					
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

rollout _STEP3 "STEP 3: Model Overview" 
(
	group "Overview"
	(
		dotNetControl edtOverview "System.Windows.Forms.TextBox"  height: 250
	)
		
	on _STEP3 open do
	(
		edtOverview.MultiLine = true
		edtOverview.scrollbars = (dotnetclass "System.Windows.Forms.ScrollBars").vertical		
		edtOverview.acceptsReturn = true
		edtOverview.acceptsTab = true
		edtOverview.wordWrap = true
		
		edtOverview.text = getProp "edtOverview"
	)
	
	on edtOverview keyPress s a do
	(
		if(a.keyChar == "\r") do 
		(
			edtOverview.text += "\r\n"
			edtOverview.SelectionStart = (edtOverview.text.count)
		)
	)
	
	progressBar hr "" height: 3 offset:[0,15]
	button btnPrev "<< Back" align: #left  across: 2
	button btnNext "Next >>" align: #right  enabled: true	
	on btnPrev pressed do 
	(	
		sOverview = edtOverview.text
		goStep 2		
	)
	on btnNext pressed do 
	(
		if(not isSelected()) do return false
					
		sOverview = edtOverview.text			
		saveProp "edtOverview" sOverview
			
		goStep 4	
	)
)

rollout _STEP4 "STEP 4: Preview" 
(
	group "Preview"
	(
		dotNetControl btmPreview "System.Windows.Forms.PictureBox" width: 240 height: 240		
		label lblDimensions ""
		dropdownlist ddlPreview "" align: #left width: 210 across: 2
		button btnDelPreview "X" align: #right tooltip: "Delete Preview"
		checkbox cbxMainPreview "Main Preview"
	)

	button btnPreviewAdd "Add New Picture" offset: [0, 12]
		
	progressBar hr "" height: 3 offset:[0,15]
	button btnPrev "<< Back" align: #left  across: 2
	button btnNext "Next >>" align: #right  enabled: true	
	on btnPrev pressed do goStep 3		
	on btnNext pressed do 
	(	
		if(not isSelected()) do return false
		if(sPreviewPath.count == 0) do return messageBox "Please add at least one image!" title: "Warning!"
					
		saveProp "sPreviewPath" (implode sPreviewPath)
		saveProp "bMainPreview" (implode bMainPreview)
			
		goStep 5
	)
	
	
	fn checkMainPreview =
	(
		a = makeUniqueArray bMainPreview
		if(a.count == 1 and a[1] == false) do bMainPreview[1] = true
		s = ddlPreview.selection 
		if(s != 0) do cbxMainPreview.checked = if(bMainPreview[s] == true) then true else false
	)
	
	fn setMainPreview i x =
	(
		for p in 1 to bMainPreview.count do bMainPreview[p] = false
		bMainPreview[i] = x				
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
	
	fn showPreview f show: false =
	(	
		s = ddlPreview.selection 
			
		if(f == undefined or not doesFileExist f) do return false
		
		b = openBitMap f		
		setClipboardBitmap  b
		clipboard = dotNetClass "System.Windows.Forms.Clipboard"
		img = clipboard.GetImage()
		btmPreview.image = img
		
		w = b.width as string
		h = b.height as string
		
		lblDimensions.caption = w + " x " + h	
		
		checkMainPreview()
	)
	
	on ddlPreview selected x do
	(
		showPreview sPreviewPath[x]
	)
	
	on btnPreviewAdd pressed do
	(
		f = getOpenImage()
				
		if(f.count != 0) do
		(
			checkMainPreview()
			
			for i in f do 
			(	
				append sPreviewPath i
				append bMainPreview false
			)
						
			ddlPreview.items = for i in sPreviewPath collect filenameFromPath i

			ddlPreview.selection = sPreviewPath.count

			showPreview f[f.count]							
		)
	)
	
	on btnDelPreview pressed do
	(
		s = ddlPreview.selection
				
		if(s != 0) do 
		(
			lblDimensions.caption = ""
			cbxMainPreview.checked = false
			deleteItem sPreviewPath s
			deleteItem bMainPreview s
			ddlPreview.items = for i in sPreviewPath collect filenameFromPath i
			ddlPreview.selection = 1
			if(sPreviewPath.count != 0) then showPreview sPreviewPath[1] else btmPreview.image = undefined			
			checkMainPreview()
		)
		
	)
	
	on cbxMainPreview changed x do 
	(	
		s = ddlPreview.selection
				
		if(s != 0) do 
		(
			setMainPreview s x
			checkMainPreview()
		)
	)
	
	on _STEP4 open do
	(
		btmPreview.SizeMode = (dotNetclass "System.Windows.Forms.PictureBoxSizeMode").Zoom  
		
		sPreviewPath = explode (getProp "sPreviewPath")
		t = explode (getProp "bMainPreview")
		bMainPreview = for i in t collect i as booleanClass
		
		ddlPreview.items = for i in sPreviewPath collect filenameFromPath i

		if(sPreviewPath.count > 0) do showPreview sPreviewPath[1]
	)
)

rollout _STEP5 "STEP 5: Pack Model" 
(
	group "Summary"
	(
		dotNetControl Category "System.Windows.Forms.Label" text: "" 
		
		dotNetControl lbl1 "System.Windows.Forms.Label" text: "Model Name:" across: 2 offset: [0, 20]
		dotNetControl _lblModelName "System.Windows.Forms.Label" text: "" offset: [0, 20]
		
		dotNetControl lbl2 "System.Windows.Forms.Label" text: "Manufacturer:" across: 2 
		dotNetControl _lblManufacturer "System.Windows.Forms.Label" text: "" 
		
		dotNetControl lbl3 "System.Windows.Forms.Label" text: "Modeller:" across: 2 
		dotNetControl _lblModeller "System.Windows.Forms.Label" text: "" 
		
		dotNetControl lbl4 "System.Windows.Forms.Label" text: "Project:" across: 2 
		dotNetControl _lblProject "System.Windows.Forms.Label" text: "" 
		
		dotNetControl lbl5 "System.Windows.Forms.Label" text: "Tags:" across: 2 
		dotNetControl _lblTags "System.Windows.Forms.Label" text: "" 
				
		dotNetControl lbl55 "System.Windows.Forms.Label" text: "Format:" across: 2 offset: [0, 20]
		dotNetControl _lblFormat "System.Windows.Forms.Label" text: "" offset: [0, 20]	
		
		dotNetControl lbl6 "System.Windows.Forms.Label" text: "Units:" across: 2
		dotNetControl _lblUnits "System.Windows.Forms.Label" text: ""	

		dotNetControl lbl7 "System.Windows.Forms.Label" text: "Dimension:" across: 2 
		dotNetControl _lblDim "System.Windows.Forms.Label" text: "" 
		
		dotNetControl lbl8 "System.Windows.Forms.Label" text: "Polys:" across: 2 
		dotNetControl _lblPolys "System.Windows.Forms.Label" text: "" 
		
		dotNetControl lbl9 "System.Windows.Forms.Label" text: "Render:" across: 2 
		dotNetControl _lblRender "System.Windows.Forms.Label" text: "" 
		
		dotNetControl lbl10 "System.Windows.Forms.Label" text: "Overview:" across: 2 
		dotNetControl _lblOverview "System.Windows.Forms.Label" text: "" 
		
		dotNetControl lbl11 "System.Windows.Forms.Label" text: "Preview:" across: 2 
		dotNetControl _lblPreview "System.Windows.Forms.Label" text: "" 
		
		dotNetControl lbl12 "System.Windows.Forms.Label" text: "Rigged:" across: 2 
		dotNetControl _lblRigged "System.Windows.Forms.Label" text: "" 
		
		dotNetControl lbl13 "System.Windows.Forms.Label" text: "Animated:" across: 2 
		dotNetControl _lblAnimated "System.Windows.Forms.Label" text: "" 
		
		dotNetControl lbl14 "System.Windows.Forms.Label" text: "Baked:" across: 2 
		dotNetControl _lblBaked "System.Windows.Forms.Label" text: "" 
		
		dotNetControl lbl15 "System.Windows.Forms.Label" text: "Unwrap:" across: 2 
		dotNetControl _lblUnwrap "System.Windows.Forms.Label" text: "" 
		
		dotNetControl lbl16 "System.Windows.Forms.Label" text: "Lods:" across: 2 
		dotNetControl _lblLods "System.Windows.Forms.Label" text: "" 
	)
	
	button btnPackModel "Pack Model" width: 244 height: 45
		
	fn getNA t = if(t == "" or t.count == 0) then "N/A" else t
	fn getYesNo t = if(t == true) then "Yes" else "No"
	
	fn getControls n =
	(
		c = #()
		for i in _STEP5.controls where classof i == dotNetControl do
		(  			
			s = filterString (i as string) ":" 
			
			if(matchPattern s[2] pattern: (n + "*")) do append c i
		)
		
		return c
	)
	
	on _STEP5 open do
	(		
		bg = getBgColor()
		txt = getTextColor()
	
		labels = getControls "lbl"
		
		for i in labels do
		(
			i.backColor = bg
			i.foreColor = txt
			i.width = 100
		)
		
		labels = getControls "_lbl"
		for i in labels do
		(
			i.backColor = bg
			i.foreColor = txt
			i.pos.x -= 40
			i.width += 40
		)
		
		Category.backColor = bg
		Category.foreColor = txt
		
		Category.text = "No category"
		if(category3[iCat3][2] != "") do	Category.text = category1[iCat1][2] + " / " + category2[iCat2][2] + " / " + category3[iCat3][2]
		_lblModelName.text = getNA sModelName
		_lblManufacturer.text = getNA sManufacturer
		_lblModeller.text = getNA sModeller
		_lblProject.text = getNA sProject
		_lblTags.text = getNA sTags
		_lblPreview.text = getYesNo (sPreviewPath.count > 0)
		_lblUnits.text = getUnits()
		_lblDim.text = getDim selection
		_lblPolys.text = polyCount selection
		_lblRender.text = (getRender())[2]
		_lblOverview.text = getYesNo (sOverview.count > 0)
		_lblRigged.text = getYesNo bRigged
		_lblAnimated.text = getYesNo bAnimated
		_lblBaked.text = getYesNo bBakedTextures
		_lblUnwrap.text = getYesNo bUnwrap
		_lblLods.text = getYesNo bLods
		_lblFormat.text = (getMaxVersion())[1]
	)
		
	fn checkTexturesError =
	(
		errorTex = #()
		oldTex = #()
				
		for i in selection where i.material != undefined do
		(
			for type in BitmapTypes where classOf type == textureMap do
			(
				for b in getClassInstances type target:i where b != undefined do
				(				
					if(invalidBitmap b and findItem oldTex b == 0) then
					(										
						append errorTex #("empty file name path! Map: " + b.name, i.name) 
						append oldTex b
					)
					else if (validBitmap b and doesFileExist (getFullPath (b.filename)) == false) then
					(
						append errorTex #(b.filename, i.name) 
					)
				)
			)
		)
		
		if (errorTex.count > 0) do
		(
			m = "[" + localTime + "] : Error! Not found next textures. Please fix model and try again.\n\n"
			
			for er in errorTex do m += "Object: " + er[2] + "\nTexture: " + er[1] + "\n\n"
			
			messageBox m title: "Error!"
			
			return true
		)
		
		return false
	)
	
	fn copyPreview szPreview szDir =
	(
		out = #()
		
		iPreview = 0
		for i in 1 to sPreviewPath.count do
		(
			p = getFilenameType sPreviewPath[i] 
			if(doesFileExist sPreviewPath[i]) do 
			(								
				if(bMainPreview[i]) then 
				(
					n = (szDir + "main" + p) 
					copyFile sPreviewPath[i] n
					append out n
				)
				else
				(
					iPreview += 1
					n = (szPreview + "picture" + iPreview as string + p)
					copyFile sPreviewPath[i] n
					append out n
				)
			)
		)

		return out
	)
		
	fn copyMaps szMaps szName = 
	(
		oldTex = #()
		oldTexPaths = #()
		toRename = #()
		iCnt = 1
				
		for i in selection where i.material != undefined do
		(
			for type in BitmapTypes where classOf type == textureMap do 
			(	
				for b in getClassInstances type target:i where (validBitmap b) and findItem oldTex b == 0 do
				(																	
					f = getFullPath (b.filename)
				
					n = filenameFromPath f
					p = getFilenamePath f
					ext = getFilenameType f
							
					n = szName + "-" + (getNum iCnt) + ext
										
					if(doesFileExist f) do
					( 
						ep = findItem oldTexPaths b.filename
						
						if(ep != 0) then n = toRename[ep][5]
						
						append oldTex b
						append toRename #(b, (p + n), (szMaps + n), f, n) 
				
						if(ep == 0) do iCnt += 1
						append oldTexPaths b.filename
					)												
				)
			)
		)
							
		for t in toRename do
		(
			t[1].filename = @"maps\" + t[5]
			t[1].name = getFilenameFile t[5]
			
			copyFile t[4] t[3]
		)		
	)
	
	fn renameObjects szName = 
	(
		g = objects
				
		for i in 1 to g.count where isValidNode g[i] do
		(
			g[i].name = szName + "-" + (getNum i) 
		)
	)
	
	fn getMaterialDependence s type =
	(
		sceneMaps = #()
		
		for c in type.classes where c != undefined and findItem BitmapTypes c == 0 do 
		(		
			for g in s do
			(
				for m in getClassInstances c target:g where m != undefined do 
				(					
					appendIfUnique sceneMaps m
				)
			)
		)	
		
		return sceneMaps
	)
	
	fn renameMaterials szName = 
	(
		oldMtl = #()
		iCnt = 1
						
		toRename = #()
		mat = getMaterialDependence selection material
		tex = getMaterialDependence selection textureMap	
		
		join toRename mat
		join toRename tex
				
		for i in toRename do 
		(
			i.name = szName + "-" + (getNum iCnt)
			iCnt += 1	
		)
	)
	
	fn removeLayers = 
	(
		layer0 = LayerManager.getLayer 0
		for i in objects do layer0.addNode i
	)
	
	fn copyIES szAssets szName = 
	(	
		global iIesCnt = 1
		
		fn copyIes ies szAssets szName =
		(
			if(ies == undefined) do return undefined
			
			ies = getFullPath ies
			
			if(not doesFileExist ies) do return ies
			
			n = filenameFromPath ies
			ext = getFilenameType ies
			
			f = szAssets  + "IES-" + szName + "-" + (getNum iIesCnt) + ext
			
			copyFile ies f
			
			iIesCnt += 1
			
			return f
		)
				
		for l in lights where l.isSelected == true do
		(
			if(isProperty l #ies_file) do l.ies_file = copyIes (l.ies_file) szAssets szName
			if(isProperty l #webFile) do l.webFile = copyIes (l.webFile) szAssets szName
			if(isProperty l #iesFile) do l.iesFile = copyIes (l.iesFile) szAssets szName
		)		
	)
	
	fn createInfo szDir previewList =
	(
		f = szDir + "info.ini"
		v = getMaxVersion()
		r = getRender()
		p = polyCount geometry
		d = getDim geometry
		u = getUnits()
		
		o = substituteString sOverview "\r\n" "|"
		
		setIniSetting f "INFO" "TYPE" "model"
		setIniSetting f "INFO" "CATID" (catID as string)
		setIniSetting f "INFO" "NAME" sModelName
		setIniSetting f "INFO" "FORMAT" v[1]
		setIniSetting f "INFO" "RENDER" r[2]
		setIniSetting f "INFO" "POLYS" p
		setIniSetting f "INFO" "UNITS" u
		setIniSetting f "INFO" "DIMENSION" d
		setIniSetting f "INFO" "PROJECT" (getNA sProject)
		setIniSetting f "INFO" "RIGGED" (getYesNo bRigged)
		setIniSetting f "INFO" "LODS" (getYesNo bLods)
		setIniSetting f "INFO" "MODELLER" sModeller
		setIniSetting f "INFO" "TAGS" sTags
		setIniSetting f "INFO" "MANUFACTURER" sManufacturer
		setIniSetting f "INFO" "OVERVIEW" o
		setIniSetting f "INFO" "UNWRAP" (getYesNo bUnwrap)
		setIniSetting f "INFO" "PREVIEW" (getYesNo (sPreviewPath.count > 0))
		
		iCnt = 0	
		for i in previewList do 
		(
			j = substituteString i szDir ""
			setIniSetting f "PREVIEW" (iCnt as string) j
			iCnt += 1
		)
	)
	
	on btnPackModel pressed do
	(
		if(not isSelected()) do return false
		fileToLoad = maxFilePath + maxFileName
		if(fileToLoad.count < 1) do return messageBox "Please save scene!" title: "Warning!"
		if(sModelName.count < 1) do return messageBox "Please specify Model Name in STEP 1!" title: "Warning!"
		if(sTags.count < 1) do return messageBox "Please specify Tags in STEP 1!" title: "Warning!"
		if(catID == -1) do return messageBox "Please select the category in STEP 1!" title: "Warning!"	
		max file save
		
		-- Set paths
		
		szName = stripName sModelName
		
		szDir = _SAVEPATH + szName + @"\"		
		szMaps = szDir + @"maps\"
		szAssets = szDir + @"assets\"
		szPreview = szDir + @"preview\"
		szExport = szDir + @"export\"
		szInfo = szDir  + "info.ini"
		
		-- Set out zip
		szZip = _SAVEPATH + szName + ".zip"		 
				
		-- Make dirs
		
		makeDir szDir	
		makeDir szMaps	
		makeDir szAssets
		makeDir szPreview	
		makeDir szExport	
		
		-- Check tex errors	
		if(checkTexturesError()) do return false
		
		-- Copy previews
		previewList = copyPreview szPreview szDir
			
		-- Copy maps		
		copyMaps szMaps szName
			
			
		-- Copy IES
		copyIES szAssets szName	
			
		-- Save render
		currentRender = renderers.current
	
		-- Save units
		SystemType = units.SystemType
		SystemScale = units.SystemScale 
		DisplayType = units.DisplayType		
			
		-- Add Versions
				
		saveNodes selection (szDir + "_tmp.max") quiet:true
		resetMaxFile #noPrompt
		try(
			units.SystemType = SystemType
			units.SystemScale = SystemScale
			units.DisplayType	= DisplayType
		)catch()
		
		
		mergeMAXFile (szDir + "_tmp.max") #select #mergedups #renameMtlDups #noRedraw quiet:true			
		deleteFile (szDir + "_tmp.max")
			
		try(renderers.current = currentRender)catch()
					
		select objects
			
		renameObjects szName
		renameMaterials szName 
		removeLayers()
		
		v = getMaxVersion()
		r = getRender()
		
		-- Save file properties
		saveAllProp()
		
		f = szDir + szName + r[1] + ".max"
		saveMaxFile f saveAsVersion:(v[1] as integer) quiet:true
		
		f = szExport + szName + r[1] + ".fbx"
		FBXExporterSetParam "FileVersion" "FBX201200"	-- Warning!! This option important!!
		exportFile f #noPrompt  --selectedOnly:true
		
		createInfo szDir previewList
				
		zipper.packFiles source: szDir target: _SAVEPATH name: (szName + " (" + r[2] + ")") del: true
		
		resetMaxFile #noPrompt
		loadMaxFile fileToLoad useFileUnits: true quiet:true		
		
		q = queryBox "Model packed success!\nDo you want to open containing folder?" title: "Success!"
		if(q) do shellLaunch _SAVEPATH ""		
	)
	
	progressBar hr "" height: 3 offset:[0,15]
	button btnPrev "<< Back" align: #left 
	on btnPrev pressed do goStep 4	
)


rollout rAbout "About" 
(
	label lbl1 "Assets Library Pack Model" offset: [0, 30]
	label lbl2 "v1.0.1"
	label lb3 "Vasily Lukyanenko"
	label lb4 "VISCO" offset: [0, 20]
	label lbl5 "" offset: [0, 15]
	
)

goStep 1

