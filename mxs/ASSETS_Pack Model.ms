/*  
[INFO] 

NAME = Pack Model
VERSION = 1.1.2
AUTHOR = Vasily Lukyanenko
DEV = VISCO
HELP = 

[INSTALL]

MACRO=PackModel
CAT=VISCO
TEXT=Pack Model
ICON=#("PhysX_Main", 30)

[1.0.0]

* First release =

[1.0.1]

+ Added: Corona support=

[1.0.2]

+ Added: Custom attribut 1=

[1.0.3]

+ Added: Geometry Fix - Snapshot=
+ Added: Geometry Fix - Detach Trick=
+ Added: Baloon messages=
+ Added: Tags editor=
+ Added: Model Properties - Game Engine Ready option=
+ Added: Options for rename Textures, Materials and Objects=
* Now any fix geometry converts to Editable Mesh for minimize scene size=
* Scaled buttons sizes=
* Display open dialog when click on preview=
* Step1 and Step2 swapped=

[1.0.4]

- BugFix: Crash on select category=

[1.0.5]

+ Added: Rename Groups option=

[1.0.6]

* Changed: Now script will close when you do reset/open/new scene=
+ Added: Load data about model from WebService DB, if you open model from Assets Library=
+ Added: Generate unique id for each model=


[1.0.7]

- BugFix: Not save Custom 1 option=

[1.0.8]

- BugFix: Incorrect IES paths=

[1.0.9]

+ Added: Remove Layers option= 

[1.1.0]

- BugFix: Not save Game Engine Ready option=
* Changed: Wider field for 3Ds Max Version dropdown=


[1.1.1]

- BugFix: Animated option not save=

[1.1.2]

+ Added: Save scene option=

[ABOUT]

This script helps to prepare model to upload=
for Assets Library=

[TEST]

[SCRIPT]
*/	


/* GLOBAL SETTINGS */

global _URL = "http://svg-web-003/"
global _SAVEPATH = @"C:\temp\Assets Library\"
global _SILENT_MODE = false

try(closeRolloutFloater fFastRender) catch()
try(destroyDialog rPostProcess) catch()

try(closeRolloutFloater rAssetsLibraryModelPack)catch()
global rAssetsLibraryModelPack = newRolloutFloater "Pack Model" 280 800

/* GLOBALS */

global _STEP1
global _STEP2
global _STEP3
global _STEP4
global _STEP5
global _rAbout
global _RelTagsList
global _ASSETS_PACK_TYPE = 1

global Keys = dotNetClass "System.Windows.Forms.Keys"
global szChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890-"
global szGoodChars = szChars + "&$%#()!_+ " 
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
global clients = #()
global sPreviewPath = #()
global bMainPreview = #()
global sOverview = ""
global sCustom1 = ""
global sClient = ""
global zipper
	
global sModelName = ""
global sManufacturer = ""
global sModeller = ""
global sProject = ""
global sTags = ""
global sToken = ""

global bRigged = false
global bAnimated = false
global bBakedTextures = false
global bGameEngineReady = false
global bLods = false
global bUnwrap = false
global bLights = false

global iCat1 = 1
global iCat2 = 1
global iCat3 = 1

global catId1 = 1
global catId2 = 1
global catId3 = 1

fn useSettings k p w =
(
	/*
		useSettings "key" "value/int/float/bool/bool_not" "r/w" 
	*/
	
	u = sysInfo.username
	d = getFilenamePath  (getThisScriptFilename())
	n = filenameFromPath (getThisScriptFilename())			
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

fn getPackTypeText pack: "Pack " = (
	a = case _ASSETS_PACK_TYPE of (2: "Scene"; default: "Model")
	return pack + a
)


fn getSelectionByPackType type: #objects = (
	if(_ASSETS_PACK_TYPE == 2) do (
		if(type == #geometry) do return geometry
		return objects
	)
	return selection
)

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
	fn packFile  source: "" target: "" name: "tmpzip" del: false = 
	(	
		out = name + ".zip"
		
		curDir = sysInfo.currentDir
		sysInfo.currentDir = getFilenamePath  source
		
		maz  out (filenameFromPath source)
		
		sysInfo.currentDir = curDir 
		
		if(del == true) do deleteFile source
		return true
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

struct XML
(
	xmlDoc,
	fn load r =
	(
		try
		(
			xmlDoc = dotNetObject "system.xml.xmlDocument"	
			xmlDoc.LoadXml r				
		)
		catch 
		(
			return false
		)
		
		return true
	),
	fn get v type: "value"=
	(
		try
		(
			o = xmlDoc.item["data"].item[v].GetAttribute(type)
			if(o == undefined) do o = ""
		)
		catch(return "")
		
		return o		
	)
)

/* MAIN FUNCTIONS */

fn trim s c: " " = trimRight (trimLeft s c) c

fn setPosRelTagsList =
(
	p = rAssetsLibraryModelPack.pos				
	p.x += _STEP2.width + 20
	try(SetDialogPos _RelTagsList p)catch()
)

fn rebuildPackModel =
(	
	try(closeRolloutFloater rAssetsLibraryModelPack)catch()
	--try(fileIn (getThisScriptFileName()))catch()
)

fn conversion s = 
(
	 dncConvert = dotNetClass "System.Convert"
	 dnvStringIn = (dotNetObject "System.String" "Dave")
	 dnoASCII = dotNetObject "System.Text.ASCIIEncoding"
	 dnvByteArrayIn = dnoASCII.getBytes dnvStringIn
	 arry = dotnetobject "System.Byte[]" dnvByteArrayIn.count
	 for i = 1 to dnvByteArrayIn.count do ( ByteArrayTools.SetValue arry (i - 1) dnvByteArrayIn[i] )
	 dnvB64String = dncConvert.ToBase64String arry
	 dnvByteArrayOut = dncConvert.FromBase64String dnvB64String
	 dnoUTF8 = dotNetObject "System.Text.UTF8Encoding"
	 arry = dotnetobject "System.Byte[]" dnvByteArrayOut.count
	 for i = 1 to dnvByteArrayOut.count do ( ByteArrayTools.SetValue arry (i - 1) dnvByteArrayOut[i] )
	 dnvStringOut = dnoUTF8.getString arry
	 return dnvStringOut
)

fn convertFileToUTF8 o n  =
(
	f = openFile o
	buffer = #()
	while (not eof f) do
	(
		szLine = readline f		
		szLine = trimRight (trimLeft szLine)		
		append buffer szLine
	)
	flush f
	close f	
	
	deleteFile o
	
	s = dotNetObject "System.IO.StreamWriter" (n) true ((dotNetClass "System.Text.Encoding").GetEncoding("UTF-8"))
	
	for l in buffer where  l.count > 0 do s.WriteLine(dotNetObject "System.String" l)			
	s.Close()		
)

fn generateUniqueID = 
(
	c = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890"
	code = ""
	
	sec = 9
	secNum = 5
	
	for i in 1 to sec do 
	(
		for ii in 1 to secNum do code += c[random 1 c.count] 
		if(i != sec) do code += "-"
	)
	
	return code
)

fn getScriptInfo s releaseInfo: "" =
(		
	if(releaseInfo != "") do
	(
		r = getINISetting s releaseInfo
		return r
	)
	
	v = getINISetting s "INFO" "VERSION"
	a = getINISetting s "INFO" "AUTHOR"
	n = getINISetting s "INFO" "NAME"
	o = getINISetting s "ABOUT"
	c = getINISetting s "INFO" "DEV"
	h = getINISetting s "INFO" "HELP"
	
	r = for i in (getINISetting s) where i != "ABOUT" and i != "SCRIPT" and i != "COPY" collect i
	
	return #(n, a, v, o, r, c, h)
)


fn showTooltip c t title: "Warning!" show: 4000=
(
	toolTip = dotnetobject "System.Windows.Forms.ToolTip"
	toolTip.SetToolTip c t   
	--toolTip.Active = true    
	toolTip.isBalloon = true
	toolTip.InitialDelay = 100
	toolTip.AutoPopDelay = 500
	toolTip.ReshowDelay = 50
	toolTip.ToolTipTitle = title
	toolTip.Show t c show	
	case title of
	(
		"Warning!": toolTip.ToolTipIcon = (dotnetclass "System.Windows.Forms.ToolTipIcon").Warning
		"Info!": toolTip.ToolTipIcon = (dotnetclass "System.Windows.Forms.ToolTipIcon").Info
		"Error!": toolTip.ToolTipIcon = (dotnetclass "System.Windows.Forms.ToolTipIcon").Error
	)	
)

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

fn replaceBadChars s rule: szChars =
(
	badChars = for i in 1 to s.count where findString rule s[i] == undefined collect s[i]
	for c in badChars do s = substituteString s c ""
		
	return s
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
	k = #(_STEP1, _STEP2, _STEP3, _STEP4, _STEP5, _rAbout) 
	for i in k do 
	(
		removeRollout i rAssetsLibraryModelPack
	)
	
	addRollout k[step] rAssetsLibraryModelPack border: true
	addRollout _rAbout rAssetsLibraryModelPack border: true rolledUp: true
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
		#int: return 0
		#negative: return -1
		#array: return #("")
		default: ""
	)
)

fn loadModelInfo id =
(
	saveProp "externalModelID" (id as string)
	print ("Load info for: " + id as string)
	try(fileIn (getThisScriptFileName()))catch()
)

fn isSelected =
(	
	if(selection.count < 1) do 
	(
		--messageBox "Please select model!" title: "Warning!"
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

fn isValidText t component =
(
	c = for i in 1 to t.count where findString szGoodChars t[i] == undefined collect t[i]

	if(c.count > 0) do 
	(
		c = makeUniqueArray c
		m = "Unsupported symbols:"
		for i in c do m += " " + i
				
		showTooltip component m
		return false
	)
	
	return true
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

fn getCurrRender =
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

fn saveMtlLib p =
(
	for i in 1 to currentMaterialLibrary.count do deleteItem currentMaterialLibrary 1
	for i in objects where i.material != undefined do append currentMaterialLibrary i.material
	saveMaterialLibrary p
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
	
	find = for i in tags where tag.count > 0 and matchPattern i pattern: (tag + "*") collect toLower i
		
	return find
)

fn saveAllProp saveCat: #auto = 
(
	saveProp "edtModelName" sModelName
	saveProp "edtManufacturer" sManufacturer
	saveProp "edtModeller" sModeller
	saveProp "edtProject" sProject
	saveProp "edtTags" sTags
	saveProp "catID" catID
	saveProp "edtToken" sToken
			
	saveProp "cbxRigged" bRigged
	saveProp "cbxAnimated" bAnimated
	saveProp "cbxBakedTextures" bBakedTextures
	saveProp "cbxGameEngineReady" bGameEngineReady
	saveProp "cbxLods" bLods
	saveProp "cbxUnwrap" bUnwrap
	saveProp "cbxLights" bLights
	
	case (saveCat) of
	(
		#auto: (
			saveProp "ddlLib" iCat1
			saveProp "ddlCat" iCat2
			saveProp "ddlSubCat" iCat3
			
			saveProp "catId1" (if(iCat1 > 0) then category1[iCat1][1] else -1)
			saveProp "catId2" (if(iCat2 > 0) then category2[iCat2][1] else -1)
			saveProp "catId3" (if(iCat3 > 0) then category3[iCat3][1] else -1)
				
			saveProp "category1" (implodeCat category1)
			saveProp "category2" (implodeCat category2)
			saveProp "category3" (implodeCat category3)	
		)
		#force: (
			saveProp "edtOverview" sOverview
			saveProp "edtCustom1" sCustom1
			saveProp "edtClient" sClient
			
			saveProp "catId1" catId1
			saveProp "catId2" catId2
			saveProp "catId3" catId3
			
			saveProp "sPreviewPath" (implode sPreviewPath)
			saveProp "bMainPreview" (implode bMainPreview)
		)
	)
)

fn getTrueFalse d =
(
	if(d == "Yes") do return true
	return false
)


fn getNA d = 
(
	if(d == "N/A") do return ""
	return d
)

rollout _STEP1 "STEP 1: Model Transform" 
(
	group "" (
		radiobuttons rdoPackType "" labels: #("Pack Model", "Pack Scene")	
	)
	
	group "1. Geometry Fix"
	(
		button btnResetXForm "Reset XForm" width: 80 height: 28 across: 2 tooltip: "Collaps modifier stack and reset objects transformation."
		button btnBoxTrick "Box Trick" width: 80 height: 28 tooltip: "Fix broken geometry.\nAttach  selected objects to box then remove generated faces of box."
		
		button btnSnapshot "Snapshot" width: 80 height: 28 across: 2 tooltip: "Standart 3ds max snaphot tool."
		button btnDetachTrick "Detach Trick" width: 80 height: 28 tooltip: "Recreates the object by detaching all its sub-objects.\nIt helps to fix vertex normal issues."
		
		checkbox cbxConvertToMesh "Convert to Mesh" checked: true
		button btnRemoveIsolatedVerts "Remove Isolated/Unused vertices" width: 200 height: 28  tooltip: "Delete floating vertices."
	)

	group "2. Group"
	(
		button btnGroup "Group All" width: 80 height: 28 tooltip: "Make group from selected objects." across: 2
		button btnUngroup "Ungroup All" width: 80 height: 28 tooltip: "Recursive explode all groups."
	)
	
	group "3. Set Pivot"
	(
		button btnSetPivotBotton "Bottom" across: 5 tooltip: "Set pivot point in center and bottom of objects." align: #right offset: [15,0] width: 50 height: 28
		label lblOr "or" width: 10 offset: [10,6] 
		button btnSetPivotCenter "Center" tooltip: "Set pivot point in center of objects." align: #left width: 50 height: 28
		label lblOr2 "or" width: 10 offset: [-6,6]
		button btnSetPivotTop "Top" tooltip: "Set pivot point in center and top of objects." align: #left offset: [-15,0] width: 50 height: 28
		checkbox cbxOnePivotForAll "One Pivot For All" tooltip: "All selected objects will have same pivot point."
	)
	
	group "4. Position"
	(
		button btnPostiton "Set to Scene Center" tooltip: "Move objects in to center of scene." height: 28
	)
	
	timer tmrBoxTrick "" pos:[0,0] width:24 height:24 interval:100 active:false
	
	progressBar hr "" height: 3 offset:[0,15]
	button btnNext "Next >>" align: #right  enabled: true width: 75 height: 28 
	dotnetcontrol nextWarn "System.Windows.Forms.Button" height:0 width:0 align: #right

	fn getModelById id =
	(
		clearListener()
		url = _URL + "vault/mxs/mxsGetModelByID.php?id=" + id + "&time=" + timeStamp() as string
		r = httpGet url	
			
		x = XML()
			
		if(x.load r == false) do return false	
		if(x.get "responce" != "OK") do return false
		
		sModelName = x.get "name"
		sManufacturer = getNA (x.get "manufacturer")
		sModeller = getNA (x.get "modeller")
		sProject = getNA (x.get "project")
		sTags = x.get "tags"
		catID = x.get "catid"
		sToken = x.get "token"
		sClient = getNA (x.get "client")
		sCustom1 = getNA (x.get "custom1")
		sOverview = subStituteString (x.get "overview") "|" "\r\n"
		
		bRigged = getTrueFalse (x.get "rigged")
		bAnimated = getTrueFalse (x.get "animated")
		bBakedTextures = getTrueFalse (x.get "baked")
		bGameEngineReady = getTrueFalse (x.get "gameengine")
		bLods = getTrueFalse (x.get "lods")
		bUnwrap = getTrueFalse (x.get "unwrap")
		bLights = getTrueFalse (x.get "lights")
			
		catId1 = x.get "cat1" type: "id"
		catId2 = x.get "cat2" type: "id"
		catId3 = x.get "cat3" type: "id"
		
		sPreviewPath = try(explode (x.get "previews"))catch(#())
		bMainPreview  = #(true)
			
		saveAllProp saveCat: #force
			
		m = "Data for this model was downloaded from DB!"	
		showTooltip nextWarn m title: "Info!" show: 10000
	)
	
	fn setPivot p: #bottom =
	(				
		if(cbxOnePivotForAll.checked) then (						
			for i in selection as array do 
			(	
				i.pivot = selection.center			
				pivotPoint = case p of
				(
					#bottom: i.pivot.z = selection.min.z
					#top: i.pivot.z = selection.max.z
				)					 
			)
		) else
		(
			for i in selection as array do 
			(	
				i.pivot = i.center			
				pivotPoint = case p of
				(
					#bottom: i.pivot.z = i.min.z
					#top: i.pivot.z = i.max.z				
				)					 
			)
		)
		
		ForceCompleteRedraw()
	)
	
	on cbxConvertToMesh changed v do useSettings "CONVERTTOMESH"	v "w"	
	
	on _STEP1 open do
	(
		callbacks.removeScripts id:#postNew_PackModel
		callbacks.removeScripts id:#postReset_PackModel
		callbacks.removeScripts id:#filePostOpen_PackModel
		
		callbacks.addScript #systemPostNew "rebuildPackModel()" id:#postNew_PackModel
		callbacks.addScript #systemPostReset "rebuildPackModel()" id:#postReset_PackModel
		callbacks.addScript #filePostOpen "rebuildPackModel()" id:#filePostOpen_PackModel
		
		
		v = useSettings "CONVERTTOMESH" "bool" "r"	
		cbxConvertToMesh.checked = if(v == false) then false else true
		
		try(rdoPackType.state = _ASSETS_PACK_TYPE) catch()
		
		-- Get Model Data from DB
		externalModelID = getProp "externalModelID"
		if(externalModelID != "" and externalModelID.count > 0) do 
		(			
			getModelById externalModelID			
		)
	)
	
	on btnRemoveIsolatedVerts pressed do
	(
		s = selection as array
		for o in s do
		(
			case (classOf o) of
			(
				Editable_Poly:
				(
					o.deleteIsoVerts()
					o.DeleteIsoMapVerts()
				)
				Editable_Mesh:
				(
					meshOp.deleteIsoVerts o
					meshOp.deleteIsoMapVertsAll o
				)
			)
		)
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
			if(cbxConvertToMesh.checked) do convertToMesh b
			
			progressUpdate (100.0 * i / hObj.count)	
						
			if(getProgressCancel() == true) do exit	

				
			append forSelect b
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
			if(not cbxConvertToMesh.checked and canConvertTo i Editable_Poly) do convertToPoly i
			if(cbxConvertToMesh.checked and canConvertTo i Editable_Mesh) do convertToMesh i
		)	
	)
	
	on btnSnapshot pressed do
	(
		if(not isSelected()) do return false
		
		forSelect = #()	
		s = selection as array
			
		for i in s where superClassOf i == GeometryClass and not isDeleted i and isValidNode i do
		(		
			a = snapShot i
			update a
			append forSelect a	
		
			a.rotation = i.rotation
			a.name = i.name
			a.pos = i.pos
			a.wirecolor = i.wirecolor
			setUserPropBuffer a (getUserPropBuffer i)
			if(not cbxConvertToMesh.checked and canConvertTo a Editable_Poly) do convertToPoly a				
			
			try(delete i) catch()			
		)
		
		select forSelect
	)
	
	on btnDetachTrick pressed do
	(
		if(not isSelected()) do return false
		
		clearListener()
		progressStart "Detach Trick"
		escapeEnable	
			
		forSelect = #()	
		iCnt = 0
		s = selection as array	
			
		for i in 1 to s.count where superClassOf s[i] == GeometryClass and not isDeleted s[i] and isValidNode s[i] do
		(	
			nn = "__DetachTrick_000000" + i as string
			
			convertToPoly s[i]
			f = polyop.getNumFaces s[i]
			if(f == 0) do 
			(
				try(delete s[i]) catch()
				continue
			)
			
			p = #{1..f} 
			polyop.detachFaces s[i] p asNode:true name: nn
			newObj = getNodeByName nn
			newObj.name =  s[i].name
			newObj.rotation = s[i].rotation
			newObj.name = s[i].name
			newObj.pos = s[i].pos
			newObj.wirecolor = s[i].wirecolor
			setUserPropBuffer newObj (getUserPropBuffer s[i])
			if(cbxConvertToMesh.checked) do convertToMesh newObj
						
			try(delete s[i]) catch()
						
			append forSelect newObj	
			
			progressUpdate (100.0 * i / s.count)							
			if(getProgressCancel() == true) do exit	
		)
		
		progressEnd()
		select forSelect
	)
	
	on btnGroup pressed do
	(
		if(not isSelected()) do return false
			
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
		
		setPivot p: #bottom
	)
	
	on btnSetPivotTop pressed do
	(
		if(not isSelected()) do return false
			
		setPivot p: #top
	)
	
	on btnSetPivotCenter pressed do
	(
		if(not isSelected()) do return false
			
		setPivot p: #center
	)
	
	on btnPostiton pressed do
	(
		if(not isSelected()) do return false
		
		g = group selection

		g.pos = [0,0,0]
		g.pos.z = 0 - selection.min.z
			
		ungroup g		
	)
	
	on btnNext pressed do 
	(
		if(not isSelected() and _ASSETS_PACK_TYPE == 1) do return showTooltip nextWarn "Please select objects!"
			
		goStep 2
	)
	
	on rdoPackType changed a do (
		_ASSETS_PACK_TYPE = a		
		rAssetsLibraryModelPack.title = getPackTypeText()
	)
)

rollout _STEP2 "STEP 2: Model Info" 
(	

	group "Category"
	(
		label lbl6 "Library:" align: #left across: 2 
		dotNetControl lblWarn3 "System.Windows.Forms.Label" text: "*" offset: [-84, 0] width: 10		
		dropdownlist ddlLib "" 
		
		label lbl7 "Category:" align: #left across: 2 
		dotNetControl lblWarn4 "System.Windows.Forms.Label" text: "*" offset: [-72, 0] width: 10	
		dropdownlist ddlCat ""
		
		label lbl8 "Sub Category:" align: #left across: 2 
		dotNetControl lblWarn5 "System.Windows.Forms.Label" text: "*" offset: [-51, 0] width: 10	
		dropdownlist ddlSubCat ""		
	)
	
	group "Model Info"
	(
		label lbl1 "Model Name:" align: #left across: 2  	
		dotNetControl lblWarn1 "System.Windows.Forms.Label" text: "*" offset: [-59, 0] width: 10	
		dotNetControl edtModelName "System.Windows.Forms.TextBox" height: 25 tabstop: true
		label lbl2 "Manufacturer:" align: #left		
		dotNetControl edtManufacturer "System.Windows.Forms.TextBox" height: 25 tabstop: true
		label lbl3 "Modeller:" align: #left		
		dotNetControl edtModeller "System.Windows.Forms.TextBox" height: 25 tabstop: true
		label lbl4 "Project:" align: #left		
		dotNetControl edtProject "System.Windows.Forms.TextBox" height: 25 tabstop: true
		label lbl5 "Tags:" align: #left across: 2 			
		dotNetControl lblWarn2 "System.Windows.Forms.Label" text: "*" offset: [-94, 0] width: 10	
		dotNetControl edtTags "System.Windows.Forms.TextBox" height: 60 tabstop: true across: 2 width: 180 readOnly: true
		button btnShowRelTags "Edit" align: #right offset: [0, 0] tooltip: "Select tags actual for current category." height: 60 width: 60
	
		-- Hidden TextBox with token
		dotNetControl edtToken "System.Windows.Forms.TextBox" height: 0 width: 0 readOnly: true 
	)
	
	group "Model Properties"
	(
		checkbox cbxUnwrap "  Unwrapped" across: 2
		checkbox cbxGameEngineReady "  Game Engine Ready"
		checkbox cbxLights "  Lights" across: 2
		checkbox cbxBakedTextures "  Baked Textures"
		checkbox cbxAnimated "  Animated" across: 2
		checkbox cbxLods "  Lods"
		checkbox cbxRigged "  Rigged"		
	)
		
	timer tmr1 "Tmr1" active: false interval: 300
	progressBar hr "" height: 3 offset:[0,15]
	button btnPrev "<< Back" align: #left  across: 2 width: 75 height: 28
	button btnNext "Next >>" align: #right  enabled: true width: 75 height: 28
	dotnetcontrol nextWarn "System.Windows.Forms.Button" height:0 width:0 align: #right
		
	global bgErrorColor = getColor 255 229 153
	global fgErrorColor = getColor 215 144 0
	
	fn switchControl e d = if(e.KeyCode == e.KeyCode.Enter or e.KeyCode == e.KeyCode.Tab)  do try(setFocus d)catch()
		
	on edtModelName KeyUp s e do switchControl e edtManufacturer
	on edtManufacturer KeyUp s e do switchControl e edtModeller
	on edtModeller KeyUp s e do switchControl e edtProject
	on edtProject KeyUp s e do switchControl e edtTags
	on _STEP2 moved p do
	(
		setPosRelTagsList()
	)
		
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
		
		edtTags.readOnly = true
		edtTags.ScrollBars = (dotNetClass "System.Windows.Forms.ScrollBars").Vertical
		edtTags.MultiLine = true
		edtTags.WordWrap = true
		
		edtToken.readOnly = true
		edtToken.backColor = edtToken.backColor.gray
		edtToken.foreColor = edtToken.foreColor.white
			
		warnLabels = #(lblWarn1, lblWarn2, lblWarn3, lblWarn4, lblWarn5)
		bg = getBgColor()
		txt = getTextColor()
				
		for i in warnLabels do 
		(
			i.backColor = bg
			i.foreColor = i.foreColor.red
		)				
	)
	
	fn setTagText t = 
	(
		c = for i in _RelTagsList.controls where isProperty i "name" and matchPattern (i.name as string) pattern: "btnTag*" collect i
		for i in c do i.visible = false
		
		for i in 1 to t.count where i <= 7 do
		(
			c[i].caption = t[i] 
			c[i].visible = true
		)
	)
		
	fn getRelTagsList id =
	(
		--catID = -1
		url = _URL + "vault/mxs/mxsGetRelTags.php?id=" + id as string + "&time=" + timeStamp() as string
		r = httpGet url		
		t = for i in filterString r ";" collect trim i
		return t
	)
		
	
	fn relTagsList =
	(
		if(catID == -1) do return showTooltip lblWarn5 "Please select the category!"
		
		global relTags = #()
		try(destroyDialog _RelTagsList)catch()
		
		
		rollout _RelTagsList "Tags Editor"
		(		
			multilistbox lbxTags "Tags:" height: 23 across: 2 width: 180 align: #left
			multilistbox lbxTagsActual "Recommended Tags:" height: 23 width: 180 align: #right						
			dotNetControl edtAddTag"System.Windows.Forms.TextBox" height: 25 tabstop: true width: 180 across: e
			label lblTagStatus "Start typing to view tips..." align: #left offset: [-25, 4]
			
			button btnTag1 "" visible: false width: 60 border: true align: #right across: 7
			button btnTag2 "" visible: false width: 60 border: false align: #right
			button btnTag3 "" visible: false width: 60 border: false align: #right
			button btnTag4 "" visible: false width: 60 border: false align: #right
			button btnTag5 "" visible: false width: 60 border: false align: #right
			button btnTag6 "" visible: false width: 60 border: false align: #right
			button btnTag7 "" visible: false width: 60 border: false align: #right
			label lblEmpty ""			
			button btnAddNewTag "Add" align: #left height: 28 width: 80 across: 3
			button btnDeleteTag "Delete" align: #left height: 28 width: 80 offset: [-44, 0]
			button btnCloseWind "Close" align: #right height: 28 width: 80 
			button btnAddTag "<<" width: 60 height: 28 pos: [200, 145]			
			timer tmrSetPos "" active: false interval: 300			
						
			fn strToTags t =
			(
				o = filterString t ","
				o = for i in o collect trim i
				o = for i in o where i.count > 0 and i != " " collect i
					
				return o
			)
			
			fn findRelTag =
			(
				t = edtAddTag.text
				lbxTagsActual.items = relTags				
				
				if(t.count == 0) do return false
				t2 = for i in relTags where matchPattern i pattern: (t + "*") collect i
				lbxTagsActual.items = t2
			)
			
			fn addRelTags t showError: false =
			(
				o = strToTags edtTags.text
				c1 = o.count
				
				join o t
				o = makeUniqueArray o
				
				c2 = o.count
				
				lbxTags.items = o
				
				s = (implode o tag: ", ")
				
				edtTags.text = s	
				setTagText	#()
				
				lblTagStatus.caption = "Tag added successfully!"
				if(showError and c1 == c2) do lblTagStatus.caption = "Duplicated tag was found and removed!"
			)
			
			fn addTag a b =
			(		
				t = for i in filterString b.text "," collect toLower (trim i)
				t = for i in 1 to t.count - 1 where t[i].count > 0 and t[i] != " " collect t[i]
				o1 = t.count 
				append t a.caption
				t = makeUniqueArray t
				o2 = t.count 
				b.text = (implode t tag: ", ")
				
				if(o1 == o2) do
				(
					lblTagStatus.caption = "Duplicated tag was found and removed!"
					setTagText #()	
				)

				setFocus b	
				b.SelectionStart = (b.text.count)			
			)
			
			fn addDefaultTagsFromCatName = 
			(
				t = #()
				t[1] = ddlLib.selected
				t[2] = ddlCat.selected
				t[3] = ddlSubCat.selected
				
				badWords = #(" And ", "and",  "AND", "ANd"," ")
				
				tmp = ""
				for i in t do 
				(
					i = trim i		
					i = toLower i
					for ii in badWords do  i = substituteString i ii ","					
					tmp += i + ","
				)
				
				tmp2 = ""
				for i in filterString tmp "," do tmp2 += i + ","
							
				
				if(lbxTags.items.count == 0) do
				(
					lbxTags.items = strToTags tmp2 
					edtTags.text = tmp2 
				)
			)
			
			on btnCloseWind pressed do try(destroyDialog _RelTagsList)catch()
			on _RelTagsList open do 
			(	
				setPosRelTagsList()		
				relTags = getRelTagsList catID
								
				if(relTags.count == 0) do 
				(				
					lbxTagsActual.enabled = false
					
					relTags = #("No tags yet!")
				)
				
				lbxTagsActual.items = relTags
				lbxTags.items = strToTags edtTags.text 
				
				addDefaultTagsFromCatName()
			)
			
			on btnDeleteTag pressed do
			(
				s = lbxTags.items
				items = lbxTags.selection as array
				for i in items.count to 1 by -1 do try(deleteItem s items[i]) catch()
				lbxTags.items = s
				lbxTags.selection = #{}
				
				t = (implode s tag: ", ")
				edtTags.text = t				
			)
			
			on _RelTagsList close do 
			(
			)
			on _RelTagsList moved p do
			(
				tmrSetPos.active = false
				tmrSetPos.active = true
				
				setFocus edtAddTag
			)
			on tmrSetPos tick do
			(
				setPosRelTagsList()	
				tmrSetPos.active = false
			)
			on btnAddTag pressed do
			(
				t = for i in lbxTagsActual.selection where  lbxTagsActual.items[i].count > 0 collect lbxTagsActual.items[i]
				addRelTags t showError: true
			)
			
			fn addNewTag = 
			(
				t = toLower (edtAddTag.text)
				t = replaceBadChars t
				
				if(t.count == 0) do return false	
				
				addRelTags #(t) showError: true
				edtAddTag.text = ""	

				lbxTagsActual.items = relTags				
			)
			
			on edtAddTag KeyUp s e do
			(							
				if(e.KeyCode == Keys.Enter) do (			
					if(btnTag1.visible == true and btnTag1.caption.count > 0) do addTag btnTag1 edtAddTag	
					addNewTag()
				)
				
				findRelTag()
			)
			
			on btnAddNewTag pressed do addNewTag()	
			
			on btnTag1 pressed do addTag btnTag1 edtAddTag
			on btnTag2 pressed do addTag btnTag2 edtAddTag
			on btnTag3 pressed do addTag btnTag3 edtAddTag
			on btnTag4 pressed do addTag btnTag4 edtAddTag
			on btnTag5 pressed do addTag btnTag5 edtAddTag
			on btnTag6 pressed do addTag btnTag6 edtAddTag
			on btnTag7 pressed do addTag btnTag7 edtAddTag
		
			on edtAddTag TextChanged s a do
			(		
				t = findTag edtAddTag.text		
				if(t.count == 0) do return setTagText #()
				setTagText t									
			)
			
		)
		createDialog _RelTagsList 460 445 escapeEnable: true style: #(#style_titlebar, #style_border, #style_sysmenu) parent: _STEP2.hwnd
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
	
	fn findIndex c id =
	(
		for i in 1 to c.count where c[i][1] == id as string do return i
		return -1
	)
	
	fn ddlLib_selected v =
	(		
		catID = -1
		ddlCat.items = #()
		ddlSubCat.items = #()
		category2 = getCat (category1[v][1])	
				
		useSettings "CAT1"	category1[v][1] "w"	
		
		ddlCat.items = for i in category2 collect i[2]
			
		try(destroyDialog _RelTagsList)catch()
	)
	
	fn ddlCat_selected v =
	(
		catID = -1		
		ddlSubCat.items = #()
		category3 = getCat (category2[v][1])	
		
		useSettings "CAT2"	category2[v][1] "w"	
				
		ddlSubCat.items = for i in category3 collect i[2]
			
		try(destroyDialog _RelTagsList)catch()
	)
	
	fn ddlSubCat_selected v = 
	(
		useSettings "CAT3"	category3[v][1] "w"	
		catID = category3[v][1] as integer
		
		try(destroyDialog _RelTagsList)catch()
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
	
	fn detectLights =
	(
		_selection = getSelectionByPackType()
		
		for i in _selection where superClassOf i == light do 
		(
			cbxLights.checked = true
			bLights = true
		)
	)
	
	fn detectAnimation =
	(
		a = false
		
		_selection = getSelectionByPackType()
		
		for i in _selection do 
		(		
			try(if(numKeys i.pos.controller > 0) do a = true)catch()
			try(if(numKeys i.rotation.controller > 0) do a = true)catch()
			try(if(numKeys i.scale.controller > 0) do a = true)catch()
		)
		
		cbxAnimated.checked = a
		bAnimated = a
	)
	
	on btnNext pressed do 
	( 						
		sModelName = trim (edtModelName.text)
		sManufacturer = trim (edtManufacturer.text)
		sModeller = trim (edtModeller.text)
		sProject = trim (edtProject.text)
		sToken = edtToken.text
		sTags = edtTags.text

		bRigged = cbxRigged.checked
		bAnimated = cbxAnimated.checked
		bBakedTextures = cbxBakedTextures.checked
		bGameEngineReady = cbxGameEngineReady.checked
		bLods = cbxLods.checked
		bUnwrap = cbxUnwrap.checked
		bLights = cbxLights.checked
			
		iCat1 = ddlLib.selection
		iCat2 = ddlCat.selection
		iCat3 = ddlSubCat.selection
		
		if(iCat1 > 0) do catId1 = category1[iCat1][1]
		if(iCat2 > 0) do catId2 = category2[iCat2][1]
		if(iCat3 > 0) do catId3 = category3[iCat3][1]

		if(catID == -1) do return showTooltip lblWarn5 "Please select the category!"
		if(sModelName.count == 0) do return showTooltip edtModelName "Please enter correct model name!"
		if(sTags.count == 0) do 	return showTooltip edtTags "Please enter few tags!" 
				
		if(not isValidText sModelName edtModelName) do return false 
		if(not isValidText sManufacturer edtManufacturer) do return false 
		if(not isSelected() and _ASSETS_PACK_TYPE == 1) do return showTooltip nextWarn "Please select objects!"
			
		saveAllProp()
			
		try(destroyDialog _RelTagsList)catch()
		
		goStep 3	
	)
	
	on btnPrev pressed do
	(
		try(destroyDialog _RelTagsList)catch()
		
		goStep 1				
	)

	on edtTags KeyUp s e do
	(		
		if(e.KeyCode == Keys.Enter) do (			
			if(btnTag1.visible == true and btnTag1.caption.count > 0) do addTag btnTag1 edtTags				
		)
	)
	
	on edtModelName LostFocus s a do isValidText (edtModelName.text) edtModelName
	on edtManufacturer LostFocus s a do isValidText (edtManufacturer.text) edtManufacturer
		
	on _STEP2 open do
	(	
		
		_STEP2.title = getPackTypeText pack: "" + " Info"
		
		initDotNet()
		
		edtToken.text = getProp "edtToken"
		if(edtToken.text.count == 0) then
		(
			edtToken.text =generateUniqueID()
		)
				
		edtModelName.text = getProp "edtModelName"
		edtManufacturer.text = getProp "edtManufacturer" 
		edtModeller.text =  getProp "edtModeller"
		edtProject.text =  getProp "edtProject"
		edtTags.text = getProp "edtTags"
		
		cbxRigged.checked = getProp "cbxRigged" type: #bool
		cbxAnimated.checked = getProp "cbxAnimated" type: #bool
		cbxBakedTextures.checked = getProp "cbxBakedTextures" type: #bool
		cbxGameEngineReady.checked = getProp "cbxGameEngineReady" type: #bool
		cbxLods.checked = getProp "cbxLods" type: #bool
		cbxUnwrap.checked	= getProp "cbxUnwrap" type: #bool
		cbxLights.checked	= getProp "cbxLights" type: #bool
				
		catId1 = getProp "catId1" type: #int
		catId2 = getProp "catId2" type: #int
		catId3 = getProp "catId3" type: #int
		
		if(catId1 == 0 and catId2 == 0 and catId3 == 0) do 
		(
			catId1 = useSettings "CAT1" "int" "r"
			catId2 = useSettings "CAT2" "int" "r"
			catId3 = useSettings "CAT3" "int" "r"
		)
		
		catID = (getProp "catID" type: #negative) as integer
		
		tmr1.active = true	
	
		detectLights()
		detectAnimation()						
	)
		
	on _STEP2 close do
	(
	
	)
	
	on btnShowRelTags pressed do relTagsList()
		
	on tmr1 tick do
	(		
		loadCategories()
		
		tags = getTagsList()			
		modellers = getModellerList()
				
		edtModeller.AutoCompleteCustomSource.AddRange modellers
		
		for i in getModels() do 
		(	
			t = filterString i "|"
			if(t.count > 1) do
			(
				if(t[1] != undefined) do appendIfUnique models t[1]
				if(t[2] != undefined) do appendIfUnique manufacturers t[2]
				if(t[3] != undefined) do appendIfUnique projects t[3]
				if(t[4] != undefined) do appendIfUnique clients t[4]
			)
		)
			
		edtProject.AutoCompleteCustomSource.AddRange projects
		edtModelName.AutoCompleteCustomSource.AddRange models
		edtManufacturer.AutoCompleteCustomSource.AddRange manufacturers
		
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

rollout _STEP3 "STEP 3: Model Review" 
(
	group "Description"
	(
		dotNetControl edtOverview "System.Windows.Forms.TextBox"  height: 250
	)
	group "Additional Info"
	(
		label lbl1 "Client:" align: #left		
		dotNetControl edtClient "System.Windows.Forms.TextBox" height: 25 tabstop: true
	)
	
	group "Custom Attributes"
	(
		label lbl2 "Custom 1:" align: #left				
		dotNetControl edtCustom1 "System.Windows.Forms.TextBox" height: 25 tabstop: true
		label lbl3 "Ex.: Collection, Unique ID,  Alternative Name..." align: #left
	)
	timer tmr1 "Tmr1" active: false interval: 300
	
	fn fixLine s = 
	(
		s = substituteString s "\\n" "\r\n"
		return s
	)
	
	on _STEP3 open do
	(
		_STEP3.title = getPackTypeText pack: "" + " Review"
		
		edtOverview.MultiLine = true
		edtOverview.scrollbars = (dotnetclass "System.Windows.Forms.ScrollBars").vertical		
		edtOverview.acceptsReturn = true
		edtOverview.acceptsTab = true
		edtOverview.wordWrap = true
		
		edtOverview.text = fixLine (getProp "edtOverview")
		edtCustom1.text = getProp "edtCustom1"
		edtClient.text = getProp "edtClient"
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
	button btnPrev "<< Back" align: #left  across: 2 width: 75 height: 28
	button btnNext "Next >>" align: #right  enabled: true width: 75 height: 28	
	dotnetcontrol nextWarn "System.Windows.Forms.Button" height:0 width:0 align: #right
	
	on btnPrev pressed do 
	(	
		sOverview = edtOverview.text			
		sCustom1 = edtCustom1.text			
		sClient = edtClient.text	
		
		saveProp "edtOverview" sOverview
		saveProp "edtCustom1" sCustom1
		saveProp "edtClient" sClient
		
		goStep 2		
	)
	on btnNext pressed do 
	(
		if(not isSelected() and _ASSETS_PACK_TYPE == 1) do return showTooltip nextWarn "Please select objects!"
					
		sOverview = edtOverview.text			
		sCustom1 = edtCustom1.text			
		sClient = edtClient.text			
		
		saveProp "edtOverview" sOverview
		saveProp "edtCustom1" sCustom1
		saveProp "edtClient" sClient
			
		goStep 4	
	)
	
	on tmr1 tick do
	(						
		try(edtClient.AutoCompleteCustomSource.AddRange clients)catch()
		
		tmr1.active = false
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

	button btnPreviewAdd "Add New Picture" offset: [0, 12] height: 28
		
	progressBar hr "" height: 3 offset:[0,15]
	button btnPrev "<< Back" align: #left  across: 2 width: 75 height: 28
	button btnNext "Next >>" align: #right  enabled: true	width: 75 height: 28
	dotnetcontrol nextWarn "System.Windows.Forms.Button" height:0 width:0 align: #right
	
	on btnPrev pressed do goStep 3		
	on btnNext pressed do 
	(	
		if(not isSelected() and _ASSETS_PACK_TYPE == 1) do return showTooltip nextWarn "Please select objects!"
		if(sPreviewPath.count == 0) do return showTooltip btmPreview "Please add at least one image!\nPress the \"Add New Picture\" button!"
					
		saveProp "sPreviewPath" (implode sPreviewPath)
		saveProp "bMainPreview" (implode bMainPreview)
			
		goStep 5
	)
	
	fn setBitmap b gamma: 0.45 =
	(
		if(b == undefined) do return false
		
		bs = dotNetclass "System.Windows.Forms.PictureBoxSizeMode"
		btmPreview.SizeMode = bs.Zoom 
		
		if(btmPreview.width > b.width and btmPreview.height > b.height) do btmPreview.SizeMode = bs.CenterImage
			
		setClipboardBitmap  b	
		c = dotNetClass "System.Windows.Forms.Clipboard"
		img = c.GetImage()	
		r = dotNetObject "System.Drawing.Rectangle" 0 0 img.width img.height			
		gu = (dotNetClass "System.Drawing.GraphicsUnit").Pixel			
		a = dotNetObject "System.Drawing.Imaging.ImageAttributes"
		a.SetGamma gamma			
		g = (dotNetClass "System.Drawing.Graphics").FromImage(img)	
		g.DrawImage img r 0 0 img.width img.height gu a			
		c.setImage(img)			
		btmPreview.image = c.GetImage()
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
		
		b = openBitMap f	gamma: 2.2	
		
		setBitmap b
		
		w = b.width as string
		h = b.height as string
		
		lblDimensions.caption = w + " x " + h	
		
		checkMainPreview()
	)
	
	on ddlPreview selected x do
	(
		showPreview sPreviewPath[x]
	)
	
	fn addPreview =
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
	
	on btnPreviewAdd pressed do addPreview()
	
	on btnDelPreview pressed do
	(
		s = ddlPreview.selection
				
		if(s != 0) do 
		(
			lblDimensions.caption = ""
			cbxMainPreview.checked = false
			deleteItem sPreviewPath s
			try(deleteItem bMainPreview s)catch()
			ddlPreview.items = for i in sPreviewPath collect filenameFromPath i
			ddlPreview.selection = 1
			if(sPreviewPath.count != 0) then showPreview sPreviewPath[1] else btmPreview.image = undefined			
			checkMainPreview()
		)
		
	)
	
	on btmPreview Click s a do addPreview()

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
		
		dotNetControl lbl15 "System.Windows.Forms.Label" text: "Game Engine:" across: 2 
		dotNetControl _lblGameEngineReady "System.Windows.Forms.Label" text: "" 
		
		dotNetControl lbl16 "System.Windows.Forms.Label" text: "Unwrap:" across: 2 
		dotNetControl _lblUnwrap "System.Windows.Forms.Label" text: "" 	
		
		dotNetControl lbl17 "System.Windows.Forms.Label" text: "Lights:" across: 2 
		dotNetControl _lblLights "System.Windows.Forms.Label" text: "" 
		
		dotNetControl lbl18 "System.Windows.Forms.Label" text: "Lods:" across: 2 
		dotNetControl _lblLods "System.Windows.Forms.Label" text: "" 
		

		dotNetControl lbl19 "System.Windows.Forms.Label" text: "Client:" across: 2 offset: [0, 20]
		dotNetControl _lblClient "System.Windows.Forms.Label" text: "" offset: [0, 20]	

		dotNetControl lbl20 "System.Windows.Forms.Label" text: "Custom 1:" across: 2 
		dotNetControl _lblCustom1 "System.Windows.Forms.Label" text: "" 
	)
	group "Settings" 
	(
		label lblMaxVer "3Ds Max Version: " across: 2 align: #left 
		dropdownlist ddlMaxVer "" width: 70 offset: [-30, -3]
		checkbox cbxRenameTextures "Rename Textures" checked: true across: 2		
		checkbox cbxRenameObjects "Rename Objects" checked: true 
		checkbox cbxRenameMaterials "Rename Materials" checked: true across: 2
		checkbox cbxRenameGroups "Rename Groups" checked: true
		checkbox cbxRemoveLayers "Remove Layers" checked: true 
	)
		
	button btnPackModel "Pack Model" width: 244 height: 45 align: #left across: 2
	dotnetcontrol warnMsg "System.Windows.Forms.Button" height:0 width:0 offset: [0, 25]
	
		
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
		
		_selection = getSelectionByPackType type: #geometry
			
		Category.text = "No category"
		if(category3[iCat3][2] != "") do	Category.text = category1[iCat1][2] + " / " + category2[iCat2][2] + " / " + category3[iCat3][2]
		_lblModelName.text = getNA sModelName
		_lblManufacturer.text = getNA sManufacturer
		_lblModeller.text = getNA sModeller
		_lblProject.text = getNA sProject
		_lblTags.text = (explode sTags tag: ",").count as string
		_lblPreview.text = sPreviewPath.count as string
		_lblUnits.text = getUnits()
		_lblDim.text = getDim _selection
		_lblPolys.text = polyCount _selection
		_lblRender.text = (getCurrRender())[2]
		_lblOverview.text = getYesNo (sOverview.count > 0)
		_lblRigged.text = getYesNo bRigged
		_lblAnimated.text = getYesNo bAnimated
		_lblBaked.text = getYesNo bBakedTextures
		_lblGameEngineReady.text = getYesNo bGameEngineReady
		_lblUnwrap.text = getYesNo bUnwrap
		_lblLights.text = getYesNo bLights
		_lblLods.text = getYesNo bLods		
		_lblCustom1.text = getNA sCustom1
		_lblClient.text = getNA sClient
			
			
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
		
		_lblFormat.text = ddlMaxVer.selected
		
		
		-- 
		if(_ASSETS_PACK_TYPE == 2) do (
			cbxRenameTextures.enabled = false
			cbxRenameObjects.enabled = false
			cbxRenameMaterials.enabled = false
			cbxRenameGroups.enabled = false
			cbxRemoveLayers.enabled = false
			
			cbxRenameTextures.checked = false
			cbxRenameObjects.checked = false
			cbxRenameMaterials.checked = false
			cbxRenameGroups.checked = false
			cbxRemoveLayers.checked = false						
		)		
		
		_STEP5.title = getPackTypeText()
		btnPackModel.caption = getPackTypeText()
	)
		
	fn checkTexturesError =
	(
		errorTex = #()
		oldTex = #()
		
		_selection = getSelectionByPackType()
				
		for i in _selection where i.material != undefined do
		(
			for type in BitmapTypes where classOf type == textureMap do
			(
				for b in getClassInstances type target:i where b != undefined do
				(
					try(b.coords.coords.realWorldScale = false)catch()
					try(b.coords.realWorldScale = false)catch()
					
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
			p = toLower p
			if(doesFileExist sPreviewPath[i]) do 
			(								
				if(bMainPreview[i] != undefined and bMainPreview[i]) then 
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
		
	fn copyMaps _selection szMaps szName = 
	(
		oldTex = #()
		oldTexPaths = #()
		toRename = #()
		iCnt = 1
				
		for i in _selection where i.material != undefined do
		(
			for type in BitmapTypes where classOf type == textureMap do 
			(	
				for b in getClassInstances type target:i where (validBitmap b) and findItem oldTex b == 0 do
				(																	
					f = getFullPath (b.filename)
				
					--n = filenameFromPath f
					n = getFilenameFile f
					p = getFilenamePath f
					ext = getFilenameType f
										
					if(cbxRenameTextures.checked) do n = szName + "-" + (getNum iCnt)
					n = n + (toLower ext)
					
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
		r = cbxRenameObjects.checked
		
		iObjCnt = 1
		iGrpCnt = 1
				
		for g in objects where isValidNode g do
		(								
			n = g.name
			if(isGroupHead g and not (isGroupMember g)) then 
			(					
				if(cbxRenameGroups.checked) do n = szName + "-Group-"  + (getNum iGrpCnt) 
				iGrpCnt += 1
			)
			else 
			(
				if(r) do n = szName + "-" + (getNum iObjCnt) 
				iObjCnt += 1
			)
			
			g.name = n			
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
		if(not cbxRenameMaterials.checked) do return false
		
		oldMtl = #()
		iCnt = 1
		
		_selection = getSelectionByPackType()	
		
		toRename = #()
		mat = getMaterialDependence _selection material
		tex = getMaterialDependence _selection textureMap	
		
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
		layerManager.closeDialog()
		defLayer = LayerManager.getLayerFromName "0"
		defLayer.current = true
		
		for i in objects do defLayer.addNode i
			
		for i in (layerManager.count - 1) to 1 by -1 do
		(
			l = layerManager.getLayer i
			n = l.name
					
			layerManager.deleteLayerByName n
		)
	)
	
	fn copyIESfiles _selection szAssets szName = 
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
			
			return @"assets\" + filenameFromPath f
		)
				
		for l in _selection where superClassOf l == light do
		(
			if(isProperty l #ies_file and l.ies_file != undefined) do l.ies_file = copyIes (l.ies_file) szAssets szName
			if(isProperty l #webFile and l.webFile != undefined) do l.webFile = copyIes (l.webFile) szAssets szName
			if(isProperty l #iesFile and l.iesFile != undefined) do l.iesFile = copyIes (l.iesFile) szAssets szName
		)		
	)
	
	fn removeDefaultLights = (
		l = for i in lights where matchPattern i.name pattern: "HighLight_*" collect
		try(delete l)catch()
		
		l = for i in lights where classOf i == VrayLight and i.type == 1 collect i
		try(delete l)catch()
	)
	
	fn setIniInfo f s k v = 
	(	
		-- Set correct ini setting for PHP
		v = substituteString v "\"" "'"
		setIniSetting f s k ("\"" + v + "\"") 
	)
	
	fn createInfo szDir previewList ver =
	(
		f = szDir + "info.tmp"
		n = szDir + "info.ini"		
		r = getCurrRender()
		p = polyCount geometry
		d = getDim geometry
		u = getUnits()
		
		o = substituteString sOverview "\r\n" "|"
						
		setIniInfo f "INFO" "TYPE" "model" 
		setIniInfo f "INFO" "CATID" (catID as string) 
		setIniInfo f "INFO" "NAME" sModelName 
		setIniInfo f "INFO" "TOKEN" sToken 
		setIniInfo f "INFO" "FORMAT" ver 
		setIniInfo f "INFO" "RENDER" r[2] 
		setIniInfo f "INFO" "POLYS" p 
		setIniInfo f "INFO" "UNITS" u 
		setIniInfo f "INFO" "DIMENSION" d 
		setIniInfo f "INFO" "PROJECT" (getNA sProject) 
		setIniInfo f "INFO" "CUSTOM1" (getNA sCustom1)
		setIniInfo f "INFO" "CLIENT" (getNA sClient)
		setIniInfo f "INFO" "RIGGED" (getYesNo bRigged) 
		setIniInfo f "INFO" "LODS" (getYesNo bLods) 
		setIniInfo f "INFO" "ANIMATED" (getYesNo bAnimated) 
		setIniInfo f "INFO" "MODELLER" (getNA sModeller) 
		setIniInfo f "INFO" "TAGS" sTags 
		setIniInfo f "INFO" "MANUFACTURER" (getNA sManufacturer) 
		setIniInfo f "INFO" "OVERVIEW" o 
		setIniInfo f "INFO" "UNWRAP" (getYesNo bUnwrap) 
		setIniInfo f "INFO" "LIGHTS" (getYesNo bLights) 
		setIniInfo f "INFO" "BAKED" (getYesNo bBakedTextures) 
		setIniInfo f "INFO" "GAMEENGINE" (getYesNo bGameEngineReady) 
		setIniInfo f "INFO" "PREVIEW" (getYesNo (sPreviewPath.count > 0)) 
				
		iCnt = 0	
		for i in previewList do 
		(
			j = substituteString i szDir ""
			setIniInfo f "PREVIEW" (iCnt as string) j 
			iCnt += 1
		)
		

		convertFileToUTF8 f	n
	)
	
	on cbxRenameTextures changed v do useSettings "RENTEX"	v "w"	
	on cbxRenameObjects changed v do useSettings "RENOBJ"	v "w"	
	on cbxRenameMaterials changed v do useSettings "RENMAT"	v "w"	
	on cbxRenameGroups changed v do useSettings "RENGRP"	v "w"
	on cbxRemoveLayers changed v do useSettings "REMLAYERS"	v "w"
	on ddlMaxVer selected v do _lblFormat.text = ddlMaxVer.selected

	
	
	on btnPackModel pressed do
	(
		if(not isSelected() and _ASSETS_PACK_TYPE == 1) do return showTooltip warnMsg "Please select objects!"
		fileToLoad = maxFilePath + maxFileName
		if(fileToLoad.count < 1) do  return showTooltip warnMsg "Please save scene!"
		if(sModelName.count < 1) do return showTooltip warnMsg "Please specify Model Name in STEP 1!" 
		if(sTags.count < 1 and _SILENT_MODE == false) do return showTooltip warnMsg "Please specify Tags in STEP 2!" 
		if(catID == -1) do return showTooltip warnMsg "Please select the category in STEP 2!"
		max file save
		
			
		removeDefaultLights()	
			
		_selection = getSelectionByPackType()
			
		v = ddlMaxVer.selected 
		r = getCurrRender()	
			
		-- Set paths
		
		szName = stripName sModelName
		
		szDir = _SAVEPATH + szName + @"\"		
		szMaps = szDir + @"maps\"
		szAssets = szDir + @"assets\"
		szPreview = szDir + @"preview\"
		szExport = szDir + @"export\"
		szMtl = szDir + @"matlib\"	
		szInfo = szDir  + "info.ini"
		
		-- Set out zip
		szZip = _SAVEPATH + szName + ".zip"
		szMatLib = (szMtl + szName + " (" +  r[2] + ").mat")		
				
		-- Make dirs
		
		makeDir szDir	
		makeDir szMaps	
		makeDir szAssets
		makeDir szPreview	
		makeDir szExport	
		makeDir szMtl	
		
		-- Check tex errors	
		if(checkTexturesError()) do return false
		
		-- Copy previews
		previewList = copyPreview szPreview szDir
					
		-- Copy maps		
		-- Some maps not copied in row (maxscript bug)	
		for i in 1 to 3 do copyMaps _selection szMaps szName
				
		-- Copy IES
		copyIESfiles _selection szAssets szName	
			
		-- Save render
		currentRender = renderers.current

		-- Save units
		SystemType = units.SystemType
		SystemScale = units.SystemScale 
		DisplayType = units.DisplayType	
		MetricType = units.MetricType
			
		-- Add Versions
		if(_ASSETS_PACK_TYPE == 1) do (
			saveNodes _selection (szDir + "_tmp.max") quiet:true
			resetMaxFile #noPrompt
			try(
				units.SystemType = SystemType
				units.SystemScale = SystemScale
				units.DisplayType	= DisplayType
				units.MetricType = MetricType
			)catch()
				
			-- Delete objects from default scene
			try(for o in objects where isValidNode o do delete o)catch()
			
			mergeMAXFile (szDir + "_tmp.max") #select #mergedups #renameMtlDups #noRedraw quiet:true			
			deleteFile (szDir + "_tmp.max")
				
			try(renderers.current = currentRender)catch()
		)
			
		select objects						
		max tool zoomextents all
		
		if(_ASSETS_PACK_TYPE == 1) do (	
			renameObjects szName
			renameMaterials szName 
			if(cbxRemoveLayers.checked) do removeLayers()
		)
			
		-- Save file properties
		saveAllProp()
		
		f = szDir + szName + r[1] + ".max"
		saveMaxFile f saveAsVersion:(v as integer) quiet:true
		
		-- Save material library
		saveMtlLib szMatLib	
		
		f = szExport + szName + r[1] + ".fbx"
		FBXExporterSetParam "FileVersion" "FBX201200"	-- Warning!! This option important!!
		ungroupRecursive objects
		exportFile f #noPrompt  --selectedOnly:true
		
		szOutName = szName + " (" + r[2] + ")"
		deleteFile (_SAVEPATH + szOutName + ".zip")
		
		zipper = zip()
		zipper.packFile source: f target: szExport name: (szOutName + ".fbx") del: true
		
		createInfo szDir previewList v
				
		zipper = zip()
		zipper.packFiles source: szDir target: _SAVEPATH name: szOutName del: true
		
		resetMaxFile #noPrompt
		loadMaxFile fileToLoad useFileUnits: true quiet:true		
		
		if(_SILENT_MODE == false) do (
			q = queryBox "Model packed success!\nDo you want to open containing folder?" title: "Success!"
			if(q) do shellLaunch _SAVEPATH ""		
		)
	)
	
	progressBar hr "" height: 3 offset:[0,15]
	button btnPrev "<< Back" align: #left  width: 75 height: 28
	on btnPrev pressed do goStep 4	
)


rollout _rAbout "About" 
(
	label lblName "" 
	label lblVer "" 
	
	label lblAuthor "" height: 30
	label lblCopy ""  height: 30
	
	on _rAbout open do
	(		
		i = getScriptInfo (getThisScriptFilename())
			
		lblName.caption = i[1]
		lblAuthor.caption = i[2]
		lblVer.caption = i[3]
		lblCopy.caption = i[6]
	)
)

goStep 1

