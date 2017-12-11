/*  
[INFO] 

NAME = WebGL Export
VERSION = 1.0.0
AUTHOR = Vasily Lukyanenko
DEV = VISCO
HELP = 

[1.0.0]

* First release =

[1.0.1]

+ Added: VRay to Standard converter=
+ Added: Set color 240 240 240 by default for Standard materials=
+ Added: Batch Setup Standard Material Properties=

[ABOUT]

This script helps to prepare model for WebGL Player in Assets Library=

[TEST]

[SCRIPT]
*/	

try(closeRolloutFloater fAssetsLibraryWebGLExport)catch()
global fAssetsLibraryWebGLExport = newRolloutFloater "WebGL Export" 220 610
global szChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890-_ "

global _SAVEPATH = @"C:\temp\Assets Library\"

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

rollout _rWebGLExport_PreExportFixes "PreExport Fixes" 
(
	group "Convert VRay to Standard" (
		button btnConvertVrayToStandard "Convert"
		label lblConvertStatus ""
	)
	
	group "Setup Standard Material Properties" (
		spinner spnSpecularLevel "Specular Level  " type: #integer range: [0, 900, 60]
		spinner spnGlossiness "Glossines  " type: #integer range: [0, 900, 55]
		button btnSetupProperties "Setup Properties" offset: [0, 5]
	)
	
	fn isVRayInstalled = (
		return (for o in rendererClass.classes where matchPattern (o as string) pattern:"*v_ray*" collect o).count > 0
	)
	
	fn getConvertColorToPercent c =
	(
		k = (c.r + c.g + c.b) / 3
		
		return 100 - (k / 2.55)
	)
	
	fn isBlack c =
	(
		--c = c as point3
		--return (distance c [0,0,0]) == 0
		return c == (color 0 0 0)
	)
	
	fn convertVrayToStandard = 
	(
		if(isVRayInstalled() == false) do return messageBox "Vray not installed!" title: "Warning!"
		
		lblConvertStatus.caption = "Converting..."
		
		MMClean.fixAll prompt: false
		
		for m in getClassInstances VrayMtl do (
			nm = Standard()
			
			nm.name = m.name
			
			-- Set diffuse as VRay
			nm.DiffuseMap = m.texmap_diffuse
			
			-- Set default shiness
			nm.glossiness = 20
			nm.specularLevel = 20
			nm.Specular = color 70 70 70
				
			-- If has reflect
			refl = m.Reflection
			if(not isBlack refl or m.texmap_reflection != undefined) do (
				nm.reflectionMapEnable = on
				nm.reflectionMap = m.texmap_reflection				
				nm.shaderType = 1
				
				-- Get color amount in percent
				k = getConvertColorToPercent refl
				
				-- Set refl level 40 if texmap exist and color black
				if(k < 10 and m.texmap_reflection != undefined) do k = 40
				
				nm.reflectionMapAmount = k
				nm.glossiness = 30
				nm.specularLevel = 60
				nm.Specular = color 200 200 200
			)
			
			-- If has opacity
			refr = m.Refraction 
			if(not isBlack refr) do (
				nm.Diffuse = m.Diffuse
				nm.opacity = 40	
				nm.glossiness = 20
				nm.specularLevel = 90
				nm.Specular = color 120 120 120
			)
			
			replaceInstances m nm
		)
		
		for m in getClassInstances Blend do (
			mtl1 = undefined
			mtl2 = undefined
			
			if(classOf m.map1 == Standard) do mtl1 = m.map1
			if(classOf m.map2 == Standard) do mtl2 = m.map2
			
			
			if(mtl1 != undefined and mtl2 != undefined) do (
				nm = Standard()
								
				mixBlend = Mix()
				
				nm.name = m.name
				
				mixBlend.map1 = mtl1.DiffuseMap
				mixBlend.map2 = mtl2.DiffuseMap
				mixBlend.mask = m.mask
				
				nm.DiffuseMap = mixBlend
								
				nm.DiffuseMap = mtl1.DiffuseMap
				nm.glossiness = mtl1.glossiness
				nm.specularLevel = mtl1.specularLevel
				nm.Specular = mtl1.Specular
				
				replaceInstances m nm
			)
			
		)
		
		for m in getClassInstances VRayLightMtl do (
			nm = Standard()
			nm.useSelfIllumColor = on
			nm.selfIllumColor = m.color
			
			replaceInstances m nm
		)
		
		messageBox "Convert successfully done!" title: "Success!" beep: false
		
		lblConvertStatus.caption = "Done."
	)
	
	on btnConvertVrayToStandard pressed do (
		q = queryBox "Do you really want to convert All materials VRay -> Standard?" title: "Confirm!"
		
		if(not q) do return false
		
		convertVrayToStandard()
	)
	
	on btnSetupProperties pressed do (
		q = queryBox "Do you really want to set properties \nfor All Standard materials in scene?" title: "Confirm!"
		
		if(not q) do return false
		
		for i in getClassInstances Standard do (
			i.glossiness = spnGlossiness.value
			i.specularLevel = spnSpecularLevel.value
		)
		
		messageBox "Setup properties successfully done!" title: "Success!" beep: false
	)
)

rollout _rWebGLExport_Settings "Export Settings" 
(	
	group "Note!"
	(
		label lblNote "Please use system units Meters\nfor correct export model!" align: #left height: 30
	)
	group "Name"
	(
		dotNetControl edtModelName "System.Windows.Forms.TextBox" height: 25 tabstop: true
	)
	group "Textures"
	(
		label lblTextureSize "Texture Size (px):" across: 2 offset: [0, 3] align: #left 
		dropdownlist ddlTextureSize "" width: 70 align: #left items:#("512", "1024", "2048", "4096")	 selection: 2	
		checkbox cbxRenderProcedurals "Render Procedurals" checked: true
	)
	group "Optimize"
	(
		checkbox cbxOptimizeVertex "Vertex" checked: true
		checkbox cbxOptimizeNormals "Normals" checked: true
		checkbox cbxOptimizeTextureCoord "Texture-Coord" checked: true
	)
	
	button btnExport "Export" width: 200 height: 35
	
	fn replaceBadChars s rule: szChars =
	(
		badChars = for i in 1 to s.count where findString rule s[i] == undefined collect s[i]
		for c in badChars do s = substituteString s c ""
			
		return s
	)
		
	fn trim s tag: " "= trimLeft (trimRight s tag) tag
	
	
	fn getMatIDs o = (
		ids = #()
		for f in 1 to  getNumFaces o do appendIfUnique ids (getFaceMatID o f)
		
		return ids
	)
	
	fn hasMaterialError =
	(
		mat = #()
		for o in geometry where o != undefined and o.material != undefined do 
		(	
			m = o.material			
			if(classOf  m == Multimaterial) then join mat m.materialList  else append mat m			
		)
		
		mat = makeUniqueArray mat
		
		badMats = #()
		badMats = for m in mat where m != undefined and classOf m != Standard  collect m
		
		msg = "Found errors in Materials:\n\n"
		for i in badMats do msg += i.name + " (" + (classOf i) as string + ") - not Standard material!\n"
		
		if(badMats.count != 0) do messageBox msg title: "Error!"
		
		return badMats.count != 0
	)
	
	fn ungroupRecursive s = for i in s where isGroupHead i and isValidNode i do explodeGroup i
		
	fn invalidBitmap b = 
	(	
		return b == undefined or b.filename == undefined or b.filename == "" or b.filename.count == 0 or not doesFileExist b.filename
	)
	
	fn normalizeDiffuseColor = (
		c = color 240 240 240
		for i in getClassInstances Standard where i.opacity == 100 do i.Diffuse = c
	)
	

	
	fn checkTexturesError =
	(
		errorTex = #()
				
		for i in geometry where i.material != undefined do
		(
			for b in getClassInstances BitmapTex target:i where b != undefined do
			(
				if(invalidBitmap b) do
				(										
					append errorTex #("empty file name path! Map: " + b.name, i.name) 
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
	)TangentSpaceExport

	
	fn setFbxSettings =
	(
		-- Geometry
		
		FBXExporterSetParam "SmoothingGroups" true		
		FBXExporterSetParam "NormalsPerPoly" false
		FBXExporterSetParam "TangentSpaceExport" false
		FBXExporterSetParam "SmoothMeshExport" false
		FBXExporterSetParam "Preserveinstances" false
		FBXExporterSetParam "SelectionSetExport" false
		FBXExporterSetParam "GeomAsBone" false
		FBXExporterSetParam "Triangulate" true
		FBXExporterSetParam "ColladaTriangulate" true
		FBXExporterSetParam "ConvertUnit" "m"
		FBXExporterSetParam "PreserveEdgeOrientation" true
		FBXExporterSetParam "ASCII" false
		FBXExporterSetParam "EmbedTextures" true
		FBXExporterSetParam "FileVersion" "FBX200900"
		FBXExporterSetParam "UseSceneName" true
		
		-- Animation
		FbxExporterSetParam "Animation" false
		FbxExporterSetParam "Cameras" false
		FbxExporterSetParam "Lights" false
		FbxExporterSetParam "Shape" false		
		
		-- Import Settings
		try(
			FbxImporterSetParam "RescaleRoot" true
		) catch (
			FbxImporterSetParam "ScaleConversion" true
			FbxImporterSetParam "ConvertUnit" "m"
		)
	)
	
	fn setObjExpSettings =
	(
		_INI = objExp.getIniName()
		
		setIniSetting _INI "General" "UseLogging" "1"
		setIniSetting _INI "General" "Preset" "<NONE>"
		setIniSetting _INI "General" "MapSearchPath" "" 
		setIniSetting _INI "Geometry" "FlipZyAxis" "1" 
		setIniSetting _INI "Geometry" "Shapes" "0" 
		setIniSetting _INI "Geometry" "ExportHiddenObjects" "0" 
		setIniSetting _INI "Geometry" "FaceType" "1" 
		setIniSetting _INI "Geometry" "TextureCoords" "1" 
		setIniSetting _INI "Geometry" "Normals" "1" 
		setIniSetting _INI "Geometry" "ObjScale" "1.000000" 
		setIniSetting _INI "Geometry" "SmoothingGroups" "1" 
		setIniSetting _INI "Material" "UseMaterial" "1" 
		setIniSetting _INI "Material" "CreateMatLibrary" "1" 
		setIniSetting _INI "Material" "ForceBlackAmbient" "1" 
		setIniSetting _INI "Material" "UseMapPath" "1" 
		setIniSetting _INI "Material" "MapPath" "./" 
		setIniSetting _INI "Material" "AutoMapChannel" "0" 
		setIniSetting _INI "Material" "MapChannel" "1" 
		setIniSetting _INI "Material" "ExtendedParams" "1" 
		setIniSetting _INI "Material" "ConvertBitmaps" "1" 
		setIniSetting _INI "Material" "RenderProcedural" "1" 
		setIniSetting _INI "Material" "ResizeMaps" "1" 
		setIniSetting _INI "Material" "ResizeMaps2n" "0" 
		setIniSetting _INI "Material" "MapFormat" "1" 
		setIniSetting _INI "Material" "MapSizeX" (ddlTextureSize.selected) 
		setIniSetting _INI "Material" "MapSizeY" (ddlTextureSize.selected) 
		setIniSetting _INI "Output" "RelativeIndex" "0"
		setIniSetting _INI "Output" "Target" "0"
		setIniSetting _INI "Output" "Precision" "6"
		setIniSetting _INI "Optimize" "optVertex" (if(cbxOptimizeVertex.checked) then "1" else "0" )
		setIniSetting _INI "Optimize" "optNormals" (if(cbxOptimizeNormals.checked) then "1" else "0" )
		setIniSetting _INI "Optimize" "optTextureCoords" (if(cbxOptimizeTextureCoord.checked) then "1" else "0" )
		
		return _INI
	)
	
	fn setUnits = 
	(
		units.DisplayType = #Metric
		units.MetricType = #Meters
		units.SystemType = #Meters
	)
	
	fn exportToWebGL =
	(	
		_REOPEN = maxFilePath + maxFileName
		_NAME = replaceBadChars edtModelName.text
		_NAME = trim _NAME
		
		_DIR = _SAVEPATH + _NAME + @"\"
		_INFOINI = _DIR + "info.ini"
		_INFOINITMP = _DIR + "info.tmp"
		
		err =  units.SystemType != #meters
		if(err) do return messageBox "Please use system units Meters for export and try again!" title: "Warning"
		
		MMClean.fixAll prompt: false
		
		err = hasMaterialError()
		if(err) do return false
		
		err = checkTexturesError()
		if(err) do return false
		
		normalizeDiffuseColor()
		
		if(_NAME.count < 2) do return messageBox "Please enter the name!" title: "Warning!"
		makeDir _SAVEPATH	
		makeDir _DIR	
				
		ungroupRecursive objects
				
		for i in geometry do try(
			resetXForm i
			if(canConvertTo i Editable_Mesh) do convertToMesh i
		)catch()
		
		for i in geometry do i.name = uniqueName (_NAME + "_")
		geometry.pivot = geometry.center
		geometry.pivot.z = geometry.min.z
		geometry.pos = [0,0,0]
		geometry.pivot = geometry.center
				
		setFbxSettings()
		exportFile (_DIR + "index.fbx") #noPrompt
		resetMaxFile #noPrompt
		
		-- Set units
		setUnits()
		setFbxSettings()
		importFile (_DIR + "index.fbx") #noPrompt
		deleteFile (_DIR + "index.fbx")
				
		_INI = setObjExpSettings()
		exportFile (_DIR + "index.obj") #noPrompt		
		deleteFile _INI
		
		for ff in getFiles (_DIR + @"index.fbm\*") do deleteFile ff
				
		setIniSetting _INFOINITMP "INFO" "TYPE" "\"webgl\""
		
		convertFileToUTF8 _INFOINITMP	_INFOINI
		
		deleteFile (_SAVEPATH + _NAME + ".zip")
		
		zipper = zip()
		zipper.packFiles source: _DIR target: _SAVEPATH name: _NAME del: true
		
		if(_REOPEN != "") do loadMaxFile  _REOPEN useFileUnits: true quiet: true
		
		q = queryBox "Export done!\nDo you want to open destination folder?" title: "Done!"
		if(not q) do return false
		
		shellLaunch _SAVEPATH ""
	)
	
	on btnExport pressed do exportToWebGL()
	on edtModelName TextChanged s a do
	(
		s.Text = replaceBadChars s.Text
		s.Select (s.Text.count) 0
	)
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

addRollout _rWebGLExport_Settings fAssetsLibraryWebGLExport rolledUp:false
addRollout _rWebGLExport_PreExportFixes fAssetsLibraryWebGLExport rolledUp:false
addRollout _rAbout fAssetsLibraryWebGLExport rolledUp:true 