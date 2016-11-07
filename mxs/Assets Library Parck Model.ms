try(closeRolloutFloater rPrepareModel)catch()
global rPrepareModel = newRolloutFloater "Prepare Model 3DDD" 260 600
global szVer = "1.4.1"

global _STEP1
global _STEP2
global _STEP3
global _STEP4
global _STEP5
global rAbout


global szModelName = ""
global szPreviewPath = #()
global bModelPostfix = true
global bSetMaterialNames = true
global bTexturesStrip = true
global bTexturesRename = true
global bObjectsRename = true
global bAddMatLib = true
global bLayer0 = true
global currentRender = undefined

global szExport = #()


global szSaveToPath = @"C:\temp\3ddd\"
global szWinRarPath = @"C:\Program Files\WinRAR\WinRAR.exe"
global szWinRarPath2 = @"C:\Program Files (x86)\WinRAR\WinRAR.exe"
global szGoodChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz \\;:1234567890!@#$%^&*()_+~'.,[]{}" 

global iWindowPos = [0.0, 0.0]


fn getMaxVersion =
(
	v = maxVersion()
	v = (v[1] / 1000) - 2
	
	a = 2000 + v
	
	o = case v of 
	(
		11: #(2010,2011)
		12: #(2010,2011,2012)
		default: #(a - 3, a - 2, a - 1, a)		
	)
	
	return (for i in o collect i as string)
)

fn getRender =
(
	r = renderers.current as string

	if matchpattern r pattern:"*Corona*" do return #("_corona", "Corona")
	if matchpattern r pattern:"*V_Ray_Adv*" do return #("_vray", "V-Ray")
	if matchpattern r pattern:"*Default_Scanline*" do return #("_scanline", "Default Scanline")
	if matchpattern r pattern:"*mental_ray*" do return #("_mentalray", "Mental Ray")
	if matchpattern r pattern:"*iray_Renderer*" do return #("_iray", "IRay")
		
	return #("", "")
)

fn stripTab m = 
(
	q = filterString m "\t"	
	l = ""
	for i in q do l += i
	
	return l
)

fn writeFile f s =
(
	f = openFile f mode: "wt"

	format(s as string) to:f

	flush f
	close f
)

fn sliderStep n x =
(
	k = #(_STEP1, _STEP2, _STEP3, _STEP4, _STEP5)
	
	for i in k where i.open == true and i != k[n] do i.open = false
	
	k[n].open = x
	
	rAbout.open = true
	rAbout.open = false
)

fn getWinRar = 
(
	if(doesFileExist szWinRarPath) do return szWinRarPath
	return undefined
)

fn checkWinrar =
(
	if(doesFileExist szWinRarPath) do return true
	
	if(doesFileExist szWinRarPath2) do
	(
		szWinRarPath = szWinRarPath2
		return true
	)
	
	messageBox "WinRar not found.\nPlease specify WinRar path in STEP3: Basic Setup!" title: "Warning!"
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




fn getDim s =
(
	d = s.max - s.min
	
	d = for i in 1 to 3 collect ((d[i] * 100) as integer / 100.0 ) as string
	
	return d[1] + " x " + d[2] + " x " + d[3]
)

fn showStatistic m title:"Statistic" width: 260 =
(
	global rStatistic
	try(DestroyDialog rStatistic)catch()	
	global szStat = m
	global iWidth = width
	
	rollout rStatistic title
	(
		edittext edtStat "" height: 260 width: iWidth offset: [-15, -2] readOnly: true
		button btnCopy "Copy" align: #left width: 50 across: 2
		button btnOK "Ok" align: #right  width: 35
		
		on btnOK pressed do try(DestroyDialog rStatistic)catch()
		on rStatistic open do edtStat.text = szStat	
		on btnCopy pressed do setClipBoardText (stripTab edtStat.text)
		
	)

	createDialog rStatistic width 295
)

fn polyCount s =
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

fn useSettings k p w =
(	
	settingsFile = getThisScriptFilename() + ".ini"
	
	v = maxVersion()
	v = 2000 + ((v[1] / 1000) - 2)
	
	case w of
	(
		"r":
		(
			s = getINISetting settingsFile (v as string) k
			case p of
			(
				"int": return s as integer
				"float": return s as float
				"bool": if s == "true" or s.count < 1 then true else false
				"bool_not": if s == "true" then true else false
				
				default: return s
			)
		)
		default: return setINISetting settingsFile (v as string) k (p as string)
	)
)

fn saveWindowPos =
(
	useSettings "X"  iWindowPos[1] "w"
	useSettings "Y"  iWindowPos[2] "w"		
)

fn saveMtlLib p =
(
	for i in 1 to currentMaterialLibrary.count do deleteItem currentMaterialLibrary 1
	for i in selection where i.material != undefined do append currentMaterialLibrary i.material
	saveMaterialLibrary p
)

fn ungroupRecursive s = for i in s where isGroupHead i and isValidNode i do explodeGroup i

rollout _STEP1 "STEP 1: Model Name" 
(	
	group "Model Name"
	(
		edittext edtModelName "" height: 25 
	)
	
	progressBar hr "" height: 3 offset:[0,15]
	button btnNext "Next >>" align: #right  enabled: false	

	on _STEP1 rolledUp x do sliderStep 1 x
	on btnNext pressed do sliderStep 2 true		

	on edtModelName changed x do
	(
		btnNext.enabled = false
		t = edtModelName.text
		
		p = FilterString t szGoodChars
		if(p.count > 0) do 
		(
			edtModelName.text = ""
			return messageBox "The names are not supported by cyrillic or special characters!\nPlease choose another name!" title: "Warning!"
		)
		
		if(t.count > 1) do 
		(
			btnNext.enabled = true
					
			if(t[t.count] == "\n") do 
			(
				edtModelName.text = trimRight t "\n"
				
				sliderStep 2 true	
			)
			
		)
	
		a = filterString t " "
		
		t2 = ""
		for i in 1 to a.count do
		(
			t2 += a[i] + (if(i != a.count) then "_" else "")
		)
		
		szModelName = t2				
	)
	
	on _STEP1 open do
	(
		x = (useSettings "x" "float" "r") 
		y = (useSettings "y" "float" "r") 
	
		if(x != 0.0 and y != 0.0) do rPrepareModel.pos = [x, y]
		
		checkWinrar()		
	)
		
	on _STEP1 moved p do
	(
		iWindowPos = p
	)
	
	on _STEP1 close do
	(
		saveWindowPos()
	)
)

rollout _STEP2 "STEP 2: Model Transform" 
(
	group "1. Geometry Fix"
	(
		button btnResetXForm "Reset XForm" across: 2 tooltip: "Collaps modifier stack and reset objects transformation."
		button btnBoxTrick "Box Trick" tooltip: "Fix broken geometry.\nAttach  selected objects to box then remove generated faces of box."
	)

	group "2. Group"
	(
		button btnGroup "Make Group" tooltip: "Make group from selected objects."
	)
	
	group "3. Set Pivot"
	(
		button btnSetPivotBotton "Bottom" across: 3 tooltip: "Set pivot point in center and bottom of objects." align: #right offset: [15,0]
		label lblOr "or" width: 10 offset: [0,3]
		button btnSetPivotTop "Top" tooltip: "Set pivot point in center and top of objects." align: #left offset: [-15,0]
	)
	
	group "4. Position"
	(
		button btnPostiton "Position in Center Scene" tooltip: "Move objects in to center of scene."
	)
		
	Timer tmrBoxTrick "" pos:[0,0] width:24 height:24 interval:100 active:false
		
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
		
	on tmrBoxTrick tick do
	(		
		clearListener()
		progressStart "Box Trick"
		escapeEnable
			
		hObj = selection as array
	
		forSelect = #()
		
		for i = 1 to hObj.count where superClassOf hObj[i] == GeometryClass do
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
	
	on btnGroup pressed do
	(
		if(not isSelected()) do return false
		if(szModelName.count < 1) do return messageBox "Please do STEP 1" title: "Warning!"
			
		ungroupRecursive selection
			
		group selection name:szModelName select:true
	)
	
	on btnBoxTrick pressed do
	(
		if(not isSelected()) do return false
		
		ungroupRecursive selection
			
		tmrBoxTrick.active = true
		btnBoxTrick.enabled = false
	)
	
	on btnPostiton pressed do
	(
		if(not isSelected()) do return false
		
		g = group selection

		g.pos = [0,0,0]
		g.pos.z = 0 - selection.min.z
			
		ungroup g		
	)
	
	fn setPivot p =
	(
		selection.pivot = selection.center
		selection.pivot.z = p
			
		ForceCompleteRedraw()
	)
	
	on btnSetPivotBotton pressed do
	(
		if(not isSelected()) do return false
			
		setPivot selection.min.z
	)
	
	on btnSetPivotTop pressed do
	(
		if(not isSelected()) do return false
			
		setPivot selection.max.z
	)
	
		
	progressBar hr "" height: 3 offset:[0,15]
	button btnPrev "<< Back" align: #left  across: 2
	button btnNext "Next >>" align: #right  enabled: true	
	on btnPrev pressed do sliderStep 1 true	
	on _STEP2 rolledUp x do sliderStep 2 x
	on btnNext pressed do sliderStep 3 true	
)

rollout _STEP3 "STEP 3: Basic Setup" 
(	
	group "Files"
	(
		checkbox cbxAddPostfix "" checked: true align: #left tooltip: "Add render tag in file name for saved formats *.max, *.fbx, *.obj (ex. chair_vray.max)"
	)
	
	group "Geometry"
	(
		checkbox cbxRenameObjects "Rename objects" checked: true align: #left tooltip: "Rename objects to name specified in STEP 1."
		checkbox cbxLayer0 "Remove Layers" checked: true align: #left tooltip: "Remove all layers.\nSet default (0) layer for all objects."
	)
	
	group "Materials"
	(
		checkbox cbxSetMaterialNames "Material Names Same as Model Name" checked: true tooltip: "Rename materials to name specified in STEP 1."
		checkbox cbxTexturesStrip "Strip Textures Paths" checked: true tooltip: "Remove textures paths, leave only filename (ex. maps/texturename.jpg).\n\nImportant!\nFor upload model to 3ddd.ru/3dsky.org please keep this option checked!"
		checkbox cbxTexturesRename "Rename Textures as Model Name" checked: true tooltip: "Add model tag in textures file name specified in STEP 1."
		checkbox cbxAddMatLib "Create Material Library (*.mat)" checked: true tooltip: "Add Material Library (*.mat) to archive."
	)
		
	group "WinRar Path"
	(
		edittext edtWinRarPath "" height: 25 readOnly: true
		button btnSelectPath "Select WinRar Path"
	)
	
	group "Save Model To"
	(
		edittext edtSaveToPath "" height: 25 readOnly: true
		button btnSaveToPath "Select Path"
	)
	
	on cbxSetMaterialNames changed x do (bSetMaterialNames = x; useSettings "SETTINGS_MATERIAL_NAMES" x "w")
	on cbxTexturesStrip changed x do (bTexturesStrip = x; useSettings "SETTING_STRIP_TEXTURE" x "w")
	on cbxTexturesRename changed x do (bTexturesRename = x; useSettings "SETTING_RENAME_TEXTURE" x "w")
	on cbxAddPostfix changed x do (bModelPostfix = x; useSettings "SETTING_ADD_POSTFIX" x "w")
	on cbxRenameObjects changed x do (bObjectsRename = x; useSettings "SETTING_RENAME_OBJECTS" x "w")
	on cbxAddMatLib changed x do (bAddMatLib = x; useSettings "SETTING_ADD_MATLIB" x "w")
	on cbxLayer0 changed x do (bLayer0 = x; useSettings "SETTING_REMOVE_LAYERS" x "w")
		
	on btnSelectPath pressed do
	(
		f = getOpenFileName caption: "Select WinRar Path" types:"winrar.exe|winrar.exe"
		
		if(f != undefined) do
		(
			edtWinRarPath.text = f
			szWinRarPath = f
						
			useSettings "WINRAR" f "w"
		)
	)
	
	on btnSaveToPath pressed do
	(
		f = getSavePath  caption: "Select Path" initialDir: szSaveToPath
		
		if(f != undefined) do
		(
			p = FilterString f szGoodChars
			if(p.count > 0) do return messageBox "The paths are not supported by cyrillic characters!\nPlease choose another path!" title: "Warning!"
			
			szSaveToPath = f + "\\"
			edtSaveToPath.text = szSaveToPath
									
			useSettings "SAVETO" szSaveToPath "w"
		)
	)
	
	on _STEP3 open do
	(
		w = useSettings "WINRAR" "string" "r"
		
		if(doesFileExist w) do szWinRarPath = w
			
		edtWinRarPath.text = szWinRarPath
				
		n = useSettings "SAVETO" "string" "r"
		r = FilterString n szGoodChars
		
		if(n != "" and n != undefined and r.count == 0) do szSaveToPath = n
			
		edtSaveToPath.text = szSaveToPath
		
		p = getRender()
		cbxAddPostfix.caption = "Add  \"" + p[1] + "\"  in file name?"

		cbxSetMaterialNames.checked = useSettings "SETTINGS_MATERIAL_NAMES" "bool" "r"
		bSetMaterialNames = cbxSetMaterialNames.checked
		
		cbxTexturesStrip.checked = useSettings "SETTING_STRIP_TEXTURE" "bool" "r"
		bTexturesStrip = cbxTexturesStrip.checked 
		
		cbxTexturesRename.checked = useSettings "SETTING_RENAME_TEXTURE" "bool" "r"
		bTexturesRename = cbxTexturesRename.checked;
		
		cbxAddPostfix.checked = useSettings "SETTING_ADD_POSTFIX" "bool" "r"
		bModelPostfix = cbxAddPostfix.checked; 
		
		cbxRenameObjects.checked = useSettings "SETTING_RENAME_OBJECTS" "bool" "r"
		bObjectsRename = cbxRenameObjects.checked; 
		
		cbxAddMatLib.checked = useSettings "SETTING_ADD_MATLIB" "bool" "r"
		bAddMatLib = cbxAddMatLib.checked; 
		
		cbxLayer0.checked = useSettings "SETTING_REMOVE_LAYERS" "bool" "r"
		bLayer0 = cbxLayer0.checked; 
	)
	
	progressBar hr "" height: 3 offset:[0,15]
	button btnPrev "<< Back" align: #left  across: 2
	button btnNext "Next >>" align: #right  enabled: true	
	on btnPrev pressed do sliderStep 2 true	
	on _STEP3 rolledUp x do 
	(	
		if(szModelName.count > 0) do cbxRenameObjects.caption = "Rename objects to \"" + szModelName + "\""
		
		sliderStep 3 x
	)
	on btnNext pressed do sliderStep 4 true
)

rollout _STEP4 "STEP 4: Preview" 
(
	group "Preview"
	(
		bitmap btmPreview
		label lblDimensions ""
		dropdownlist ddlPreview "" align: #left width: 193 across: 2
		button btnDelPreview "X" align: #right tooltip: "Delete Preview"
		
	)

	button btnPreviewAdd "Add New Preview" offset: [0, 12]
		
	fn showPreview f =
	(		
		if(f == undefined or not doesFileExist f) do return false
		
		w = btmPreview.width
		h = btmPreview.height
		
		b = bitmapTex filename: f					
		b2 = bitmap w h			
		copy b.bitmap b2
		
		btmPreview.bitmap = b2
		
		w = b.bitmap.width as string
		h = b.bitmap.height as string
		
		lblDimensions.caption = w + " x " + h	
	)
	
	progressBar hr "" height: 3 offset:[0,15]
	button btnPrev "<< Back" align: #left  across: 2
	button btnNext "Next >>" align: #right  enabled: true	
	on btnPrev pressed do sliderStep 3 true	
	on _STEP4 rolledUp x do sliderStep 4 x
	on btnNext pressed do sliderStep 5 true
	
	fn getOpenImage = 
	(
		f = #()
		
		imageDialog = dotNetObject "System.Windows.Forms.OpenFileDialog" 
		imageDialog.title = "Select Preview"
		imageDialog.Multiselect = true
		imageDialog.Filter = "JPG (*.jpg)|*.jpg|PNG (*.png)|*.png|BMP (*.bmp)|*bmp"
		imageDialog.FilterIndex = 1
		
		result = imageDialog.showDialog() 
		result.ToString() 
		result.Equals result.OK 
		result.Equals result.Cancel 
		
		f = imageDialog.fileNames 
		 
		return f
	)
	
	on btnPreviewAdd pressed do
	(
		f = getOpenImage()
				
		if(f.count != 0) do
		(
			for i in f do append szPreviewPath i
						
			ddlPreview.items = for i in szPreviewPath collect filenameFromPath i

			ddlPreview.selection = szPreviewPath.count
			btnNext.enabled = true	

			showPreview	f[f.count]							
		)
	)
	
	on ddlPreview selected x do
	(
		showPreview szPreviewPath[x]
	)
	
	on btnDelPreview pressed do
	(
		s = ddlPreview.selection
		
		btmPreview.bitmap = bitmap 1 1 color: (color 68 68 68)
		lblDimensions.caption = ""
		if(s != 0) do 
		(
			deleteItem szPreviewPath s
			ddlPreview.items = for i in szPreviewPath collect filenameFromPath i
			showPreview	szPreviewPath[1]
		)
		
	)
)

rollout _STEP5 "STEP 5: Create Archive" 
(
	global iColumnOffset = -50
	global szModelName = ""
	global szDir = ""
	global szZip = ""
	global fileToLoad = ""
	
	
	group "Info"
	(
		label lblModelName "Name:" align: #left across: 2
		label lblModelNameStat "" align: #left offset: [iColumnOffset, 0]		
		label lblModelVerison "Version:" align: #left across: 2
		label lblModelVersionStat "" align: #left offset: [iColumnOffset, 0]
		label lblPreview "Preview:" align: #left across: 2
		label lblPreviewStat "" align: #left offset: [iColumnOffset, 0]
		label lblUnits "Units:" align: #left  across: 2
		label lblUnitsStat "" align: #left offset: [iColumnOffset, 0]
		label lblRender "Render:" align: #left  across: 2
		label lblRenderStat "" align: #left offset: [iColumnOffset, 0]
		label lblWinrar "WinRar:" align: #left  across: 2 
		label lblWinrarStat "" align: #left offset: [iColumnOffset, 0] enabled: false
		button btnDisplayStatistic "Statistic" align: #right
						
	)
	
	group "Add Version"
	(
		label lblMax "MAX: " across: 5 align: #left
		dropdownlist ddlMax "" align: #left width: 60 offset: [-20, -4]
		
		checkbox cbxFBX "FBX" checked: true align: #left 
		checkbox cbxOBJ "OBJ" checked: true align: #left
		checkbox cbx3DS "3DS" checked: false align: #left
	)
	
	button btnSaveModel "Save Model" width: 200 height: 35 offset: [0, 10]
	
	progressBar hr "" height: 3 offset:[0,15]
	button btnPrev "<< Back" align: #left 
	
	timer tmrExport "" interval: 1000 active: false	
	
	global iCountExport = 1
	
		
	fn displayStatistic sel:selection displ:true =
	(
		-- Statistic
		if(not isSelected()) do return ""
		
		sPoly = polyCount sel
		v = ddlMax.selected
		
		bBoxTrick = "No"
		bResetXForm = "No"

		for i in sel where getUserProp i "boxtrick" != undefined do bBoxTrick = "Yes"
		for i in sel where getUserProp i "xform" != undefined do bResetXForm = "Yes"
		if(szModelName.count < 1) do szModelName = "No"
		
		modelParts = for i in sel where isGroupHead i == false collect i
		
		r = getRender()
		
		m = "Name: \t\t" + szModelName
		m += "\nVersion: \t\t" + v
		m += "\nPreview: \t\t" + lblPreviewStat.caption
		m += "\nUnits: \t\t" + lblUnitsStat.caption
		m += "\nDimension: \t" + (getDim sel)
		m += "\nPolys: \t\t" + sPoly
		m += "\nXForm: \t\t" + bResetXForm
		m += "\nBox Trick: \t" + bBoxTrick
		m += "\nModel Parts: \t" + modelParts.count as string
		m += "\nRender: \t\t" + r[2]
		m += "\nFormats: \t\t" + "3Ds Max " + v + (if(cbxOBJ.checked) then ", OBJ" else "") + (if(cbxFBX.checked) then ", FBX" else "") + (if(cbx3DS.checked) then ", 3DS" else "") 
		
		if(displ == true) do showStatistic m
		
		return m
	)
	
	fn getFullPath p = 
	(	
		if(doesFileExist p) do return p
		
		return mapPaths.getFullFilePath (filenameFromPath  p)
	)
		
	fn gatherTextures szMaps type: Bitmaptexture = 
	(
		oldMtl = #()
		oldTex = #()
		errorTex = #()
		toRename = #()
		
		try
		(		
			for i in selection where i.material != undefined and findItem oldMtl i.material == 0 do
			(			
				if(bSetMaterialNames and findString (i.material.name) szModelName == undefined) do i.material.name = szModelName + "_" + i.material.name
				
				append oldMtl i.material
								
				for b in getClassInstances type target:i do
				(				
					f = getFullPath b.filename
					
					n = filenameFromPath f
					p = getFilenamePath f
										
					if(bTexturesRename and findItem oldTex b == 0) do 
					(
						n = szModelName + "_" + n					
						append oldTex b
						
						if(doesFileExist f) then append toRename #(b, (p + n), (szMaps + n), n, f) else append errorTex #(b.filename, i.name)								
					)									
				)
			)
		)catch()
		
		if(errorTex.count > 0) do return errorTex
		
		for t in toRename do
		(
			copyFile t[5] t[3]
			
			t[1].filename = t[2]
			if(bTexturesStrip) do t[1].filename = "maps\\" + t[4]
		)

		return errorTex
	)
	
	
	on btnPrev pressed do sliderStep 4 true	
	
	on _STEP5 rolledUp x do 
	(			
		lblModelNameStat.caption = szModelName
		if(szModelName.count < 1) do lblModelNameStat.caption = "No"

		u = (trimLeft (units.SystemType as string) "#")
		u[1] = toUpper u[1]
		lblUnitsStat.caption = u

		lblWinrarStat.caption = "Not Installed!"
		if(getWinRar() != undefined) do lblWinrarStat.caption = "Installed!"; lblWinrarStat.enabled = true
			
		lblPreviewStat.caption = "No"
		if(szPreviewPath.count > 0) do lblPreviewStat.caption = "Yes"
		
		r = getRender()
		lblRenderStat.caption = r[2]
				
		sliderStep 5 x
	)
	
	on _STEP5 open do
	(
		v = getMaxVersion()
		ddlMax.items = v
		lblModelVersionStat.caption = ddlMax.items[1]
		
		cbxFBX.checked = useSettings "FORMAT_FBX" "bool" "r"
		cbxOBJ.checked = useSettings "FORMAT_OBJ" "bool" "r"
		cbx3DS.checked = useSettings "FORMAT_3DS" "bool_not" "r"
		
		x = useSettings "FORMAT_MAX" "int" "r"
		
		if(x != 0) do ddlMax.selection = x
	)
	
	on ddlMax selected x do 
	(
		lblModelVersionStat.caption = ddlMax.items[x]		
		useSettings "FORMAT_MAX" x "w"
	)
	
	on cbxFBX changed x do useSettings "FORMAT_FBX" x "w"
	on cbxOBJ changed x do useSettings "FORMAT_OBJ" x "w"
	on cbx3DS changed x do useSettings "FORMAT_3DS" x "w"
	on btnDisplayStatistic pressed do displayStatistic()
		
	on btnSaveModel pressed do 
	(			
		fileToLoad = maxFilePath + maxFileName
		 		
		if(fileToLoad.count < 1) do return messageBox "Please save scene!" title: "Warning!"
		if(not isSelected()) do return false
		if(szModelName.count < 1) do return messageBox "Please do STEP 1" title: "Warning!"
		
		versions 
		v = ddlMax.selected
		supportVersions = getMaxVersion()
		currVer = supportVersions[supportVersions.count]
		r = getRender()
		p = if(bModelPostfix) then r[1] else ""
			
		szDir = szSaveToPath + szModelName + "\\"
		szMaps = szDir + "maps\\"
		szExportDir = szDir + "export\\"
		szMtl = szDir + "matlib\\"
		szPreview = szDir + "preview\\"
			
		szScene = (szDir + szModelName + "_" + v + "_" + p + ".max")
		szExport[1] = (szExportDir + szModelName + "_" +  p + ".obj")		
		szExport[2] = (szExportDir + szModelName + "_" +  p + ".3ds")
		szExport[3] = (szExportDir + szModelName + "_" +  p + ".fbx")
			
		szZip = szSaveToPath + "Please wait, pack files.bat"
		szInfo = szDir + "info.txt"
		szMatLib = (szMtl + szModelName + "_" +  p + ".mat")
		
		makeDir szDir	
		makeDir szMaps	
		makeDir szExportDir	
		makeDir szPreview	
		makeDir szMtl	
			
		
		-- Rename Models			
		if(bObjectsRename) do (for i in selection do i.name = uniqueName (szModelName + "_"))
		
		-- Layer
		if(bLayer0) do
		(
			layer0 = LayerManager.getLayer 0
			for i in selection do layer0.addNode i
		)
			
		-- Materials
		
		errorTex = #()
		
		t1 = gatherTextures szMaps
		t2 = gatherTextures szMaps type: CoronaBitmap
		
		join errorTex t1
		join errorTex t2
		
		if (errorTex.count > 0) do
		(
			t = localTime
			m = "[" + t + "] : Error! Not found next textures. Please fix model and try again.\n\n"
			
			for er in errorTex do m += "Object: " + er[2] + "\nTexture: " + er[1] + "\n\n"
			
			showStatistic m title:"Log" width:500
			return false
		)
	
		saveMtlLib szMatLib
		
		-- Preview
		iPreview = 0
		for i in szPreviewPath do
		(
			p = getFilenameType i 
			if(doesFileExist i) do 
			(
				iPreview += 1
				copyFile i (szPreview + "preview" + iPreview as string + p)
			)
		)
		
		
		currentRender = renderers.current
			
		-- Add Versions
		saveNodes selection szScene saveAsVersion:(v as integer) quiet:true
		resetMaxFile #noPrompt
		mergeMAXFile szScene #select #mergedups #renameMtlDups #noRedraw quiet:true
		-- Fix selection in 3Ds Max 2016		
		if(currVer != v) then
		(
			saveNodes objects szScene saveAsVersion:(v as integer) quiet:true
		)
		else
		(
			saveNodes objects szScene quiet:true
		)
				
		try(renderers.current = currentRender)catch()
		
		select objects
			
		-- Info		
		m = displayStatistic sel:objects displ:false
		l = stripTab m				
		writeFile szInfo l
		
		setClipBoardText l
		
		displayStatistic sel:objects displ:true
		
		tmrExport.active = true
	)
	
	on tmrExport tick do
	(		
		if(cbxFBX.checked and iCountExport == 1) do 
		(
			FBXExporterSetParam "FileVersion" "FBX201200"	-- Warning!! This option important!!
			exportFile szExport[3] #noPrompt  --selectedOnly:true
		)
		if(cbxOBJ.checked and iCountExport == 2) do exportFile szExport[1] #noPrompt  --selectedOnly:true
		if(cbx3DS.checked and iCountExport == 3) do exportFile szExport[2] #noPrompt  --selectedOnly:true
						
		iCountExport += 1
		
		if(iCountExport >= 4) do 
		(																			
			szLaunch = ("echo off\ncls\necho Create archive, please wait..\n\"" + szWinRarPath + "\" m -r -ibck -ep1 -m5 \"" + szSaveToPath + szModelName + ".rar\" \"" + szDir + "\"\ncls\nrd \"" + szDir + "\" /q /s\ncls\ndel \"" + szZip +"\" /q\ncls")
					
			writeFile szZip szLaunch
						
			shellLaunch szZip ""
						
			resetMaxFile #noPrompt
			loadMaxFile fileToLoad useFileUnits: true quiet:true
								
			shellLaunch szSaveToPath ""	
						
			iCountExport = 1
			tmrExport.active = false
			
			/*successMessage = "Model prepared!\nNow creating archive \"" + szModelName + ".rar.\"\n\nPress OK for open directory with archive." 
			messageBox successMessage beep: false title: "Success!"	*/					
		)
	)
)


rollout rAbout "About" 
(
	local c = color 200 200 200 
	
	label lbl2 "Prepare Model 3DDD" 
	label lbl3 szVer 
	
	label lbl5 "by MastaMan" 
	label lbl6 "" 

		
	hyperLink href2 "Script Home" address: "http://3ddd.ru/forum/thread/show/skript_prepare_model_3ddd/" align: #center hoverColor: c visitedColor: c
	hyperLink href "http://3ddd.ru" address: "http://3ddd.ru" align: #center hoverColor: c visitedColor: c
)

addRollout _STEP1 rPrepareModel rolledUp:false 
addRollout _STEP2 rPrepareModel rolledUp:true 
addRollout _STEP3 rPrepareModel rolledUp:true 
addRollout _STEP4 rPrepareModel rolledUp:true 
addRollout _STEP5 rPrepareModel rolledUp:true 
addRollout rAbout rPrepareModel rolledUp:true 
setFocus _STEP1.edtModelName