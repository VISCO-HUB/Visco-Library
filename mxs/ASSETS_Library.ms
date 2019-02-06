/*  
[INFO] 

NAME = Assets Library
VERSION = 1.1.0
AUTHOR = Vasily Lukyanenko
DEV = VISCO
HELP = 

[INSTALL]

MACRO=AssetsLibrary
CAT=VISCO
TEXT=Assets Library
ICON=#("AssetsLibrary", 1)

[ICONS]

\\visco.local\data\Instal_Sync\scripts\scripts\icons\AssetsLibrary\AssetsLibrary_24a.bmp=
\\visco.local\data\Instal_Sync\scripts\scripts\icons\AssetsLibrary\AssetsLibrary_24i.bmp=
\\visco.local\data\Instal_Sync\scripts\scripts\icons\AssetsLibrary\AssetsLibrary_16i.bmp=
\\visco.local\data\Instal_Sync\scripts\scripts\icons\AssetsLibrary\AssetsLibrary_16a.bmp=


[1.0.0]

* First release =

[1.0.5]

* Changed: Improved speed and fixes=
* Changed: Favicon=


[1.0.6]

+ Added: When open model you can download data for PackModel=

[1.0.7]

- BugFix: Auto Login=

[1.0.8]

* Improved: Restore window on relaunch Library=

[1.0.9]

* Improved: Support material merge=

[1.1.0]

+ Added: Restore OSL settings from ini=

[ABOUT]

Open Assets Library via 3Ds Max=

[TEST]

[SCRIPT]
*/


try(
	_ViscoAssetsForm.Show()
	_ViscoAssetsForm.WindowState = (dotNetClass "System.Windows.Forms.FormWindowState").Normal
	_ViscoAssetsForm.Focus()
) catch 	(
	global siteUrl = @"http://assets.visco.no/"
	global siteUrl = @"http://svg-web-003/"
	global iconFile = @"\\visco.local\data\Instal_Sync\scripts\scripts\img\assets_favicon.ico"
	global trustUrl = siteUrl + @"vault/mxs/mxsTrustUser.php?user=" + sysInfo.username
	global forceRefresh
	global clearIEChache
	global __AUTO_PLACE_MODE = false

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

	struct file (
		fn path f = getFilenamePath f,
		fn name f = getFilenameFile f,
		fn ext f = getFilenameType f,
		fn nameext f = filenameFromPath f,
		fn lastfolder f = (
			p = getFilenamePath f
			parts = filterString p @"\"
			if(parts.count == 0) do return ""
			return parts[parts.count]
		)
	)
	
	fn saveProp p d = try(fileProperties.addProperty #custom p d) catch()

	 fn useSettings k p w =
	(
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

	global _ViscoAssetsForm
	global _FILLREPORT = "\n1. Open full model info\n2. Go to comments\n3. Check the button \"Send As Bug Report\"\n3. Fill the form\n4. Press the button \"Send\""
	global _MODELNOTEXIST = "This model not exist!\n\nPlease notify administrator about this issue:" + _FILLREPORT
	global _MODELBAD = "This model not exist in data base!\n\nPlease notify administrator about this issue:" + _FILLREPORT
	global _BADRESPONCE = "Can not connect to the server!\nCheck your network connection and try again!"

	try(_ViscoAssetsForm.close())catch()	
		
	_ViscoAssetsForm = dotNetObject "MaxCustomControls.MaxForm"
	_ViscoAssetsForm.size = dotNetObject "System.Drawing.Size" 1240 800
	_ViscoAssetsForm.MinimumSize = dotNetObject "System.Drawing.Size" 800 800
	_ViscoAssetsForm.ShowInTaskBar = true
	_ViscoAssetsForm.text = siteUrl
	_ViscoAssetsForm.StartPosition = _ViscoAssetsForm.StartPosition.Manual	
		
	executeTimer = dotNetObject "System.Windows.Forms.Timer"	
	executeTimer.interval = 10

		
	try(_ViscoAssetsForm.icon = dotNetObject "System.Drawing.Icon" iconFile)catch()
	--_ViscoAssetsForm.topmost = true
		
	wbTrust = dotNetObject "System.Windows.Forms.WebBrowser"
	wbTrust.size =dotNetObject "System.Drawing.Size" 0 0
	wbTrust.location = dotNetObject "System.Drawing.Point" -100 -100
		
	wbTrust2 = dotNetObject "System.Windows.Forms.WebBrowser"
	wbTrust2.size =dotNetObject "System.Drawing.Size" 0 0
	wbTrust2.location = dotNetObject "System.Drawing.Point" -100 -100


	execCommand = dotNetObject "System.Windows.Forms.Textbox"			
	execCommand.size = dotNetObject "System.Drawing.Size" 0 0
	execCommand.location = dotNetObject "System.Drawing.Point" -100 -100
		
	global wb = dotNetObject "System.Windows.Forms.WebBrowser"
	wb.size = _ViscoAssetsForm.ClientSize
	--wb.AllowWebBrowserDrop  = true
	wb.location = dotNetObject "System.Drawing.Point" 0 0
	wb.ObjectForScripting = execCommand
		

	fn getUri s = dotNetObject "System.Uri" s	


	fn loadUrl =
	(
		wb.url = getUri siteUrl	
	)
		
	_ViscoAssetsForm.controls.add  execCommand
	_ViscoAssetsForm.controls.add  wbTrust
	_ViscoAssetsForm.controls.add  wb
	
	fn getWidnowState =
	(
		s = dotNetClass "System.Windows.Forms.FormWindowState"
		
		case _ViscoAssetsForm.WindowState of
		(	
			(s.Maximized): return #maximized
			(s.Minimized): return #minimized
			default: return #normal
		)
	)

	fn setWindowState =
	(	
		v = useSettings "STATE" "str" "r"
		
		case v of
		(
			"maximized": _ViscoAssetsForm.WindowState = (dotNetClass "System.Windows.Forms.FormWindowState").Maximized
			--"minimized": _ViscoAssetsForm.WindowState = s.Minimized
			default: _ViscoAssetsForm.WindowState = (dotNetClass "System.Windows.Forms.FormWindowState").Normal
		)
	)

	fn saveWindowState =
	(
		s = getWidnowState()
		useSettings "STATE" s "w"
	)

	fn saveWindowPos =
	(
		s = getWidnowState()
		
		if(s != #maximized) do
		(	
			x = _ViscoAssetsForm.left
			y = _ViscoAssetsForm.top
		
			if(x < -31000 or y < -31000) do return false
			
			useSettings "POSX" x "w"
			useSettings "POSY" y "w"
		) 	
	)

	fn saveWindowSize =
	(
		s = getWidnowState()
		
		if(s == #normal) do
		(
			w = _ViscoAssetsForm.size.width
			h = _ViscoAssetsForm.size.height
					
			useSettings "WIDTH" w "w"
			useSettings "HEIGHT" h "w"
		)		
	)

	fn setWindowPos =
	(
		x = useSettings "POSX" "int" "r"	
		y = useSettings "POSY" "int" "r"	
		
		if(x != 0 and y != 0) do	
		(
			_ViscoAssetsForm.Location = dotNetObject "System.Drawing.Point" x y
		)
	)

	fn setWindowSize =
	(
		w = useSettings "WIDTH" "int" "r"	
		h = useSettings "HEIGHT" "int" "r"	
		
		
		if(w != 0 and h != 0) do	
		(
			_ViscoAssetsForm.size = dotNetObject "System.Drawing.Size" w h
		)
	)

	fn clearIEChache = 
	(
		q = queryBox "Clear IE Cache?\n\nWarning: This action will delete temporary Windows files!" title: "Confirm!"
		if(q) do
		(
			cmd = "RunDll32.exe InetCpl.cpl,ClearMyTracksByProcess 8"
			HiddenDosCommand cmd
			cmd = "erase \"%LOCALAPPDATA%\\Microsoft\\Windows\\Tempor~1\\*.*\\\" /f /s /q"
			HiddenDOSCommand  cmd
			cmd = "for /D %%i in (\"%LOCALAPPDATA%\\Microsoft\\Windows\\Tempor~1\\*\") do RD /S /Q \"%%i\""
			HiddenDOSCommand  cmd	
		)
	)

	fn forceRefresh = 
	(	
		for i in 1 to 10 do
		(
			wb.refresh()
			wb.refresh (dotnetclass "System.Windows.Forms.WebBrowserRefreshOption").Completely
		)	
	)

	fn goBack = wb.GoBack()
	fn goForward = wb.GoForward()

	fn onResize s e = 
	(
		wb.size = _ViscoAssetsForm.ClientSize
		saveWindowSize()		
	)

	fn onMove s e = 
	(
		saveWindowPos()
	)

	fn onClose s e =
	(
		saveWindowSize()
		saveWindowPos()
		saveWindowState()
	)

	fn onLoad s e =
	(
		setWindowPos()
		setWindowSize()
		setWindowState()
		
		wbTrust.Navigate (trustUrl)
	)

	fn getUrl = (
		if(wb.url != undefined) then
		(
			return (wb.url).toString()
		) 
		
		return ""
	)

	fn onNavigating e =
	(	
		enableAccelerators = false	
	)


	fn onCompleted e =
	(

	)

	fn onFocus e = 
	(
		for i in 1 to 3 do enableAccelerators = false	
	)

	fn setTexturesPath path: "" geom: selection type: BitmapTex =
	(
		for i in geom as array do
		(
			for b in getClassInstances type target: i do 
			(	
				if(b.filename == undefined) do continue
				if(findString b.filename path == undefined) do
				(
					b.filename = path + b.filename
				)
			)
		)
	)
	
	fn setVRayHDRIPath path: "" geom: selection = (
		if(VRayHDRI == undefined) do return false
		for i in geom as array do
		(
			for b in getClassInstances VRayHDRI target: i do 
			(	
				if(b.HDRIMapName == undefined or b.HDRIMapName == "") do continue
				if(findString b.HDRIMapName path == undefined) do
				(
					b.HDRIMapName = path + b.HDRIMapName
				)
			)
		)
	)
	
	fn setVRayOSLTexPath path: "" geom: selection = (
		if(VRayOSLTex == undefined) do return false
		for i in geom as array do
		(
			for b in getClassInstances VRayOSLTex target: i do 
			(	
				if(b.shader_file == undefined or b.shader_file == "") do continue
				if(findString b.shader_file path == undefined) do
				(
					b.shader_file = path + b.shader_file
					
					iniOsl = b.shader_file + ".ini"
					if(doesFileExist iniOsl) do (
						params = getIniSetting iniOsl "PARAMETERS" 
						for p in params do (
							v = getIniSetting iniOsl "PARAMETERS" p
							try(setProperty b p (execute v)) catch()
						)
					)
				)
			)
		)
	)

	fn setIesPath path: "" geom: selection type: light =
	(
		a = @"assets\"
		for l in geom where superClassOf l == type do 
		(
			if(isProperty l #ies_file and  l.ies_file != undefined and not doesFileExist l.ies_file) do l.ies_file = path + a + filenameFromPath l.ies_file
			if(isProperty l #webFile and l.webFile != undefined and not doesFileExist l.webFile) do l.webFile = path + a + filenameFromPath  l.webFile
			if(isProperty l #iesFile and l.iesFile != undefined  and not doesFileExist l.iesFile) do l.iesFile = path + a + filenameFromPath  l.iesFile	
		)
	)

	fn showStatusPaste t c: false =
	(
		global statusText = t
		global timerActive = c
		global PasteStatus
		
		try(destroyDialog PasteStatus) catch()
		
		rollout PasteStatus "MastaMan" 
		(
			label lblStatus "Downloading..." offset: [0, 14] 
			timer tmrTimer "" active: true interval: 30000
			
			on PasteStatus open do
			(
				lblStatus.caption = statusText
				tmrTimer.active = timerActive
			)
			
			on tmrTimer tick do 
			(	
				try(destroyDialog PasteStatus)catch()
				tmrTimer.active = false
			)
		)
		createDialog PasteStatus 160 50 style: #(#style_border)
	)

	fn getCatNameFromPath p end: 4 =
	(
		d = filterString p "\\"
		if(d.count <= end) do return false
		s =  d[d.count - end]
		s = substituteString s "-" " "
		return s
	)

	fn moveToLayer n sel: #() =
	(
		l = LayerManager.getLayerFromName n
		if(l == undefined) do 
		(
			l = LayerManager.NewLayer()
			l.setName n
		)
		if(l != undefined) do for i in sel do l.addNode i
	)

	fn addFile id mode: #merge =
	(				
		oldSelection = selection as array
		if(oldSelection.count == 0 and mode == #apply_material) do return messageBox "Please selecte objects" title: "Error!"
		
		clearListener()
		url = siteUrl + "vault/mxs/mxsAddModel.php?id=" + id + "&user=" + sysInfo.username + "&time=" + timeStamp() as string
		r = httpGet url	
			
		x = XML()
			
		if(x.load r == false) do return messageBox _BADRESPONCE title: "Error!"	
		resp = x.get "responce"
		
		if(resp != "MODELOK") do
		(
			case resp of
			(
				"MODELNOTEXIST": messageBox _MODELNOTEXIST title: "Error!"
				"MODELBAD": messageBox _MODELBAD title: "Error!"
			)
			
			return false
		)

		f = x.get "file"
		
		if(not doesFileExist f) do return messageBox _MODELNOTEXIST title: "Error!"
		
		showStatusPaste "Downloading..."
		p = (getFilenamePath f)
		
		catName = getCatNameFromPath f
		
		sel = #()
		
		disableSceneRedraw()
		try(setVRaySilentMode()) catch()
				
		case mode of
		(
			#open_material: (				
							
				mergeMaxFile f #select #autoRenameDups  #renameMtlDups   quiet:true
				sel = selection as array
				
				if(sel[1] != undefined and sel[1].material != undefined) do (	
					mat = sel[1].material
					mat.name = sel[1].name
					
					setVRayHDRIPath path: p geom: #(mat)
					--setVRayOSLTexPath path: p geom: #(mat)
					
					setTexturesPath path: p geom: #(mat) type: BitmapTex
					try(setTexturesPath path: p geom: #(mat) type: CoronaBitmap)catch()
										
					if(MatEditor.mode == #basic) then (
						q = queryBox "This operation will replace the active material slot!" title: "Confirm?"
						if(q) do (
							if(not MatEditor.isOpen()) do MatEditor.Open() 
							mid = medit.GetActiveMtlSlot() 
							medit.PutMtlToMtlEditor mat mid
						)
					) else if(MatEditor.mode == #advanced) do (
						if(not sme.IsOpen()) do sme.Open() 
						sme.OpenMtlBrowseDlg()
							
						viewNode = sme.GetView (sme.activeView)
						viewNode.SelectNone()
							
						lastPos = [0, 0]	
						
						smeView = trackViewNodes[#sme][(sme.activeView)]
						for n in 1 to smeView.numSubs do (
							gg = smeView[n]							
							if(isProperty gg "reference") do (
								ref = gg.reference
								nd = viewNode.GetNodeByRef ref
								if(superClassOf ref == material and nd.position[2] > lastPos[2]) do lastPos = nd.position            
							)
						)	
													
						lastPos[2] += 450
							
						viewNode.createNode mat lastPos																			
						sme.SetMtlInParamEditor mat
						viewNode.SetSelectedNodes mat
						
						viewNode.ZoomExtents type: #selected												
					)
					
					try(delete sel) catch()
				)
			)
			#apply_material: (				
				
				mergeMaxFile f #select #autoRenameDups  #renameMtlDups  quiet:true
				sel = selection as array
				
				if(sel[1] != undefined and sel[1].material != undefined) do (	
					mat = sel[1].material
					mat.name = sel[1].name
					
					setVRayHDRIPath path: p geom: #(mat)
					--setVRayOSLTexPath path: p geom: #(mat)
					
					setTexturesPath path: p geom: #(mat) type: BitmapTex
					try(setTexturesPath path: p geom: #(mat) type: CoronaBitmap)catch()
										
					try(
						select oldSelection
						for i in oldSelection do try(i.material = mat) catch()
					) catch()
					
					try(delete sel) catch()
				)
			)
			#open:
			(
				setSaveRequired true
				checkForSave()
				resetMaxFile #noPrompt
				loadMaxFile f useFileUnits: true quiet:true	
								
				sel = objects as array			
				
				setIesPath path: p geom: sel	
				setTexturesPath path: p geom: sel type: BitmapTex
				try(setTexturesPath path: p geom: sel type: CoronaBitmap)catch()
				
				setVRayHDRIPath path: p geom: sel
				--setVRayOSLTexPath path: p geom: sel
				
				try(select sel)catch()
			)
			#xref:
			(
				objs = getMAXFileObjectNames f
				o = xrefs.addNewXrefObject f objs dupMtlNameAction: #autoRename
				select o
				sel = selection as array
				
				-- Add objects to layer
				if(catName != false) do moveToLayer catName sel: sel
			)
			default: 
			(
				mergeMaxFile f #select #promptDups  #promptMtlDups  quiet:true
				sel = selection as array
				
				setTexturesPath path: p geom: sel type: BitmapTex
				try(setTexturesPath path: p geom: sel type: CoronaBitmap)catch()
				setIesPath path: p geom: sel
				
				setVRayHDRIPath path: p geom: sel
				--setVRayOSLTexPath path: p geom: sel
				
				-- Add objects to layer
				if(catName != false) do moveToLayer catName sel: sel
			)	
		)
					
		toolMode.selectioncenter() 
		enableSceneRedraw()
		
		--max tool zoomextents all
			
		try(destroyDialog PasteStatus) catch()
		
		return true
	)

	fn onExecute e =
	(	
		executeTimer.start()
	)

	fn removeHash d = if(d != undefined) then (filterString d "#")[1] else ""

	fn onExecuteCommand s e =
	(	
		executeTimer.stop()
		
		t = filterString execCommand.text "="
		p = removeHash t[2] 
		__AUTO_PLACE_MODE = false	
		scriptPlacer = getFilenamePath (getThisScriptFileName()) + "ASSETS_Object Placer.ms"
		
		case t[1] of
		(		
			"APPLY_MATERIAL": 
			(				
				addFile p mode: #apply_material				
			)			
			"OPEN_MATERIAL": 
			(				
				addFile p mode: #open_material				
			)
			"MERGE_MODEL": 
			(
				__AUTO_PLACE_MODE = true
				addFile p mode: #merge
				try(fileIn scriptPlacer)catch()
			)
			"XREF_MODEL": 
			(
				__AUTO_PLACE_MODE = true
				addFile p mode: #xref			
				try(fileIn scriptPlacer)catch()
			)
			"OPEN_MODEL": 
			(			
				addFile p mode: #open
				saveProp "externalModelID" p
			)		
			"GOBACK": goBack()
			"GOFORWARD": goForward()
			"FORCEREFRESH": forceRefresh()
			"CLEARCHACHE": clearIEChache()
		)
		
		execCommand.text = ""				
	)
	/*
	fn onExecuteCommand s e =
	(	
		executeTimer.stop()
		
		t = filterString execCommand.text "="
		p = removeHash t[2] 
		id = removeHash t[3]	
		
		case t[1] of
		(
			"MERGE_MODEL": addFile p mode: #merge
			"XREF_MODEL": addFile p mode: #xref
			"OPEN_MODEL": 
			(			
				addFile p mode: #open
				saveProp "externalModelID" id
			)
			"MODELNOTEXIST": messageBox _MODELNOTEXIST title: "Error!"
			"MODELBAD": messageBox _MODELBAD title: "Error!"
			"GOBACK": goBack()
			"GOFORWARD": goForward()
			"FORCEREFRESH": forceRefresh()
			"CLEARCHACHE": clearIEChache()
		)
		
		execCommand.text = ""				
	)*/

	dotNet.addEventHandler _ViscoAssetsForm "Resize" onResize
	dotNet.addEventHandler _ViscoAssetsForm "Move" onMove
	dotNet.addEventHandler _ViscoAssetsForm "Load" onLoad
	dotNet.addEventHandler _ViscoAssetsForm "Closing" onClose
	dotnet.addEventHandler wb "gotFocus" onFocus
	dotnet.addEventHandler wb "Click" onFocus
	dotNet.addEventHandler wb "Navigating" onNavigating
	dotNet.addEventHandler wb "DocumentCompleted" onCompleted
	dotNet.addEventHandler wbTrust "DocumentCompleted" loadUrl

	dotNet.addEventHandler execCommand "TextChanged" onExecute
	dotnet.addEventHandler executeTimer "tick" onExecuteCommand

	dotNet.setLifetimeControl _ViscoAssetsForm #dotnet

	fn maxHWND = 
	(
		maxHandlePointer=(Windows.GetMAXHWND())	
		sysPointer = DotNetObject "System.IntPtr" maxHandlePointer
		maxHwnd = DotNetObject "MaxCustomControls.Win32HandleWrapper" sysPointer
	)

	maxHandlePointer = (Windows.GetMAXHWND())
	sysPointer = DotNetObject "System.IntPtr" maxHandlePointer
	_ViscoAssetsForm.fromHandle sysPointer

	_ViscoAssetsForm.ShowInTaskbar = on
	--_ViscoAssetsForm.showmodeless()	
	_ViscoAssetsForm.show()
	clearListener()
)

